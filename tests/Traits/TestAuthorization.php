<?php

namespace Tests\Traits;

use App\Models\User;

trait TestAuthorization {
    public function withAuth() 
    {
        $user = User::factory()->create();
        $response = $this->json('post', 'api/auth/login', [
            'username' => $user->username,
            'password' => 'password'
        ]);
        return $this->withHeader('Authorization', 'Bearer '.$response['access_token']);
    }
}