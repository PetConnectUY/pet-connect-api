<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Tests\TestCase;
use Tests\Traits\TestAuthorization;

class PetImageTest extends TestCase
{
    use RefreshDatabase, TestAuthorization;

    const PET_URL = 'api/pets';
    const IMAGE_URL = 'api/pets-images';

    public function test_store_image()
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->image('test.png');
        $image = Image::make($file->path());
        Storage::put('test.png', $image->stream());


        $petData = [
            'name' => 'Test store',
            'birth_date' => '2022-07-20',
            'race' => 'unknow',
            'gender' => 'female',
            'pet_information' => 'test',
        ];

        $pet = $this->withAuth()
            ->post(self::PET_URL, $petData)
            ->assertStatus(Response::HTTP_OK);

        $imageData = [
            'pet_id' => $pet['id'],
            'image' => $file,
            'cover_image' => 0
        ];

        $image = $this->withAuth()
            ->post(self::IMAGE_URL, $imageData)
            ->assertStatus(Response::HTTP_OK);

        $image->assertExactJson([
            'id' => $image['id'],
            'pet_id' => $image['pet_id'],
            'name' => $image['name'],
            'cover_image' => $image['cover_image']
        ]);
    }

    public function test_update_image()
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->image('test.png');
        $image = Image::make($file->path());
        Storage::put('test.png', $image->stream());


        $petData = [
            'name' => 'Test store',
            'birth_date' => '2022-07-20',
            'race' => 'unknow',
            'gender' => 'female',
            'pet_information' => 'test',
        ];

        $pet = $this->withAuth()
            ->post(self::PET_URL, $petData)
            ->assertStatus(Response::HTTP_OK);

        $imageData = [
            'pet_id' => $pet['id'],
            'image' => $file,
            'cover_image' => 0
        ];

        $image = $this->withAuth()
            ->post(self::IMAGE_URL, $imageData)
            ->assertStatus(Response::HTTP_OK);
        
        $updateData = [
            'cover_image' => 1,
        ];

        $this->withAuth()
            ->post(self::IMAGE_URL.'/'.$image['id'], $updateData)
            ->assertStatus(Response::HTTP_OK)
            ->assertExactJson([
                'id' => $image['id'],
                'pet_id' => $image['pet_id'],
                'name' => $image['name'],
                'cover_image' => $updateData['cover_image'],
            ]);
    }

    public function test_destroy_image()
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->image('test.png');
        $image = Image::make($file->path());
        Storage::put('test.png', $image->stream());


        $petData = [
            'name' => 'Test store',
            'birth_date' => '2022-07-20',
            'race' => 'unknow',
            'gender' => 'female',
            'pet_information' => 'test',
        ];

        $pet = $this->withAuth()
            ->post(self::PET_URL, $petData)
            ->assertStatus(Response::HTTP_OK);

        $imageData = [
            'pet_id' => $pet['id'],
            'image' => $file,
            'cover_image' => 0
        ];

        $image = $this->withAuth()
            ->post(self::IMAGE_URL, $imageData)
            ->assertStatus(Response::HTTP_OK);
        
        $this->withAuth()
            ->delete(self::IMAGE_URL.'/'.$image['id'])
            ->assertStatus(Response::HTTP_OK)
            ->assertExactJson([
                'id' => $image['id'],
                'pet_id' => $image['pet_id'],
                'name' => $image['name'],
                'cover_image' => $image['cover_image'],
            ]);
    }
}
