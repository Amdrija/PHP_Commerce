<?php

namespace Andrijaj\DemoProject\Controllers\FrontControllers;

use Andrijaj\DemoProject\Framework\Exceptions\CategoryNotFoundException;
use Andrijaj\DemoProject\Framework\Exceptions\ProductNotFoundException;
use Andrijaj\DemoProject\Framework\Responses\ErrorResponseFactory;
use Andrijaj\DemoProject\Framework\Request;
use Andrijaj\DemoProject\Framework\Responses\Response;
use Andrijaj\DemoProject\Services\CategoryService;
use Andrijaj\DemoProject\Services\ProductPaginationService;
use Andrijaj\DemoProject\Services\ProductService;
use Andrijaj\DemoProject\Services\ServiceNotFoundException;
use Andrijaj\DemoProject\Services\ServiceRegistry;
use Exception;

class ProductController extends FrontController
{
    private const MAX_PRODUCTS_PER_PAGE = 101;
    private const DEFAULT_PRODUCTS_PER_PAGE = 5;
    private const DEFAULT_SORT_BY = 'PRICE_ASC';
    private ProductService $productService;
    private CategoryService $categoryService;
    private ProductPaginationService $productPaginationService;

    /**
     * ProductController constructor.
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

    public function indexAction(array $parameters): Response
    {
        $productSKU = $parameters['SKU'];
        try {
            $product = $this->productService->getProductBySKU($productSKU, true);
            $this->productService->incrementProductViewCount($productSKU);
        } catch (ProductNotFoundException $e) {
            return ErrorResponseFactory::getResponse($e->getMessage(), 404);
        } catch (Exception $e) {
            return ErrorResponseFactory::getResponse($e->getMessage(), 400);
        }

        return $this->buildHtmlResponse(
            'productDetails',
            [
                'title' => 'Demo Shop | ' . $product->title,
                'product' => $product,
                'categoryTree' =>
                    json_encode($this->categoryService->getCategoryTree()),
            ]
        );
    }

    /**
     * Returns the data needed to render search results.
     * @param array $parameters
     * @return Response
     */
    public function listAction(array $parameters): Response
    {
        $categoryCode = $parameters['categoryCode'];
        $sortBy = $this->getSortBy($this->request->getQueryParameters(), self::DEFAULT_SORT_BY);

        try {
            $rootCategory = $this->categoryService->getCategoryModelByCode($categoryCode);
            $categoryIds = $this->categoryService->getArrayOfCategoryAndSubcategoryIds($rootCategory);

            $productsPerPage = $this->productPaginationService->getProductsPerPage(
                $this->request->getQueryParameters(),
                self::MAX_PRODUCTS_PER_PAGE,
                self::DEFAULT_PRODUCTS_PER_PAGE
            );
            $pageCount = $this->productPaginationService->getProductPageCount(
                $productsPerPage,
                $this->productService->getProductsCountByCategoryIds(
                    $categoryIds
                )
            );
            $page = $this->productPaginationService->getProductPage($this->request->getQueryParameters(), $pageCount);

            $products = $this->productService->getProductsByCategoryIds(
                $categoryIds,
                $page,
                $productsPerPage,
                $sortBy
            );
        } catch (CategoryNotFoundException $e) {
            return ErrorResponseFactory::getResponse('Category not found.', 404);
        } catch (Exception $e) {
            return ErrorResponseFactory::getResponse('Unknown error.', 400);
        }

        return $this->buildHtmlResponse(
            'productList',
            [
                'products' => $products,
                'title' => $rootCategory->Title,
                'categoryTree' => json_encode($this->categoryService->getCategoryTree()),
                'currentPage' => $page,
                'productPageCount' => $pageCount,
                'productsPerPage' => $productsPerPage,
                'sortBy' => $sortBy,
            ]
        );
    }
}