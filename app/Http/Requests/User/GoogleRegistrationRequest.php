<?php

namespace App\Http\Requests\User;

use App\Traits\ApiResponser;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class GoogleRegistrationRequest extends FormRequest
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
            'g-recaptcha-response' => ['required'],
            'firstname' => ['required', 'min:3', 'max:16'],
            'lastname' => ['required', 'min:3', 'max:16'],
            'email' => ['required', 'email', Rule::unique('users')->ignore(auth()->user()->id)],
            'password' => ['required', 'min:6'],
            'birth_date' => ['required', 'date'],
            'phone' => ['required'],
            'address' => ['required'],
        ];
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = $this->errorResponse($validator->errors(), Response::HTTP_BAD_REQUEST);
        throw new ValidationException($validator, $response);
    }
}
