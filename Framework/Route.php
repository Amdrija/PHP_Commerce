<?php

namespace Andrijaj\DemoProject\Framework;

use Andrijaj\DemoProject\Framework\Exceptions\HttpUnauthorizedException;
use Andrijaj\DemoProject\Framework\Responses\ErrorResponseFactory;
use Andrijaj\DemoProject\Framework\Responses\Response;
use Exception;

/**
 * Class Route
 * @package Andrijaj\DemoProject\Framework
 *
 * A class that represents a Route
 */
class Route
{
    private const PARAM_REGEX = '/^{:(.+)}$/';
    private string $uri;
    private string $method;
    private string $controllerName;
    private string $controllerAction;
    private array $middlewareList;

    public function __construct(string $uri, string $method)
    {
        $this->uri = $this->trimURI($uri);
        $this->method = $method;
    }

    /**
     * Returns the URI of the Route.
     * @return string
     */
    public function getURI(): string
    {
        return $this->uri;
    }

    /**
     * Returns the method of the Route.
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Returns the controller name of the route
     * @return string
     */
    public function getController(): string
    {
        return $this->controllerName;
    }

    /**
     * Returns controller action.
     * @return string
     */
    public function getAction(): string
    {
        return $this->controllerAction;
    }

    /**
     * Returns true if the provided URI matches the route.
     * Also compares with params. For example
     * /posts/123/comments matches /posts/{:id}/comments
     * @param string $uri
     * @return bool
     */
    public function matchURI(string $uri): bool
    {
        $prettyURI = $this->removeQueryFromURI($uri);
        if ($prettyURI === $this->uri) {
            return true;
        }

        $uriArray = $this->arrayFromURI($prettyURI);
        $routeURIArray = $this->arrayFromURI($this->uri);
        if (count($uriArray) !== count($routeURIArray)) {
            return false;
        }

        foreach ($routeURIArray as $index => $routePart) {
            if ((!preg_match(self::PARAM_REGEX, $routePart) || $routePart === '') && $routePart !== $uriArray[$index]) {
                return false;
            }
        }

        return true;
    }

    /**
     * Extracts the URI parameters from the specified URI.
     * @param string $uri
     * @return array
     */
    public function extractURIParameters(string $uri): array
    {
        $parameters = [];
        $uriArray = $this->arrayFromURI($this->removeQueryFromURI($uri));
        $routeURIArray = $this->arrayFromURI($this->uri);

        foreach ($routeURIArray as $index => $routePart) {
            if (preg_match(self::PARAM_REGEX, $routePart, $matches)) {
                $parameters[$matches[1]] = $uriArray[$index];
            }
        }

        return $parameters;
    }

    /**
     * Adds the specified controller to handle the route.
     * @param string $controller
     * @param string $action
     * @param array $middleware
     */
    public function register(string $controller, string $action, array $middleware)
    {
        $this->controllerName = $controller;
        $this->controllerAction = $action;
        $this->middlewareList = $middleware;
    }

    /**
     * Executes the callbacks in the order they were registered.
     * Returns a controller that handles the view that is displayed
     * on this route.
     * @param Request $request
     * @return Response
     */
    public function execute(Request $request): Response
    {
        foreach ($this->middlewareList as $middlewareClass) {
            /** @var  Middleware $middleware */
            try {
                $middleware = new $middlewareClass();
                $middleware->execute();
            } catch (HttpUnauthorizedException $e) {
                return ErrorResponseFactory::getResponse('Access denied.', 403);
            } catch (Exception $e) {
                return ErrorResponseFactory::getResponse('Bad request.', 400);
            }
        }

        $controller = new $this->controllerName($request);
        $uriParameters = $this->extractURIParameters($request->getURI());

        return empty($uriParameters) ? $controller->{$this->controllerAction}(
        ) : $controller->{$this->controllerAction}($uriParameters);
    }

    /**
     * Returns a trimmed URI without unnecessary trailing '/' unless the URI is '/'.
     * @param string $uri
     * @return string
     */
    private function trimURI(string $uri): string
    {
        return $uri === '/' ? '/' : rtrim($uri, '/');
    }

    /* PRIVATE METHODS */

    /**
     * Returns the URI without the query parameters.
     * @param string $uri
     * @return string
     */
    private function removeQueryFromURI(string $uri): string
    {
        return strtok($uri, '?');
    }

    /**
     * Returns an array of URI parts separated by '/'.
     * @param string $uri
     * @return array
     */
    private function arrayFromURI(string $uri): array
    {
        return explode('/', $uri);
    }
}