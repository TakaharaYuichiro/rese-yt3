<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Manager;
use App\Models\Shop;
use Mail;        
use App\Mail\ChangeManagementShopsNotificationMail;

class ChangeManagerController extends Controller
{
    public function index(Request $request) 
    {
        $target_user = User::find($request->user_id);
        $managers = Manager::where('user_id', $target_user->id)->get();
        
        $shops = Shop::all();
        $shop_dict = [];
        foreach($shops as $shop) {
            $shop_dict[$shop['id']] = $shop['name'];
        }

        return view('admin/change_manager', compact('target_user', 'managers', 'shop_dict'));
    }

    // 「店舗代表者が管理する店舗」を更新する
    public function updateShops(Request $request) {
        if (isset($_POST['cancel'])){
            return redirect('/admin');
        }

        $targetUserId = $request->user_id;
        $user = User::find($targetUserId);

        // 設定変更メールに記載するために、更新前の担当店舗を覚えておく
        $previousManagementShopNames = Manager::with('shop')
            -> where('user_id', '=', $targetUserId)
            -> get()
            -> map(function ($previousManagement) {
                return $previousManagement->shop->name;
            })
            -> toArray();

        // 登録されているすべての店舗を取得
        $shops = Shop::all();

        // チェックされている店舗のみが入っている配列
        $checkedShopIds = $request->checked_shop_ids; 

        // 更新実行
        foreach($shops as $shop) {
            $targetShopId = $shop->id;
            $currentRowData = Manager::query()
                -> where('user_id', '=', $targetUserId)
                -> where('shop_id', '=', $targetShopId)
                -> first();

            $isManagementShop = false;
            if (!is_null($checkedShopIds)) {
                $isManagementShop =array_key_exists($targetShopId, $checkedShopIds);
            }
            
            // Managerデータベースにこのuser_idとshop_idの組み合わせのレコードが存在する時
            // -> isManagementShopがtrueならなにもしない
            // -> isManagementShopがfalseならその行を削除
            // データベースにuser_idとshop_idの組み合わせがない時
            // -> isManagementShopがtrueならcreateでデータを作成
            // -> isManagementShopがfalseならなにもしない
            if($currentRowData) {
                if(!$isManagementShop){
                    $data = [
                        'user_id' => $targetUserId,
                        'shop_id' => $targetShopId,
                    ];
                    $currentRowData->delete();
                }
            } else {
                if($isManagementShop){
                    $data = [
                        'user_id' => $targetUserId,
                        'shop_id' => $targetShopId,
                    ];
                    Manager::create($data);
                }
            }
        }

        $message = $user['name'].'さんの担当店舗を更新しました。';

        if (isset($request->is_mail_sending)){
            // 更新後の担当店舗
            $newManagementShopNames = Manager::with('shop')
                -> where('user_id', '=', $targetUserId)
                -> get()
                -> map(function ($management) {
                    return $management->shop->name;
                })
                -> toArray();

            // 追加された店舗
            $addedShops = array_diff($newManagementShopNames, $previousManagementShopNames);

            // 削除された店舗
            $removedShops = array_diff($previousManagementShopNames, $newManagementShopNames);

            // メール送信
            $mailContent = [
                'name' => $user['name'],
                'after' =>  $newManagementShopNames,
                'added' => $addedShops,
                'removed' => $removedShops,
            ];
            Mail::to($user['email'])
                ->send(new ChangeManagementShopsNotificationMail($mailContent));
            $message .= 'また、'.$user['name'].'さんにメールを送信しました。';
        }
        return redirect('/admin')->with('message', $message);
    }
}
