<?php

namespace App\Http\Controllers;

use App\Models\BuyOptions;
use App\Models\room;
use App\Models\room_types;
use App\Models\transaction;
use App\Models\User;
use Illuminate\Http\Request;

class ApiMainController extends Controller
{

    private function guidGenerator()
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    public function getBuyOptions(){
        $buyOptions = BuyOptions::all();
        
        $array = [];
        foreach($buyOptions as $buyOption){
            $array[] = $buyOption->option_name;
        }
        
        
        return response()->json(['status' => true, 'option_names' => $array]);
    }
    //
    public function getUser($id)
    {
        $user = User::find($id);
        return response()->json(['status' => true, 'user' => $user]);
    }

    public function getRoomTypes()
    {
        $room_types = room_types::all();

        //have a room
        foreach ($room_types as $room_type) {
            $room_type->have_room = room::where('room_type_id', $room_type->id)->where('status', 0)->count();
            //unset
        }
        $room_type->price = $room_type->room_price;
        $room_type->type = $room_type->room_type;

        unset($room_type->room_price);
        unset($room_type->room_type);
        return response()->json(['status' => "true", 'room_types' => $room_types]);
    }

    public function getUserRooms($id)
    {
        $user = User::find($id);
        //get user transactions but this date between check_in_date and check_out_date
        $current_date = date('Y-m-d');

        $user_rooms = transaction::where('user_id', $user->id)->where("transaction_status", 0)->where('check_in_date', '<=', $current_date)->where('check_out_date', '>=', $current_date)->get();

        return response()->json(['status' => true, 'user_rooms' => $user_rooms]);
    }

    public function buyRoomRequest(Request $request)
    {
        //validate
        $request->validate(
            [
                'walled_id' => 'required',
                'room_type_id' => 'required|integer',
                'buyoptionname' => 'required |string',
            ],
            [
                'walled_id.required' => 'Cüzdan adresi boş olamaz!',
                'room_type_id.required' => 'Oda tipi boş olamaz!',
                'room_type_id.integer' => 'Oda tipi sayı olmalı!',
                'buyoptionname.required' => 'Satin Alma Tipi boş olamaz!',
                'buyoptionname.string' => 'Satin Alma Tipi string olmalı!',
            ]
        );
        //find time type already have or not
        
        //find buyOPtions by time type
        $buyOption = BuyOptions::where('option_name', $request->buyoptionname)->first();
        if (!$buyOption) {
            return response()->json(['status' => false, 'message' => 'Satin Alma Tipi bulunamadı!']);
        }
        //check in date and check out date
        $check_in_date = date('Y-m-d');
        $check_out_date = date('Y-m-d', strtotime($check_in_date . ' + ' . $buyOption->option_days . ' days'));

        //check if user exists
        $user = User::where('wallet_id', $request->walled_id)->first();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Kullanıcı bulunamadı!']);
        }

        //check if room type exists
        $room_type = room_types::find($request->room_type_id);
        if (!$room_type) {
            return response()->json(['status' => false, 'message' => 'Oda tipi bulunamadı!']);
        }

        //check available room
        $available_room = room::where('room_type_id', $room_type->id)->where('status', 0)->first();
        if (!$available_room) {
            return response()->json(['status' => false, 'message' => 'Bu oda tipi için boş oda bulunamadı!']);
        }
        if ($check_in_date > $check_out_date) {
            return response()->json(['status' => false, 'message' => 'Giriş tarihi çıkış tarihinden büyük olamaz!']);
        }

        $oneDayPrice= $room_type->room_price;
        $amount = $oneDayPrice * $buyOption->option_days;


        $guid = $this->guidGenerator();

        transaction::create([
            'room_id' => $available_room->id,
            'user_id' => $request->user_id,
            'wallet_id' => $user->wallet_id,
            'check_in_date' => $request->check_in_date,
            'check_out_date' => $request->check_out_date,
            'transaction_id' => $guid,
            'transaction_amount' => $amount,
        ]);
        //nulls
        // transaction_status
        // transaction_booking_status
        // transaction_payment_method

        return response()->json(['status' => true, 'transaction_id' => $guid]);
    }

    public function buyRoomConfirm(Request $request)
    {
        //validate
        $request->validate(
            [
                'transaction_id' => 'required|string',
                'transaction_status' => 'required|integer',
            ],
            [
                'transaction_id.required' => 'İşlem ID alanı boş bırakılamaz!',
                'transaction_status.required' => 'İşlem durumu alanı boş bırakılamaz!',
                'transaction_id.string' => 'İşlem ID sadece harflerden oluşabilir!',
                'transaction_status.integer' => 'İşlem durumu sadece sayılardan oluşabilir!',
            ]
        );

        //check if transaction exists
        $transaction = transaction::where('transaction_id', $request->transaction_id)->first();
        if (!$transaction) {
            return response()->json(['status' => false, 'message' => 'İşlem bulunamadı!']);
        }

        //check if transaction status is 0
        if ($transaction->transaction_status != 0) {
            return response()->json(['status' => false, 'message' => 'İşlem zaten onaylanmış!']);
        }

        $transaction->transaction_status = $request->transaction_status;
        $transaction->save();

        //get room
        $room = room::find($transaction->room_id);

        $room->transaction_id = $transaction->transaction_id;
        $room->status = 1; //1 means room is occupied
        $room->save();


        return response()->json(['status' => true, 'message' => 'İşlem başarıyla onaylandı!']);



        //think this are csharp make me httpclient and post request
        // var client = new HttpClient();
        // var values = new Dictionary<string, string>
        // {
        //     { "transaction_id", "123456789" },
        //     { "transaction_status", "1" }
        // };

        // var content = new FormUrlEncodedContent(values);

        // var response = await client.PostAsync("http://localhost:8000/api/buyRoomConfirm", content);

        //read json response
        // var responseString = await response.Content.ReadAsStringAsync();

        // var responseJson = JsonConvert.DeserializeObject<Dictionary<string, string>>(responseString);

        // Console.WriteLine(responseJson["status"]);
        // Console.WriteLine(responseJson["message"]);

    }
}
