<?php

namespace App\Http\Requests\User;

use App\Traits\ApiResponser;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class PutRequest extends FormRequest
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
            'firstname' => ['required', 'min:3', 'max:16'],
            'lastname' => ['required', 'min:3', 'max:16'],
            'birth_date' => ['required', 'date'],
            'phone' => ['required'],
            'address' => ['required']
        ];
    }

    public function messages()
    {
        return [
            'firstname.required' => 'El nombre es requerido.',
            'firstname.min' => 'Son necesarios 3 caracteres para el nombre.',
            'firstname.max' => 'El máximo de caracteres para el nombre es de 16.',
            'lastname.required' => 'El apellido es requerido.',
            'lastname.min' => 'Son necesarios 3 caracteres para el apellido.',
            'lastname.max' => 'El máximo de caracteres para el apellido es de 16.',
            'birth_date.required' => 'La fecha de nacimiento es requerida',
            'birth_date.date' => 'El formato de la fecha es incorrecto',
            'phone.required' => 'El número de contacto es necesario.',
            'address.required' => 'La dirección es necesaria.',
        ];
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = $this->errorResponse($validator->errors(), Response::HTTP_BAD_REQUEST);
        throw new ValidationException($validator, $response);
    }
}
