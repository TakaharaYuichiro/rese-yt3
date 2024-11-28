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
                <form id="menu1--form" action="/" method="get">
                    @csrf
                    <button><span id="menu1--text">Home</span></button>
                </form>
            </li>
            <li>
                <form id="menu2--form" action="/register" method="get">
                    @csrf
                    <button><span id="menu2--text">Registration</span></button>
                </form>
            </li>
            <li>
                <form id="menu3--form" action="/login" method="get">
                    @csrf
                    <button><span id="menu3--text">Login</span></button>
                </form>
            </li>
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

    const userName = '<?php echo $profile['name']; ?>';
    if(userName === '') {

    } else {


        const menuForm2 = document.getElementById('menu2--form');
        const menuForm3 = document.getElementById('menu3--form');
        const menuText2 = document.getElementById('menu2--text');
        const menuText3 = document.getElementById('menu3--text');

        menuText2.innerHTML = "Logout";
        menuForm2.setAttribute('action', '/logout');
        menuForm2.setAttribute('method', 'post');
        menuText3.innerHTML = "My Page";
        menuForm3.setAttribute('action', '/mypage');
        
    }



</script>