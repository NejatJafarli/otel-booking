<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User_Wallets;

class ApiAuthController extends Controller
{
    //
    public function register(Request $request){
        //request validate username and character_number
        $request->validate(
            [
                'username' => 'required|string|max:255',
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
        //check if username exists
        $user = User::where('username', $request->username)->first();
        if($user){
            return response()->json([
                'status' => false,
                'message' => 'Bu kullanıcı adı daha önce alınmış!'
            ]);
        }
       //user wallets
        $user_wallet = User_Wallets::where('wallet_id', $request->wallet_id)->first(); 
        if($user_wallet){
            return response()->json([
                'status' => false,
                'message' => 'Bu cüzdan ID ile daha önce kayıt olunmuş!'
            ]);
        }

        //create user
        $user = User::create([
            'username' => $request->username,
            'character_number' => $request->character_number,
        ]);

        //get user id
        $user_id = $user->id;
        //create user wallet
        $user_wallet = User_Wallets::create([
            'user_id' => $user_id,
            'wallet_id' => $request->wallet_id,
        ]);

        //return user
        return response()->json([
            'status' => true,
            'user_id' => $user->id,
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

        // //check if user exists
        // $user = User::where('wallet_id', $request->wallet_id)->first();

        //check wallet id inside user_wallets table
        $user_wallet = User_Wallets::where('wallet_id', $request->wallet_id)->first();
        //if user exists
        if($user_wallet){

            $user= User::where('id', $user_wallet->user_id)->first();

            if($user){

                $user_wallets = User_Wallets::where('user_id', $user->id)->get();

                //pluck wallet id
                $wallet_ids = $user_wallets->pluck('wallet_id')->toArray();
                return response()->json([
                    'status' => true,
                    'username' => $user->username,
                    'user_id'=> $user->id,
                    'character_number' => $user->character_number,
                    'wallet_ids' => $wallet_ids,
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
        else{
            return response()->json([
                'status' => false,
                'message' => 'Bu Wallet Id Sistemde Kayitli Degil'
            ]);
        }
    }
}
