@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/mypage.css') }}" />
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
            <div class="content-header">
    
            </div>

            <div class="reservation-status">
                <div class="reservation-status__title">
                    <span>予約状況</span>
                </div> 
        
                @foreach($reservations as $reservation)
                    <div class="reservation">
                        <div class="reservation-header">
                            <div class="reservation-header__id">
                                <span class="material-icons">schedule</span>
                                <span>{{"予約". $reservation->id}}</span>
                            </div>
                            <form class="reservation-header__change" action="/reservation/reservation_change" method="post">
                                @csrf
                                <input type="hidden" name="reservation_id" value="{{ $reservation->id}}"/>
                                <input type="hidden" name="shop_id" value="{{ $reservation->shop_id}}"/>
                                <button class="reservation-input-button">変更</button>
                            </form>
                            <form class="reservation-header__delete" action="/reservation/delete" method="post">
                                @csrf
                                <input type="hidden" name="reservation_id" value="{{ $reservation->id }}"/>
                                <button class="reservation-delete-button"></button>
                            </form>

                        </div>

                        <table class="reservation-table">
                            <tr class="reservation-table__row">
                                <th class="reservation-table__header">店名</th>
                                <td class="reservation-table__text">
                                    <span>{{ $reservation -> shop -> name }} </span>
                                    <a href="{{ route('detail',['shop_id' => $reservation -> shop_id ]) }}">詳細</a>
                                </td>
                            </tr>

                            <tr class="reservation-table__row">
                                <th class="reservation-table__header">日付</th>
                                <td class="reservation-table__text">
                                    <span>
                                        <?php 
                                            $week = ['日', '月', '火', '水', '木', '金', '土'];
                                            $timestamp = strtotime($reservation -> booked_datetime);
                                            $date = date('Y年m月d日', $timestamp);
                                            $day = $week[date('w', $timestamp)];
                                            echo $date. '('. $day. ')';
                                        ?>
                                    </span>
                                </td>
                            </tr>

                            <tr class="reservation-table__row">
                                <th class="reservation-table__header">開始時刻</th>
                                <td class="reservation-table__text">
                                    <span>{{date('H:i', strtotime($reservation -> booked_datetime)).  '〜';}}</span>
                                </td>
                            </tr>

                            <tr class="reservation-table__row">
                                <th class="reservation-table__header">予約人数</th>
                                <td class="reservation-table__text">
                                    <span>{{ $reservation['people_counts']. '名' }} </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                @endforeach
            </div>

            <div class="reservation-status">
                <div class="reservation-status__title">
                    <span>来店済み</span>
                </div> 
        
                @foreach($reservation_histories as $reservation)
                    <div class="reservation-history">
                        <div class="reservation-header">
                            <div class="reservation-header__id">
                                <span class="material-icons">check_circle</span>
                                <span>来店済み</span>
                            </div>
                            <form class="reservation-header__evaluation" action="/reservation/evaluation" method="post">
                                @csrf
                                @if ($reservation->evaluation_score == 0)
                                    <input type="hidden" name="reservation_id" value="{{ $reservation->id}}"/>
                                    <input type="hidden" name="shop_id" value="{{ $reservation->shop_id}}"/>
                                    <button class="reservation-input-button">お店を評価</button>
                                @else
                                    <div><span>あなたの評価点：{{$reservation->evaluation_score}}</span></div>
                                @endif
                            </form>
                        </div>

                        <table class="reservation-table">
                            <tr class="reservation-table__row">
                                <th class="reservation-table__header">店名</th>
                                <td class="reservation-table__text">
                                    <span>{{ $reservation -> shop -> name }} </span>
                                    <a href="{{ route('detail',['shop_id' => $reservation -> shop_id ]) }}">詳細</a>
                                </td>
                            </tr>

                            <tr class="reservation-table__row">
                                <th class="reservation-table__header">日付</th>
                                <td class="reservation-table__text">
                                    <span>
                                        <?php 
                                            $week = ['日', '月', '火', '水', '木', '金', '土'];
                                            $timestamp = strtotime($reservation -> booked_datetime);
                                            $date = date('Y年m月d日', $timestamp);
                                            $day = $week[date('w', $timestamp)];
                                            echo $date. '('. $day. ')';
                                        ?>
                                    </span>
                                </td>
                            </tr>

                            <tr class="reservation-table__row">
                                <th class="reservation-table__header">開始時刻</th>
                                <td class="reservation-table__text">
                                    <span>{{date('H:i', strtotime($reservation -> booked_datetime)).  '〜';}}</span>
                                </td>
                            </tr>

                            <tr class="reservation-table__row">
                                <th class="reservation-table__header">予約人数</th>
                                <td class="reservation-table__text">
                                    <span>{{ $reservation['people_counts']. '名' }} </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="sub-container">
            <div class="reservation-status">
                <div class="reservation-status__title">
                    <span>お気に入り店舗</span>
                </div> 
                <div class="panel-section">
                    @isset($shops)
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
                                    <form action="/mypage/favorite" method="post" id="submit_form">
                                        @csrf
                                        <input type="hidden" name="shop_id" value="{{$shop['id']}}">
                                        <button class="panel-section__item--button--favorite" type="submit">
                                            @php
                                                $favorite = false;
                                                foreach($shop['evaluation'] as $evaluation){
                                                    if ($evaluation['user_id'] == 1) {
                                                        $favorite = $evaluation['favorite']; 
                                                    }
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
                    @endisset
                </div>
            </div>
        </div>
    </div>

   
</div>

<script>
    const favoriteButtons = document.getElementsByClassName("panel-section__item--button--favorite");
    for(let favoriteButton of favoriteButtons) {
        favoriteButton.addEventListener("click", () => {
            const answer = window.confirm("この店舗のお気に入り登録を解除しますか？");
            if (answer) {
                favoriteButton.name='submit';
            }
            else {
                favoriteButton.name='cancel';
            }

            const submitForm = document.getElementById('submit_form');
            submitForm.submit();
        });
    }

    const reservationDeleteButtons = document.getElementsByClassName("reservation-delete-button");
    for(let reservationDeleteButton of reservationDeleteButtons) {
        reservationDeleteButton.addEventListener("click", () => {
            console.log('予約取り消し');
            const answer = window.confirm("この予約を取り消してもよろしいですか？");
            if (answer) {
                reservationDeleteButton.name='submit';
            }
            else {
                reservationDeleteButton.name='cancel';
            }
        });
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

