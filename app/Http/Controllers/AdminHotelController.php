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
        ]);

        if($request->hotelprice!=null){
         //validate hotelprice and hoteldayforprice 
            $request->validate([
                'hotelprice'=>'required | numeric | min:1',
                'hoteldayforprice'=>'required | integer | min:1'
            ],[
                'hotelprice.required'=>'Otel fiyatı boş bırakılamaz!',
                'hotelprice.numeric'=>'Otel fiyatı sayısal olmalıdır!',
                'hotelprice.min'=>'Otel fiyatı en az 1 olmalıdır!',
                'hoteldayforprice.required'=>'Otel fiyatı günü boş bırakılamaz!',
                'hoteldayforprice.integer'=>'Otel fiyatı günü sayısal olmalıdır!',
                'hoteldayforprice.min'=>'Otel fiyatı günü en az 1 olmalıdır!'
            ]);
        }

        //create hotel
        $hotel=Hotel::create([
            'name'=>$request->hotelname,
            'day_for_price'=>$request->hoteldayforprice,
            'price'=>$request->hotelprice
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
        ],[
            'hotel_id.required'=>'Otel id boş bırakılamaz!',
            'hotel_id.integer'=>'Otel id sayısal olmalıdır!',
            'hotelname.required'=>'Otel adı boş bırakılamaz!',
            'hotelname.max'=>'Otel adı en fazla 255 karakter olabilir!',
            'hotelname.min'=>'Otel adı en az 3 karakter olabilir!',
            'hoteladdress.required'=>'Otel adresi boş bırakılamaz!',
        ]);

        if($request->hotelprice!=null){
            //validate hotelprice and hoteldayforprice 
                $request->validate([
                    'hotelprice'=>'required | numeric | min:1',
                    'hoteldayforprice'=>'required | integer | min:1'
                ],[
                    'hotelprice.required'=>'Otel fiyatı boş bırakılamaz!',
                    'hotelprice.numeric'=>'Otel fiyatı sayısal olmalıdır!',
                    'hotelprice.min'=>'Otel fiyatı en az 1 olmalıdır!',
                    'hoteldayforprice.required'=>'Otel fiyatı günü boş bırakılamaz!',
                    'hoteldayforprice.integer'=>'Otel fiyatı günü sayısal olmalıdır!',
                    'hoteldayforprice.min'=>'Otel fiyatı günü en az 1 olmalıdır!'
                ]);
        }
        $hotel=Hotel::find($request->hotel_id);
        if($hotel){
            $hotel->name=$request->hotelname;
            if($request->hotelprice!=null){
                $hotel->day_for_price=$request->hoteldayforprice;
                $hotel->price=$request->hotelprice;
            }
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
