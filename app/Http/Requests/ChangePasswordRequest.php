<?php

namespace App\Http\Requests;

use App\Traits\ApiResponser;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class ChangePasswordRequest extends FormRequest
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
            'current_password' => ['required'], 
            'new_password' => ['required', 'min:6', 'required_with:confirm_password', 'same:confirm_password', function ($attribute, $value, $fail) {
                if ($value === $this->input('current_password')) {
                    $fail('La :attribute no puede ser igual a la Contraseña actual.');
                }
            }],
            'confirm_password' => ['min:6']
        ];
    }

    public function attributes()
    {
        return [
            'current_password' => 'Contraseña actual',
            'new_password' => 'Contraseña nueva',
            'confirm_password' => 'Contraseña de confirmación',
        ];
    }

    public function messages()
    {
        return [
            'current_password.required' => 'La :attribute es requerida',
            'new_password.required' => 'La :attribute es requerida',
            'new_password.min' => 'La :attribute debe contener al menos 6 caracteres',
            'new_password.required_with' => 'La :attribute debe coincidir con la Contraseña de confirmación',
            'new_password.same' => 'La :attribute debe ser igual a la Contraseña de confirmación',
            'confirm_password.min' => 'La :attribute debe contener al menos 6 caracteres',
        ];
    }
    
    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = $this->errorResponse($validator->errors(), Response::HTTP_BAD_REQUEST);
        throw new ValidationException($validator, $response);
    }
}
