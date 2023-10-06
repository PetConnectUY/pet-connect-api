<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponser;
use App\Traits\UUID;
use Illuminate\Http\Request;
use App\Models\QrCodeActivation;


class QrCodeController extends Controller
{
    use ApiResponser, UUID;

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

        return $this->successResponse($codes->with('pet')->paginate(12));
    }

}
