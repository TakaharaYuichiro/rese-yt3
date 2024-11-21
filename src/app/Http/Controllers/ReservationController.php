<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Consts\CommonConst;
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

        $shop = Shop::query() -> where('id', '=',  $request->shop_id) -> first();

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
        ];
        return view('/confirm_reservation', compact('reservation'));
    }

    public function index(){
        // $reservations = Reservation::all();

        // return view('/shop_detail', compact('reservations'));
    }

    public function store(Request $request){
        if (isset($_POST['cancel'])){
            // 「修正」ボタンが押された時の処理
            $shopId = -1;
            if (!empty($request->shop_id)) {
                $shopId = $request->shop_id;
            }
    
            $shop = Shop::query() -> where('id', '=', $shopId) -> first();
            $prefCode = $shop? CommonConst::PREF_CODE[$shop['area_index']]: '00';

            return view('shop_detail', compact('shop', 'prefCode'));
        }
        else
        {
            $dateStr = $request -> date;
            $timeStr = $request -> start_time. ':00';
            $datetimeStr = $dateStr. ' '. $timeStr;
            $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $datetimeStr);
    
            $data = [
                'shop_id' => $request->shop_id, 
                'booked_datetime' => $datetime, 
                'booked_minutes' => 60, 
                'people_counts' => $request->people_counts, 
                'user_id' => 1,
                'remarks' => 'test'
            ];
            Reservation::create($data);
    
            return view('/done');
        }
        
    }

    public function delete(Request $request){
        Reservation::find($request->id)->delete();
        return redirect('/')->with('message', 'Todoを削除しました');
    }

    public function update(Request $request){
        // $todo = $request->only(['content']);
        // Todo::find($request->id)->update($todo);

        // return redirect('/')->with('message', 'Todoを更新しました');
    }
}
