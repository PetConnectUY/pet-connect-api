<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubscribeRequest;
use App\Models\Subscription;
use App\Traits\ApiResponser;

class SubscriptionController extends Controller
{
    use ApiResponser;

    public function subscribe(SubscribeRequest $request)
    {
        $subscription = Subscription::create($request->validated());
        return $this->successResponse(['message' => 'Subscribed successfully']);
    }
}
