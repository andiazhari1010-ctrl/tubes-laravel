<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Note</title>
  
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/notes.css') }}">
</head>
<body>
  <header class="top-bar">
    <a href="{{ url('/') }}" class="icon-box">⬅</a>
    
    <h2 style="color:#1b5e20; font-size: 20px;">My Notes</h2>
    <button class="icon-box" id="newNoteBtn" style="font-size: 20px;">➕</button>
  </header>

  <main class="notes-main">
    <textarea id="noteContent" placeholder="Write your note here..." class="note-textarea"></textarea>
    
    <button id="saveNoteBtn" class="add-btn">💾 Save Note</button>
    
    <div class="notes-list-container">
      <h3>Recent Notes:</h3>
      <ul id="notesList" class="notes-list">
      </ul>
    </div>
  </main>

  <footer class="bottom-menu">
    <a href="{{ url('/index') }}" class="menu-item">🏠 <span>Dashboard</span></a>
    <a href="{{ url('/pomodoro') }}" class="menu-item">🍅 <span>Pomodoro</span></a>
    <a href="{{ url('/notes') }}" class="menu-item">🗒️ <span>Notes</span></a>
    <a href="{{ url('/calendar') }}" class="menu-item">📅 <span>Calendar</span></a>
    <a href="{{ url('/info') }}" class="menu-item">ℹ️ <span>Info</span></a>
  </footer>

  <script src="{{ asset('js/notes.js') }}"></script>
</body>
</html>