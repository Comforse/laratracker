<?php

namespace Rooles;

use App\User;

/**
 * Class PermsMiddleware
 * @package Rooles
 */
class PermsMiddleware extends BaseMiddleware
{

    /**
     * @param string $permissions
     * @param User $user
     * @return bool
     */
    protected function verifyCondition($permissions, $user)
    {
        return $user->role->cannot($permissions);
    }

}
