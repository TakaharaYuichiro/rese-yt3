<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Exception\CardException;
use Stripe\Exception\InvalidRequestException;
use App\Models\User;
use App\Models\Shop;
use App\Models\Reservation;
use App\Models\Course;
use App\Models\ReservedCourse;
use Mail;                   
use App\Mail\ConfirmMail;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $reservationId = $request -> reservation_id;
        $reservedCourses = ReservedCourse::where('reservation_id', $reservationId) -> get();
        $totalPrice = 0;
        foreach($reservedCourses as $reservedCourses) {
            $priceAsOfReservation = $reservedCourses -> price_as_of_reservation;
            $quantity = $reservedCourses -> quantity;
            $totalPrice += $priceAsOfReservation * $quantity;
        }

        $reservation_data = [
            'id' => $reservationId,
            'total_price' => $totalPrice,
        ];

        return view('payment', compact('reservation_data'));
    }

    public function store(Request $request)
    {
        Stripe::setApiKey(config('stripe.stripe_secret_key'));
        $reservation = null;
        try {
            $charge = Charge::create([
                'source' => $request->stripeToken,
                'amount' => $request->total_price,
                'currency' => 'jpy',
            ]);

            $reservation = Reservation::find($request->reservation_id);
            $data = [
                'charge_id' => $charge->id, 
            ];
            $reservation->update($data);          
        
        } catch(CardException $e) {
            return view('/done')->with('message', '決済に失敗しました('. $e->getMessage() . ')');       
        } catch (InvalidRequestException $e) {
            return view('/done')->with('message', '決済に失敗しました('. $e->getMessage() . ')');
        } catch (Exception $e) {
            return view('/done')->with('message', '決済に失敗しました('. $e->getMessage() . ')');
        }

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
        $isPaymentCompleted = true;

        // QRコード
        $reservationModel = new Reservation();
        $qrCode = $reservationModel->createQrCode($reservation);

        // 予約内容をメールで送る
        $mailContent = [
            'user_name' => $reservation->user->name,
            'shop_name' => $reservation->shop->name,
            'datetime' => $reservation->booked_datetime,
            'people_counts' => $reservation->people_counts,
            'qrcode' => $qrCode,
            'course_content_html' => $courseContentHtml,
            'is_payment_completed' => $isPaymentCompleted,
            'total_price' => $totalPrice,
        ];

        // 現在認証されているユーザーを取得
        $user = Auth::user();
        $userId = Auth::id();
        $profile = User::find($userId);
        if(!isset($profile)){
            $profile = ['name' => ''];
        }

        Mail::to($profile['email'])->send(new ConfirmMail('【Rese】予約完了', $mailContent));

        return view('/done')->with('message', '決済が完了しました！');       
    }
}
