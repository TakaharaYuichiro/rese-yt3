@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/shop_editor.css') }}"/>
    <link rel="stylesheet" href="{{ asset('css/common/admin_menu.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/common/header.css') }}" />
@endsection

@section('content')
<?php
    $prefCodes = App\Consts\CommonConst::PREF_CODE;
    $genres = App\Models\Genre::get();
?>

<div class="main-container">
    @include('layouts.header', ['pageTitle'=>($is_new)? '新規店舗作成': '店舗情報編集'])

    <div class="message-container">
        @if(session('message'))
            <div class="message-container--success">{{session('message')}}</div>
        @endif

        @if($errors->any())
            <div class="message-container--danger">
                <ul >
                    @foreach ($errors->all() as $error)
                        <li >{{$error}}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
 
    <form class="editor-form" action="/shop_editor/store" method="post" enctype="multipart/form-data">
        @csrf
        <table class="shop-info-table">
            <tr>
                <th>店名</th>
                <td><input type="text" name="name" placeholder="店名" value="{{ old('name', $shop['name']) }}"></td>
            </tr>
            <tr>
                <th>画像</th>
                <td>
                    <div class="shop-img__container">
                        @if($shop['image_filename']!="")
                            <?php
                                $image_storage = config('const.image_storage');
                                $disk = Storage::disk($image_storage);
                                $img_obj = null;
                                $file_name = 'shop_imgs/'. $shop['image_filename'];
                                
                                if ($disk->exists($file_name)) {
                                    $img_obj = $disk -> url($file_name);
                                } 
                            ?>
                            <img src="{{ $img_obj }}" id="img_prv">
                        @else
                            <img src="{{asset('storage/shop_imgs/test_img/noimage.png')}}" id="img_prv">
                        @endif                        
                        <div class="shop-img__file-select__container">
                            <span>画像を変更:</span>
                            <input name="new_image_file" type="file" accept="image/png, image/jpeg" onChange="changeImage(event)"/>
                        </div>                        
                    </div>
                </td>

            </tr>
            <tr>
                <th>地域</th>
                <td>
                    <select class="submit_item" name="area_index">
                        @foreach($prefCodes as $key => $val)
                            <option value="{{$key}}" @if(old('area_index', $shop['area_index']) == $key) selected @endif>{{$val}}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <th>ジャンル</th>
                <td>
                    <select class="submit_item" name="genre_id" >
                        @foreach($genres as $genre)
                            <option value="{{$genre['id']}}" @if(old('genre_id', $shop['genre_id'] ?? '') == $genre['id']) selected @endif>{{$genre['genre']}}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <th>説明</th>
                <td><textarea name="detail" placeholder="説明">{{ old('detail', $shop['content'])}}</textarea></td>
            </tr>
            <tr>
                <th>コースメニュー</th>
                <td>
                    <div class="course-info">
                        @if ($is_new)
                            <div>コースメニューは店舗登録後に編集することができます</div>
                        @else
                            @if (count($courses)>0)
                                <ul class="course-info">
                                    @foreach($courses as $course) 
                                        <li>{{$course['name']. ' ('. number_format($course['price']). '円)'}}</li>
                                    @endforeach
                                </ul>
                            @else
                                <div>登録されているコースメニューはありません</div>
                            @endif
                        @endif
                        <button type="button" class="course-detail-button" id="course-detail-button" {{ $is_new? 'disabled': '' }}>コースメニュー詳細</button>
                    </div>
                </td>
            </tr>
        </table>

        <input type="hidden" name="is_new" value="{{$is_new}}">
        <input type="hidden" name="shop_id" value="{{$shop['id']}}">
        <div class="form-submit-button__container">
            <button type="submit">{{($is_new)? "この内容で新規登録": "店舗情報を更新"}}</button>
        </div>
    </form>

    <div class="extra-buttons-container">
        <form class="extra-form" action="/shop_editor/break" method="post" id="form-break">
            @csrf
            <button class="extra-button" type="submit" id="form-break--submit-button">店舗代表者ページに戻る</button>
        </form>
        <form class="extra-form" action="/shop_editor/remove" method="post" id="form-remove">
            @csrf
            <input type="hidden" name="shop_id" value="{{$shop['id']}}">
            <button class="extra-button" type="submit" id="form-remove--submit-button" {{$is_new? 'disabled': ''}} >この店舗を削除</button>
        </form>
    </div>
</div> 

<script>
    // ファイルダイアログで選択された画像ファイルをimg要素のsrcに書き込む
    // https://qiita.com/yoshiakidayo/items/c6b89e9adb873e81a62e
    function changeImage(event) {
        const reader = new FileReader();        
        reader.onload = function (event) {
            const imgPreview = document.getElementById('img_prv');
            imgPreview.src = event.target.result;
        }
        reader.readAsDataURL(event.target.files[0]);
    }

    const courseDetailButton =  document.getElementById('course-detail-button');
    courseDetailButton.addEventListener('click', ()=>{
        console.log("course-detail-button clicked");
        location.href='{{ route('course_list', ['shop_id'=>$shop['id']]) }}';
    });

    const formBreakSubmitButton = document.getElementById('form-break--submit-button');
    formBreakSubmitButton.addEventListener('click', ()=> {
        const answer = window.confirm("店舗代表者ページに戻りますか？（変更箇所がある場合は破棄されますので事前に更新してください）");
            if (answer) {
                formBreakSubmitButton.name='submit';
            }
            else {
                formBreakSubmitButton.name='cancel';
            }

            const formBreak = document.getElementById('form-break');
            formBreak.submit();
    });

    const formRemoveSubmitButton = document.getElementById('form-remove--submit-button');
    formRemoveSubmitButton.addEventListener('click', ()=> {
        const answer = window.confirm("この店舗を削除しますか？");
            if (answer) {
                formRemoveSubmitButton.name='submit';
            }
            else {
                formRemoveSubmitButton.name='cancel';
            }

            const formRemove = document.getElementById('form-remove');
            formRemove.submit();
    });
</script>
@endsection