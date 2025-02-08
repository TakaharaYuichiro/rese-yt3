@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/thanks.css') }}" />
@endsection

@section('content')
  <div class="thanks__content">
    <div class="thanks__content--inner">
      <div class="thanks__heading">
      <h2>{{$message}}</h2>
      </div>
      <div class="form__button">
        <button class="form__button-submit">
          <a href="/mypage">戻る</a>
        </button>
      </div>
    </div>
  </div>
@endsection
