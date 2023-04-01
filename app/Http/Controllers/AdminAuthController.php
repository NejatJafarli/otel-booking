<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;

class AdminAuthController extends Controller
{
    //

    //admin login
    public function login()
    {
        return view('Admin/Auth/login');
    }

    public function postLogin(Request $req)
    {
        //validate request data
        $req->validate([
            'email' => 'required',
            'password' => 'required'
        ],[
            'email.required' => 'Email alanı boş bırakılamaz!',
            'password.required' => 'Şifre alanı boş bırakılamaz!'
        ]);

        // //make lowercase email
        $req->merge([
            'email' => strtolower($req->email)
        ]);
        
        //auth attempt
        $credentials = $req->only('email', 'password');
        if (auth()->guard('admin')->attempt($credentials)) {
            
            return redirect()->route('adminDashboard');
        }
        return redirect()->back()->withErrors('error', 'Giriş bilgileri yanlış!');
        //if redirect back with error
    }

    // logout
    public function logout()
    {
        auth()->guard('admin')->logout();
        return redirect()->route('adminLogin');
    }
}
