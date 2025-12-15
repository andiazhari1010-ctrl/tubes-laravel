<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Pixel Pastel</title>
  <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body class="login-bg">

  <div class="cloud"></div>
  <div class="cloud small"></div>

  <div class="login-container">
    <h1>🌿 Welcome!</h1>
    <p class="subtitle">Please login to continue</p>

    @if (session('success'))
      <div style="color: green; text-align: center; margin-bottom: 10px; font-size: 10px;">
          {{ session('success') }}
      </div>
    @endif

    @error('login_error')
        <div style="color: red; text-align: center; margin-bottom: 10px; font-size: 10px;">
            {{ $message }}
        </div>
    @enderror

    <form id="loginForm" action="{{ url('/login') }}" method="POST">
        @csrf
        <input name="username" type="text" id="username" placeholder="Username" required>
        <input name="password" type="password" id="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>

    <p class="switch-page">
      Belum punya akun?
      <a href="{{ url('/register') }}">Register</a>
    </p>
  </div>

 
</body>
</html>
