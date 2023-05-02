<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_Wallets extends Model
{
    use HasFactory;

    //fillable userid walletid
    protected $fillable = [
        'user_id',
        'wallet_id'
    ];
}
