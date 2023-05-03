<?php

namespace App\Http\Controllers;

use App\Models\transaction;
use App\Models\transaction_request;
use App\Models\User;
use App\Models\Hotel;
use App\Models\room;
use Illuminate\Http\Request;

class AdminTransactionController extends Controller
{
    //

    //trans
    public function trans()
    {
        $trans_count = transaction::count();

        //order by id desc and paginate
        // $trans = transaction::orderBy('id', 'desc')->get(); where room_id is not null
        $trans = transaction::orderBy('id', 'desc')->get();

        // //find room id
        foreach ($trans as $t) {
            $t->user = User::find($t->user_id);
        }


        return view('Admin/Transactions/transaction', ['transactions' => $trans, 'trans_count' => $trans_count]);
    }

    public function trans_request()
    {

        //get all transaction request
        $trans_count = transaction_request::where('status',"pending")->count();

        $transids = transaction_request::where('status',"pending")->get();

        //get column name transaction_id
        $transids = $transids->pluck('transaction_id');

        
        $transids = $transids->toArray();
        
        //get all transaction with transaction ids
        $trans = transaction::whereIn('id',$transids)->orderBy('id', 'desc')->get();
     
        //order by id desc and paginate
        // $trans = transaction::orderBy('id', 'desc')->get(); where room_id is not null
        //find room id
        foreach ($trans as $t) {
        //     $t->room = $t->room()->first();
            $t->user = User::find($t->user_id);
        }


        return view('Admin/Transactions/transactionRequest', ['transactions' => $trans, 'trans_count' => $trans_count]);
    }

    public function confirmOrRejectTransaction(Request $request){

        //tran_id tran_status
        //validate
        $request->validate([
            'transaction_id' => 'required',
            'transaction_status' => 'required'
        ],
        [
            'transaction_id.required' => 'İşlem ID gereklidir',
            'transaction_status.required' => 'İşlem durumu gereklidir'
        ]);

        //tran request
        $tran_req= transaction_request::where('transaction_id',$request->transaction_id)->first();

        if(!$tran_req){
            return response()->json(['status' => false, 'message' => 'Transaction request not found!']);
        }
        //find transaction
        $transaction = transaction::find($request->transaction_id);

        if(!$transaction){
            return response()->json(['status' => false, 'message' => 'Transaction not found!']);
        }

        $transaction->transaction_id=$tran_req->own_transaction_id;
        $transaction->save();

        if($transaction->hotel_id!=null){
            //check if transaction is hotel transaction
            if($transaction->hotel_id == null){
                return response()->json(['status' => false, 'message' => 'This transaction is not a hotel transaction!']);
            }
            //check hotel already have
            $hotel = Hotel::find($transaction->hotel_id);
            if(!$hotel){
                return response()->json(['status' => false, 'message' => 'Hotel not found!']);
            }

            //check if transaction is confirmed
            // if($transaction->transaction_status != 2){
            //     return response()->json(['status' => false, 'message' => 'This transaction status is not Pending !']);
            // }
            //check if transaction is confirmed

            if($request->transaction_status == 0){
                $transaction->transaction_status = 0;
                $transaction->save();
                $tran_req->status = "confirmed";
                $tran_req->save();
                return response()->json(['status' => true, 'message' => 'Transaction confirmed!']);
            }else if ($request->transaction_status == 1){
                $transaction->transaction_status = 1;
                $transaction->save();
                $tran_req->status = "rejected";
                $tran_req->save();
                return response()->json(['status' => true, 'message' => 'Transaction rejected!']);
            }else{
                return response()->json(['status' => false, 'message' => 'Transaction status is not valid!']);
            }
        }
        else if ($transaction->room_id!=null){
            $transaction->transaction_status = $request->transaction_status;
            $transaction->save();
            if($request->transaction_status==0){
                $tran_req->status = "confirmed";
            }else if ($request->transaction_status==1){
                $tran_req->status = "rejected";
            }
            $tran_req->save();
            
            if($request->transaction_status==0){
                //get room
                $room = room::find($transaction->room_id);
    
                $room->transaction_id = $transaction->transaction_id;
                $room->room_status = 1; //1 means room is occupied
                $room->save();
    
                return response()->json(['status' => true, 'message' => 'Transaction confirmed successfully!',"room_number"=>$room->room_number,"room_type"=>$room->room_type()->first()->room_type]);
            }
            else if ($request->transaction_status==1){
                return response()->json(['status' => false, 'message' =>  'Transaction declined!']);
            }
        }
      
    }
}
