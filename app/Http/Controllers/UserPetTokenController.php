<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserPetTokenRequest;
use App\Models\UserPetToken;
use App\Traits\ApiResponser;
use App\Traits\UUID;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class UserPetTokenController extends Controller
{
    use ApiResponser, UUID;

    public function generateQRCode(UserPetTokenRequest $request)
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
            return $this->errorResponse('Ocurrió un error al generar el código QR', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
