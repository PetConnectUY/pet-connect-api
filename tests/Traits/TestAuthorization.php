<?php

namespace Tests\Traits;

use App\Models\Role;
use App\Models\UserRole;
use App\Models\User;

trait TestAuthorization {
    public function withAuth() 
    {
        $user = User::factory()->create();
        $role = $this->UserRoleFunction($user);
        $response = $this->json('post', 'auth/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);
        return $this->withHeader('Authorization', 'Bearer '.$response['access_token']);
    }

    private function UserRoleFunction($user){
        $role = Role::create([
            'name' => 'rol',
            'description' => 'descripcion'
        ]);

        $userRol = UserRole::create([
            'user_id' => $user->id,
            'role_id' => $role->id
        ]);
        return $role;
    }
}