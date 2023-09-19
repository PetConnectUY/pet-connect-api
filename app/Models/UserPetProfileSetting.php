<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPetProfileSetting extends Model
{
    use HasFactory;

    protected $table = 'users_pet_profile_settings';
    protected $fillable = [
        'user_id',
        'user_fullname_visible',
        'user_location_visible',
        'user_phone_visible',
        'user_email_visible',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'user_fullname_visible' => $this->user_fullname_visible,
            'user_location_visible' =>  $this->user_location_visible,
            'user_phone_visible' =>  $this->user_phone_visible,
            'user_email_visible' =>  $this->user_email_visible,
            'user' => $this->user,
        ];
    }
}
