<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;
use Tests\Traits\TestAuthorization;

class UserTest extends TestCase
{
    use RefreshDatabase, TestAuthorization;

    const USER_URL = 'users';
    const AUTH_URL = 'auth';

    // public function test_store_user()
    // {
    //     $data = [
    //         'firstname' => 'userTest',
    //         'lastname' => 'userTest',
    //         'email' => 'userTest@user.com',
    //         'password' => 'password',
    //         'birth_date' => '2000-02-20',
    //         'phone' => '096390295',
    //         'address' => 'userTest'
    //     ];
    //     $response = $this->withAuth()
    //         ->post(self::USER_URL, $data);
    //     $response->assertStatus(Response::HTTP_OK)
    //         ->assertExactJson([
    //             'id' => $response['id'],
    //             'firstname' => $response['firstname'],
    //             'lastname' => $response['lastname'],
    //             'email' => $response['email'],
    //             'birth_date' => $response['birth_date'],
    //             'phone' => $response['phone'],
    //             'address' => $response['address'],
    //         ]);
    // }

//    public function test_update_user()
//    {
//         $user = User::factory()->create();

//         $data = [
//             'username' => $user->username,
//             'password' => 'password',
//         ];

//         $response = $this->post(self::AUTH_URL.'/login', $data)
//             ->assertStatus(Response::HTTP_OK);
        
//         $updateData = [
//             'firstname' => 'updateTest',
//             'lastname' => 'updateTest',
//             'birth_date' => '2000-02-20',
//             'phone' => '096390295',
//             'address' => 'updateTest'
//         ];
        
//         $this->withHeader('Authorization', 'Bearer '. $response['access_token'])
//             ->post(self::USER_URL.'/'.$user->id, $updateData)
//             ->assertStatus(Response::HTTP_OK)
//             ->assertExactJson([
//                 'id' => $user->id,
//                 'firstname' => $updateData['firstname'],
//                 'lastname' => $updateData['lastname'],
//                 'username' => $user->username,
//                 'email' => $user->email,
//                 'birth_date' => $updateData['birth_date'],
//                 'phone' => $updateData['phone'],
//                 'address' => $updateData['address']
//             ]);
//    }

//    public function test_destroy_user()
//    {
//         $user = User::factory()->create();

//         $data = [
//             'username' => $user->username,
//             'password' => 'password',
//         ];

//         $response = $this->post(self::AUTH_URL.'/login', $data)
//             ->assertStatus(Response::HTTP_OK);

//         $this->withHeader('Authorization', 'Bearer '. $response['access_token'])
//             ->delete(self::USER_URL.'/'.$user->id)
//             ->assertStatus(Response::HTTP_OK)
//             ->assertExactJson([
//                 'id' => $user->id,
//                 'firstname' => $user->firstname,
//                 'lastname' => $user->lastname,
//                 'username' => $user->username,
//                 'email' => $user->email,
//                 'birth_date' => $user->birth_date,
//                 'phone' => $user->phone,
//                 'address' => $user->address,
//             ]);

//    }
}
