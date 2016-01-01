<?php

namespace Rooles;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class ForbiddenHttpException
 * @package Rooles
 */
class ForbiddenHttpException extends HttpException
{

    /**
     * Constructor.
     *
     * @param string $message The internal exception message
     */
    public function __construct($message = null)
    {
        parent::__construct(403, $message, null, [], 0);
    }

}