@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/confirm_reservation.css') }}" />
@endsection

@section('content')
<div class="main-container">
    <form class="reservation" action="/reservation/store" method="post">
        @csrf
        <div class="reservation__contents">
            <div class="reservation__title">
                <span>
                    @if ($reservation['exists_reservation_id'] == 0)
                        予約内容確認
                    @else
                        予約内容確認(予約変更)
                    @endif
                </span>
            </div> 

            <table class="confirm-table">
                <tr class="confirm-table__row">
                    <th class="confirm-table__header">お名前</th>
                    <td class="confirm-table__text">
                        <span>{{ $reservation['user_name'] }} </span>
                        <input type="hidden" name="user_id" value="{{ $reservation['user_id'] }}"/>
                    </td>
                </tr>

                <tr class="confirm-table__row">
                    <th class="confirm-table__header">店名</th>
                    <td class="confirm-table__text">
                        <span>{{ $reservation['shop_name'] }} </span>
                        <input type="hidden" name="shop_id" value="{{ $reservation['shop_id'] }}"/>
                    </td>
                </tr>

                <tr class="confirm-table__row">
                    <th class="confirm-table__header">日付</th>
                    <td class="confirm-table__text">
                        <span>
                            <?php 
                                $week = ['日', '月', '火', '水', '木', '金', '土'];
                                $timestamp = strtotime($reservation['date']);
                                $date = date('Y年m月d日', strtotime($reservation['date']));
                                $day = $week[date('w', $timestamp)];
                                echo $date. '('. $day. ')';
                            ?>
                        </span>
                        <input type="hidden" name="date" value="{{ $reservation['date'] }}"/>
                    </td>
                </tr>

                <tr class="confirm-table__row">
                    <th class="confirm-table__header">開始時刻</th>
                    <td class="confirm-table__text">
                        <span>{{ $reservation['start_time']. '〜' }} </span>
                        <input type="hidden" name="start_time" value="{{ $reservation['start_time'] }}"/>
                    </td>
                </tr>

                <tr class="confirm-table__row">
                    <th class="confirm-table__header">予約人数</th>
                    <td class="confirm-table__text">
                        <span>{{ $reservation['people_counts']. '名' }} </span>
                        <input type="hidden" name="people_counts" value="{{ $reservation['people_counts'] }}"/>
                    </td>
                </tr>

                <tr class="confirm-table__row">
                    <th class="confirm-table__header"><div>コース</div><div>メニュー</div></th>
                    <td class="confirm-table__text">

                        <div>
                            @if (count($reservation['reserved_courses'])>0)
                                <ul>
                                    @foreach($reservation['reserved_courses'] as $reservedCourse)
                                        <li>{{ $reservedCourse['name']. '('. number_format($reservedCourse['price']). '円) × '. $reservedCourse['quantity']. '名分'}}</li>
                                        <input type="hidden" name="courses[{{$reservedCourse['id']}}]" value="{{$reservedCourse['quantity'] }}"/>
                                    @endforeach
                                </ul>
                                <div><span>{{'合計：'. number_format($reservation['total_cost']). '円'}}</span></div>
                            @else
                                <div><span>コースメニューは予約されていません</span></div>
                            @endif
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <input type="hidden" name="exists_reservation_id" value="{{ $reservation['exists_reservation_id'] }}"/>

        <div class="reservation__button-container">
            <button class="reservation__button" type="submit" name="submit">
                @if($reservation['exists_reservation_id'] == 0)
                    この内容で予約する
                @else
                    この内容に変更する
                @endif
            </button>
            <button class="reservation__button reservation__button-cancel" type="submit" name="cancel">修正</button>
        </div>
    </form>
</div>
@endsection