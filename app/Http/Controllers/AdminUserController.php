<?php

namespace App\Http\Controllers;

use App\Models\User;
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

    public function createUser(Request $request){
        //username email wallet char_number validation





    }

    

}
