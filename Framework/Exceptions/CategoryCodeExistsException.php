<?php

namespace Andrijaj\DemoProject\Framework\Exceptions;

use Exception;
use Throwable;

class CategoryCodeExistsException extends Exception
{
    public function __construct($message = 'Category code is not unique.', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}