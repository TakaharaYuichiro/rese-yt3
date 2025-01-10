<p>{{ $data['user_name']. " 様"}}</p>
<p>本メールは予約当日にご確認のためにお送りしています。</p>
<br>

<p>---予約内容---</p>
<p>店名：{{ $data['shop_name'] }}</p>
<p>日時：{{ $data['datetime'] }}</p>
<p>人数：{{ $data['people_counts'] }}</p>
<br>

{{$data['qrcode']}}
<br>