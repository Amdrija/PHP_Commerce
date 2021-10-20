<?php

namespace Andrijaj\DemoProject\Framework;

use Andrijaj\DemoProject\Controllers\FrontControllers\ProductController;
use Andrijaj\DemoProject\Controllers\FrontControllers\ProductSearchController;
use Andrijaj\DemoProject\Middleware\AuthenticationMiddleware;
use Andrijaj\DemoProject\Repositories\AdminRepository;
use Andrijaj\DemoProject\Repositories\CategoryRepository;
use Andrijaj\DemoProject\Repositories\ProductRepository;
use Andrijaj\DemoProject\Repositories\RepositoryRegistry;
use Andrijaj\DemoProject\Repositories\StatisticsRepository;
use Andrijaj\DemoProject\Services\CategoryService;
use Andrijaj\DemoProject\Services\LoginService;
use Andrijaj\DemoProject\Services\ProductPaginationService;
use Andrijaj\DemoProject\Services\ProductService;
use Andrijaj\DemoProject\Services\ServiceRegistry;
use Andrijaj\DemoProject\Services\StatisticsService;
use Exception;
use Illuminate\Database\Capsule\Manager as Capsule;
use Andrijaj\DemoProject\Controllers\AdminControllers\AdminProductController;
use Andrijaj\DemoProject\Controllers\FrontControllers\HomeController;
use Andrijaj\DemoProject\Controllers\FrontControllers\LoginController;
use\Andrijaj\DemoProject\Controllers\AdminControllers\DashboardController;
use Andrijaj\DemoProject\Controllers\AdminControllers\CategoryController;

class Bootstrap
{
    /**
     * Function that initializes the app.
     * @throws Exception
     */
    public static function initialize()
    {
        session_start();
        self::initializeEloquent();
        self::initializeGuestRoutes();
        self::initializeAdminRoutes();
        self::initializeServiceRegistry();
        self::initializeRepositoryRegistry();
    }

    /**
     * Initializes the RepositoryRegistry.
     */
    public static function initializeRepositoryRegistry()
    {
        RepositoryRegistry::set('categoryRepository', CategoryRepository::class);
        RepositoryRegistry::set('productRepository', ProductRepository::class);
        RepositoryRegistry::set('statisticsRepository', StatisticsRepository::class);
        RepositoryRegistry::set('adminRepository', AdminRepository::class);
    }

    /**
     * Initializes the ServiceRegistry.
     */
    public static function initializeServiceRegistry()
    {
        ServiceRegistry::set('categoryService', CategoryService::class);
        ServiceRegistry::set('productService', ProductService::class);
        ServiceRegistry::set('statisticsService', StatisticsService::class);
        ServiceRegistry::set('loginService', LoginService::class);
        ServiceRegistry::set('imageService', ImageService::class);
        ServiceRegistry::set('productPaginationService', ProductPaginationService::class);
    }

    /**
     * Initializes Eloquent ORM.
     */
    private static function initializeEloquent()
    {
        $capsule = new Capsule;
        $capsule->addConnection(
            [
                'driver' => 'mysql',
                'host' => getenv('HOST'),
                'database' => getenv('MYSQL_DATABASE'),
                'username' => getenv('MYSQL_USER'),
                'password' => getenv('MYSQL_PASSWORD'),
                'charset' => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'prefix' => '',
            ]
        );
        $capsule->bootEloquent();
        $capsule->setAsGlobal();
    }

    /**
     * Initializes Guest routes.
     * @throws Exception
     */
    private static function initializeGuestRoutes()
    {
        /* Index route */
        Router::register(
            'GET',
            '/',
            [
                'controller' => HomeController::class,
                'action' => 'indexAction',
                'middleware' =>
                    [],
            ]
        );

        /* Category route */
        Router::register(
            'GET',
            '/category/{:categoryCode}',
            ['controller' => ProductController::class, 'action' => 'listAction', 'middleware' => []]
        );

        /* Product route */
        Router::register(
            'GET',
            '/product/{:SKU}',
            [
                'controller' => ProductController::class,
                'action' => 'indexAction',
                'middleware' =>
                    [],
            ]
        );

        /* Search route */
        Router::register(
            'GET',
            '/search',
            [
                'controller' => ProductSearchController::class,
                'action' => 'indexAction',
                'middleware' =>
                    [],
            ]
        );
    }

    /**
     * Initializes Admin routes.
     * @throws Exception
     */
    private static function initializeAdminRoutes()
    {
        /* Login routes */
        Router::register(
            'GET',
            '/admin/login',
            ['controller' => LoginController::class, 'action' => 'indexAction', 'middleware' => []]
        );
        Router::register(
            'POST',
            '/admin/login',
            ['controller' => LoginController::class, 'action' => 'loginAction', 'middleware' => []]
        );

        /* Dashboard routes */
        Router::register(
            'GET',
            '/admin',
            [
                'controller' => DashboardController::class,
                'action' => 'indexAction',
                'middleware' => [AuthenticationMiddleware::class],
            ]
        );

        /* Category routes */
        Router::register(
            'GET',
            '/admin/categories',
            [
                'controller' => CategoryController::class,
                'action' => 'indexAction',
                'middleware' => [AuthenticationMiddleware::class],
            ]
        );
        Router::register(
            'POST',
            '/admin/categories',
            [
                'controller' => CategoryController::class,
                'action' => 'newCategoryAction',
                'middleware' => [AuthenticationMiddleware::class],
            ]
        );
        Router::register(
            'PUT',
            '/admin/categories',
            [
                'controller' => CategoryController::class,
                'action' => 'updateCategoryAction',
                'middleware' => [AuthenticationMiddleware::class],
            ]
        );
        Router::register(
            'DELETE',
            '/admin/categories',
            [
                'controller' => CategoryController::class,
                'action' => 'deleteCategoryAction',
                'middleware' => [AuthenticationMiddleware::class],
            ]
        );

        /* Product routes. */
        Router::register(
            'GET',
            '/admin/products',
            [
                'controller' => AdminProductController::class,
                'action' => 'indexAction',
                'middleware' => [AuthenticationMiddleware::class],
            ]
        );
        Router::register(
            'GET',
            '/admin/products/create',
            [
                'controller' => AdminProductController::class,
                'action' => 'newProductAction',
                'middleware' => [AuthenticationMiddleware::class],
            ]
        );
        Router::register(
            'POST',
            '/admin/products/create',
            [
                'controller' => AdminProductController::class,
                'action' => 'createProductAction',
                'middleware' => [AuthenticationMiddleware::class],
            ]
        );
        Router::register(
            'GET',
            '/admin/products/{:SKU}',
            [
                'controller' => AdminProductController::class,
                'action' => 'editProductAction',
                'middleware' => [AuthenticationMiddleware::class],
            ]
        );
        Router::register(
            'POST',
            '/admin/products/{:SKU}',
            [
                'controller' => AdminProductController::class,
                'action' => 'saveEditedProductAction',
                'middleware' => [AuthenticationMiddleware::class],
            ]
        );
        Router::register(
            'POST',
            '/admin/products/actions/enable',
            [
                'controller' => AdminProductController::class,
                'action' => 'setProductsEnabledAction',
                'middleware' => [AuthenticationMiddleware::class],
            ]
        );
        Router::register(
            'POST',
            '/admin/products/actions/disable',
            [
                'controller' => AdminProductController::class,
                'action' => 'setProductsEnabledAction',
                'middleware' => [AuthenticationMiddleware::class],
            ]
        );
        Router::register(
            'POST',
            '/admin/products/actions/delete',
            [
                'controller' => AdminProductController::class,
                'action' => 'deleteProductsAction',
                'middleware' => [AuthenticationMiddleware::class],
            ]
        );
    }
}