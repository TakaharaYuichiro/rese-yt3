<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ReservationRequest;
use App\Models\User;
use App\Models\Shop;
use App\Models\Course;
use App\Models\Reservation;
use App\Models\ReservedCourse;
use DateTime;   
use Stripe\Stripe;
use Stripe\Refund;

class ConfirmReservationController extends Controller
{
    public function index(ReservationRequest $request) 
    {
        if (isset($_POST['delete'])){
            
            if ($request->delete == 'true') {
                // 予約画面(shop_detail)で「削除」が押された時
                $targetReservation = Reservation::find($request->exists_reservation_id);

                // Stripeの返金処理(Refund)もここで実施する
                $flagRefundState = 0;   // 0:返金処理なし
                if(!is_null($targetReservation->charge_id)) {
                    try {
                        Stripe::setApiKey(config('stripe.stripe_secret_key'));
                        $refund = Refund::create([
                            'charge' => $targetReservation->charge_id,
                        ]); 
                        $flagRefundState = 1; // 1:返金成功
                    }
                    catch (Exception $e) {
                        $flagRefundState = -1; // -1:返金失敗
                    }
                }

                // 予約取り消し処理
                $message = "";
                if ($flagRefundState >= 0) {
                    // 返金の必要がない場合、もしくは、返金に成功した場合は予約取り消し処理実行
                    $targetReservation -> delete();
                    $message = "予約を取り消しました。";

                    if ($flagRefundState == 1) {
                        // 返金に成功した場合はメッセージ追加
                        $message .= "また、返金処理を実施しました。";
                    }
                } else {
                    // 返金処理に失敗した場合
                    $message = "返金処理に失敗しました。";
                }
                
                return view('/done')->with('message', $message);

            } else {
                // 予約画面で「削除」が押されたものの、javascriptダイアログでキャンセルされたとき
                return back() -> withInput();
            }
        }
        else {
            // deleteではないとき＝新規予約 or 予約変更の時
            // 現在認証されているユーザーを取得
            $user = Auth::user();
            $userId = Auth::id();
            $profile = User::find($userId);
            if(!isset($profile)){
                $profile = ['name' => ''];
            }

            // requestのshop_idに対応する店舗のデータを取得
            $shopId = $request->shop_id;
            $shop = Shop::find($shopId);

            // requestの日付と開始時刻を結合してdatetime型に変換
            $dateStr = $request -> date;
            $timeStr = $request -> start_time. ':00';
            $datetimeStr = $dateStr. ' '. $timeStr;
            $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $datetimeStr);

            // requestのコースメニューの内容をコレクションにする
            $reservedCourses = collect();
            $totalCost = 0;

            if ($request->exists_reservation_id == 0) {
                // 新規予約の時
                $courseIds = $request->course_id;
                if($courseIds) {
                    $courses = Course::where('shop_id', $shopId) -> get();
                    $courseQuantities = $request->quantity;
                    foreach($courseIds as $index=>$courseId) {
                        $courseQuantity = $courseQuantities[$index];
                        if($courseQuantity > 0) {
                            $course = $courses -> find($courseId);
                            $reservedCourse = [
                                'id' => $courseId,
                                'name' => $course -> name,
                                'price' => $course -> price,
                                'quantity' => $courseQuantity,
                            ];
                            $reservedCourses -> push($reservedCourse);
                            $totalCost += ($course -> price)*$courseQuantity;
                        }
                    }
                }
            } 
            else {
                // 予約更新の時
                $existReservedCourses = ReservedCourse::with('course') -> where('reservation_id', $request->exists_reservation_id)->get();
                foreach($existReservedCourses as $existReservedCourse) {
                    $reservedCourse = [
                        'id' => $existReservedCourse->course_id,
                        'name' => $existReservedCourse -> course -> name,
                        'price' => $existReservedCourse -> price_as_of_reservation,
                        'quantity' => $existReservedCourse -> quantity,
                    ];
                    $reservedCourses -> push($reservedCourse);
                    $totalCost += ($existReservedCourse -> price_as_of_reservation)*($existReservedCourse -> quantity);
                }
            }
        
            $reservation = [
                'shop_id' => $request->shop_id, 
                'booked_datetime' => $datetime, 
                'booked_minutes' => 60, 
                'people_counts' => $request->people_counts, 
                'user_id' => $userId,
                'remarks' => 'test',

                'shop_name' => $shop['name'],
                'user_name' => $user['name'],
                'date' => $request->date,
                'start_time' => $request->start_time, 

                'exists_reservation_id' => $request->exists_reservation_id,

                'reserved_courses' => $reservedCourses,
                'total_cost' => $totalCost,
            ];

            return view('/confirm_reservation', compact('reservation'));
        }   
    }
}
