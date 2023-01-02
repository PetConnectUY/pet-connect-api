<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;
use Tests\Traits\TestAuthorization;

class PetTest extends TestCase
{
    use RefreshDatabase, TestAuthorization;

    const PET_URL = 'api/pets';

    public function test_store_pet()
    {
        $petData = [
            'name' => 'Test store',
            'birth_year' => 2022,
        ];

        $this->withAuth()
            ->post(self::PET_URL, $petData)
            ->assertStatus(Response::HTTP_OK)
            ->assertExactJson([
                'id' => 1,
                'name' => $petData['name'],
                'birth_year' => $petData['birth_year'],
                'user' => [
                    'id' => auth()->user()->id,
                    'firstname' => auth()->user()->firstname,
                    'lastname' => auth()->user()->lastname,
                    'username' => auth()->user()->username,
                    'phone' => auth()->user()->phone,
                    'address' => auth()->user()->address
                ]
            ]);
    }

    public function test_update_pet()
    {
        $petData = [
            'name' => 'Test store',
            'birth_year' => 2022,
        ];

        $pet = $this->withAuth()
            ->post(self::PET_URL, $petData)
            ->assertStatus(Response::HTTP_OK);

        $updateData = [
            'name' => 'Test Update',
            'birth_year' => 0000,
        ];

        $this->withAuth()
            ->post(self::PET_URL.'/'.$pet['id'], $updateData)
            ->assertStatus(Response::HTTP_OK)
            ->assertExactJson([
                'id' => $pet['id'],
                'name' => $updateData['name'],
                'birth_year' => $updateData['birth_year'],
                'user' => [
                    'id' => auth()->user()->id,
                    'firstname' => auth()->user()->firstname,
                    'lastname' => auth()->user()->lastname,
                    'username' => auth()->user()->username,
                    'phone' => auth()->user()->phone,
                    'address' => auth()->user()->address
                ]
            ]);
    }

    public function test_destroy_pet()
    {
        $petData = [
            'name' => 'Test store',
            'birth_year' => 2022,
        ];

        $auth = $this->withAuth();

        $pet = $auth->post(self::PET_URL, $petData)
            ->assertStatus(Response::HTTP_OK);

        $auth->delete(self::PET_URL.'/'.$pet['id'])
            ->assertStatus(Response::HTTP_OK)
            ->assertExactJson([
                'id' => $pet['id'],
                'name' => $pet['name'],
                'birth_year' => $pet['birth_year'],
                'user' => [
                    'id' => auth()->user()->id,
                    'firstname' => auth()->user()->firstname,
                    'lastname' => auth()->user()->lastname,
                    'username' => auth()->user()->username,
                    'phone' => auth()->user()->phone,
                    'address' => auth()->user()->address
                ]
            ]);
    }
}
