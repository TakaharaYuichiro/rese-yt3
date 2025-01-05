<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Reservation;
use App\Models\ReservedCourse;

class ReservationAcceptedController extends Controller
{
    public function index(Request $request){
        // reservationIdの整合チェック
        $isUnmatch = false;
        $reservationId = $request->reservation_id;
        $reservation = Reservation::find($reservationId);

        if(is_null($reservation)) {
            // 要求されたreservationIdが存在しない
            $isUnmatch = true;
        } else {
            $user = Auth::user();
            $userId = Auth::id();
            if(($reservation->user_id) != $userId) {
                // reservation tableに登録されたuser_idと現在ログインしているユーザーのuserIdが一致しない');
                $isUnmatch = true;
            }
        }

        if ($isUnmatch == false) {
            // コースメニューの予約内容(予約金額)を取得
            $reservedCourses = ReservedCourse::with('course')->where('reservation_id', $request -> reservation_id) -> get();
            $totalPrice = 0;
            foreach($reservedCourses as $reservedCourse) {
                $totalPrice += $reservedCourse['price_as_of_reservation'] * $reservedCourse['quantity'];
            }

            // 予約受付画面表示
            $reservation_data = [
                'id' => $request->reservation_id,
                'total_price' =>  $totalPrice
            ];

            return view('reservation_accepted', compact('reservation_data'));
        }
        else {
            return back()->with('message', 'データに異常があります');
        }
    }
}
