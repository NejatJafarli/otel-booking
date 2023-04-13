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

Route::group(['prefix' => 'api'], function () {

    //user login post
    Route::post('user/login', [ApiAuthController::class, 'login']);
    //user/get/id
    Route::get('user/get/{id}', [ApiMainController::class, 'getUser']);

    //user rooms
    Route::get('user/rooms/{id}', [ApiMainController::class, 'getUserRooms']);
    //get room Types
    Route::get('/room_types', [ApiMainController::class, 'getRoomTypes']);

    //get buy options
    Route::get('/buy_options', [ApiMainController::class, 'getBuyOptions']);

    // buy a room
    Route::post('user/buyRoomRequest', [ApiMainController::class, 'buyRoomRequest']);

    Route::post('user/buyRoomConfirm', [ApiMainController::class, 'buyRoomConfirm']);
});

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
