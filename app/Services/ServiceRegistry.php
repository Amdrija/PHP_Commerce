<?php

namespace Andrijaj\DemoProject\Services;

use Andrijaj\DemoProject\Framework\ImageService;

class ServiceRegistry
{
    private static array $services;

    private function __construct()
    {

    }

    /**
     * Sets the $serviceClass to corresponds to $serviceName.
     * @param string $serviceName
     * @param string $serviceClass
     */
    public static function set(string $serviceName, string $serviceClass)
    {
        static::$services[$serviceName] = $serviceClass;
    }

    /**
     * Returns the name of the service class that corresponds to $serviceName.
     * @param string $serviceName
     * @return CategoryService|ProductService|StatisticsService|LoginService|ImageService|ProductPaginationService
     * @throws ServiceNotFoundException
     */
    public static function get(string $serviceName)
    {
        if (!isset(static::$services[$serviceName])) {
            throw new ServiceNotFoundException();
        }

        if (static::$services[$serviceName] === ProductService::class) {
            return new ProductService();
        }

        return new self::$services[$serviceName];
    }
}