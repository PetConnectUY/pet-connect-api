<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientBranchRequest;
use App\Models\Client;
use App\Models\ClientBranch;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ClientBranchController extends Controller
{
    use ApiResponser;
    public function store(ClientBranchRequest $request, $clientId)
    {
        $client = Client::find($clientId);
        if(is_null($client)) {
            return $this->errorResponse('El cliente no existe', Response::HTTP_NOT_FOUND);
        }

        $clientBranch = ClientBranch::create([
            'client_id' => $clientId,
            'contact_firstname' => $request->validated('contact_firstname'),
            'address' => $request->validated('address'),
            'phone' => $request->validated('phone'),
            'email' => $request->validated('email')
        ]);

        return $this->successResponse($clientBranch);
    }

    public function update(ClientBranchRequest $request, $clientId, $id) {
        $branch = ClientBranch::whereHas('clients', function($query) use($clientId) {
                $query->where('id', $clientId);
            })
            ->where('id', $id)
            ->first();
        
        if(is_null($branch)) {
            return $this->errorResponse('La sucursal no existe', Response::HTTP_NOT_FOUND);
        }

        $branch->update([
            'contact_firstname' => $request->validated('contact_firstname'),
            'address' => $request->validated('address'),
            'phone' => $request->validated('phone'),
            'email' => $request->validated('email')
        ]);

        return $this->successResponse($branch);
    }

    public function destroy($clientId, $id) {
        $client = Client::find($clientId);
        $branch = ClientBranch::find($id);
        if(is_null($client)) {
            return $this->errorResponse('El cliente no existe', Response::HTTP_NOT_FOUND);
        }
        
        if(is_null($branch)) {
            return $this->errorResponse('La sucursal no existe', Response::HTTP_NOT_FOUND);
        }

        $branch->delete();

        return $this->successResponse($branch);
    }
}
