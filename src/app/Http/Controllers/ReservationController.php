<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Consts\CommonConst;
use App\Models\User;
use App\Models\Shop;
use App\Models\Reservation;
use App\Models\Course;
use App\Models\ReservedCourse;
use DateTime;
use Mail;                   
use App\Mail\ConfirmMail;

class ReservationController extends Controller
{
    // ReservationデータベースのCreateとUpdate兼用のメソッド
    public function store(Request $request)
    {
        // 現在認証されているユーザーを取得
        $user = Auth::user();
        $userId = Auth::id();
        $profile = User::find($userId);
        if(!isset($profile)){
            $profile = ['name' => ''];
        }

        // 店舗情報取得
        $shopId = $request->shop_id;
        $shop = Shop::find($shopId);
        
        if (isset($_POST['cancel'])){
            // 「修正」ボタンが押された時の処理
            // 予約変更？新規予約？
            $is_current_reservation = ($request->exists_reservation_id > 0);
            if($is_current_reservation) {
                return redirect()
                    ->route('reservation_change', ['shop_id' => $request->shop_id, 'reservation_id' => $request->exists_reservation_id])
                    ->withInput();
            } else {
                return redirect()
                    ->route('detail', ['shop_id' => $request->shop_id])
                    ->withInput();
            }
        }

        // submitボタンが押されたときの処理・・・①Reservationデータベースに新規予約内容を登録/②既存の予約を更新
        // 日時の型変換
        $dateStr = $request -> date;
        $timeStr = $request -> start_time. ':00';
        $datetimeStr = $dateStr. ' '. $timeStr;
        $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $datetimeStr);

        // コースメニューの配列をjsonに変換  ***** 削除予定 *****
        $reservationModel = new Reservation();
    
        if($request->exists_reservation_id==0) {
            // ①新規予約のとき
            $data = [
                'shop_id' => $request->shop_id, 
                'booked_datetime' => $datetime, 
                'booked_minutes' => 60, 
                'people_counts' => $request->people_counts, 
                'user_id' => $userId,
                'remarks' => 'test'
            ];
            $reservation = Reservation::create($data);

            // 予約されたコースメニューの内容を整理（DB登録用、および、メールメッセージ用）
            $courses = Course::where('shop_id', $request->shop_id) -> get();   // 登録されている単価を調べるため、まずはコースメニュー一覧を取得
            $reservedCoursesForDb = array();
            $reservedCoursesForMail = array();
            $totalPrice = 0; // 予約したコースメニューの金額がゼロかどうかでこの後の処理を変えるため、ここで計算しておく
            if($request->courses) {
                foreach($request->courses as $requestedCourseId => $requestedCourseQuantity) {
                    $course = $courses -> find($requestedCourseId);
                    $reservedCourseForDb = [
                        'reservation_id' => $reservation->id,
                        'course_id' => $requestedCourseId,
                        'price_as_of_reservation' => $course -> price,
                        'quantity' => $requestedCourseQuantity,
                        'created_at' => new DateTime(),
                        'updated_at' => new DateTime(),
                    ];
                    $reservedCoursesForDb[] =  $reservedCourseForDb;

                    $reservedCourseForMail = [
                        'name' => $course -> name,
                        'price_as_of_reservation' => $course -> price,
                        'quantity' => $requestedCourseQuantity,
                    ];
                    $reservedCoursesForMail[] =  $reservedCourseForMail;

                    $totalPrice += ($course -> price) * $requestedCourseQuantity;
                }
            }
            // 予約されたコースメニューをreserved_coursesテーブルに保存
            ReservedCourse::insert($reservedCoursesForDb);

            // メール用のコースメニューの内容のテキスト
            $courseContentHtml = "";
            if(count($reservedCoursesForMail) == 0) {
                $courseContentHtml = "<p>コースメニューは予約されていません。</p>";
            } else {
                $courseContentHtml = "<p>コースメニュー予約内容: </p>";
                // dd($reservedCoursesForMail);
                foreach($reservedCoursesForMail as $reservedCourseForMail) {
                    $courseContentHtml .= "<span>". $reservedCourseForMail['name']. "(". $reservedCourseForMail['price_as_of_reservation']. "円) ×".  $reservedCourseForMail['quantity']. "</span><br>";
                }
                $courseContentHtml .= "<br>";
            }
            
            // 支払い済みのフラグ。新規なので常にfalse
            $isPaymentCompleted = false;

            // QRコード
            $reservationModel = new Reservation();
            $qrCode = $reservationModel->createQrCode($reservation);

            // 予約内容をメールで送る
            $mailContent = [
                'user_name' => $reservation->user->name,
                'shop_name' => $reservation->shop->name,
                'datetime' => $reservation->booked_datetime ->format('Y-m-d H:i'),
                'people_counts' => $reservation->people_counts,
                'qrcode' => $qrCode,
                'course_content_html' => $courseContentHtml,
                'is_payment_completed' => $isPaymentCompleted,
                'total_price' => $totalPrice,
            ];
            $subject = ($totalPrice>0)? '【Rese】予約受付（予約はまだ完了してません）': '【Rese】予約完了';
            Mail::to($profile['email'])->send(new ConfirmMail($subject, $mailContent));

            // 予約受付画面表示
            return redirect(route('reservation_accepted', ['reservation_id'=>$reservation->id]));

        } else {
            // ②予約変更のとき
            $data = [
                'booked_datetime' => $datetime, 
                'booked_minutes' => 60, 
                'people_counts' => $request->people_counts, 
            ];
            $reservation = Reservation::find($request->exists_reservation_id);
            $reservation->update($data);

            // 予約されたコースメニューの内容を取得
            $reservedCourses = ReservedCourse::with('course')->where('reservation_id', $reservation->id)->get();
            $totalPrice = 0;
            foreach($reservedCourses as $reservedCourse) {
                $totalPrice += ( $reservedCourse -> price_as_of_reservation) * ($reservedCourse->quantity);
            }

            // メール用のコースメニューの内容のテキスト
            $courseContentHtml = "";
            if(count($reservedCourses) == 0) {
                $courseContentHtml = "<p>コースメニューは予約されていません。</p>";
            } else {
                $courseContentHtml = "<p>コースメニュー予約内容: </p>";
                foreach($reservedCourses as $reservedCourse) {
                    $courseContentHtml .= "<span>". $reservedCourse['course']['name']. "(". $reservedCourse['price_as_of_reservation']. "円) ×".  $reservedCourse['quantity']. "</span><br>";
                }
                $courseContentHtml .= "<br>";
            }
            
            // 支払い済みのフラグ
            $isPaymentCompleted = !is_null($reservation->charge_id);

            // QRコード
            $reservationModel = new Reservation();
            $qrCode = $reservationModel->createQrCode($reservation);

            // 変更した予約内容をメールで送る
            $mailContent = [
                'reservation' => $reservation,
                'qrcode' => $qrCode,
            ];

            // 予約内容をメールで送る
            $mailContent = [
                'user_name' => $reservation->user->name,
                'shop_name' => $reservation->shop->name,
                'datetime' => $reservation->booked_datetime ->format('Y-m-d H:i'),
                'people_counts' => $reservation->people_counts,
                'qrcode' => $qrCode,
                'course_content_html' => $courseContentHtml,
                'is_payment_completed' => $isPaymentCompleted,
                'total_price' => $totalPrice,
            ];
            Mail::to($profile['email'])->send(new ConfirmMail('【Rese】予約内容を変更しました', $mailContent));

            return view('/done')->with('message', '予約内容を変更しました');
        }
    }

    public function delete(Request $request) 
    {
        if (isset($_POST['cancel'])){
            // キャンセル（予約を取り消すことをキャンセルする、という意味）
            return redirect('/mypage');
        }

        // 予約取り消し処理
        Reservation::find($request->reservation_id)->delete();
        return view('/done')->with('message', '予約を取り消しました');;
    }
}
