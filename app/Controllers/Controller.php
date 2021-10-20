<?php

namespace Andrijaj\DemoProject\Controllers;

use Andrijaj\DemoProject\Framework\Responses\HTMLResponse;
use Andrijaj\DemoProject\Framework\Request;

/**
 * Class Controller
 *
 * @package Andrijaj\DemoProject\Controllers
 */
abstract class Controller
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Returns the request.
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * Returns the HTMLResponse that was built with the specified $layout, $view and $variables.
     * @param string $layout
     * @param string $view
     * @param array $variables
     * @return HTMLResponse
     */
    protected function buildHtmlResponseLayout(string $layout, string $view, array $variables = []): HTMLResponse
    {
        $response = new HTMLResponse($view, $variables);
        $response->setLayout($layout);

        return $response;
    }

    /**
     * Returns true if the provided string contains URL reserved characters or whitespace.
     * @param string $string
     * @return bool
     */
    protected function stringContainsURLReservedCharactersOrSpaces(string $string): bool
    {
        return preg_match("/[!*'();:@&=+$,\/?#\[\]\s]/", $string);
    }
}