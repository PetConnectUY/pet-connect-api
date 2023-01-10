<?php

namespace App\Http\Requests\Pet;

use App\Traits\ApiResponser;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StoreRequest extends FormRequest
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
            'name' => ['required', 'min:3'],
            'birth_date' => ['nullable', 'date'],
            'race' => ['required'],
            'gender' => ['required', Rule::in(['male', 'female'])],
            'pet_information' => ['required'],
            'user_id' => ['exists:users'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El nombre de la mascota es requerido.',
            'name.min' => 'El nombre de la mascota debe contener como minimo 3 caracteres.',
            'birth_date.date' => 'El formato de la fecha de nacimiento es incorrecto',
            'race.required' => 'La raza es requerida',
            'gender.required' => 'El género es requerido.',
            'gender.in' => 'El formato del género es incorrecto.',
            'pet_information.required' => 'La información de la mascota es requerida.',
            'user_id.required' => 'El usuario es requerido',
            'user_id.exists' => 'El usuario no existe.',
        ];
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = $this->errorResponse($validator->errors(), Response::HTTP_BAD_REQUEST);
        throw new ValidationException($validator, $response);
    }
}
