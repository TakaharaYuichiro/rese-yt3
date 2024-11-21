@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/common_auth.css') }}">
@endsection


@section('content')
<div class="main-content">
  <div class="main-content__window">
    <div class="form__heading">
      <p>Login</p>
    </div>

    <form class="form" action="/login" method="post">
      @csrf
      <div class="form__group">
        <div class="form__group-content">
          <div class="form__input--text">
            <div class="form__input--icon"><span class="material-icons">email</span></div>
            <input type="text" name="email" placeholder="Email" value="{{ old('email') }}" />
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
            <div class="form__input--icon"><span class="material-icons">lock</span></div>
            <input type="password" name="password" placeholder="Password"/>
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
  </div>

 

  <!-- <div class="login-register-switching">
    <p>
      <span>アカウントをお持ちでない方はこちらから</span><br>
      <a href="/register">会員登録</a>
    </p>
  </div> -->

</div>
@endsection