<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;
use Tests\Traits\TestAuthorization;

class AuthTest extends TestCase
{
    use RefreshDatabase, TestAuthorization;

    const AUTH_URL = 'auth';

    public function test_login_valid()
    {
        $user = User::factory()->create();

       $role = $this->UserRoleFunction($user);

        $response = $this->post(self::AUTH_URL.'/login', [
            'email' => $user->email,
            'password' => 'password',
        ])

         ->assertStatus(Response::HTTP_OK)
         ->assertJsonStructure(['access_token', 'token_type', 'expires_in', 'user']);

        $this->assertEquals($response['user'], [
            'id' => $user->id,
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'email' => $user->email,
            'birth_date' => $user->birth_date,
            'phone' => $user->phone,
            'address' => $user->address,
             'role' => [
                'id' => $role->id,
                'name' => $role->name,
                'description' => $role->description
                ]
            ]);

    }

    public function test_login_invalid()
    {
        $data = [
            'username' => 'dasd',
            'password' => 'dasd',
        ];

        $this->post(self::AUTH_URL.'/login', $data)
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJsonFragment(['error' => 'Error email or password.']);
    }

    public function test_logout()
    {
        $user = User::factory()->create();

        $role = $this->UserRoleFunction($user);

        $response = $this->post(self::AUTH_URL.'/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->withHeader('Authorization', 'Bearer '. $response['access_token'])
            ->post(self::AUTH_URL.'/logout')
            ->assertStatus(Response::HTTP_OK)
            ->assertExactJson([
                'message' => 'Session closed.'
            ]);
    }

    public function test_refresh_token_without_auth()
    {
        $this->post(self::AUTH_URL.'/refresh')
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_refresh_token_valid()
    {
        $this->withAuth()
            ->post(self::AUTH_URL.'/refresh')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['access_token', 'token_type', 'expires_in', 'user']);
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
