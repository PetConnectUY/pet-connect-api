<?php

namespace App\Http\Middleware;

use App\Models\UserPetToken;
use App\Traits\ApiResponser;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;

class RolePetTokenLimits
{
    use ApiResponser;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $loggedUser = auth()->user();
        $roleLimits = Config::get('role_limits');
        if ($roleLimits && array_key_exists($loggedUser->role->name, $roleLimits)) {
            $limit = $roleLimits[$loggedUser->role->name];

            $countPetTokens = UserPetToken::whereHas('pet', function ($query) use ($loggedUser) {
                $query->where('user_id', $loggedUser->id);
            })->count();

            if ($countPetTokens >= $limit) {
                return $this->errorResponse('Has alcanzado el límite de tokens permitidos', Response::HTTP_UNAUTHORIZED);
            }
        }

        return $next($request);
    }
}
