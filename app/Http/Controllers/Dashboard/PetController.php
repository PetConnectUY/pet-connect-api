<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PetRequest;
use App\Models\Pet;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class PetController extends Controller
{
    use ApiResponser;

    CONST PETS_PER_PAGE = 12;

    public function getPets(Request $request)
    {
        $pets = Pet::where('deleted_at', null)
            ->where('user_id', auth()->user()->id);

        if($request->input('name'))
        {
            $pets->where('name', 'LIKE', '%'.$request->input('name').'%');
        }

        if($request->input('birth_date'))
        {
            $pets->where('birth_date', $request->input('birth_date'));
        }
        
        return $this->successResponse($pets->paginate(self::PETS_PER_PAGE));
    }
}
