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
    public function chatLog(){

        //public/chatHistory 
        //load file year-month-day.txt
        // get current date

        //set timezone europe/istanbul
        date_default_timezone_set('Europe/Istanbul');
        $date = date('Y-m-d');

        $file = public_path('chatHistory/'.$date.'.txt');
        
        //if file does not exist
        if(!file_exists($file)){
            return view('Admin/Main/chatLog',['content'=>[]]);
        }

        $content = file_get_contents($file);
        $content = explode("\n", $content);


        return view('Admin/Main/chatLog',['content'=>$content]);
    }

    public function onlineChat(){

        //get all users
        $users = \App\Models\User::all();
        return view('Admin/Main/onlineChat',['users'=>$users]);

    }

}
