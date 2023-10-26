<?php

namespace App\Http\Controllers;

use App\Models\QrCode;
use App\Models\QrCodeActivation;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class QrCodeActivationController extends Controller
{
    use ApiResponser;

    public function manageQrCode(Request $request, string $token)
    {
        $qrCode = QrCode::where('token', $token)
            ->first();
        
        if(is_null($qrCode))
        {
            return $this->errorResponse('El código qr no existe.', Response::HTTP_NOT_FOUND);
        }

        if(!is_null($qrCode->activation) && !is_null($qrCode->activation->user_id) && !is_null($qrCode->activation->pet_id))
        {
            return $this->successResponse($qrCode->activation->pet);
        }

        if(is_null(auth()->user()))
        {
            return $this->errorResponse('Unauthorized', Response::HTTP_UNAUTHORIZED);
        } 
        else
        {
            if($qrCode->is_used)
            {
                if(is_null($qrCode->activation))
                {
                    $qrCode->is_used = false;
                    $qrCode->save();
                    return $this->errorResponse('Intente nuevamente.', Response::HTTP_BAD_REQUEST);
                }

                switch($qrCode->activation)
                {
                    /*
                        * El código QR está en uso. La activación tiene un usuario y pertenece al usuario autenticado.
                        * Valida que no exista una mascota asignada a la activación
                        * Valida que exista y que esté en la solicitud
                        * Asigna la mascota que viene en el cuarpo de la solicitud mediante el "pet_id".
                        * Retorna un mensaje de éxito si no hay errores o excepciones.
                    */
                    case (!is_null($qrCode->activation->user_id) && $qrCode->activation->user_id == auth()->id() && is_null($qrCode->activation->pet_id)):
                        $request->validate([
                            'pet_id' => ['required', Rule::exists('pets', 'id')],
                        ], [
                            'pet_id.required' => 'El id de la mascota es requerida para activar el codigo qr.',
                            'pet_id.exists' => 'El id de la mascota no existe.'
                        ]);

                        if(is_null($request->input('pet_id')))
                        {
                            return $this->errorResponse('Debe asignar la mascota al código QR.', Response::HTTP_BAD_REQUEST);
                        }
                        try 
                        {
                            DB::beginTransaction();

                            $qrCode->activation->pet_id = $request->input('pet_id');    
                            $qrCode->activation->save();

                            DB::commit();

                            return $this->successResponse(['message' => 'La mascota fué asignada correctamente.']);
                        } catch (Exception $e) 
                        {
                            DB::rollBack();
                            return $this->errorResponse('Ocurrió un error al asignar la mascota al código qr.', Response::HTTP_INTERNAL_SERVER_ERROR);
                        }
                        break;
                    case (!is_null($qrCode->activation->user_id) && $qrCode->activation->user_id != auth()->id() && is_null($qrCode->activation->pet_id)):
                        return $this->errorResponse('Este código QR no te pertenece.', Response::HTTP_BAD_REQUEST);
                        break;
                    default: 
                        return $this->errorResponse('Hay un caso no cubierto.', Response::HTTP_BAD_REQUEST);
                        break;
                }
            } else 
            {
                if(is_null($qrCode->activation))
                {
                    QrCodeActivation::create([
                        'qr_code_id' => $qrCode->id,
                        'user_id' => auth()->user()->id,
                    ]);
                    $qrCode->is_used = true;
                    $qrCode->save();
                    return $this->successResponse(['message' => 'Se asignó el código QR con éxito']);
                }
            }
        }
    }

    public function getCookie(Request $request, string $token)
    {
        try
        {
            $cookie = Cookie::get('TOKEN_COOKIE');
            if(is_null($cookie))
            {
                if($token)
                {
                    try
                    {
                        $qrCode = QrCode::where('token', $token)
                            ->first();
                        if(is_null($qrCode))
                        {
                            return $this->errorResponse('No se encontró el código QR.', Response::HTTP_NOT_FOUND);
                        }
                        $qrCode->is_used = false;
                        $qrCode->save();
                        $qrCode->activation->delete();
                    }
                    catch(Exception $e)
                    {
                        DB::rollBack();
                        return $this->errorResponse('Ocurrió un error al eliminar los datos del código qr.', Response::HTTP_INTERNAL_SERVER_ERROR);
                    }
                }
                return $this->successResponse(['message' => 'La cookie expiró y se reinició la información del qr.']);
            }
            return $this->successResponse(['data' => decrypt($cookie)]);
        }
        catch(DecryptException)
        {
            return $this->errorResponse('No se pudo verificar la cookie.', Response::HTTP_BAD_REQUEST);
        }
    }

    private function makeCookie($token)
    {
        return Cookie::make('TOKEN_COOKIE', encrypt($token), 1440);
    }
}
