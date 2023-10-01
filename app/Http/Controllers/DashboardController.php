<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Illuminate\Http\Response;
use App\Models\Pet;
use App\Models\QrCodeActivation;
use App\Models\User;

class DashboardController extends Controller
{
    use ApiResponser;

    public function getPets(Request $request)
    {
        $pets = Pet::where('deleted_at', null)
            ->where('user_id', auth()->user()->id)
            ->paginate(10);
        
        return $this->successResponse($pets);

    }

    public function getQrCodes(Request $request)
    {
        $codes = QrCodeActivation::where('qr_code_id', '!=', null)
            ->where('user_id', auth()->user()->id);
            
        if ($request->input('name')) {
            $petName = $request->input('name');
            $codes->whereHas('pet', function ($query) use ($petName) {
                $query->where('name', $petName);
            });
        }   

        if($request->input('start_date') && $request->input('end_date'))
        {
            $codes->where('created_at', '>=' , $request->input('start_date'))
                ->where('created_at', '<=' , $request->input('end_date'));
                
        }

        return $this->successResponse($codes->with('pet')->paginate(10));
    }

}


