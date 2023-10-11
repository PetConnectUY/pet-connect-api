<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PetRace extends Model
{
    use HasFactory;
    
    protected $table = 'pets_races';
    protected $fillable = ['name'];

    const CREATED_AT = null;
    const UPDATED_AT = null;
}
