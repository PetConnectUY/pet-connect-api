<?php

namespace App\Http\Controllers;

use App\Models\QrCode;
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
}
