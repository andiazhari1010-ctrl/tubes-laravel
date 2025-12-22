document.addEventListener("DOMContentLoaded", () => {
    console.log("Pixel Dashboard Script Loaded! 🚀");

    // ============================================
    // 1. SEARCH BAR LOGIC (BOOTSTRAP + API)
    // ============================================
    const searchInput = document.getElementById("searchInput");
    const searchDropdown = document.getElementById("searchDropdown");
    let searchTimeout = null;

    if (searchInput && searchDropdown) {
        
        // A. Saat Klik Input -> Menu Shortcut
        searchInput.addEventListener('focus', () => {
            if(searchInput.value.trim() === "") showDefaultMenu();
            else performSearch(searchInput.value);
        });

        // B. Saat Mengetik
        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            clearTimeout(searchTimeout);

            if(query === "") {
                showDefaultMenu();
                return;
            }
            // Debounce 400ms
            searchTimeout = setTimeout(() => performSearch(query), 400);
        });

        // C. Klik di luar -> Tutup
        document.addEventListener('click', (e) => {
            if (!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) {
                searchDropdown.style.display = 'none';
            }
        });

        // FUNGSI: Shortcut Default
        function showDefaultMenu() {
            searchDropdown.innerHTML = '';
            const label = document.createElement('div');
            label.className = 'p-2 fw-bold text-muted';
            label.style.fontSize = '10px';
            label.textContent = 'QUICK MENU';
            searchDropdown.appendChild(label);

            const shortcuts = [
                { icon: '🍅', name: 'Pomodoro Timer', link: '/pomodoro' },
                { icon: '📝', name: 'My Notes', link: '/notes' },
                { icon: '📅', name: 'Calendar', link: '/calendar' },
                { icon: '👥', name: 'Friend Requests', link: '/add-friend' }
            ];

            shortcuts.forEach(item => {
                const div = document.createElement('div');
                div.className = 'search-item';
                div.innerHTML = `<span>${item.icon} ${item.name}</span> <span class="badge bg-secondary badge-pixel">GO</span>`;
                div.addEventListener('click', () => window.location.href = item.link);
                searchDropdown.appendChild(div);
            });
            searchDropdown.style.display = 'block';
        }

        // FUNGSI: Cari User via API
        async function performSearch(query) {
            searchDropdown.innerHTML = '<div class="p-3 text-center text-muted" style="font-size:12px;">Searching... ⏳</div>';
            searchDropdown.style.display = 'block';

            try {
                const res = await fetch(`/api/search?q=${query}`);
                const users = res.ok ? await res.json() : [];

                searchDropdown.innerHTML = '';

                if(users.length > 0) {
                    const userLabel = document.createElement('div');
                    userLabel.className = 'p-2 fw-bold text-primary';
                    userLabel.style.fontSize = '10px';
                    userLabel.textContent = `PEOPLE (${users.length})`;
                    searchDropdown.appendChild(userLabel);

                    users.forEach(user => {
                        const div = document.createElement('div');
                        div.className = 'search-item';
                        div.innerHTML = `
                            <div class="d-flex align-items-center">
                                <div style="width:30px; height:30px; background:#1b5e20; color:white; border-radius:50%; display:flex; align-items:center; justify-content:center; margin-right:10px; font-weight:bold;">
                                    ${user.name[0].toUpperCase()}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">${user.name}</div>
                                    <div class="text-muted" style="font-size:10px;">${user.email}</div>
                                </div>
                            </div>
                            <button class="btn btn-sm btn-success badge-pixel rounded-0">+ ADD</button>
                        `;
                        
                        div.querySelector('button').addEventListener('click', (e) => {
                            e.stopPropagation();
                            addFriend(user.id, user.name);
                        });
                        searchDropdown.appendChild(div);
                    });
                } else {
                    searchDropdown.innerHTML += `<div class="p-2 text-center text-muted" style="font-size:12px;">No user found named "${query}"</div>`;
                }
            } catch (error) {
                console.error(error);
                searchDropdown.innerHTML = '<div class="p-3 text-center text-danger">Error fetching data.</div>';
            }
        }

        // FUNGSI: Add Friend
        async function addFriend(id, name) {
            if(!confirm(`Add ${name} as friend?`)) return;
            const token = document.querySelector('meta[name="csrf-token"]')?.content;
            if(!token) return alert("CSRF Token Error");

            try {
                const res = await fetch('/api/friend/add', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
                    body: JSON.stringify({ friend_id: id })
                });
                const data = await res.json();
                
                if(res.ok) {
                    alert(`✅ Request sent to ${name}!`);
                    searchDropdown.style.display = 'none';
                    searchInput.value = '';
                } else {
                    alert(`⚠️ ${data.message}`);
                }
            } catch(e) { alert("Network Error"); }
        }
    }

    // ============================================
    // 2. KALENDER WIDGET (PIXEL ART) - DIKEMBALIKAN
    // ============================================
    const calendarEl = document.getElementById("calendar");
    if (calendarEl) {
        const date = new Date();
        const monthNames = ["JANUARY", "FEBRUARY", "MARCH", "APRIL", "MAY", "JUNE", "JULY", "AUGUST", "SEPTEMBER", "OCTOBER", "NOVEMBER", "DECEMBER"];
        
        calendarEl.innerHTML = `
            <div class="calendar-header" style="text-align:center; margin-bottom:10px; font-weight:bold; border-bottom:2px solid #66bb6a; padding-bottom:5px; font-family: 'Press Start 2P'; font-size: 12px; color: #1b5e20;">
                <span>${monthNames[date.getMonth()]} ${date.getFullYear()}</span>
            </div>
            <div class="calendar-grid" id="calendarGrid" style="display:grid; grid-template-columns:repeat(7, 1fr); gap:2px; text-align:center;">
                <div style="font-size:10px; font-weight:bold; color:#2e7d32;">S</div><div style="font-size:10px; font-weight:bold; color:#2e7d32;">M</div><div style="font-size:10px; font-weight:bold; color:#2e7d32;">T</div><div style="font-size:10px; font-weight:bold; color:#2e7d32;">W</div><div style="font-size:10px; font-weight:bold; color:#2e7d32;">T</div><div style="font-size:10px; font-weight:bold; color:#2e7d32;">F</div><div style="font-size:10px; font-weight:bold; color:#2e7d32;">S</div>
            </div>`;

        const grid = document.getElementById('calendarGrid');
        const firstDay = new Date(date.getFullYear(), date.getMonth(), 1).getDay();
        const daysInMonth = new Date(date.getFullYear(), date.getMonth() + 1, 0).getDate();

        for (let i = 0; i < firstDay; i++) grid.appendChild(document.createElement('div'));
        for (let i = 1; i <= daysInMonth; i++) {
            const dayCell = document.createElement('div');
            dayCell.textContent = i;
            dayCell.style.cssText = "padding:5px; font-size:12px; cursor:default; font-family: monospace;";
            
            if (i === date.getDate()) {
                dayCell.style.cssText += "background:#66bb6a; color:white; border-radius:4px; font-weight:bold; box-shadow: 2px 2px 0 rgba(0,0,0,0.2);";
            }
            grid.appendChild(dayCell);
        }
    }

    // ============================================
    // 3. FITUR LAIN (Clock, Notif, Logout, Avatar)
    // ============================================
    
    // Clock
    const clockEl = document.getElementById("clock");
    if(clockEl) setInterval(() => clockEl.textContent = new Date().toLocaleTimeString('en-US', {hour12:false}), 1000);

    // Logout Popup
    const logoutBtn = document.getElementById("logoutBtn");
    const logoutPopup = document.getElementById("logoutPopup");
    if(logoutBtn && logoutPopup) {
        logoutBtn.addEventListener("click", () => logoutPopup.style.display = 'block');
        document.getElementById("logoutCancel")?.addEventListener("click", () => logoutPopup.style.display = 'none');
        document.getElementById("logoutYes")?.addEventListener("click", () => window.location.href = '/logout');
    }

    // Avatar Animation
    const avatarBox = document.querySelector(".avatar-box");
    if (avatarBox) {
        avatarBox.addEventListener("click", () => avatarBox.classList.toggle("rotate"));
        const style = document.createElement("style");
        style.innerHTML = `@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } } .rotate { animation: spin 5s linear infinite; }`;
        document.head.appendChild(style);
    }

    // Notification System (API)
    const notifBtn = document.querySelector('a[title="Notifications"]');
    if(notifBtn) {
        let notifBox = document.createElement('div');
        notifBox.id = 'notifDropdown';
        notifBox.style.cssText = "display:none; position:absolute; top:60px; right:70px; width:280px; background:#fff; border:3px solid #66bb6a; border-radius:8px; z-index:9999; box-shadow:0 5px 15px rgba(0,0,0,0.2); max-height:300px; overflow-y:auto;";
        document.body.appendChild(notifBox);

        notifBtn.addEventListener('click', (e) => {
            e.preventDefault();
            if(notifBox.style.display === 'block') notifBox.style.display = 'none';
            else { notifBox.style.display = 'block'; loadNotifications(); }
        });

        document.addEventListener('click', (e) => {
            if (!notifBtn.contains(e.target) && !notifBox.contains(e.target)) notifBox.style.display = 'none';
        });

        async function loadNotifications() {
            notifBox.innerHTML = '<div style="padding:10px; font-size:12px;">Loading...</div>';
            try {
                const res = await fetch('/api/notifications');
                const data = await res.json();
                notifBox.innerHTML = '';
                if(data.length === 0) { notifBox.innerHTML = '<div style="padding:10px; font-size:12px; color:#888;">No notifications.</div>'; return; }
                
                data.forEach(n => {
                    const item = document.createElement('div');
                    item.style.cssText = `padding:10px; border-bottom:1px solid #eee; cursor:pointer; background:${n.is_read ? '#fff' : '#e8f5e9'};`;
                    item.innerHTML = `<div style="font-weight:bold; font-size:11px; color:#2e7d32;">${n.title}</div><div style="font-size:10px; color:#333;">${n.message}</div>`;
                    item.addEventListener('click', async () => {
                        const token = document.querySelector('meta[name="csrf-token"]')?.content;
                        if(token) await fetch(`/api/notifications/${n.id}/read`, {method:'POST', headers:{'X-CSRF-TOKEN':token}});
                        if(n.link) window.location.href = n.link;
                    });
                    notifBox.appendChild(item);
                });
            } catch(e) { console.error(e); }
        }
    }
});