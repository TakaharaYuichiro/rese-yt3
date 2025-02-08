@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/evaluation.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/menu.css') }}" />
@endsection

@section('utilities')
  @include('layouts.utility')
@endsection

@section('content')
<div class="main-container">
    <div class="header-container">
        <div class="header-container__1st-block">
            @include('layouts.menu')
        </div>
        <div class="header-container__2nd-block">
            <div class="user-name__content">
                <span class="material-icons">person</span>
                <span id="user-name__text">
                    {{$profile['name']==''? 'ログインしていません': $profile['name']. 'さん' }}
                </span>
            </div>
        </div>
    </div>

    <div class="sub-container">
        <div class="content-container">
            <div class="shop-detail">
                @isset($shop)
                    <div class="shop-detail__name">
                        <span>{{$shop['name']}}</span>
                    </div>
                    
                    <div class="shop-detail__main-block">
                        @if($shop['image_filename']!="")
                            <img src="{{ asset('storage/'. $shop['image_filename']) }}">
                        @else
                            <img src="{{ asset('storage/shop_imgs/test_img/noimage.png') }}">
                        @endif
                        
                        <div class="shop-detail__content--container">
                            <div class="shop-detail__content">
                                <span>{{'#'. $prefCode}}</span>
                                <span>{{'#'. $shop['genre']['genre']}}</span>
                            </div>
                            <div class="shop-detail__content">
                                <span>{{$shop['content']}}</span>
                            </div>
                        </div>
                    </div>
                @endisset
            </div>
        </div>

        <?php 
            $evaluation_score = 0;  
            if ($evaluation) {
                $evaluation_score = $evaluation->score;
            }
        ?>

        <div class="content-container">
            <form class="evaluation" action="/evaluation/store" method="post">
                @csrf   
                <div class="content-message">
                    <span>
                        この店舗を評価してください
                    </span>
                </div> 
                <div class="evaluation__content">
                    @for ($i=0; $i<5; $i++)
                        <button class="star-button" type='button'>
                            <div>
                                
                                <span class="material-icons star-text--icon" style="color: {{ ($evaluation->score < ($i+1))? 'lightgray': 'red' }}">star</span>
                            </div>
                            <div><span class="star-text--point">{{$i+1}}点</span></div>
                        </button>
                    @endfor
                </div>
                <div class="evaluation__content">
                    <textarea class="evaluation__content--comment" name="evaluation_comment" maxlength="255" rows="5" cols="50" placeholder="評価の理由などをここに記入してください"></textarea>
                </div>
            
                <input type="hidden" name="evaluation_id" value="{{$evaluation->id}}">
                <input type="hidden" id="star-value" name="evaluation_score" value="0">
                <div class="evaluation__content">
                    <button class="evaluation__submit-button">
                        送信
                    </button>
                </div>
            </form>
        </div>

        <div class="content-container">
            <div class="content-title">
                <span>過去の評価結果</span>
            </div> 
            @if($is_empty_evaluation)
                <div class="empty-message">過去の評価結果はありません</div>
            @else
                <div class="evaluation-item">
                    <div class="evaluation-item--name"><span>評価点</span></div>
                    <div class="evaluation-item--value reservation-header__evaluation--score">
                        @for ($i=0; $i<5; $i++)
                            <span class="material-icons evaluation-score" style="color: {{ ($evaluation->score < ($i+1))? 'lightgray': '#FAA422' }}">star</span>
                        @endfor
                    </div>
                </div>
                <div class="evaluation-item">
                    <div class="evaluation-item--name"><span>コメント</span></div>
                    <div class="evaluation-item--value"><span>{{ $evaluation->comment}}</span></div>
                </div>
                <div class="evaluation-item">
                    <div class="evaluation-item--name"><span>評価日時</span></div>
                    <div class="evaluation-item--value"><span>{{ $evaluation->updated_at}}</span></div>
                </div>
            @endif
        </div>

        <div class="content-container">
            <div class="content-title">
                <span>この店舗の利用履歴</span>
            </div> 
            @if (count($reservation_histories) == 0)
                <div class="empty-message">記録がありません</div>
            @else
                <table class="reservation-table">
                    <tr>
                        <th class="reservation-table__header--date">日付</th>
                        <th class="reservation-table__header--start-time">時刻</th>
                        <th class="reservation-table__header--people-counts">人数</th>
                    </tr>    
                    @foreach($reservation_histories as $reservation)
                        <tr>
                            <td>
                                <?php 
                                    $week = ['日', '月', '火', '水', '木', '金', '土'];
                                    $timestamp = strtotime($reservation -> booked_datetime);
                                    $date = date('Y年m月d日', $timestamp);
                                    $day = $week[date('w', $timestamp)];
                                    echo $date. '('. $day. ')';
                                ?>
                            </td>
                            <td>{{ date('H:i', strtotime($reservation -> booked_datetime)). '〜' }}</td>
                            <td>{{ $reservation['people_counts']. '名' }}</td>
                        </tr>
                    @endforeach
                </table>    
            @endif
        </div>
    </div>
</div>

<script>
    const starButtons = document.getElementsByClassName('star-button');
    for(let i=0; i<5; i++) {
        starButtons[i].addEventListener('click', () => {
            for(let j=0; j<5; j++) {
                const children = starButtons[j].getElementsByClassName('star-text--icon');
                for(let icon of children) {
                    icon.style.color='lightgray';
                }
            }
            for(let j=0; j<=i; j++) {
                const children = starButtons[j].getElementsByClassName('star-text--icon');
                for(let icon of children) {
                    icon.style.color='red';
                }
            }
            const starValue = document.getElementById('star-value');
            starValue.value = i+1;
        });
    }
</script>

@endsection

