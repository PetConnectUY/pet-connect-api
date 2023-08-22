<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StatisticController extends Controller
{
    use ApiResponser;
    public function userStatistic()
    {
        $user = User::find(auth()->user()->id);
        if(!$user)
        {
            return $this->errorResponse('No se encontró el usuario', Response::HTTP_NOT_FOUND);
        }
        $stats = [
            'total_pets' => $user->pets()->count(),
            'total_tokens' => $user->petTokens()->count(),
        ];

        return $this->successResponse($stats);
    }
}
