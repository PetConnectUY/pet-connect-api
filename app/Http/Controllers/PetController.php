<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PetRequest;
use App\Models\Pet;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class PetController extends Controller
{
    use ApiResponser;

    public function index()
    {
        $pets = Pet::whereHas('user', function($query){
            $query->where('deleted_at', null);
        })
        ->where('user_id', auth()->user()->id)
        ->paginate(10);

        return $this->successResponse($pets);
    }

    public function view($id)
    {
        $pet = Pet::whereHas('user', function($query) {
            $query->where('deleted_at', null);
        })
        ->where('user_id', auth()->user()->id)
        ->where('id', $id)
        ->first();

        if(is_null($pet))
        {
            return $this->errorResponse('No se encontro la mascota', Response::HTTP_NOT_FOUND);
        }

        return $this->successResponse($pet);
    }

    public function store(PetRequest $request)
    {
        try
        {
            DB::beginTransaction();
            $pet = Pet::create([
                'name' => $request->validated('name'),
                'birth_date' => $request->validated('birth_date'),
                'race' => $request->validated('race'),
                'gender' => $request->validated('gender'),
                'pet_information' => $request->validated('pet_information'),
                'user_id' => auth()->user()->id,
            ]);
    
            DB::commit();

            return $this->successResponse($pet);
        }
        catch(Exception $e)
        {
            return $this->errorResponse('Ocurrió un error al crear la mascota. '.$e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(PetRequest $request, $id)
    {
        $pet = Pet::whereHas('user', function($query) {
                $query->where('deleted_at', null);
            })
            ->where('user_id', auth()->user()->id)
            ->where('id', $id)
            ->first();

        if(!$pet)
        {
            return $this->errorResponse('Mascota no encontrada.', Response::HTTP_NOT_FOUND);
        }

        try
        {
            DB::beginTransaction();

            $pet->update([
                'name' => $request->validated('name'),
                'birth_date' => $request->validated('birth_date'),
                'race' => $request->validated('race'),
                'gender' => $request->validated('gender'),
                'pet_information' => $request->validated('pet_information'),
                'user_id' => auth()->user()->id,
            ]);
    
            DB::commit();
            return $this->successResponse($pet);
        }
        catch(Exception $e)
        {
            DB::rollBack();
            return $this->errorResponse('Ocurrió un error al actualizar la mascota', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        $pet = Pet::whereHas('user', function($query) {
                $query->where('deleted_at', null);
            })
            ->where('user_id', auth()->user()->id)
            ->where('id', $id)
            ->first();

        if(!$pet)
        {
            return $this->errorResponse('Mascota no encontrada.', Response::HTTP_NOT_FOUND);
        }

        try
        {
            DB::beginTransaction();
            $pet->delete();
            DB::commit();
            
            return $this->successResponse($pet);
        }
        catch(Exception $e)
        {
            DB::rollBack();
            return $this->errorResponse('Ocurrió un error al eliminar la mascota', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
