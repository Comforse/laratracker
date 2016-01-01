<?php

namespace Rooles\Traits;

use Illuminate\Support\Facades\App;
use Rooles\Contracts\RoleRepository;

/**
 * Class UserRole
 * @package Rooles\Traits
 *
 * @property \Rooles\Contracts\Role|string $role
 */
trait UserRole
{

    /**
     * Called on boot of the model
     *
     * @param string $role
     *
     * @return \Rooles\Contracts\Role
     */
    public function getRoleAttribute($role)
    {
        return App::make(RoleRepository::class)->get($role);
    }

    /**
     * @param array|string $permissions
     *
     * @return bool
     */
    public function can($permissions)
    {
        return $this->role->can($permissions);
    }

    /**
     * @param array|string $permissions
     *
     * @return bool
     */
    public function cannot($permissions)
    {
        return $this->role->cannot($permissions);
    }

}
