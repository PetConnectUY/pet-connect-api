<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\URL;
use SimpleSoftwareIO\QrCode\Facades\QrCode; // Corregir el namespace

class UserPetToken extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'users_pets_tokens';

    protected $fillable = [
        'token',
        'pet_id',
        'qr_code',
    ];

    protected $appends = ['qr_code'];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($userPetToken) {
            $userPetToken->generateQrCode();
        });
    }

    public function pet()
    {
        return $this->belongsTo(Pet::class, 'pet_id');
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'token' => $this->token,
            'qr_code' => $this->qr_code
        ];
    }

    public function generateQRCode()
    {
        $qrCodeUrl = URL::to("/pets/{$this->token}");
        $qrCode = QrCode::size(250)->generate($qrCodeUrl);
        $this->qr_code = $qrCode;
    }
}
