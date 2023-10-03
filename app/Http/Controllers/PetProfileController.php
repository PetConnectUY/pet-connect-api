<?php

namespace App\Http\Controllers;

use Anhskohbo\NoCaptcha\NoCaptcha;
use App\Http\Requests\PetFoundRequest;
use App\Jobs\SendPetFoundJob;
use App\Models\Pet;
use App\Models\PetFound;
use App\Models\QrCode;
use App\Models\QrCodeActivation;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

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

    public function petFound(PetFoundRequest $request, $token)
    {
        $response = $request->input('g-recaptcha-response');
        $captcha = new NoCaptcha(config('services.recaptcha.secret'), config('services.recaptcha.sitekey'));
        $isHuman = $captcha->verifyResponse($response);
        if ($isHuman) {
            $petQrActivation = QrCodeActivation::whereHas('qrCode', function($query) use ($token){
                $query->where('token', $token);
            })->first();
        
            if(is_null($petQrActivation))
            {
                return $this->errorResponse('No se encontró la mascota.', Response::HTTP_NOT_FOUND);
            }
            try 
            {
                DB::beginTransaction();

                $petFound = PetFound::create([
                    'phone' => $request->validated('phone'),
                    'firstname' => $request->validated('firstname'),
                    'email' => $request->validated('email'),
                    'pet_id' => $petQrActivation->pet_id
                ]);

                SendPetFoundJob::dispatch($petQrActivation, $petFound);

                DB::commit();

                return $this->successResponse(['message' => 'Solocitud envíada con éxito']);
            }
            catch(Exception $e)
            {
                DB::rollBack();
                return $this->errorResponse('Ocurrió un error al enviar la solicitud.'. $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            
        } else {
            return $this->errorResponse('No se pudo completar la acción porque no se verificó el captcha', Response::HTTP_BAD_REQUEST);
        }
    }
}
