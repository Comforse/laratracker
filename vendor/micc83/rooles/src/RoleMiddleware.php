<?php

namespace Rooles;

use App\User;

/**
 * Class RoleMiddleware
 * @package Rooles
 */
class RoleMiddleware extends BaseMiddleware
{

    /**
     * @param string $roles
     * @param User $user
     *
     * @return bool
     */
    protected function verifyCondition($roles, $user)
    {
        return !$user->role->isIn(explode('|', $roles));
    }

}
