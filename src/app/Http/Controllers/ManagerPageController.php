<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Shop;
use App\Models\Evaluation;
use App\Models\Reservation;
use App\Models\Manager;

class ManagerPageController extends Controller
{
    public function index() {
        // 現在認証されているユーザーを取得
        $user = Auth::user();
        $userId = Auth::id();
        $profile = User::find($userId);
        if(!isset($profile)){
            $profile = ['name' => ''];
        }

        $shop_dict = $this->getShopDict();

        // このユーザーが担当している店舗を取得
        $managementShopIds = Manager::query()
            -> where('user_id', '=', $userId)
            -> select('shop_id')
            -> get()
            -> toArray();

        $shops = Shop::all();
        $reservations = Reservation::all();

        $management_shops  = collect();
        foreach ($managementShopIds as $managementShopId) {
            $managementShop = $shops->where('id', $managementShopId['shop_id'])->first();

            $itemLists = [];
            if ($managementShop) {
                $itemLists['id'] = $managementShop['id'];
                $itemLists['name'] = $managementShop['name'];
            }

            $today = date('Y-m-d H:i:s', mktime(0,0,0));

            $targetShopReservations = $reservations
                ->where('shop_id', $managementShopId['shop_id'])
                ->where('booked_datetime', '>=', $today)
                ->all();
            $itemLists['reservations'] = $targetShopReservations;

            $management_shops->push($itemLists);
        }
        return view('manager.manager_page', compact('profile', 'management_shops'));
    }


     // 登録されているすべての店舗のデータを辞書として取得
     private function getShopDict(){
        $shop_dict = array();
        $shops = Shop::all();
        foreach($shops as $shop){
            $shop_dict[$shop->id] = $shop->name;
        }
        return $shop_dict;
    }

    private function getManagementShops($userId, $shop_dict){
        $managementShops = Manager::select(["user_id",'shop_id'])->get();
        $users = collect();
        foreach ($origUsers as $origUser) {
            $itemLists = [];
            $itemLists['id'] = $origUser['id'];
            $itemLists['name'] = $origUser['name'];
            $itemLists['email'] = $origUser['email'];
            $itemLists['role'] = $origUser['role'];

            $target_user_id = $origUser['id'];
            $target_managers = $managers->where('user_id', '=', $target_user_id);

            $shops = collect();
            foreach ($target_managers as $target_manager) {
                $shopLists = [];
                $shopLists['shop_id'] = $target_manager['shop_id'];
                $shopLists['shop_name'] = $shop_dict[$target_manager['shop_id']];
                $shops->push($shopLists);
            }
            $itemLists['shops'] = $shops;
            $users->push($itemLists);
        }

        return $users;
    }
}
