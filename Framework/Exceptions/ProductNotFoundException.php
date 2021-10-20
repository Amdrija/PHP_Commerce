<?php

namespace Andrijaj\DemoProject\Framework\Exceptions;

use Exception;
use Throwable;

class ProductNotFoundException extends Exception
{
    public function __construct($message = "Product not found.", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}