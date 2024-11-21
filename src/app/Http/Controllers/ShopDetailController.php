<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Consts\CommonConst;
use App\Models\Shop;
use App\Models\Genre;

class ShopDetailController extends Controller
{
    public function index(Request $request){
        $shopId = -1;
        if (!empty($request->shop_id)) {
            $shopId = $request->shop_id;
        }

        $shop = Shop::query() -> where('id', '=', $shopId) -> first();
        $prefCode = $shop? CommonConst::PREF_CODE[$shop['area_index']]: '00';

        return view('shop_detail', compact('shop', 'prefCode'));
    }
}
