<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Models\UserPetProfileSetting;
use App\Http\Requests\PetSettingRequest;
use App\Jobs\ChangePasswordJob;
use App\Models\User;
use App\Traits\ApiResponser;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use ApiResponser;

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

    public function changePassword(ChangePasswordRequest $request) {
        $user = User::find(auth()->user()->id);

        if(is_null($user))
        {
            return $this->errorResponse('No se encontró el usuario', Response::HTTP_NOT_FOUND);
        }

        if(!Hash::check($request->input('current_password'), $user->password)) {
            return $this->errorResponse('La contraseña actual no es válida', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user->password = Hash::make($request->input('new_password'));
        $user->save();

        ChangePasswordJob::dispatch($user->firstname. ' '. $user->lastname, $user->email);

        return $this->successResponse(['message' => 'Contraseña cambiada exitosamente']);
    }
}
