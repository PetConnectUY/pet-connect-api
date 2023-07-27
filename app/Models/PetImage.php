<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PetImage extends Model
{
    use HasFactory;

    protected $table = 'pets_images';

    protected $fillable = [
        'pet_id',
        'name',
        'cover_image'
    ];

    const CREATED_AT = null;
    const UPDATED_AT = null;

    public function pet()
    {
        return $this->belongsTo(Pet::class, 'pet_id');
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'is_cover_image' => $this->cover_image,
            'pet' => [
                'id' => $this->pet->id,
                'name' => $this->pet->name,
                'birth_date' => $this->pet->birth_date,
                'race' => $this->pet->race,
                'gender' => $this->pet->gender,
                'pet_information' => $this->pet->pet_information,
                'user' => [
                    'id' => $this->pet->user->id,
                    'firstname' => $this->pet->user->firstname,
                    'lastname' => $this->pet->user->lastname,
                    'username' => $this->pet->user->username,
                    'email' => $this->pet->user->email,
                    'birth_date' => $this->pet->user->birth_date,
                    'phone' => $this->pet->user->phone,
                    'address' => $this->pet->user->address,
                    'role' => [
                        'id' => $this->pet->user->role->id,
                        'name' => $this->pet->user->role->name,
                        'description' => $this->pet->user->role->description
                    ]
                ],
            ],
        ];
    }
}
