<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Http\Requests\PetImage\StoreRequest;
use App\Models\Contact;
use App\Models\User;
use App\Traits\UUID;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Exception;



class ContactController extends Controller
{
    use UUID;

    public function store(StoreRequest $request)
    {
        // Verificar si se ha enviado un contacto en los últimos 15 minutos
        // $lastContact = Contact::where('email', $request->email)
        //     ->where('created_at', '>', now()->subMinutes(15))
        //     ->first();

        // if ($lastContact) {
        //     return response()->json(['error' => 'Solo se permite un contacto cada 15 minutos.'], 429);
        // }

        // Verificar si ya se ha enviado un contacto hoy
        $dailyContact = Contact::where('email', $request->email)
            ->whereDate('created_at', now())
            ->first();

        if ($dailyContact) {
            return response()->json(['error' => 'Solo se permite un contacto por día.'], 429);
        }
        try
        {
            DB::beginTransaction();

            // Crear el nuevo contacto
            $token = $this->generateToken(new Contact(), 'token');
            $contact = Contact::create([
            'name' => $request->name,
            'email' => $request->email,
            'message' => $request->message,
            'token' => $token,
            'was_seen' => $request->wasSeen,
            'replyed' => $request->replyed,
            ]);

            DB::commit();

            return $this->successResponse($contact);
        }
        catch(Exception $e)
        {
            DB::rollBack();
            return $this->errorResponse('Ocurrió un error al registrar el contacto. '.$e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
