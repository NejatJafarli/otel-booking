<?php

namespace App\Http\Controllers;

use App\Models\BuyOptions;
use App\Models\room;
use App\Models\Hotel;
use App\Models\room_types;
use App\Models\transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Models\myConfig;
class ApiMainController extends Controller
{

    
    // decimal to hex
    public function dec2hex($dec) {
        return dechex($dec);
    }

     //convert eth to wei
     public function convertEthToWeiAndConvertHex($eth){
        return "0x".$this->dec2hex($eth * 1000000000000000000);
    }

    private function guidGenerator()
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    private function GetCurrentEthValueByUsd(){

        $url='https://bitpay.com/api/rates';
        $json=json_decode( file_get_contents( $url ) );
        $dollar=$btc=0;
        $eth = 0;
        $usd = 1;
        foreach( $json as $obj ){
            if( $obj->code=='USD' )$btc=$obj->rate;
            if( $obj->code=='ETH' )$eth=$obj->rate;
        }
        
         return intval( ($usd/$eth)*$btc);
            
    }

    public function getBuyOptions($type_id){

        $room_type = room_types::find($type_id);
        if($room_type == null){
            return response()->json(['status' => false, 'message' => 'room type not found']);
        }


        $buyOptions = BuyOptions::all();
        
        $array = [];
        $priceArray = [];
        $priceEthArray = [];
        $priceEthArrayFloat = [];
        foreach($buyOptions as $buyOption){
            $array[] = $buyOption->option_name;
            if($buyOption->discount_percent!=null){
                //find discount
                $discount = $room_type->room_price * ($buyOption->discount_percent/100);
                $price = $room_type->room_price - $discount;
                $price = ($price/7) * $buyOption->option_days;
                //round price and add to array
                $priceArray[] = intval($price);
            }else{
                $price =  ($room_type->room_price/7) * $buyOption->option_days;
                //round price and add to array
                $priceArray[] = intval($price);
            }
            $OneEth= $this->GetCurrentEthValueByUsd();
            $UsdPrice= $priceArray[count($priceArray)-1];

            $realPrice = $UsdPrice/$OneEth;
            $priceEthArrayFloat[] = $realPrice;
            //get 4 decimal
            $priceEthArray[] =$this->convertEthToWeiAndConvertHex($realPrice);
        }
        
        
        return response()->json(['status' => true, 'option_names' => $array ,"prices"=>$priceArray,"eth_prices"=>$priceEthArray,"eth_pricesfloat"=>$priceEthArrayFloat]);
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
            $room_type->have_room = room::where('room_type_id', $room_type->id)->where('room_status', 0)->count();
            //unset
            $room_type->price = $room_type->room_price;
            $room_type->type = $room_type->id;
            $room_type->name = $room_type->room_type;
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
                'wallet_id.required' => 'Cüzdan adresi boş olamaz!',
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

        //get check in date datetime
        $check_in_date= date('Y-m-d H:i:s');
        $check_out_date = date('Y-m-d H:i:s', strtotime($check_in_date . ' + ' . $buyOption->option_days . ' days'));
        //check if user exists
        $user = User::where('wallet_id', $request->wallet_id)->first();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Kullanıcı bulunamadı!']);
        }

        //check if room type exists
        $room_type = room_types::find($request->room_type_id);
        if (!$room_type) {
            return response()->json(['status' => false, 'message' => 'Oda tipi bulunamadı!']);
        }

        //check available room
        $available_room = room::where('room_type_id', $room_type->id)->where('room_status', 0)->first();
        if (!$available_room) {
            return response()->json(['status' => false, 'message' => 'Bu oda tipi için boş oda bulunamadı!']);
        }
        if ($check_in_date > $check_out_date) {
            return response()->json(['status' => false, 'message' => 'Giriş tarihi çıkış tarihinden büyük olamaz!']);
        }

        $oneDayPrice= $room_type->room_price;
        $amount = $oneDayPrice * $buyOption->option_days;



        if($buyOption->discount_percent!=null){
            $discount = $buyOption->option_discount;
            //find percent of price
            $discountAmount = $amount * $discount / 100;
            $amount = $amount - $discountAmount;
        }


        $guid = $this->guidGenerator();

        transaction::create([
            'room_id' => $available_room->id,
            'user_id' => $user->id,
            'wallet_id' => $user->wallet_id,
            'check_in_date' => $check_in_date,
            'check_out_date' => $check_out_date,
            'transaction_id' => $guid,
            'transaction_amount' => $amount,
            "transaction_status"=>"2"
        ]);
        //nulls
        // transaction_status
        // transaction_booking_status
        // transaction_payment_method

        return response()->json(['status' => true, 'transaction_id' => $guid,"message"=>"İşlem başarılı bir şekilde oluşturuldu!"]);
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

        if($transaction->transaction_status==2){

            if($request->transaction_status==0){
                //get room
                $room = room::find($transaction->room_id);

                $room->transaction_id = $transaction->transaction_id;
                $room->room_status = 1; //1 means room is occupied
                $room->save();
            }

            $transaction->transaction_status = $request->transaction_status;
            $transaction->save();
            return response()->json(['status' => true, 'message' => 'İşlem başarıyla onaylandı!',"room_number"=>$room->room_number,"room_type"=>$room->room_type()->first()->room_type]);

        }else{
            //islem Status u pending de degil 
            return response()->json(['status' => false, 'message' => 'İşlem Beklemede Değil!']);
        }
    }

    public function setRoomPassword(Request $req){

        //validate room id and password
        $req->validate(
            [
                'room_number' => 'required|integer',
                'password' => 'string',
            ],
            [
                'room_number.required' => 'Oda numarası boş olamaz!',
                'room_number.integer' => 'Oda numarası sadece sayılardan oluşmalı!',
                'password.required' => 'Şifre boş olamaz!',
                'password.string' => 'Şifre string olmalı!',
            ]
        );

        // find room
        $room = room::where('room_number',$req->room_number)->first();
        if(!$room){
            return response()->json(['status' => false, 'message' => 'Oda bulunamadı!']);
        }

        //check if room is occupied
        if($room->room_status != 1){
            return response()->json(['status' => false, 'message' => 'Oda boş!']);
        }

        //get room transaction
        $transaction = transaction::where('transaction_id',$room->transaction_id)->first();

        //check if transaction is confirmed
        if($transaction->transaction_status != 0){
            return response()->json(['status' => false, 'message' => 'İşlem onaylanmamış!']);
        }

        $transaction->room_password = Hash::make($req->password);

        $transaction->save();

        return response()->json(['status' => true, 'message' => 'Şifre başarıyla ayarlandı!']);
    }

    public function getBookedRooms(Request $request){
        //wallet id 
        $request->validate(
            [
                'wallet_id' => 'required|string',
            ],
            [
                'wallet_id.required' => 'Cüzdan ID boş olamaz!',
                'wallet_id.string' => 'Cüzdan ID sadece harflerden oluşmalı!',
            ]
        );
        //get all user transactions check if transaction status is 0 and check in date and check out date between now
        $transactions = transaction::where('wallet_id',$request->wallet_id)->where("room_id","!=",null)->where('transaction_status',0)->where('check_out_date','>=',now())->get();
        if(!$transactions){
            return response()->json(['status' => false, 'message' => 'Rezervasyon bulunamadı!']);
        }
        $rooms = [];
        foreach($transactions as $transaction){
            $room = room::find($transaction->room_id);

            //get room type id and number
            $room_type = $room->room_type()->first()->id;
            $room_number = $room->room_number;
            //add room to array
            array_push($rooms,["room_type"=>$room_type,"room_number"=>$room_number,"room_type_name"=>$room->room_type()->first()->room_type]);
        }

        return response()->json(['status' => true, 'message' => 'Rezervasyonlar başarıyla getirildi!',"BookedRooms"=>$rooms]);
    }

    public function enterRoom(Request $request){
        //room number and password
        $request->validate(
            [
                'room_number' => 'required|integer',
            ],
            [
                'room_number.required' => 'Oda numarası boş olamaz!',
                'room_number.integer' => 'Oda numarası sadece sayılardan oluşmalı!',
            ]
        );

        // find room
        $room = room::where('room_number',$request->room_number)->first();
        if(!$room){
            return response()->json(['status' => false, 'message' => 'Oda bulunamadı!']);
        }

        //check if room is occupied
        if($room->room_status != 1){
            return response()->json(['status' => false, 'message' => 'Oda boş!']);
        }

        //get room transaction
        $transaction = transaction::where('transaction_id',$room->transaction_id)->first();

        //check if transaction is confirmed
        if($transaction->transaction_status != 0){
            return response()->json(['status' => false, 'message' => 'İşlem onaylanmamış!']);
        }

        //check dates
        $check_in_date = Carbon::parse($transaction->check_in_date);
        $check_out_date = Carbon::parse($transaction->check_out_date);
        $now = Carbon::now();

        if($now->between($check_in_date,$check_out_date)){
            //check password
            if($transaction->room_password==null){
                return response()->json(['status' => true, 'message' => 'Odaya giriş başarılı!',"room_type"=>$room->room_type()->first()->id]);
            }
            
            if(Hash::check($request->password,$transaction->room_password)){
                return response()->json(['status' => true, 'message' => 'Odaya giriş başarılı!',"room_type"=>$room->room_type()->first()->id]);
            }else{
                return response()->json(['status' => false, 'message' => 'Şifre yanlış!']);
            }
        }else{
            return response()->json(['status' => false, 'message' => 'Oda için rezervasyon tarihi geçmiş!']);
        }
    }

    public function BuyHotelRequest(Request $req){
        // 'wallet_id' => 'required', and hotel id
        $req->validate(
            [
                'wallet_id' => 'required|string',
                'hotel_id' => 'required|integer',
            ],
            [
                'wallet_id.required' => 'Cüzdan ID boş olamaz!',
                'wallet_id.string' => 'Cüzdan ID sadece harflerden oluşmalı!',
                'hotel_id.required' => 'Otel ID boş olamaz!',
                'hotel_id.integer' => 'Otel ID sadece sayılardan oluşmalı!',
            ]
        );

        //find hotel
        $hotel = Hotel::find($req->hotel_id);

        if(!$hotel){
            return response()->json(['status' => false, 'message' => 'Otel bulunamadı!']);
        }

        //check if hotel is have price
        if($hotel->price == null){
            return response()->json(['status' => false, 'message' => 'Otel için fiyat belirlenmemiş!']);
        }

        $guid=$this->guidGenerator();

        $check_in_date_time= Carbon::now();
        //checkout date add days $hotel->day_for_price
        $check_out_date_time = Carbon::now()->addDays($hotel->day_for_price);

        //create transaction
        transaction::create([
            'wallet_id' => $req->wallet_id,
            'hotel_id' => $req->hotel_id,
            'transaction_id' => $guid,
            'transaction_status' => 2,
            'transaction_amount' => $hotel->price,
            'check_in_date' => $check_in_date_time,
            'check_out_date' => $check_out_date_time,
        ]);

        return response()->json(['status' => true,'transaction_id'=>$guid, 'message' => 'Otel için Giriş İsteği başarıyla oluşturuldu!']);
    }

    public function BuyHotelConfirm(Request $req){
        // transaction id // status 0 or 1
        //validate
        $req->validate(
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

        //find transaction
        $transaction = transaction::where('transaction_id',$req->transaction_id)->first();

        if(!$transaction){
            return response()->json(['status' => false, 'message' => 'İşlem bulunamadı!']);
        }

        //check if transaction is hotel transaction
        if($transaction->hotel_id == null){
            return response()->json(['status' => false, 'message' => 'İşlem otel işlemi değil!']);
        }

        //check if transaction is confirmed
        if($transaction->transaction_status != 2){
            return response()->json(['status' => false, 'message' => 'İşlem Beklemede değil!']);
        }

        //check if transaction is confirmed
        if($req->transaction_status == 0){
            $transaction->transaction_status = 0;
            $transaction->save();
            return response()->json(['status' => true, 'message' => 'İşlem onaylandı!']);
        }else if ($req->transaction_status == 1){
            $transaction->transaction_status = 1;
            $transaction->save();
            return response()->json(['status' => true, 'message' => 'İşlem reddedildi!']);
        }else{
            return response()->json(['status' => false, 'message' => 'Durum 0 veya 1 olmalı!']);
        }
    }
    public function getHotel($id){
        $hotel = Hotel::find($id);
        if(!$hotel){
            return response()->json(['status' => false, 'message' => 'Otel bulunamadı!']);
        }

        //change hotel field name like name -= hotel_name
        $hotel->Hotel_Name = $hotel->name;
        unset($hotel->name);
        $hotel->Hotel_Type=$hotel->price == null ? 0 : 1;
        $hotel->Hotel_Price=$hotel->price;
        $hotel->Hotel_DayForPrice=$hotel->day_for_price;
        unset($hotel->price);
        unset($hotel->day_for_price);

        $OneEth= $this->GetCurrentEthValueByUsd();
        $UsdPrice=$hotel->Hotel_Price;
        $realPrice = $UsdPrice/$OneEth;

        $hotel->Hotel_EthPriceFloat=$realPrice;
        $hotel->Hotel_EthPrice=$this->convertEthToWeiAndConvertHex($realPrice);
       
        return response()->json(['status' => true, 'message' => 'Otel bilgileri getirildi!','hotel'=>$hotel]);
    }

    public function enterHotel(Request $req){
        //wallet id and hotel_id
        $req->validate(
            [
                'wallet_id' => 'required|string',
                'hotel_id' => 'required|integer',
            ],
            [
                'wallet_id.required' => 'Cüzdan ID boş olamaz!',
                'wallet_id.string' => 'Cüzdan ID sadece harflerden oluşmalı!',
                'hotel_id.required' => 'Otel ID boş olamaz!',
                'hotel_id.integer' => 'Otel ID sadece sayılardan oluşmalı!',
            ]
        );

        //find hotel
        $hotel = Hotel::find($req->hotel_id);
        
        if(!$hotel)
            return response()->json(['status' => false, 'message' => 'Otel bulunamadı!']);
        

        //check if hotel is have price
        if($hotel->price == null)
            return response()->json(['status' => false, 'message' => 'Otel için fiyat belirlenmemiş!']);
        

        //find wallet
        $user = User::where("wallet_id",$req->wallet_id);


        if(!$user)
            return response()->json(['status' => false, 'message' => 'Kullanici bulunamadı!']);


        //check transaction
        $transaction = transaction::where('wallet_id',$req->wallet_id)->where('hotel_id',$req->hotel_id)->where('transaction_status',0)->first();

        if($transaction){
            //check date
            $check_in_date_time = Carbon::parse($transaction->check_in_date);
            $check_out_date_time = Carbon::parse($transaction->check_out_date);

            $now = Carbon::now();

            if($now->between($check_in_date_time,$check_out_date_time))
                return response()->json(['status' => true, 'message' => 'Otel için Giriş Satin Alimi bulundu!']);
            
        }else
            return response()->json(['status' => false,"price"=>$hotel->price,"priceforday"=>$hotel->day_for_price,"payment"=>true, 'message' => 'Otel için Giriş Satin Alimi bulunamadı!']);
    }
    public function getConfig(Request $req){
        // get named config
        $config = myConfig::where('key',$req->get)->first();
        if($config)
            return response()->json(['status' => true, 'message' => 'Config getirildi!','cevap'=>$config->value]);
        else
            return response()->json(['status' => false, 'message' => 'Config bulunamadı!']);
    }
}
