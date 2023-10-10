<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pet;
use App\Traits\ApiResponser;

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

        if($request->input('race'))
        {
            $pets->where('race', $request->input('race'));
        }

    
        if($request->input('start_date'))
        {
            $pets->where('created_at', '>=', $request->input('start_date'));
        }

        if($request->input('end_date'))
        {
            $pets->where('created_at', '<=', $request->input('end_date'));
        }

        if($request->input('order'))
        {
            if($request->input('order') == 'a_z')
                $pets->orderBy('name', 'asc');
            if($request->input('order') == 'z_a')
                $pets->orderBy('name', 'desc') ;          

        }
        
        return $this->successResponse($pets->paginate(self::PETS_PER_PAGE));
    }
}
