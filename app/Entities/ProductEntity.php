<?php

namespace Andrijaj\DemoProject\Entities;

use Andrijaj\DemoProject\Models\Product;
use Illuminate\Support\Collection;

class ProductEntity
{
    public int $id;
    public int $categoryId;
    public string $categoryTitle;
    public string $SKU;
    public string $title;
    public string $brand;
    public int $price;
    public string $shortDescription;
    public string $description;
    public string $image;
    public bool $enabled;
    public bool $featured;
    public int $viewCount;

    /**
     * Creates the ProductEntity base on the Product model.
     * @param Product $product
     * @return ProductEntity
     */
    public static function create(Product $product): ProductEntity
    {
        $entity = new ProductEntity();
        $entity->id = $product->Id;
        $entity->categoryId = $product->CategoryId;
        $entity->categoryTitle = $product->category->Title;
        $entity->SKU = $product->SKU;
        $entity->title = $product->Title;
        $entity->brand = $product->Brand;
        $entity->price = $product->Price;
        $entity->shortDescription = $product->ShortDescription;
        $entity->description = $product->Description;
        $entity->image = $product->Image;
        $entity->enabled = $product->Enabled;
        $entity->featured = $product->Featured;
        $entity->viewCount = $product->ViewCount;

        return $entity;
    }

    /**
     * Returns an array of ProductEntity based on the passed collection.
     * @param Collection|null $products
     * @return array
     */
    public static function createArray(?Collection $products): array
    {
        $productsArray = [];
        foreach ($products as $product) {
            /** @var Product $product */
            $productsArray[] = ProductEntity::create($product);
        }

        return $productsArray;
    }

    /**
     * Compares two products by title and returns 0 if their titles are equal,
     * -1 if the first product's title is "lower" than the second product's title
     * and 1 if the first product's title is "higher" than the second product's title.
     * @param ProductEntity $a
     * @param ProductEntity $b
     * @return int
     */
    public static function compareByTitle(ProductEntity $a, ProductEntity $b): int
    {
        if($a->title === $b->title) {
            return 0;
        }

        return $a->title < $b->title ? -1 : 1;
    }

    /**
     * Compares two products by title and returns 0 if their titles are equal,
     * -1 if the first product's title is "higher" than the second product's title
     * and 1 if the first product's title is "lower" than the second product's title.
     * @param ProductEntity $a
     * @param ProductEntity $b
     * @return int
     */
    public static function compareByTitleDescending(ProductEntity $a, ProductEntity $b): int
    {
        return -self::compareByTitle($a, $b);
    }

    /**
     * Compares two products by price and returns 0 if their prices are equal,
     * -1 if the first product's price is "lower" than the second product's price
     * and 1 if the first product's price is "higher" than the second product's price.
     * @param ProductEntity $a
     * @param ProductEntity $b
     * @return int
     */
    public static function compareByPrice(ProductEntity $a, ProductEntity $b): int
    {
        if($a->price === $b->price) {
            return 0;
        }

        return $a->price < $b->price ? -1 : 1;
    }

    /**
     * Compares two products by price and returns 0 if their prices are equal,
     * -1 if the first product's price is "higher" than the second product's price
     * and 1 if the first product's price is "lower" than the second product's price.
     * @param ProductEntity $a
     * @param ProductEntity $b
     * @return int
     */
    public static function compareByPriceDescending(ProductEntity $a, ProductEntity $b): int
    {
        return -self::compareByPrice($a, $b);
    }

    /**
     * Compares two products by brand and returns 0 if their brands are equal,
     * -1 if the first product's brand is "lower" than the second product's brand
     * and 1 if the first product's brand is "higher" than the second product's brand.
     * @param ProductEntity $a
     * @param ProductEntity $b
     * @return int
     */
    public static function compareByBrand(ProductEntity $a, ProductEntity $b): int
    {
        if($a->brand === $b->brand) {
            return 0;
        }

        return $a->brand < $b->brand ? -1 : 1;
    }

    /**
     * Compares two products by brand and returns 0 if their brands are equal,
     * -1 if the first product's brand is "higher" than the second product's brand
     * and 1 if the first product's brand is "lower" than the second product's brand.
     * @param ProductEntity $a
     * @param ProductEntity $b
     * @return int
     */
    public static function compareByBrandDescending(ProductEntity $a, ProductEntity $b): int
    {
        return -self::compareByBrand($a, $b);
    }
}