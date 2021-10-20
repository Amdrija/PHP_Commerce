<?php

namespace Andrijaj\DemoProject\Entities;

class StatisticsEntity
{
    private int $productsCount;
    private int $categoriesCount;
    private int $homepageViewCount;
    private string $mostViewedProduct;
    private int $mostViewedProductViews;

    public function __construct(
        int $productsCount,
        int $categoriesCount,
        int $homepageViewCount,
        ?string $mostViewedProduct,
        ?int $mostViewedProductViews
    )
    {
        $this->productsCount = $productsCount;
        $this->categoriesCount = $categoriesCount;
        $this->homepageViewCount = $homepageViewCount;
        $this->mostViewedProduct = $mostViewedProduct === null ? '' : $mostViewedProduct;
        $this->mostViewedProductViews = $mostViewedProductViews === null ? 0 : $mostViewedProductViews;
    }

    /**
     * Returns the total number of products in the catalog.
     * @return int
     */
    public function getProductsCount()
    {
        return $this->productsCount;
    }

    /**
     * Returns the total number of categories in the catalog.
     * @return int
     */
    public function getCategoriesCount()
    {
        return $this->categoriesCount;
    }

    /**
     * Returns the number of views od the homepage.
     * @return int
     */
    public function getHomepageViewCount()
    {
        return $this->homepageViewCount;
    }

    /**
     * Returns the SKU of the most viewed product.
     * @return string
     */
    public function getMostViewedProduct()
    {
        return $this->mostViewedProduct;
    }

    /**
     * Returns the number of times the most viewed product
     * has been viewed.
     * @return int
     */
    public function getMostViewedProductViews()
    {
        return $this->mostViewedProductViews;
    }
}