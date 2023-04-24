<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class myConfig extends Model
{
    use HasFactory;

    //fillable id key value
    protected $fillable = [
        'key',
        'value',
    ];
}
