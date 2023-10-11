<?php

namespace App\Http\Requests;

use App\Traits\ApiResponser;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PetRequest extends FormRequest
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
            'name' => ['required', 'min:3', 'regex:/^[a-zA-ZáÁéÉíÍóÓúÚñÑ\s]+$/u'],
            'birth_date' => ['nullable', 'date'],
            'race_id' => ['nullable', 'regex:/^[a-zA-ZáÁéÉíÍóÓúÚñÑ\s]+$/u', 'exists:pets_races,id'],
            'gender' => ['required', Rule::in(['male', 'female'])],
            'pet_information' => ['required', 'regex:/^[a-zA-ZñÑáÁéÉíÍóÓúÚüÜ\s\d.,!?-]*$/'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El nombre de la mascota es requerido.',
            'name.min' => 'El nombre de la mascota debe contener como minimo 3 caracteres.',
            'name.regex' => 'El nombre de la mascota contiene caracteres no admitidos.',
            'birth_date.date' => 'El formato de la fecha de nacimiento es incorrecto',
            'race_id.regex' => 'La raza de la mascota contiene caracteres no admitidos.',
            'race_id.exists' => 'La raza de la mascota no existe.',
            'gender.required' => 'El género es requerido.',
            'gender.in' => 'El formato del género es incorrecto.',
            'pet_information.required' => 'La información de la mascota es requerida.',
            'pet_information.regex' => 'Las características de la mascota contiene caracteres no admitidos.',
        ];
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = $this->errorResponse($validator->errors(), Response::HTTP_BAD_REQUEST);
        throw new ValidationException($validator, $response);
    }
}
