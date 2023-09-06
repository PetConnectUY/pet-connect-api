<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory, UUID;

    protected $keyType = 'string';
    protected $primaryKey = 'uuid';
    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $order->uuid = $order->generateUUID($order, 'uuid');
        });
    }

    protected $fillable = [
        'uuid',
        'user_id',
        'product_id',
        'payment_id',
        'status',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }
}
