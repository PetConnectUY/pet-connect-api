<?php

namespace App\Http\Controllers;

use Anhskohbo\NoCaptcha\NoCaptcha;
use App\Models\Client;
use App\Models\QrCode;
use App\Models\QrCodeActivation;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class PetProfileController extends Controller
{
    use ApiResponser;

    public function view($id)
    {
        $client = Client::whereHas('client', function($query) {
            $query->where('deleted_at', null);
        })
        ->where('id', $id)
        ->first();

        if(is_null($client))
        {
            return $this->errorResponse('No se encontro el cliente', Response::HTTP_NOT_FOUND);
        }

        return $this->successResponse($client);
    }

    public function store(Request $request)
    {
        try
        {
            DB::beginTransaction();
            $client = Client::create([
                'name' => $request->validated('name'),
                'access_token' => $request->validated('access_token'),
                'address' => $request->validated('address'),
                'phone' => $request->validated('phone'),
                'contact_name' => $request->validated('contact_name'),
                'email' => $request->validated('email'),
            ]);

            DB::commit();

            return $this->successResponse($client);
        }
        catch(Exception $e)
        {
            DB::rollBack();
            return $this->errorResponse('Ocurrió un error al crear el cliente. '.$e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
   
    public function update(Request $request, $id)
    {
        $client = Client::find($id);
        if(!$client)
        {
            return $this->errorResponse('Client not found', Response::HTTP_NOT_FOUND);
        }
        try
        {
            DB::beginTransaction();

            $client->update([
                'name' => $request->validated('name'),
                'address' => $request->validated('address'),
                'phone' => $request->validated('phone'),
                'contact_name' => $request->validated('contact_name'),
            ]);

            DB::commit();
            return $this->successResponse($client);
        }
        catch(Exception $e)
        {
            return $this->errorResponse('Ocurrió un error al actualizar el cliente', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        $client = Client::find($id);
        if(!$client)
        {
            return $this->errorResponse('Client not found', Response::HTTP_NOT_FOUND);
        }
        try
        {
            DB::beginTransaction();
            $client->delete();
            DB::commit();

            return $this->successResponse($client);
        }
        catch(Exception $e)
        {
            return $this->errorResponse('Ocurrió un error al eliminar el cliente.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


}