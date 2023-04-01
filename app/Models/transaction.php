<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class transaction extends Model
{
    use HasFactory;


    // /fillable
    protected $fillable = [
        "room_id",
        "wallet_id",
        "check_out_date",
        "check_in_date",
        "transaction_type",
        "transaction_amount",
        "transaction_status",
        "status",
    ];


    //relationships
    public function room()
    {
        return $this->belongsTo(room::class);
    }
    
}
