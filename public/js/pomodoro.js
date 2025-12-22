document.addEventListener('DOMContentLoaded', () => {
    // ==========================================
    // 1. CONFIG & IMAGES
    // ==========================================
    let settings = JSON.parse(localStorage.getItem('pomo_settings')) || {
        pomodoro: 20, shortBreak: 10, longBreak: 15,
        longBreakInterval: 4, autoStartBreak: false, autoStartPomo: false
    };

    if(document.getElementById('set-pomo')) {
        document.getElementById('set-pomo').value = settings.pomodoro;
        document.getElementById('set-short').value = settings.shortBreak;
        document.getElementById('set-long').value = settings.longBreak;
        document.getElementById('set-interval').value = settings.longBreakInterval;
        document.getElementById('set-auto-break').checked = settings.autoStartBreak;
        document.getElementById('set-auto-pomo').checked = settings.autoStartPomo;
    }

    const durations = {
        pomodoro: parseInt(settings.pomodoro),
        shortBreak: parseInt(settings.shortBreak),
        longBreak: parseInt(settings.longBreak)
    };

    const backgrounds = { pomodoro: '/images/pxl-bg.jpg', shortBreak: '/images/aft-bg.png', longBreak: '/images/night.jpg' };
    
    // === FITUR 5: CHARACTER IMAGES ===
    const charImages = {
        pomodoro: '/images/char-focus.gif',   // Ganti dengan file kamu
        shortBreak: '/images/char-break.gif', // Ganti dengan file kamu
        longBreak: '/images/char-sleep.gif'   // Ganti dengan file kamu
    };

    const soundClick = new Audio('/audio/start.mp3'); 
    const soundFinish = new Audio('/audio/finish.mp3');

    const app = document.getElementById('app');
    const timerDisplay = document.getElementById('timer');
    const startStopBtn = document.getElementById('startStopBtn');
    const resetBtn = document.getElementById('resetBtn');
    const skipBtn = document.getElementById('skipBtn');
    const charElement = document.getElementById('pomo-character');
    
    const statSecondsDisplay = document.getElementById('stat-seconds');
    const statSessionsDisplay = document.getElementById('stat-sessions');

    let currentMode = localStorage.getItem('pomo_mode') || 'pomodoro';
    let isRunning = localStorage.getItem('pomo_isRunning') === 'true';
    let timer = null;
    let dbTotalSeconds = 0; 
    let dbTotalSessions = 0;
    let activityChart = null;

    if (!['pomodoro', 'shortBreak', 'longBreak'].includes(currentMode)) currentMode = 'pomodoro';

    // ==========================================
    // 2. FITUR 3: CUSTOM TOAST (PENGGANTI ALERT)
    // ==========================================
    function showToast(message, type = 'info') {
        const container = document.getElementById('toast-container');
        if(!container) return;

        const toast = document.createElement('div');
        toast.className = 'toast';
        // Tambahkan Ikon sesuai mood
        let icon = '🔔';
        if(message.includes('Selesai')) icon = '🎉';
        if(message.includes('Istirahat')) icon = '☕';
        if(message.includes('Gagal')) icon = '❌';

        toast.innerHTML = `<span>${icon}</span> <span>${message}</span>`;
        container.appendChild(toast);

        // Hapus otomatis setelah 3 detik
        setTimeout(() => {
            toast.classList.add('hide');
            toast.addEventListener('animationend', () => toast.remove());
        }, 3000);
    }

    // ==========================================
    // 3. UI ENHANCEMENT: CYCLE TRACKER
    // ==========================================
    let dotsContainer = document.getElementById('cycle-dots');
    if (!dotsContainer && timerDisplay) {
        dotsContainer = document.createElement('div');
        dotsContainer.id = 'cycle-dots';
        dotsContainer.style.display = 'flex';
        dotsContainer.style.gap = '8px';
        dotsContainer.style.justifyContent = 'center';
        dotsContainer.style.marginBottom = '20px';
        timerDisplay.after(dotsContainer); 
    }

    function renderCycleDots() {
        if(!dotsContainer) return;
        dotsContainer.innerHTML = ''; 
        const interval = parseInt(settings.longBreakInterval) || 4;
        const cycleCount = parseInt(localStorage.getItem('pomo_cycleCount')) || 0;
        const currentPos = cycleCount % interval; 

        for(let i = 0; i < interval; i++) {
            const dot = document.createElement('span');
            dot.style.width = '12px'; dot.style.height = '12px';
            dot.style.border = '2px solid white'; dot.style.borderRadius = '50%';
            dot.style.display = 'inline-block'; dot.style.transition = 'all 0.3s ease';
            if (i < currentPos) {
                dot.style.backgroundColor = 'white';
                dot.style.boxShadow = '0 0 5px rgba(255,255,255,0.8)';
            } else {
                dot.style.backgroundColor = 'transparent'; 
            }
            dotsContainer.appendChild(dot);
        }
    }

    // ==========================================
    // 4. MODAL LOGIC
    // ==========================================
    if(document.getElementById('btnReport')) document.getElementById('btnReport').addEventListener('click', () => {
        document.getElementById('modalReport').classList.remove('hidden');
        renderChart(); 
    });
    if(document.getElementById('btnSetting')) document.getElementById('btnSetting').addEventListener('click', () => {
        document.getElementById('modalSetting').classList.remove('hidden');
    });

    window.closeModal = (id) => document.getElementById(id).classList.add('hidden');
    window.switchTab = (tabName) => {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
        document.getElementById('tab-' + tabName).classList.add('active');
        event.target.classList.add('active');
    };

    window.saveSettings = () => {
        const newSettings = {
            pomodoro: document.getElementById('set-pomo').value,
            shortBreak: document.getElementById('set-short').value,
            longBreak: document.getElementById('set-long').value,
            longBreakInterval: document.getElementById('set-interval').value,
            autoStartBreak: document.getElementById('set-auto-break').checked,
            autoStartPomo: document.getElementById('set-auto-pomo').checked
        };
        localStorage.setItem('pomo_settings', JSON.stringify(newSettings));
        
        settings = newSettings;
        durations.pomodoro = parseInt(newSettings.pomodoro);
        durations.shortBreak = parseInt(newSettings.shortBreak);
        durations.longBreak = parseInt(newSettings.longBreak);
        
        if (!isRunning) {
            localStorage.removeItem('pomo_targetTime');
            localStorage.setItem('pomo_timeLeft', durations[currentMode]);
        }
        showToast("Settings Saved! Reloading...");
        setTimeout(() => location.reload(), 1000);
    };

    // ==========================================
    // 5. STATS & CHART
    // ==========================================
    async function fetchStatsAndReport() {
        try {
            const res = await fetch('/api/stats'); 
            if(res.ok) {
                const data = await res.json();
                dbTotalSeconds = data.total_seconds || 0;
                dbTotalSessions = data.total_sessions || 0;

                if(statSecondsDisplay) statSecondsDisplay.textContent = dbTotalSeconds;
                if(statSessionsDisplay) statSessionsDisplay.textContent = dbTotalSessions;
                if(document.getElementById('rep-seconds')) document.getElementById('rep-seconds').textContent = dbTotalSeconds;
                if(document.getElementById('rep-session-passed')) document.getElementById('rep-session-passed').textContent = dbTotalSessions;

                const rankList = document.getElementById('ranking-list');
                if(rankList) {
                    rankList.innerHTML = '';
                    data.ranking.forEach((user, index) => {
                        const li = document.createElement('li');
                        li.innerHTML = `<span>#${index+1} ${user.name}</span> <span>${user.time}</span>`;
                        rankList.appendChild(li);
                    });
                }
                window.chartDataConfig = data.chart_data;
                if(!document.getElementById('modalReport').classList.contains('hidden')) renderChart();
            }
        } catch (error) { console.log("Stats fetch error", error); }
    }

    function renderChart() {
        const ctx = document.getElementById('activityChart');
        if(!ctx) return;
        if(activityChart) activityChart.destroy();
        const dataConfig = window.chartDataConfig || [];
        
        activityChart = new Chart(ctx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: dataConfig.map(d => d.date),
                datasets: [{
                    label: 'Seconds', data: dataConfig.map(d => d.seconds),
                    backgroundColor: '#b9534c', borderRadius: 4
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } }
            }
        });
    }

    // ==========================================
    // 6. TIMER LOGIC
    // ==========================================
    function getTimeLeft() {
        const target = localStorage.getItem('pomo_targetTime');
        const saved = localStorage.getItem('pomo_timeLeft');
        if (isRunning && target) {
            const now = Date.now();
            const left = Math.ceil((parseInt(target) - now) / 1000);
            return left > 0 ? left : 0;
        }
        if (saved) return parseInt(saved);
        return durations[currentMode];
    }

    function updateDisplay() {
        const left = getTimeLeft();
        let m = Math.floor(left / 60);
        let s = left % 60;
        
        if (timerDisplay) {
            timerDisplay.textContent = `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
            document.title = isRunning ? `(${m}:${s}) Fokus...` : 'Pomodoro';
        }
        if (currentMode === 'pomodoro') {
            let currentDisplay = dbTotalSeconds;
            if (isRunning) currentDisplay += (durations.pomodoro - left);
            if(statSecondsDisplay) statSecondsDisplay.textContent = currentDisplay;
        }
        renderCycleDots();
    }

    function startTimer() {
        playSound(soundClick);
        if (isRunning) {
            clearInterval(timer);
            const target = localStorage.getItem('pomo_targetTime');
            let leftToSave = durations[currentMode];
            if (target) {
                leftToSave = Math.ceil((parseInt(target) - Date.now()) / 1000);
                if (leftToSave < 0) leftToSave = 0;
            }
            localStorage.setItem('pomo_timeLeft', leftToSave);
            isRunning = false;
            localStorage.removeItem('pomo_targetTime'); 

            if(startStopBtn) startStopBtn.textContent = 'RESUME';
            app.classList.remove('timer-running'); app.classList.add('timer-paused');
        } else {
            isRunning = true;
            let savedLeft = localStorage.getItem('pomo_timeLeft');
            let currentLeft = savedLeft ? parseInt(savedLeft) : durations[currentMode];
            localStorage.setItem('pomo_targetTime', Date.now() + (currentLeft * 1000));
            
            if(startStopBtn) startStopBtn.textContent = 'PAUSE';
            app.classList.remove('timer-paused'); app.classList.add('timer-running');
            
            timer = setInterval(() => {
                const left = getTimeLeft();
                if (left <= 0) { completeSession(); return; }
                updateDisplay();
            }, 1000);
        }
        localStorage.setItem('pomo_isRunning', isRunning);
    }

    function completeSession() {
        clearInterval(timer);
        isRunning = false;
        localStorage.setItem('pomo_isRunning', false);
        localStorage.removeItem('pomo_targetTime');
        localStorage.setItem('pomo_timeLeft', durations[currentMode]);
        playSound(soundFinish);

        let nextMode = '';
        let cycleCount = parseInt(localStorage.getItem('pomo_cycleCount')) || 0;
        let isSessionPending = localStorage.getItem('pomo_sessionPending') === 'true';

        if (currentMode === 'pomodoro') {
            dbTotalSeconds += durations.pomodoro;
            if(statSecondsDisplay) statSecondsDisplay.textContent = dbTotalSeconds;
            saveLogToBackend(durations.pomodoro, 'completed');
            localStorage.setItem('pomo_sessionPending', 'true'); 
            cycleCount++;
            localStorage.setItem('pomo_cycleCount', cycleCount);
            
            const interval = parseInt(settings.longBreakInterval) || 4;
            nextMode = (cycleCount % interval === 0) ? 'longBreak' : 'shortBreak';
            
            switchMode(nextMode);
            // Ganti Alert dengan Toast
            showToast(nextMode === 'longBreak' ? "Waktunya Istirahat Panjang!" : "Waktunya Istirahat Pendek!", 'success');
            
            if(settings.autoStartBreak) setTimeout(startTimer, 1000);

        } else {
            if (isSessionPending) {
                dbTotalSessions += 1;
                if(statSessionsDisplay) statSessionsDisplay.textContent = dbTotalSessions;
                saveLogToBackend(durations[currentMode], 'completed');
                localStorage.setItem('pomo_sessionPending', 'false');
                switchMode('pomodoro');
                showToast("Siklus Selesai! Sesi +1. Kembali Fokus!", 'success');
                if(settings.autoStartPomo) setTimeout(startTimer, 1000);
            } else {
                switchMode('pomodoro');
                showToast("Istirahat Selesai!", 'info');
                if(settings.autoStartPomo) setTimeout(startTimer, 1000);
            }
        }
    }

    function skipSession() {
        const timeLeft = getTimeLeft();
        const timeSpent = durations[currentMode] - timeLeft;
        clearInterval(timer);
        isRunning = false;
        localStorage.removeItem('pomo_targetTime');

        let nextMode = '';
        let cycleCount = parseInt(localStorage.getItem('pomo_cycleCount')) || 0;
        let isSessionPending = localStorage.getItem('pomo_sessionPending') === 'true';

        if (currentMode === 'pomodoro') {
            if (timeSpent > 0) {
                dbTotalSeconds += timeSpent;
                if(statSecondsDisplay) statSecondsDisplay.textContent = dbTotalSeconds;
                saveLogToBackend(timeSpent, 'skipped');
            }
            localStorage.setItem('pomo_sessionPending', 'true');
            cycleCount++;
            localStorage.setItem('pomo_cycleCount', cycleCount);
            
            const interval = parseInt(settings.longBreakInterval) || 4;
            nextMode = (cycleCount % interval === 0) ? 'longBreak' : 'shortBreak';
            
            switchMode(nextMode);
            showToast("Skipped! Lanjut Istirahat.", 'warning');
            startTimer();
        } else {
            if (isSessionPending) {
                dbTotalSessions += 1;
                if(statSessionsDisplay) statSessionsDisplay.textContent = dbTotalSessions;
                localStorage.setItem('pomo_sessionPending', 'false');
                switchMode('pomodoro');
                showToast("Break Skipped! Sesi +1.", 'warning');
                startTimer();
            } else {
                switchMode('pomodoro');
                startTimer();
            }
        }
    }

    // ==========================================
    // 7. HELPER & BACKEND
    // ==========================================
    function switchMode(mode) {
        currentMode = mode;
        isRunning = false; 
        clearInterval(timer);
        
        localStorage.removeItem('pomo_targetTime');
        localStorage.setItem('pomo_timeLeft', durations[mode]);
        localStorage.setItem('pomo_mode', mode);
        localStorage.setItem('pomo_isRunning', false);
        
        app.classList.remove('timer-running', 'timer-paused');
        if(startStopBtn) startStopBtn.textContent = 'START';
        
        document.querySelectorAll('nav button').forEach(btn => btn.classList.remove('active'));
        if (mode === 'pomodoro') document.getElementById('pomodoroBtn').classList.add('active');
        if (mode === 'shortBreak') document.getElementById('shortBreakBtn').classList.add('active');
        if (mode === 'longBreak') document.getElementById('longBreakBtn').classList.add('active');
        
        // === UPDATE BACKGROUND & CHARACTER (FITUR 5) ===
        updateBackground();
        if(charElement && charImages[mode]) {
            charElement.src = charImages[mode];
        }

        updateDisplay();
    }

    function resetTimer() {
        clearInterval(timer); isRunning = false;
        localStorage.removeItem('pomo_targetTime');
        localStorage.setItem('pomo_timeLeft', durations[currentMode]);
        localStorage.setItem('pomo_isRunning', false);
        app.classList.remove('timer-running', 'timer-paused');
        if(startStopBtn) startStopBtn.textContent = 'START';
        updateDisplay(); fetchStatsAndReport(); 
    }

    async function saveLogToBackend(duration, status) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        try {
            const response = await fetch('/api/log', {
                method: 'POST', 
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({type: currentMode, duration: duration, status: status})
            });
            if (!response.ok) {
                showToast("Gagal simpan data!", 'error');
            } else {
                await fetchStatsAndReport();
            }
        } catch (error) { console.error(error); }
    }

    function updateBackground() { document.body.style.backgroundImage = `url('${backgrounds[currentMode]}')`; }
    function playSound(audio) { audio.currentTime = 0; audio.play().catch(e => {}); }

    // INIT
    document.getElementById('pomodoroBtn').addEventListener('click', () => switchMode('pomodoro'));
    document.getElementById('shortBreakBtn').addEventListener('click', () => switchMode('shortBreak'));
    document.getElementById('longBreakBtn').addEventListener('click', () => switchMode('longBreak'));
    startStopBtn.addEventListener('click', startTimer);
    resetBtn.addEventListener('click', resetTimer);
    skipBtn.addEventListener('click', skipSession);

    if (isRunning) {
        startStopBtn.textContent = 'PAUSE'; app.classList.add('timer-running');
        timer = setInterval(() => { if (getTimeLeft() <= 0) { completeSession(); return; } updateDisplay(); }, 1000);
    } else {
        if(localStorage.getItem('pomo_timeLeft') && parseInt(localStorage.getItem('pomo_timeLeft')) < durations[currentMode]) {
            startStopBtn.textContent = 'RESUME'; app.classList.add('timer-paused');
        }
    }
    updateDisplay(); updateBackground(); fetchStatsAndReport();
});