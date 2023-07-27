<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleRequest;
use App\Models\Role;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    use ApiResponser;

    public function store(RoleRequest $request)
    {
        try
        {
            DB::beginTransaction();
            $role = Role::create($request->validated());

            return $this->successResponse($role);
        }
        catch(Exception $e)
        {
            return $this->errorResponse('Error creando el rol. '.$e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
