<div class="header-utilities">
    <form class="utilitiy-form" action="/" method="get">
        @csrf
        <button class="utility-button"><span>勤務登録</span></button>
    </form>
    <form class="utilitiy-form" action="/attendance" method="get">
        @csrf
        <button class="utility-button"><span>日付別</span></button>
    </form>
    <form class="utilitiy-form" action="/personal" method="get">
        @csrf
        <button class="utility-button"><span>会員別</span></button>
    </form>
    <form class="utilitiy-form" action="/logout" method="post">
        @csrf
        <button class="utility-button"><span>ログアウト</span></button>
    </form>
</div>