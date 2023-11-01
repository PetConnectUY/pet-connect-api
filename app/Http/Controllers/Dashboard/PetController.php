<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pet;
use App\Traits\ApiResponser;

class PetController extends Controller
{
    use ApiResponser;

    CONST PETS_PER_PAGE = 8;

    public function getPets(Request $request)
    {
        $pets = Pet::where('deleted_at', null)
            ->where('user_id', auth()->user()->id)
            ->orderBy('name', 'asc');

        $perPage = self::PETS_PER_PAGE;

        $request->input('total') != null ? $perPage = $request->input('total') : $perPage;
        
        return $this->successResponse($pets->paginate($perPage));
    }
}
