<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Add Friend</title>

  <link rel="stylesheet" href="{{ asset('css/style.css') }}" />
  
  <link rel="stylesheet" href="{{ asset('css/add-friend.css') }}" />
</head>
<body>
  <header class="top-bar">
    <a href="{{ url('index') }}" class="icon-box" aria-label="Back">⬅</a>
    <h2 class="page-title">Add Friend</h2>
    <div class="spacer"></div>
  </header>

  <main class="add-friend">
    <div class="add-friend-card">
      <p class="invite-title">Invite someone to be your friend</p>

      <div class="friend-input">
        <input id="friendNameInput" type="text" placeholder="Friend's name..." class="input-box">
        <button id="uiAddFriendBtn" class="add-btn">Add</button>
      </div>

      <div class="info-card">
        <h3 class="people-title">Your Friend List</h3>
        <ul class="friend-list" id="currentFriendList">
          <li>
            <div>
              <strong>Rani</strong><br><small>Status: Friends</small>
            </div>
            <span style="font-size: 20px;">🤝</span>
          </li>
          <li>
            <div>
              <strong>Budi</strong><br><small>Status: Friends</small>
            </div>
            <span style="font-size: 20px;">🤝</span>
          </li>
          <li>
            <div>
              <strong>Andi</strong><br><small>Status: Friends</small>
            </div>
            <span style="font-size: 20px;">🤝</span>
          </li>
        </ul>
      </div>

      <div id="afStatus" class="status-text"></div>
    </div>
  </main>

  <footer class="bottom-menu">
    <a href="{{ url('/index') }}" class="menu-item">🏠 <span>Dashboard</span></a>
    <a href="{{ url('/pomodoro') }}" class="menu-item">🍅 <span>Pomodoro</span></a>
    <a href="{{ url('/notes') }}" class="menu-item">📝 <span>Notes</span></a>
    <a href="{{ url('/calendar') }}" class="menu-item">📅 <span>Calendar</span></a>
    <a href="{{ url('/info') }}" class="menu-item">ℹ️ <span>Info</span></a>
  </footer>

  <script src="{{ asset('js/add-friend.js') }}"></script>
</body>
</html>