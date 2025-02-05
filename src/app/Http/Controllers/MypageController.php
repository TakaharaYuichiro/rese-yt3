<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Shop;
use App\Models\Evaluation;
use App\Models\Reservation;
use App\Models\ReservedCourse;

class MypageController extends Controller
{    
    public function index(){
        // 現在認証されているユーザーを取得
        $user = Auth::user();
        $userId = Auth::id();
        $profile = User::find($userId);
        if(!isset($profile)){
            $profile = ['name' => ''];
        }

        // このユーザーがお気に入り登録している店舗を取得
        $evaluations = Evaluation::with('shop') 
            -> where('user_id', $userId)
            -> where('favorite', 1)
            -> get();

        // このユーザーの本日以降の予約一覧を取得
        $today = date('Y-m-d H:i:s', mktime(0,0,0));
        $reservations = Reservation::query()
            -> where('user_id', '=', $userId)
            -> whereDate('booked_datetime', '>=', $today)   // 今日以降の予約のみ選択(過去分は表示しない)
            -> get();
        $reservations = $reservations -> sortBy('booked_datetime');   // 予約日時順に並べかえ

        // 予約一覧にコース情報などを追加
        foreach($reservations as $reservation) {
            $targetReservationId = $reservation->id;
            $isPaymentCompleted = isset($reservation->charge_id); 
            $targetReservedCourses = ReservedCourse::where('reservation_id', $targetReservationId) -> get();
            $totalPrice = 0;
            foreach($targetReservedCourses as $targetReservedCourse) {
                $price = $targetReservedCourse['price_as_of_reservation'];
                $quantity = $targetReservedCourse['quantity'];
                $totalPrice += $price * $quantity;
            }

            $isPaymentRequired = false;
            $paymentStatusMessage = "";
            if ($totalPrice==0) {
                $paymentStatusMessage = "お支払いが必要な内容はありません";
            } else {
                if ($isPaymentCompleted) {
                    $paymentStatusMessage = "お支払いが完了しています";
                } else {
                    $paymentStatusMessage = "お支払いが未済のため、予約が完了していません";
                    $isPaymentRequired = true;
                }
            }

            $reservation['total_price'] = $totalPrice;
            $reservation['payment_status_message'] = $paymentStatusMessage;
            $reservation['is_payment_required'] = $isPaymentRequired;
        }

        // このユーザーの過去の予約履歴を取得
        $reservation_histories = Reservation::query()
            -> where('user_id', '=', $userId)
            -> where('booked_datetime', '<', $today)   // 今日以降の予約のみ選択(過去分は表示しない)
            -> get();
        $reservation_histories = $reservation_histories -> sortByDesc('booked_datetime');   // 予約日時の降順に並べかえ

        // 過去の予約履歴に評価情報を追加
        $evaluations2 = Evaluation::where('user_id', '=', $userId) -> get();
        foreach($reservation_histories as $reservation_history) {
            $targetShopId = $reservation_history->shop_id;
            $targetEvaluation = $evaluations2->where('shop_id', $targetShopId) -> first();

            if (!is_null($targetEvaluation)) {
                $reservation_history['evaluaiton_id'] = $targetEvaluation['id'];
                $reservation_history['evaluaiton_favorite'] = $targetEvaluation['favorite'];
                $reservation_history['evaluation_score'] = $targetEvaluation['score'];
                $reservation_history['evaluaiton_comment'] = $targetEvaluation['comment'];
            }
        }

        return view('mypage', compact('evaluations', 'profile', 'reservations', 'reservation_histories'));
    }

    public function favorite(Request $request) {

        if (isset($_POST['cancel'])){
            return redirect('/mypage');
        }

        $shopId = $request -> shop_id;
        $userId = Auth::id(); 
        $evaluation = Evaluation::query()
            -> where('user_id', '=', $userId)
            -> where('shop_id', '=', $shopId)
            -> first();

        if (!empty($evaluation)) {
            $updatedFavorite = !$evaluation['favorite'];
            $evaluation -> update(['favorite' => $updatedFavorite]);
        } else {
            $param = [
                'user_id' => $userId,
                'shop_id' => $shopId,
                'favorite' => true,
                'score' => 0,
                'comment' => '', 
            ];
            Evaluation::create($param);
        }

        return back();
    }
}
