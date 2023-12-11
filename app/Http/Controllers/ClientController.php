<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientRequest;
use App\Models\Client;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    use ApiResponser;

    public function index(Request $request) {
        $clients = Client::where('deleted_at', null)
            ->get();

        return $this->successResponse($clients);
    }

    public function store(ClientRequest $request)
    {
        $client = Client::create([
            'name' => $request->validated('name'),
            'central_address' => $request->validated('central_address'),
        ]);

        return $this->successResponse($client);
    }

    public function update(ClientRequest $request, $id) {
        $client = Client::find($id);

        if(is_null($client))
        {
            return $this->errorResponse('No se encontró el cliente.', Response::HTTP_NOT_FOUND);
        }

        $client->update([
            'name' => $request->validated('name'),
            'central_address' => $request->validated('central_address')
        ]);

        return $this->successResponse($client);
    }


    public function destroy($id) 
    {
        $client = Client::find($id);

        if(is_null($client))
        {
            return $this->errorResponse('No se encontró el cliente.', Response::HTTP_NOT_FOUND);
        }
        
        $client->delete();

        return $this->successResponse($client);
    }
}