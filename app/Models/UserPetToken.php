<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserPetToken extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'users_pets_tokens';

    protected $fillable = [
        'token',
        'pet_id'
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class, 'pet_id');
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'token' => $this->token,
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
                    'address' => $this->pet->user->address
                ],
            ],
        ];
    }
}
