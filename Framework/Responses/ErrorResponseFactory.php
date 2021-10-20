<?php

namespace Andrijaj\DemoProject\Framework\Responses;

class ErrorResponseFactory
{
    private const ERROR_VIEW = 'errorView';

    public static function getResponse(string $error, int $status): HTMLResponse
    {
        return new HTMLResponse(self::ERROR_VIEW, ['errorMessage' => $error, 'errorStatus' => $status], $status);
    }
}