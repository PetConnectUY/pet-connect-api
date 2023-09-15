<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PetSettings extends Model
{
    use HasFactory;

    protected $table = 'pets_settings';
    protected $fillable = [
        'pet_id',
        'user_fullname_visible',
        'user_location_visible',
        'user_phone_visible',
        'user_email_visible',
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class, 'pet_id');
    }

    public function qrActivation()
    {
        return $this->belongsTo(QrCodeActivation::class, 'pet_id', 'pet_id');
    }

    public function toArray()
    {
        return [
            'pet' => $this->pet
        ];
    }
}
