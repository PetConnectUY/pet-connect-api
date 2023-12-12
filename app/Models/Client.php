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
        'central_address',
        'image',
        'url'
    ];

    public function branches() {
        return $this->hasMany(ClientBranch::class, 'client_id');
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'central_address' => $this->central_address,
            'url' => $this->url,
            'branches' => $this->branches,
        ];
    }
}
