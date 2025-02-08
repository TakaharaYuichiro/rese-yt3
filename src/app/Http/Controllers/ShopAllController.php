<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Shop;
use App\Models\Evaluation;

class ShopAllController extends Controller
{
    public function index(){
        // 現在認証されているユーザーを取得
        $user = Auth::user();
        $userId = Auth::id();
        $profile = User::find($userId);

        if(isset($profile)){
            if(!$profile->hasVerifiedEmail()) {
                // 会員登録したもののメール認証が済んでいない場合はメール認証を促す画面へ遷移
                return view('auth.verify-email-massage');
            }
        } else {
            $profile = ['name' => '', 'id' => 0];
        }
        
        $shops = Shop::get();

        return view('shop_all', compact('shops', 'profile'));
    }

    public function favorite(Request $request) {
        // 現在認証されているユーザーを取得
        $user = Auth::user();
        $userId = Auth::id();
        $profile = User::find($userId);
        if(!isset($profile)){
            // return redirect('/');
            return back();
        }

        $shopId = $request -> shop_id;

        $evaluation = Evaluation::query()
            -> where('user_id', '=', $userId)
            -> where('shop_id', '=', $shopId)
            -> first();

        if (!empty($evaluation)) {
            $updatedFavorite = !$evaluation['favorite'];
            $evaluation -> update(['favorite' => $updatedFavorite]);
        } else {
            $param = [
                'user_id' => $userId,
                'shop_id' => $shopId,
                'favorite' => true,
                'score' => 0,
                'comment' => '', 
            ];
            Evaluation::create($param);
        }
        return back();
    }

    public function search(Request $request){
        $selectedItems = [
            'keyword' => '',
            'areaIndex' => '00',
            'genreId' => '',
        ];

        if (!empty($request->area_index)) {
            $keyword = $request->keyword;
            $areaIndex = $request->area_index;
            $genreId = $request->genre_id;

            $selectedItems = [
                'keyword' => $keyword,
                'areaIndex' => $areaIndex,
                'genreId' => $genreId,
            ];

            $shops = Shop::query()
                -> keywordSearch($keyword)
                -> areaSearch($areaIndex)
                -> genreSearch($genreId)
                -> get();
        }
        
        // 現在認証されているユーザーを取得
        $user = Auth::user();
        $id = Auth::id();

        $profile = User::where('id', $id)->first();
        if(!isset($profile)){
            $profile = ['name' => '', 'id' => 0];
        }

        return view('shop_all', compact('shops', 'profile', 'selectedItems'));
    }
}
