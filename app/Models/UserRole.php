<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    use HasFactory;

    const USER_ROLE_ID = 1;

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $table = 'users_roles';

    protected $fillable = [
        'user_id',
        'role_id'
    ];
}
