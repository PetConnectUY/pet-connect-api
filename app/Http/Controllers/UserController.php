<?php

namespace App\Http\Controllers;

use Anhskohbo\NoCaptcha\NoCaptcha;
use App\Classes\UserRole as ClassesUserRole;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\User\GoogleRegistrationRequest;
use App\Http\Requests\User\PutRequest;
use App\Http\Requests\User\StoreRequest;
use App\Models\User;
use App\Models\UserPetProfileSetting;
use App\Models\UserRole;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use ApiResponser;

    public function store(StoreRequest $request)
    {
        $response = $request->input('g-recaptcha-response');
        $captcha = new NoCaptcha(config('services.recaptcha.secret'), config('services.recaptcha.sitekey'));
        $isHuman = $captcha->verifyResponse($response);
        if ($isHuman) {
            try
            {
                DB::beginTransaction();

                $user = User::create([
                    'firstname' => $request->validated('firstname'),
                    'lastname' => $request->validated('lastname'),
                    'email' => $request->validated('email'),
                    'password' => Hash::make($request->validated('password')),
                    'birth_date' => Carbon::parse($request->validated('birth_date'))->format('Y-m-d'),
                    'phone' => $request->validated('phone'),
                    'address' => $request->validated('address')
                ]);

                UserRole::create([
                    'user_id' => $user->id,
                    'role_id' => ClassesUserRole::USER_ROLE,
                ]);

                UserPetProfileSetting::create([
                    'user_id' => $user->id,
                ]);

                DB::commit();

                return $this->successResponse($user);
            }
            catch(Exception $e)
            {
                DB::rollBack();
                return $this->errorResponse('Ocurrió un error al registrar el usuario. '.$e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }

        }else{
            return $this->errorResponse('No se pudo completar la acción porque no se verificó el captcha', Response::HTTP_BAD_REQUEST);
        }
    }

    public function updateGoogleRegistration(GoogleRegistrationRequest $request, $id)
    {
        $user = User::find($id);
        if(is_null($user))
        {
            return $this->errorResponse('No se encontró el usuario.', Response::HTTP_NOT_FOUND);
        }
        $response = $request->input('g-recaptcha-response');
        $captcha = new NoCaptcha(config('services.recaptcha.secret'), config('services.recaptcha.sitekey'));
        $isHuman = $captcha->verifyResponse($response);
        if ($isHuman) {
            try
            {
                DB::beginTransaction();

                $user->update([
                    'firstname' => $request->validated('firstname'),
                    'lastname' => $request->validated('lastname'),
                    'email' => $request->validated('email'),
                    'password' => Hash::make($request->validated('password')),
                    'birth_date' => Carbon::parse($request->validated('birth_date'))->format('Y-m-d'),
                    'phone' => $request->validated('phone'),
                    'address' => $request->validated('address')
                ]);

                DB::commit();

                return $this->successResponse($user);
            }
            catch(Exception $e)
            {
                DB::rollBack();
                return $this->errorResponse('Ocurrió un error al registrar el usuario. '.$e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }

        }
        else
        {
            return $this->errorResponse('No se pudo completar la acción porque no se verificó el captcha', Response::HTTP_BAD_REQUEST);
        }
    }

    public function update(PutRequest $request, $id)
    {
        $user = User::find($id);
        if(!$user)
        {
            return $this->errorResponse('User not found', Response::HTTP_NOT_FOUND);
        }
        try
        {
            DB::beginTransaction();

            $user->update([
                'firstname' => $request->validated('firstname'),
                'lastname' => $request->validated('lastname'),
                'birth_date' => Carbon::parse($request->validated('birth_date'))->format('Y-m-d'),
                'phone' => $request->validated('phone'),
                'address' => $request->validated('address')
            ]);

            DB::commit();
            return $this->successResponse($user);
        }
        catch(Exception $e)
        {
            return $this->errorResponse('Ocurrió un error al actualizar el usuario', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if(!$user)
        {
            return $this->errorResponse('User not found', Response::HTTP_NOT_FOUND);
        }
        try
        {
            DB::beginTransaction();
            $user->delete();
            DB::commit();

            return $this->successResponse($user);
        }
        catch(Exception $e)
        {
            return $this->errorResponse('Ocurrió un error al eliminar el usuario.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function checkEmailAvailability($email) {
        $user = User::where('email', $email)->first();
        return $this->successResponse(!empty($user));
    }
}
