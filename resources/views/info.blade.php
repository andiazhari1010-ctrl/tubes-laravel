<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Info Website</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
  <header class="top-bar">
    <a href="notification.html" class="icon-box">🔔</a>
    <a href="add-friend.html" class="icon-box">👤</a>
  </header>

  <main class="info-page">
    <h1 class="info-title animate-slide">Tentang Website</h1>

    <div class="info-card animate-left">
      <h2>Kelompok 5</h2>
      <ul class="member-list">
        <li>Achmad Fandanish</li>
        <li>Adistya Putri Richardo</li>
        <li>Andy Azhari Pane</li>
        <li>Disha Aziz Maulana</li>
        <li>Fistiara Ashari</li>
        <li>Aldi Firmansyah</li>
      </ul>
      <p class="program-info"><strong>S1 Teknologi Informasi</strong><br>Telkom University — 2025</p>
    </div>

    <div class="info-card animate-right">
      <h3>Deskripsi Singkat</h3>
      <p>
        Website ini dirancang untuk membantu mahasiswa mengatur waktu, menjadwalkan kegiatan, dan meningkatkan fokus belajar dengan fitur kalender serta timer Pomodoro.
      </p>
      <p>
        Menariknya, pengguna juga dapat menambahkan <strong>teman belajar</strong> untuk saling menyemangati dan berbagi progres, menciptakan pengalaman belajar yang lebih seru dan interaktif!
      </p>
    </div>
  </main>

  <footer class="bottom-menu">
    <a href="{{ url('/index') }}" class="menu-item">🏠 <span>Dashboard</span></a>
    <a href="{{ url('/pomodoro') }}" class="menu-item">🍅 <span>Pomodoro</span></a>
    <a href="{{ url('/notes') }}" class="menu-item">🗒️ <span>Notes</span></a>
    <a href="{{ url('/calendar') }}" class="menu-item">📅 <span>Calendar</span></a>
    <a href="{{ url('/info') }}" class="menu-item">ℹ️ <span>Info</span></a>
  </footer>
</body>
</html>
