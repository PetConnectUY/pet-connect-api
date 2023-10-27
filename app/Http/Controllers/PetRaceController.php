<?php

namespace App\Http\Controllers;

use App\Models\PetRace;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PetRaceController extends Controller
{
    use ApiResponser;

    public function index(Request $request)
    {
        $query = PetRace::query();
        $query->where('type', $request->input('type', 'd'));
        return $this->successResponse($query->get());
    }

    public function store(Request $request)
    {
        $messages = [
            'name.required' => 'El nombre de la raza es obligatoria.',
            'name.min' => 'El nombre de la raza debe contener al menos :min caracteres',
        ];

        $validated = $request->validate([
            'name' => 'required|min:3',
        ], $messages);

        $petRace = PetRace::create([
            'name' => $request->input('name'),
        ]);

        return $this->successResponse($petRace);
    }

    public function destroy($id)
    {
        $petRace = PetRace::find($id);
        if(is_null($petRace))
        {
            return $this->errorResponse('No se encontró la raza de la mascota', Response::HTTP_NOT_FOUND);
        }

        $petRace->delete();
        
        return $this->successResponse($petRace);
    }
}
