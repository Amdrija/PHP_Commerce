<?php

namespace Andrijaj\DemoProject\Controllers\AdminControllers;

use Andrijaj\DemoProject\Framework\Exceptions\CategoryNotFoundException;
use Andrijaj\DemoProject\Framework\Responses\ErrorResponseFactory;
use Andrijaj\DemoProject\Framework\Exceptions\ProductNotFoundException;
use Andrijaj\DemoProject\Framework\Responses\RedirectResponse;
use Andrijaj\DemoProject\Framework\Request;
use Andrijaj\DemoProject\Framework\Responses\Response;
use Andrijaj\DemoProject\Framework\Exceptions\SKUAlreadyExistsException;
use Andrijaj\DemoProject\Services\CategoryService;
use Andrijaj\DemoProject\Services\ProductPaginationService;
use Andrijaj\DemoProject\Services\ProductService;
use Andrijaj\DemoProject\Services\ServiceNotFoundException;
use Andrijaj\DemoProject\Services\ServiceRegistry;
use Exception;

class AdminProductController extends AdminController
{
    private const MAX_PRODUCTS_PER_PAGE = 8;
    private const MIN_IMAGE_RATIO = 4 / 3;
    private const MIN_IMAGE_RATIO_MESSAGE = "4 by 3";
    private const MAX_IMAGE_RATIO = 16 / 9;
    private const MAX_IMAGE_RATIO_MESSAGE = "16 by 9";
    private const IMAGE_TYPES = ['image/jpeg', 'image/png'];
    private const MAX_SKU_LENGTH = 64;
    private const MAX_TITLE_LENGTH = 64;
    private const MAX_BRAND_LENGTH = 64;
    private const MAX_SHORT_DESCRIPTION_LENGTH = 128;
    private const MAX_DESCRIPTION_LENGTH = 512;
    private const MAX_PRICE = 2147483647;
    private const MAX_CATEGORY_ID = 2147483647;
    private const MIN_IMAGE_WIDTH = 600;
    private ProductService $productService;
    private CategoryService $categoryService;
    private ProductPaginationService $productPaginationService;

    /**
     * AdminProductController constructor.
     * @param Request $request
     * @throws ServiceNotFoundException
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->productService = ServiceRegistry::get('productService');
        $this->categoryService = ServiceRegistry::get('categoryService');
        $this->productPaginationService = ServiceRegistry::get('productPaginationService');
    }

    /**
     * Returns the data needed to render the add new product view.
     * @return Response
     */
    public function newProductAction(): Response
    {
        return $this->buildHtmlResponse(
            'adminProductEdit',
            [
                'product' => null,
                'categories' => json_encode($this->categoryService->getCategoryList()),
                'title' => 'Create Product',
            ]
        );
    }

    /**
     * Tries to create a new product and redirects to the index action.
     * @return Response
     */
    public function createProductAction(): Response
    {
        $file = $this->request->getFilesByParameter('image');
        $product = $this->request->getBody();

        if ($this->stringContainsURLReservedCharactersOrSpaces($product['SKU'])) {
            return $this->buildCreateErrorResponse('Product SKU cannot contain reserved URL characters or whitespace.');
        }

        if ($this->productFieldsTooLong($product)) {
            return $this->buildCreateErrorResponse('Product field(s) is (are) too long.');
        }

        if (!$this->isProductInputFilled($product)) {
            return $this->buildCreateErrorResponse('Product fields cannot be empty.');
        }

        if (!$this->isImageValid($file)) {
            return $this->buildCreateErrorResponse(
                "Check if the image height/width ratio is between " .
                self::MIN_IMAGE_RATIO_MESSAGE .
                ' and ' .
                self::MAX_IMAGE_RATIO_MESSAGE . '. Check if the image width is greater than ' . self::MIN_IMAGE_WIDTH
            );
        }

        try {
            $this->productService->addProduct($product, $file);
        } catch (SKUAlreadyExistsException $e) {
            return $this->buildCreateErrorResponse("Product with specified SKU: {$product['SKU']} already exists.");
        } catch (CategoryNotFoundException $e) {
            return $this->buildCreateErrorResponse("Specified category not found.");
        } catch (Exception $e) {
            return ErrorResponseFactory::getResponse($e->getMessage(), 400);
        }

        return new RedirectResponse('/admin/products');
    }

    /**
     * Returns the data needed to render a list of all products.
     * @return Response
     */
    public function indexAction(): Response
    {
        $productsPerPage = $this->productPaginationService->getProductsPerPage(
            $this->request->getQueryParameters(),
            self::MAX_PRODUCTS_PER_PAGE,
            self::MAX_PRODUCTS_PER_PAGE
        );

        $pageCount = $this->productPaginationService->getProductPageCount(
            $productsPerPage,
            $this->productService->getProductCount()
        );

        $page = $this->productPaginationService->getProductPage($this->request->getQueryParameters(), $pageCount);

        return $this->buildHtmlResponse(
            'adminProducts',
            [
                'products' => $this->productService->getProducts($page, $productsPerPage),
                'productPageCount' => $pageCount,
                'currentPage' => $page,
                'productsPerPage' => $productsPerPage,
                'title' => 'Admin | Products',
            ]
        );
    }

    /**
     * Returns the data needed to render the edit product view.
     * @param array $parameters
     * @return Response
     */
    public function editProductAction(array $parameters): Response
    {
        try {
            $product = $this->productService->getProductBySKU($parameters['SKU']);
        } catch (ProductNotFoundException $e) {
            return ErrorResponseFactory::getResponse('Product not found.', 404);
        }

        return $this->buildHtmlResponse(
            'adminProductEdit',
            [
                'product' => $product,
                'categories' => json_encode($this->categoryService->getCategoryList()),
                'title' => "Admin | Edit product",
            ]
        );
    }

    /**
     * Tries to edit a product and redirects to the edit product action of the specified product.
     * @param array $parameters
     * @return Response
     */
    public function saveEditedProductAction(array $parameters): Response
    {
        $file = $this->request->getFilesByParameter('image');
        $product = $this->request->getBody();

        if ($this->stringContainsURLReservedCharactersOrSpaces($product['SKU'])) {
            return $this->buildEditErrorResponse(
                'Product SKU cannot contain reserved URL characters or whitespace.',
                $parameters['SKU']
            );
        }

        if ($this->productFieldsTooLong($product)) {
            return $this->buildEditErrorResponse('Product field(s) is (are) too long.', $parameters['SKU']);
        }

        if (!$this->isProductInputFilled($product)) {
            return $this->buildEditErrorResponse('Product fields cannot be empty.', $parameters['SKU']);
        }

        if ($file['error'] == 0 && !$this->isImageValid($file)) {
            return $this->buildEditErrorResponse(
                "Check if the image height/width ratio is between " .
                self::MIN_IMAGE_RATIO_MESSAGE .
                ' and ' .
                self::MAX_IMAGE_RATIO_MESSAGE . '. Check if the image width is greater than ' . self::MIN_IMAGE_WIDTH,
                $parameters['SKU']
            );
        }

        try {
            $this->productService->editProduct($product, $file, $parameters['SKU']);
        } catch (SKUAlreadyExistsException $e) {
            return $this->buildEditErrorResponse(
                "Product with the specified SKU: {$product['SKU']} already exists.",
                $parameters['SKU']
            );
        } catch (CategoryNotFoundException $e) {
            return $this->buildEditErrorResponse(
                "Specified category not found.",
                $parameters['SKU']
            );
        } catch (Exception $e) {
            return ErrorResponseFactory::getResponse('Unknown error.', 400);
        }

        return new RedirectResponse('/admin/products/' . $product['SKU']);
    }

    /**
     * Batch enables products and redirects to index action.
     * @return Response
     */
    public function setProductsEnabledAction(): Response
    {
        $this->productService->setProductsEnabled(
            $this->request->getBodyParameter('SKUs'),
            $this->request->getBodyParameter('enabled')
        );

        return $this->indexAction();
    }

    /**
     * Tries to batch delete products and redirects to index action.
     * @return Response
     */
    public function deleteProductsAction(): Response
    {
        try {
            $this->productService->deleteProducts($this->request->getBodyParameter('SKUs'));
        } catch (Exception $e) {
            return ErrorResponseFactory::getResponse($e->getMessage(), 400);
        }

        return new RedirectResponse('/admin/products');
    }

    /**
     * Returns true if the product inputs are valid.
     * @param array $product
     * @return bool
     */
    private function isProductInputFilled(array $product): bool
    {
        return $product['SKU'] !== '' &&
               $product['title'] !== '' &&
               $product['brand'] !== '' &&
               $product['categoryId'] > 0 &&
               $product['price'] > 0 &&
               $product['short-description'] !== '' &&
               $product['long-description'] !== '';
    }

    /**
     * Returns true if the fields are too long for the database to hold.
     * @param array $product
     * @return bool
     */
    private function productFieldsTooLong(array $product): bool
    {
        return strlen($product['SKU']) > self::MAX_SKU_LENGTH ||
               strlen($product['title']) > self::MAX_TITLE_LENGTH ||
               strlen($product['brand']) > self::MAX_BRAND_LENGTH ||
               $product['categoryId'] > self::MAX_CATEGORY_ID ||
               $product['price'] > self::MAX_PRICE ||
               strlen($product['short-description']) > self::MAX_SHORT_DESCRIPTION_LENGTH ||
               strlen($product['long-description']) > self::MAX_DESCRIPTION_LENGTH;
    }

    /**
     * Returns true if the image width is greater than 600px and if
     * height/width ratio is between MIN_IMAGE_RATIO and MAX_IMAGE_RATIO.
     * @param array $file
     * @return bool
     */
    private function isImageValid(array $file): bool
    {
        if (!in_array($file['type'], self::IMAGE_TYPES)) {
            return false;
        }
        $image = $file['tmp_name'];
        $imageSize = getimagesize($image);
        $imageWidth = $imageSize[0];
        $imageHeight = $imageSize[1];
        $imageHeightWidthRatio = $imageHeight / $imageWidth;

        return $imageWidth >= self::MIN_IMAGE_WIDTH &&
               $imageHeightWidthRatio >= self::MIN_IMAGE_RATIO &&
               $imageHeightWidthRatio <= self::MAX_IMAGE_RATIO;
    }

    /**
     * Returns a response to render the edit product form when the editing was unsuccessful.
     * @param string $error
     * @param string $SKU
     * @return Response
     */
    private function buildEditErrorResponse(string $error, string $SKU): Response
    {
        try {
            return $this->buildHtmlResponse(
                'adminProductEdit',
                [
                    'product' => $this->productService->getProductBySKU($SKU),
                    'categories' => json_encode($this->categoryService->getCategoryList()),
                    'title' => 'Admin | Edit Product',
                    'error' => $error,
                ]
            );
        } catch (Exception $e) {
            return ErrorResponseFactory::getResponse($e->getMessage(), 400);
        }
    }

    /**
     * Returns a response to render the create product form when the creation wasn't successful.
     * @param string $error
     * @return Response
     */
    private function buildCreateErrorResponse(string $error): Response
    {
        return $this->buildHtmlResponse(
            'adminProductEdit',
            [
                'product' => null,
                'categories' => json_encode($this->categoryService->getCategoryList()),
                'title' => 'Admin | Create Product',
                'error' => $error,
            ]
        );
    }
}