<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminMainController;
use App\Http\Controllers\AdminRoomController;
use App\Http\Controllers\AdminTransactionController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminHotelController;
use App\Http\Controllers\AdminBuyOptionController;
use App\Http\Controllers\AdminFinanceController;
use App\Http\Controllers\AdminConfigController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('', function () {
    //check if admin is logged in
    if (auth()->guard('admin')->check()) {
        return redirect()->route('adminDashboard');
    }
    return redirect()->route('adminLogin');
});
Route::get('admin', function () {
    //check if admin is logged in
    if (auth()->guard('admin')->check()) {
        return redirect()->route('adminDashboard');
    }
    return redirect()->route('adminLogin');
});




//route admin/login
Route::get('/admin/login', [AdminAuthController::class, 'login'])->name('adminLogin');

Route::post('/admin/login', [AdminAuthController::class, 'postLogin'])->name('adminLoginPost');

//create middleware adminAuthenticated route group for admin
Route::group(['prefix' => 'admin', 'middleware' => 'adminauth'], function () {

    //admin list
    Route::get('/admins', [AdminAuthController::class, 'admins'])->name('admins');
    //admin register
    Route::post('/register', [AdminAuthController::class, 'adminRegister'])->name('adminRegister');
    //admin delete
    Route::get('/delete/{id}', [AdminAuthController::class, 'deleteAdmin'])->name('deleteAdmin');
    //admin edit
    // Route::post('/edit', [AdminAuthController::class, 'editAdmin'])->name('editAdmin');


    Route::get('/dashboard', [AdminMainController::class, 'dashboard'])->name('adminDashboard');
    
    Route::get('/logout', [AdminAuthController::class, 'logout'])->name('adminLogout');
    
    Route::group(['prefix' => 'room_types'],function () {
        
        Route::get('', [AdminRoomController::class, 'roomTypes'])->name('roomTypes');
        
        Route::post('/create', [AdminRoomController::class, 'createRoomType'])->name('createRoomType');
        
        Route::get('/delete/{id}', [AdminRoomController::class, 'deleteRoomType'])->name('deleteRoomType');
        
        Route::post('/edit', [AdminRoomController::class, 'editRoomType'])->name('editRoomType');
        
    });
    Route::group(['prefix' => 'configs'],function () {
        
        Route::get('', [AdminConfigController::class, 'configs'])->name('configs');
        
        Route::post('/create', [AdminConfigController::class, 'createMyConfig'])->name('createMyConfig');
        
        Route::get('/delete/{id}', [AdminConfigController::class, 'deleteMyConfig'])->name('deleteMyConfig');
        
        Route::post('/edit', [AdminConfigController::class, 'editMyConfig'])->name('editMyConfig');
        
    });
    Route::group(['prefix' => 'finance'],function () {

        Route::get('', [AdminFinanceController::class, 'raporlar'])->name('raporlar');
        
        Route::post('/getDatas', [AdminFinanceController::class, 'datebydateReports'])->name('datebydateReports');
       
    });
    Route::group(['prefix' => 'buyOption'], function () {
        
        Route::get('', [AdminBuyOptionController::class, 'buyOptions'])->name('buyOptions');

        Route::post('/create', [AdminBuyOptionController::class, 'createBuyOption'])->name('createBuyOption');

        Route::get('/delete/{id}', [AdminBuyOptionController::class, 'deleteBuyOption'])->name('deleteBuyOption');

        Route::post('/edit', [AdminBuyOptionController::class, 'editBuyOption'])->name('editBuyOptions');
        
    });
    //route group Rooms
    Route::group(['prefix' => 'rooms'], function () {
        
        Route::get('', [AdminRoomController::class, 'rooms'])->name('rooms');
        
        Route::post('/create', [AdminRoomController::class, 'createRoom'])->name('CreateRoom');
        
        Route::get('/delete/{id}', [AdminRoomController::class, 'deleteRoom'])->name('DeleteRoom');
        
        Route::post('/edit', [AdminRoomController::class, 'editRoom'])->name('editRoom');
    });
    Route::group(['prefix' => 'hotels'], function () {
       
        Route::get('', [AdminHotelController::class, 'hotels'])->name('hotels');
        
        Route::post('/create', [AdminHotelController::class, 'createHotel'])->name('createHotel');
        
        Route::get('/delete/{id}', [AdminHotelController::class, 'deleteHotel'])->name('deleteHotel');
        
        Route::post('/edit', [AdminHotelController::class, 'editHotel'])->name('editHotel');
    });
    //route prefix user
    Route::get('/chatLog', [AdminMainController::class, 'chatLog'])->name('chatLog');
    Route::get('/onlineUsers', [AdminMainController::class, 'onlineUsers'])->name('onlineUsers');
    Route::get('/onlineChat', [AdminMainController::class, 'onlineChat'])->name('onlineChat');
    Route::group(['prefix' => 'users'], function () {
        Route::get('/{id}', [AdminUserController::class, 'userDetail'])->name('userDetail');
        Route::get('', [AdminUserController::class, 'users'])->name('users');

        Route::post('/create', [AdminUserController::class, 'createUser'])->name('createUser');

        Route::get('/delete/{id}', [AdminUserController::class, 'deleteUser'])->name('deleteUser');

        Route::post('/edit', [AdminUserController::class, 'editUser'])->name('editUser');


        
    });

    Route::group(['prefix' => 'trans'], function () {
        Route::get('', [AdminTransactionController::class, 'trans'])->name('trans');
        Route::get('/requests', [AdminTransactionController::class, 'trans_request'])->name('trans_request');
        Route::post('/confirmOrRejectTransaction', [AdminTransactionController::class, 'confirmOrRejectTransaction'])->name('confirmOrRejectTransaction');
        Route::post('/editTranPass', [AdminTransactionController::class, 'editTranPass'])->name('editTranPass');
    });

    

});
