<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Consts\CommonConst;
use App\Models\User;
use App\Models\Shop;
use App\Models\Genre;

class ShopDetailController extends Controller
{
    public function index($shop_id){
        // 現在認証されているユーザーを取得
        $user = Auth::user();
        $userId = Auth::id();
        $profile = User::where('id', $userId)->first();
        if(!isset($profile)){
            $profile = ['name' => ''];
        }

        // 選択された店舗のデータを取得
        $shop = Shop::find($shop_id);

        // その他のデータ
        $prefCode = $shop? CommonConst::PREF_CODE[$shop['area_index']]: '00';
        $reservation = null;

        return view('shop_detail', compact('shop', 'prefCode', 'profile', 'reservation'));
    }
}
