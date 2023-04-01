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
Route::post('user/register', [ApiAuthController::class, 'register']);
//user/get/id
Route::get('user/get/{id}', [ApiMainController::class, 'getUser']);


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
