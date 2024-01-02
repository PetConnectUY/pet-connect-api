<?php

namespace App\Http\Requests;

use App\Traits\ApiResponser;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ValidateExistentEmailRequest extends FormRequest
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
            'current_email' => ['required', 'email', Rule::exists('users', 'email')->where('id', auth()->user()->id)],
            'new_email' => ['required', 'email', 'unique:users,email'],
        ];
    }

    public function messages()
    {
        return [
            'current_email.required' => 'El email actual es requerido',
            'current_email.email' => 'El formato del email actual no es correcto',
            'current_email.exists' => 'El email actual no es correcto',
            'new_email.required' => 'El email nuevo es requerido',
            'new_email.email' => 'El formato del email nuevo no es correcto',
            'new_email.unique' => 'El email nuevo ya está registrado',
        ];
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = $this->errorResponse($validator->errors(), Response::HTTP_BAD_REQUEST);
        throw new ValidationException($validator, $response);
    }
}
