<div id="menu-container__parent">
    <input type="checkbox" id="humberger-button--check-box">
    <label for="humberger-button--check-box" class="humberger-button--label" >
        <div class="humberger-line"></div>
        <div class="humberger-line"></div>
        <div class="humberger-line"></div>
    </label>
    
    <div class="menu-title"><span>Rese</span></div>

    <div class="menu-container">
        <ul>
            <li>
                <form action="/" method="get">
                    <button><span>Home</span></button>
                </form>
            </li>
            @guest
                <li>
                    <form action="/register" method="get">
                        <button><span>Registration</span></button>
                    </form>
                </li>
                <li>
                    <form action="/login" method="get">
                        <button><span>Login</span></button>
                    </form>
                </li>
            @endguest
            @auth                
                <li>
                    <form action="/logout" method="post">
                        @csrf
                        <button><span>Logout</span></button>
                    </form>
                </li>
                <li>
                    <form action="/mypage" method="get">
                        <button><span>My Page</span></button>
                    </form>
                </li>
            @endauth
            @can('manager-higher')
                <li>
                    <form action="/manager_page" method="get">
                        <button><span id="menu4--text">店舗代表者マイページ</span></button>
                    </form>
                </li>
            @endcan
            @can('admin-higher')
                <li>
                    <form action="/admin" method="get">
                        <button><span>管理者ページ</span></button>
                    </form>
                </li>
            @endcan
        </ul>
    </div>
</div>

<script>
    // メニューが開いている時に、メニュー領域の外側をクリックしたとき、メニューを閉じる
    const menuBtn = document.getElementById('humberger-button--check-box');
    document.addEventListener('click', (e) => {
        if(!e.target.closest('#menu-container__parent')) {
            if(menuBtn.checked) {
                menuBtn.checked = false;
            }
        } 
    });
</script>