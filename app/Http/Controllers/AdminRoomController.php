<?php

namespace App\Http\Controllers;

use App\Models\room;
use App\Models\room_types;
use App\Models\Hotel;
use Illuminate\Http\Request;

class AdminRoomController extends Controller
{
    //
    // rooms
    public function rooms()
    {
        //get all rooms with pagination
        $rooms = room::orderBy('id', 'desc')->paginate(10);

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

    public function roomTypes(){
        $types = room_types::orderBy('id', 'desc')->paginate(10);
        $room_type_count = room_types::count();


        //get all hotels
        $hotels = Hotel::all();


        return view('Admin/RoomTypes/room_type', ['types' => $types,"room_type_count"=>$room_type_count,"hotels"=>$hotels]);
    }
    public function deleteRoomType($id){
        $room=room_types::find($id);

        if($room){
            $room->delete();
            return response()->json([
                "status" => true,
                "message" => "Oda başarıyla silindi!"
            ], 200);
        }
        else{
            return response()->json([
                "status" => false,
                "message" =>  "Oda bulunamadı!"
            ], 200);
        }
    }
    public function createRoomType(Request $req){
        //room type name and price validation
        $req->validate(
            [
                "room_type" => "required",
                "room_price" => "required | numeric | min:1",
                "hotel_id" => "required | numeric | min:1",
            ],
            [
                "room_type.required" => "Oda Turu boş bırakılamaz!",
                "room_price.required" => "Oda fiyatı boş bırakılamaz!",
                "room_price.numeric" => "Oda fiyatı sayısal olmalıdır!",
                "room_price.min" => "Oda fiyatı 1'den küçük olamaz!",
            ]
        );
        //create room type
        room_types::create([
            "room_type" => $req->room_type,
            "room_price" => $req->room_price,
            "hotel_id" => $req->hotel_id,
        ]);
        return redirect()->back()->with('success', 'Oda başarıyla eklendi!');
        
    }
    public function editRoomType(Request $req)
    {
        //validate request data
        $req->validate(
            [
                "room_type_name" =>  "required",
                //price
                "room_price" => "required | numeric | min:1",
                "hotel_id" => "required | numeric | min:1",
            ],
            [
                "room_type_name.required" => "Oda Turu boş bırakılamaz!",
                "room_price.required" => "Oda fiyatı boş bırakılamaz!",
                "room_price.numeric" => "Oda fiyatı sayısal olmalıdır!",
                "room_price.min" => "Oda fiyatı 1'den küçük olamaz!",
            ]
        );
        //find room
        $room = room_types::find($req->room_type_id);
        //update room
        $room->update([
            "room_type" => $req->room_type_name,
            "room_price" => $req->room_price,
            "hotel_id" => $req->hotel_id,
        ]);

        //return json response
        return response()->json([
            "status" => true,
            "message" => "Oda başarıyla güncellendi!"
        ], 200);
    }
    // createRoom
    public function createRoom(Request $req)
    {
        //validate request data
        $req->validate(
            [
                "room_number" => "required | numeric | min:1 ",
                "room_type" => "required | numeric | min:1",
            ],
            [
                "room_number.required" => "Oda numarası boş bırakılamaz!",
                "room_number.numeric" => "Oda numarası sayısal olmalıdır!",
                "room_number.min" => "Oda numarası 1'den küçük olamaz!",
                "room_type.required" => "Oda Turu boş bırakılamaz!",
                "room_type.numeric" => "Oda Turu sayısal olmalıdır!",
                "room_type.min" => "Oda Turu seçiniz!",
            ]
        );
        //create room

        room::create([
            "room_number" => $req->room_number,
            "room_type_id" => $req->room_type,
            "room_status" => 0
        ]);

        return redirect()->back()->with('success', 'Oda başarıyla oluşturuldu!');
    }

    // deleteRoom
    public function deleteRoom($id)
    {
        $room = room::find($id);
        if ($room) {
            $room->delete();
            return response()->json([
                "status" => true,
                "message" => "Oda başarıyla silindi!"
            ], 200);
        } else {
            return response()->json([
                "status" => false,
                "message" => "Oda bulunamadı!"
            ], 200);
        }
        
    }

    //editRoom
    public function editRoom(Request $req)
    {
        //validate request data
        $req->validate(
            [
                "room_number" => "required | numeric | min:1",
                "room_type" => "required | numeric | min:1",
            ],
            [
                "room_number.required" => "Oda numarası boş bırakılamaz!",
                "room_number.numeric" => "Oda numarası sayısal olmalıdır!",
                "room_number.min" => "Oda numarası 1'den küçük olamaz!",
                "room_type.required" => "Oda Turu boş bırakılamaz!",
                "room_type.numeric" => "Oda Turu sayısal olmalıdır!",
                "room_type.min" => "Oda Turu seçiniz!",
            ]
        );
        //find room
        $room = room::find($req->room_id);
        //update room
        $room->update([
            "room_number" => $req->room_number,
            "room_type_id" => $req->room_type,
        ]);

        //return json response
        return response()->json([
            "status" => true,
            "message" => "Oda başarıyla güncellendi!"
        ], 200);
    }
}
