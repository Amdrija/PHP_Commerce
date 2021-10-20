<?php

namespace Andrijaj\DemoProject\Controllers\FrontControllers;

use Andrijaj\DemoProject\Framework\Request;
use Andrijaj\DemoProject\Framework\Responses\Response;
use Andrijaj\DemoProject\Services\CategoryService;
use Andrijaj\DemoProject\Services\ProductService;
use Andrijaj\DemoProject\Services\ServiceNotFoundException;
use Andrijaj\DemoProject\Services\ServiceRegistry;
use Andrijaj\DemoProject\Services\StatisticsService;

class HomeController extends FrontController
{
    private ProductService $productService;
    private CategoryService $categoryService;
    private StatisticsService $statisticsService;

    /**
     * HomeController constructor.
     * @param Request $request
     * @throws ServiceNotFoundException
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->productService = ServiceRegistry::get('productService');
        $this->categoryService = ServiceRegistry::get('categoryService');
        $this->statisticsService = ServiceRegistry::get('statisticsService');
    }

    /**
     * Returns the response which contains the data needed to render landing page view.
     * @return Response
     */
    public function indexAction(): Response
    {
        $this->statisticsService->incrementHomeViewCount();
        return $this->buildHtmlResponse(
            'landingPage',
            [
                'title' => 'Demo Shop',
                'featuredProducts' => $this->productService->getFeaturedProducts(),
                'categoryTree' => json_encode($this->categoryService->getCategoryTree()),
            ]
        );
    }
}