<?php

namespace Andrijaj\DemoProject\Framework\Exceptions;

use Exception;
use Throwable;

class CategoryCircularReferenceException extends Exception
{
    public function __construct(
        $message = "A category can't have a sub category as it's parent category.",
        $code = 0,
        Throwable $previous = null
    )
    {
        parent::__construct($message, $code, $previous);
    }
}