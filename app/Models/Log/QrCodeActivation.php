<?php

namespace App\Models\Log;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QrCodeActivation extends Model
{
    use HasFactory;

    protected $table = 'log_qr_code_activations';

    protected $fillable = [
        'qr_code_id',
        'user_id',
        'actived_at'
    ];
}
