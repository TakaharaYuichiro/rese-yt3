@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/common_auth.css') }}">
@endsection


@section('content')
<div class="main-content">
  <div class="form__heading">
    <h2>ログイン</h2>
  </div>

  <form class="form" action="/login" method="post">
    @csrf
    <div class="form__group">
      <div class="form__group-content">
        <div class="form__input--text">
          <input type="text" name="email" placeholder="メールアドレス" value="{{ old('email') }}" />
        </div>
        <div class="form__error">
          @error('email')
          {{ $message }}
          @enderror
        </div>
      </div>
    </div>
    <div class="form__group">
      <div class="form__group-content">
        <div class="form__input--text">
          <input type="password" name="password" placeholder="パスワード"/>
        </div>
        <div class="form__error">
          @error('password')
          {{ $message }}
          @enderror
        </div>
      </div>
    </div>
    <div class="form__button">
      <button class="form__button-submit" type="submit">ログイン</button>
    </div>
  </form>

  <div class="login-register-switching">
    <p>
      <span>アカウントをお持ちでない方はこちらから</span><br>
      <a href="/register">会員登録</a>
    </p>
  </div>

</div>
@endsection