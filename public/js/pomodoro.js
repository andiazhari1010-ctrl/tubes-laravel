// ==========================================
// KONFIGURASI PATH & ASET
// ==========================================
const backgrounds = {
  pomodoro: '/images/pxl-bg.jpg',
  shortBreak: '/images/aft-bg.png',
  longBreak: '/images/night.jpg'
};

// Durations default
let durations = {
  pomodoro: 25 * 60,
  shortBreak: 5 * 60,
  longBreak: 15 * 60
};

// Elements
const app = document.getElementById('app'); 
const pomodoroBtn = document.getElementById('pomodoroBtn');
const shortBreakBtn = document.getElementById('shortBreakBtn');
const longBreakBtn = document.getElementById('longBreakBtn');
const timerDisplay = document.getElementById('timer');
const sessionCounterDisplay = document.getElementById('session-counter'); 
const startStopBtn = document.getElementById('startStopBtn');
const skipBtn = document.getElementById('skipBtn');
const resetBtn = document.getElementById('resetBtn'); 

// Audio
const originalTitle = document.title; 
const soundClick = new Audio('/audio/start.mp3'); 
const soundFinish = new Audio('/audio/finish.mp3');
soundClick.preload = 'auto';
soundFinish.preload = 'auto';

function playSound(audioElement) {
  audioElement.currentTime = 0; 
  audioElement.play().catch(e => console.error("Gagal memutar suara:", e));
}

// ==========================================
// STATE MANAGEMENT (MODIFIKASI UTAMA)
// ==========================================
let currentMode = localStorage.getItem('pomo_mode') || 'pomodoro';
let timeLeft = durations[currentMode]; // Sementara, nanti diupdate fetchConfig
let timerInterval = null;
let running = false;
let endTime = null; 
let pomodoroCount = parseInt(localStorage.getItem('pomo_count')) || 0;
let shortBreakCount = parseInt(localStorage.getItem('pomo_sb_count')) || 0;

// ==========================================
// FUNGSI SIMPAN & MUAT (LOCAL STORAGE)
// ==========================================

// Simpan status saat ini ke browser
function saveState() {
    localStorage.setItem('pomo_mode', currentMode);
    localStorage.setItem('pomo_count', pomodoroCount);
    localStorage.setItem('pomo_sb_count', shortBreakCount);
    localStorage.setItem('pomo_running', running);
    
    if (running && endTime) {
        localStorage.setItem('pomo_endTime', endTime);
    } else {
        localStorage.removeItem('pomo_endTime');
        localStorage.setItem('pomo_timeLeft', timeLeft); // Simpan sisa waktu jika pause
    }
}

// Cek apakah ada timer yang berjalan sebelumnya
function loadState() {
    const savedRunning = localStorage.getItem('pomo_running') === 'true';
    const savedEndTime = localStorage.getItem('pomo_endTime');
    const savedTimeLeft = localStorage.getItem('pomo_timeLeft');

    // Update tampilan counter
    updateCounterDisplay();
    updateActiveButton();
    updateBackground();

    if (savedRunning && savedEndTime) {
        // KASUS 1: Timer ditinggal jalan (Pindah Tab/Close Browser)
        const now = Date.now();
        const remaining = Math.ceil((parseInt(savedEndTime) - now) / 1000);

        if (remaining > 0) {
            // Timer masih ada sisa waktu -> LANJUTKAN
            console.log("Melanjutkan timer dari storage...");
            timeLeft = remaining;
            endTime = parseInt(savedEndTime); // Restore endTime
            running = true;
            startTimer(true); // True artinya "Restore Mode"
        } else {
            // Timer habis saat user pergi -> SELESAIKAN
            console.log("Timer selesai saat Anda pergi.");
            timeLeft = 0;
            finishSession(true); // True = tanpa suara (karena mungkin user baru buka)
        }
    } else if (savedTimeLeft) {
        // KASUS 2: Timer dalam posisi PAUSE
        console.log("Mengembalikan posisi pause...");
        timeLeft = parseInt(savedTimeLeft);
        updateTimerDisplay();
        // UI tetap dalam kondisi pause (tombol START muncul)
        startStopBtn.textContent = 'RESUME';
        app.classList.add('timer-paused');
    }
}

// ==========================================
// API & INTEGRASI
// ==========================================

async function fetchConfig() {
    try {
        const response = await fetch('/api/config');
        if (response.ok) {
            const data = await response.json();
            durations.pomodoro = data.pomodoro_duration * 60;
            durations.shortBreak = data.short_break_duration * 60;
            durations.longBreak = data.long_break_duration * 60;
            
            // Hanya reset timeLeft jika TIDAK sedang jalan & TIDAK ada data pause tersimpan
            if (!running && !localStorage.getItem('pomo_timeLeft') && !localStorage.getItem('pomo_endTime')) {
                timeLeft = durations[currentMode];
                updateTimerDisplay();
            }
        }
    } catch (error) {
        console.warn("Gagal ambil config, pakai default.");
    }
}

async function logSession(type, duration, status) {
    try {
        await fetch('/api/log', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ type, duration, status })
        });
    } catch (e) { console.error(e); }
}

// ==========================================
// LOGIKA UI
// ==========================================

function updateBackground() {
  document.body.style.backgroundImage = `url('${backgrounds[currentMode]}')`;
}

function updateTimerDisplay() {
  const minutes = Math.floor(timeLeft / 60).toString().padStart(2, '0');
  const seconds = (timeLeft % 60).toString().padStart(2, '0');
  timerDisplay.textContent = `${minutes}:${seconds}`;
  
  if (running) {
    document.title = `${minutes}:${seconds} - ${currentMode}`;
  } else {
    document.title = originalTitle;
  }
}

function updateCounterDisplay() {
  sessionCounterDisplay.textContent = `Session: ${pomodoroCount}`;
}

function updateActiveButton() {
  pomodoroBtn.classList.remove('active');
  shortBreakBtn.classList.remove('active');
  longBreakBtn.classList.remove('active');
  if (currentMode === 'pomodoro') pomodoroBtn.classList.add('active');
  if (currentMode === 'shortBreak') shortBreakBtn.classList.add('active');
  if (currentMode === 'longBreak') longBreakBtn.classList.add('active');
}

function switchMode(mode) {
  if (running) {
    clearInterval(timerInterval);
    running = false;
  }
  
  startStopBtn.textContent = 'START';
  app.classList.remove('timer-running');
  app.classList.remove('timer-paused');
  
  currentMode = mode;
  timeLeft = durations[mode];
  
  // Bersihkan storage terkait timer saat ganti mode manual
  localStorage.removeItem('pomo_endTime');
  localStorage.removeItem('pomo_timeLeft');
  saveState(); // Simpan mode baru
  
  updateTimerDisplay();
  updateBackground();
  updateActiveButton();
}

// ==========================================
// LOGIKA TIMER UTAMA
// ==========================================

function startTimer(isRestoring = false) {
  if (!isRestoring) playSound(soundClick);
  
  if (running && !isRestoring) {
    // --- PAUSE ---
    clearInterval(timerInterval);
    running = false;
    startStopBtn.textContent = 'RESUME';
    
    app.classList.remove('timer-running');
    app.classList.add('timer-paused'); 
    document.title = `(Paused) ${originalTitle}`;
    
    saveState(); // Simpan kondisi pause
    
  } else {
    // --- START / RESUME ---
    running = true;
    startStopBtn.textContent = 'PAUSE';
    
    app.classList.remove('timer-paused');
    app.classList.add('timer-running'); 
    
    // Jika BUKAN restore (Start baru), hitung endTime baru
    if (!isRestoring) {
        const now = Date.now();
        endTime = now + (timeLeft * 1000); 
    }
    
    saveState(); // Simpan kondisi jalan (termasuk endTime)

    updateTimerDisplay(); 
    
    timerInterval = setInterval(() => {
      const currentTime = Date.now();
      const remainingSeconds = Math.ceil((endTime - currentTime) / 1000);

      if (remainingSeconds > 0) {
        timeLeft = remainingSeconds;
        updateTimerDisplay();
      } else {
        finishSession();
      }
    }, 1000);
  }
}

function finishSession(silent = false) {
    clearInterval(timerInterval);
    timeLeft = 0;
    updateTimerDisplay();
    logSession(currentMode, durations[currentMode], 'completed')
      .then(() => fetchStats());

    handleTimerCompletion();
    
    // Hapus data timer dari storage karena sudah selesai
    localStorage.removeItem('pomo_endTime');
    localStorage.removeItem('pomo_timeLeft');
    localStorage.setItem('pomo_running', false);

    if (!silent) {
        playSound(soundFinish); 
        alert(`${currentMode} selesai!`);
    }
    
    running = false;
    startStopBtn.textContent = 'START'; 
    app.classList.remove('timer-running');
    app.classList.remove('timer-paused');
    
    logSession(currentMode, durations[currentMode], 'completed');
    handleTimerCompletion(); 
}

function handleTimerCompletion() {
    if (currentMode === 'pomodoro') {
        if (shortBreakCount < 2) { 
            shortBreakCount++;
            switchMode('shortBreak');
        } else {
            shortBreakCount = 0; 
            switchMode('longBreak');
        }
    } else {
        if (currentMode !== 'pomodoro') { 
            pomodoroCount++; 
            updateCounterDisplay(); 
        }
        switchMode('pomodoro');
    }
    
    // Auto-save perubahan sesi
    saveState();
    
    // Opsional: Langsung start timer berikutnya? 
    // startTimer(); 
}

// ==========================================
// TOMBOL LAIN
// ==========================================

function skipSession() {
    playSound(soundClick);
    if (running) {
        clearInterval(timerInterval);
        running = false;
    }
    app.classList.remove('timer-running');
    app.classList.remove('timer-paused');
    
    const timeSpent = durations[currentMode] - timeLeft;
    logSession(currentMode, timeSpent, 'skipped');

    // alert(`Skipping...`); 
    
    if (currentMode !== 'pomodoro') {
        pomodoroCount++;
        updateCounterDisplay();
        switchMode('pomodoro');
    } else {
        handleTimerCompletion();
    }
}

function resetCurrentTimer() {
    playSound(soundClick);
    if (running) {
        clearInterval(timerInterval);
        running = false;
    }
    
    timeLeft = durations[currentMode];
    localStorage.removeItem('pomo_endTime');
    localStorage.removeItem('pomo_timeLeft');
    localStorage.setItem('pomo_running', false);
    
    updateTimerDisplay();
    app.classList.remove('timer-paused');
    app.classList.remove('timer-running');
    startStopBtn.textContent = 'START'; 
}

function resetSessionCounter() {
    if (confirm("Reset sesi?")) {
        pomodoroCount = 0;
        shortBreakCount = 0;
        updateCounterDisplay();
        saveState();
    }
}

// ==========================================
// INISIALISASI
// ==========================================
pomodoroBtn.addEventListener('click', () => switchMode('pomodoro'));
shortBreakBtn.addEventListener('click', () => switchMode('shortBreak'));
longBreakBtn.addEventListener('click', () => switchMode('longBreak'));

startStopBtn.addEventListener('click', () => startTimer(false));
skipBtn.addEventListener('click', skipSession);
resetBtn.addEventListener('click', resetCurrentTimer); 
sessionCounterDisplay.addEventListener('click', resetSessionCounter); 

// URUTAN PENTING:
// 1. Load config (durasi user)
// 2. Load state (apakah lagi jalan/pause)
fetchConfig().then(() => {
    loadState();
    fetchStats(); // <--- TAMBAHKAN INI
});

// Ambil data statistik dari Laravel
async function fetchStats() {
    try {
        const response = await fetch('/api/stats');
        if (response.ok) {
            const data = await response.json();
            
            // Update tampilan HTML
            const statMin = document.getElementById('stat-minutes');
            const statSes = document.getElementById('stat-sessions');
            
            if(statMin) statMin.textContent = data.total_minutes;
            if(statSes) statSes.textContent = data.total_sessions;
        }
    } catch (error) {
        console.error("Gagal ambil stats:", error);
    }
}

