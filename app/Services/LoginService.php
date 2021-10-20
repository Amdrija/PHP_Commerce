<?php

namespace Andrijaj\DemoProject\Services;

use Andrijaj\DemoProject\Models\Admin;
use Andrijaj\DemoProject\Repositories\AdminRepository;
use Andrijaj\DemoProject\Repositories\RepositoryNotFoundException;
use Andrijaj\DemoProject\Repositories\RepositoryRegistry;

class LoginService
{
    private AdminRepository $adminRepository;

    /**
     * LoginService constructor.
     * @throws RepositoryNotFoundException
     */
    public function __construct()
    {
        $this->adminRepository = RepositoryRegistry::get('adminRepository');
    }

    /**
     * Checks if the Admin with the specified username and password is registered.
     * If it is, this function initializes the session and initializes the adminToken cookie
     * if $keepMeLoggedIn is true. Then the function returns true if the log in was successful.
     * If the admin with specified credentials doesn't exist, the method returns false.
     * @param string $username
     * @param string $password
     * @param bool $keepMeLoggedIn
     * @return bool
     */
    public function logIn(string $username, string $password, bool $keepMeLoggedIn): bool
    {
        $admin = $this->checkLoginCredentials($username, $password);
        if ($admin === null) {
            return false;
        }

        if ($keepMeLoggedIn) {
            $this->initializeAdminToken($admin);
        }

        $this->initializeSession($admin->Id);

        return true;
    }

    /**
     * Returns true and initializes the session if the cookie matches
     * an admin token. Otherwise, returns false.
     * @return bool
     */
    public function automaticLogIn(): bool
    {
        $admin = $this->isLoginRemembered();

        if ($admin === null) {
            return false;
        }

        $this->initializeSession($admin->Id);

        return true;
    }

    /**
     * Returns true if there is an active session.
     * A session is active if a $_SESSION['adminId'] is set.
     * @return bool
     */
    public function isSessionActive(): bool
    {
        return isset($_SESSION['adminId']);
    }

    /**
     * Returns Admin Id if the Admin user with the specified credentials exists.
     * Otherwise, if it doesn't exist, the method returns false.
     * @param string $username
     * @param string $password
     * @return Admin|null
     */
    private function checkLoginCredentials(string $username, string $password): ?Admin
    {
        $admin = $this->adminRepository->getAdminByUsername($username);

        return $admin !== null && password_verify($password, $admin->Password) ? $admin : null;
    }

    /**
     * Initializes the 'Keep me logged in token' and saves it in the database.
     * @param Admin $admin
     */
    private function initializeAdminToken(Admin $admin)
    {
        $token = uniqid();
        setcookie('adminToken', $token, time() + 60 * 60 * 24 * 365);

        $this->adminRepository->setAdminToken($admin, $token);
    }

    /**
     * Initializes the $_SESSION['adminId'] to be the specified adminId
     * @param int $adminId
     */
    private function initializeSession(int $adminId)
    {
        $_SESSION['adminId'] = $adminId;
    }

    /**
     * Returns the Admin Id if there is an admin with the Token that is the same
     * as the $_COOKIE['adminToken']. Otherwise, return false.
     * @return Admin|null
     */
    private function isLoginRemembered(): ?Admin
    {
        if (!isset($_COOKIE['adminToken'])) {
            return null;
        }

        return $this->adminRepository->getAdminByToken($_COOKIE['adminToken']);
    }
}