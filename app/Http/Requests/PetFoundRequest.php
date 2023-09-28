<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class PetFoundRequest extends FormRequest
{
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
            'g-recaptcha-response' => ['required'],
            'firstname' => ['required', 'min:3', 'max:16'],
            'lastname' => ['required', 'min:3', 'max:16'],
            'email' => ['required', 'email'],
            'phone' => ['nullable'],
            'location' => ['nullable']
        ];
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = $this->errorResponse($validator->errors(), Response::HTTP_BAD_REQUEST);
        throw new ValidationException($validator, $response);
    }
}
