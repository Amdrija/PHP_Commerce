<?php

namespace Andrijaj\DemoProject\Controllers\FrontControllers;

use Andrijaj\DemoProject\Framework\Responses\HTMLResponse;
use Andrijaj\DemoProject\Framework\Responses\RedirectResponse;
use Andrijaj\DemoProject\Framework\Request;
use Andrijaj\DemoProject\Framework\Responses\Response;
use Andrijaj\DemoProject\Services\LoginService;
use Andrijaj\DemoProject\Services\ServiceNotFoundException;
use Andrijaj\DemoProject\Services\ServiceRegistry;

class LoginController extends FrontController
{
    private LoginService $loginService;

    /**
     * LoginController constructor.
     * @param Request $request
     * @throws ServiceNotFoundException
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->loginService = ServiceRegistry::get('loginService');
    }

    /**
     * Index action of the Log in controller.
     * Returns a response that has log in page content.
     * @return Response
     */
    public function indexAction(): Response
    {
        if ($this->loginService->isSessionActive() || $this->loginService->automaticLogIn()) {
            return new RedirectResponse('/admin');
        }

        return new HTMLResponse('login', ['title' => 'Log In']);
    }

    /**
     * Log in action of the Log in controller.
     * Tries to log the user in. Upon successful login, returns the response
     * of the Admin controller action.
     * Upon unsuccessful login, return Error404 response.
     * @return Response
     */
    public function logInAction(): Response
    {
        $body = $this->request->getBody();

        if (!isset($body['username']) || !isset($body['password'])) {
            return new HTMLResponse('login', ['title' => 'Log In', 'error' => 'Login unsuccessful']);
        }

        $keepMeLoggedIn = isset($body['keepMeLoggedIn']);
        if (!$this->loginService->logIn($body['username'], $body['password'], $keepMeLoggedIn)) {
            return new HTMLResponse('login', ['title' => 'Log In', 'error' => 'Login unsuccessful']);
        }

        return new RedirectResponse('/admin');
    }
}