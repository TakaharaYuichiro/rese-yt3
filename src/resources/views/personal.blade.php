@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/personal.css') }}" />
@endsection

@section('utilities')
  @include('layouts.utility')
@endsection

@section('content')
<div class="main-content">
  <div class="user-section">

    <div class="user-section__message">
      <div class="user-section__name">
        <span id="user-name">{{$condition['name']}}</span>
        <span>{{'さんの直近1ヶ月の勤務データ'}}</span>
      </div>
    </div>

    <div class="user-section__select">
      <a href="#selectUser">会員一覧</a>
    </div>
  </div>

  <div class="list-section">
    <?php $week = ['日', '月', '火', '水', '木', '金', '土']; ?>

    <table class="atte-table">
      <tr>
        <th class="atte-table__header atte-table__header--name">日付</th>
        <th class="atte-table__header atte-table__header--time">勤務開始</th>
        <th class="atte-table__header atte-table__header--time">勤務終了</th>
        <th class="atte-table__header atte-table__header--time">休憩時間</th>
        <th class="atte-table__header atte-table__header--time">勤務時間</th>
      </tr>

      @foreach($data as $attendance)
      <tr class="atte-table__row">
        <?php
          $day = date('w', strtotime($attendance['date'])); 
          $dateStr = $attendance['date'] . ' ('. $week[$day]. ')';
        ?>
        <td><span>{{ $dateStr }}</span></td> 
        <td><span>{{ $attendance['startTime'] }}</span></td> 
        <td><span>{{ $attendance['endTime'] }}</span></td> 
        <td><span>{{ $attendance['restTimeTotal'] }}</span></td> 
        <td><span>{{ $attendance['workingTime'] }}</span></td> 
      </tr>
      @endforeach
    </table>
  </div> 

  <div class="optional-section">
    <div class="optional-section__pagination">
        {{-- $data->appends(request()->query())->links('vendor.pagination.original_pagination_view') --}}
        {{ $data->links('vendor.pagination.original_pagination_view') }}
    </div>
  </div>
</div>

<!-- ユーザー選択用のモーダルウインドウ -->
<div class="modal" id="selectUser">
  <a href="#!" class="modal-overlay"></a>
  <div class="modal__inner">
    <div class="modal__header">
      <div class="modal__title">
        <h2>会員一覧</h2>
      </div>
      <div class="modal__close-btn__parent">
        <a href="#" class="modal__close-btn"></a>
      </div>
    </div>


    <div class="users-list-section">
      <table class="users-list-section__table">
        <tbody class="users-list-section__table--body"></div>
          @foreach($users as $user)
          <tr>
            <td class="users-list-section__table--name"><span>{{ $user['name'] }}</span></td> 
            <td class="users-list-section__table--button">
              <form action="/personal" method="get">
                @csrf
                <input type="hidden" name='user_id' value="{{$user['id']}}">
                <button>勤務記録</button>
              </form>
            </td> 
          </tr>
          @endforeach
        </tbody>    
      </table>
    </div>

  </div>
</div>

@endsection
