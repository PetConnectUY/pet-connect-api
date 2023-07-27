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
            $role = Role::create([
                'name' => $request->validated('name'),
                'description' => $request->validated('description')
            ]);

            DB::commit();

            return $this->successResponse($role);
        }
        catch(Exception $e)
        {
            DB::rollBack();
            return $this->errorResponse('Error creando el rol. '.$e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try
        {
            DB::beginTransaction();
            $role = Role::find($id);

            if(is_null($role))
            {
                return $this->errorResponse('Rol no encontrado', Response::HTTP_NOT_FOUND);
            }
            
            $role->delete();
            DB::commit();

            return $this->successResponse($role);
        }
        catch(Exception $e)
        {
            DB::rollBack();
            return $this->errorResponse('Ocurrió un error al eliminar el rol'. $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
