<?php

namespace App\Http\Controllers;

use App\Models\Log\QrCodeActivation as LogQrCodeActivation;
use App\Models\QrCode;
use App\Models\QrCodeActivation;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class QrCodeActivationController extends Controller
{
    use ApiResponser;

    public function activate(Request $request, $activationToken)
    {
        $qrCode = QrCode::where('token', $activationToken)
            ->first();

        if(is_null($qrCode))
        {
            return $this->errorResponse('No se encontró el código qr.', Response::HTTP_NOT_FOUND);
        }

        if($qrCode->is_used == true)
        {
            $pet = QrCodeActivation::where('qr_code_id', $qrCode->id)
                ->first();

            return $this->successResponse($pet->pet);
        }

        $request->validate([
            'pet_id' => ['required', Rule::exists('pets', 'id')],
        ], [
            'pet_id.required' => 'El id de la mascota es requerida para activar el codigo qr.',
            'pet_id.exists' => 'El id de la mascota no existe.'
        ]);

        try {
            DB::beginTransaction();

            $qrCodeActivation = QrCodeActivation::create([
                'qr_code_id' => $qrCode->id,
                'user_id' => auth()->user()->id,
                'pet_id' => $request->input('pet_id'),
            ]);

            $qrCode->is_used = true;
            $qrCode->save();

            LogQrCodeActivation::create([
                'qr_code_id' => $qrCode->id,
                'user_id' => auth()->user()->id,
                'actived_at' => Date::now(),
            ]);
            DB::commit();

            return $this->successResponse($qrCodeActivation);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Ocurrió un error al activar el código qr.'. $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function verifyQrActivation($activationToken)
    {
        $qrCode = QrCode::where('token', $activationToken)
            ->first();

        $isActived = $qrCode->is_used == false ? false : true;

        return $this->successResponse($isActived);
    }
}
