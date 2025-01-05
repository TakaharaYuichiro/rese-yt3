@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/reservation_accepted.css') }}" />
@endsection

@section('content')
<div class="thanks__content">
    <div class="thanks__content--inner">
        @if ($reservation_data['total_price'] > 0)
            <div class="thanks__heading">
                <h2>予約はまだ完了していません</h2>
            </div>

            <div class="thanks__message">
                <p>予約を完了するためには料金の支払いが必要です。</p>
                <p>支払いを行いますか？</p>
            </div>

            <form action="/payment" method="post">
                @csrf
                <input type="hidden" name="reservation_id" value="{{ $reservation_data['id'] }}">
                <div class="form__button">
                    <button class="form__button-submit">支払いへ</button>
                    <button class="form__button-submit form__button-cancel" type="button">
                        <a href="/mypage">今はしない</a>
                    </button>
                </div>
            </form>
        @else
            <div class="thanks__heading">
                <h2>ご予約ありがとうございました！</h2>
            </div>

            <div class="thanks__message">
                <p>予約を完了しました。</p>
                <p>お支払いが必要な項目はありません。</p>
            </div>

            <div class="form__button">
                <button class="form__button-submit" type="button">
                    <a href="/mypage">戻る</a>
                </button>
            </div>
        @endif
    </div>
</div>
@endsection
