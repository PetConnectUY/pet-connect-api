<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserPetTokenRequest;
use App\Models\UserPetToken;
use App\Traits\ApiResponser;
use App\Traits\UUID;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class UserPetTokenController extends Controller
{
    use ApiResponser, UUID;

    public function generateToken(UserPetTokenRequest $request)
    {
        try
        {
            DB::beginTransaction();
            $token = $this->generateUUID(UserPetToken::class, 'token');

            $userPetToken = UserPetToken::create([
                'token' => $token,
                'pet_id' => $request->validated('pet_id'),
            ]);

            DB::commit();

            return $this->successResponse($userPetToken);
        }
        catch(Exception $e)
        {
            DB::rollBack();
            return $this->errorResponse('Ocurrió un error crear el token.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        $petToken = UserPetToken::whereHas('pet', function($query) {
                $query->where('user_id', auth()->user()->id);
            })
            ->where('id', $id)
            ->first();

        if(is_null($petToken))
        {
            return $this->errorResponse('El token no se encontró', Response::HTTP_NOT_FOUND);
        }

        try {
            DB::beginTransaction();

            $petToken->delete();

            DB::commit();

            return $this->successResponse($petToken);
        }
        catch(Exception $e) 
        {
            DB::rollBack();
            return $this->errorResponse('Ocurrió un error al eliminar el token.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function trashed()
    {
        try
        {
            $petTokens = UserPetToken::onlyTrashed()
                ->get();

            return $this->successResponse($petTokens);
        }
        catch (QueryException $e)
        {
            return $this->errorResponse('Ocurrió un error al obtener los tokens eliminados', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function restoreTrashed($id)
    {
        try
        {
            DB::beginTransaction();
            $petToken = UserPetToken::withTrashed()
                ->whereHas('pet', function($query){
                    $query->where('user_id', auth()->user()->id);
                })
                ->where('id', $id)
                ->first();

            if (!$petToken) {
                return $this->errorResponse('El token de mascota no fue encontrado', Response::HTTP_NOT_FOUND);
            }

            $petToken->restore();
            DB::commit();

            return $this->successResponse($petToken);
        }
        catch (QueryException $e)
        {
            DB::rollBack();
            return $this->errorResponse('Ocurrió un error al restaurar el token de mascota', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


}
