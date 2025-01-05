@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin/change_role.css') }}"/>
@endsection

@section('content')
<?php
    $roleDict = App\Consts\CommonConst::ROLE;
?>

<div class="main-container">
    <div class="main-content__window">
        <div class="header-container">
            <span class="user-name--text">{{$target_user['name']}}</span>
            <span>さんのユーザー権限</span>
        </div>

        <div class="main-form-container">
            <div class="main-form-container__content">
                <div class="main-form-container__content-title">現在の権限</div>
                <div class="main-form-container__content-data"><span>{{$roleDict[$target_user['role']]}}</span></div>
            </div>

            <form action="/change_role/update_role" method="post" class="main-form">
                @csrf
                <div class="main-form-container__content">
                    <div class="main-form-container__content-title">変更後の権限</div>
                    <div class="main-form-container__content-data">
                        <select name="new_role" id="new-role">
                            @foreach($roleDict as $roleIndex => $roleName)
                                <option value="{{$roleIndex}}" @if($target_user['role'] == $roleIndex) selected @endif>{{$roleName}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            
                <input type="hidden" name="user_id" value="{{$target_user['id']}}">
                <input type="hidden" name="current_role" value="{{ $target_user['role'] }}">
                <div class="main-form__button">
                    <div>
                        <input type="checkbox" id="checkbox__mail-sending" name="is_mail_sending">
                        <label for="checkbox__mail-sending">設定内容をメールで通知</label>
                    </div>
                </div>
                <div class="main-form__button">
                    <button class="button--submit" type="submit">設定</button>
                    <button class="button--submit button--cancel" type="submit" name="cancel">キャンセル</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection