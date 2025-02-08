@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/payment.css') }}" />
@endsection

@section('content')
<div class="main-container">
    <div class="main-content__window">

    <div class="header-container">
        <div class="header-text">Stripe決済</div>
    </div>

    <div class="message-container">
        @if (session('flash_alert'))
            <div class="message-container--danger">{{ session('flash_alert') }}</div>
        @endif
    </div>

    <div class="description">
        Stripeを利用したカード決済のデモです。<br>
        テストに使用するカード番号等は
        <a href="https://docs.stripe.com/testing?locale=ja-JP" target="_blank">こちら(stripeDOCS)</a>
        を参照してください。
    </div>

    <div class="main-form-container">
        <div class="payment-content">
            <span>支払い金額: </span>
            <span>{{ number_format($reservation_data['total_price']).'円' }}</span>
        </div>
        <form id="card-form" action="{{ route('payment.store') }}" method="POST">
            @csrf
            <div class="card-content">
                <label for="card_number">カード番号</label>
                <div id="card-number" class="form-control"></div>
            </div>

            <div class="card-content">
                <label for="card_expiry">有効期限</label>
                <div id="card-expiry" class="form-control"></div>
            </div>

            <div class="card-content">
                <label for="card-cvc">セキュリティコード</label>
                <div id="card-cvc" class="form-control"></div>
            </div>

            <div id="card-errors" class="text-danger"></div>

            <input type="hidden" name="reservation_id" value="{{ $reservation_data['id'] }}">
            <input type="hidden" name="total_price" value="{{ $reservation_data['total_price'] }}">

            <div class="button-container">
                <button>支払い</button>
            </div>
        </form>
    </div>
</div>

<script src="https://js.stripe.com/v3/"></script>

<script>
    const stripe_public_key = "{{ config('stripe.stripe_public_key') }}"
    const stripe = Stripe(stripe_public_key);
    const elements = stripe.elements();

    var cardNumber = elements.create('cardNumber');
    cardNumber.mount('#card-number');
    cardNumber.on('change', function(event) {
        var displayError = document.getElementById('card-errors');
        if (event.error) {
            displayError.textContent = event.error.message;
        } else {
            displayError.textContent = '';
        }
    });

    var cardExpiry = elements.create('cardExpiry');
    cardExpiry.mount('#card-expiry');
    cardExpiry.on('change', function(event) {
        var displayError = document.getElementById('card-errors');
        if (event.error) {
            displayError.textContent = event.error.message;
        } else {
            displayError.textContent = '';
        }
    });

    var cardCvc = elements.create('cardCvc');
    cardCvc.mount('#card-cvc');
    cardCvc.on('change', function(event) {
        var displayError = document.getElementById('card-errors');
        if (event.error) {
            displayError.textContent = event.error.message;
        } else {
            displayError.textContent = '';
        }
    });

    var form = document.getElementById('card-form');
    form.addEventListener('submit', function(event) {
        event.preventDefault();
        var errorElement = document.getElementById('card-errors');
        if (event.error) {
            errorElement.textContent = event.error.message;
        } else {
            errorElement.textContent = '';
        }

        stripe.createToken(cardNumber).then(function(result) {
            if (result.error) {
                errorElement.textContent = result.error.message;
            } else {
                stripeTokenHandler(result.token);
            }
        });
    });

    function stripeTokenHandler(token) {
        var form = document.getElementById('card-form');
        var hiddenInput = document.createElement('input');
        hiddenInput.setAttribute('type', 'hidden');
        hiddenInput.setAttribute('name', 'stripeToken');
        hiddenInput.setAttribute('value', token.id);
        form.appendChild(hiddenInput);
        form.submit();
    }
</script>
@endsection