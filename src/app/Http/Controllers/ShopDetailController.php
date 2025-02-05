<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Consts\CommonConst;
use App\Models\User;
use App\Models\Shop;
use App\Models\Genre;
use App\Models\Course;
use App\Models\Reservation;
use App\Models\ReservedCourse;
use App\Models\Evaluation;

class ShopDetailController extends Controller
{
    public function index($shop_id)
    {
        // 現在認証されているユーザーを取得
        $user = Auth::user();
        $userId = Auth::id();
        $profile = User::find($userId);
        if(isset($profile)){
            if(!$profile->hasVerifiedEmail()) {
                // 会員登録したもののメール認証が済んでいない場合はメール認証を促す画面へ遷移
                return view('auth.verify-email-massage');
            }
        } else {
            $profile = ['name' => '', 'id' => 0];
        }

        // 選択された店舗のデータを取得
        $shop = Shop::find($shop_id);
        $prefName = CommonConst::PREF_CODE[$shop['area_index']];

        // この店舗に登録されているすべてのコースメニューのうち、予約可能なもののリスト
        $courses = Course::where('shop_id', '=', $shop_id) 
            -> where('enable', "=", true)
            -> get();

        // その他のデータ
        $reservation = null;

        // この店舗の評価データを取得
        $ret = $this->getEvaluationData($shop_id, $profile['id']);
        $evaluations = $ret[0];
        $evaluation_summary = $ret[1];
        $my_evaluation = $ret[2];
        return view('shop_detail', compact('shop', 'prefName', 'profile', 'reservation', 'courses', 'evaluations', 'evaluation_summary', 'my_evaluation'));
    }

    public function reservationChange(Request $request) 
    {
        // 現在認証されているユーザーを取得
        $user = Auth::user();
        $userId = Auth::id();
        $profile = User::find($userId);
        if(isset($profile)){
            if(!$profile->hasVerifiedEmail()) {
                // 会員登録したもののメール認証が済んでいない場合はメール認証を促す画面へ遷移
                return view('auth.verify-email-massage');
            }
        } else {
            $profile = ['name' => '', 'id' => 0];
        }

        // 対象の予約内容を取得
        $currentReservation = Reservation::find($request -> reservation_id);

        // コースメニューの予約内容を取得
        $reservedCourses = ReservedCourse::with('course')->where('reservation_id', $request -> reservation_id) -> get();
        $totalPrice = 0;
        foreach($reservedCourses as $reservedCourse) {
            $totalPrice += $reservedCourse['price_as_of_reservation'] * $reservedCourse['quantity'];
        }

        $reservation = [
            "id" => $currentReservation->id,
            'shop_id' => $currentReservation->shop_id,
            'booked_datetime' =>$currentReservation->booked_datetime,
            'people_counts' => $currentReservation->people_counts,
            'user_id' => $currentReservation->user_id,
            'reserved_courses' => $reservedCourses,
            'total_price' => $totalPrice,
        ];

        // 選択された店舗のデータを取得
        $shop = Shop::find($request->shop_id);
        $prefName = CommonConst::PREF_CODE[$shop['area_index']];
       
        // この店舗に登録されているすべてのコースメニューのうち、予約可能なもののリスト
        $courses = Course::where('shop_id', '=', $request->shop_id) 
            -> where('enable', "=", true)
            -> get();
                
        // この店舗の評価データを取得
        $ret = $this->getEvaluationData($request->shop_id, $profile['id']);
        $evaluations = $ret[0];
        $evaluation_summary = $ret[1];
        $my_evaluation = $ret[2];
        return view('shop_detail', compact('shop', 'prefName', 'profile', 'reservation', 'courses', 'evaluations', 'evaluation_summary', 'my_evaluation'));
    }

    private function getEvaluationData($shopId, $userId) 
    {
        // この店舗の評価データを取得
        $evaluations = Evaluation::where('shop_id', $shopId)
            ->where('score', '>', 0)
            ->orderBy('created_at', 'desc')
            ->get();
        $totalScore = 0;
        $reviewerCounts = 0;
        foreach($evaluations as $evaluation) {
            if ($evaluation->score > 0) {
                $totalScore += $evaluation->score;
                $reviewerCounts += 1;
            }
        }

        // 評価スコアの平均値と評価者数のまとめ
        $avgScore = ($reviewerCounts>0)? $totalScore/$reviewerCounts: 0;
        $evaluation_summary = [
            'score'=>$avgScore,
            'reviewer_counts' => $reviewerCounts,
        ];

        // ログイン中のユーザーの評価結果
        $myEvaluation = Evaluation::where('shop_id', $shopId)->where('user_id', $userId)->first();

        return [$evaluations, $evaluation_summary, $myEvaluation];
    }
}
