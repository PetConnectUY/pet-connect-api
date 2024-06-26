<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QrCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'token',
        'image_url',
    ];

    public function activation()
    {
        return $this->hasOne(QrCodeActivation::class, 'qr_code_id');
    }
}
