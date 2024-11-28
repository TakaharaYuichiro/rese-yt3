<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Consts\CommonConst;
use App\Models\User;
use App\Models\Shop;
use App\Models\Genre;
use App\Models\Evaluation;

class ShopAllController extends Controller
{
    public function index(){
        // 現在認証されているユーザーを取得
        $user = Auth::user();
        $id = Auth::id();

        $profile = User::where('id', $id)->first();
        if(!isset($profile)){
            $profile = ['name' => '', 'id' => 0];
        }
        
        $prefCodes = CommonConst::PREF_CODE;
        $shops = Shop::get();
        $genres = Genre::get();

        // dd($shops);
        // dd($shops[0]['evaluation']);

        return view('shop_all', compact('prefCodes', 'shops', 'genres', 'profile'));
    }

    public function favorite(Request $request) {
        $shopId = $request -> shop_id;
        $userId = 1;  //仮
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



        // お気に入りデータ書き換え後、改めてデータ取得
        // $shops = Shop::get();
        // $prefCodes = CommonConst::PREF_CODE;
        // $genres = Genre::get();

        // dd($shops);
        // dd($shops[0]['evaluation']);

        // return view('shop_all', compact( 'prefCodes', 'shops', 'genres'));
        return redirect('/');
    }

    public function search(Request $request){
        // $query = Shop::query();

        // if($request->area_index == "00") dd($request);
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

            // $query->where('area_index', '=', $request->area_index);  
            // if($request->area_index == "00") $query = Shop::query();
        }
        
        // 現在認証されているユーザーを取得
        $user = Auth::user();
        $id = Auth::id();

        $profile = User::where('id', $id)->first();
        if(!isset($profile)){
            $profile = ['name' => '', 'id' => 0];
        }
        
        $prefCodes = CommonConst::PREF_CODE;
        $genres = Genre::get();


        // return view('shop_all', compact('prefCodes', 'shops', 'genres', 'profile'));
        return view('shop_all', compact('prefCodes', 'shops', 'genres', 'profile', 'selectedItems'));

    }

    // public function areaSearch($query, $area_index){
    //     if (!empty($area)) {
    //         // $query->where('gender', $gender);
    //         if ($area_index == "00"){

    //         } else {
    //             $query->where('area_index', '=', $area_index);  
    //         }
            
    //     }
    // }
}
