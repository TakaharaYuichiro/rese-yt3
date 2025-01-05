<p>{{ $data['user_name']. " 様"}}</p>
<p>本メールはReseでご飲食店をご予約していただいたお客様に確認のためにお送りしています。</p>
<br>
<p>---予約内容---</p>
<p>店名：{{ $data['shop_name'] }}</p>
<p>日時：{{ $data['datetime'] }}</p>
<p>人数：{{ $data['people_counts'] }}</p>
<br>

{!!$data['course_content_html']!!}
<p>合計：{{ number_format($data['total_price'])}}円</p>

@if ($data['total_price'] == 0) 
    <p>お支払いが必要な項目はありません。</p>
@else
    @if ($data['is_payment_completed'])
        <p>お支払いは完了しています。</p>
    @else
        <div>
            <sapn style="color:#ff0000;">このメール送信の時点で予約はまだ完了してません。</span><br>
            <span style="color:#ff0000;">予約完了のためには、お支払いを完了する必要があります。</span>
        </div>
    @endif
@endif
<br>
{{$data['qrcode']}}
<br>
