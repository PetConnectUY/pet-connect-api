<?php

namespace App\Http\Requests;

use App\Traits\ApiResponser;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class PetSettingRequest extends FormRequest
{
    use ApiResponser;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'user_fullname_visible' => ['required'],
            'user_location_visible' => ['required'],
            'user_phone_visible' => ['required'],
            'user_email_visible' => ['required'],
        ];
    }

    public function attributes()
    {
        return [
            'user_fullname_visible' => 'La configuración del nombre',
            'user_location_visible' => 'La configuración de la locación',
            'user_phone_visible' => 'La configuración del teléfono',
            'user_email_visible' => 'La configuración del correo electrónico'
        ];
    }

    public function messages()
    {
        return [
            'user_fullname_visible.required' => ':attribute es requerida.',
            'user_fullname_visible.boolean' => ':attribute debe ser un booleano.',
            'user_location_visible.required' => ':attribute es requerida.',
            'user_location_visible.boolean' => ':attribute debe ser un booleano.',
            'user_phone_visible.required' =>  ':attribute es requerida.',
            'user_phone_visible.boolean' => ':attribute debe ser un booleano.',
            'user_email_visible.required' => ':attribute es requerida.',
            'user_email_visible.boolean' => ':attribute debe ser un booleano.'
        ];
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = $this->errorResponse($validator->errors(), Response::HTTP_BAD_REQUEST);
        throw new ValidationException($validator, $response);
    }
}
