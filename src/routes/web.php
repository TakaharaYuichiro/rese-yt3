<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterdUserController;
use App\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\ShopAllController;
use App\Http\Controllers\ShopDetailController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ManagerPageController;
use App\Http\Controllers\ShopEditorController;
use App\Http\Controllers\ShopCourseEditorController;
use App\Http\Controllers\ReservationListController;
use App\Http\Controllers\ReservationDetailController;
use App\Http\Controllers\CourseListController;
use App\Http\Controllers\CourseEditorController;
use App\Http\Controllers\ConfirmReservationController;
use App\Http\Controllers\StripePaymentsController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReservationAcceptedController;
use App\Http\Controllers\ChangeRoleController;
use App\Http\Controllers\ChangeManagerController;

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
		// 確認メール送信
		Route::post('verification-notification', 'notification')
			->middleware('throttle:6,1')->name('send');

		// 確認メールリンクの検証
		Route::get('verification/{id}/{hash}', 'verification')
			->middleware(['signed', 'throttle:6,1'])->name('verify');
		
		// 確認メール再送
		Route::get('resend_verify_email', 'resendVerifyEmail');

		// セッションをリセットしてゲストユーザーで閲覧
		Route::get('resetAndToHome', 'resetAndToHome');
		
	});

// ログイン関連のルート
Route::post('/register',[RegisterdUserController::class,'store']);
Route::post('/login',[AuthenticatedSessionController::class,'store']);
Route::post('/logout',[AuthenticatedSessionController::class,'destroy']);

// guest(会員登録しておらずログインしていないユーザー)でも使えるルート
Route::get('/', [ShopAllController::class, 'index'])->name('home');
Route::get('/search', [ShopAllController::class, 'search']);
Route::post('/favorite', [ShopAllController::class, 'favorite']);	// guestでもviewのfavoriteボタンを押せるがcontroller側で拒否
Route::get('/detail/{shop_id}', [ShopDetailController::class, 'index'])->name('detail');

// 会員登録＆メール認証が必要なルート
Route::middleware(['web', 'verified', 'auth'])->group(function () {
    Route::get('/reservation_change', [ShopDetailController::class, 'reservationChange'])->name('reservation_change');	// 予約内容画面を開く(実際のupdateはstoreで実行)
	Route::post('/confirm_reservation', [ConfirmReservationController::class, 'index']);
	Route::post('/reservation/store', [ReservationController::class, 'store']);	// exists_reservation_id==0ならcreate, >0ならupdateと使い分ける
	Route::post('/reservation/delete', [ReservationController::class, 'delete']);
	Route::get('/reservation_accepted', [ReservationAcceptedController::class, 'index'])->name('reservation_accepted');

	Route::get('/mypage', [MypageController::class, 'index']);
	Route::post('/mypage/favorite', [MypageController::class, 'favorite']);	
	Route::post('/evaluation', [EvaluationController::class, 'index']);
	Route::post('/evaluation/store', [EvaluationController::class, 'store']);
	
    Route::post('/payment', [PaymentController::class, 'index']);
    Route::post('/payment/store', [PaymentController::class, 'store'])->name('payment.store');

	// 店舗代表者用ルート
	Route::group(['middleware' => ['auth', 'can:manager-higher']], function () {
		Route::get('/manager_page', [ManagerPageController::class, 'index'])->name('manager_page');
		Route::get('/shop_editor', [ShopEditorController::class, 'index'])->name('shop_editor');
        Route::get('/shop_editor/new_entry', [ShopEditorController::class, 'newEntry']);
        Route::post('/shop_editor/store', [ShopEditorController::class, 'store']);
		Route::post('/shop_editor/break', [ShopEditorController::class, 'break']);
		Route::post('/shop_editor/remove', [ShopEditorController::class, 'remove']);

		Route::get('/course_list', [CourseListController::class, 'index'])->name('course_list');
		Route::get('/course_editor', [CourseEditorController::class, 'index']);
		Route::get('/course_editor/new_entry', [CourseEditorController::class, 'newEntry']);
		Route::post('/course_editor/store', [CourseEditorController::class, 'store']);
		Route::post('/course_editor/break', [CourseEditorController::class, 'break']);
		Route::post('/course_editor/remove', [CourseEditorController::class, 'remove']);
		
		Route::get('/reservation_list', [ReservationListController::class, 'index']);
		Route::get('/reservation_list/search', [ReservationListController::class, 'search']);
		Route::post('/reservation_detail', [ReservationDetailController::class, 'index']);
	});

	// 管理者用ルート
	Route::group(['middleware' => ['auth', 'can:admin-higher']], function () {
		Route::get('/admin', [AdminController::class, 'index']);
		Route::get('/admin/search', [AdminController::class, 'search']);
		Route::post('/admin/send_group_mail', [AdminController::class, 'sendGroupMail']);
	
		Route::post('/change_role', [ChangeRoleController::class, 'index']);
		Route::post('/change_role/update_role', [ChangeRoleController::class, 'updateRole']);
		Route::post('/change_manager', [ChangeManagerController::class, 'index']);
		Route::post('/change_manager/update_shops', [ChangeManagerController::class, 'updateShops']);
	});
});
