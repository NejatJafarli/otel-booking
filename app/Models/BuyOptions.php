<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuyOptions extends Model
{
    use HasFactory;

    //fillable
    protected $fillable = [
        'option_name',
        'option_days',
    ];
}
