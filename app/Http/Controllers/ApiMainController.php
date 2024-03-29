<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ApiAuthController;
use App\Models\BuyOptions;
use App\Models\room;
use App\Models\Hotel;
use App\Models\room_types;
use App\Models\transaction;
use App\Models\transaction_request;
use App\Models\User_Wallets;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Models\myConfig;

class ApiMainController extends Controller
{
    function send_notification($message)
    {
        $content = array(
            "en" => $message
        );

        $fields = array(
            'app_id' => "1b7268f1-4239-41c8-b1c1-35a82e32e373",
            'included_segments' => array('All'),
            'data' => array("foo" => "bar"),
            'contents' => $content,
            'url' => "https://panel.cyprusvarosha.com/admin/trans/requests"
        );

        $fields = json_encode($fields);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Basic NjI2MjdkYjItMTNmMi00ZDZkLWE0ODAtZGFkMDg5NjI2OGJh'
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }
    // decimal to hex
    public function dec2hex($dec)
    {
        return dechex($dec);
    }

    //convert eth to wei
    public function convertEthToWeiAndConvertHex($eth)
    {
        return "0x" . $this->dec2hex($eth * 1000000000000000000);
    }

    private function guidGenerator()
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    private function GetCurrentEthValueByUsd()
    {

        $url = 'https://bitpay.com/api/rates';
        $json = json_decode(file_get_contents($url));
        $dollar = $btc = 0;
        $eth = 0;
        $usd = 1;
        foreach ($json as $obj) {
            if ($obj->code == 'USD') $btc = $obj->rate;
            if ($obj->code == 'ETH') $eth = $obj->rate;
        }

        return intval(($usd / $eth) * $btc);
    }

    public function getBuyOptions($type_id)
    {

        $room_type = room_types::find($type_id);
        if ($room_type == null) {
            return response()->json(['status' => false, 'message' => 'room type not found']);
        }


        $buyOptions = BuyOptions::all();

        $array = [];
        $priceArray = [];
        $priceEthArray = [];
        $priceEthArrayFloat = [];
        foreach ($buyOptions as $buyOption) {
            $array[] = $buyOption->option_name;
            if ($buyOption->discount_percent != null) {
                //find discount
                $discount = $room_type->room_price * ($buyOption->discount_percent / 100);
                $price = $room_type->room_price - $discount;
                $price = ($price / 7) * $buyOption->option_days;
                //round price and add to array
                $priceArray[] = intval($price);
            } else {
                $price =  ($room_type->room_price / 7) * $buyOption->option_days;
                //round price and add to array
                $priceArray[] = intval($price);
            }
            if ($room_type->room_price != 0) {
                $OneEth = $this->GetCurrentEthValueByUsd();
                $UsdPrice = $priceArray[count($priceArray) - 1];
                
                $realPrice = $UsdPrice / $OneEth;
                $priceEthArrayFloat[] = $realPrice;
                //get 4 decimal
                $priceEthArray[] = $this->convertEthToWeiAndConvertHex($realPrice);
            }else{
                $priceEthArray[] = "0x0";
                $priceEthArrayFloat[] = 0;
            }
        }


        return response()->json(['status' => true, 'option_names' => $array, "prices" => $priceArray, "eth_prices" => $priceEthArray, "eth_pricesfloat" => $priceEthArrayFloat]);
    }

    public function getUser($id)
    {
        $user = User::find($id);
        return response()->json(['status' => true, 'user' => $user]);
    }

    public function getRoomTypes(Request $req)
    {
        //hotel id validate
        $req->validate([
            'hotel_id' => 'required',
        ], [
            'hotel_id.required' => 'hotel id is required',
        ]);


        $hotel = Hotel::find($req->hotel_id);
        if ($hotel == null)
            return response()->json(['status' => false, 'message' => 'hotel not found']);


        $room_types = room_types::where('hotel_id', $req->hotel_id)->get();

        if ($room_types == null)
            return response()->json(['status' => false, 'message' => 'room types not found on this hotel']);

        //have a room
        foreach ($room_types as $room_type) {
            $room_type->have_room = room::where('room_type_id', $room_type->id)->where('room_status', 0)->count();
            //unset
            $room_type->price = $room_type->room_price;
            $room_type->type = $room_type->id;
            $room_type->name = $room_type->room_type . " (" . $room_type->have_room . " Room)";
            unset($room_type->room_price);
            unset($room_type->room_type);
            unset($room_type->id);
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
                'wallet_id' => 'required',
                'room_type_id' => 'required|integer',
                'buyoptionname' => 'required |string',
            ],
            [
                'wallet_id.required' =>  'wallet id is required',
                'room_type_id.required' =>  'room type id is required',
                'room_type_id.integer' =>  'room type id must be integer',
                'buyoptionname.required' =>  'buy option name is required',
                'buyoptionname.string' =>  'buy option name must be string',
            ]
        );
        //find time type already have or not


        //find buyOPtions by time type
        $buyOption = BuyOptions::where('option_name', $request->buyoptionname)->first();
        if (!$buyOption)
            return response()->json(['status' => false, 'message' => 'Buy option not found!']);
        //check in date and check out date

        //get check in date datetime
        $check_in_date = date('Y-m-d H:i:s');
        $check_out_date = date('Y-m-d H:i:s', strtotime($check_in_date . ' + ' . $buyOption->option_days . ' days'));
        //check if user exists

        $userid = User_Wallets::where('wallet_id', $request->wallet_id)->first();
        if (!$userid)
            return response()->json(['status' => false, 'message' => 'User not found!']);

        $user = User::find($userid->user_id);

        if (!$user)
            return response()->json(['status' => false, 'message' => 'User not found!']);
        //check if room type exists
        $room_type = room_types::find($request->room_type_id);
        if (!$room_type)
            return response()->json(['status' => false, 'message' => 'Room type not found!']);

        //check available room
        $available_room = room::where('room_type_id', $room_type->id)->where('room_status', 0)->first();

        if (!$available_room)
            return response()->json(['status' => false, 'message' => 'There is no available room! for this room type']);

        if ($check_in_date > $check_out_date)
            return response()->json(['status' => false, 'message' => 'Check in date must be less than check out date!']);

        $oneDayPrice = $room_type->room_price / 7;
        $amount = $oneDayPrice * $buyOption->option_days;


        if ($buyOption->discount_percent != null) {
            $discount = $buyOption->option_discount;
            //find percent of price
            $discountAmount = $amount * $discount / 100;
            $amount = $amount - $discountAmount;
        }


        $guid = $this->guidGenerator();

        $tran = transaction::create([
            'room_id' => $available_room->id,
            'user_id' => $user->id,
            'wallet_id' => $request->wallet_id,
            'user_id' => $user->id,
            'check_in_date' => $check_in_date,
            'check_out_date' => $check_out_date,
            // 'transaction_id' => $guid,
            'transaction_amount' => $amount,
            "transaction_status" => "2"
        ]);
        //nulls
        // transaction_status
        // transaction_booking_status
        // transaction_payment_method

        return response()->json(['status' => true, 'transaction_id' => $tran->id, "message" => "Transaction created successfully!"]);
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
                'transaction_id.required' => 'Transaction ID field cannot be left blank!',
                'transaction_status.required' => 'Transaction status field cannot be left blank!',
                'transaction_id.string' => 'Transaction ID can only consist of letters!',
                'transaction_status.integer' => 'Transaction status can only consist of numbers!',

            ]
        );

        //check if transaction exists
        $transaction = transaction::find($request->transaction_id);
        if (!$transaction) {
            return response()->json(['status' => false, 'message' => 'Transaction not found!']);
        }
        $transaction->transaction_id = $request->real_tran_id;
        $transaction->save();

        if ($transaction->transaction_status == 2) {
            $transaction->transaction_status = $request->transaction_status;
            $transaction->save();

            if ($request->transaction_status == 0) {
                //get room
                $room = room::find($transaction->room_id);

                $room->transaction_id = $transaction->transaction_id;
                $room->room_status = 1; //1 means room is occupied
                $room->save();

                return response()->json(['status' => true, 'message' => 'Transaction confirmed successfully!', "room_number" => $room->room_number, "room_type" => $room->room_type()->first()->room_type]);
            } else if ($request->transaction_status == 1) {
                return response()->json(['status' => false, 'message' =>  'Transaction declined!']);
            }
        } else {
            //islem Status u pending de degil 
            return response()->json(['status' => false, 'message' => 'Transaction status is not pending!']);
        }
    }

    public function setRoomPassword(Request $req)
    {

        //validate room id and password username user_pass
        $req->validate(
            [
                'room_number' => 'required|integer',
                'password' => 'required|string',
                'user_id' => 'required',
                'token' => 'required',
                "hotel_id" => "required"
            ],
            [
                'room_number.required' => 'Room number field cannot be left blank!',
                'room_number.integer' => 'Room number can only consist of numbers!',
                'password.required' => 'Password field cannot be left blank!',
                'password.string' => 'Password can only consist of letters!',
                'user_id.required' => 'User password field cannot be left blank!',
                'user_id.string' => 'User password can only consist of letters!',
                "hotel_id" => "Hotel id field cannot be left blank!",
            ]
        );

        //check if user exists4
        $user = User::find($req->user_id);
        if (!$user)
            return response()->json(['status' => false, 'message' => 'User not found!']);


        if ($user->token != $req->token)
            return response()->json(['status' => false, 'message' => 'Token is not valid!']);

        // find room
        $room = room::where('room_number', $req->room_number)->get();
        if (!$room)
            return response()->json(['status' => false, 'message' => "Room not found!"]);

        //foreach
        $found = null;
        foreach ($room as $r) {
            $r_type = $r->room_type()->first();
            if ($r_type) {
                if ($r_type->hotel_id == $req->hotel_id) {
                    $found = $r;
                    break;
                }
            }
        }
        $room = $found;

        if (!$found)
            return response()->json(['status' => false, 'message' => "Room not found!"]);

        //check if room is occupied
        if ($room->room_status != 1)
            return response()->json(['status' => false, 'message' => 'This room is not occupied!']);


        //get room transaction
        $transaction = transaction::where('transaction_id', $room->transaction_id)->first();

        //check if transaction is confirmed
        if ($transaction->transaction_status != 0)
            return response()->json(['status' => false, 'message' => 'This transaction is not confirmed']);


        if ($transaction->user_id != $user->id)
            return response()->json(['status' => false, 'message' => 'This transaction does not belong to this user!']);



        $transaction->room_password = Hash::make($req->password);

        $transaction->save();

        return response()->json(['status' => true, 'message' => 'Room password set successfully!']);
    }

    public function getBookedRooms(Request $request)
    {
        //wallet id 
        $request->validate(
            [
                'user_id' => 'required',
                "hotel_id" => "required"
            ],
            [
                'user_id.required' => 'Wallet ID field cannot be left blank!',
                "hotel_id.required" => "Hotel ID field cannot be left blank!"
            ]
        );

        $hotel = Hotel::find($request->hotel_id);
        if (!$hotel) {
            return response()->json(['status' => false, 'message' => 'Hotel not found!']);
        }

        //get user wallets by wallet id
        //get user by wallet id
        $user = User::find($request->user_id);
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User not found!']);
        }
        //get room types ids which hotel id is equal to hotel id
        $room_types = room_types::where('hotel_id', $hotel->id)->get();
        if (!$room_types) {
            return response()->json(['status' => false, 'message' => 'Room types not found!']);
        }
        $room_types_ids = [];
        foreach ($room_types as $room_type)
            array_push($room_types_ids, $room_type->id);

        //find all this typed rooms
        $rooms = room::whereIn('room_type_id', $room_types_ids)->get();

        if (!$rooms)
            return response()->json(['status' => false, 'message' => 'Rooms not found!']);

        $rooms_ids = [];
        foreach ($rooms as $room)
            array_push($rooms_ids, $room->id);


        //get all user transactions check if transaction status is 0 and check in date and check out date between now
        // $transactions = transaction::where('user_id',$user->id)->where("room_id","!=",null)->where('transaction_status',0)->where('check_out_date','>=',now())->get();
        //get all conditions and rooms_ids
        $transactions = transaction::where('user_id', $user->id)->where("room_id", "!=", null)->where('transaction_status', 0)->where('check_out_date', '>=', now())->whereIn('room_id', $rooms_ids)->get();

        //get all conditions and room_types_ids 
        // $transactions = transaction::where('user_id',$user->id)->where("room_id","!=",null)->where('transaction_status',0)->where('check_out_date','>=',now())->whereIn('room_type_id',$room_types_ids)->get();

        if (!$transactions) {
            return response()->json(['status' => false, 'message' => 'No reservations found!']);
        }
        $rooms = [];
        foreach ($transactions as $transaction) {
            $room = room::find($transaction->room_id);

            //get room type id and number
            $room_type = $room->room_type()->first()->id;
            $room_number = $room->room_number;
            //add room to array
            array_push($rooms, ["room_type" => $room_type, "room_number" => $room_number, "room_type_name" => $room->room_type()->first()->room_type]);
        }
        return response()->json(['status' => true, 'message' => 'Reservations fetched successfully!', "BookedRooms" => $rooms]);
    }

    public function enterRoom(Request $request)
    {
        //room number and password

        $request->validate(
            [
                'room_number' => 'required|integer',
                "hotel_id" => "required",
                "password" => "required"
            ],
            [
                'room_number.required' => 'Room number field cannot be left blank!',
                'room_number.integer' => 'Room number can only consist of numbers!',
                "hotel_id.required" => "Hotel ID field cannot be left blank!",
                "password.required" => "Password field cannot be left blank!"
            ]
        );

        // find room
        $room = room::where('room_number', $request->room_number)->get();
        if (!$room)
            return response()->json(['status' => false, 'message' => "Room not found!"]);

        //foreach
        $found = null;
        foreach ($room as $r) {
            $r_type = $r->room_type()->first();
            if ($r_type) {
                if ($r_type->hotel_id == $request->hotel_id) {
                    $found = $r;
                    break;
                }
            }
        }
        $room = $found;

        if (!$found)
            return response()->json(['status' => false, 'message' => "Room not found!"]);
        // if(!$room->room_type()->first()->hotel_id!= $request->hotel_id)
        //     return response()->json(['status' => false, 'message' => "this room does not belong to this hotel!"]);

        //check if room is occupied
        if ($room->room_status != 1)
            return response()->json(['status' => false, 'message' => "This room is not occupied!"]);


        //get room transaction
        $transaction = transaction::where('transaction_id', $room->transaction_id)->first();

        //check if transaction is confirmed
        if ($transaction->transaction_status != 0) {
            return response()->json(['status' => false, 'message' => 'This room transaction is not confirmed!']);
        }

        //check dates
        $check_in_date = Carbon::parse($transaction->check_in_date);
        $check_out_date = Carbon::parse($transaction->check_out_date);
        $now = Carbon::now();

        if ($now->between($check_in_date, $check_out_date)) {
            //check password
            if ($transaction->room_password == null) {
                return response()->json(['status' => true, 'message' => 'Room entry successful!', "room_type" => $room->room_type()->first()->id]);
            }

            if (Hash::check($request->password, $transaction->room_password)) {
                return response()->json(['status' => true, 'message' => 'Room entry successful!', "room_type" => $room->room_type()->first()->id]);
            } else {
                return response()->json(['status' => false, 'message' => ' Wrong password!']);
            }
        } else {
            return response()->json(['status' => false, 'message' => 'Expired reservation!']);
        }
    }

    public function BuyHotelRequest(Request $req)
    {
        // 'wallet_id' => 'required', and hotel id
        $req->validate(
            [
                'wallet_id' => 'required|string',
                'hotel_id' => 'required|integer',
            ],
            [

                'wallet_id.required' => 'Wallet ID field cannot be left blank!',
                'wallet_id.string' => 'Wallet ID can only consist of letters!',
                'hotel_id.required' => 'Hotel ID field cannot be left blank!',
                'hotel_id.integer' => 'Hotel ID can only consist of numbers!',
            ]
        );

        //find hotel
        $hotel = Hotel::find($req->hotel_id);

        if (!$hotel) {
            return response()->json(['status' => false, 'message' => 'Hotel not found!']);
        }

        //check if hotel is have price
        if ($hotel->price == null) {
            return response()->json(['status' => false, 'message' => ' This hotel is not for sale!']);
        }

        $guid = $this->guidGenerator();

        $check_in_date_time = Carbon::now();
        //checkout date add days $hotel->day_for_price
        $check_out_date_time = Carbon::now()->addDays($hotel->day_for_price);

        //get user wallet
        $wallet = User_Wallets::where('wallet_id', $req->wallet_id)->first();

        if (!$wallet) {
            return response()->json(['status' => false, 'message' => 'Wallet not found!']);
        }

        //get user
        $user = User::find($wallet->user_id);
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User not found!']);
        }

        //create transaction
        $tran = transaction::create([
            'wallet_id' => $req->wallet_id,
            'hotel_id' => $req->hotel_id,
            "user_id" => $user->id,
            // 'transaction_id' => $guid,
            'transaction_status' => 2,
            'transaction_amount' => $hotel->price,
            'check_in_date' => $check_in_date_time,
            'check_out_date' => $check_out_date_time,
        ]);

        return response()->json(['status' => true, 'transaction_id' => $tran->id, 'message' => 'Hotel purchase request created successfully!']);
    }

    public function BuyHotelConfirm(Request $req)
    {
        // transaction id // status 0 or 1
        //validate
        $req->validate(
            [
                'transaction_id' => 'required|string',
                'transaction_status' => 'required|integer',
            ],
            [

                'transaction_id.required' => 'Transaction ID field cannot be left blank!',
                'transaction_status.required' => 'Transaction status field cannot be left blank!',
                'transaction_id.string' => 'Transaction ID can only consist of letters!',
                'transaction_status.integer' => 'Transaction status can only consist of numbers!',
                "real_tran_id.required" => "Real transaction ID field cannot be left blank!",
                "real_tran_id.string" => "Real transaction ID can only consist of letters!"
            ]
        );

        //find transaction
        $transaction = transaction::find($req->transaction_id);

        if (!$transaction) {
            return response()->json(['status' => false, 'message' => 'Transaction not found!']);
        }
        $transaction->transaction_id = $req->real_tran_id;
        $transaction->save();

        //check if transaction is hotel transaction
        if ($transaction->hotel_id == null) {
            return response()->json(['status' => false, 'message' => 'This transaction is not a hotel transaction!']);
        }

        //check if transaction is confirmed
        if ($transaction->transaction_status != 2) {
            return response()->json(['status' => false, 'message' => 'This transaction status is not Pending !']);
        }

        //check if transaction is confirmed
        if ($req->transaction_status == 0) {
            $transaction->transaction_status = 0;
            $transaction->save();
            return response()->json(['status' => true, 'message' => 'Transaction confirmed!']);
        } else if ($req->transaction_status == 1) {
            $transaction->transaction_status = 1;
            $transaction->save();
            return response()->json(['status' => true, 'message' => 'Transaction rejected!']);
        } else {
            return response()->json(['status' => false, 'message' => 'Transaction status is not valid!']);
        }
    }

    public function getHotel($id)
    {
        $hotel = Hotel::find($id);
        if (!$hotel) {
            return response()->json(['status' => false, 'message' => 'Hotel not found!']);
        }

        //change hotel field name like name -= hotel_name
        $hotel->Hotel_Name = $hotel->name;
        unset($hotel->name);
        $hotel->Hotel_Type = $hotel->price == null ? 0 : 1;
        $hotel->Hotel_Price = $hotel->price;
        $hotel->Hotel_DayForPrice = $hotel->day_for_price;
        unset($hotel->price);
        unset($hotel->day_for_price);

        $OneEth = $this->GetCurrentEthValueByUsd();
        $UsdPrice = $hotel->Hotel_Price;
        $realPrice = $UsdPrice / $OneEth;

        $hotel->Hotel_EthPriceFloat = $realPrice;
        $hotel->Hotel_EthPrice = $this->convertEthToWeiAndConvertHex($realPrice);
        return response()->json(['status' => true, 'message' => 'Hotel information retrieved!', 'hotel' => $hotel]);
    }

    public function enterHotel(Request $req)
    {
        //wallet id and hotel_id
        $req->validate(
            [
                'user_id' => 'required',
                'hotel_id' => 'required|integer',
            ],
            [
                'user_id.required' => 'Wallet ID cannot be empty!',
                'hotel_id.required' => 'Hotel ID cannot be empty!',
                'hotel_id.integer' => 'Hotel ID can only consist of numbers!',
            ]
        );

        //find hotel
        $hotel = Hotel::find($req->hotel_id);

        if (!$hotel)
            return response()->json(['status' => false, 'message' => 'Hotel not found!']);


        //check if hotel is have price
        if ($hotel->price == null)
            return response()->json(['status' => false, 'message' => 'Hotel is not for sale!']);


        $user = User::find($req->user_id);


        if (!$user)
            return response()->json(['status' => false, 'message' => 'User not found!']);

        //check transaction
        $transaction = transaction::where('user_id', $user->id)->where('hotel_id', $req->hotel_id)->where('transaction_status', 0)->first();

        if ($transaction) {
            //check date
            $check_in_date_time = Carbon::parse($transaction->check_in_date);
            $check_out_date_time = Carbon::parse($transaction->check_out_date);

            $now = Carbon::now();

            if ($now->between($check_in_date_time, $check_out_date_time))
                return response()->json(['status' => true, 'message' => 'You can enter the hotel!']);
        } else
            return response()->json(['status' => false, "price" => $hotel->price, "priceforday" => $hotel->day_for_price, "payment" => true, 'message' => 'You have not made a reservation for this hotel!']);
    }

    public function getConfig(Request $req)
    {
        // get named config
        $config = myConfig::where('key', $req->get)->first();
        if ($config)
            return response()->json(['status' => true, 'message' => 'Config Received', 'cevap' => $config->value]);
        else
            return response()->json(['status' => false, 'message' => 'Config Not Found']);
    }

    public function RegisterManuel(Request $req)
    {
        //username password validation
        $req->validate(
            [
                'username' => 'required|string',
                'password' => 'required|string',
                "char_number" => "required|integer"
            ],
            [
                'username.required' => 'Username cannot be empty!',
                'username.string' => 'Username can only consist of letters!',
                'password.required' => 'Password cannot be empty!',
                'password.string' => 'Password can only consist of letters!',
                'char_number.required' => 'Char number cannot be empty!',
                'char_number.integer' => 'Char number can only consist of numbers!',
            ]
        );

        //check if username is taken
        $user = User::where('username', $req->username)->first();

        if ($user)
            return response()->json(['status' => false, 'message' => 'Username is taken!']);

        //create user


        //check username validations like latin chars and numbers only etc.
        $regex = "/^[a-zA-Z0-9]+$/";
        if (!preg_match($regex, $req->username))
            return response()->json(['status' => false, 'message' => 'Username can only consist of letters and numbers!']);

        //don't allow to use space
        if (strpos($req->username, ' ') !== false)
            return response()->json(['status' => false, 'message' => 'Username cannot contain spaces!']);


        $user = User::create([
            'username' => $req->username,
            'password' => Hash::make($req->password),
            'character_number' => $req->char_number,
            'token' => ApiAuthController::generateToken()
        ]);

        return response()->json(['status' => true, 'token' => $user->token, 'message' => 'User created!', 'user_id' => $user->id]);
    }

    public function LoginManuel(Request $req)
    {
        //username password validation
        $req->validate(
            [
                'username' => 'required|string',
                'password' => 'required|string',
            ],
            [
                'username.required' => 'Username cannot be empty!',
                'username.string' => 'Username can only consist of letters!',
                'password.required' => 'Password cannot be empty!',
                'password.string' => 'Password can only consist of letters!',
            ]
        );

        //check if username is have
        $user = User::where('username', $req->username)->first();

        if (!$user)
            return response()->json(['status' => false, 'message' => 'User not found!']);

        //check password
        if (!Hash::check($req->password, $user->password))
            return response()->json(['status' => false, 'message' => 'Password is wrong!']);

        $wallet_ids = User_Wallets::where('user_id', $user->id)->get()->pluck("wallet_id")->toArray();

        $user_wallet_id = null;
        if ($wallet_ids) {
            $user_wallet_id = $wallet_ids[0];
        }

        //update user token and return
        $user->token = ApiAuthController::generateToken();
        $user->save();

        return response()->json(['status' => true, 'token' => $user->token, 'message' => 'User logged in!', 'user_id' => $user->id, "wallet_id" => $user_wallet_id, "wallet_ids" => $wallet_ids, "character_number" => $user->character_number]);
    }
    //create TransactionRequest
    public function createTransactionRequest(Request $req)
    {
        //transaction id validation
        $req->validate(
            [
                'transaction_id' => 'required|string',
                'user_tran_id' => 'required|string',
            ],
            [
                'transaction_id.required' => 'Transaction ID cannot be empty!',
                'transaction_id.string' => 'Transaction ID can only consist of letters!',
                'user_tran_id.required' => 'user_tran_id ID cannot be empty!',
                'user_tran_id.string' => 'user_tran_id ID can only consist of letters!',
            ]
        );

        //find transaction
        $transaction = transaction::find($req->transaction_id);

        if (!$transaction)
            return response()->json(['status' => false, 'message' => 'Transaction not found!']);

        if ($transaction->transaction_status != 2)
            return response()->json(['status' => false, 'message' => 'Transaction is not pending!']);

        //watch transaction request is have
        $transactionRequest = transaction_request::where('transaction_id', $req->transaction_id)->first();

        if ($transactionRequest)
            return response()->json(['status' => false, 'message' => 'Transaction Request is have!']);

        //create transaction request
        $transactionRequest = transaction_request::create([
            'transaction_id' => $req->transaction_id,
            'own_transaction_id' => $req->user_tran_id,
        ]);
        $this->send_notification($transaction->user_id . " Transaction Request" . " Transaction Request is created! " . $transactionRequest->id);

        return response()->json(['status' => true, 'message' => 'Transaction Request created!']);
    }

    public function addWalletId(Request $req)
    {

        //validate wallet id
        $req->validate(
            [
                'wallet_id' => 'required|string',
                'user_id' => 'required',
            ],
            [
                'wallet_id.required' => 'Wallet ID cannot be empty!',
                'wallet_id.string' => 'Wallet ID can only consist of letters!',
                'user_id.required' => 'User ID cannot be empty!',
            ]
        );

        //check if wallet id is taken
        $user_wallets = User_Wallets::where('wallet_id', $req->wallet_id)->first();

        if ($user_wallets)
            return response()->json(['status' => false, 'message' => 'Wallet ID is taken!']);

        $user = User::find($req->user_id);

        if (!$user)
            return response()->json(['status' => false, 'message' => 'User not found!']);

        //create new user wallet
        $user_wallets = User_Wallets::create([
            'user_id' => $user->id,
            'wallet_id' => $req->wallet_id,
        ]);

        return response()->json(['status' => true, 'message' => 'Wallet ID added!']);
    }
    //remove walllet id
    public function removeWalletId(Request $req)
    {

        //validate wallet id
        $req->validate(
            [
                'wallet_id' => 'required|string',
                'user_id' => 'required',
            ],
            [
                'wallet_id.required' => 'Wallet ID cannot be empty!',
                'wallet_id.string' => 'Wallet ID can only consist of letters!',
                'user_id.required' => 'User ID cannot be empty!',
            ]
        );

        //check if wallet id is taken
        $user_wallets = User_Wallets::where('wallet_id', $req->wallet_id)->first();

        if (!$user_wallets)
            return response()->json(['status' => false, 'message' => 'Wallet ID not found!']);

        $user = User::find($req->user_id);

        if (!$user)
            return response()->json(['status' => false, 'message' => 'User not found!']);

        if ($user->id != $user_wallets->user_id)
            return response()->json(['status' => false, 'message' => 'Wallet ID not found!']);

        //delete user wallet
        $user_wallets->delete();

        return response()->json(['status' => true, 'message' => 'Wallet ID removed!']);
    }

    public function getRoomTypeScene($id)
    {
        //find this room type
        $room_type = room_types::find($id);

        if (!$room_type)
            return response()->json(['status' => false, 'message' => 'Room Type not found!']);

        if (!$room_type->sceneName)
            return response()->json(['status' => false, 'message' => 'this room type is not have scene!']);
        //find this room type scene
        return response()->json(['status' => true, "sceneName" => $room_type->sceneName, 'message' => 'Room Type not found!']);
    }
}
