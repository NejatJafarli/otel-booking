<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class room extends Model
{
    use HasFactory;

    //fillable
    protected $fillable = [
        'room_number',
        'room_type_id',
        'room_status',
        "created_at",
        "updated_at"
    ];

    //relationships
    public function room_type()
    {
        return $this->belongsTo(room_types::class);
    }
}
