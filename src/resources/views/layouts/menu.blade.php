<div class="form-header__menu" id="form-header__menu">
    <input type="checkbox" id="menu-btn">
    <label for="menu-btn" class="form-header__menu--humberger" >
        <div class="humberger-line"></div>
        <div class="humberger-line"></div>
        <div class="humberger-line"></div>
    </label>
    
    <div class="form-header__menu--text">Rese</div>

    <ul class="menu1" id="menu1">
        <li><a href="/">Home</a></li>
        <li><a href="/register">Registration</a></li>
        <li><a href="/login">Login</a></li>
    </ul>
</div>

<script>


    // メニューが開いている時に、メニュー領域の外側をクリックしたとき、メニューを閉じる
    const menuBtn = document.getElementById('menu-btn');
    document.addEventListener('click', (e) => {
        if(!e.target.closest('#form-header__menu')) {
            if(menuBtn.checked) {
                menuBtn.checked = false;
            }
        } 
    })
</script>