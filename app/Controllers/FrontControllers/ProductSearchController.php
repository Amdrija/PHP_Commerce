<?php

namespace Andrijaj\DemoProject\Controllers\FrontControllers;

use Andrijaj\DemoProject\Framework\Request;
use Andrijaj\DemoProject\Framework\Responses\Response;
use Andrijaj\DemoProject\Services\CategoryService;
use Andrijaj\DemoProject\Services\ProductPaginationService;
use Andrijaj\DemoProject\Services\ProductService;
use Andrijaj\DemoProject\Services\ServiceNotFoundException;
use Andrijaj\DemoProject\Services\ServiceRegistry;

class ProductSearchController extends FrontController
{
    private const MAX_PRODUCTS_PER_PAGE = 101;
    private const DEFAULT_PRODUCTS_PER_PAGE = 5;
    private const DEFAULT_SORT_BY = 'RELEVANCE';
    private ProductService $productService;
    private CategoryService $categoryService;
    private ProductPaginationService $productPaginationService;

    /**
     * ProductSearchController constructor.
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

    public function indexAction(): Response
    {
        $queryParameters = $this->request->getQueryParameters();
        $selectedCategoryId = empty($queryParameters['categoryId']) ? '' : $queryParameters['categoryId'];

        if ($this->isSearchCriteriaInvalid($queryParameters)) {
            return $this->buildHtmlResponse(
                'productSearch',
                [
                    'title' => 'Demo Shop | Search',
                    'products' => [],
                    'categories' => json_encode($this->categoryService->getCategoryList()),
                    'productsPerPage' => $this->productPaginationService->getProductsPerPage(
                        $queryParameters,
                        self::MAX_PRODUCTS_PER_PAGE,
                        self::DEFAULT_PRODUCTS_PER_PAGE
                    ),
                    'sortBy' => $queryParameters['sortBy'],
                    'currentPage' => 1,
                    'productPageCount' => 0,
                    'categoryTree' => json_encode($this->categoryService->getCategoryTree()),
                    'keyword' => $queryParameters['keyword'],
                    'selectedCategoryId' => $selectedCategoryId,
                    'minPrice' => $queryParameters['minPrice'],
                    'maxPrice' => $queryParameters['maxPrice'],
                    'error' => 'Search criteria too broad.',
                ]
            );
        }

        $queryParameters['sortBy'] = $this->getSortBy($queryParameters, self::DEFAULT_SORT_BY);

        $paginationData = $this->getPaginationData($queryParameters);

        $products = $this->productService->searchProducts(
            $queryParameters,
            $paginationData['page'],
            $paginationData['productsPerPage']
        );

        return $this->buildHtmlResponse(
            'productSearch',
            [
                'title' => 'Demo Shop | Search',
                'products' => $products,
                'categories' => json_encode($this->categoryService->getCategoryList()),
                'productsPerPage' => $paginationData['productsPerPage'],
                'sortBy' => $queryParameters['sortBy'],
                'currentPage' => $paginationData['page'],
                'productPageCount' => $paginationData['pageCount'],
                'categoryTree' => json_encode($this->categoryService->getCategoryTree()),
                'keyword' => $queryParameters['keyword'],
                'selectedCategoryId' => $selectedCategoryId,
                'minPrice' => $queryParameters['minPrice'],
                'maxPrice' => $queryParameters['maxPrice'],
            ]
        );
    }

    /**
     * Returns true if all 'keyword', 'categoryId', 'maxPrice' and 'minPrice'
     * entries of search criteria are empty.
     * @param array $searchCriteria
     * @return bool
     */
    private function isSearchCriteriaInvalid(array $searchCriteria)
    {
        return empty($searchCriteria['keyword']) &&
               empty($searchCriteria['categoryId']) &&
               empty($searchCriteria['maxPrice']) &&
               empty($searchCriteria['minPrice']);
    }

    /**
     * Returns an associative array containing the entries 'productsPerPage' ,
     * 'page' and 'pageCount' needed for pagination to work properly.
     * @param array $queryParameters
     * @return array
     */
    private function getPaginationData(array $queryParameters): array
    {
        $productsPerPage = $this->productPaginationService->getProductsPerPage(
            $queryParameters,
            self::MAX_PRODUCTS_PER_PAGE,
            self::DEFAULT_PRODUCTS_PER_PAGE
        );

        $pageCount = $this->productPaginationService->getProductPageCount(
            $productsPerPage,
            $this->productService->getProductsCountBySearchCriteria($queryParameters)
        );

        $page = $this->productPaginationService->getProductPage($queryParameters, $pageCount);

        return ['productsPerPage' => $productsPerPage, 'pageCount' => $pageCount, 'page' => $page];
    }

}