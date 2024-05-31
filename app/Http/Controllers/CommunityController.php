<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class CommunityController extends Controller
{
    use ApiResponser;
    CONST PETS_PER_PAGE = 12;

    public function index(Request $request)
    {
        $petsToFeed = Pet::whereNotNull('user_id')
            ->whereHas('images')
            ->whereHas('activation')
            ->with('images')
            ->paginate($request->input('total', self::PETS_PER_PAGE));

        $formattedPets = $petsToFeed->getCollection()->transform(function ($pet) {
            return $pet->toFeedArray();
        });

        $petsToFeed->setCollection($formattedPets);

        return $this->successResponse($petsToFeed);
    }
}
