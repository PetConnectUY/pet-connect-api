<?php

namespace App\Http\Controllers;

use App\Http\Requests\PetSettingRequest;
use App\Models\PetSettings;
use App\Traits\ApiResponser;
use Illuminate\Http\Response;

class PetSettingController extends Controller
{
    use ApiResponser;

    public function changeSettings($petId, PetSettingRequest $request)
    {
        $setting = PetSettings::where('pet_id', $petId)
            ->with('qractivation')
            ->first();

        if(is_null($setting))
        {
            return $this->errorResponse('No se encontró la configuración de la mascota.', Response::HTTP_NOT_FOUND);
        }

        $setting->user_fullname_visible = $request->validated('user_fullname_visible');
        $setting->user_location_visible = $request->validated('user_location_visible');
        $setting->user_phone_visible = $request->validated('user_phone_visible');
        $setting->user_email_visible = $request->validated('user_email_visible');
        $setting->save();

        return $this->successResponse($setting);
    }
}
