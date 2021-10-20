<?php

namespace Andrijaj\DemoProject\Services;

class ProductPaginationService
{
    /**
     * Returns the number of product pages.
     * @param int $productsPerPage
     * @param int $productsCount
     * @return int
     */
    public function getProductPageCount(int $productsPerPage, int $productsCount): int
    {
        return ceil($productsCount / $productsPerPage);
    }

    /**
     * Returns productsPerPage based on the maxProductsPerPage, $defaultProductsPerPage and $queryParameters.
     * @param array $queryParameters
     * @param int $maxProductsPerPage
     * @param int $defaultProductsPerPage
     * @return int
     */
    public function getProductsPerPage(array $queryParameters, int $maxProductsPerPage, int $defaultProductsPerPage)
    {
        $productsPerPage = isset($queryParameters['productsPerPage']) ? intval($queryParameters['productsPerPage']) : 0;

        return $this->isProductsPerPageValid(
            $productsPerPage,
            $maxProductsPerPage
        ) ? $productsPerPage : $defaultProductsPerPage;
    }

    /**
     * Returns page number based on the maxPages and $queryParameters.
     * @param array $queryParameters
     * @param int $maxPages
     * @return int
     */
    public function getProductPage(array $queryParameters, int $maxPages)
    {
        $page = isset($queryParameters['page']) ? intval($queryParameters['page']) : 0;

        return $this->isPageNumberValid($page, $maxPages) ? $page : 1;
    }

    /**
     * Checks if the $productsPerPage number is valid based on the $maxProductsPerPage.
     * @param int|null $productsPerPage
     * @param int $maxProductsPerPage
     * @return bool
     */
    private function isProductsPerPageValid(?int $productsPerPage, int $maxProductsPerPage)
    {
        return !is_null($productsPerPage) && $productsPerPage > 0 && $productsPerPage < $maxProductsPerPage;
    }

    /**
     * Checks if the provided $page number is valid based on the $productPageCount.
     * @param int|null $page
     * @param int $productPageCount
     * @return bool
     */
    private function isPageNumberValid(?int $page, int $productPageCount)
    {
        return !is_null($page) && $page > 0 && $page <= $productPageCount;
    }
}