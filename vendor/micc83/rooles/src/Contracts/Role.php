<?php

namespace Rooles\Contracts;

/**
 * Interface Role
 * @package Rooles\Contracts
 */
interface Role
{

    /**
     * Constructor
     *
     * @param string $id
     * @param Permissions $permissions
     */
    public function __construct($id, Permissions $permissions);

    /**
     * Grants a single or multiple (array) permission
     *
     * @param array|string $permissions
     *
     * @return Role
     */
    public function grant($permissions);

    /**
     * Denies a single or multiple (array) permission
     *
     * @param array|string $permissions
     *
     * @return Role
     */
    public function deny($permissions);

    /**
     * Invert the result of can
     *
     * @param array|string $permissions
     *
     * @return bool
     */
    public function cannot($permissions);

    /**
     * Check permission for a single or multiple permission query
     *
     * @param array|string $permissions
     *
     * @return bool
     */
    public function can($permissions);

    /**
     * Verify if the current role is the one provided
     *
     * @param string $roleName
     *
     * @return bool
     */
    public function is($roleName);

    /**
     * Verify if the current role is in the provided array
     *
     * @param array $roles
     *
     * @return bool
     */
    public function isIn(array $roles);

    /**
     * Return role name
     *
     * @return string
     */
    public function name();

    /**
     * Return role id
     *
     * @return string
     */
    public function id();

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function assignName($name);

    /**
     * If the object is called as a string will return the role name
     *
     * @return string
     */
    public function __toString();

}
