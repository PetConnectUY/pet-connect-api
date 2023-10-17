<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'access_token',
        'address',
        'phone',
        'contact_name', //El nombre de la persona de contacto en la veterinaria.
        'email',
    ];

    public function qrCodes()
    {
        return $this->hasMany(ClientQrCode::class, 'client_id');
    }

}
