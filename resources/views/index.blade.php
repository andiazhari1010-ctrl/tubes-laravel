<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pixel Dashboard</title>
  <script src="{{ asset('js/script.js') }}"></script>
  <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

  <header class="top-bar">
    
    <a class="icon-box logout-icon" id="logoutBtn" title="Logout">🚪</a>

    <a href="{{ url('/notification') }}" class="icon-box" title="Notifications">
        🔔 
        </a>

    <div class="search-container">
      <input type="text" id="searchInput" class="search-bar" placeholder="🔍 Search..." />
      <ul class="search-history" id="searchHistory"></ul>
    </div>

    <a href="{{ url('/add-friend') }}" class="icon-box" title="Add Friend">👥</a>
  </header>


  <div class="dashboard">
    <div id="heartLeft" class="heart">💚</div>

    <div class="avatar-section">
      <h2 class="welcome-text">Welcome, {{ Auth::user()->name }}!</h2>
      <div class="avatar-box floating">
        <img src="{{ asset('images/polo.jpg') }}" alt="Avatar" class="avatar">
      </div>
    </div>

    <div id="heartRight" class="heart">💚</div>
    
    <div class="widget calendar-widget" id="calendar"></div>
  </div>

  <div class="top-clock" id="clock"></div>

  <div class="bottom-menu">
    <a href="{{ url('/index') }}" class="menu-item active">🏠 <span>Dashboard</span></a>
    <a href="{{ url('/pomodoro') }}" class="menu-item">🍅 <span>Pomodoro</span></a>
    <a href="{{ url('/notes') }}" class="menu-item">📝 <span>Notes</span></a>
    <a href="{{ url('/calendar') }}" class="menu-item">📅 <span>Calendar</span></a>
    <a href="{{ url('/info') }}" class="menu-item">ℹ️ <span>Info</span></a>
  </div>

  <div id="logoutPopup" class="logout-popup hidden">
    <div class="popup-box">
      <h3>Log Out</h3>
      <p>Are you sure you want to exit?</p>

      <div class="popup-actions">
        <a href="{{ url('/logout') }}" class="popup-btn yes">Yes, Logout</a>
        
        <button id="logoutCancel" class="popup-btn cancel">Cancel</button>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
        const logoutBtn = document.getElementById('logoutBtn');
        const logoutPopup = document.getElementById('logoutPopup');
        const logoutCancel = document.getElementById('logoutCancel');

        // Buka Popup
        logoutBtn.addEventListener('click', () => {
            logoutPopup.classList.remove('hidden');
        });

        // Tutup Popup
        logoutCancel.addEventListener('click', () => {
            logoutPopup.classList.add('hidden');
        });

        // Jam Digital Sederhana
        setInterval(() => {
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', { hour12: false });
            const clockEl = document.getElementById('clock');
            if(clockEl) clockEl.innerText = timeString;
        }, 1000);
    });
  </script>

  </body>
</html>