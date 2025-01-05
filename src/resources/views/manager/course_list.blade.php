@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/course_list.css') }}"/>
    <link rel="stylesheet" href="{{ asset('css/common/admin_menu.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/common/header.css') }}" />
@endsection


@section('content')
<?php
    $prefCodes = App\Consts\CommonConst::PREF_CODE;
    $genres = App\Models\Genre::get();
?>

<div class="main-container">
    @include('layouts.header', ['pageTitle'=>'コースメニューリスト'])

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
        <div class="utility-container__title">
            {{$shop['name']}}
        </div>
        <form action="/course_editor/new_entry" method="get">
            @csrf
            <input type="hidden" name="shop_id" value="{{$shop['id']}}">
            <button><span>新規コースメニュー作成</span></button>
        </form>
    </div>

    <div class="editor-container">
        <table class="course-table" id="course-table__enable">
            <tr>
                <th class="course-table__header--name">メニュー名</th>
                <th class="course-table__header--price">単価</th>
                <th class="course-table__header--button"></th>
            </tr>

            @foreach($courses as $course) 
                <tr>
                    <td class="course-td--text">{{$course['name']}}</td>
                    <td class="course-td--text course-td--price">{{ number_format($course['price']) }}円</td>
                    <td class="course-td__button">
                        <form action="/course_editor" method="get">
                            <input type="hidden" name="course_id" value="{{$course['id']}}">
                            <button type="submit" class='course-edit-button'>編集</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
</div> 
@endsection