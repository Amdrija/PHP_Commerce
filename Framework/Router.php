<?php

namespace Andrijaj\DemoProject\Framework;

use Andrijaj\DemoProject\Framework\Responses\ErrorResponseFactory;
use Andrijaj\DemoProject\Framework\Responses\Response;
use Exception;

/**
 * Class Router
 * @package Andrijaj\DemoProject\Framework
 *
 * A class that handles routing
 */
class Router
{
    private static array $routes = [];

    /**
     * Register the specified callback to a route with the specified URI
     * for all HTTP methods.
     * @param string $uri
     * @param $callback
     * @throws Exception
     */
    public static function all(string $uri, $callback)
    {
        foreach (Request::HTTP_METHODS as $method) {
            Router::register($method, $uri, $callback);
        }
    }

    /**
     * Register the callback to the a route with the specified URI and method.
     * @param string $method
     * @param string $uri
     * @param array $controller
     * @throws Exception
     */
    public static function register(string $method, string $uri, array $controller)
    {
        $method = strtoupper($method);

        if (!in_array($method, Request::HTTP_METHODS)) {
            throw new Exception("The HTTP method $method doesn't exist.");
        }

        $route = self::findRoute($method, $uri);

        if (!$route) {
            $route = new Route($uri, $method);

            if (!isset(static::$routes[$method])) {
                static::$routes[$method] = [];
            }
        }

        $route->register($controller['controller'], $controller['action'], $controller['middleware']);

        static::$routes[$method][] = $route;
    }

    public static function dispatch(Request $request): Response
    {
        /** @var Route $route */
        $route = static::findRoute($request->getMethod(), $request->getURI());
        if (!$route) {

            return ErrorResponseFactory::getResponse('Not found.', 404);
        }

        return $route->execute($request);
    }

    /**
     * Returns a route with the specified method and URI.
     * @param string $method
     * @param string $uri
     * @return mixed|null
     */
    private static function findRoute(string $method, string $uri)
    {
        //This is just in case the static::$routes doesn't contain the method
        // or if it doesn't contain an array.
        //This is so the warnings are fixed.
        if (isset(static::$routes[$method]) && is_array(static::$routes[$method])) {
            foreach (static::$routes[$method] as $route) {
                if ($route->matchURI($uri)) {
                    return $route;
                }
            }
        }

        return null;
    }
}