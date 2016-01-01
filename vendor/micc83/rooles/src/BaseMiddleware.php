<?php

namespace Rooles;

use App\User;
use Closure;
use Illuminate\Contracts\Auth\Guard;

/**
 * Class BaseMiddleware
 * @package Rooles
 */
abstract class BaseMiddleware
{

    /**
     * @var RoleManager
     */
    protected $roleRepo;

    /**
     * @var Guard
     */
    private $auth;

    /**
     * Create a new filter instance.
     *
     * @param Guard $auth
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param string $param
     *
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function handle($request, Closure $next, $param = '')
    {

        /** @var User $user */
        $user = $this->auth->user();

        if (!$user || $this->verifyCondition($param, $user)) {
            if ($request->ajax()) {
                return response()->json([
                    'error' => [
                        'code'    => 403,
                        'message' => 'Forbidden'
                    ]
                ], 403);
            } else {
                throw new ForbiddenHttpException('Forbidden');
            }
        }

        return $next($request);
    }


}
