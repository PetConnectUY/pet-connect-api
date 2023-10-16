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

        if(auth()->user())
        {
            if($qrCode->is_used)
            {
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
                     * El código QR ya está en uso, la activación no existe y el usaurio está autenticado
                     * Le asigna el código QR al usuario autenticado.
                     * Retorna un mensaje de éxito
                    */
                    case (is_null($qrCode->activation) && auth()->user()):
                        $cookie = Cookie::make(env('TOKEN_COOKIE_NAME'), $token, 60);
                        QrCodeActivation::create([
                            'qr_code_id' => $qrCode->id,
                            'user_id' => auth()->user()->id,
                        ]);
                        return $this->successResponse(['message' => 'Se asignó el código QR con éxito'])
                            ->withCookie($cookie);
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
                        return $this->errorResponse('Hay un caso no validado.', Response::HTTP_BAD_REQUEST);
                        break;
                }
            } else
            {
                switch($qrCode->activation)
                {
                    case (!is_null($qrCode->activation->user_id || !is_null($qrCode->activation->pet_id))):
                        return $this->successResponse($qrCode->activation->pet);
                        break;
                    case (is_null($qrCode->activation) && auth()->user()):
                        QrCodeActivation::create([
                            'qr_code_id' => $qrCode->id,
                            'user_id' => auth()->user()->id,
                        ]);
                        $qrCode->is_used = true;
                        $qrCode->save();
                        return $this->successResponse(['message' => 'Se asignó el código QR con éxito']);
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
                                return $this->errorResponse('Hay un caso no validado.', Response::HTTP_BAD_REQUEST);
                                break;
                }
            }
        }
        else 
        {
            return $this->errorResponse('Unauthorized', Response::HTTP_UNAUTHORIZED);
        }
    }

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
        $qrCode = QrCode::where('token', $activationToken)->first();

        if (is_null($qrCode)) {
            return $this->successResponse(['message' => 'No se encontró el código qr']);
        }

        if (!$qrCode->is_used) 
        {
            return $this->successResponse(['message' => 'Código qr no activado']);
        } 
        else 
        {
            if(!is_null($qrCode->activation->user_id))
            {
                if(!is_null($qrCode->activation->pet_id))
                {
                    return $this->successResponse(['message' => 'Código qr activado']);
                } else {
                    if(auth()->user() && $qrCode->activation->user_id == auth()->user()->id)
                    {
                        return $this->successResponse(['message' => 'Debe asignar mascota']);
                    } else if(auth()->user() && $qrCode->activation->user_id != auth()->user()->id) {
                        return $this->successResponse(['message' => 'Este qr no pertenece al usuario y no tiene una mascota asignada']);
                    } else {
                        return $this->errorResponse('Unauthenticated', Response::HTTP_UNAUTHORIZED);
                    }
                }
            } else {
                return $this->successResponse(['message' => 'Código qr en uso pero no activado']);
            }
        }
    }

    public function activateQrWithUser(Request $request, $activationToken)
    {
        $qrCode = QrCode::where('token', $activationToken)->first();
        
        if (is_null($qrCode)) {
            return $this->errorResponse('No se encontró el código QR', Response::HTTP_NOT_FOUND);
        }

        try {
            DB::beginTransaction();

            $existingActivation = QrCodeActivation::where('qr_code_id', $qrCode->id)->first();

            if ($existingActivation && $existingActivation->user_id == auth()->user()->id) {
                return $this->successResponse(['message' => 'Código QR ya existe y está activado por el usuario']);
            }

            if ($existingActivation && $existingActivation->user_id != auth()->user()->id && !is_null($existingActivation->pet_id)) {
                return $this->successResponse(['message' => 'El código QR ya está en uso por otro usuario']);
            }

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
