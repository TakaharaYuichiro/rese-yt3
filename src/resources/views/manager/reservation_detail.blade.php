@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/reservation_detail.css') }}"/>
    <link rel="stylesheet" href="{{ asset('css/common/admin_menu.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/common/header.css') }}" />
@endsection

@section('content')
<div class="main-container">
    @include('layouts.header', ['pageTitle'=>'予約内容詳細'])

    <div class="reservation__contents">
        <table class="confirm-table">
            <tr class="confirm-table__row">
                <th class="confirm-table__header">お名前</th>
                <td class="confirm-table__text">
                    <span>{{ $reservation['user']['name'] }} </span>
                </td>
            </tr>

            <tr class="confirm-table__row">
                <th class="confirm-table__header">店名</th>
                <td class="confirm-table__text">
                    <span>{{ $reservation['shop']['name'] }} </span>
                </td>
            </tr>

            <tr class="confirm-table__row">
                <th class="confirm-table__header">日付</th>
                <td class="confirm-table__text">
                    <span>
                        <?php 
                            $week = ['日', '月', '火', '水', '木', '金', '土'];
                            $timestamp = strtotime($reservation['booked_datetime']);
                            $date = date('Y年m月d日', $timestamp);
                            $day = $week[date('w', $timestamp)];
                            echo $date. '('. $day. ')';
                        ?>
                    </span>
                </td>
            </tr>

            <tr class="confirm-table__row">
                <th class="confirm-table__header">開始時刻</th>
                <td class="confirm-table__text">
                    <span>{{ date('H:i', strtotime($reservation['booked_datetime'])). '〜' }} </span>
                </td>
            </tr>

            <tr class="confirm-table__row">
                <th class="confirm-table__header">予約人数</th>
                <td class="confirm-table__text">
                    <span>{{ $reservation['people_counts']. '名' }} </span>
                </td>
            </tr>

            <tr class="confirm-table__row">
                <th class="confirm-table__header">コースメニュー</th>
                <td class="confirm-table__text">
                    @if (count($reserved_courses) > 0) 
                        <ul>
                            @foreach($reserved_courses as $reservedCourse)
                                <li>{{ $reservedCourse['course']['name']. '('. $reservedCourse['price_as_of_reservation']. '円) × '. $reservedCourse['quantity']. '名分'}}</li>
                            @endforeach
                        </ul>
                    @else
                        <div><span>コースメニューは予約されていません</span></div>
                    @endif

                    {{-- 
                    @if ($reservation['course_id']) 
                        @for($i=0; $i<count($reservation['course_id']); $i++)
                            <div>
                                <span>{{ $reservation['course_id'][$i] }}：</span>
                                <span>{{ $reservation['quantity'][$i] }}</span>
                            </div>
                        @endfor
                    @else
                        <div><span>コースメニューは予約されていません</span></div>
                    @endif
                    --}}
                </td>
            </tr>
        </table>
    </div>
    <div class="reservation__button-container">
        <button class="reservation__button" type="button" onClick="history.back()">戻る</button>
    </div>
</div>

@endsection