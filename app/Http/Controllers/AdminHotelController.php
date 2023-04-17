<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hotel;

class AdminHotelController extends Controller
{
    //
    public function hotels(){

        $hotels=Hotel::paginate(10);
        $hotel_count=Hotel::count();

        return view('Admin/Hotels/Hotel',['hotels'=>$hotels,'hotel_count'=>$hotel_count]);
    }

    public function createHotel(Request $request){
        //hotelname and address validation
        $request->validate([
            'hotelname'=>'required | max:255 | min:3 | unique:hotels,name',
            'hoteladdress'=>'required | max:255 | min:3'
        ]);

        //create hotel
        $hotel=Hotel::create([
            'name'=>$request->hotelname,
            'address'=>$request->hoteladdress
        ]);

        //return response
        return redirect()->back()->with('success', 'Otel başarıyla eklendi!');
    }

    public function deleteHotel($id){
        $hotel=Hotel::find($id);
        if($hotel){
            $hotel->delete();
            return response()->json([
                "status"=>true,
                "message"=>"Otel başarıyla silindi!"
            ],200);
        }
        else{
            return response()->json([
                "status"=>false,
                "message"=>"Otel bulunamadı!"
            ],200);
        }
    }

}
