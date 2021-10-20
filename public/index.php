<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Andrijaj\DemoProject\Framework\Bootstrap;
use Andrijaj\DemoProject\Framework\Responses\ErrorResponseFactory;
use Andrijaj\DemoProject\Framework\Router;
use Andrijaj\DemoProject\Framework\Request;

try {
    Bootstrap::initialize();
    $response = Router::dispatch(new Request());
} catch (Exception $e) {
    $response = ErrorResponseFactory::getResponse($e->getMessage(), 500);
}
http_response_code($response->getStatus());
$response->constructHeader();
echo $response->getContent();