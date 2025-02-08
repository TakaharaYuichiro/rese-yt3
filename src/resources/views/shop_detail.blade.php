@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/shop_detail.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/menu.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/common/header.css') }}" />
@endsection

@section('content')
<div class="main-container">
    @include('layouts.header', ['pageTitle'=>''])

    <div class="message-container">
        @if(session('message'))
            <div class="message-container--success">{{session('message')}}</div>
        @endif

        @if($errors->any())
            <div class="message-container--danger">
                <ul >
                    @foreach ($errors->all() as $error)
                        <li >{{$error}}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <div class="sub-container">
        <div class="content-container content-containerA">
            <div class="shop-detail">
                @isset($shop)
                    <div class="shop-detail__name">
                        <button class="shop-detail__back-button">
                            <a href="/"><</a>
                        </button>

                        <div class="shop-detail__name-text">{{$shop['name']}}</div>

                        <?php 
                            $favorite = false;
                            if (isset($my_evaluation)) {
                                $favorite = $my_evaluation['favorite'];
                            }
                        ?>

                        <form class="shop-detail__favorite-form" action="/favorite" method="post">
                            @csrf
                            <input type="hidden" name="shop_id" value="{{$shop['id']}}">
                            <button class="shop-detail__favorite-button" type="submit">
                                <span class="material-icons" style="color: {{ $favorite? 'red' :'lightgray';}}">
                                    favorite
                                </span>
                            </button>
                        </form>
                    </div>

                    @if($shop['image_filename']!="")
                        <?php
                            $image_storage = config('const.image_storage');
                            $disk = Storage::disk($image_storage);
                            $img_obj = null;
                            $file_name = 'shop_imgs/'. $shop['image_filename'];
                            if ($disk->exists($file_name)) {
                                $img_obj = $disk -> url($file_name);
                            } 
                        ?>
                        <img src="{{ $img_obj }}">
                    @else
                        <img src="{{asset('storage/shop_imgs/test_img/noimage.png')}}">
                    @endif
                    
                    <div class="shop-detail__content">
                        <span>{{'#'. $prefName}}</span>
                        <span>{{'#'. $shop['genre']['genre']}}</span>
                    </div>
                    <div class="shop-detail__content">
                        <span>{{$shop['content']}}</span>
                    </div>
                @endisset
            </div>
        </div>

        <?php
            $is_new_reservation = true;     // 新規予約と予約変更の両方に対応するためのフラグ
            if ($reservation) {
                if ($reservation['id'] > 0) {
                    $is_new_reservation = false;
                }
            }

            $isChangeable = true;  // 本日の日付と予約日の関係によって予約変更可否を決めるフラグ
            if($reservation) {
                if ($reservation['booked_datetime'] >  date('Y-m-d H:i:s', mktime(23,59,59))) {
                    $isChangeable = true;
                } else {
                    $isChangeable = false;  // 予約済みで、予約日が本日(またはそれ以前)なら変更不可とする
                }
            }
        ?>

        <div class="content-container content-containerB">
            <form class="reservation" action="/confirm_reservation" method="post" >
                @csrf
                @if(!$is_new_reservation)
                    <div class="reservation__inputs">
                        <div class="reservation__title">
                            <span>現在の予約内容</span>
                        </div> 

                        <div class="reservation__content">
                            <div class="reservation__content--title"><span>日付</span></div> 
                            <div class="reservation__content--current-value"><span>{{date('Y/m/d', strtotime($reservation['booked_datetime'])) }}</span></div>
                        </div>
                        <div class="reservation__content">
                            <div class="reservation__content--title"><span>開始時刻</span></div> 
                            <div class="reservation__content--current-value"><span>{{date('H:i', strtotime($reservation['booked_datetime'])) }}</span></div>
                        </div>
                        <div class="reservation__content">
                            <div class="reservation__content--title"><span>予約人数</span></div> 
                            <div class="reservation__content--current-value"><span>{{$reservation['people_counts']. '名' }}</span></div>
                        </div>
                        <div class="reservation__course">
                            <div class="reservation__course--title"><span>コースメニュー予約内容</span></div> 
                            <?php $counter = 0; ?>
                            @foreach($reservation['reserved_courses'] as $reservationCourse)
                                @if ($reservationCourse['quantity']>0)
                                    <?php $counter += $reservationCourse['quantity']; ?>
                                    <div class="reservation__content--message">
                                        <span>{{'・'. $reservationCourse['course']['name'].'(' . $reservationCourse['price_as_of_reservation'] .'円)×'. $reservationCourse['quantity']. '名分' }}</span>
                                    </div>
                                @endif
                            @endforeach

                            @if ($counter==0)
                                <div class="reservation__content--message">
                                    <span>予約しているコースメニューはありません</span>
                                </div>
                            @else
                                <div class="reservation__content--message">
                                    <div class="reservation__content--title"><span>合計金額</span></div> 
                                    <div class="reservation__content--title"><span>{{ number_format($reservation['total_price']).'円' }}</span></div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
                
                <div class="reservation__inputs">
                    <div class="reservation__title">
                        <span>
                            @if ($is_new_reservation) 新規予約 @else 変更後の予約内容 @endif
                        </span>
                    </div> 
                    
                    <div class="reservation__content">
                        <div class="reservation__content--title"><span>日付</span></div> 
                        <input 
                            type="date" name="date" {{$isChangeable? '': 'disabled'}}
                            value="{{ old('date', !$reservation? date('Y-m-d', strtotime('1 day')): date('Y-m-d', strtotime($reservation['booked_datetime']))) }}">
                    </div>
                    @if ($errors->has('date'))
                        <div class="reservation__inputs--error-message"><span>{{$errors->first('date')}}</span></div>
                    @endif 

                    <div class="reservation__content">
                        <div class="reservation__content--title"><span>開始時刻</span></div> 
                        <select name="start_time" {{$isChangeable? '': 'disabled'}}>
                            @for ($hour = 15; $hour < 23; $hour++)
                                @for ($minute = 0; $minute <=59; $minute+=30)
                                    {{ $strTime = sprintf('%02d', $hour). ':'. sprintf('%02d', $minute)}}
                                    <option 
                                        value="{{$strTime}}" 
                                        @if (!$reservation)
                                            @if($strTime == old('start_time'))
                                                selected
                                            @endif
                                        @else
                                            @if($strTime == old('start_time', date('H:i', strtotime($reservation['booked_datetime']) )))
                                                selected
                                            @endif
                                        @endif
                                    >{{$strTime}}</option>
                                @endfor
                            @endfor
                        </select>
                    </div>

                    <div class="reservation__content">
                        <div class="reservation__content--title"><span>予約人数</span></div> 
                        <select name="people_counts" {{$isChangeable? '': 'disabled'}}>
                            @for ($count = 1; $count < 21; $count++)
                                <option 
                                    value="{{$count}}"
                                    @if (!$reservation)
                                        @if($count == old('people_counts'))
                                            selected
                                        @endif
                                    @else
                                        @if($count == old('people_counts', $reservation['people_counts'] )))
                                            selected
                                        @endif
                                    @endif
                                >{{$count. "名"}}</option>
                            @endfor
                        </select>
                    </div>

                    <div class="reservation__course">
                        @if ($is_new_reservation)
                            <div class="reservation__course--title"><span>予約可能なコースメニュー</span></div> 
                            
                            @if (count($courses) > 0)
                                <table class="reservation__course--table">
                                <tr>
                                    <th class="reservation__course--header__name">コース名</th>
                                    <th class="reservation__course--header__price">金額(1名分)</th>
                                    <th class="reservation__course--header__quantity">数量</th>
                                </tr>
                                
                                @for($i=0; $i<count($courses); $i++)
                                    <tr>
                                        <td class="reservation__course--data__name"> {{ $courses[$i]->name }} </td>
                                        <td class="reservation__course--data__price">{{ number_format($courses[$i]->price) }}円</td>
                                        <td class="reservation__course--data__quantity">
                                            <?php
                                                $quantity = 0;
                                                $targetCouseId = $courses[$i]->id;
                                                if ($reservation) {
                                                    $reservatinCourses = $reservation['courses'];
                                                    foreach ($reservatinCourses as $reservatinCourse) {
                                                        if ($reservatinCourse['id'] == $targetCouseId) {
                                                            $quantity = $reservatinCourse['quantity'];
                                                            break;
                                                        }
                                                    }
                                                }
                                            ?>
                                            <input type="tel" name="quantity[{{$i}}]" class="reservation__course--input-quantity" 
                                                value="{{ old('quantity.'.$i  ,$quantity) }}">
                                            <span>名分</span>
                                        </td>
                                    </tr>
                                    <input type="hidden" name="course_id[{{$i}}]" value="{{$courses[$i]->id}}">
                                @endfor
                                </table>
                            @else
                                <div class="reservation__content--message"><span>予約可能なコースメニューはありません</span></div>
                            @endif

                            @for($i=0; $i<count($courses); $i++)
                                @if ($errors->first("quantity.$i"))
                                    <div class="reservation__inputs--error-message"><span>{{$errors->first("quantity.$i")}}</span></div>
                                @endif
                            @endfor

                            <div class="reservation__content--message">
                                <div class="reservation__content--title"><span>合計金額</span></div> 
                                <div id="total-price"></div>
                            </div>
                        
                        @else
                            <div class="reservation__course--title">
                                <span>(コースメニューの変更はできません)</span>
                            </div> 

                            @for($i=0; $i<count($courses); $i++)
                                <?php
                                    $quantity = 0;
                                    $targetCouseId = $courses[$i]->id;
                                    if ($reservation) {
                                        $reservatinCourses = $reservation['reserved_courses'];
                                        foreach ($reservatinCourses as $reservatinCourse) {
                                            if ($reservatinCourse['id'] == $targetCouseId) {
                                                $quantity = $reservatinCourse['quantity'];
                                                break;
                                            }
                                        }
                                    }
                                ?>
                                <input type="hidden" name="quantity[{{$i}}]" value="{{ $quantity }}">
                                <input type="hidden" name="course_id[{{$i}}]" value="{{$courses[$i]->id}}">
                            @endfor
                        @endif
                    </div>
                </div>

                <input type="hidden" name="shop_id" value="{{$shop['id']}}">
                <input type="hidden" name="exists_reservation_id" value="{{$reservation? $reservation['id']: 0}}">
            
                <div class="reservation__bottom-margin"></div>
                
                @if ($profile['name']=='')
                    <div class="reservation__button-container--not-logged">
                        <span>予約するにはログインが必要です</span>
                    </div>
                @elseif ($isChangeable == false) 
                    <div class="reservation__button-container--not-logged">
                        <span>予約当日のため変更できません</span>
                    </div>
                @else
                    <div class="reservation__button-container">
                        @if ($is_new_reservation)
                            <button class="reservation__button--submit">予約内容確認</button>
                        @else
                            <button class="reservation__button--submit">変更内容確認</button>
                            <button class="reservation__button--delete" id="reservation__button--delete" name="delete" value="false">この予約を削除</button>
                        @endif
                    </div>
                @endif
            </form>
        </div>

        <div class="content-container content-containerC">
            <div class="evaluation">
                <div class="evaluation__title">
                    この店舗の評価
                </div> 
                <div class="evaluation__summary">
                        @for ($i=0; $i<5; $i++)
                            <span class="material-icons evaluation-stars" 
                                  style="color: {{ (round($evaluation_summary['score']) < ($i+1))? 'lightgray': '#FAA422' }}">star
                            </span>
                        @endfor
    
                    <div class="evaluation__summary__score">
                        {{ sprintf('%.1f', round($evaluation_summary['score'], 1)) }}
                    </div>
                    <div class="evaluation__summary__reviewer-counts">
                        {{'('.$evaluation_summary['reviewer_counts'] . '件)' }}
                    </div>
                </div>
                @foreach ($evaluations as $evaluation) 
                    <div class="evaluation__content">
                        <div class="evaluation__content__comment">
                            {{ $evaluation->comment }}
                        </div>
                        <div class="evaluation__content__value">
                            @for ($i=0; $i<5; $i++)
                                <span class="material-icons evaluation__content__stars" 
                                    style="color: {{ (round($evaluation->score) < ($i+1))? 'lightgray': '#FAA422' }}">star
                                </span>
                            @endfor
                            <div class="evaluation__content__created-at">
                                {{ $evaluation->created_at->format('Y-m-d H:i') }}
                            </div>
                        </div>
                        
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
    const courses = JSON.parse('<?php echo $courses; ?>');
    let prices = [];
    for (let course of courses) {
        if (course['enable'] == 1) {
            prices.push(course['price']);
        }
    }

    const isNewReservation = '<?php echo $is_new_reservation; ?>';
    const isLogged = '<?php echo $profile['name']!=''; ?>';
    const isChangeable = '<?php echo $isChangeable; ?>';
    const quantityInputs = document.getElementsByClassName('reservation__course--input-quantity');

    for(let quantityInput of quantityInputs) {
        quantityInput.addEventListener('change', ()=>{
            showTotalPrice();
        });
    }

    showTotalPrice();   // ページ表示の際、まずは合計金額を計算して表示

    function showTotalPrice(){    
        if(isNewReservation == 1) {
            const totalPriceView = document.getElementById('total-price');
            let total = 0;
            for (let i=0; i<prices.length; i++) {
                total += Number(quantityInputs[i].value) * prices[i];
            }
            totalPriceView.innerHTML = `${total.toLocaleString()}円`;
        }
    }

    // 予約削除ボタンの処理。ログインしていなかったり、新規予約のときは削除ボタンを表示しない
    if ((isLogged==1) && (isChangeable==1) && (isNewReservation!=1)) {
        const reservationButtonDelete = document.getElementById("reservation__button--delete");
        reservationButtonDelete.addEventListener("click", () => {
            const answer = window.confirm("この予約を取り消してもよろしいですか？");
            if (answer) {
                reservationButtonDelete.value='true';
            }
            else {
                reservationButtonDelete.value='false';
            }
        });
    }
</script>

@endsection

