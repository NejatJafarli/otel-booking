<?php

use App\Http\Controllers\ApiAuthController;
use App\Http\Controllers\ApiMainController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


//user login post
Route::post('user/login', [ApiAuthController::class, 'login']);
Route::post('user/register', [ApiAuthController::class, 'register']);
//user/get/id
Route::get('user/get/{id}', [ApiMainController::class, 'getUser']);

//user rooms
Route::get('user/rooms/{id}', [ApiMainController::class, 'getUserRooms']);
//get room Types
Route::get('/room_types', [ApiMainController::class, 'getRoomTypes']);

//get buy options
Route::get('/buy_options/{type_id}', [ApiMainController::class, 'getBuyOptions']);

Route::get('getHotel/{id}', [ApiMainController::class, 'getHotel']);

Route::post('enterHotel', [ApiMainController::class, 'enterHotel']);
// buy a room
Route::post('user/buyRoomRequest', [ApiMainController::class, 'buyRoomRequest']);

Route::post('user/buyRoomConfirm', [ApiMainController::class, 'buyRoomConfirm']);
Route::post('user/setRoomPassword', [ApiMainController::class, 'setRoomPassword']);

Route::post('user/BuyHotelRequest', [ApiMainController::class, 'BuyHotelRequest']);
Route::post('user/BuyHotelConfirm', [ApiMainController::class, 'BuyHotelConfirm']);
Route::post('user/loginManuel', [ApiMainController::class, 'LoginManuel']);
Route::post('user/registerManuel', [ApiMainController::class, 'RegisterManuel']);
Route::post('user/setWalletId', [ApiMainController::class, 'setWalletId']);
Route::post('user/createTransactionRequest', [ApiMainController::class, 'TransactionRequest']);


//enter room
Route::post('enterRoom', [ApiMainController::class, 'enterRoom']);
Route::post('getBookedRooms', [ApiMainController::class, 'getBookedRooms']);
Route::post('getConfig', [ApiMainController::class, 'getConfig']);

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
