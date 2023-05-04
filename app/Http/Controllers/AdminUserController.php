<?php

namespace App\Http\Controllers;

use App\Models\transaction;
use App\Models\User;
use App\Models\User_Wallets;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    //

    //users
    public function users()
    {
        $users = User::orderBy('id', 'desc')->get();

        $user_count= User::count();

        return view('Admin/Users/users',['users' => $users, 'user_count' => $user_count]);
    }

    public function createUser(Request $req){
        //username email wallet char_number validation
        $req->validate(
            [
                'username' => 'required| max:255|string',
                "char_number"=>"required|integer"
            ],
            [
                'username.required' => 'Kullanıcı adı boş bırakılamaz!',
                'username.max' => 'Kullanıcı adı 255 karakterden fazla olamaz!',
                'username.string' => 'Kullanıcı adı string olmalı!',
                'char_number.required' => 'Karakter sayısı boş bırakılamaz!',
                'char_number.integer' => 'Karakter sayısı integer olmalı!'
            ]
        );
        if($req->email !=null){
            $req->validate(
                [
                    'email' => 'string | email'
                ],
                [
                    'email.string' => 'Email string olmalı!',
                    'email.email' => 'Email formatı yanlış!'
                ]
            );
        }

        User::create([
            "username" => $req->username,
            "email" => $req->email,
            "character_number"=>$req->char_number
        ]);

        return redirect()->back()->with('success', 'Kullanici başarıyla oluşturuldu!');
    }

    public function deleteUser($id){
        $user=User::find($id);

        if($user){
            $user->delete();
            return response()->json([
                "status" => true,
                "message" => "Kullanici başarıyla silindi!"
            ], 200);
        }
        else{
            return response()->json([
                "status" => false,
                "message" =>  "Kullanici bulunamadı!"
            ], 200);
        }
    }

    public function editUser(Request $req){
        //username email wallet char_number validation
        $req->validate(
            [
                'username' => 'required|max:255|string',
                "char_number"=>"required|integer"
            ],
            [
                'username.required' => 'Kullanıcı adı boş bırakılamaz!',
                'username.max' => 'Kullanıcı adı 255 karakterden fazla olamaz!',
                'username.string' => 'Kullanıcı adı string olmalı!',
                'email.string' => 'Email string olmalı!',
                'char_number.required' => 'Karakter sayısı boş bırakılamaz!',
                'char_number.integer' => 'Karakter sayısı integer olmalı!'
            ]
        );
        if($req->email !=null){
            $req->validate(
                [
                    'email' => 'string | email'
                ],
                [
                    'email.string' => 'Email string olmalı!',
                    'email.email' => 'Email formatı yanlış!'
                ]
            );
        }
        //find $req->id user
        $user = User::find($req->id);

        //update user
        $user->update([
            "username" => $req->username,
            "email" => $req->email,
            "character_number"=>$req->char_number
        ]);

         //return json response
         return response()->json([
            "status" => true,
            "message" => "Kullanici başarıyla güncellendi!"
        ], 200);
    }

    public function userDetail($id){
        $user = User::find($id);



        //get user transactions
        $userRoomTrans=transaction::where("user_id", $user->id)->where("hotel_id",null)->get();
        $userHotelTrans=transaction::where("user_id", $user->id)->where("room_id",null)->get();

        $userWallets=User_Wallets::where("user_id", $user->id)->get();

        $wallets=array();
        foreach($userWallets as $wallet){
            $wallets[]=$wallet->wallet_id;
        }

        $userWallets=implode(",",$wallets);

        $user->wallet_id=$userWallets;

        return view('Admin/Users/user_detail',['user' => $user, 'userRoomTrans' => $userRoomTrans, 'userHotelTrans' => $userHotelTrans]);
    }
}
