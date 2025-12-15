<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Notifications</title>
  
  <link rel="stylesheet" href="{{ asset('css/style.css') }}" />
  
  <link rel="stylesheet" href="{{ asset('css/notification.css') }}" />
</head>
<body>

  <header class="top-bar">
    <a href="{{ url('/') }}" class="icon-box" aria-label="Back">⬅</a>
    <h1 class="page-title">Notifications</h1>
    <div class="spacer"></div>
  </header>

  <main style="flex:1; display:flex; align-items:center; justify-content:center; padding:18px;">
    <div style="width:100%; max-width:560px;">
      <div class="info-card">
        <h3 class="notif-title">Recent activity</h3>

        <ul class="notif-list" id="notifListContainer">
        </ul>

        <div class="notif-buttons">
          <button id="markAllRead" class="add-btn">Mark all as read</button>
          <a href="{{ url('/') }}" class="add-btn back-btn">Back</a>
        </div>
      </div>
    </div>
  </main>

  <footer class="bottom-menu">
    <a href="{{ url('/index') }}" class="menu-item">🏠 <span>Dashboard</span></a>
    <a href="{{ url('/pomodoro') }}" class="menu-item">🍅 <span>Pomodoro</span></a>
    <a href="{{ url('/notes') }}" class="menu-item">📝 <span>Notes</span></a>
    <a href="{{ url('/calendar') }}" class="menu-item">📅 <span>Calendar</span></a>
    <a href="{{ url('/info') }}" class="menu-item">ℹ️ <span>Info</span></a>
  </footer>

  <script src="{{ asset('js/notification.js') }}"></script>
</body>
</html>