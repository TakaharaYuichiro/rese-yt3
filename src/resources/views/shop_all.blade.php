@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/index.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/menu.css') }}" />
@endsection

@section('utilities')
  @include('layouts.utility')
@endsection

@section('content')

<div class="form-content">
    <div class="form-header">

            @include('layouts.menu')


        {{-- 
        <div class="form-header__menu" id="form-header__menu">
            <input type="checkbox" id="menu-btn">
            <label for="menu-btn" class="form-header__menu--humberger" >
                <div class="humberger-line"></div>
                <div class="humberger-line"></div>
                <div class="humberger-line"></div>
            </label>
            
            <div class="form-header__menu--text">Rese</div>

            <ul class="menu1" id="menu1">
                <li><a href="/">Home</a></li>
                <li><a href="/register">Registration</a></li>
                <li><a href="/login">Login</a></li>
            </ul>
        </div>
        --}}

        <div class="search-section">        
            <form  class="search-form" id="submit_form" action="/search" method="post">
                @csrf
                <select class="submit_item search-form__item search-form__item--category" name="area_index">
                    {{-- selectの値を保持する。??はnull合体演算子  https://qiita.com/yoshitaro-yoyo/items/28fbe9a6dc84d9cada03 --}}
                    <option value="00" selected @if(old('area_index', $selectedItems['areaIndex'] ?? '') == '00') selected @endif>{{old('area_index')}} All area</option>
                    @foreach($prefCodes as $key => $val)
                        <option value="{{$key}}" @if(old('area_index', $selectedItems['areaIndex'] ?? '') == $key) selected @endif>{{$val}}</option>
                    @endforeach
                </select>

                <select class="submit_item search-form__item search-form__item--category" name="genre_id" >
                    <option value="" selected @if(old('genre_id', $selectedItems['genreId'] ?? '') == "") selected @endif>{{old('genre_id')}} All genre</option>
                    @foreach($genres as $genre)
                        <option value="{{$genre['id']}}" @if(old('genre_id', $selectedItems['genreId'] ?? '') == $genre['id']) selected @endif>{{$genre['genre']}}</option>
                    @endforeach
                </select>

                <div class="search-form__item search-form__item--keyword search-form__item--search">
                    <span class="material-icons">search</span>
                    <input type="text" name="keyword" placeholder="Search ..." value="{{old('keyword', $selectedItems['keyword'] ?? '')}}"/>
                </div>
            </form>
        </div>
    </div>
    
    <div class="form-grid">
        @foreach($shops as $shop)
        <div class="form-grid__item">
            <img src="{{asset('storage/'. $shop['image_filename'])}}">
            <div class="form-grid__item--content">
                <div class="form-grid__item--name">{{$shop['name']}}</div>
                <div class="form-grid__item--tag">
                    <span>{{'#'. $prefCodes[$shop['area_index']]}}</span>
                    <span>{{'#'. $shop['genre']['genre']}}</span>
                </div>
                <div class="form-grid__item--button">
                    <form action="/detail" method="post">
                        @csrf
                        <input type="hidden" name="shop_id" value="{{$shop['id']}}">
                        <button class="form-grid__item--button--detail" type="submit">詳しくみる</button>
                    </form>
                    <div class="icon"><span class="material-icons">favorite</span></div>
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

    // メニューが開いている時に、メニュー領域の外側をクリックしたとき、メニューを閉じる
    // const menuBtn = document.getElementById('menu-btn');
    // document.addEventListener('click', (e) => {
    //     if(!e.target.closest('#form-header__menu')) {
    //         if(menuBtn.checked) {
    //             menuBtn.checked = false;
    //         }
    //     } 
    // })
</script>
@endsection

