<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\myConfig;
class AdminConfigController extends Controller
{
    //  //
    public function configs(){

        $cons=myConfig::all();
        $config_count=myConfig::count();

        return view('Admin/MyConfig/myConfig',['configs'=>$cons,'config_count'=>$config_count]);
    }

    //create config
    public function createMyConfig(Request $request){
        //key and value validation
        $request->validate([
            'key'=>'required | max:255 | unique:my_configs,key',
            'value'=>'required | max:255'
        ]);

        //create config
        $config=myConfig::create([
            'key'=>$request->key,
            'value'=>$request->value
        ]);

        //return response
        return redirect()->back()->with('success', 'Config başarıyla eklendi!');
    }

    //delete config
    public function deleteMyConfig($id){
        $config=myConfig::find($id);
        if($config){
            $config->delete();
            return response()->json([
                "status"=>true,
                "message"=>"Config başarıyla silindi!"
            ],200);
        }
        else{
            return response()->json([
                "status"=>false,
                "message"=>"Config bulunamadı!"
            ],200);
        }
    }

    //edit config
    public function editMyConfig(Request $request){
        //key and value validation
        $config=myConfig::find($request->id);


        if($config){
            
        //if key is changed
        if($config->key!=$request->key){
            $request->validate([
                'key'=>'required | max:255 | unique:my_configs,key',
                'value'=>'required | max:255 '
            ]);
        }
        else{
            $request->validate([
                'key'=>'required | max:255',
                'value'=>'required | max:255'
            ]);
        }
            $config->key=$request->key;
            $config->value=$request->value;
            $config->save();
            return response()->json([
                "status"=>true,
                "message"=>"Config başarıyla güncellendi!"
            ],200);
        }
        else{
            return response()->json([
                "status"=>false,
                "message"=>"Config bulunamadı!"
            ],200);
        }
    }
}
