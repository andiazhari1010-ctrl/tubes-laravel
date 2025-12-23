<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Notes</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/notes.css') }}">

  <style>
      /* FIX DARURAT: Paksa body agar background muncul & layout rapi */
      body {
          background: url("{{ asset('images/background.jpg') }}") no-repeat center center fixed !important;
          background-size: cover !important;
          display: flex;
          flex-direction: column;
          min-height: 100vh;
      }
      
      /* FIX GEPEK: Pastikan wrapper punya lebar */
      .main-layout-wrapper {
          width: 100%; 
          flex: 1;
          display: flex;
          flex-direction: column;
          align-items: center; /* Tengahkan konten */
          padding-top: 20px;
          padding-bottom: 100px; /* Space untuk menu bawah */
      }
      
      .pixel-card {
          background: rgba(255, 255, 255, 0.95);
          border: 4px solid #1b5e20;
          padding: 20px;
          box-shadow: 8px 8px 0px rgba(0,0,0,0.2);
          width: 100%; /* Pastikan card mengisi kolom */
      }
  </style>
</head>
<body>

  <header class="d-flex justify-content-between align-items-center px-4 py-3" style="background: rgba(255,255,255,0.9); border-bottom: 4px solid #1b5e20;">
    <a href="{{ url('/index') }}" class="btn btn-dark rounded-0 btn-sm" style="font-family: 'Press Start 2P'; font-size: 10px;">⬅ BACK</a>
    <h2 class="m-0" style="font-family: 'Press Start 2P'; font-size: 16px; color: #1b5e20;">MY NOTES</h2>
    <button id="newNoteBtn" class="btn btn-success rounded-0 btn-sm" style="font-family: 'Press Start 2P'; font-size: 10px;">➕ NEW</button>
  </header>

  <main class="main-layout-wrapper">
    <div class="container"> <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                
                <div class="pixel-card mb-4">
                    <textarea id="noteContent" placeholder="Write something..." class="form-control mb-3 rounded-0" rows="6" 
                        style="border: 3px solid #a5d6a7; font-family: 'Poppins'; background: #f1f8e9; resize: none;"></textarea>
                    
                    <button id="saveNoteBtn" class="btn btn-success w-100 rounded-0 py-2" 
                        style="border: 3px solid #1b5e20; font-family: 'Press Start 2P'; font-size: 12px; box-shadow: 4px 4px 0 #000;">
                        💾 SAVE NOTE
                    </button>
                </div>

                <div>
                    <h4 class="text-center mb-3" style="font-family: 'Press Start 2P'; font-size: 12px; color: #fff; text-shadow: 2px 2px 0 #1b5e20;">
                        📂 RECENT NOTES
                    </h4>
                    <ul id="notesList" class="list-unstyled">
                        </ul>
                </div>

            </div>
        </div>
    </div>
  </main>

  <div class="bottom-menu" style="position: fixed; bottom: 0; width: 100%; background: #a5d6a7; border-top: 4px solid #1b5e20; z-index: 999; display: flex; justify-content: space-around; padding: 10px 0;">
    <a href="{{ url('/index') }}" class="menu-item text-decoration-none text-center text-dark">
        <div style="font-size: 20px;">🏠</div><span style="font-size: 10px; font-weight: bold; font-family: 'Press Start 2P';">Home</span>
    </a>
    <a href="{{ url('/pomodoro') }}" class="menu-item text-decoration-none text-center text-dark">
        <div style="font-size: 20px;">🍅</div><span style="font-size: 10px; font-weight: bold; font-family: 'Press Start 2P';">Pomo</span>
    </a>
    <a href="{{ url('/notes') }}" class="menu-item text-decoration-none text-center text-dark">
        <div style="font-size: 20px;">📝</div><span style="font-size: 10px; font-weight: bold; font-family: 'Press Start 2P';">Notes</span>
    </a>
    <a href="{{ url('/calendar') }}" class="menu-item text-decoration-none text-center text-dark">
        <div style="font-size: 20px;">📅</div><span style="font-size: 10px; font-weight: bold; font-family: 'Press Start 2P';">Cal</span>
    </a>
    <a href="{{ url('/info') }}" class="menu-item text-decoration-none text-center text-dark">
        <div style="font-size: 20px;">ℹ️</div><span style="font-size: 10px; font-weight: bold; font-family: 'Press Start 2P';">Info</span>
    </a>
  </div>

  <script src="{{ asset('js/notes.js') }}"></script>
</body>
</html>