<?php

namespace Andrijaj\DemoProject\Framework\Exceptions;

use Exception;
use Throwable;

class DeletedCategoryHasProductException extends Exception
{
    public function __construct(
        $message = "Can't delete a category that has products. ",
        $code = 0,
        Throwable $previous = null
    )
    {
        parent::__construct($message, $code, $previous);
    }
}