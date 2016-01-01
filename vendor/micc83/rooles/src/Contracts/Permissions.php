<?php

namespace Rooles\Contracts;

/**
 * Class Permissions
 * @package Rooles\Contracts
 */
interface Permissions
{

    /**
     * Set permissions from string or array
     *
     * @param string|array $permissions
     * @param string $value
     */
    public function set($permissions, $value);

    /**
     * Check permissions string or array
     *
     * @param array|string $query
     *
     * @return bool
     */
    public function evaluate($query);

}
