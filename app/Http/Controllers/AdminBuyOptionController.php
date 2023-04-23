<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BuyOptions;
class AdminBuyOptionController extends Controller
{
    //

    public function buyOptions(){
        $buyOptionUser = BuyOptions::orderBy('id', 'desc')->get();

        $buyOption_count= BuyOptions::count();

        return view('Admin/BuyOptions/buyOption',['buyOptions' => $buyOptionUser, 'buyOption_count' => $buyOption_count]);
    }

    public function createBuyOption(Request $request){
        // option_name option_day discount 
        $request->validate([
            'option_name'=>'required | max:255 | min:3',
            'option_day'=>'required | integer | min:1',
        ]);

        $buyOption=BuyOptions::create([
            'option_name'=>$request->option_name,
            'option_days'=>$request->option_day,
            'discount_percent'=>$request->discount
        ]);

        return redirect()->back()->with('success', 'Satin Alma Secenegi basariyla eklendi!');
    }

    public function deleteBuyOption($id){
        $BuyOption=BuyOptions::find($id);
        if($BuyOption){
            $BuyOption->delete();
            return response()->json([
                "status"=>true,
                "message"=>"Satin Alma Secenegi silindi!"
            ],200);
        }
        else{
            return response()->json([
                "status"=>false,
                "message"=>"Satin Alma Secenegi bulunamadı!"
            ],200);
        }
    }
    public function editBuyOption(Request $request){
        //option name option_day discount
        $request->validate([
            'option_name'=>'required | max:255 | min:3',
            'option_day'=>'required | integer | min:1',
        ]);

        $buyOption=BuyOptions::find($request->id);

        if($buyOption){
            $buyOption->update([
                'option_name'=>$request->option_name,
                'option_days'=>$request->option_day,
                'discount_percent'=>$request->discount
            ]);
            return response()->json([
                "status"=>true,
                "message"=> "Satin Alma Secenegi basariyla guncellendi!"
            ],200);
        }
        else{
            return response()->json([
                "status"=>false,
                "message"=>"Satin Alma Secenegi bulunamadı!"
            ],200);
        }
    }
}
