<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientBranch extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'contact_firstname',
        'address',
        'phone',
        'email'
    ];

    public function clients()
    {
        return $this->hasOne(Client::class, 'client_id');
    }
}
