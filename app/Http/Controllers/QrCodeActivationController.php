<?php

namespace App\Http\Controllers;

use App\Models\QrCode;
use App\Models\QrCodeActivation;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class QrCodeActivationController extends Controller
{
    use ApiResponser;

    public function manageQrCode(Request $request, $token)
    {
        $qrCode = QrCode::where('token', $token)
            ->first();

        if(is_null($qrCode))
        {
            return $this->errorResponse('El código qr no existe.', Response::HTTP_NOT_FOUND);
        }

        if($qrCode->is_used)
        {
            /*
                * El código QR ya está en uso, la activación no existe y el usaurio está autenticado
                * Le asigna el código QR al usuario autenticado.
                * Retorna un mensaje de éxito
            */
            if(is_null($qrCode->activation))
            {
                $cookie = Cookie::make(env('TOKEN_COOKIE_NAME'), $token, 60);
                QrCodeActivation::create([
                    'qr_code_id' => $qrCode->id,
                    'user_id' => auth()->user()->id,
                ]);
                return $this->successResponse(['message' => 'Se asignó el código QR con éxito'])
                    ->withCookie($cookie);
            }
            switch($qrCode->activation)
            {
                /* 
                    * El código QR ya está en uso, la activación tiene un usuario y una mascota.
                    * Retorna el pet en la solicitud.
                */
                case (!is_null($qrCode->activation->user_id || !is_null($qrCode->activation->pet_id))):
                    return $this->successResponse($qrCode->activation->pet);
                    break;
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
                    try 
                    {
                        DB::beginTransaction();

                        $qrCode->activation->pet_id = $request->input('pet_id');    
                        $qrCode->activation->save();

                        DB::commit();
                        $cookie = Cookie::make(env('TOKEN_COOKIE_NAME'), $token, 60);

                        return $this->successResponse(['message' => 'La mascota fué asignada correctamente.'])
                            ->withCookie($cookie);
                    } catch (Exception $e) 
                    {
                        DB::rollBack();
                        return $this->errorResponse('Ocurrió un error al asignar la mascota al código qr.', Response::HTTP_INTERNAL_SERVER_ERROR);
                    }
                    break;
                /*
                    * El código QR está en uso. Valida que la activación tenga un usuario y si no pertenece al usuario,
                    * retorna un mensaje de "BAD REQUEST" siempre y cuando el "pet_id" sea nulo.
                */
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
                $cookie = Cookie::make(env('TOKEN_COOKIE_NAME'), $token, 60);
                QrCodeActivation::create([
                    'qr_code_id' => $qrCode->id,
                    'user_id' => auth()->id()
                ]);
                $qrCode->is_used = true;
                $qrCode->save();
                // dd($cookie);
                return $this->successResponse(['message' => 'Se asignó el código QR con éxito'])
                    ->withCookie($cookie);
            }
            switch($qrCode->activation)
            {
                case (!is_null($qrCode->activation->user_id || !is_null($qrCode->activation->pet_id))):
                    return $this->successResponse($qrCode->activation->pet);
                    break;
                case (!is_null($qrCode->activation->user_id) && $qrCode->activation->user_id == auth()->id() && is_null($qrCode->activation->pet_id)):
                    $request->validate([
                        'pet_id' => ['required', Rule::exists('pets', 'id')],
                    ], [
                        'pet_id.required' => 'El id de la mascota es requerida para activar el codigo qr.',
                        'pet_id.exists' => 'El id de la mascota no existe.'
                    ]);
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
        }
    }
}
