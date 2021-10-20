<?php

namespace Andrijaj\DemoProject\Middleware;

use Andrijaj\DemoProject\Framework\Exceptions\HttpUnauthorizedException;
use Andrijaj\DemoProject\Framework\Middleware;
use Andrijaj\DemoProject\Services\LoginService;
use Andrijaj\DemoProject\Services\ServiceNotFoundException;
use Andrijaj\DemoProject\Services\ServiceRegistry;

class AuthenticationMiddleware implements Middleware
{
    private LoginService $loginService;

    /**
     * AuthenticationMiddleware constructor.
     * @throws ServiceNotFoundException
     */
    public function __construct()
    {
        $this->loginService = ServiceRegistry::get('loginService');
    }

    /**
     * Authenticates the current user, returns true if the authentication was successful
     * and throws HttpUnauthorizedException if the authentication failed.
     * @return bool
     * @throws HttpUnauthorizedException
     */
    public function execute(): bool
    {
        if ($this->loginService->isSessionActive() || $this->loginService->automaticLogIn()) {
            return true;
        }

        throw new HttpUnauthorizedException();
    }
}