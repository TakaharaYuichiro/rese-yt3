<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Shop;
use App\Models\Course;
use App\Models\Manager;
use App\Models\Reservation;
use App\Models\ReservedCourse;
use App\Http\Requests\EditCourseRequest;

class CourseListController extends Controller
{
    public function index(Request $request) {
        $shop_id = $request->shop_id;
        $shop = Shop::find($shop_id);

        $courses = Course::query() 
            -> where('shop_id', $shop_id)
            -> get();

        // 現在認証されているユーザーを取得
        $user = Auth::user();
        $id = Auth::id();

        $profile = User::find($id);
        if(!isset($profile)){
            $profile = ['name' => '', 'id' => 0];
        }

        return view('manager.course_list', Compact('shop', 'courses',  'profile'));
    }
}
