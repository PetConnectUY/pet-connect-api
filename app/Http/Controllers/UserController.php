<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\PutRequest;
use App\Http\Requests\User\StoreRequest;
use App\Models\User;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use ApiResponser;
    public function store(StoreRequest $request)
    {
        $user = User::create([
            'firstname' => $request->validated('firstname'),
            'lastname' => $request->validated('lastname'),
            'username' => $request->validated('username'),
            'email' => $request->validated('email'),
            'password' => Hash::make($request->validated('password')),
            'birth_date' => $request->validated('birth_date'),
            'phone' => $request->validated('phone'),
            'address' => $request->validated('address')
        ]);

        return $this->successResponse($this->jsonResponse($user));
    }

    public function update(PutRequest $request, $id)
    {
        $user = User::find($id);
        if(!$user)
        {
            return $this->errorResponse('User not found', Response::HTTP_NOT_FOUND);
        }
        $user->update([
            'firstname' => $request->validated('firstname'),
            'lastname' => $request->validated('lastname'),
            'birth_date' => $request->validated('birth_date'),
            'phone' => $request->validated('phone'),
            'address' => $request->validated('address')
        ]);

        return $this->successResponse($this->jsonResponse($user));
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if(!$user)
        {
            return $this->errorResponse('User not found', Response::HTTP_NOT_FOUND);
        }
        $user->delete();

        return $this->successResponse($this->jsonResponse($user));
    }

    private function jsonResponse($data)
    {
        return [
            'id' => $data->id,
            'firstname' => $data->firstname,
            'lastname' => $data->lastname,
            'username' => $data->username,
            'email' => $data->email,
            'birth_date' => $data->birth_date,
            'phone' => $data->phone,
            'address' => $data->address,
        ];
    }
}
