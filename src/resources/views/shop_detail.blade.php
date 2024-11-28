@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/shop_detail.css') }}" />
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
        </div>
    </div>

    <div class="main-container">
        <div class="sub-container">
            <div class="shop-detail">
                @isset($shop)
                    <div class="shop-detail__name">
                        <button onclick="history.back()"><</button>
                        <span>{{$shop['name']}}</span>
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

        <?php
            $is_new_reservation = true;
            if ($reservation) {
                if ($reservation['id'] > 0) {
                    $is_new_reservation = false;
                }
            }
        ?>

        <div class="sub-container">
            <form class="reservation" action="/reservation/confirm" method="post">
                @csrf

                @isset($reservation)
                    <div class="reservation__inputs">
                        <div class="reservation__title">
                            <span>現在の予約内容</span>
                        </div> 

                        <div class="reservation__content">
                            <div class="reservation__content--title"><span>日付</span></div> 
                            <div class="reservation__content--current-value"><span>{{date('Y-m-j', strtotime($reservation['booked_datetime'])) }}</span></div>
                        </div>
                        <div class="reservation__content">
                            <div class="reservation__content--title"><span>開始時刻</span></div> 
                            <div class="reservation__content--current-value"><span>{{date('H:i', strtotime($reservation['booked_datetime'])) }}</span></div>
                        </div>
                        <div class="reservation__content">
                            <div class="reservation__content--title"><span>予約人数</span></div> 
                            <div class="reservation__content--current-value"><span>{{$reservation['people_counts']. '名' }}</span></div>
                        </div>
                    </div>
                @endisset
                
                <div class="reservation__inputs">
                    <div class="reservation__title">
                        <span>
                            @if ($is_new_reservation)
                                新規予約
                            @else
                                変更後の予約内容
                            @endif
                        </span>
                    </div> 
                    
                    <div class="reservation__content">
                        <div class="reservation__content--title"><span>日付</span></div> 
                        <input 
                            type="date" name="date" 
                            value="{{!$reservation? date('Y-m-d'): date('Y-m-d', strtotime($reservation['booked_datetime'])) }}"
                        >
                    </div>
                    <div class="reservation__content">
                        <div class="reservation__content--title"><span>開始時刻</span></div> 
                        <select name="start_time">
                            @for ($hour = 15; $hour < 23; $hour++)
                                @for ($minute = 0; $minute <=59; $minute+=30)
                                    {{ $strTime = sprintf('%02d', $hour). ':'. sprintf('%02d', $minute)}}
                                    <option 
                                        value="{{$strTime}}" 
                                        @if ($reservation)
                                            {{ ($strTime == date('H:i', strtotime($reservation['booked_datetime'])))? 'selected': '' }}
                                        @endif
                                    >{{$strTime}}</option>
                                @endfor
                            @endfor
                        </select>
                    </div>
                    <div class="reservation__content">
                        <div class="reservation__content--title"><span>予約人数</span></div> 
                        <select name="people_counts">
                            @for ($count = 1; $count < 21; $count++)
                                <option 
                                    value="{{$count}}"
                                    @if ($reservation)
                                        {{ ($count == $reservation['people_counts'])? 'selected': '' }}
                                    @endif
                                >{{$count. "名"}}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <input type="hidden" name="shop_id" value="{{$shop['id']}}">
                <input type="hidden" name="exists_reservation_id" value="{{$reservation? $reservation['id']: 0}}">

                <div class="reservation__button-container">
                    <button>
                        @if ($is_new_reservation)
                            予約内容確認
                        @else
                            予約変更内容確認
                        @endif
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
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

