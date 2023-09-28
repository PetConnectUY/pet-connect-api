<?php

namespace App\Http\Controllers;

use Anhskohbo\NoCaptcha\NoCaptcha;
use App\Http\Requests\PetFoundRequest;
use App\Models\Pet;
use App\Models\QrCode;
use App\Models\QrCodeActivation;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PetProfileController extends Controller
{
    use ApiResponser;

    public function view($token)
    {
        $qrCode = QrCode::where('token', $token)
            ->first();

        if(is_null($qrCode))
        {
            return $this->errorResponse('No se encontró el código qr', Response::HTTP_NOT_FOUND);
        }
        if(!$qrCode->is_used)
        {
            return $this->errorResponse('El código qr no se activó', Response::HTTP_BAD_REQUEST);
        }
        if($qrCode->is_used && is_null($qrCode->activation->pet_id))
        {
            return $this->errorResponse('El código qr no tiene una mascota asignada', Response::HTTP_BAD_REQUEST);
        }
        return $this->successResponse($qrCode->activation->pet);
    }

    public function petFound(PetFoundRequest $request, $petId)
    {
        $response = $request->input('g-recaptcha-response');
        $captcha = new NoCaptcha(config('services.recaptcha.secret'), config('services.recaptcha.sitekey'));
        $isHuman = $captcha->verifyResponse($response);
        if ($isHuman) {
            $petQrActivation = QrCodeActivation::where('pet_id', $petId)
            ->first();
        
            if(is_null($petQrActivation))
            {
                return $this->errorResponse('No se encontró la mascota.', Response::HTTP_NOT_FOUND);
            }

            return $this->successResponse('captcha completo');
        } else {
            return $this->errorResponse('No se pudo completar la acción porque no se verificó el captcha', Response::HTTP_BAD_REQUEST);
        }
        

    }
}
