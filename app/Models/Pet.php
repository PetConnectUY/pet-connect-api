<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pet extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'birth_date',
        'race',
        'gender',
        'pet_information',
        'user_id',
        'cover_image_id',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function images()
    {
        return $this->hasMany(PetImage::class, 'pet_id');
    }

    public function settings()
    {
        return $this->hasOne(UserPetProfileSetting::class, 'user_id', 'user_id');
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'birth_date' => $this->birth_date,
            'race' => $this->race,
            'gender' => $this->gender,
            'pet_information' => $this->pet_information,
            'images' => $this->images,
            'user' => [
                'id' => $this->user->id,
                'firstname' => $this->settings->user_fullname_visible == 1 ? $this->user->firstname : null,
                'lastname' => $this->settings->user_fullname_visible == 1 ? $this->user->lastname : null,
                'email' => $this->settings->user_email_visible == 1 ? $this->user->email : null,
                'birth_date' => $this->user->birth_date,
                'phone' => $this->settings->user_phone_visible == 1 ? $this->user->phone : null,
                'address' => $this->settings->user_location_visible == 1 ? $this->user->address : null,
                'role' => [
                    'id' => $this->user->role->id,
                    'name' => $this->user->role->name,
                    'description' => $this->user->role->description
                ]
            ]
        ];
    }
}
