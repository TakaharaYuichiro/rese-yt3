@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/common_auth.css') }}">
@endsection


@section('content')
<div class="main-content">
  <div class="main-content__window">
    <div class="form__heading">
      <p>Registration</p>
    </div>

    <form class="form" action="/register" method="post">
      @csrf
      <div class="form__group">
        <div class="form__group-content">
          <div class="form__input--text">
            <div class="form__input--icon"><span class="material-icons">person</span></div>
            <input type="text" name="name" placeholder="名前" value="{{ old('name') }}" />
          </div>
          <div class="form__error">
            @error('name')
            {{ $message }}
            @enderror
          </div>
        </div>
      </div>
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
            <input type="password" name="password" placeholder="パスワード"/>
          </div>
          <div class="form__error">
            @error('password')
            {{ $message }}
            @enderror
          </div>
        </div>
      </div>
      <div class="form__group">
        <div class="form__group-content">
          <div class="form__input--text">
            <div class="form__input--icon"><span class="material-icons">lock</span></div>
            <input type="password" name="password_confirmation" placeholder="確認用パスワード"/>
          </div>

          <div class="form__error">
            <!-- 行間を少し空けるためのダミー要素 -->
          </div>
        </div>
      </div>
      <div class="form__button">
        <div class="form__button-cancel"><a href="{{ route('home') }}">キャンセル</a></div> 
        <button class="form__button-submit" type="submit">会員登録</button>
      </div>
    </form>
  </div>
</div>
@endsection