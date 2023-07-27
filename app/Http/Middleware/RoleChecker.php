<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponser;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RoleChecker
{
    use ApiResponser;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $loggedUser = auth()->user();

        if(!in_array($loggedUser->role->name, $roles))
        {
            return $this->errorResponse('No tienes permisos para realizar esta acción', Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
