<?php

namespace App\Http\Controllers;

use App\Models\room;
use App\Models\room_types;
use Illuminate\Http\Request;

class AdminRoomController extends Controller
{
    //
    // rooms
    public function rooms()
    {
        //get all rooms with pagination
        $rooms = room::paginate(10);

        //get count of all rooms
        $rooms_count = room::count();
        //get count of all rooms with status 1
        $rooms_count_1 = room::where('room_status', 1)->count();

        $types = room_types::all();

        //merge room types
        foreach ($rooms as $room) {
            $room->room_type = $room->room_type()->first();
        }
        return view('Admin/Rooms/rooms', ['rooms' => $rooms, 'types' => $types, 'rooms_count' => $rooms_count, 'rooms_count_1' => $rooms_count_1]);
    }

    // createRoom
    public function createRoom(Request $req)
    {
        //validate request data
        $req->validate(
            [
                "room_number" => "required | numeric | min:1 | unique:rooms",
                "room_type" => "required | numeric | min:1",
                "room_price" => "required | numeric | min:1",
            ],
            [
                "room_number.required" => "Oda numarası boş bırakılamaz!",
                "room_number.numeric" => "Oda numarası sayısal olmalıdır!",
                "room_number.min" => "Oda numarası 1'den küçük olamaz!",
                "room_number.unique" => "Oda numarası zaten mevcut!",
                "room_type.required" => "Oda Turu boş bırakılamaz!",
                "room_type.numeric" => "Oda Turu sayısal olmalıdır!",
                "room_type.min" => "Oda Turu seçiniz!",
                "room_price.required" => "Oda fiyatı boş bırakılamaz!",
                "room_price.numeric" => "Oda fiyatı sayısal olmalıdır!",
                "room_price.min" => "Oda fiyatı 1'den küçük olamaz!",
            ]
        );
        //create room

        room::create([
            "room_number" => $req->room_number,
            "room_type_id" => $req->room_type,
            "room_price" => $req->room_price,
            "room_status" => 0
        ]);

        return redirect()->back()->with('success', 'Oda başarıyla oluşturuldu!');
    }
}
