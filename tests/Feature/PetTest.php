<?php

namespace Tests\Feature;

use App\Models\User;
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
        $user = User::factory()->create();
        $petData = [
            'name' => 'Test store',
            'birth_date' => '2022-07-20',
            'race' => 'unknow',
            'gender' => 'female',
            'pet_information' => 'test',
            'user_id' => $user->id,
        ];

        $this->withAuth()
            ->post(self::PET_URL, $petData)
            ->assertStatus(Response::HTTP_OK)
            ->assertExactJson([
                'id' => 1,
                'name' => $petData['name'],
                'birth_date' => $petData['birth_date'],
                'race' => $petData['race'],
                'gender' => $petData['gender'],
                'pet_information' => $petData['pet_information'],
                'user' => [
                    'id' => $user->id,
                    'firstname' => $user->firstname,
                    'lastname' => $user->lastname,
                    'username' => $user->username,
                    'birth_date' => $user->birth_date,
                    'phone' => $user->phone,
                    'address' => $user->address
                ]
            ]);
    }

    public function test_update_pet()
    {
        $user = User::factory()->create();

        $petData = [
            'name' => 'Test store',
            'birth_date' => '2022-07-20',
            'race' => 'unknow',
            'gender' => 'female',
            'pet_information' => 'test',
            'user_id' => 1,
        ];

        $pet = $this->withAuth()
            ->post(self::PET_URL, $petData)
            ->assertStatus(Response::HTTP_OK);

        $updateData = [
            'name' => 'Test Update',
            'birth_date' => '2020-02-02',
            'race' => 'asdas',
            'gender' => 'male',
            'pet_information' => 'adsdas',
            'user_id' => $user->id,
        ];

        $this->withAuth()
            ->post(self::PET_URL.'/'.$pet['id'], $updateData)
            ->assertStatus(Response::HTTP_OK)
            ->assertExactJson([
                'id' => $pet['id'],
                'name' => $updateData['name'],
                'birth_date' => $updateData['birth_date'],
                'race' => $updateData['race'],
                'gender' => $updateData['gender'],
                'pet_information' => $updateData['pet_information'],
                'user' => [
                    'id' => $user->id,
                    'firstname' => $user->firstname,
                    'lastname' => $user->lastname,
                    'username' => $user->username,
                    'birth_date' => $user->birth_date,
                    'phone' => $user->phone,
                    'address' => $user->address
                ]
            ]);
    }

    public function test_destroy_pet()
    {
        $user = User::factory()->create();
        $auth = $this->withAuth();

        $petData = [
            'name' => 'Test Update',
            'birth_date' => '2020-02-02',
            'race' => 'asdas',
            'gender' => 'male',
            'pet_information' => 'adsdas',
            'user_id' => $user->id,
        ];

        $pet = $auth->post(self::PET_URL, $petData)
            ->assertStatus(Response::HTTP_OK);

        $auth->delete(self::PET_URL.'/'.$pet['id'])
            ->assertStatus(Response::HTTP_OK)
            ->assertExactJson([
                'id' => $pet['id'],
                'name' => $pet['name'],
                'birth_date' => $pet['birth_date'],
                'race' => $pet['race'],
                'gender' => $pet['gender'],
                'pet_information' => $pet['pet_information'],
                'user' => [
                    'id' => $user->id,
                    'firstname' => $user->firstname,
                    'lastname' => $user->lastname,
                    'username' => $user->username,
                    'birth_date' => $user->birth_date,
                    'phone' => $user->phone,
                    'address' => $user->address
                ]
            ]);
    }
}
