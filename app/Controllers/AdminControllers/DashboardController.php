<?php

namespace Andrijaj\DemoProject\Controllers\AdminControllers;
use Andrijaj\DemoProject\Framework\Request;
use Andrijaj\DemoProject\Framework\Responses\Response;
use Andrijaj\DemoProject\Services\ServiceNotFoundException;
use Andrijaj\DemoProject\Services\ServiceRegistry;
use Andrijaj\DemoProject\Services\StatisticsService;

class DashboardController extends AdminController
{
    private StatisticsService $statisticsService;

    /**
     * DashboardController constructor.
     * @param Request $request
     * @throws ServiceNotFoundException
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->statisticsService = ServiceRegistry::get('statisticsService');
    }

    /**
     * Returns the data needed to render the admin dashboard view.
     * @return Response
     */
    public function indexAction(): Response
    {
        $dashboardViewParameters = [
            'statistics' => $this->statisticsService->getStatistics(),
            'title' => 'Admin | Dashboard',
        ];

        return $this->buildHtmlResponse('dashboard', $dashboardViewParameters);
    }
}