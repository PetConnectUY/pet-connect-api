<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\PutRequest;
use App\Http\Requests\User\StoreRequest;
use App\Models\User;
use App\Models\UserPetProfileSetting;
use App\Http\Requests\PetSettingRequest;
use App\Models\UserRole;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use ApiResponser;

    public function store(StoreRequest $request)
    {
        try
        {
            DB::beginTransaction();

            $user = User::create([
                'firstname' => $request->validated('firstname'),
                'lastname' => $request->validated('lastname'),
                'email' => $request->validated('email'),
                'password' => Hash::make($request->validated('password')),
                'birth_date' => $request->validated('birth_date'),
                'phone' => $request->validated('phone'),
                'address' => $request->validated('address')
            ]);

            UserRole::create([
                'user_id' => $user->id,
                'role_id' => UserRole::USER_ROLE_ID,
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
                'birth_date' => $request->validated('birth_date'),
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

    public function changeSettings(PetSettingRequest $request)
    {
        $setting = UserPetProfileSetting::where('user_id', auth()->user()->id)->first();

        if(is_null($setting))
        {
            return $this->errorResponse('No se encontró la configuración del perfil de la mascota.', Response::HTTP_NOT_FOUND);
        }

        $setting->user_fullname_visible = $request->validated('user_fullname_visible');
        $setting->user_location_visible = $request->validated('user_location_visible');
        $setting->user_phone_visible = $request->validated('user_phone_visible');
        $setting->user_email_visible = $request->validated('user_email_visible');
        $setting->save();

        return $this->successResponse($setting);
    }

    public function getSettings()
    {
        $setting = UserPetProfileSetting::where('user_id', auth()->user()->id)
            ->first();

        if(is_null($setting))
        {
            return $this->errorResponse('No se encontró la configuración del usuario.', Response::HTTP_NOT_FOUND);
        }

        return $this->successResponse($setting);
    }
}
