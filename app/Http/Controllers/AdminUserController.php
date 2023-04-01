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

        $users = User::paginate(10);

        $user_count= User::count();

        return view('Admin/Users/users',['users' => $users, 'user_count' => $user_count]);
    }

}
