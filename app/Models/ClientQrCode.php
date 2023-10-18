<?php

namespace App\Models;

use BaconQrCode\Encoder\QrCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientQrCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'qr_code_id'           
    ];

    public function qrCodes()
    {
        return $this->hasMany(QrCode::class, 'id', 'qr_code_id');
    }

    public function clients()
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }

    public function qrCodesClients()
    {
        return $this->hasManyThrough(
            Client::class,
            'qr_codes',
            'id',
            'id',
            'qr_code_id',
            'client_id'
        );
    }
}
