<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class AdminMainController extends Controller
{
    //

    //dashboard
    public function dashboard()
    {
        return view('Admin/Main/dashboard');
    }

    public function onlineUsers(){
        return view('Admin/Main/OnlineUsers');

    }
}
