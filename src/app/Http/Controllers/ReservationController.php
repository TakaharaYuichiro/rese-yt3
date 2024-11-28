<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Consts\CommonConst;
use App\Models\User;
use App\Models\Shop;
use App\Models\Reservation;
use DateTime;   // phpのDateTime関数を使うときのおまじない

class ReservationController extends Controller
{
    public function confirm(Request $request) {
        $dateStr = $request -> date;
        $timeStr = $request -> start_time. ':00';
        $datetimeStr = $dateStr. ' '. $timeStr;
        $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $datetimeStr);

        $shop = Shop::find($request->shop_id);

        $reservation = [
            'shop_id' => $request->shop_id, 
            'booked_datetime' => $datetime, 
            'booked_minutes' => 60, 
            'people_counts' => $request->people_counts, 
            'user_id' => 1,
            'remarks' => 'test',

            'shop_name' => $shop['name'],
            'user_name' => 'test user',
            'date' => $request->date,
            'start_time' => $request->start_time, 

            'exists_reservation_id' => $request->exists_reservation_id,
        ];
        return view('/confirm_reservation', compact('reservation'));
    }

    // ReservationデータベースのCreateとUpdate兼用のメソッド
    public function store(Request $request){
        if (isset($_POST['cancel'])){
            // 「修正」ボタンが押された時の処理
            // 現在認証されているユーザーを取得
            $user = Auth::user();
            $userId = Auth::id();
            $profile = User::where('id', $userId)->first();
            if(!isset($profile)){
                $profile = ['name' => ''];
            }

            // // 予約確認画面の前の画面に戻る時に必要になるので、予約内容を取得しておく（exists_reservation_id>0のとき。0ならnullを返す）
            // $reservation = Reservation::find($request->exists_reservation_id);
            // dd($reservation);

            // 新規予約か否か
            $is_new_reservation = $request->exists_reservation_id>0;

            // detail画面の予約入力欄で入力しかけていたデータを取得して返す
            $dateStr = $request -> date;
            $timeStr = $request -> start_time. ':00';
            $datetimeStr = $dateStr. ' '. $timeStr;
            $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $datetimeStr);

            $reservation = [
                "id" => $request->exists_reservation_id,
                "shop_id" => $request->shop_id, 
                "booked_datetime" => $datetimeStr,
                "people_counts" =>  $request->people_counts, 
                "user_id" => $userId
            ];

            // dd($reservation);



            // 選択された店舗のデータを取得
            $shopId = -1;
            if (!empty($request->shop_id)) {
                $shopId = $request->shop_id;
            }
            $shop = Shop::find($shopId);

            // その他のデータ
            $prefCode = $shop? CommonConst::PREF_CODE[$shop['area_index']]: '00';

            return view('shop_detail', compact('shop', 'prefCode', 'profile', 'reservation'));
        }

        // submitボタンが押されたときの処理
        $dateStr = $request -> date;
        $timeStr = $request -> start_time. ':00';
        $datetimeStr = $dateStr. ' '. $timeStr;
        $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $datetimeStr);

        if($request->exists_reservation_id==0) {
            // ①新規予約のとき
            $data = [
                'shop_id' => $request->shop_id, 
                'booked_datetime' => $datetime, 
                'booked_minutes' => 60, 
                'people_counts' => $request->people_counts, 
                'user_id' => 1,
                'remarks' => 'test'
            ];
            Reservation::create($data);
            return view('/done')->with('message', 'ご予約ありがとうございました');
        } else {
            // ②予約変更のとき
            $data = [
                'booked_datetime' => $datetime, 
                'booked_minutes' => 60, 
                'people_counts' => $request->people_counts, 
            ];
            Reservation::find($request->exists_reservation_id)->update($data);
            return view('/done')->with('message', '予約内容を変更しました');;
        }
    }
    
    public function delete(Request $request) {
        if (isset($_POST['cancel'])){
            // 予約を取り消すことをキャンセルする、という意味
            return redirect('/mypage');
        }

        // 予約取り消し処理
        Reservation::find($request->reservation_id)->delete();
        return view('/done')->with('message', '予約を取り消しました');;
    }
    // public function update(Request $request){
    //     // $todo = $request->only(['content']);
    //     // Todo::find($request->id)->update($todo);

    //     // return redirect('/')->with('message', 'Todoを更新しました');
    // }

    public function reservationChange(Request $request) {
        // 現在認証されているユーザーを取得
        $user = Auth::user();
        $userId = Auth::id();
        $profile = User::find($userId);
        if(!isset($profile)){
            $profile = ['name' => ''];
        }

        // 対象の予約内容を取得
        $reservation = Reservation::find($request -> reservation_id);

        // 選択された店舗のデータを取得
        $shop = Shop::find($request->shop_id);
        
        // その他のデータ
        $prefCode = $shop? CommonConst::PREF_CODE[$shop['area_index']]: '00';
        
        return view('shop_detail', compact('shop', 'prefCode', 'profile', 'reservation'));
    }

    



}
