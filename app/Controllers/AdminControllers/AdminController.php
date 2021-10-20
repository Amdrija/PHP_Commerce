<?php

namespace Andrijaj\DemoProject\Controllers\AdminControllers;

use Andrijaj\DemoProject\Controllers\Controller;
use Andrijaj\DemoProject\Framework\Responses\HTMLResponse;

abstract class AdminController extends Controller
{

    protected function buildHtmlResponse(string $view, array $variables = []): HTMLResponse
    {
        return $this->buildHtmlResponseLayout('adminLayout', $view, $variables);
    }
}