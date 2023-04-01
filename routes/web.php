<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminMainController;
use App\Http\Controllers\AdminRoomController;
use App\Http\Controllers\AdminTransactionController;
use App\Http\Controllers\AdminUserController;

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
    //route admin/dashboard
    Route::get('/dashboard', [AdminMainController::class, 'dashboard'])->name('adminDashboard');
    //route admin/logout
    Route::get('/logout', [AdminAuthController::class, 'logout'])->name('adminLogout');

    //route group Rooms
    Route::group(['prefix' => 'rooms'], function () {
        //route admin/rooms
        Route::get('', [AdminRoomController::class, 'rooms'])->name('rooms');
        // //route admin/rooms/create
        Route::post('/create', [AdminRoomController::class, 'createRoom'])->name('CreateRoom');
        // //route admin/rooms/store
        // Route::post('/store', [AdminRoomController::class, 'storeRoom'])->name('StoreRoom');
        // //route admin/rooms/edit/{id}
        // Route::get('/edit/{id}', [AdminRoomController::class, 'editRoom'])->name('EditRoom');
        // //route admin/rooms/update/{id}
        // Route::post('/update/{id}', [AdminRoomController::class, 'updateRoom'])->name('UpdateRoom');
        // //route admin/rooms/delete/{id}
        // Route::get('/delete/{id}', [AdminRoomController::class, 'deleteRoom'])->name('DeleteRoom');
    });

    //route prefix user
    Route::group(['prefix' => 'users'], function () {
        //route admin/users
        Route::get('', [AdminUserController::class, 'users'])->name('users');
        // //route admin/users/create
        Route::post('/create', [AdminUserController::class, 'createUser'])->name('CreateUser');
        // //route admin/users/store
        // Route::post('/store', [AdminUserController::class, 'storeUser'])->name('StoreUser');
        // //route admin/users/edit/{id}
        // Route::get('/edit/{id}', [AdminUserController::class, 'editUser'])->name('EditUser');
        // //route admin/users/update/{id}
        // Route::post('/update/{id}', [AdminUserController::class, 'updateUser'])->name('UpdateUser');
        // //route admin/users/delete/{id}
        // Route::get('/delete/{id}', [AdminUserController::class, 'deleteUser'])->name('DeleteUser');
    });

    Route::group(['prefix' => 'trans'], function () {
        Route::get('', [AdminTransactionController::class, 'trans'])->name('trans');
    });

    

});
