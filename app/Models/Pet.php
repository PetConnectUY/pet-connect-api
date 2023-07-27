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

    public function toArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'birth_date' => $this->birth_date,
            'race' => $this->race,
            'gender' => $this->gender,
            'pet_information' => $this->pet_information,
            'user' => [
                'id' => $this->user->id,
                'firstname' => $this->user->firstname,
                'lastname' => $this->user->lastname,
                'username' => $this->user->username,
                'email' => $this->user->email,
                'birth_date' => $this->user->birth_date,
                'phone' => $this->user->phone,
                'address' => $this->user->address
            ]
        ];
    }
}
