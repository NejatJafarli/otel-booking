<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ApiAuthController extends Controller
{
    //

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
                'status' => 'error',
                "user" => $user,
                'message' => 'Kullanıcı bulundu!'
            ]);
        }
        else{
            //request validate username and character_number
            $request->validate(
                [
                    'username' => 'required|string|max:255|unique:users',
                    'character_number' => 'required|integer',
                ],
                [
                    'username.required' => 'Kullanıcı adı alanı boş bırakılamaz!',
                    'character_number.required' => 'Karakter ID alanı boş bırakılamaz!',
                    'username.unique' => 'Bu kullanıcı adı daha önce alınmış!',
                    "character_number.integer" => "Karakter ID sadece sayılardan oluşabilir!"
                ]
            );

            //create user
            $user = User::create([
                'username' => $request->username,
                'wallet_id' => $request->wallet_id,
                'character_number' => $request->character_id,
            ]);

            //return user
            return response()->json([
                'status' => 'success',
                "user" => $user,
                'message' => 'Kullanıcı oluşturuldu!'
            ]);
        }
    }
}
