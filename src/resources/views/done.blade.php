@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/thanks.css') }}" />
@endsection

@section('content')
  <div class="thanks__content">
    <div class="backStr"><span>Thank you</span></div>
    <div class="thanks__content--inner">
      <div class="thanks__heading">
        <h2>ご予約ありがとうございました</h2>
      </div>
      <div class="form__button">
        <button class="form__button-submit">
          <a href="/">HOME</a>
        </button>
      </div>
    </div>
  </div>
@endsection
