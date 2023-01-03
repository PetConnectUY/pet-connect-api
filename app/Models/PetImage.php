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
        return $this->hasOne(Pet::class, 'id', 'pet_id');
    }
}
