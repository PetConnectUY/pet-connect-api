<?php

namespace App\Http\Controllers;

use App\Http\Requests\PetRequest;
use App\Models\Pet;
use App\Traits\ApiResponser;
use Illuminate\Http\Response;

class PetController extends Controller
{
    use ApiResponser;

    public function store(PetRequest $request)
    {
        $pet = Pet::create([
            'name' => $request->validated('name'),
            'birth_date' => $request->validated('birth_date'),
            'race' => $request->validated('race'),
            'gender' => $request->validated('gender'),
            'pet_information' => $request->validated('pet_information'),
            'user_id' => auth()->user()->id,
        ]);

        return $this->successResponse($pet);
    }

    public function update(PetRequest $request, $id)
    {
        $pet = Pet::find($id);
        if(!$pet)
        {
            return $this->errorResponse('Mascota no encontrada.', Response::HTTP_NOT_FOUND);
        }

        $pet->update([
            'name' => $request->validated('name'),
            'birth_date' => $request->validated('birth_date'),
            'race' => $request->validated('race'),
            'gender' => $request->validated('gender'),
            'pet_information' => $request->validated('pet_information'),
            'user_id' => auth()->user()->id,
        ]);

        return $this->successResponse($pet);
    }

    public function destroy($id)
    {
        $pet = Pet::find($id);
        if(!$pet)
        {
            return $this->errorResponse('Mascota no encontrada.', Response::HTTP_NOT_FOUND);
        }

        $pet->delete();

        return $this->successResponse($pet);
    }
}
