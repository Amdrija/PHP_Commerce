<?php

namespace Andrijaj\DemoProject\Services;

use Andrijaj\DemoProject\Entities\ProductEntity;
use Andrijaj\DemoProject\Framework\Exceptions\CategoryNotFoundException;
use Andrijaj\DemoProject\Framework\Exceptions\FileNonExistentException;
use Andrijaj\DemoProject\Framework\ImageService;
use Andrijaj\DemoProject\Framework\Exceptions\ProductNotFoundException;
use Andrijaj\DemoProject\Framework\Exceptions\SKUAlreadyExistsException;
use Andrijaj\DemoProject\Models\Product;
use Andrijaj\DemoProject\Repositories\CategoryRepository;
use Andrijaj\DemoProject\Repositories\ProductRepository;
use Andrijaj\DemoProject\Repositories\RepositoryNotFoundException;
use Andrijaj\DemoProject\Repositories\RepositoryRegistry;
use Exception;

class ProductService
{
    private const DEFAULT_RELATIVE_IMAGE_SAVE_PATH = '/public/img';
    private const START_VIEW_COUNT = 0;
    private const PRICE_ASCENDING_ORDER = 'PRICE_ASC';
    private const PRICE_DESCENDING_ORDER = 'PRICE_DESC';
    private const TITLE_ASCENDING_ORDER = 'TITLE_ASC';
    private const TITLE_DESCENDING_ORDER = 'TITLE_DESC';
    private const BRAND_ASCENDING_ORDER = 'BRAND_ASC';
    private const BRAND_DESCENDING_ORDER = 'BRAND_DESC';
    private ProductRepository $productRepository;
    private CategoryRepository $categoryRepository;
    private ImageService $imageService;

    /**
     * ProductService constructor.
     * @throws ServiceNotFoundException
     * @throws RepositoryNotFoundException
     */
    public function __construct()
    {
        $this->productRepository = RepositoryRegistry::get('productRepository');
        $this->categoryRepository = RepositoryRegistry::get('categoryRepository');
        $this->imageService = ServiceRegistry::get('imageService');
    }

    /**
     * Returns the $productsPerPage products on the given $page.
     * @param int $page
     * @param int $productsPerPage
     * @return array
     */
    public function getProducts(int $page, int $productsPerPage): array
    {
        $pageOffset = $page - 1;
        $products = $this->productRepository->getProducts($pageOffset * $productsPerPage, $productsPerPage);

        return ProductEntity::createArray($products);
    }

    /**
     * Returns the total number of products.
     * @return int
     */
    public function getProductCount(): int
    {
        return $this->productRepository->getProductsCount();
    }

    /**
     * Returns the total number of products in the category and it's subcategories.
     * @param array $categoryIds
     * @return int
     */
    public function getProductsCountByCategoryIds(array $categoryIds): int
    {
        return $this->productRepository->getProductsByCategoryIdsCount($categoryIds);
    }

    /**
     * Returns the number of products that match the search criteria.
     * @param array $searchCriteria
     * @return int
     */
    public function getProductsCountBySearchCriteria(array $searchCriteria): int
    {
        return $this->productRepository->getProductCountBySearchCriteria($searchCriteria);
    }

    /**
     * Returns the $productsPerPage number of products that belong to the specified category for display on
     * specified
     * $page.
     * @param array $categoryIds
     * @param int $page
     * @param int $productsPerPage
     * @param string $sortBy
     * @return array
     */
    public function getProductsByCategoryIds(array $categoryIds, int $page, int $productsPerPage, string $sortBy):
    array
    {
        $offset = ($page - 1) * $productsPerPage;

        return ProductEntity::createArray(
            $this->productRepository->getProductsByCategoryIds(
                $categoryIds,
                $offset,
                $productsPerPage,
                $sortBy
            )
        );
    }

    /**
     * Sorts the products by sortBy order.
     * @param array $products
     * @param string|null $sortBy
     * @return array
     */
    public function sortProducts(array $products, ?string $sortBy): array
    {
        switch ($sortBy) {
            case self::PRICE_ASCENDING_ORDER :
                usort($products, [ProductEntity::class, 'compareByPrice']);
                break;
            case self::PRICE_DESCENDING_ORDER :
                usort($products, [ProductEntity::class, 'compareByPriceDescending']);
                break;
            case self::TITLE_ASCENDING_ORDER :
                usort($products, [ProductEntity::class, 'compareByTitle']);
                break;
            case self::TITLE_DESCENDING_ORDER :
                usort($products, [ProductEntity::class, 'compareByTitleDescending']);
                break;
            case self::BRAND_ASCENDING_ORDER :
                usort($products, [ProductEntity::class, 'compareByBrand']);
                break;
            case self::BRAND_DESCENDING_ORDER :
                usort($products, [ProductEntity::class, 'compareByBrandDescending']);
                break;
            default:
                usort($products, [ProductEntity::class, 'compareByPrice']);
        }

        return $products;
    }

    /**
     * Returns all featured products as an array of ProductEntity.
     * @return array
     */
    public function getFeaturedProducts(): array
    {
        return ProductEntity::createArray($this->productRepository->getFeaturedProducts());
    }

    /**
     * Adds the product to the database.
     * If the specified SKU matches a SKU of an existing product, then it throws SKUAlreadyExistsException.
     * If the specified category doesn't exist, then it throws CategoryNotFoundException.
     * @param array $product
     * @param array $image
     * @throws CategoryNotFoundException
     * @throws FileNonExistentException
     * @throws SKUAlreadyExistsException
     */
    public function addProduct(array $product, array $image)
    {
        $product['image-relative-path'] = $this->imageRelativePath(
            $product['SKU'] . $this->imageService->getImageExtension($image)
        );
        $this->productRepository->createProduct($product, self::START_VIEW_COUNT);
        $this->imageService->moveTemporaryFile(
            $image,
            $this->imageSaveDirectory(),
            $product['SKU']
        );

    }

    /**
     * Edits the given product.
     * @param array $product
     * @param array $image
     * @param string $oldSKU
     * @throws CategoryNotFoundException
     * @throws FileNonExistentException
     * @throws ProductNotFoundException
     * @throws SKUAlreadyExistsException
     */
    public function editProduct(array $product, array $image, string $oldSKU)
    {
        $oldProduct = $this->productRepository->getProductBySKU($oldSKU);
        $oldImagePath = $oldProduct->Image;

        if ($oldSKU !== $product['SKU']) {
            $product['image-relative-path'] = self::DEFAULT_RELATIVE_IMAGE_SAVE_PATH . '/' .
                                              $product['SKU'] .
                                              '.' .
                                              pathinfo($oldImagePath)['extension'];
        }

        try {
            $this->productRepository->editProduct($product, $oldProduct);
            if ($this->imageUploaded($image)) {
                $this->imageService->moveTemporaryFile(
                    $image,
                    $this->imageSaveDirectory(),
                    $oldSKU
                );
            }
            if ($oldSKU !== $product['SKU']) {
                $this->imageService->rename(
                    $this->imageFullSavePath($oldImagePath),
                    $product['SKU'] . '.' . pathinfo($oldImagePath)['extension']
                );
            }
        } catch (SKUAlreadyExistsException $e) {
            throw $e;
        } catch (Exception $e) {
            $this->imageService->rename(
                $this->imageFullSavePath($product['image-relative-path']),
                basename($oldImagePath)
            );
            throw $e;
        }

    }

    /**
     * Returns ProductEntity with the specified SKU.
     * @param string $SKU
     * @param bool $onlyEnabled
     * @return ProductEntity
     * @throws ProductNotFoundException
     */
    public function getProductBySKU(string $SKU, bool $onlyEnabled = false): ProductEntity
    {
        $product = $onlyEnabled ? $this->productRepository->getEnabledProductBySKU($SKU) :
            $this->productRepository->getProductBySKU($SKU);
        if (is_null($product)) {
            throw new ProductNotFoundException();
        }

        return ProductEntity::create($product);
    }

    /**
     * Returns the array of products that match a certain criteria.
     * @param array $searchCriteria
     * @param int $page
     * @param int $productsPerPage
     * @return array
     */
    public function searchProducts(array $searchCriteria, int $page, int $productsPerPage)
    {
        $searchCriteria['productsPerPage'] = $productsPerPage;
        $searchCriteria['offset'] = ($page - 1) * $productsPerPage;

        return ProductEntity::createArray(
            $this->productRepository->searchProducts(
                $searchCriteria
            )
        );
    }

    /**
     * Increment the view count of the product with the specified SKU.
     * Throws ProductNotFoundException if the product with the specified SKU doesn't exist.
     * @param string $SKU
     */
    public function incrementProductViewCount(string $SKU)
    {
        $this->productRepository->incrementProductViews($SKU);
    }

    /**
     * Sets the Enabled attribute of products whose SKU is in $SKUs array to $enabled.
     * @param array $SKUs
     * @param bool $enabled
     */
    public function setProductsEnabled(array $SKUs, bool $enabled)
    {
        $this->productRepository->setProductsEnabled($SKUs, $enabled);
    }

    /**
     * Deletes the products whose SKU is in $SKUs array.
     * If the delete wasn't successful it will rethrow Exception.
     * We have to delete products in multiple queries, because if something
     * happens during the delete process we don't want to delete the image as well.
     * @param array $SKUs
     * @throws Exception
     */
    public function deleteProducts(array $SKUs)
    {
        $products = $this->productRepository->getProductsBySKUs($SKUs);
        foreach ($products as $product) {
            /** @var Product $product */
            try {
                $this->productRepository->deleteProduct($product);
                $this->imageService->delete($this->imageFullSavePath($product->Image));
            } catch (Exception $e) {
                throw $e;
            }
        }
    }

    /**
     * Image is uploaded if the $image['error'] is 0 and this function returns true.
     * Otherwise there was an error uploading the image and this function returns false.
     * @param array $image
     * @return bool
     */
    private function imageUploaded(array $image): bool
    {
        return $image['error'] === 0;
    }

    /**
     * Returns the path of where we save the image.
     * @return string
     */
    private function imageSaveDirectory(): string
    {
        return __DIR__ . '/../..' . self::DEFAULT_RELATIVE_IMAGE_SAVE_PATH;
    }

    /**
     * Returns the relative path where the images was saved based on the name.
     * @param string $imageName
     * @return string
     */
    private function imageRelativePath(string $imageName): string
    {
        return self::DEFAULT_RELATIVE_IMAGE_SAVE_PATH . '/' . $imageName;
    }

    /**
     * Returns the full save path of the image.
     * @param string $imagePath
     * @return string
     */
    private function imageFullSavePath(string $imagePath): string
    {
        return $this->imageSaveDirectory() . '/' . basename($imagePath);
    }
}