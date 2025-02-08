<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Shop;
use App\Models\Course;
use App\Models\ReservedCourse;
use App\Http\Requests\EditCourseRequest;

class CourseEditorController extends Controller
{
    public function index(Request $request) {
        $courseId = $request->course_id;;
        $course = Course::find($courseId); 

        $shopId = $course->shop_id;
        $shop = Shop::find($shopId);

        // 現在認証されているユーザーを取得
        $user = Auth::user();
        $id = Auth::id();

        $profile = User::find($id);
        if(!isset($profile)){
            $profile = ['name' => '', 'id' => 0];
        }

        $is_new = 0;
        return view('manager.course_editor', Compact('is_new', 'course', 'shop', 'profile'));
    }

    public function newEntry(Request $request) {
        $course = [
            "id" => 0,
            'name'=>'',
            'price'=>0,
            'detail'=>'', 
        ];

        $shopId = $request->shop_id;
        $shop = Shop::find($shopId);

        // 現在認証されているユーザーを取得
        $user = Auth::user();
        $id = Auth::id();
        $profile = User::find($id);
        if(!isset($profile)){
            $profile = ['name' => '', 'id' => 0];
        }
        
        $is_new = 1;

        return view('manager.course_editor', Compact('is_new', 'course', 'shop', 'profile'));
    }

    public function break(Request $request) {
        if (isset($_POST['cancel'])){
            return back();
        }
        else {
            return redirect(route('course_list', ['shop_id' => $request -> shop_id]));
        }
    }

    public function remove(Request $request) {
        if (isset($_POST['cancel'])){
            return back();
        }

        $courseId = $request->course_id;
        $targetCourse = Course::find($courseId);
        $shopId = $targetCourse -> shop_id;

        // このコースメニューが予約されたことがあるかを確かめる
        $isReserved = $this->isReserved($courseId);

        if($targetCourse) {
            $courseName = $targetCourse->name;

            // 予約されたことがなければこのコースメニューのデータをすべて削除。過去を含め予約されたことがあれば記録のため削除できない仕様とする
            if($isReserved) {
                return back()->with('message', '「'.$courseName.'」は予約されているため削除できません');
            } else {
                $targetCourse->delete();
                return redirect(route('course_list', ['shop_id' => $shopId]))->with('message', 'コースメニュー「'.$courseName.'」を削除しました');
            }
        } else {
            return redirect(route('course_list', ['shop_id' => $shopId]))->with('message', 'コースメニューが登録されていません');
        }        
    }

    public function store(EditCourseRequest $request) {
        $courseData = [
            'shop_id' => $request->shop_id,
            'name' => $request->name,
            'price' => $request->price,
            'detail' => $request->detail,
            'enable' => 1,
        ];

        if($request->is_new == 1) {
            // 新規コースメニュー作成のとき
            Course::create($courseData);;
            return redirect(route('course_list', [
                'shop_id' => $request->shop_id,
            ]))->with('message', 'コースメニューが追加されました'); ;
        } 
        else {
            // コースメニュー編集のとき
            $courseId = $request->course_id;
            $isReserved = $this->isReserved($courseId);
            // 予約されたことがなければ更新。過去を含め予約されたことがあれば更新できない仕様とする
            if($isReserved) {
                return back()->with('message', '「このコースメニューは予約されているため更新できません');
            } else {
                Course::find($courseId) -> update($courseData);
                return redirect(route('course_list', [
                    'shop_id' => $request->shop_id,
                ]))->with('message', 'コースメニューが更新されました'); ;
            }
        }
    }

    private function isReserved($courseId) {
        // このコースメニューが予約されたことがあるかを確かめる
        $reservedCourses = ReservedCourse::where('course_id', $courseId) -> get();
        return (count($reservedCourses) > 0);
    }

    public function availability(Request $request) {
        if (isset($_POST['cancel'])){
            return back();
        }

        $enable = $request->availability == 'prevent'? 0: 1;
        $targetCourse = Course::find($request->course_id);
        if($targetCourse) {
            $courseData = [
                'enable' => $enable ,
            ];
            $targetCourse -> update($courseData);
        }
        return back();
    }
}
