<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register - Pixel Pastel</title>
  <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body class="login-bg">

  <div class="cloud"></div>
  <div class="cloud small"></div>

  <div class="login-container">
    <h1>🌿 Create Account</h1>
    <p class="subtitle">Register to continue</p>

    @if ($errors->any())
        <div style="color: red; text-align: center; margin-bottom: 10px; font-size: 10px;">
            {{ $errors->first() }}
        </div>
    @endif

    <form id="registerForm" action="{{ url('/register') }}" method="POST">
      @csrf <input name="name" type="text" placeholder="Full Name" required style="margin-bottom: 10px;">

      <input name="username" id="regUsername" placeholder="Username" required>
      <input name="password" id="regPassword" type="password" placeholder="Password" required>
    
      <button type="submit">Register</button>
    </form>

    <p class="switch-page">
      Sudah punya akun?
      <a href="{{ url('/login') }}">Login</a>
    </p>
  </div>

  
</body>
</html>
