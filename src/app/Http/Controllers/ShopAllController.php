<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Consts\CommonConst;
use App\Models\Shop;
use App\Models\Genre;

class ShopAllController extends Controller
{
    public function index(){
        $prefCodes = CommonConst::PREF_CODE;
        $shops = Shop::get();
        $genres = Genre::get();

        return view('shop_all', compact( 'prefCodes', 'shops', 'genres'));
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
        // $shops = $query -> get();
        $prefCodes = CommonConst::PREF_CODE;
        $genres = Genre::get();


        return view('shop_all', compact( 'prefCodes', 'shops', 'genres', 'selectedItems'));

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
