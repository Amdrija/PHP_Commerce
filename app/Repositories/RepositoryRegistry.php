<?php

namespace Andrijaj\DemoProject\Repositories;

class RepositoryRegistry
{
    private static array $repositories;

    private function __construct()
    {

    }

    /**
     * Sets the $repositoryClass to correspond to $repositoryName.
     * @param string $repositoryName
     * @param string $repositoryClass
     */
    public static function set(string $repositoryName, string $repositoryClass)
    {
        static::$repositories[$repositoryName] = $repositoryClass;
    }

    /**
     * Returns the instance of the repository that corresponds to $repositoryName.
     * @param string $repositoryName
     * @return CategoryRepository|ProductRepository|StatisticsRepository|AdminRepository
     * @throws RepositoryNotFoundException
     */
    public static function get(string $repositoryName)
    {
        if (!isset(static::$repositories[$repositoryName])) {
            throw new RepositoryNotFoundException();
        }

        return new static::$repositories[$repositoryName];
    }
}