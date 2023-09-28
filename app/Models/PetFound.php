<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PetFound extends Model
{
    use HasFactory;
    protected $fillable = [
        'pet_id',
        'firstname',
        'lastname',
        'email',
        'phone',
        'location'
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class, 'pet_id');
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'email' => $this->email,
            'phone' => $this->phone,
            'location' => $this->location,
            'pet' => $this->pet
        ];
    }
}
