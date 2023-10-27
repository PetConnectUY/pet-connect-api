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
        'type',
        'birth_date',
        'race_id',
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

    public function activation()
    {
        return $this->hasOne(QrCodeActivation::class, 'pet_id');
    }

    public function race()
    {
        return $this->hasOne(PetRace::class, 'id', 'race_id');
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'name' => $this->name,
            'birth_date' => $this->birth_date,
            'race' => $this->race,
            'gender' => $this->gender,
            'pet_information' => $this->pet_information,
            'images' => $this->images,
            'user' => [
                'firstname' => $this->settings && $this->settings->user_fullname_visible == 1 ? $this->user->firstname : null,
                'lastname' => $this->settings && $this->settings->user_fullname_visible == 1 ? $this->user->lastname : null,
                'email' => $this->settings && $this->settings->user_email_visible == 1 ? $this->user->email : null,
                'phone' => $this->settings && $this->settings->user_phone_visible == 1 ? $this->user->phone : null,
                'address' => $this->settings && $this->settings->user_location_visible == 1 ? $this->user->address : null,
            ],
            'pet_token' => [
                'id' => $this->activation && $this->activation->id != null ? $this->activation->id : null,
                'token' => $this->activation && $this->activation->qrCode->token != null ? $this->activation->qrCode->token : null
            ]
        ];
    }
}
