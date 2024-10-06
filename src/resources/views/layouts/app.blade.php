<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Atte</title>
  <link href='https://fonts.googleapis.com/css?family=Inika' rel='stylesheet'>
  <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/common.css') }}" />
  @yield('css')
</head>

<body>
  <header class="header">
    <div class="header__inner">
      <div class="header-logo">
        <a  href="/">
          <h1>Atte</h1>
        </a>
      </div>
      @yield('utilities')
    </div>
  </header>

  <main class="main">
    @yield('content')
  </main>

  <footer  class="footer">
  	<p>Atte, Inc.</p>
  </footer>
</body>

</html>