<?php

namespace App\Http\Controllers;

use App\Models\QrCode;
use App\Models\QrCodeActivation;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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

        $existingActivation = QrCodeActivation::where('qr_code_id', $qrCode->id)
            ->first();

        $request->validate([
            'pet_id' => ['required', Rule::exists('pets', 'id')],
        ], [
            'pet_id.required' => 'El id de la mascota es requerida para activar el codigo qr.',
            'pet_id.exists' => 'El id de la mascota no existe.'
        ]);

        if($qrCode->is_used == true && $existingActivation && !is_null($existingActivation->pet_id))
        {
            return $this->successResponse(['message' => 'El código QR ya está en uso.']);
        } else if ($qrCode->is_used == true && $existingActivation && is_null($existingActivation->pet_id) && $existingActivation->user_id == auth()->user()->id)
        {
            try 
            {
                DB::beginTransaction();

                $existingActivation->pet_id = $request->input('pet_id');    
                $existingActivation->save();

                DB::commit();

                return $this->successResponse(['message' => 'La mascota fué asignada correctamente.']);
            } catch (Exception $e) 
            {
                DB::rollBack();
                return $this->errorResponse('Ocurrió un error al asignar la mascota al código qr.', Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } else if ($qrCode->is_used == true && $existingActivation && is_null($existingActivation->pet_id) && $existingActivation->user_id != auth()->user()->id) 
        {
            return $this->successResponse(['message' => 'El períl de la mascota no fué creado.']);
        } else if (is_null($existingActivation))
        {
            return $this->errorResponse('Debes seguir los pasos indecados para asignar el código QR de forma correcta.', Response::HTTP_BAD_REQUEST);
        }
    }

    public function verifyQrActivation($activationToken)
    {
        $qrCode = QrCode::where('token', $activationToken)
            ->first();
        
        if(is_null($qrCode))
        {
            return $this->errorResponse('No se encontró el código qr', Response::HTTP_NOT_FOUND);
        }

        $isActived = $qrCode->is_used == 0 ? 0 : 1;

        return $this->successResponse($isActived);
    }

    public function activateQrWithUser(Request $request, $activationToken)
    {
        // Validación inicial para verificar si el código QR existe
        $qrCode = QrCode::where('token', $activationToken)->first();
        
        if (is_null($qrCode)) {
            return $this->errorResponse('No se encontró el código QR', Response::HTTP_NOT_FOUND);
        }

        try {
            DB::beginTransaction();

            $existingActivation = QrCodeActivation::where('qr_code_id', $qrCode->id)->first();

            // Validación si el código QR ya está activado por el usuario actual
            if ($existingActivation && $existingActivation->user_id == auth()->user()->id) {
                return $this->successResponse(['message' => 'Código QR ya existe y está activado por el usuario']);
            }

            // Validación si el código QR está en uso por otro usuario y tiene un perfil de mascota
            if ($existingActivation && $existingActivation->user_id != auth()->user()->id && !is_null($existingActivation->pet_id)) {
                return $this->successResponse(['message' => 'El código QR ya está en uso por otro usuario.']);
            }

            // Si no se encontraron activaciones existentes, se procede a la creación
            QrCodeActivation::create([
                'qr_code_id' => $qrCode->id,
                'user_id' => auth()->user()->id,
            ]);

            $qrCode->is_used = true;
            $qrCode->save();

            DB::commit();

            return $this->successResponse(['message' => 'Se asignó el código QR con éxito']);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Ocurrió un error al crear la activación. '.$e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
