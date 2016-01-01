<?php

namespace Rooles;

/**
 * Class Helpers
 * @package Rooles
 */
class Helpers
{

    /**
     * Is the provided string a wildcard char
     *
     * @param string $queryStep
     *
     * @return bool
     */
    public static function isWildcard($queryStep)
    {
        return $queryStep === '*';
    }

    /**
     * Is the provided string a wildcard char
     *
     * @param array $array
     *
     * @return bool
     */
    public static function hasWildcard(array $array)
    {
        return isset($array['*']);
    }

    /**
     * Is the provided string a deny char
     *
     * @param $permission
     *
     * @return bool
     */
    public static function isDenied($permission)
    {
        return $permission === '!';
    }

    /**
     * Explode the permission string adding the wildcard at the end of the array
     *
     * @param string $permission
     *
     * @return array
     */
    public static function explodePermission($permission)
    {
        return array_merge(explode('.', static::removeEndingWildcard($permission)), ['*']);
    }

    /**
     * Remove the wildcard at the end of the string
     *
     * @param string $key
     *
     * @return string
     */
    protected static function removeEndingWildcard($key)
    {
        return preg_replace('/\.\*$/', '', $key);
    }

}