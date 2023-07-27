<?php

namespace App\Http\Controllers;

use App\Http\Requests\Pet\PutRequest;
use App\Http\Requests\Pet\StoreRequest;
use App\Models\Pet;
use App\Traits\ApiResponser;
use Illuminate\Http\Response;

class PetController extends Controller
{
    use ApiResponser;

    public function store(StoreRequest $request)
    {
        $pet = Pet::create([
            'name' => $request->validated('name'),
            'birth_date' => $request->validated('birth_date'),
            'race' => $request->validated('race'),
            'gender' => $request->validated('gender'),
            'pet_information' => $request->validated('pet_information'),
            'user_id' => auth()->user()->id,
        ]);
        $pet->load('user');

        return $this->successResponse($this->jsonResponse($pet));
    }

    public function update(PutRequest $request, $id)
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
            'user_id' => $request->validated('user_id'),
        ]);

        return $this->successResponse($this->jsonResponse($pet));
    }

    public function destroy($id)
    {
        $pet = Pet::find($id);
        if(!$pet)
        {
            return $this->errorResponse('Mascota no encontrada.', Response::HTTP_NOT_FOUND);
        }

        $pet->delete();

        return $this->successResponse($this->jsonResponse($pet));
    }

    private function jsonResponse($data)
    {
        return [
            'id' => $data->id,
            'name' => $data->name,
            'birth_date' => $data->birth_date,
            'race' => $data->race,
            'gender' => $data->gender,
            'pet_information' => $data->pet_information,
            'user' => [
                'id' => $data->user->id,
                'firstname' => $data->user->firstname,
                'lastname' => $data->user->lastname,
                'username' => $data->user->username,
                'birth_date' => $data->user->birth_date,
                'phone' => $data->user->phone,
                'address' => $data->user->address
            ]
        ];
    }
}
