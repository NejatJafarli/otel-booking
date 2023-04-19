<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    use HasFactory;

    //fillable
    protected $fillable = [
        'name',
        'price',
        "day_for_price"
    ];


    
}
