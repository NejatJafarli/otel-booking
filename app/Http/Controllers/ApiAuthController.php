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
    public function register(Request $request)
    {
        //validate username and password wallet id and email
        $request->validate(
            [
                'username' => 'required|string|max:255|unique:users',
                'password' => 'required|string|min:8',
                'wallet_id' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
            ],
            [
                'username.required' => 'Kullanıcı adı alanı boş bırakılamaz!',
                'password.required' => 'Şifre alanı boş bırakılamaz!',
                'wallet_id.required' => 'Cüzdan ID alanı boş bırakılamaz!',
                'email.required' => 'Email alanı boş bırakılamaz!',
                'email.unique' => 'Bu email adresi zaten kayıtlı!',
                'password.min' => 'Şifre en az 8 karakter olmalıdır!',
                'username.unique' => 'Bu kullanıcı adı zaten kayıtlı!',
            ]
        );

        //create user
        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'wallet_id' => $request->wallet_id,
            'email' => $request->email,
        ]);

        return response()->json([
            "status" => true,
            'message' => 'Kayıt başarılı!',
            'user' => $user
        ], 201);
    }
}
