<?php

namespace Rooles\Contracts;

/**
 * Interface RoleRepository
 * @package Rooles\Contracts
 */
interface RoleRepository
{

    /**
     * Get an existing role or create a new one with the given name
     *
     * @param string $roleName
     *
     * @return Role
     */
    public function getOrCreate($roleName);

    /**
     * Get the role with the given name
     *
     * Return a "default" role if the given name is empty or
     * throw InvalidArgumentException if role name is not found
     *
     * @param string $roleName
     *
     * @return Role
     */
    public function get($roleName);

    /**
     * Create a new role
     *
     * @param string $roleName
     *
     * @return Role
     */
    public function create($roleName);

    /**
     * Add an existing role object to the repository
     *
     * @param Role $role
     *
     * @return Role
     */
    public function add(Role $role);

}