<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserEmailChange extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'current_email',
        'new_email',
        'token',
        'changed',
        'created_at',
    ];

    public function toArray()
    {
        return [
            'id' => $this->id,
            'new_email' => $this->new_email,
            'changed' => $this->changed,
        ];
    }
}
