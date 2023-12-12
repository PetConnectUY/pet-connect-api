<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientRequest;
use App\Models\Client;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use App\Traits\Image;
use App\Traits\UUID;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    use ApiResponser, UUID, Image;

    public function index(Request $request) 
    {
        $clients = Client::where('deleted_at', null)
            ->get();

        return $this->successResponse($clients);
    }

    public function store(ClientRequest $request)
    {
        $client = new Client();
        $client->name = $request->validated('name');
        $client->central_address = $request->validated('central_address');
        if($request->has('image')) {
            $imgName = $this->generateUUID(new Client, 'name');
            $imgExtension = $request->file('image')->getClientOriginalExtension();

            try 
            {
                $this->createImages($request->file('image'), env('CLIENT_IMAGES_FOLDER'), $imgName, $imgExtension);
                $imageUrl = asset('storage/'.env('CLIENT_IMAGES_FOLDER') . $imgName.'.'.$imgExtension);
                $client->url = $imageUrl;
                $client->save();
            }
            catch(Exception $e) 
            {
                return $this->errorResponse('Ocurrió un error al subir la imagen del cliente.', Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        return $this->successResponse($client);
    }

    public function update(ClientRequest $request, $id) 
    {
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