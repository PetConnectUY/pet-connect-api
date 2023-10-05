<?php

namespace App\Http\Controllers;

use App\Http\Requests\PetSettingRequest;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Illuminate\Http\Response;
use App\Models\Pet;
use App\Models\QrCodeActivation;
use App\Models\User;
use App\Models\UserPetProfileSetting;

class DashboardController extends Controller
{
    use ApiResponser;

    public function getPets(Request $request)
    {
        $pets = Pet::where('deleted_at', null)
            ->where('user_id', auth()->user()->id)
            ->paginate(12);
        
        return $this->successResponse($pets);
    }

    public function getQrCodes(Request $request)
    {
        $codes = QrCodeActivation::where('qr_code_id', '!=', null)
            ->where('user_id', auth()->user()->id);
            
        if ($request->input('name')) {
            $petName = $request->input('name');
            $codes->whereHas('pet', function ($query) use ($petName) {
                $query->where('name', $petName);
            });
        }   

        if($request->input('start_date') && $request->input('end_date'))
        {
            $codes->where('created_at', '>=' , $request->input('start_date'))
                ->where('created_at', '<=' , $request->input('end_date'));
                
        }

        return $this->successResponse($codes->with('pet')->paginate(12));
    }

    //debe migrarse a dashboard/userontroller.php
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


