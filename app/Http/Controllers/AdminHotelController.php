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
    public function editHotel(Request $request){
        //hotel_id hotelname hoteladdress
        $request->validate([
            'hotel_id'=>'required | integer',
            'hotelname'=>'required | max:255 | min:3',
            'hoteladdress'=>'required | max:255 | min:3'
        ],[
            'hotel_id.required'=>'Otel id boş bırakılamaz!',
            'hotel_id.integer'=>'Otel id sayısal olmalıdır!',
            'hotelname.required'=>'Otel adı boş bırakılamaz!',
            'hotelname.max'=>'Otel adı en fazla 255 karakter olabilir!',
            'hotelname.min'=>'Otel adı en az 3 karakter olabilir!',
            'hoteladdress.required'=>'Otel adresi boş bırakılamaz!',
            'hoteladdress.max'=>'Otel adresi en fazla 255 karakter olabilir!',
            'hoteladdress.min'=>'Otel adresi en az 3 karakter olabilir!',
        ]);

        $hotel=Hotel::find($request->hotel_id);
        if($hotel){
            $hotel->name=$request->hotelname;
            $hotel->address=$request->hoteladdress;
            $hotel->save();
            return response()->json([
                "status"=>true,
                "message"=>"Otel başarıyla güncellendi!"
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
