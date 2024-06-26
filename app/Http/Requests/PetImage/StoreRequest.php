<?php

namespace App\Http\Requests\PetImage;

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
            'pet_id' => ['required', 'exists:pets,id'],
            'image' => ['required', 'mimes:jpg,png,jpeg,JPG,PNG,JPEG'],
            'cover_image' => ['required', Rule::in(0, 1), Rule::unique('pets_images', 'cover_image')->where('pet_id', $this->pet_id)]
        ];
    }

    public function messages()
    {
        return [
            'pet_id.required' => 'La mascota es requerida.',
            'pet_id.exists' => 'La mascota no existe.',
            'image.required' => 'La imagen es requerida.',
            'image.mimes' => 'El formato de imagen es incorrecto, debe ser de tipo jpg, png o jpeg.',
            'cover_image.required' => 'Es necesario indicar la imagen inicial.',
            'cover_image.in' => 'El formato indicativo de la imagen inicial es incorrecto.',
            'cover_image.unique' => 'La mascota ya tiene una imagen inicial.',
        ];
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = $this->errorResponse($validator->errors(), Response::HTTP_BAD_REQUEST);
        throw new ValidationException($validator, $response);
    }
}
