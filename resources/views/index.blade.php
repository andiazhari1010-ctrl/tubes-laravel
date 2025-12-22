<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  
  <title>Pixel Dashboard</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">

  <style>
      /* OVERRIDE BOOTSTRAP BIAR PIXELATED */
      .pixel-search-container {
          width: 400px; /* Lebar search bar */
          max-width: 100%;
          position: relative;
      }
      
      .pixel-input {
          border: 4px solid #1b5e20 !important;
          border-radius: 0 !important; /* Hilangkan lengkungan (Rounded) */
          font-family: 'Press Start 2P', cursive;
          font-size: 10px !important;
          background-color: #e8f5e9 !important;
          color: #1b5e20 !important;
          box-shadow: 4px 4px 0px rgba(0,0,0,0.2) !important;
      }
      
      .pixel-input:focus {
          background-color: #fff !important;
          box-shadow: 4px 4px 0px rgba(0,0,0,0.4) !important;
          outline: none !important;
      }

      .pixel-dropdown {
          border: 4px solid #1b5e20 !important;
          border-radius: 0 !important;
          margin-top: 10px !important;
          box-shadow: 8px 8px 0px rgba(0,0,0,0.2) !important;
          background: #fff !important;
          display: none; /* Hidden by default */
          position: absolute;
          width: 100%;
          z-index: 1050; /* Pastikan di atas segalanya */
          max-height: 400px;
          overflow-y: auto;
      }

      /* Style untuk item hasil search */
      .search-item {
          padding: 10px;
          border-bottom: 2px dashed #ccc;
          cursor: pointer;
          font-family: 'Poppins', sans-serif; /* Biar mudah dibaca */
          font-size: 12px;
          display: flex;
          align-items: center;
          justify-content: space-between;
      }
      .search-item:hover {
          background-color: #e8f5e9;
      }
      .search-item:last-child { border-bottom: none; }
      
      .badge-pixel {
          font-family: 'Press Start 2P';
          font-size: 8px;
          padding: 5px;
          border-radius: 0 !important;
      }
  </style>
</head>
<body>

  <header class="top-bar d-flex justify-content-between align-items-center px-4 py-3" style="background-color: #fff; border-bottom: 4px solid #1b5e20;">

    <a class="icon-box logout-icon text-decoration-none" id="logoutBtn" title="Logout" style="cursor: pointer; font-size: 24px;">🚪</a>

    <div class="pixel-search-container">
        <input type="text" class="form-control pixel-input" id="searchInput" placeholder="Search Friend / Menu..." autocomplete="off">
        
        <div class="pixel-dropdown" id="searchDropdown">
            </div>
    </div>

    <div class="d-flex gap-3">
        <a href="#" class="icon-box text-decoration-none" title="Notifications" style="font-size: 24px;">🔔</a>
        <a href="{{ url('/add-friend') }}" class="icon-box text-decoration-none" title="Friend List" style="font-size: 24px;">👥</a>
    </div>

  </header>

  <div class="dashboard container mt-4 text-center">
    <div class="avatar-section mb-4">
      <h2 class="welcome-text" style="font-family: 'Press Start 2P'; color: #1b5e20; margin-bottom: 20px;">Welcome, {{ Auth::user()->name }}!</h2>
      <div class="avatar-box floating mx-auto" style="width: 120px; height: 120px; border: 4px solid #1b5e20; border-radius: 50%; overflow: hidden;">
        <img src="{{ asset('images/polo.jpg') }}" alt="Avatar" class="avatar w-100 h-100 object-fit-cover">
      </div>
    </div>

    <div class="widget calendar-widget mx-auto" id="calendar" style="max-width: 350px;"></div>
  </div>

  <div class="top-clock text-center mt-3" id="clock" style="font-family: 'Press Start 2P'; font-size: 24px; color: #1b5e20;"></div>

  <div class="bottom-menu d-flex justify-content-center gap-4 mt-5 fixed-bottom pb-4" style="background: rgba(255,255,255,0.9); padding-top:10px; border-top: 4px solid #1b5e20;">
    <a href="{{ url('/index') }}" class="menu-item text-decoration-none text-dark fw-bold">🏠 <span>Home</span></a>
    <a href="{{ url('/pomodoro') }}" class="menu-item text-decoration-none text-dark fw-bold">🍅 <span>Pomodoro</span></a>
    <a href="{{ url('/notes') }}" class="menu-item text-decoration-none text-dark fw-bold">📝 <span>Notes</span></a>
    <a href="{{ url('/calendar') }}" class="menu-item text-decoration-none text-dark fw-bold">📅 <span>Calendar</span></a>
  </div>

  <div id="logoutPopup" class="logout-popup hidden" style="display: none;">
    <div class="popup-box" style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: #fff; border: 4px solid #000; padding: 20px; z-index: 9999; text-align: center;">
      <h3>Log Out?</h3>
      <div class="mt-3">
        <button id="logoutYes" class="btn btn-danger rounded-0" style="font-family: 'Press Start 2P'; font-size: 10px;">YES</button>
        <button id="logoutCancel" class="btn btn-secondary rounded-0" style="font-family: 'Press Start 2P'; font-size: 10px;">NO</button>
      </div>
    </div>
  </div>

  <script src="{{ asset('js/script.js') }}"></script>
</body>
</html>