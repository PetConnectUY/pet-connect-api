<?php

namespace App\Http\Requests;

use App\Traits\ApiResponser;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class ClientBranchRequest extends FormRequest
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
            'contact_firstname' => ['required', 'min:3', 'max:20'],
            'address' => ['required'],
            'phone' => ['required'],
            'email' => ['email'],
        ];
    }

    public function attributes()
    {
        return [
            'client_id' => 'Cliente',
            'contact_firtstname' => 'Nombre del contacto',
            'address' => 'Dirección',
            'phone' => 'Teléfono',
            'email' => 'Correo electrónico'
        ];
    }

    public function messages() {
        return [
            'client_id.required' => 'El :attribute es requerido',
            'client_id.exists' => 'El :attribute no existe',
            'contact_firstname.required' => 'El :attribute es requerido',
            'contact_firstname.min' => 'El :attribute debe contener al menos 3 caracteres',
            'contact_firstname.max' => 'El :attribute debe contener como máximo 20 caracteres',
            'phone.required' => 'El :attribute es requerido',
            'email.email' => 'El :attribute no tiene un formato correcto',
        ];
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = $this->errorResponse($validator->errors(), Response::HTTP_BAD_REQUEST);
        throw new ValidationException($validator, $response);
    }
}
