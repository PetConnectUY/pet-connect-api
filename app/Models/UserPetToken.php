<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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
        $qrCodeUrl = env('FRONTEND_URL').'pets/'.$this->token;
        $image = QrCode::format('png')
            ->size(256)
            ->generate($qrCodeUrl);

        Storage::put(env('QR_IMAGES_FOLDER').$this->token.'.png', $image);
        $fileName = $this->token .'.png';
        $qrImageUrl = asset('storage'.env('QR_IMAGES_FOLDER').$fileName);
        $this->qr_code = $qrImageUrl;
    }
}
