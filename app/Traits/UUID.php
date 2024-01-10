<?php
namespace App\Traits;

use Illuminate\Support\Str;

trait UUID
{
    public function generateUUID($model, $column)
    {
        $uuid = Str::uuid()->toString();
        while(!is_null($model::where($column, '=', $uuid)->first())){
            $uuid = Str::uuid()->toString();
        }
        return $uuid;
    }

    public function generateToken($model, $column) {
        $token = random_int(100000, 999999);
        while(!is_null($model::where($column, '=', $token)->first())) {
            $token = random_int(100000, 999999);
        }
        return $token;
    }
}