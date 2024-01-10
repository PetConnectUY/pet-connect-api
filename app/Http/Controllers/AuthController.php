<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserPetProfileSetting;
use App\Models\UserRole;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    use ApiResponser;

    public function login()
    {
        try
        {
            $credentials = request(['email', 'password']);
            if(! $token = auth()->attempt($credentials))
            {
                return $this->errorResponse('Error email or password.', Response::HTTP_UNAUTHORIZED);
            }
            
            return $this->respondWithToken($token);
        }
        catch(Exception $e)
        {
            return $this->errorResponse('Ocurrió un error al iniciar sesión. '.$e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function logout()
    {
        Auth::logout();
        return $this->successResponse(['message' => 'Session closed.']);
    }

    public function refresh() //TIRABA ERROR
    {
        return $this->respondWithToken(auth()->refresh());
    }


    public function googleAuth(Request $request)
    {
        try 
        {
            if($request->input('provider') == 'GOOGLE')
            {
                $userExists = User::where('email', $request->input('email'))->first();
                if($userExists)
                {
                    if(is_null($userExists->external_id))
                    {
                        $userExists->external_id = $request->input('id');
                        $userExists->external_auth = 'google';
                        $userExists->save();
                    }
                    $token = auth()->login($userExists);
                    return $this->respondWithToken($token);
                }
                else
                {
                    $newUser = User::create([
                        'firstname' => $request->input('firstName'),
                        'lastname' => $request->input('lastName'),
                        'email' => $request->input('email'),
                        'external_id' => $request->input('id'),
                        'external_auth' => 'google',
                    ]);
                    if ($newUser) 
                    {
                        UserRole::create([
                            'user_id' => $newUser->id,
                            'role_id' => UserRole::USER_ROLE_ID,
                        ]);
            
                        UserPetProfileSetting::create([
                            'user_id' => $newUser->id,
                        ]);

                        DB::commit();
                        $token = auth()->login($newUser);
                        return $this->respondWithToken($token);
                    } else 
                    {
                        DB::rollBack();
                        return $this->errorResponse('Ocurrió un error al registrar el usuario.', Response::HTTP_INTERNAL_SERVER_ERROR);
                    }
                }
            }    
        }
        catch(Exception $e)
        {
            DB::rollBack();
            return $this->errorResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function respondWithToken($token)
    {
        return $this->successResponse([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL(), //TIRABA ERROR
            'user' => [
                'id' => auth()->user()->id,
                'firstname' => auth()->user()->firstname,
                'lastname' => auth()->user()->lastname,
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
