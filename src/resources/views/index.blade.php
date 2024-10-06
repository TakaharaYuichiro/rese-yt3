@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/index.css') }}" />
@endsection

@section('utilities')
  @include('layouts.utility')
@endsection

@section('content')
<div class="form-content">
  <!-- ステータス表示領域（上部） -->
  <div class="current-status__container">
    <div class="current-status current-status__div1">
      <div class="current-status__time">
        <div class="current-status__time--today"><span id="today"></span></div>
        <div class="current-status__time--realtime"><span id="realtime"></span></div>
      </div>
    </div>
    <div class="current-status current-status__div2">
      <div class="current-status__profile">
        <span id="user-name">{{$profile['name']}}</span>
        <span>{{'さん お疲れ様です！'}}</span>
      </div>
    </div>
  </div>

  <!-- 打刻用の4つのボタンの領域 -->
  <div class="atte-selecter">
    <form class="atte-selecter__form" action="/store" method="post">
      @csrf
      <input type="hidden" name="user_id" value="{{$profile->id}}"/>
      <input type="hidden" name="content_index" value="1"/>
      <div class="atte-selecter__button">
        <button {{ $data['workState']>0 ? 'disabled': '' }} >勤務開始</button>
      </div>
    </form>

    <form class="atte-selecter__form" action="/store" method="post">
      @csrf
      <input type="hidden" name="user_id" value="{{$profile->id}}"/>
      <input type="hidden" name="content_index" value="2"/>
      <input type="hidden" name="is_rest" value="{{$data['isRest']}}"/>
      <div class="atte-selecter__button">
        <button {{ $data['workState']!=1 ? 'disabled': '' }} >勤務終了</button>
      </div>
    </form>

    <form class="atte-selecter__form" action="/store" method="post">
      @csrf
      <input type="hidden" name="user_id" value="{{$profile->id}}"/>
      <input type="hidden" name="content_index" value="3"/>
      <div class="atte-selecter__button">
        <button {{ ($data['isRest'] || $data['workState']!=1)? 'disabled': ''}}>休憩開始</button>
      </div>
    </form>

    <form class="atte-selecter__form" action="/store" method="post">
      @csrf
      <input type="hidden" name="user_id" value="{{$profile->id}}"/>
      <input type="hidden" name="content_index" value="3"/>
      <div class="atte-selecter__button">
        <button {{ (!$data['isRest'] || $data['workState']!=1)? 'disabled': '' }}>休憩終了</button>
      </div>
    </form>
  </div>

  <!-- ステータス表示領域（下部） -->
  <div class="record-status">
    <table>
      <tr>
        <th>現在のステータス</th>
        <td>
          <span>
            @switch($data['workState'])
              @case(1)
                @if($data['isRest'])
                  休憩中
                @else
                  勤務中
                @endif
                @break;
              @case(2)
                勤務終了
                @break;
              @default
                勤務開始前
                @break
            @endswitch
          </span>
        </td>
      </tr>
      <tr>
        <th>本日の勤務開始時刻</th>
        <td>{{$data['startTime']}}</td>
      </tr>
      <tr>
        <th>休憩時間 合計</th>
        <td>{{$data['restTimeTotal']}}</td>
      </tr>
    </table>
  </div>

  <div class="optional-section">
    <form action="/reset_all" method="post" class="optional-section__form--reset">
      @csrf
      <input type="hidden" name="id" value="{{$profile->id}}">
      <button type="submit" onclick='showDeleteAllMessage()'>
          <span>本日の勤務データを全て削除</span> 
      </button>
    </form>

    <form action="/reset_end" method="post" class="optional-section__form--reset">
      @csrf
      <input type="hidden" name="id" value="{{$profile->id}}">
      <button type="submit" onclick='return confirm("勤務終了時刻のデータを削除してもよろしいですか？")'>
          <span>勤務終了時刻のみ削除</span> 
      </button>
    </form>
  </div>
</div>


<script>
  var lastDateTime = new Date();
  // const testHour = 20;  // リロードのテスト用
  // const testMin = 21; // リロードのテスト用
  // lastDateTime.setHours(lastDateTime.getHours() - testHour);  // リロードのテスト用
  // lastDateTime.setMinutes(lastDateTime.getMinutes() - testMin);   // リロードのテスト用

  // 現在時刻を表示する
  function showClock() {
    const days = ["日", "月", "火", "水", "木", "金", "土"];
    const nowTime = new Date();
    const nowYear = nowTime.getFullYear();
    const nowMonth = nowTime.getMonth() + 1; 
    const nowDate = nowTime.getDate(); 
    const nowDay = nowTime.getDay();
    const nowHour = nowTime.getHours();
    const nowMin  = nowTime.getMinutes();
    const nowSec  = nowTime.getSeconds();
    const date = `${nowYear}年 ${nowMonth}月 ${nowDate}日 (${days[nowDay]})`
    const time = `${('00' + nowHour).slice(-2)}:${('00' + nowMin).slice(-2)}:${('00' + nowSec).slice(-2)}`
    const msg = "現在時刻：" + nowHour + ":" + nowMin + ":" + nowSec;
    document.getElementById("today").innerHTML = date;
    document.getElementById("realtime").innerHTML = time;  
  }

  // 日付が変わった時、強制的にページを更新する
  function checkNewDay(){
    const currentDateTime = new Date();
    // currentDateTime.setHours(currentDateTime.getHours() - testHour);  // リロードのテスト用
    // currentDateTime.setMinutes(currentDateTime.getMinutes() - testMin);   // リロードのテスト用
    if (currentDateTime.getDay() !== lastDateTime.getDay()) {   // 曜日の変化でチェック
      location.reload();
    } 

    lastDateTime = currentDateTime;
  }

  // ページ読み込み直後に現在時刻を表示
  showClock();

  // その後、1秒毎にスクリプト実行
  setInterval(()=>{
    showClock();
    checkNewDay();
  }, 1000);


  function showDeleteAllMessage(){
    const userName = '<?php echo $profile['name']; ?>';
    return confirm(`${userName}さんの本日の勤務データを全て削除してもよろしいですか？`);
  }
</script>
@endsection

