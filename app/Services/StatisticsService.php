<?php

namespace Andrijaj\DemoProject\Services;

use Andrijaj\DemoProject\Entities\StatisticsEntity;
use Andrijaj\DemoProject\Repositories\CategoryRepository;
use Andrijaj\DemoProject\Repositories\ProductRepository;
use Andrijaj\DemoProject\Repositories\RepositoryNotFoundException;
use Andrijaj\DemoProject\Repositories\RepositoryRegistry;
use Andrijaj\DemoProject\Repositories\StatisticsRepository;

class StatisticsService
{
    private StatisticsRepository $statisticsRepository;
    private CategoryRepository $categoryRepository;
    private ProductRepository $productRepository;

    /**
     * StatisticsService constructor.
     * @throws RepositoryNotFoundException
     */
    public function __construct()
    {
        $this->statisticsRepository = RepositoryRegistry::get('statisticsRepository');
        $this->categoryRepository = RepositoryRegistry::get('categoryRepository');
        $this->productRepository = RepositoryRegistry::get('productRepository');
    }

    /**
     * Returns the statistics needed for the admin dashboard page.
     * @return StatisticsEntity
     */
    public function getStatistics(): StatisticsEntity
    {
        $productsCount = $this->productRepository->getProductsCount();
        $categoriesCount = $this->categoryRepository->getCategoryCount();
        $homepageViewCount = $this->statisticsRepository->getHomeViewCount();
        $mostViewedProduct = $this->productRepository->getMostViewedProduct();
        $mostViewedProductSKU = $mostViewedProduct->SKU;
        $mostViewedProductViews = $mostViewedProduct->ViewCount;

        return new StatisticsEntity(
            $productsCount,
            $categoriesCount,
            $homepageViewCount,
            $mostViewedProductSKU,
            $mostViewedProductViews
        );
    }

    /**
     * Increments the home view count by 1.
     */
    public function incrementHomeViewCount()
    {
        $this->statisticsRepository->incrementHomeViewCount();
    }
}