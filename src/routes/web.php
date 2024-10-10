<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendancesController;
use App\Http\Controllers\RegisterdUserController;
use App\Http\Controllers\AuthenticatedSessionController;

use App\Http\Controllers\Auth\EmailVerificationController;
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

Route::controller(EmailVerificationController::class)
	->prefix('email')->name('verification.')->group(function () {
		// 確認メール送信画面
		// Route::get('verify', 'index')->name('notice');
		// Route::get('verify', 'index')->middleware('auth')->name('notice');

		// 確認メール送信
		Route::post('verification-notification', 'notification')
			->middleware('throttle:6,1')->name('send');

		// 確認メールリンクの検証
		Route::get('verification/{id}/{hash}', 'verification')
			->middleware(['signed', 'throttle:6,1'])->name('verify');
		
		// 確認メール再送
		Route::get('resend_verify_email', 'resendVerifyEmail');

		// セッションリセット（確認メールが届かないなどイレギュラー時の対応）
		Route::get('reset', 'resetSession');
	});



// ログイン関連のページ
Route::post('/register',[RegisterdUserController::class,'store']);
Route::post('/login',[AuthenticatedSessionController::class,'store']);
Route::post('/logout',[AuthenticatedSessionController::class,'destroy']);

// 認証が必要なページ
Route::middleware(['web', 'verified', 'auth'])->group(function () {
    Route::get('/', [AttendancesController::class, 'index']);
    Route::post('/store', [AttendancesController::class, 'store']);
    Route::get('/attendance', [AttendancesController::class, 'attendance']);
    Route::get('/personal', [AttendancesController::class, 'personal']);

    Route::post('/reset_all', [AttendancesController::class, 'resetAll']);
    Route::post('/reset_end', [AttendancesController::class, 'resetEnd']);
});
