@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin/admin.css') }}"/>
    <link rel="stylesheet" href="{{ asset('css/common/admin_menu.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/common/header.css') }}" />
@endsection

@section('content')
<?php
    $roleDict = App\Consts\CommonConst::ROLE;
?>

<div class="main-container">
    @include('layouts.header', ['pageTitle'=>'管理者ページ'])
    
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

    <div class="search-section">        
        <form  class="search-form" action="/admin/search" method="get">
            @csrf
            <input class="search-form__item search-form__item--keyword" type="text" name="keyword" placeholder="名前やメールアドレスを入力してください" value="{{old('keyword')}}"/>

            <select class="search-form__item search-form__item--gender" name="role" >
                <option value="" selected>全ての権限</option>
                @foreach($roleDict as $roleIndex => $roleName)
                    <option value="{{$roleIndex}}">{{$roleName}}</option>
                @endforeach
            </select>

            <button class="search-form__button" type="submit" name="submit">検索</button>
            <button class="search-form__button search-form__item--reset" type="submit" name="reset">リセット</button>
        </form>
    </div>

    <div class="utility-container">
        <button id="create-notification-mail-button">
            <span class="material-icons">mail</span>
            <span>お知らせメール</span>
        </button>
    </div>
 
    <div class="list-section">
        <table class="list-table">
            <tr>
                <th class="list-table__header">名前</th>
                <th class="list-table__header">メールアドレス</th>
                <th class="list-table__header" colspan="2">権限</th>
                <th class="list-table__header" colspan="2">店舗代表者の担当店舗</th>
            </tr>

            @foreach($users as $user)
                <tr class="list-table__row">
                    <td class="list-table__td"><span>{{ $user['name'] }}</span></td> 
                    <td class="list-table__td"><span>{{ $user['email'] }}</span></td>
                    <td class="list-table__td"><span>{{ $roleDict[$user['role']] }}</span></td>
                    <td class="list-table__td--containg-button">
                        @if ($user['id'] != $profile['id'])
                            <form action="/change_role" method="post">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $user['id'] }}">
                                <button class="list-table__button--change-role" type="submit">
                                    <span class="material-icons">settings</span>
                                </button>
                            </form>
                        @endif
                    </td> 

                    <td class="list-table__td">
                        @if ($user['role']==11)
                            <span>
                                <?php
                                    $text = "";
                                    $counts = count($user['shops']);
                                    if ($counts < 4) {
                                        foreach ($user['shops'] as $shop) {
                                            $text .= $shop['shop_name']." ";
                                        }
                                        
                                    } else {
                                        for ($i=0; $i<3; $i++) {
                                            $text .= $user['shops'][$i]['shop_name']." ";
                                        }
                                        $text .= "他". strval($counts-3). "店";
                                    }
                                    echo($text);
                                ?>
                            </span>
                        @endif
                    </td>

                    <td class="list-table__td--containg-button">
                        @if ($user['role']==11)
                            <form action="/change_manager" method="post">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $user['id'] }}">
                                <button class="list-table__button-detail" type="submit">
                                    <span class="material-icons">settings</span>
                                </button>
                            </form>
                        @endif
                    </td> 
                </tr>
            @endforeach
        </table>
    </div> 

    <div class="optional-section">
        <div class="optional-section__pagination">
            {{ $users->appends(request()->query())->links('vendor.pagination.original_pagination_view') }}
        </div>
    </div>
</div>

{{-- モーダルウィンドウ: お知らせメール --}}
<div id="modal-window__notification-mail" class="modal-window">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-header__user-name--container">
                <span class="modal-header__user-name--text">お知らせメール</span>
            </div>
            <div class="modal-header__button--contaier">
                <span class="modal-close" id="close-button--mail"></span>
            </div>
        </div>
        <div class="modal-body">
            <form action="/admin/send_group_mail" method="post" class="modal-form" id="submit-form--mail">
                @csrf
                <div class="modal-body__content_vertical">
                    <div class="modal-body__content_vertical-header">以下の権限のユーザーにメールを送信</div>
                    <div class="modal-body__content_vertical-data">
                        @foreach ($roleDict as $roleIndex => $roleName)
                        <div>
                            <input type="checkbox" class="checkbox-mail" id="checkbox-mail_{{$roleIndex}}" {{($roleIndex==21)?"checked":""}}>
                            <label for="checkbox-mail_{{$roleIndex}}">{{$roleName}}</label>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-body__content_vertical">
                    <div class="modal-body__content_vertical-header">メールのタイトル</div>
                    <div class="modal-body__content_vertical-data">
                        <input class="modal-body__content_vertical-data--input" id="mail_subject" type="text" name="subject" value="【Rese】お知らせ">
                    </div>
                </div>
                <div class="modal-body__content_vertical">
                    <div class="modal-body__content_vertical-header">本文</div>
                    <div class="modal-body__content_vertical-data">
                        <textarea class="modal-body__content_vertical-data--textarea" id="mail_content" name="mail_content" rows="20"></textarea>
                    </div>
                </div>
        
                <input type="hidden" id="modal-form__user-id--mail" name="user_id" value="">
                @foreach ($roleDict as $roleIndex => $roleName)
                    <input type="hidden" class="is-sending-mail" id="is-sending-mail_{{$roleIndex}}" name="{{'is_sending_mail['. $roleIndex. ']' }}">
                @endforeach

                <div class="modal-form__button">
                    <button id="submit-button--mail" type="button">送信</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const modalWindowNotificationMail = document.getElementById('modal-window__notification-mail');

    // お知らせメールのモーダルウィンドウを開くためのボタン設定
    const buttonOpenWindowMail = document.getElementById('create-notification-mail-button');
    buttonOpenWindowMail.addEventListener('click', () => {
        modalWindowNotificationMail.style.display = 'flex';

        // サーバーへのデータ送信をjavascriptで実行
        const submitButton = document.getElementById('submit-button--mail');
        submitButton.addEventListener('click', ()=>{
            // 設定時にメールを送信するか否かを決めるチェックボックスの状態を調べてInput要素のValueに代入
            const checkboxMailInputs = document.getElementsByClassName('checkbox-mail');
            
            for(let checkboxMailInput of checkboxMailInputs) {
                const checkboxMailInputId = checkboxMailInput.id;
                const prefix = 'checkbox-mail_';
                const roleIndex = checkboxMailInputId.substr(checkboxMailInputId.indexOf(prefix) + prefix.length );
                const isSendingMailInput = document.getElementById('is-sending-mail_' + roleIndex);
                isSendingMailInput.value = checkboxMailInput.checked;            
            }

            const answer = window.confirm('メールを送信してもよろしいですか？');
            if (!answer) {
                return;
            }

            const submitForm = document.getElementById('submit-form--mail');
            submitForm.submit();
        });
    })

    // バツ印がクリックされた時
    const closeButtonMail = document.getElementById('close-button--mail');
    closeButtonMail.addEventListener('click', ()=>{
        modalWindowNotificationMail.style.display = 'none';
    });

    // モーダルコンテンツ以外がクリックされた時
    addEventListener('click', (e)=>{
        const modalWindows = document.getElementsByClassName('modal-window');
        if (e.target.className == 'modal-window') {
            for(let modalWindow of modalWindows) {
                modalWindow.style.display = 'none';
            }
        }
    });
</script>

@endsection