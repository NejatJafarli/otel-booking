<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;

// Use Hash
use Illuminate\Support\Facades\Hash;

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

    public function admins(){
        $admins = Admin::all();

        
        $admin_count=Admin::count();
        return view('Admin/Auth/admin',['admins'=>$admins,'admin_count'=>$admin_count]);
    }

    public function adminRegister(Request $req){
        //username email unique password role
        $req->validate([
            'adminusername' => 'required | unique:admins,username',
            'adminemail' => 'required|unique:admins,email',
            'adminpassword' => 'required',
            'adminrole' => 'required'
        ],[
            'adminusername.required' => 'Kullanıcı adı alanı boş bırakılamaz!',
            'adminusername.unique' => 'Bu kullanıcı adı zaten kullanılıyor!',
            'adminemail.required' => 'Email alanı boş bırakılamaz!',
            'adminemail.unique' => 'Bu email zaten kullanılıyor!',
            'adminpassword.required' => 'Şifre alanı boş bırakılamaz!',
            'adminrole.required' => 'Rol alanı boş bırakılamaz!'
        ]);

        //create admin
        Admin::create([
            'username' => $req->adminusername,
            'email' => $req->adminemail,
            'password' => Hash::make($req->adminpassword),
            'role' => $req->adminrole
        ]);

        return redirect()->back()->with('success', 'Yeni admin başarıyla oluşturuldu!');
    }

    public function deleteAdmin($id){

        //find admin
        $admin = Admin::find($id);

        if($admin){
            $admin->delete();
            //json response
            return response()->json([
                'status' => true,
                'message' => 'Admin başarıyla silindi!'
            ]);
        }
        else{
            return response()->json([
                'status' => false,
                'message' => 'Admin bulunamadı!'
            ]);
        }
    }
}
