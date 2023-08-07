<?php

namespace App\Http\Requests;

use App\Traits\ApiResponser;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class ProductRequest extends FormRequest
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
            'price' => ['required'],
            'description' => ['required', 'min:3'],
            'image' => ['required', 'mimes:jpg,png,jpeg,webp,JPG,PNG,JPEG,WEBP']
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El nombre del producto es requerido.',
            'name.min' => 'El nombre debe tener al menos 3 caracteres.',
            'price.required' => 'El precio es requerido.',
            'description.required' => 'La descripción es requerida.',
            'description.min' => 'La descripción debe tener al menos 3 caracteres.',
            'image.required' => 'La imagen del producto es requreida.',
            'image.mimes' => 'El formato de imagen es incorrecto, debe ser de tipo jpg, png o jpeg.',
        ];
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = $this->errorResponse($validator->errors(), Response::HTTP_BAD_REQUEST);
        throw new ValidationException($validator, $response);
    }
}
