<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Shop;
use App\Models\Reservation;
use App\Models\Course;
use App\Models\ReservedCourse;
use DateTime; 

class ReservationListController extends Controller
{
     // 店舗代表者向け　店舗ごとの予約状況のリスト
     public function index(Request $request){
        // 現在認証されているユーザーを取得
        $user = Auth::user();
        $userId = Auth::id();
        $profile = User::find($userId);
        if(!isset($profile)){
            $profile = ['name' => ''];
        }

        $shop_id = $request->shop_id;
        $shop = Shop::find($shop_id);

        $today = date('Y-m-d H:i:s', mktime(0,0,0));
        $reservations = Reservation::with('user')
            -> where('shop_id', '=', $shop_id)
            -> whereDate('booked_datetime', '>=', $today)   // 今日以降の予約のみ選択(過去分は表示しない)
            -> get();
        $reservations = $reservations -> sortBy('booked_datetime');   // 予約日時順に並べかえ

        // dd($shop_id , $reservations);

        // コースメニューの予約有無確認
        foreach($reservations as $reservation) {
            $reservationId = $reservation->id;
            $ret = ReservedCourse::where('reservation_id', $reservationId) -> first();
            $exists_reserved_course = false;
            if (!(is_null($ret))) {
                $exists_reserved_course = true;
            }
            $reservation['exists_reserved_course'] = $exists_reserved_course;
        }
        return view('manager.reservation_list', compact('profile', 'shop', 'reservations'));
    }

    // 検索
    public function search(Request $request){
        // 現在認証されているユーザーを取得
        $user = Auth::user();
        $userId = Auth::id();
        $profile = User::find($userId);
        if(!isset($profile)){
            $profile = ['name' => ''];
        }

        $keyword = $request->keyword;
        $date = $request->date;
        $shop_id = $request->shop_id;
        $shop = Shop::find($shop_id);

        if (isset($_GET['reset'])){
            // 「リセット」ボタンが押された時はnullを入れておく。 KeywordSearch, DateSearchともにnullだとスルーしてくれる       
            $keyword = null;
            $date = null;
        }

        $today = date('Y-m-d H:i:s', mktime(0,0,0));
        $reservations = Reservation::with('user')
            -> where('shop_id', '=', $shop_id)
            -> whereDate('booked_datetime', '>=', $today)   // 今日以降の予約のみ選択(過去分は表示しない)
            -> KeywordSearch($keyword)  
            -> DateSearch($date)
            -> get();
        $reservations = $reservations -> sortBy('booked_datetime');   // 予約日時順に並べかえ
        
        return view('manager.reservation_list', compact('profile', 'shop', 'reservations'));
    }
}
