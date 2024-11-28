<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// use App\Consts\CommonConst;
use App\Models\User;
use App\Models\Shop;
use App\Models\Evaluation;
use App\Models\Reservation;

class MypageController extends Controller
{    
    public function index(){
        // 現在認証されているユーザーを取得
        $user = Auth::user();
        $userId = Auth::id();
        $profile = User::where('id', $userId)->first();
        if(!isset($profile)){
            $profile = ['name' => ''];
        }

        // このユーザーがお気に入り登録している店舗を取得
        $favoriteShopIds = Evaluation::query() 
            -> where('user_id', '=', $userId)
            -> where('favorite', '=', true)
            -> get();

        $shops = null;
        if (count($favoriteShopIds)>0) {
            $query_shops = Shop::query() -> where('id', '=', $favoriteShopIds[0]['shop_id']);
            foreach($favoriteShopIds as $favoriteShopId) {
                $query_shops -> orWhere('id', '=', $favoriteShopId['shop_id']);
            }
            $shops = $query_shops -> get();
        }

        // このユーザーの本日以降の予約一覧を取得
        $today = date('Y-m-d H:i:s', mktime(0,0,0));
        $reservations = Reservation::query()
            -> where('user_id', '=', $userId)
            -> where('booked_datetime', '>=', $today)   // 今日以降の予約のみ選択(過去分は表示しない)
            -> get();
        $reservations = $reservations -> sortBy('booked_datetime');   // 予約日時順に並べかえ

        // このユーザーの過去の予約履歴を取得
        $reservation_histories = Reservation::query()
            -> where('user_id', '=', $userId)
            -> where('booked_datetime', '<', $today)   // 今日以降の予約のみ選択(過去分は表示しない)
            -> get();
        $reservation_histories = $reservation_histories -> sortByDesc('booked_datetime');   // 予約日時の降順に並べかえ

        $evaluations = Evaluation::query()
            -> where('user_id', '=', $userId)
            -> get();
        foreach($reservation_histories as $reservation_history) {
            $targetShopId = $reservation_history->shop_id;
            $targetEvaluation = $evaluations->where('shop_id', $targetShopId) -> first();
            $reservation_history['evaluaiton_id'] = $targetEvaluation['id'];
            $reservation_history['evaluaiton_favorite'] = $targetEvaluation['favorite'];
            $reservation_history['evaluation_score'] = $targetEvaluation['score'];
            $reservation_history['evaluaiton_comment'] = $targetEvaluation['comment'];

        }

        return view('mypage', compact('shops', 'profile', 'reservations', 'reservation_histories'));
    }

    public function favorite(Request $request) {

        if (isset($_POST['cancel'])){
            return redirect('/mypage');
        }

        $shopId = $request -> shop_id;
        $userId = 1;  //仮
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



        // return redirect('/mypage');
        return back();
    }

}
