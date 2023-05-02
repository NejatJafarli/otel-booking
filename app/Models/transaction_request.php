<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class transaction_request extends Model
{
    use HasFactory;

    //transaction id
    //fillable
    protected $fillable = [
        'transaction_id',
        'status',
        "own_transaction_id"
    ];
}
