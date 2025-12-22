<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Pomodoro Timer</title>

<link rel="preload" as="image" href="{{ asset('images/pxl-bg.jpg') }}">
<link rel="preload" as="image" href="{{ asset('images/aft-bg.png') }}">
<link rel="preload" as="image" href="{{ asset('images/night.jpg') }}">

<link rel="stylesheet" href="{{ asset('css/pomodoro.css') }}" />
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="pomodoro-page">

<div id="toast-container"></div>

<div class="header-top-center">
    <button id="btnReport" class="pixel-btn-small">📊 Report</button>
    <button id="btnSetting" class="pixel-btn-small">⚙️ Setting</button>
</div>

<div id="app">
  <header>Pomodoro timer</header>
  
  <nav>
    <button id="pomodoroBtn" class="active">Pomodoro</button>
    <button id="shortBreakBtn">Short Break</button>
    <button id="longBreakBtn">Long Break</button>
  </nav>
  
  <div class="character-container">
      <img id="pomo-character" src="{{ asset('images/char-focus.gif') }}" alt="Character">
  </div>

  <div id="timer">00:20</div>
  <div class="stats-container">
    <div class="stats-label">HARI INI (MODE DEMO)</div>
    <div class="stats-row">
        <div>
            <span id="stat-seconds">0</span>
            <div class="stats-desc">Seconds Focused</div>
        </div>
        <div>
            <span id="stat-sessions">0</span>
            <div class="stats-desc">Sesi Selesai</div>
        </div>
    </div>
  </div>
  
  <div class="controls-container">
    <button id="startStopBtn">START</button>
    <button id="resetBtn">RESET</button>
    <button id="skipBtn">SKIP</button>
  </div>
</div>

<div id="modalReport" class="modal-overlay hidden">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Report</h3>
            <button class="close-btn" onclick="closeModal('modalReport')">X</button>
        </div>
        <div class="modal-tabs">
            <button class="tab-btn active" onclick="switchTab('summary')">Summary</button>
            <button class="tab-btn" onclick="switchTab('ranking')">Ranking</button>
        </div>
        
        <div id="tab-summary" class="tab-content active">
            <div class="summary-stats">
                <div class="stat-card">
                    <span id="rep-seconds">0</span>
                    <small>Seconds Focused</small>
                </div>
                <div class="stat-card">
                    <span id="rep-session-passed">0</span>
                    <small>Session Passed</small>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="activityChart"></canvas>
            </div>
        </div>

        <div id="tab-ranking" class="tab-content">
            <p style="text-align: center; font-size: 10px; margin-bottom: 10px;">Top Users This Week</p>
            <ul id="ranking-list" class="ranking-list"></ul>
        </div>
    </div>
</div>

<div id="modalSetting" class="modal-overlay hidden">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Setting</h3>
            <button class="close-btn" onclick="closeModal('modalSetting')">X</button>
        </div>
        <div class="setting-content">
            <div class="setting-item">
                <label>Pomodoro</label>
                <input type="number" id="set-pomo" value="20">
            </div>
            <div class="setting-item">
                <label>Short Break</label>
                <input type="number" id="set-short" value="10">
            </div>
            <div class="setting-item">
                <label>Long Break</label>
                <input type="number" id="set-long" value="15">
            </div>
            <div class="setting-item">
                <label>Long Break Interval</label>
                <input type="number" id="set-interval" value="4">
            </div>
            <div class="setting-toggle">
                <label>Auto Start Breaks?</label>
                <input type="checkbox" id="set-auto-break">
            </div>
            <div class="setting-toggle">
                <label>Auto Start Pomodoro?</label>
                <input type="checkbox" id="set-auto-pomo">
            </div>
        </div>
        <div class="modal-footer">
            <button onclick="saveSettings()">OK</button>
        </div>
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