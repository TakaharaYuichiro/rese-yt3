@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/attendance.css') }}" />
@endsection

@section('utilities')
  @include('layouts.utility')
@endsection

@section('content')
<div class="main-content">
  <div class="date-section">
    <div class="date-section__center">
      <form action="/attendance" method="get">
        @csrf
        <?php 
          $prevDate = date('Y-m-d', strtotime('-1 day', strtotime($condition['targetDate']) ));
        ?>
        <input type="hidden" name="targetDate" value="{{ $prevDate }}" />
        <button class="date-section__button prev-button" type="submit"></button>
      </form>

      <div>{{ $condition['targetDate']}}</div>

      <form action="/attendance" method="get">
        @csrf
        <?php 
          $nextDate = date('Y-m-d', strtotime('1 day', strtotime($condition['targetDate']) ));
        ?>
        <input type="hidden" name="targetDate" value="{{ $nextDate }}" />
        <button class="date-section__button next-button" type="submit"></button>
      </form>
    </div>

    <div class="date-section__select-date">
        <a href="#selectDate">日付選択</a>
    </div>
  </div>

  <div class="list-section">
    <table class="atte-table">
      <tr>
          <th class="atte-table__header atte-table__header--name">名前</th>
          <th class="atte-table__header atte-table__header--time">勤務開始</th>
          <th class="atte-table__header atte-table__header--time">勤務終了</th>
          <th class="atte-table__header atte-table__header--time">休憩時間</th>
          <th class="atte-table__header atte-table__header--time">勤務時間</th>
      </tr>

      @foreach($data as $attendance)
      <tr class="atte-table__row">
          <td><span>{{ $attendance['name']}}</span></td> 
          <td><span>{{ $attendance['startTime']}}</span></td> 
          <td><span>{{ $attendance['endTime']}}</span></td> 
          <td><span>{{ $attendance['restTimeTotal']}}</span></td> 
          <td><span>{{ $attendance['workingTime']}}</span></td> 
      </tr>
      @endforeach
    </table>
  </div> 

  <div class="optional-section">
    <div class="optional-section__pagination">
        {{ $data->appends(request()->query())->links('vendor.pagination.original_pagination_view') }}
    </div>
  </div>
</div>

<!-- 日付選択用のモーダルウインドウ -->
<div class="modal" id="selectDate">
  <a href="#!" class="modal-overlay"></a>
  <div class="modal__inner">
    <div class="modal__header">
        <a href="#" class="modal__close-btn"></a>
    </div>
    <div class="modal__content">
      <form class="modal__detail-form" action="/attendance" method="get">
        @csrf
        <p class="modal-form__message">日付を選択してください</p>
        <div class="modal-form__input">
          <input  type="date" id="selectedDate" name="targetDate" value='{{ date("Y-m-d") }}'>
        </div>
        <div  class="modal-form__submit-button">
          <button type="submit">OK</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection
