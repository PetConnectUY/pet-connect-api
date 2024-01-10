<?php

namespace App\Http\Requests;

use App\Models\UserEmailChange;
use App\Traits\ApiResponser;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ConfirmEmailChangeRequest extends FormRequest
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
        $model = new UserEmailChange();
        return [
            'token' => ['required', Rule::exists('user_email_changes', 'token')
                ->where('current_email', auth()->user()->email)],
        ];
    }

    public function messages()
    {
        return [
            'token.required' => 'El código de confirmación es requerido',
            'token.exists' => 'El código de confirmación no existe',
        ];
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = $this->errorResponse($validator->errors(), Response::HTTP_BAD_REQUEST);
        throw new ValidationException($validator, $response);
    }
}
