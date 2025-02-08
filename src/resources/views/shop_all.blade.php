@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/shop_all.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/menu.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/common/header.css') }}" />
@endsection

@section('content')
<?php
    $prefCodes = App\Consts\CommonConst::PREF_CODE;
    $genres = App\Models\Genre::get();
?>

<div class="main-container">
    @include('layouts.header', ['pageTitle'=>''])

    <div class="search-container">       
        <form  class="search-form" id="submit_form" action="/search" method="get">
            <div class="search-item search-item--category">
                <select class="submit_item" name="area_index">
                    {{-- selectの値を保持する。??はnull合体演算子  https://qiita.com/yoshitaro-yoyo/items/28fbe9a6dc84d9cada03 --}}
                    <option value="00" selected @if(old('area_index', $selectedItems['areaIndex'] ?? '') == '00') selected @endif>{{old('area_index')}} All area</option>
                    @foreach($prefCodes as $key => $val)
                        <option value="{{$key}}" @if(old('area_index', $selectedItems['areaIndex'] ?? '') == $key) selected @endif>{{$val}}</option>
                    @endforeach
                </select>
            </div>

            <div class="search-item search-item--category">
                <select class="submit_item" name="genre_id" >
                    <option value="" selected @if(old('genre_id', $selectedItems['genreId'] ?? '') == "") selected @endif>{{old('genre_id')}} All genre</option>
                    @foreach($genres as $genre)
                        <option value="{{$genre['id']}}" @if(old('genre_id', $selectedItems['genreId'] ?? '') == $genre['id']) selected @endif>{{$genre['genre']}}</option>
                    @endforeach
                </select>
            </div>

            <div class="search-item search-item--keyword">
                <span class="material-icons">search</span>
                <input type="text" name="keyword" placeholder="Search ..." value="{{old('keyword', $selectedItems['keyword'] ?? '')}}"/>
            </div>
        </form>
    </div>
    
    <div class="panel-section">
        @empty($shops)
            <div class="empty-message">店舗情報を取得できませんでした</div>
        @endempty

        <?php 
            $image_storage = config('const.image_storage');
            $disk = Storage::disk($image_storage);
        ?>

        @foreach($shops as $shop)
            <div class="panel-section__item">
                @if($shop['image_filename']!="")
                    <?php
                        $img_obj = null;
                        $file_name = 'shop_imgs/'. $shop['image_filename'];
                        if ($disk->exists($file_name)) {
                            $img_obj = $disk -> url($file_name);
                        } 
                    ?>
                    <img src="{{ $img_obj }}">
                @else
                    <img src="{{asset('storage/shop_imgs/test_img/noimage.png')}}">
                @endif

                <div class="panel-section__item--content">
                    <div class="panel-section__item--name">{{$shop['name']}}</div>
                    <div class="panel-section__item--tag">
                        <span>{{'#'. $shop->getPrefName(); }}</span>
                        <span>{{'#'. $shop['genre']['genre']; }}</span>
                    </div>
                    <div class="panel-section__item--button">
                        <form action="{{ route('detail',['shop_id' => $shop->id ]) }}" method="get">
                            <button class="panel-section__item--button--detail" type="submit">詳しくみる</button>
                        </form>

                        <form action="/favorite" method="post">
                            @csrf
                            <input type="hidden" name="shop_id" value="{{$shop['id']}}">
                            <button class="panel-section__item--button--favorite" type="submit">
                                @php
                                    $favorite = false;
                                    foreach($shop['evaluation'] as $evaluation){
                                        if ($evaluation['user_id'] == $profile['id']) $favorite = $evaluation['favorite']; 
                                    }
                                @endphp
                                <span class="material-icons" style="color: {{$favorite? 'red' :'lightgray';}}">
                                    favorite
                                </span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
    // セレクトボックスを選択後、自動でサブミット送信する
    const submitItems = document.getElementsByClassName('submit_item');
    const submitForm = document.getElementById('submit_form');
    for(let item of submitItems) {
        item.addEventListener("change", () => {
            submitForm.submit();
        })
    }
</script>
@endsection

