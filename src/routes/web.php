<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendancesController;
use App\Http\Controllers\RegisterdUserController;
use App\Http\Controllers\AuthenticatedSessionController;

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

// ログイン関連のページ
Route::post('/register',[RegisterdUserController::class,'store']);
Route::post('/login',[AuthenticatedSessionController::class,'store']);
Route::post('/logout',[AuthenticatedSessionController::class,'destroy']);

// 認証が必要なページ
Route::middleware('auth')->group(function () {
    Route::get('/', [AttendancesController::class, 'index']);
    Route::post('/store', [AttendancesController::class, 'store']);
    Route::get('/attendance', [AttendancesController::class, 'attendance']);
    Route::get('/personal', [AttendancesController::class, 'personal']);

    Route::post('/reset_all', [AttendancesController::class, 'resetAll']);
    Route::post('/reset_end', [AttendancesController::class, 'resetEnd']);
});
