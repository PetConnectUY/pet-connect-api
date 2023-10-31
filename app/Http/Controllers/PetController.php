<?php

namespace App\Http\Controllers;

use App\Http\Requests\PetRequest;
use App\Models\Pet;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class PetController extends Controller
{
    use ApiResponser;

    CONST PETS_PER_PAGE = 12;

    public function index(Request $request)
    {
        $pets = Pet::select('id', 'name')
        ->whereHas('user', function($query){
            $query->where('deleted_at', null);
        })
        ->doesntHave('activation')
        ->where('user_id', auth()->user()->id);

        return $this->successResponse($pets->paginate($request->input('total', self::PETS_PER_PAGE)));
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
                'type' => $request->validated('type'),
                'birth_date' => Carbon::parse($request->validated('birth_date'))->format('Y-m-d'),
                'race_id' => $request->validated('race_id'),
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
                'type' => $request->validated('type'),
                'birth_date' => Carbon::parse($request->validated('birth_date'))->format('Y-m-d'),
                'race_id' => $request->validated('race_id'),
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
            return $this->errorResponse('Ocurrió un error al actualizar la mascota. '.$e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
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
