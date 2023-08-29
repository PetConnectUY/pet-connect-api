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

    public function toArray()
    {
        return [
            'id' => $this->id,
            'token' => $this->token,
        ];
    }
}
