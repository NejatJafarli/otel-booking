<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class room_types extends Model
{
    use HasFactory;


    //fillable
    protected $fillable = [
        'room_type',
        "room_price",
        "hotel_id",
        "discount_percent",
        "sceneName"
    ];
    
    //relationship with hotel
    public function hotel(){
        return $this->belongsTo(Hotel::class);
    }
}
