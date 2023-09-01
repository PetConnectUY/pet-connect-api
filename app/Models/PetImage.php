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

    private function imageUrl()
    {
        return asset('storage'.env('PET_IMAGES_FOLDER').$this->name);
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'pet_id' => $this->pet_id,
            'name' => $this->name,
            'is_cover_image' => $this->cover_image,
            'url' => $this->imageUrl(),
        ];
    }
}
