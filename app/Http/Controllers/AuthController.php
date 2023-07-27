<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponser;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use ApiResponser;

    public function login()
    {
        try
        {
            $credentials = request(['username', 'password']);
            if(! $token = auth()->attempt($credentials))
            {
                return $this->errorResponse('Error username or password.', Response::HTTP_UNAUTHORIZED);
            }
            
            return $this->respondWithToken($token);
        }
        catch(Exception $e)
        {
            return $this->errorResponse('Ocurrió un error al iniciar sesión', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function logout()
    {
        Auth::logout();
        return $this->successResponse(['message' => 'Session closed.']);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    private function respondWithToken($token)
    {
        return $this->successResponse([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => [
                'id' => auth()->user()->id,
                'firstname' => auth()->user()->firstname,
                'lastname' => auth()->user()->lastname,
                'username' => auth()->user()->username,
                'email' => auth()->user()->email,
                'birth_date' => auth()->user()->birth_date,
                'phone' => auth()->user()->phone,
                'address' => auth()->user()->address,
                'role' => [
                    'id' => auth()->user()->role->id,
                    'name' => auth()->user()->role->name,
                    'description' => auth()->user()->role->description
                ]
            ]
        ]);
    }
}
