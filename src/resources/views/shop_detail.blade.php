@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/shop_detail.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/menu.css') }}" />
@endsection

@section('utilities')
  @include('layouts.utility')
@endsection

@section('content')

<div class="main-container">
    <div class="sub-container">
        <div class="content-header">
            @include('layouts.menu')

            {{-- 
            <div class="content-header__menu" id="content-header__menu">
                <input type="checkbox" id="menu-btn">
                <label for="menu-btn" class="content-header__menu--humberger" >
                    <div class="humberger-line"></div>
                    <div class="humberger-line"></div>
                    <div class="humberger-line"></div>
                </label>
                
                <div class="content-header__menu--text">Rese</div>

                <ul class="menu1" id="menu1">
                    <li><a href="/">Home</a></li>
                    <li><a href="/register">Registration</a></li>
                    <li><a href="/login">Login</a></li>
                </ul>
            </div>
            --}}
        </div>

        <div class="shop-detail">
            @isset($shop)
                <div class="shop-detail__name">
                    <button><</button>
                    <span>{{$shop['name']}}</span>
                    <div class="shop-detail__name"></div>
                </div>
                <img src="{{asset('storage/'. $shop['image_filename'])}}">

                <div class="shop-detail__content">
                    <span>{{'#'. $prefCode}}</span>
                    <span>{{'#'. $shop['genre']['genre']}}</span>
                </div>
                <div class="shop-detail__content">
                    <span>{{$shop['content']}}</span>
                </div>
            @endisset
        </div>
    </div>

    <div class="sub-container">
        <form class="reservation" action="/reservation/confirm" method="post">
            @csrf
            <div class="reservation__inputs">
                <div class="reservation__title">
                    <span>予約</span>
                </div> 
                
                <div class="reservation__content">
                    <div class="reservation__content--title"><span>日付</span></div> 
                    <input type="date" name="date" value="{{ date('Y-m-j')}}">
                </div>
                <div class="reservation__content">
                    <div class="reservation__content--title"><span>開始時刻</span></div> 
                    <select name="start_time">
                        @for ($hour = 15; $hour < 23; $hour++)
                            @for ($minute = 0; $minute <=59; $minute+=30)
                                {{ $strTime = sprintf('%02d', $hour). ':'. sprintf('%02d', $minute)}}
                                <option value="{{$strTime}}" >{{$strTime}}</option>
                            @endfor
                        @endfor
                    </select>
                </div>
                <div class="reservation__content">
                    <div class="reservation__content--title"><span>予約人数</span></div> 
                    <select name="people_counts">
                        @for ($count = 1; $count < 5; $count++)
                            <option value="{{$count}}" >{{$count. "名"}}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <input type="hidden" name="shop_id" value="{{$shop['id']}}">

            <div class="reservation__button-container">
                <button>予約内容確認</button>
            </div>
        </form>
    </div>
</div>

<script>
    // メニューが開いている時に、メニュー領域の外側をクリックしたとき、メニューを閉じる
    // const menuBtn = document.getElementById('menu-btn');
    // document.addEventListener('click', (e) => {
    //     if(!e.target.closest('#content-header__menu')) {
    //         if(menuBtn.checked) {
    //             menuBtn.checked = false;
    //         }
    //     } 
    // })
</script>

@endsection

