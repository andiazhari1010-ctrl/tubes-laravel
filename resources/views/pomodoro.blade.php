<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Pomodoro Timer</title>
<link rel="stylesheet" href="{{ asset('css/pomodoro.css') }}" />
</head>
<body>

<div id="app">
  <header>Pomodoro timer</header>
  <nav>
    <button id="pomodoroBtn" class="active">Pomodoro</button>
    <button id="shortBreakBtn">Short Break</button>
    <button id="longBreakBtn">Long Break</button>
  </nav>
  <div id="timer">25:00</div>
  
  <div id="session-counter">Session: 0</div>

<div class="stats-container" style="margin-top: 20px; text-align: center; background: rgba(0,0,0,0.2); padding: 10px; border: 2px solid white;">
    <div style="font-size: 12px; margin-bottom: 5px;">HARI INI</div>
    <div style="display: flex; gap: 20px; justify-content: center;">
        <div>
            <span id="stat-minutes" style="font-size: 18px; font-weight: bold;">0</span>
            <div style="font-size: 10px;">Menit Fokus</div>
        </div>
        <div>
            <span id="stat-sessions" style="font-size: 18px; font-weight: bold;">0</span>
            <div style="font-size: 10px;">Sesi Selesai</div>
        </div>
    </div>
</div>
  
  <div class="controls-container">
    <button id="startStopBtn">START</button>
    <button id="resetBtn" title="Reset Timer">RESET</button>
    <button id="skipBtn" title="Skip Session">SKIP</button>
  </div>
</div>

<div class="bottom-menu">
    <a href="{{ url('/index') }}" class="menu-item">🏠 <span>Dashboard</span></a>
    <a href="{{ url('/pomodoro') }}" class="menu-item">🍅 <span>Pomodoro</span></a>
    <a href="{{ url('/notes') }}" class="menu-item">🗒️ <span>Notes</span></a>
    <a href="{{ url('/calendar') }}" class="menu-item">📅 <span>Calendar</span></a>
    <a href="{{ url('/info') }}" class="menu-item">ℹ️ <span>Info</span></a>
</div>


<script src="{{ asset('js/pomodoro.js') }}"></script>
</body>
</html>