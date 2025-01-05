@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/course_editor.css') }}"/>
    <link rel="stylesheet" href="{{ asset('css/common/admin_menu.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/common/header.css') }}" />
@endsection

@section('content')
<?php
    $prefCodes = App\Consts\CommonConst::PREF_CODE;
    $genres = App\Models\Genre::get();
?>

<div class="main-container">
    @include('layouts.header', ['pageTitle'=>($is_new)? '新規コースメニュー作成': 'コースメニュー情報編集'])

    <div class="message-container">
        @if(session('message'))
            <div class="message-container--success">{{session('message')}}</div>
        @endif

        @if($errors->any())
            <div class="message-container--danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{$error}}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
 
    <form class="editor-form" action="/course_editor/store" method="post">
        @csrf
        <table class="info-table">
            <tr>
                <th>店名</th>
                <td>{{ $shop['name'] }}</td>
            </tr>
            <tr>
                <th>メニュー名</th>
                <td><input type="text" name="name" placeholder="コースメニュー名" value="{{ old('name', $course['name']) }}"></td>
            </tr>
            <tr>
                <th>単価</th>
                <td><input type="text" name="price" value="{{ old('price', $course['price']) }}"></td>
            </tr>
          
            <tr>
                <th>説明</th>
                <td><textarea name="detail" placeholder="説明">{{ old('detail', $course['detail'])}}</textarea></td>
            </tr>           
        </table>

        <input type="hidden" name="is_new" value="{{$is_new}}">
        <input type="hidden" name="course_id" value="{{$course['id']}}">
        <input type="hidden" name="shop_id" value="{{$shop['id']}}">

        <div class="form-submit-button__container">
            <button type="submit">{{($is_new)? "この内容で新規登録": "コースメニューを更新"}}</button>
        </div>
    </form>

    <div class="extra-buttons-container">
        <form class="extra-form" action="/course_editor/break" method="post" id="form-break">
            @csrf
            <input type="hidden" name="shop_id" value="{{$shop['id']}}">
            <button class="extra-button" type="submit" id="form-break--submit-button">変更を破棄して戻る</button>
        </form>
        <form class="extra-form" action="/course_editor/remove" method="post" id="form-remove">
            @csrf
            <input type="hidden" name="course_id" value="{{$course['id']}}">
            <button class="extra-button" type="submit" id="form-remove--submit-button" {{$is_new? 'disabled': ''}} >このコースメニューを削除</button>
        </form>
    </div>
   
</div> 

<script>
    const formBreakSubmitButton = document.getElementById('form-break--submit-button');
    formBreakSubmitButton.addEventListener('click', ()=> {
        const answer = window.confirm("変更箇所がある場合は破棄されます。コースメニューリストに戻りますか？");
            if (answer) {
                formBreakSubmitButton.name='submit';
            }
            else {
                formBreakSubmitButton.name='cancel';
            }

            const formBreak = document.getElementById('form-break');
            formBreak.submit();
    });

    const formRemoveSubmitButton = document.getElementById('form-remove--submit-button');
    formRemoveSubmitButton.addEventListener('click', ()=> {
        const answer = window.confirm("このコースメニューを削除しますか？");
            if (answer) {
                formRemoveSubmitButton.name='submit';
            }
            else {
                formRemoveSubmitButton.name='cancel';
            }

            const formRemove = document.getElementById('form-remove');
            formRemove.submit();
    });

</script>
@endsection