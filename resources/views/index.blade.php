<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  
  <title>Pixel Dashboard</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;900&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/calendar.css') }}">

  <style>
      /* PAKSA BACKGROUND MUNCUL */
      body {
          background: url("{{ asset('images/background.jpg') }}") no-repeat center center fixed !important;
          background-size: cover !important;
          min-height: 100vh;
          display: flex;
          flex-direction: column;
          overflow-x: hidden; /* Hindari scroll samping */
      }
      
      /* UTILITY: WRAPPER TENGAH */
      .dashboard-wrapper {
          flex: 1;
          display: flex;
          align-items: center; /* Vertikal Center */
          justify-content: center;
          padding-bottom: 80px; /* Space untuk menu bawah */
          padding-top: 20px;
      }

      /* STYLE KARTU PIXEL */
      .pixel-card {
          background: rgba(255, 255, 255, 0.92); /* Sedikit lebih solid */
          border: 4px solid #1b5e20;
          padding: 25px;
          box-shadow: 10px 10px 0px rgba(0,0,0,0.2); /* Shadow lebih tegas */
          height: 100%; /* Agar tinggi kartu seragam jika dalam row */
          position: relative;
      }

      /* SEARCH BAR TENGAH */
      .search-container-center {
          flex-grow: 1;
          display: flex;
          justify-content: center;
          padding: 0 15px;
      }
      
      .pixel-input {
          width: 100%;
          max-width: 500px; /* Batasi lebar search bar */
          border: 3px solid #1b5e20 !important;
          border-radius: 0 !important;
          font-family: 'Press Start 2P';
          font-size: 10px !important;
          background: #e8f5e9 !important;
          padding: 10px 15px;
          transition: all 0.3s;
      }
      .pixel-input:focus {
          background: #fff !important;
          box-shadow: 4px 4px 0 rgba(0,0,0,0.2);
          transform: translateY(-2px);
      }

      /* AVATAR & TEXT */
      .avatar-frame {
          width: 130px; 
          height: 130px; 
          border: 4px solid #1b5e20; 
          border-radius: 50%; 
          overflow: hidden; 
          margin: 0 auto 15px auto; 
          background: #fff;
          box-shadow: 0 4px 0 rgba(0,0,0,0.1);
      }
      
      .welcome-title {
          font-family: 'Press Start 2P'; 
          color: #1b5e20; 
          font-size: 16px; 
          line-height: 1.6;
          margin-bottom: 5px;
      }

      .digital-clock {
          font-family: 'Press Start 2P'; 
          font-size: 12px; 
          color: #fff;
          background: #1b5e20;
          display: inline-block;
          padding: 8px 12px;
          margin-top: 15px;
          box-shadow: 3px 3px 0 rgba(0,0,0,0.3);
      }
  </style>
</head>
<body>

  <header class="d-flex justify-content-between align-items-center px-4 py-3" 
          style="background: rgba(255,255,255,0.95); border-bottom: 4px solid #1b5e20; z-index: 50;">
    
    <a class="text-decoration-none" id="logoutBtn" title="Logout" style="cursor: pointer; font-size: 24px; transition: transform 0.2s;">
      ⛔
    </a>
    
    <div class="search-container-center position-relative">
        <input type="text" class="form-control pixel-input" id="searchInput" placeholder="Find Friend / Menu..." autocomplete="off">
        <div id="searchDropdown" style="display:none; position:absolute; top: 100%; left:0; right:0; margin:auto; max-width:500px; background:#fff; border:4px solid #1b5e20; z-index:100; box-shadow: 8px 8px 0 rgba(0,0,0,0.1);"></div>
    </div>

    <a href="#" class="text-decoration-none position-relative" title="Notifications" style="font-size: 24px; transition: transform 0.2s;">
      🔔
      <span id="notifBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" 
            style="font-size: 8px; border: 2px solid #fff; display: none;">0</span>
    </a>
  </header>

  <div class="dashboard-wrapper">
      <div class="container">
          
          <div class="row justify-content-center align-items-stretch g-4">
              
              <div class="col-md-5 col-lg-4">
                  <div class="pixel-card text-center d-flex flex-column justify-content-center h-100">
                      
                      <div class="avatar-frame">
                          <img src="{{ asset('images/polo.jpg') }}" alt="User" style="width: 100%; height: 100%; object-fit: cover;">
                      </div>

                      <h2 class="welcome-title">
                          Hi, {{ Auth::user()->name }}!
                      </h2>
                      <p style="font-size: 10px; font-family: 'Poppins'; color: #555; margin-bottom: 0;">
                          Ready to be productive today?
                      </p>

                      <div id="clock" class="digital-clock">--:--:--</div>
                  </div>
              </div>

              <div class="col-md-7 col-lg-6">
                  <div class="pixel-card p-0 overflow-hidden d-flex flex-column" style="min-height: 320px;">
                       <div id="calendar" style="flex: 1; width: 100%;"></div> 
                  </div>
              </div>

          </div>
      </div>
  </div>

  <div class="bottom-menu" style="position: fixed; bottom: 0; width: 100%; background: #a5d6a7; border-top: 4px solid #1b5e20; z-index: 999; display: flex; justify-content: space-around; padding: 10px 0; box-shadow: 0 -4px 10px rgba(0,0,0,0.1);">
    <a href="{{ url('/index') }}" class="text-decoration-none text-center text-dark menu-item">
        <div style="font-size: 20px;">🏠</div><span style="font-size: 10px; font-weight: bold; font-family: 'Press Start 2P';">Home</span>
    </a>
    <a href="{{ url('/pomodoro') }}" class="text-decoration-none text-center text-dark menu-item">
        <div style="font-size: 20px;">🍅</div><span style="font-size: 10px; font-weight: bold; font-family: 'Press Start 2P';">Pomo</span>
    </a>
    <a href="{{ url('/notes') }}" class="text-decoration-none text-center text-dark menu-item">
        <div style="font-size: 20px;">📝</div><span style="font-size: 10px; font-weight: bold; font-family: 'Press Start 2P';">Notes</span>
    </a>
    <a href="{{ url('/calendar') }}" class="text-decoration-none text-center text-dark menu-item">
        <div style="font-size: 20px;">📅</div><span style="font-size: 10px; font-weight: bold; font-family: 'Press Start 2P';">Cal</span>
    </a>
    <a href="{{ url('/info') }}" class="text-decoration-none text-center text-dark menu-item">
        <div style="font-size: 20px;">ℹ️</div><span style="font-size: 10px; font-weight: bold; font-family: 'Press Start 2P';">Info</span>
    </a>
  </div>

  <div id="logoutPopup" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: #fff; border: 4px solid #1b5e20; padding: 30px; text-align: center; box-shadow: 10px 10px 0 rgba(0,0,0,0.3);">
      <h3 style="font-family: 'Press Start 2P'; font-size: 14px; margin-bottom: 20px; color: #b9534c;">LOG OUT?</h3>
      <button id="logoutYes" class="btn btn-danger rounded-0 me-2" style="font-family: 'Press Start 2P'; font-size: 10px; border: 2px solid #000;">YES</button>
      <button id="logoutCancel" class="btn btn-secondary rounded-0" style="font-family: 'Press Start 2P'; font-size: 10px; border: 2px solid #000;">NO</button>
    </div>
  </div>

  <div id="eventModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#fff; border:4px solid #1b5e20; padding:20px; width:90%; max-width:320px; text-align:center; box-shadow:8px 8px 0 rgba(0,0,0,0.2);">
        <h4 style="font-family:'Press Start 2P'; font-size:12px; margin-bottom:15px; color:#1b5e20;">AGENDA</h4>
        <input type="hidden" id="eventDateInput">
        <input type="hidden" id="eventIdInput">
        <div style="margin-bottom:10px; font-family:'Press Start 2P'; font-size:10px; color:#555;" id="eventDateLabel">Date: -</div>
        <input type="text" id="eventTitleInput" placeholder="Event Name..." style="width:100%; padding:10px; font-family:'Poppins'; border:2px solid #1b5e20; margin-bottom:10px; outline:none;">
        
        <div style="position: relative; margin-bottom: 15px;">
            <input type="text" id="friendNameInput" class="form-control rounded-0" placeholder="Collab with..." style="border:2px solid #1b5e20; font-size:12px; font-family:'Poppins';">
            <div id="friendSuggestions" style="display:none; position:absolute; width:100%; background:#fff; border:2px solid #1b5e20; max-height:150px; overflow-y:auto; z-index:10; text-align:left;"></div>
        </div>
        <input type="hidden" id="eventFriendId">

        <div style="display:flex; justify-content:center; gap:5px;">
            <button id="saveEventBtn" class="btn btn-sm btn-success rounded-0" style="font-family:'Press Start 2P'; font-size:8px; border:2px solid #000;">SAVE</button>
            <button id="deleteEventBtn" class="btn btn-sm btn-danger rounded-0" style="display:none; font-family:'Press Start 2P'; font-size:8px; border:2px solid #000;">DEL</button>
            <button onclick="document.getElementById('eventModal').style.display='none'" class="btn btn-sm btn-secondary rounded-0" style="font-family:'Press Start 2P'; font-size:8px; border:2px solid #000;">CLOSE</button>
        </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('js/script.js') }}"></script>
  
  <script>
      // Script kecil untuk memunculkan modal logout (karena id-nya hidden by default css)
      const logoutPopup = document.getElementById('logoutPopup');
      document.getElementById('logoutBtn').addEventListener('click', () => { logoutPopup.style.display = 'flex'; });
      document.getElementById('logoutCancel').addEventListener('click', () => { logoutPopup.style.display = 'none'; });
  </script>
</body>
</html>