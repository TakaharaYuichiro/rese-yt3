<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Shop;
use App\Models\Manager;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Mail;                   
use App\Mail\GroupNotificationMail;
use App\Consts\CommonConst;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class AdminController extends Controller
{
    const PER_PAGE = 10;

    public function index(){
        // 現在認証されているユーザーを取得
        $user = Auth::user();
        $userId = Auth::id();
        $profile = User::find($userId);
        if(!isset($profile)){
            $profile = ['name' => ''];
        }
        
        $shop_dict = $this->getShopDict();
        $origUsers = User::all();
        $users1 = $this->getUsersContainingManagers($origUsers, $shop_dict);
        $users = $this->paginate($users1, self::PER_PAGE, null, ['path'=>'/admin?']);

        return view('/admin/admin', compact('profile', 'users', 'shop_dict'));
    }

    private function paginate($items, $perPage = 5, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    public function sendGroupMail(Request $request) 
    {
        // どの権限のユーザーにメールを送信するのかを入力フォーム側で設定できる。ここでは各ユーザーの権限に応じて宛先に入れるかどうかを決める
        $isSendingMails = $request->is_sending_mail;
        $query = User::query();
        foreach($request->is_sending_mail as $roleIndex => $value) {
            // valueは"true", "false"のように文字列で返ってくるのでboolに変換する
            $isSending = filter_var($value, FILTER_VALIDATE_BOOLEAN);
            if($isSending ) {
                $query->orWhere('role', $roleIndex);
            }
        }
        $users = $query -> get();

        // 宛先のリストの作成
        $emails = array();
        foreach($users as $user){
            $emails[] =  $user->email;
        }

        // 宛先全員にメールを送信
        $mailContent = [
            'content' => $request->mail_content,
        ];
        Mail::bcc($emails)->send(new GroupNotificationMail($request->subject, $mailContent));
        
        $message = 'お知らせメールを送信しました。';
        return back()->with('message', $message);
    }

    // 登録されているすべての店舗のデータを取得し、辞書形式に変換して返す
    private function getShopDict(){
        $shop_dict = array();
        $shops = Shop::all();
        foreach($shops as $shop){
            $shop_dict[$shop->id] = $shop->name;
        }
        return $shop_dict;
    }

    // データベースのuserデータ(origUsers)をベースにして、店舗代表者が担当する店舗の情報のコレクションを入れ込んだ新たなコレクション(users)を作る
    private function getUsersContainingManagers($origUsers, $shop_dict){
        $managers = Manager::select(["user_id",'shop_id'])->get();
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

    // 検索
    public function search(Request $request){
        // 現在認証されているユーザーを取得
        $user = Auth::user();
        $userId = Auth::id();
        $profile = User::find($userId);
        if(!isset($profile)){
            $profile = ['name' => ''];
        }

        $shop_dict = $this->getShopDict();

        if (isset($_GET['reset'])){
            // 「リセット」ボタンが押された時の処理            
            $origUsers = User::all();
            $users1 = $this->getUsersContainingManagers($origUsers, $shop_dict);
            $users = $this->paginate($users1, self::PER_PAGE, null, ['path'=>'/admin?']);
            return view('/admin/admin', compact('profile', 'users', 'shop_dict'));
        }
        else
        {
            // 「検索」ボタンが押された時の処理
            $origUsers = User::query()
                ->KeywordSearch($request->keyword)  
                ->RoleSearch($request->role)
                ->get();

            $users1 = $this->getUsersContainingManagers($origUsers, $shop_dict);
            $users = $this->paginate($users1, self::PER_PAGE, null, ['path'=>'/admin/search?']);
            return view('/admin/admin', compact('profile', 'users', 'shop_dict'));
        }
    } 
}
