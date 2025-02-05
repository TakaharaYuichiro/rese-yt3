<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Consts\CommonConst;
use App\Models\Evaluation;
use App\Models\User;
use App\Models\Shop;
use App\Models\Reservation;
use DateTime;

class EvaluationController extends Controller
{
    public function index(Request $request){
        // 現在認証されているユーザーを取得
        $user = Auth::user();
        $userId = Auth::id();
        $profile = User::find($userId);
        if(!isset($profile)){
            $profile = ['name' => ''];
        }

        // 選択された店舗のデータを取得
        $shopId = $request->shop_id;
        $shop = Shop::find($shopId);

        // 登録済みの評価データを取得
        $evaluation = Evaluation::where('user_id', $userId)
            -> where('shop_id', $shopId)
            -> first();
        
        if (is_null($evaluation)) {
            $param = [
                'user_id' => $userId,
                'shop_id' => $shopId,
                'favorite' => false,
                'score' => 0,
                'comment' => '', 
                'created_at' =>  new DateTime(),
                'updated_at' =>  new DateTime(),
            ];
            $evaluation = Evaluation::create($param);
        }

        // 過去の評価結果がないことを示すフラグ。favariteがtrueだがその他の評価結果がない場合に対応
        $is_empty_evaluation = ($evaluation->score == 0)? true: false;

        // 過去の予約データ(来店済みデータとする)
        $today = date('Y-m-d H:i:s', mktime(0,0,0));
        $reservation_histories = Reservation::query()
            -> where('user_id', '=', $userId)
            -> where('shop_id', '=', $shopId)
            -> where('booked_datetime', '<', $today)   
            -> get();
        $reservation_histories = $reservation_histories -> sortByDesc('booked_datetime');   // 予約日時の降順に並べかえ

        // その他のデータ
        $prefCode = $shop? CommonConst::PREF_CODE[$shop['area_index']]: '00';
        
        return view('evaluation', compact('shop', 'prefCode', 'profile', 'reservation_histories', 'evaluation', 'is_empty_evaluation'));
    }

    public function store(Request $request) {
        $data = [
            'score' => $request->evaluation_score, 
            'comment' => $request->evaluation_comment, 
        ];
        Evaluation::find($request->evaluation_id)->update($data);
        return view('/done')->with('message', '評価ありがとうございました。');
    }
}
