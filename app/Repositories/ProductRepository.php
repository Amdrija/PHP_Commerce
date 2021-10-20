<?php

namespace Andrijaj\DemoProject\Repositories;

use Andrijaj\DemoProject\Framework\Exceptions\CategoryNotFoundException;
use Andrijaj\DemoProject\Framework\Exceptions\ProductNotFoundException;
use Andrijaj\DemoProject\Framework\Exceptions\SKUAlreadyExistsException;
use Andrijaj\DemoProject\Models\Product;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use PDOException;

class ProductRepository
{
    private const PRICE_ASCENDING_ORDER = 'PRICE_ASC';
    private const PRICE_DESCENDING_ORDER = 'PRICE_DESC';
    private const TITLE_ASCENDING_ORDER = 'TITLE_ASC';
    private const TITLE_DESCENDING_ORDER = 'TITLE_DESC';
    private const BRAND_ASCENDING_ORDER = 'BRAND_ASC';
    private const BRAND_DESCENDING_ORDER = 'BRAND_DESC';
    private const RELEVANCE_ORDER = 'RELEVANCE';

    /**
     * Returns all the products in the database.
     * @param int $offset
     * @param int $productsPerPage
     * @return Collection
     */
    public function getProducts(int $offset = 0, int $productsPerPage = 7): Collection
    {
        return Product::query()->skip($offset)->take($productsPerPage)->get();
    }

    /**
     * Returns all products with specified SKUs.
     * @param array $SKUs
     * @return Collection
     */
    public function getProductsBySKUs(array $SKUs): Collection
    {
        return Product::query()->whereIn('SKU', $SKUs)->get();
    }

    /**
     * Returns all featured products.
     * @return Collection
     */
    public function getFeaturedProducts(): Collection
    {
        return Product::query()->where('Featured', true)->where('Enabled', true)->take(100)->get();
    }

    /**
     * Returns the product with the most views.
     * @return Product|null
     */
    public function getMostViewedProduct(): ?Product
    {
        /** @var Product $product */
        $product = Product::query()->orderBy('ViewCount', 'desc')->first();

        return $product;
    }

    /**
     * Returns the total number of products.
     * @return int
     */
    public function getProductsCount(): int
    {
        return Product::query()->count();
    }

    /**
     * Increment the view count of the product with the specified SKU.
     * Throws ProductNotFoundException if the product with the specified SKU doesn't exist.
     * @param string $SKU
     */
    public function incrementProductViews(string $SKU)
    {
        Product::query()->where('SKU', $SKU)->increment('ViewCount');
    }

    /**
     * Creates the product with the specified fields.
     * @param array $product
     * @param int $startViewCount
     * @throws CategoryNotFoundException
     * @throws SKUAlreadyExistsException
     */
    public function createProduct(array $product, int $startViewCount)
    {
        try {
            $newProduct = new Product();
            $this->setProductFields($newProduct, $product);
            $newProduct->ViewCount = $startViewCount;
            $newProduct->save();
        } catch (PDOException $e) {
            if ($e->errorInfo[1] === 1062) {
                throw new SKUAlreadyExistsException();
            } else if ($e->errorInfo[1] === 1452) {
                throw new CategoryNotFoundException();
            }
            throw $e;
        }
    }

    /**
     * Edits the product specified by $oldSKU.
     * @param array $product
     * @param Product $editedProduct
     * @throws CategoryNotFoundException
     * @throws ProductNotFoundException
     * @throws SKUAlreadyExistsException
     */
    public function editProduct(array $product, Product $editedProduct)
    {
        if (is_null($editedProduct)) {
            throw new ProductNotFoundException();
        }

        try {
            $this->setProductFields($editedProduct, $product);
            $editedProduct->save();
        } catch (PDOException $e) {
            if ($e->errorInfo[1] === 1062) {
                throw new SKUAlreadyExistsException();
            } else if ($e->errorInfo[1] === 1452) {
                throw new CategoryNotFoundException();
            }
            throw $e;
        }
    }

    /**
     * Returns the product with the specified SKU.
     * @param string $SKU
     * @return Product|null
     */
    public function getProductBySKU(string $SKU): ?Product
    {
        /** @var Product $product */
        $product = Product::query()->where('SKU', $SKU)->first();

        return $product;
    }

    /**
     * Returns an enabled product with the specified SKU.
     * @param string $SKU
     * @return Product|null
     */
    public function getEnabledProductBySKU(string $SKU): ?Product
    {
        /** @var Product $product */
        $product = Product::query()->where('SKU', $SKU)->where('Enabled', true)->first();

        return $product;
    }

    /**
     * Returns a Collection containing $totalProducts number of products that belong to the Categories whose
     * id is in $categoryIds array,
     * starting from $offset.
     * @param array $categoryIds
     * @param int $offset
     * @param int $totalProducts
     * @param string $sortBy
     * @return Collection
     */
    public function getProductsByCategoryIds(array $categoryIds, int $offset, int $totalProducts, string $sortBy):
    Collection
    {
        $query = $this->getProductsByCategoryIdsQuery($categoryIds)->offset($offset)->take($totalProducts);
        $query = $this->addOrderByToQuery($query, $sortBy);

        return $query->get();
    }

    /**
     * Returns the number of products that belong to the Categories whose id is in $categoryIds array.
     * @param array $categoryIds
     * @return int
     */
    public function getProductsByCategoryIdsCount(array $categoryIds): int
    {
        return $this->getProductsByCategoryIdsQuery($categoryIds)->count();
    }

    /**
     * Searches the products by the given criteria and orders them by relevance
     * if the keyword is provided.
     * @param array $searchCriteria
     * @return Collection
     */
    public function searchProducts(array $searchCriteria): Collection
    {
        return $this->constructQueryFromSearchCriteria($searchCriteria)->get();
    }

    /**
     * Counts all the products that match the criteria.
     * @param array $searchCriteria
     * @return int
     */
    public function getProductCountBySearchCriteria(array $searchCriteria)
    {
        return $this->constructQueryFromSearchCriteria($searchCriteria, true)->count();
    }

    /**
     * Sets the Enabled attribute of products whose SKU is in $SKUs array to $enabled.
     * @param array $SKUs
     * @param bool $enabled
     */
    public function setProductsEnabled(array $SKUs, bool $enabled)
    {
        Product::query()->whereIn('SKU', $SKUs)->update(['Enabled' => $enabled]);
    }

    /**
     * Deletes the products whose SKU is in $SKUs array.
     * @param Product $product
     * @throws Exception
     */
    public function deleteProduct(Product $product)
    {
        $product->delete();
    }

    /**
     * Returns a query that we can use to search for Products that belong to the Categories whose id is
     * in $categoryIds array.
     * @param array $categoryIds
     * @return Builder
     */
    private function getProductsByCategoryIdsQuery(array $categoryIds): Builder
    {
        return Product::query()->whereIn('CategoryId', $categoryIds)->where('Enabled', true);
    }

    /**
     * Searches the products by the provided search criteria.
     * @param array $searchCriteria ['keyword' => '2110' , 'categoryId' => 2, 'minPrice' => 210, 'maxPrice' => 950,
     * 'sortBy' => 'PRICE_ASC']
     * @param bool $countProducts
     * @return Builder
     */
    private function constructQueryFromSearchCriteria(array $searchCriteria, bool $countProducts = false)
    {
        $query = Product::query()->selectRaw('Product.*');

        if (!empty($searchCriteria['keyword'])) {
            $keyword = $searchCriteria['keyword'];
            $query->join('demoDB.Category', 'Product.CategoryId', '=', 'Category.Id')
                  ->where(
                      function ($query) use ($keyword) {
                          $query->where('Product.Title', 'like', "%{$keyword}%")
                                ->orWhere('Product.Brand', 'like', "%{$keyword}%")
                                ->orWhere('Category.Title', 'like', "%{$keyword}%")
                                ->orWhere('Product.ShortDescription', 'like', "%{$keyword}%")
                                ->orWhere('Product.Description', 'like', "%{$keyword}%");
                      }
                  );
        }

        if (!empty($searchCriteria['categoryId'])) {
            $query->where('Product.CategoryId', $searchCriteria['categoryId']);
        }

        if (!empty($searchCriteria['minPrice'])) {
            $query->where('Product.Price', '>=', $searchCriteria['minPrice']);
        }

        if (!empty($searchCriteria['maxPrice'])) {
            $query->where('Product.Price', '<=', $searchCriteria['maxPrice']);
        }

        $query->where('Product.Enabled', '=', true);

        if (
            $searchCriteria['sortBy'] !== self::RELEVANCE_ORDER || empty($searchCriteria['keyword'])
        ) {
            $this->addOrderByToQuery($query, $searchCriteria['sortBy']);
        } else {
            $keyword = $searchCriteria['keyword'];
            $query->orderByRaw(
                "Product.Title LIKE '{$keyword}%' DESC,
                      Product.Title LIKE '%{$keyword}%' DESC,
                      Product.Brand LIKE '%{$keyword}%' DESC,
                      Category.Title LIKE '%{$keyword}%' DESC,
                      Product.ShortDescription LIKE '%{$keyword}%' DESC,
                      Product.Description LIKE '%{$keyword}%' DESC,
                      Product.Title ASC"
            );
        }

        if (!$countProducts) {
            $query->offset($searchCriteria['offset'])->take($searchCriteria['productsPerPage']);
        }

        return $query;
    }

    /**
     * Sets the specified $modifiedProduct's fields to $product fields.
     * @param Product $modifiedProduct
     * @param array $product
     */
    private function setProductFields(Product $modifiedProduct, array $product)
    {
        $modifiedProduct->SKU = $product['SKU'];
        $modifiedProduct->CategoryId = $product['categoryId'];
        $modifiedProduct->Title = $product['title'];
        $modifiedProduct->Brand = $product['brand'];
        $modifiedProduct->Price = $product['price'];
        $modifiedProduct->ShortDescription = $product['short-description'];
        $modifiedProduct->Description = $product['long-description'];
        $modifiedProduct->Image = isset($product['image-relative-path']) ? $product['image-relative-path'] : $modifiedProduct->Image;
        $modifiedProduct->Enabled = isset($product['enabled']);
        $modifiedProduct->Featured = isset($product['featured']);
    }

    /**
     * Adds the orderBy clause to the provided query.
     * @param Builder $query
     * @param string $sortBy
     * @return Builder
     */
    private function addOrderByToQuery(Builder $query, string $sortBy): Builder
    {
        $sortColumn = 'Product.Id';
        $sortOrder = 'ASC';
        switch ($sortBy) {
            case self::PRICE_ASCENDING_ORDER :
                $sortColumn = 'Product.Price';
                $sortOrder = 'ASC';
                break;
            case self::PRICE_DESCENDING_ORDER :
                $sortColumn = 'Product.Price';
                $sortOrder = 'DESC';
                break;
            case self::TITLE_ASCENDING_ORDER :
                $sortColumn = 'Product.Title';
                $sortOrder = 'ASC';
                break;
            case self::TITLE_DESCENDING_ORDER :
                $sortColumn = 'Product.Title';
                $sortOrder = 'DESC';
                break;
            case self::BRAND_ASCENDING_ORDER :
                $sortColumn = 'Product.Brand';
                $sortOrder = 'ASC';
                break;
            case self::BRAND_DESCENDING_ORDER :
                $sortColumn = 'Product.Brand';
                $sortOrder = 'DESC';
                break;
        }

        return $query->orderBy($sortColumn, $sortOrder);
    }
}