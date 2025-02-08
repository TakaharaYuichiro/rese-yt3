@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/reservation_list.css') }}"/>
    <link rel="stylesheet" href="{{ asset('css/common/admin_menu.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/common/header.css') }}" />
@endsection

@section('content')
<div class="main-container">
    @include('layouts.header', ['pageTitle'=>'予約状況'])

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

    <div class="search-section">        
        <form  class="search-form" action="/reservation_list/search" method="get">
            <input class="search-form__item search-form__item--keyword" type="text" name="keyword" placeholder="名前やメールアドレスを入力してください" value="{{old('keyword')}}"/>
            <input type="date" name="date" class="search-form__item search-form__item--date"/>
            <input type="hidden" name="shop_id" value="{{$shop['id']}}">

            <button class="search-form__button" type="submit" name="submit">検索</button>
            <button class="search-form__button search-form__item--reset" type="submit" name="reset">リセット</button>
        </form>
    </div>

    <div class="shop-name-container">
        <span>{{$shop['name']}} の予約状況</span>
    </div>

    <div class="list-section">
        <table class="list-table">
            <tr>
                <th class="list-table__header">日付</th>
                <th class="list-table__header">開始時刻</th>
                <th class="list-table__header">氏名</th>
                <th class="list-table__header">メールアドレス</th>
                <th class="list-table__header">人数</th>
                <th class="list-table__header">コース予約</th>
                <th class="list-table__header list-table__header--detail"></th>
            </tr>

            @foreach($reservations as $reservation)
                <tr class="list-table__row">
                    <td><span>{{date('Y-m-d', strtotime($reservation['booked_datetime'])) }}</span></td> 
                    <td><span>{{date('H:i', strtotime($reservation['booked_datetime'])) }}</span></td>
                    <td><span>{{ $reservation->user['name'] }} </span></td> 
                    <td><span>{{ $reservation->user['email'] }}</span></td>
                    <td><span>{{ $reservation['people_counts'] }} </span></td> 
                    <td>
                        <span>{{ ($reservation->exists_reserved_course)? "コース予約あり": "コース予約なし"}} </span>
                    </td> 
                    <td>
                        <form action="/reservation_detail" method="post">
                            @csrf
                            <input type="hidden" name="reservation_id" value="{{$reservation['id']}}">
                            <button class="list-table__detail-button" type="submit" value="{{ $reservation['id'] }}">詳細</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </table>
    </div> 
</div>
@endsection