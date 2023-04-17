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
}
