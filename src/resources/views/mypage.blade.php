@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/mypage.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/menu.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/common/header.css') }}" />
@endsection

@section('content')
<div class="main-container">
    @include('layouts.header', ['pageTitle'=>'My Page'])

    <div class="sub-container">
        <div class="block-container">
            <div class="content-container">
                <div class="content-container--title">
                    <span>予約状況</span>
                </div> 
                <div class="content-container--message">
                    @if (count($reservations) > 0)
                        <span>変更ボタンで予約変更・取り消しができます。</span><br>
                        <span>但し本日分は、変更・取り消しできません。</span>
                    @else
                        <span>予約はありません</span>
                    @endif
                </div>

                @foreach($reservations as $reservation)
                    <div class="reservation-status" style="background:{{$reservation['is_payment_required']? '#FF6F61': '#305DFF'}};">
                        <div class="reservation-header">
                            <div class="reservation-header__id">
                                <span class="material-icons">schedule</span>
                                <span>{{"予約番号". $reservation->id}}</span>
                            </div>

                            <div class="reservation-header__button--container">
                                @if($reservation['is_payment_required']) 
                                    <form class="reservation-header__change" action="/reservation_accepted" method="get">
                                        <input type="hidden" name="reservation_id" value="{{ $reservation->id}}"/>
                                        <button class="reservation__header--button">支払へ</button>
                                    </form>
                                @endif

                                <form class="reservation-header__change" action="/reservation_change" method="get">
                                    <input type="hidden" name="reservation_id" value="{{ $reservation->id}}"/>
                                    <input type="hidden" name="shop_id" value="{{ $reservation->shop_id}}"/>
                                    <button class="reservation__header--button">変更</button>
                                </form>
                            </div>
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

                            <tr class="reservation-table__row">
                                <th class="reservation-table__header">料金</th>
                                <td class="reservation-table__text">
                                    <span>{{ number_format($reservation['total_price']). '円' }} </span>
                                </td>
                            </tr>

                            <tr class="reservation-table__row">
                                <th class="reservation-table__header">支払状況</th>
                                <td class="reservation-table__text">
                                    <span>{{ $reservation['payment_status_message'] }} </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                @endforeach
            </div>

            <div class="content-container">
                <div class="content-container--title">
                    <span>履歴</span>
                </div> 
                
                @if (count($reservation_histories) == 0)
                    <div class="content-container--message">
                        <span>データがありません</span>
                    </div>
                @endif

                @foreach($reservation_histories as $reservation)
                    <div class="reservation-status reservation-history">
                        <div class="reservation-header">
                            <div class="reservation-header__id">
                                <span class="material-icons">check_circle</span>
                                <span>来店済み</span>
                            </div>
                            <form class="reservation-header__evaluation" action="/evaluation" method="post">
                                @csrf
                                <input type="hidden" name="reservation_id" value="{{ $reservation->id}}"/>
                                <input type="hidden" name="shop_id" value="{{ $reservation->shop_id}}"/>
                                @if ($reservation->evaluation_score == 0)
                                    <button class="reservation__header--button">お店を評価</button>
                                @else
                                    <div class="reservation-header__evaluation--score">
                                        @for ($i=0; $i<5; $i++)
                                            <span class="material-icons evaluation-score" style="color: {{ ($reservation->evaluation_score < ($i+1))? 'lightgray': 'yellow' }}">star</span>
                                        @endfor
                                    </div>
                                    <button class="reservation__header--button">再評価</button>
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
                                <th class="reservation-table__header">人数</th>
                                <td class="reservation-table__text">
                                    <span>{{ $reservation['people_counts']. '名' }} </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="block-container">
            <div class="content-container">
                <div class="content-container--title">
                    <span>お気に入り店舗</span>
                </div> 
                @if(count($evaluations)==0)
                    <div class="content-container--message">
                        <span>お気に入り店舗はありません</span>
                    </div>
                @endif

                <div class="panel-section">
                    <?php 
                        $image_storage = config('const.image_storage');
                        $disk = Storage::disk($image_storage);
                    ?>
                    @foreach($evaluations as $evaluation)
                        <div class="panel-section__item">
                            @if($evaluation['shop']['image_filename']!="")
                                <?php
                                    $img_obj = null;
                                    $file_name = 'shop_imgs/'. $evaluation['shop']['image_filename'];
                                    if ($disk->exists($file_name)) {
                                        $img_obj = $disk -> url($file_name);
                                    } 
                                ?>
                                <img src="{{ $img_obj }}">
                            @else
                                <img src="{{asset('storage/shop_imgs/test_img/noimage.png')}}">
                            @endif

                            <div class="panel-section__item--content">
                                <div class="panel-section__item--name">{{$evaluation['shop']['name']}}</div>
                                <div class="panel-section__item--tag">
                                    <span>{{'#'. $evaluation['shop']->getPrefName(); }}
                                    <span>{{'#'. $evaluation['shop']['genre']['genre']; }}</span>
                                </div>
                                <div class="panel-section__item--button">
                                    <form action="{{ route('detail',['shop_id' => $evaluation->shop_id ]) }}" method="get">
                                        <button class="panel-section__item--button--detail" type="submit">詳しくみる</button>
                                    </form>
                                    <form action="/mypage/favorite" method="post" id="submit_form">
                                        @csrf
                                        <input type="hidden" name="shop_id" value="{{$evaluation['shop_id']}}">
                                        <button class="panel-section__item--button--favorite" type="submit">
                                            <span class="material-icons" style="color: {{ $evaluation['favorite']? 'red' :'lightgray';}}">
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
</script>
@endsection

