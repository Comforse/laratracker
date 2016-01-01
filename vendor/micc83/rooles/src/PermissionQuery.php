<?php

namespace Rooles;

/**
 * Class PermissionQuery
 * @package Rooles
 */
class PermissionQuery
{

    /**
     * @var array
     */
    protected $query;

    /**
     * @var array|string
     */
    protected $permissions;

    /**
     * Constructor
     *
     * @param string $permission
     * @param array $permissions
     */
    public function __construct($permission, array $permissions)
    {
        $this->query = Helpers::explodePermission($permission);
        $this->permissions = $permissions;
    }

    /**
     * Run the query
     *
     * @return bool
     */
    public function run()
    {

        foreach ($this->query as $queryStep) {
            if ($this->existsPathToVerify($queryStep)) {

                $result = $this->followPath($queryStep, $this->findPath($queryStep));

                if (is_bool($result)) {
                    return $result;
                }

                $this->permissions = $result;

            } else {
                break;
            }
        }

        return false;
    }

    /**
     * Check if the query has a path to follow
     *
     * @param $queryStep
     *
     * @return bool
     */
    protected function existsPathToVerify($queryStep)
    {
        return isset($this->permissions[$queryStep]) || Helpers::hasWildcard($this->permissions);
    }

    /**
     * Find which path to follow
     *
     * @param string $queryStep
     *
     * @return string
     */
    protected function findPath($queryStep)
    {
        return (isset($this->permissions[$queryStep])) ? $queryStep : '*';
    }

    /**
     * Follow the path to the next step or return the result
     *
     * @param string $queryStep
     * @param string $path
     *
     * @return array|bool
     */
    protected function followPath($queryStep, $path)
    {
        $nextPath = $this->permissions[$path];
        if (Helpers::isWildcard($nextPath)) {
            return !(Helpers::isWildcard($queryStep) && $this->findDeniesOnLowerLevels($this->permissions));
        } elseif (Helpers::isDenied($nextPath)) {
            return false;
        }
        return $nextPath;
    }

    /**
     * Find it here's a deny rule on a lower level
     *
     * @param $currentLevel
     *
     * @return bool
     */
    protected function findDeniesOnLowerLevels($currentLevel)
    {
        foreach ($currentLevel as $levels) {
            if (is_array($levels)) {
                if (
                    $this->currentLevelPermissionIsDenied($levels) ||
                    $this->findDeniesOnLowerLevels($levels)
                ) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param array $level
     * @return bool
     */
    protected function currentLevelPermissionIsDenied(array $level)
    {
        return Helpers::hasWildcard($level) && Helpers::isDenied($level['*']);
    }

}
