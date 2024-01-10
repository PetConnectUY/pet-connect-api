<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\ConfirmEmailChangeRequest;
use App\Models\UserPetProfileSetting;
use App\Http\Requests\PetSettingRequest;
use App\Http\Requests\ValidateExistentEmailRequest;
use App\Jobs\ChangeEmailJob;
use App\Jobs\ChangePasswordJob;
use App\Jobs\EmailChangedJob;
use App\Models\User;
use App\Models\UserEmailChange;
use App\Traits\ApiResponser;
use App\Traits\UUID;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use ApiResponser, UUID;

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

    public function changePassword(ChangePasswordRequest $request) 
    {
        $user = User::find(auth()->user()->id);

        if(is_null($user))
        {
            return $this->errorResponse('No se encontró el usuario', Response::HTTP_NOT_FOUND);
        }

        if(!Hash::check($request->input('current_password'), $user->password)) {
            return $this->errorResponse('La contraseña actual no es válida', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user->password = Hash::make($request->input('new_password'));
        $user->save();

        ChangePasswordJob::dispatch($user->firstname. ' '. $user->lastname, $user->email);

        return $this->successResponse(['message' => 'Contraseña cambiada exitosamente']);
    }

    public function validateExistentEmail(ValidateExistentEmailRequest $request) 
    {
        try
        {
            DB::beginTransaction();

            $checkExistentChange = UserEmailChange::where('current_email', $request->validated('current_email'))
                ->where('changed', 0)
                ->first();
            
            if($checkExistentChange) {
                if($checkExistentChange->new_email != $request->validated('new_email'))
                {
                    $checkExistentChange->update(['new_email' => $request->validated('new_email')]);
                    DB::commit();
                    ChangeEmailJob::dispatch($checkExistentChange->current_email, $checkExistentChange->token);
                    return $this->successResponse(['message' => 'Código generado con éxito.']);
                } else
                {
                    $tokenExpirationTime = Carbon::parse($checkExistentChange->created_at)->addMinutes(30);
                    if(Carbon::now()->gt($tokenExpirationTime))
                    {
                        $checkExistentChange->update(['created_at', Carbon::now()]);
                        ChangeEmailJob::dispatch($checkExistentChange->current_email, $checkExistentChange->token);
                        DB::commit();
                        return $this->successResponse(['message' => 'Código generado con éxito.']);
                    }
                    ChangeEmailJob::dispatch($checkExistentChange->current_email, $checkExistentChange->token);
                    return $this->successResponse(['message' => 'Código generado con éxito.']);
                }
            }

            $emailChange = new UserEmailChange();
            $emailChange->current_email = $request->validated('current_email');
            $emailChange->new_email = $request->validated('new_email');
            $emailChange->token = $this->generateToken($emailChange, 'token');
            $emailChange->changed = 0;
            $emailChange->save();

            ChangeEmailJob::dispatch($emailChange->current_email, $emailChange->token);

            DB::commit();
            return $this->successResponse(['message' => 'Código generado con éxito.']);
        } 
        catch(Exception $e)
        {
            DB::rollBack();
            return $this->errorResponse(['message' => 'Error al validar el email'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function confirmChangeEmail(ConfirmEmailChangeRequest $request)
    {
        try
        {
            DB::beginTransaction();
            $emailChange = UserEmailChange::where('token', $request->validated('token'))
                ->first();
            if(is_null($emailChange))
            {
                return $this->errorResponse('No se encontró el cambió de email', Response::HTTP_NOT_FOUND);            
            }
            $tokenExpirationTime = Carbon::parse($emailChange->created_at)->addMinutes(30);
            if(Carbon::now()->gt($tokenExpirationTime))
            {
                $emailChange->delete();
                return $this->errorResponse('El código de confirmación ya expiro, debes generar otro', Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            $user = User::where('email', $emailChange->current_email)
                ->where('id', auth()->user()->id)
                ->first();
            
            if(is_null($user))
            {
                return $this->errorResponse('No se encontró el usuario', Response::HTTP_NOT_FOUND);
            }

            $user->update([
                'email' => $emailChange->new_email,
            ]);

            $emailChange->update([
                'changed' => 1,
            ]);

            DB::commit();
            EmailChangedJob::dispatch($user->email, $user->firstname. ' '. $user->lastname);

            return $this->successResponse(['message' => 'Email actualizado con éxito']);
        } catch(Exception $e)
        {
            DB::rollBack();
            return $this->errorResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
