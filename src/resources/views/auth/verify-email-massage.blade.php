@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/verify-email-massage.css') }}" />
@endsection

@section('content')
	@if (session('status') === 'verification-link-sent')
		<div class="main-form">
			<div class="main-form__title">
				<h2>確認メールを送信しました</h2>
			</div>
			<div class="main-form__content main-content">
				<p>{{auth()->user()['name']}}さんのメールアドレス宛に「【Rese】メールアドレスの確認」として確認メールを送信しました。</p>
				<p>メールに記載されているURLリンクをクリックして、会員登録を完了させてください。</p>
			</div>
			<div class="main-form__content">
				<p>もし、確認メールが届いていない場合は、次のボタンを押して、リンク先で確認メールを送信してください。</p>
				<button><a href="/email/resend_verify_email">確認メール再送信画面へ</a></button>
			</div>
            <div class="main-form__content main-form__content__addition">
                <p>ログインせずゲストユーザーとしてサイトを閲覧する場合は、次のボタンをクリックしてください。</p>
                <button><a href="/email/resetAndToHome">ゲストユーザー</a></button>
            </div>
		</div>

	@else
		<div class="main-form">
			<div class="main-form__title">
				<h2>会員登録はまだ完了していません</h2>
			</div>

			<div class="main-form__content  main-content">
				<p>{{auth()->user()['name']}}さんの会員登録はまだ完了していません。</p>
				<p>次のボタンを押すと{{auth()->user()['name']}}さんのメールアドレス宛に確認メールを再送信します。</p>
				<form method="post" action="{{ route('verification.send') }}">
					@method('post')
					@csrf
					<div>
						<button class="main-button" type="submit">確認メールを再送信</button>
					</div>
				</form>
			</div>

			<div class="main-form__content">
				<p>ログインせずゲストユーザーとしてサイトを閲覧する場合は、次のボタンをクリックしてください。</p>
				<button><a href="/email/resetAndToHome">ゲストユーザー</a></button>
			</div>
		</div>
	@endif
@endsection


