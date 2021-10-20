<?php

namespace Andrijaj\DemoProject\Framework\Exceptions;

use Exception;
use Throwable;

class CategoryNotFoundException extends Exception
{
    public function __construct($message = "Can't find the specified category.", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}