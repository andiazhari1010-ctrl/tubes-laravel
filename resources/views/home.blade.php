<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Catatan Pribadi</title>
  
  <link rel="stylesheet" href="{{ asset('css/home.css') }}" />
</head>
<body>
  <header class="nav">
    <div class="nav-left">
      <span class="brand-logo">📝</span>
      <span class="brand-text">Catatan Pribadi</span>
    </div>

    <div class="nav-right">
      <button class="login-btn" onclick="window.location.href='{{ url('/login') }}'">
        Login
      </button>
    </div>
  </header>

  <main class="hero">
    <section class="hero-text">
      <div class="hero-text home-hero-text">
        <h4>RAPI. FOKUS. TERENCANA.</h4>
        <h1>Satu ruang untuk semua rencana.</h1>
        <p>Kelola jadwal, sesi Pomodoro, dan progres belajar dalam satu dashboard sederhana.</p>
      </div>

      <div class="hero-actions">
        <button class="cta-btn" onclick="window.location.href='{{ url('/login') }}'">
          Masuk & Mulai
        </button>
      </div>
    </section>

    <section class="hero-visual">
      <div class="phone-stack">
        <div class="phone-card phone-back">
          <div class="phone-header">
            <span class="phone-dot"></span>
            <span class="phone-dot"></span>
            <span class="phone-dot"></span>
          </div>
          <div class="phone-body">
            <div class="pill pill-green">Kalender</div>
            <div class="pill pill-soft">Tugas Besok</div>
            <div class="pill pill-soft small">Reminder 08.00</div>
          </div>
        </div>

        <div class="phone-card phone-front">
          <div class="phone-header">
            <span class="phone-dot"></span>
            <span class="phone-dot"></span>
            <span class="phone-dot"></span>
          </div>
          <div class="phone-body">
            <div class="section-label">Hari ini</div>
            <div class="list-item">
              <span>⏱️</span>
              <div>
                <p class="item-title">Sesi Pomodoro</p>
                <p class="item-sub">25 menit fokus</p>
              </div>
            </div>
            <div class="list-item">
              <span>📚</span>
              <div>
                <p class="item-title">Review Materi</p>
                <p class="item-sub">Catatan kuliah</p>
              </div>
            </div>
            <div class="list-item">
              <span>🤝</span>
              <div>
                <p class="item-title">Teman Belajar</p>
                <p class="item-sub">Lihat progres bareng</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <footer class="footer">
    <p>© 2025 Catatan Pribadi • Bantu kamu tetap teratur & fokus setiap hari</p>
  </footer>
</body>
</html>