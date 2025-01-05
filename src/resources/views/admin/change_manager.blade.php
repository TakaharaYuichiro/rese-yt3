@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin/change_manager.css') }}"/>
@endsection

@section('content')
<div class="main-container">
    <div class="main-content__window">

        <div class="header-container">
            <span class="user-name--text">{{$target_user['name']}}</span>
            <span>さんの担当店舗</span>
        </div>

        <form action="/change_manager/update_shops" method="post" class="main-form">
            @csrf
            <div class="main-table__container">    
                <table class="main-table">
                    <tr>
                        <th class='main-table__header1'>店名</th>
                        <th class='main-table__header2'>担当店舗</th>
                    </tr>
                    @foreach ($shop_dict as $shopId => $shopName ) 
                        <?php
                            $checked ="";
                            foreach($managers as $manager) {
                                if ($manager['shop_id'] == $shopId) {
                                    $checked = "checked";
                                }
                            }
                        ?>

                        <tr>
                            <td>{{$shopName}}</td>
                            <td>
                                <input type="checkbox" name="checked_shop_ids[{{$shopId}}]" {{$checked}}>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>

            <input type="hidden" name="user_id" value="{{$target_user['id']}}">

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

@endsection