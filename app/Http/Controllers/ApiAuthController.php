<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ApiAuthController extends Controller
{
    //
    public function register(Request $request){
        //request validate username and character_number
        $request->validate(
            [
                'username' => 'required|string|max:255|unique:users',
                'character_number' => 'required|integer',
                'wallet_id' => 'required|string|max:255',
            ],
            [
                'username.required' => 'Kullanıcı adı alanı boş bırakılamaz!',
                'character_number.required' => 'Karakter ID alanı boş bırakılamaz!',
                'username.unique' => 'Bu kullanıcı adı daha önce alınmış!',
                "character_number.integer" => "Karakter ID sadece sayılardan oluşabilir!",
                "wallet_id.required" => "Cüzdan ID alanı boş bırakılamaz!",


            ]
        );
        $user= User::where('wallet_id', $request->wallet_id)->first();
        if($user){
            return response()->json([
                'status' => false,
                'message' => 'Bu cüzdan ID ile daha önce kayıt olunmuş!'
            ]);
        }

        //create user
        $user = User::create([
            'username' => $request->username,
            'wallet_id' => $request->wallet_id,
            'character_number' => $request->character_id,
        ]);

        //return user
        return response()->json([
            'status' => true,
            'message' => 'Kullanıcı oluşturuldu!'
        ]);
    }

    //login
    public function login(Request $request)
    {
        //validate username and password wallet id and email
        $request->validate(
            [
                'wallet_id' => 'required|string|max:255',
            ],
            [
                'wallet_id.required' => 'Cüzdan ID alanı boş bırakılamaz!',
            ]
        );

        //check if user exists
        $user = User::where('wallet_id', $request->wallet_id)->first();

        if($user){
            return response()->json([
                'status' => true,
                'username' => $user->username,
                'character_number' => $user->character_number,
                'message' => 'Kullanıcı bulundu!'
            ]);
        }
        else{
            return response()->json([
                'status' => false,
                'message' => 'Kullanıcı bulunamadı!'
            ]);
        }
            
    }
}
