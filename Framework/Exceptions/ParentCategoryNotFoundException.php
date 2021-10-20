<?php

namespace Andrijaj\DemoProject\Framework\Exceptions;

use Exception;
use Throwable;

class ParentCategoryNotFoundException extends Exception
{
    public function __construct(
        $message = "Can't find the specified parent category.",
        $code = 0,
        Throwable $previous = null
    )
    {
        parent::__construct($message, $code, $previous);
    }
}