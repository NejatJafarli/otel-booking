<?php

namespace App\Http\Controllers;

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
        }

        return response()->json(['status' => true, 'room_types' => $room_types]);
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
                'user_id' => 'required|integer',
                'room_type_id' => 'required|integer',
                'check_in_date' => 'required|date',
                'check_out_date' => 'required|date',
                'amount' => 'required|integer',
            ],
            [
                'user_id.required' => 'Kullanıcı ID alanı boş bırakılamaz!',
                'room_type_id.required' => 'Oda tipi ID alanı boş bırakılamaz!',
                'check_in_date.required' => 'Giriş tarihi alanı boş bırakılamaz!',
                'check_out_date.required' => 'Çıkış tarihi alanı boş bırakılamaz!',
                'user_id.integer' => 'Kullanıcı ID sadece sayılardan oluşabilir!',
                'room_type_id.integer' => 'Oda tipi ID sadece sayılardan oluşabilir!',
                'check_in_date.date' => 'Giriş tarihi geçerli bir tarih değil!',
                'check_out_date.date' => 'Çıkış tarihi geçerli bir tarih değil!',
                'amount.required' => 'Tutar alanı boş bırakılamaz!',
            ]
        );

        //check if user exists
        $user = User::find($request->user_id);
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

        //check if check_in_date and check_out_date is valid
        $check_in_date = strtotime($request->check_in_date);
        $check_out_date = strtotime($request->check_out_date);
        if ($check_in_date > $check_out_date) {
            return response()->json(['status' => false, 'message' => 'Giriş tarihi çıkış tarihinden büyük olamaz!']);
        }


        $guid = $this->guidGenerator();

        transaction::create([
            'room_id' => $available_room->id,
            'user_id' => $request->user_id,
            'wallet_id' => $user->wallet_id,
            'check_in_date' => $request->check_in_date,
            'check_out_date' => $request->check_out_date,
            'transaction_id' => $guid,
            'transaction_amount' => $request->amount,
        ]);
        //nulls
        // transaction_status
        // transaction_booking_status
        // transaction_payment_method

        return response()->json(['status' => true, 'transaction_id' => $guid, 'message' => 'Oda satın alma işlemi başarıyla gerçekleştirildi!']);
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

        $room->transaction_id=$transaction->transaction_id;
        $room->status = 1; //1 means room is occupied
        $room->save();


        return response()->json(['status' => true, 'message' => 'İşlem başarıyla onaylandı!']);
    }
}
