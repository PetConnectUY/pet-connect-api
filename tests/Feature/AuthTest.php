<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;
use Tests\Traits\TestAuthorization;

class AuthTest extends TestCase
{
    use RefreshDatabase, TestAuthorization;

    const AUTH_URL = 'api/auth';

    public function test_login_valid()
    {
        $user = User::factory()->create();

        $response = $this->post(self::AUTH_URL.'/login', [
            'username' => $user->username,
            'password' => 'password',
        ])->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure(['access_token', 'token_type', 'expires_in', 'user']);

        $this->assertEquals($response['user'], [
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'username' => $user->username,
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
            ->assertJsonFragment(['error' => 'Error username or password.']);
    }

    public function test_logout()
    {
        $user = User::factory()->create();
        $data = [
            'username' => $user->username,
            'password' => 'password',
        ];

        $response = $this->post(self::AUTH_URL.'/login', $data);
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
}
