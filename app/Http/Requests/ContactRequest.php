<?php

namespace App\Http\Requests;

use App\Traits\ApiResponser;
use App\Models\Contact;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class ClientRequest extends FormRequest
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
        $model = new Contact();
     
        return [
            'name' => ['required'],
            'email' => ['required'],
            'message' => ['required'],
            'token' => ['required', Rule::exists('contacts', 'token')
                ->where('current_email', auth()->user()->email)],
            'wasSeen' => ['required'],
            'replyed' => ['required']
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El :attribute es requerido',
            'email.required' => 'El :attribute es requerido',
            'message.required' => 'El :attribute es requerido',
        ];
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = $this->errorResponse($validator->errors(), Response::HTTP_BAD_REQUEST);
        throw new ValidationException($validator, $response);
    }
}
