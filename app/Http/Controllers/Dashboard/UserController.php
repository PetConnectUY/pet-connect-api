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
