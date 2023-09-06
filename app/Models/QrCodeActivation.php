<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QrCodeActivation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'qr_code_id',
        'user_id',
        'pet_id',
        'activation_url'
    ];
}
