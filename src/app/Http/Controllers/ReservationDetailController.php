<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Shop;
use App\Models\Reservation;
use App\Models\Course;
use App\Models\ReservedCourse;

class ReservationDetailController extends Controller
{
    public function index(Request $request){
        // 現在認証されているユーザーを取得
        $user = Auth::user();
        $userId = Auth::id();
        $profile = User::find($userId);
        if(!isset($profile)){
            $profile = ['name' => ''];
        }
        $reservation = Reservation::with('user', 'shop')->find($request->reservation_id);
        $reserved_courses = ReservedCourse::with('course')->where('reservation_id', $request->reservation_id) -> get();
        return view('manager.reservation_detail', compact('profile', 'reservation', 'reserved_courses'));
    }
}
