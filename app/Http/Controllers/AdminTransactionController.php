<?php

namespace App\Http\Controllers;

use App\Models\transaction;
use App\Models\User;
use Illuminate\Http\Request;

class AdminTransactionController extends Controller
{
    //

    //trans
    public function trans()
    {


        $trans_count = transaction::count();

        $trans = transaction::paginate(10);

        //find room id
        foreach ($trans as $t) {
            $t->room = $t->room()->first();
            $t->user = User::where("wallet_id", $t->wallet_id)->first();
        }


        return view('Admin/Transactions/transaction', ['transactions' => $trans, 'trans_count' => $trans_count]);
    }
}
