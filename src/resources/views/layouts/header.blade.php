<div class="header-container">
    <div class="header-container__1st-block">
        @include('layouts.menu')
    </div>
    <div class="header-container__2nd-block">
        <div class="page-title">
            <span>{{$pageTitle}}</span>
        </div>
        <div class="user-name__content">
            <span class="material-icons">person</span>
            <span id="user-name__text">
                &nbsp;{{$profile['name']==''? 'ログインしていません': $profile['name']. 'さん' }}
            </span>
        </div>
    </div>
</div>