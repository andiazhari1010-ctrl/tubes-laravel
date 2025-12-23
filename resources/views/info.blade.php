<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Info Website</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

  <header class="top-bar d-flex justify-content-between align-items-center px-4 py-3">
    <a href="{{ url('/index') }}" class="btn btn-dark rounded-0 btn-sm" style="font-family: 'Press Start 2P'; font-size: 10px;">⬅ DASHBOARD</a>
  </header>

  <main class="main-layout-wrapper d-flex align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                
                <div class="pixel-card text-center">
                    <h1 class="mb-4" style="font-family: 'Press Start 2P'; color: #1b5e20; font-size: 20px; line-height: 1.5;">Tentang Website</h1>
                    
                    <div class="row text-start mt-4">
                        <div class="col-md-6 mb-4">
                            <h5 style="border-bottom: 2px solid #ccc; padding-bottom: 5px; font-weight: bold;">👥 Kelompok 5</h5>
                            <ul class="list-unstyled" style="font-size: 14px; font-family: 'Poppins'; line-height: 1.8;">
                                <li>• Achmad Fandanish</li>
                                <li>• Adistya Putri Richardo</li>
                                <li>• Andy Azhari Pane</li>
                                <li>• Disha Aziz Maulana</li>
                                <li>• Fistiara Ashari</li>
                                <li>• Aldi Firmansyah</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5 style="border-bottom: 2px solid #ccc; padding-bottom: 5px; font-weight: bold;">ℹ️ Deskripsi</h5>
                            <p style="font-size: 13px; font-family: 'Poppins'; text-align: justify;">
                                Website ini dirancang untuk membantu mahasiswa mengatur waktu, menjadwalkan kegiatan, dan meningkatkan fokus belajar dengan fitur kalender serta timer Pomodoro.
                            </p>
                        </div>
                    </div>

                    <div class="mt-3 pt-3 border-top" style="font-family: 'Poppins'; font-size: 12px;">
                        <strong>S1 Teknologi Informasi</strong><br>Telkom University — 2025
                    </div>
                </div>

            </div>
        </div>
    </div>
  </main>

  <div class="bottom-menu-fixed">
    <a href="{{ url('/index') }}" class="menu-item">🏠 <span>Home</span></a>
    <a href="{{ url('/pomodoro') }}" class="menu-item">🍅 <span>Pomo</span></a>
    <a href="{{ url('/notes') }}" class="menu-item">📝 <span>Notes</span></a>
    <a href="{{ url('/calendar') }}" class="menu-item">📅 <span>Cal</span></a>
    <a href="{{ url('/info') }}" class="menu-item">ℹ️ <span>Info</span></a>
  </div>

</body>
</html>