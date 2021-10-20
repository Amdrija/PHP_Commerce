<?php

namespace Andrijaj\DemoProject\Repositories;

use Andrijaj\DemoProject\Models\Admin;

class AdminRepository
{
    /**
     * Returns the Admin that has the specified username.
     * @param string $username
     * @return Admin|null
     */
    public function getAdminByUsername(string $username): ?Admin
    {
        /** @var Admin $admin */
        $admin = Admin::query()->where('Username', $username)->first();

        return $admin;
    }

    /**
     * Sets the Admin token.
     * @param Admin $admin
     * @param string $token
     */
    public function setAdminToken(Admin $admin, string $token)
    {
        $admin->Token = $token;
        $admin->save();
    }

    /**
     * @param string $token
     * @return Admin|null
     */
    public function getAdminByToken(string $token): ?Admin
    {
        /** @var Admin $admin */
        $admin = Admin::query()->where('Token', $token)->first();

        return $admin;
    }
}