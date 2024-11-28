@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/shop_all.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/menu.css') }}" />
@endsection

@section('utilities')
  @include('layouts.utility')
@endsection

@section('content')

<div class="form-content">
    <div class="form-header">
        <div class="form-header__1st-block">
            @include('layouts.menu')
        </div>
        
        <div class="form-header__2nd-block">
            <div class="user-name__container">
                <div class="user-name__content">
                    <span class="material-icons">person</span>
                    <span id="user-name__text">
                        {{$profile['name']==''? 'ログインしていません': $profile['name']. 'さん' }}
                    </span>
                </div>
            </div>
            <div class="search-section">       
                <form  class="search-form" id="submit_form" action="/search" method="post">
                    @csrf
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
        </div>
       
    </div>
    
    <div class="panel-section">
        @foreach($shops as $shop)
        <div class="panel-section__item">
            <img src="{{asset('storage/'. $shop['image_filename'])}}">
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

    // // ユーザー名を表示
    // const userNameTextElement = document.getElementById('user-name__text');
    // const userName1 = '<?php echo $profile['name']; ?>';
    // let userNameText = '';
    // if(userName1 === '') {
    //     userNameText = 'ログインしていません';
    // } else {
    //     userNameText = userName + 'さん';
    // }
    // userNameTextElement.innerHTML = userNameText;
</script>
@endsection

