<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Shop;
use App\Models\Course;
use App\Models\Manager;
use App\Models\Reservation;
use App\Http\Requests\EditShopRequest;

use Illuminate\Support\Facades\Storage;

class ShopEditorController extends Controller
{
    public function index(Request $request) {
        $shop_id = $request->shop_id;
        $shop = Shop::find($shop_id);

        $image_storage = config('const.image_storage');
        $disk = Storage::disk($image_storage);
        $exists_image_file = $disk->exists('shop_imgs/'. $shop->image_filename);
        
        if(!$exists_image_file){
            $shop['image_filename']='';
        }

        $courses = Course::query() 
            -> where('shop_id', $shop_id)
            -> get();

        // 現在認証されているユーザーを取得
        $user = Auth::user();
        $id = Auth::id();

        $profile = User::find($id);
        if(!isset($profile)){
            $profile = ['name' => '', 'id' => 0];
        }

        $is_new = 0;

        return view('manager.shop_editor', Compact('is_new', 'shop', 'courses',  'profile'));
    }

    public function newEntry() {
        $shop = [
            "id" => 0,
            'name'=>'',
            'area_index'=>'13', 
            'genre_id'=>0, 
            'content'=>'', 
            'image_filename'=>'',
        ];
        $courses = [];

        // 現在認証されているユーザーを取得
        $user = Auth::user();
        $id = Auth::id();
        $profile = User::find($id);
        if(!isset($profile)){
            $profile = ['name' => '', 'id' => 0];
        }
        
        $is_new = 1;

        return view('manager.shop_editor', Compact('is_new', 'shop', 'courses', 'profile'));
    }

    // ①新規店舗情報作成と、②既存の店舗情報更新の両用
    public function store(EditShopRequest $request){
        // 現在認証されているユーザーを取得
        $userId = Auth::id();

        // Laravel – アップロードファイルの取得
        // http://taustation.com/laravel-acquiring-uploaded-file/
        $image = $request->file('new_image_file');
        $imageFilePathName = '';
        if($image){
            // 新しい画像がアップロードされていればStorageに書き込む
            $hashName = $image->hashName();
            $dirName = 'additional_img';
            $imageFilePathName = $dirName. '/'. $hashName;
            
            $image_storage = config('const.image_storage');
            $disk = Storage::disk($image_storage);
            $disk->putFile('shop_imgs/'. $dirName, $image, 'public');
        } 
       
        if($request->is_new) {
            // ①新規作成
            $shopData = [
                "name" => $request->name,
                "area_index" => $request->area_index,
                "genre_id" =>  $request->genre_id,
                "content" => $request->detail,
                "image_filename" => $imageFilePathName
            ];
            $newShop = Shop::create($shopData);

            // 店舗代表者が店舗情報を新規登録したときはManagerテーブルにこの店舗の店舗代表者として登録(つまり新規登録の時は管理者を通さなくても店舗管理者登録できる)
            $managerData = [
                'user_id' => $userId, 
                'shop_id' => $newShop->id, 
            ];
            Manager::create($managerData);

            return redirect('manager_page') -> with('message', '新規店舗情報を登録しました');
        }
        else
        {
            // ②既存のデータ更新
            $targetShop = Shop::find($request->shop_id);
            if($imageFilePathName == ''){
                // 画像がアップロードされていないときはデータベース登録済みの画像ファイルパスを取得
                $imageFilePathName = $targetShop->image_filename;
            }

            $shopData = [
                "name" => $request->name,
                "area_index" => $request->area_index,
                "genre_id" =>  $request->genre_id,
                "content" => $request->detail,
                "image_filename" => $imageFilePathName
            ];
            $targetShop -> update($shopData);

            return back()->withInput()-> with('message', 'データを更新しました。');
        }
    }

    public function break(Request $request) {
        if (isset($_POST['cancel'])){
            return back();
        }
        else {
            return redirect(route('manager_page'));
        }
    }

    public function remove(Request $request) {
        if (isset($_POST['cancel'])){
            return back();
        }

        $shopId = $request -> shop_id;
        $targetShop = Shop::find($shopId);
        $shopName = $targetShop -> name;

        // このコースメニューが予約されたことがあるかを確かめる
        $isReserved = $this->isReserved($shopId);

        // 予約されたことがなければこの店舗のデータをすべて削除。過去を含め予約されたことがあれば記録のため削除できない仕様とする
        if($isReserved) {
            return back()->with('message', '「'.$shopName.'」は予約されているため削除できません');
        } else {
            $targetShop->delete();
            return redirect(route('manager_page'))->with('message', '「'.$shopName.'」を削除しました');
        }      
    }

    private function isReserved($shopId) {
        // このコースメニューが予約されたことがあるかを確かめる
        $reservation = Reservation::where('shop_id', $shopId) -> get();
        return (count($reservation) > 0);
    }
}
