<?php

namespace Rooles;

use Rooles\Contracts\Permissions as PermissionsContract;

/**
 * Class Permissions
 * @package Rooles
 */
class Permissions implements PermissionsContract
{

    /**
     * Permissions storage
     * @var array
     */
    protected $permissions = [];

    /**
     * Set permissions from string or array
     *
     * @param string|array $permissions
     * @param string $value
     *
     * @return Permissions
     */
    public function set($permissions, $value)
    {
        foreach ((array)$permissions as $permission) {
            $this->setPermission($permission, $value);
        }

        return $this;
    }

    /**
     * Store a single permission
     *
     * @param string $permission
     * @param string $value
     *
     * @return void
     */
    protected function setPermission($permission, $value)
    {
        $permsLevel = &$this->permissions;

        $keys = Helpers::explodePermission($permission);

        while (count($keys) > 1) {
            $key = array_shift($keys);

            if (isset($permsLevel[$key]) && Helpers::isWildcard($permsLevel[$key])) {
                $permsLevel[$key] = ['*' => '*'];
            } elseif (!isset($permsLevel[$key]) || !is_array($permsLevel[$key])) {
                $permsLevel[$key] = [];
            }

            $permsLevel = &$permsLevel[$key];
        }

        $permsLevel[array_shift($keys)] = $value;
    }

    /**
     * Check permissions string or array
     *
     * @param array|string $query
     *
     * @return bool
     */
    public function evaluate($query)
    {
        foreach ($this->parseQuery($query) as $andPermissions) {
            foreach ($andPermissions as $orPermission) {
                if ($result = $this->evaluatePermission($orPermission)) {
                    break;
                }
            }
            if (!$result) {
                return false;
            }
        }

        return true;
    }

    /**
     * Parse the query
     *
     * If a string is provided it will be converted
     * to array. AND and OR operator will be parsed
     * in order to get a multidimensional array of
     * the requested permissions.
     *
     * @param array|string $query
     *
     * @return array
     */
    protected function parseQuery($query)
    {
        return $this->parseOrOperator(
            $this->parseAndOperator((array)$query)
        );
    }

    /**
     * Parse the query AND conditions
     *
     * It's applied when passing array of permissions
     * or when using the "&" operator
     *
     * @param array $query
     *
     * @return array
     */
    protected function parseAndOperator(array $query)
    {
        $permissions = [];
        foreach ($query as $permissionsGroup) {
            foreach (explode('&', $permissionsGroup) as $andPerms) {
                $permissions[] = $andPerms;
            }
        }
        return $permissions;
    }

    /**
     * Parse the query OR conditions
     *
     * It's applied when using the "|" operator
     *
     * @param array $query
     *
     * @return array
     */
    protected function parseOrOperator(array $query)
    {
        $permissions = [];
        foreach ($query as $key => $orPerms) {
            foreach (explode('|', $orPerms) as $permission) {
                $permissions[$key][] = $permission;
            }
        }
        return $permissions;
    }

    /**
     * Check the availability of a single permission
     *
     * @param string $permission
     *
     * @return bool
     */
    protected function evaluatePermission($permission)
    {
        return (new PermissionQuery($permission, $this->permissions))->run();
    }

}