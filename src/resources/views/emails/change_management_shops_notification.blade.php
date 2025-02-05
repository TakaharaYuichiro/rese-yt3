<p>{{ $data['name'] }}さんの担当店舗情報が管理者によって以下の通り変更されました。</p>
<p>
    <span>変更後の担当店舗：</span><br>
    @foreach($data['after'] as $shopName)
        <span>&emsp;{{$shopName}}</span><br>
    @endforeach
</p>
<p>
    <span>追加された担当店舗：</span><br>
    @foreach($data['added'] as $shopName)
        <span>&emsp;{{$shopName}}</span><br>
    @endforeach
</p>
<p>
    <span>削除された担当店舗：</span><br>
    @foreach($data['removed'] as $shopName)
        <span>&emsp;{{$shopName}}</span><br>
    @endforeach
</p>