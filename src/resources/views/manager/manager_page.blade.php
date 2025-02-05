@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/manager_page.css') }}"/>
    <link rel="stylesheet" href="{{ asset('css/common/admin_menu.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/common/header.css') }}" />
@endsection


@section('content')
<div class="main-container">
    @include('layouts.header', ['pageTitle'=>'店舗代表者マイページ'])

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

    <div class="utility-container">
        <form action="/shop_editor/new_entry" method="get">
            <button><span>新規店舗作成</span></button>
        </form>
    </div>
 
    <div class="list-section">
        <table class="info-table">
            <tr>
                <th class="info-table__header info-table__header--name" colspan="2">店名</th>
                <th class="info-table__header info-table__header--email" colspan="2">予約状況</th>
            </tr>

            @foreach($management_shops as $management_shop) 
                <tr class="info-table__row">
                    <td class="info-table__td">
                        <span>{{ $management_shop['name'] }}</span>
                    </td>
                    <td class="info-table__td--containg-button">
                        <form action="/shop_editor" method="get">
                            <input type="hidden" name="shop_id" value="{{$management_shop['id']}}">
                            <button class="with-explanation-button" type="submit" value="{{ $management_shop['id'] }}" title="店舗情報を編集します">
                                <div><span class="material-icons with-explanation-button--icon">edit</span></div>
                                <div><span class="with-explanation-button--explanation">店舗編集</span></div>
                            </button>
                        </form>
                    </td> 

                    <td class="info-table__td">
                        <span>
                            {{ count($management_shop['reservations'])==0? "予約なし": "予約あり(".count($management_shop['reservations']). "件)"}}
                        </span>
                    </td>
                    
                    <td class="info-table__td--containg-button">
                        <form action="/reservation_list" method="get">
                            <input type="hidden" name="shop_id" value="{{$management_shop['id']}}">
                            <button class="with-explanation-button" type="submit" value="{{ $management_shop['id'] }}">
                                <div><span class="material-icons with-explanation-button--icon">list</span></div>
                                <div><span class="with-explanation-button--explanation">一覧</span></div>
                            </button>
                        </form>
                    </td> 
                </tr>
            @endforeach
        </table>
    </div> 
</div>
@endsection