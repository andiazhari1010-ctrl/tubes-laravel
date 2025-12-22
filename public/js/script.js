document.addEventListener("DOMContentLoaded", () => {
    console.log("Pixel Dashboard v7.0 (Searchable Friends) Loaded! 🚀");

    // ============================================
    // 1. SEARCH BAR & ADD FRIEND
    // ============================================
    const searchInput = document.getElementById("searchInput");
    const searchDropdown = document.getElementById("searchDropdown");
    let searchTimeout = null;

    if (searchInput && searchDropdown) {
        searchInput.addEventListener('focus', () => searchInput.value.trim() === "" ? showDefaultMenu() : performSearch(searchInput.value));
        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            clearTimeout(searchTimeout);
            if(query === "") { showDefaultMenu(); return; }
            searchTimeout = setTimeout(() => performSearch(query), 400);
        });
        document.addEventListener('click', (e) => {
            if (!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) searchDropdown.style.display = 'none';
        });

        function showDefaultMenu() {
            searchDropdown.innerHTML = '';
            searchDropdown.innerHTML += '<div class="p-2 fw-bold text-muted" style="font-size:10px;">QUICK MENU</div>';
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

        async function performSearch(query) {
            searchDropdown.innerHTML = '<div class="p-3 text-center text-muted" style="font-size:12px;">Searching... ⏳</div>';
            searchDropdown.style.display = 'block';
            try {
                const res = await fetch(`/api/search?q=${query}`);
                const users = res.ok ? await res.json() : [];
                searchDropdown.innerHTML = '';
                if(users.length > 0) {
                    searchDropdown.innerHTML += '<div class="p-2 fw-bold text-primary" style="font-size:10px;">PEOPLE</div>';
                    users.forEach(user => {
                        const div = document.createElement('div');
                        div.className = 'search-item';
                        div.innerHTML = `
                            <div class="d-flex align-items-center">
                                <div style="width:30px; height:30px; background:#1b5e20; color:white; border-radius:50%; display:grid; place-items:center; margin-right:10px; font-weight:bold;">${user.name[0].toUpperCase()}</div>
                                <div><div class="fw-bold text-dark">${user.name}</div><div class="text-muted" style="font-size:10px;">${user.email}</div></div>
                            </div>
                            <button class="btn btn-sm btn-success badge-pixel rounded-0">+ ADD</button>
                        `;
                        div.querySelector('button').addEventListener('click', (e) => { e.stopPropagation(); addFriend(user.id, user.name); });
                        searchDropdown.appendChild(div);
                    });
                } else { searchDropdown.innerHTML += `<div class="p-2 text-center text-muted" style="font-size:12px;">No user found named "${query}"</div>`; }
            } catch (error) { console.error(error); }
        }

        async function addFriend(id, name) {
            if(!confirm(`Add ${name} as friend?`)) return;
            const token = document.querySelector('meta[name="csrf-token"]')?.content;
            try {
                const res = await fetch('/api/friend/add', {
                    method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
                    body: JSON.stringify({ friend_id: id })
                });
                const data = await res.json();
                alert(res.ok ? `✅ Request sent to ${name}!` : `⚠️ ${data.message}`);
                if(res.ok) { searchDropdown.style.display = 'none'; searchInput.value = ''; }
            } catch(e) { alert("Network Error"); }
        }
    }

    // ============================================
    // 2. CALENDAR WIDGET (UPDATED WITH SEARCHABLE FRIEND)
    // ============================================
    const calendarEl = document.getElementById("calendar");
    if (calendarEl) initCalendar();

    async function initCalendar() {
        let events = [];
        try {
            const res = await fetch('/api/events');
            if(res.ok) events = await res.json();
        } catch(e) { console.error(e); }

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
            dayCell.style.cssText = "padding:5px; font-size:12px; cursor:pointer; font-family: monospace; position:relative;";
            
            const yyyy = date.getFullYear();
            const mm = String(date.getMonth() + 1).padStart(2, '0');
            const dd = String(i).padStart(2, '0');
            const dateString = `${yyyy}-${mm}-${dd}`;

            const hasEvent = events.find(e => e.event_date === dateString);
            
            if (hasEvent) {
                dayCell.style.background = hasEvent.color || "#ef5350"; 
                dayCell.style.color = "white"; dayCell.style.borderRadius = "4px";
                dayCell.title = hasEvent.title;
            } else if (i === date.getDate()) {
                dayCell.style.background = "#66bb6a"; dayCell.style.color = "white"; dayCell.style.borderRadius = "4px";
            }

            dayCell.addEventListener('click', () => openEventModal(dateString, hasEvent));
            grid.appendChild(dayCell);
        }
    }

    // --- MODAL LOGIC (UPDATED: SEARCHABLE INPUT) ---
    const eventModal = document.getElementById('eventModal');
    
    // Elemen Baru
    const friendNameInput = document.getElementById('friendNameInput'); // Input Teks
    const friendDatalist = document.getElementById('friendOptions');    // List Opsi
    const friendIdHidden = document.getElementById('eventFriendId');    // Simpan ID

    // Cache daftar teman biar gak fetch berulang-ulang
    let myFriendsList = [];

    window.openEventModal = async (dateStr, eventData = null) => {
        if(!document.getElementById('eventDateInput')) return;

        document.getElementById('eventDateInput').value = dateStr;
        document.getElementById('eventDateLabel').textContent = "Date: " + dateStr;
        
        // Reset Input Teman
        if(friendNameInput) {
            friendNameInput.value = '';
            friendIdHidden.value = '';
            friendNameInput.disabled = false;
        }

        // LOAD FRIENDS (Isi Datalist)
        if(friendDatalist) {
            try {
                // Fetch hanya jika list masih kosong (Optimasi)
                if(myFriendsList.length === 0) {
                    const res = await fetch('/api/friend/list');
                    myFriendsList = await res.json();
                }

                // Render ke Datalist
                friendDatalist.innerHTML = '';
                myFriendsList.forEach(f => {
                    const opt = document.createElement('option');
                    opt.value = f.name; // Yang muncul saat diketik
                    // Kita simpan ID di attribute custom, nanti diambil pas user milih
                    opt.setAttribute('data-id', f.id); 
                    friendDatalist.appendChild(opt);
                });
            } catch(e) { console.error("Error loading friends", e); }
        }

        if (eventData) {
            // MODE: EDIT
            document.getElementById('eventIdInput').value = eventData.id;
            document.getElementById('eventTitleInput').value = eventData.title;
            document.getElementById('deleteEventBtn').style.display = 'inline-block';
            document.getElementById('saveEventBtn').textContent = "UPDATE";
            
            // Disable collab saat edit (agar simpel)
            if(friendNameInput) friendNameInput.disabled = true; 
        } else {
            // MODE: NEW
            document.getElementById('eventIdInput').value = '';
            document.getElementById('eventTitleInput').value = '';
            document.getElementById('deleteEventBtn').style.display = 'none';
            document.getElementById('saveEventBtn').textContent = "SAVE";
        }
        eventModal.style.display = 'block';
    }

    // LOGIC PENTING: Mendapatkan ID saat user mengetik/memilih nama
    if(friendNameInput) {
        friendNameInput.addEventListener('input', function() {
            const val = this.value;
            const options = friendDatalist.childNodes; // Ambil semua opsi di datalist
            friendIdHidden.value = ''; // Reset ID dulu
            
            // Cari ID berdasarkan nama yang diketik user
            // Ini looping manual karena datalist native tidak menyimpan value ID secara langsung
            for (let i = 0; i < options.length; i++) {
                if (options[i].value === val) {
                    friendIdHidden.value = options[i].getAttribute('data-id');
                    break;
                }
            }
        });
    }

    // BUTTON SAVE (UPDATED: Ambil ID dari Hidden Input)
    document.getElementById('saveEventBtn')?.addEventListener('click', async () => {
        const title = document.getElementById('eventTitleInput').value;
        const dateVal = document.getElementById('eventDateInput').value;
        
        // Ambil ID teman dari hidden input (BUKAN dari text input)
        const friendId = friendIdHidden ? friendIdHidden.value : null; 
        
        const token = document.querySelector('meta[name="csrf-token"]')?.content;

        if(!title) return alert("Enter agenda title!");

        const oldId = document.getElementById('eventIdInput').value;
        if(oldId) await fetch(`/api/events/${oldId}`, { method: 'DELETE', headers: {'X-CSRF-TOKEN': token} });

        await fetch('/api/events', {
            method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
            body: JSON.stringify({ title: title, event_date: dateVal, friend_id: friendId })
        });
        
        eventModal.style.display = 'none';
        initCalendar();
    });

    document.getElementById('deleteEventBtn')?.addEventListener('click', async () => {
        if(!confirm("Delete this agenda?")) return;
        const id = document.getElementById('eventIdInput').value;
        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        await fetch(`/api/events/${id}`, { method: 'DELETE', headers: {'X-CSRF-TOKEN': token} });
        eventModal.style.display = 'none';
        initCalendar();
    });

    // ============================================
    // 3. UI EXTRAS & NOTIFIKASI SYSTEM
    // ============================================
    const clockEl = document.getElementById("clock");
    if(clockEl) setInterval(() => clockEl.textContent = new Date().toLocaleTimeString('en-US', {hour12:false}), 1000);

    const logoutBtn = document.getElementById("logoutBtn");
    const logoutPopup = document.getElementById("logoutPopup");
    if(logoutBtn && logoutPopup) {
        logoutBtn.addEventListener("click", () => logoutPopup.style.display = 'block');
        document.getElementById("logoutCancel")?.addEventListener("click", () => logoutPopup.style.display = 'none');
        document.getElementById("logoutYes")?.addEventListener("click", () => window.location.href = '/logout');
    }

    const avatarBox = document.querySelector(".avatar-box");
    if (avatarBox) {
        avatarBox.addEventListener("click", () => avatarBox.classList.toggle("rotate"));
        const style = document.createElement("style");
        style.innerHTML = `@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } } .rotate { animation: spin 5s linear infinite; }`;
        document.head.appendChild(style);
    }

    // === NOTIFIKASI UTAMA ===
    const notifBtn = document.querySelector('a[title="Notifications"]');
    const notifBadge = document.getElementById('notifBadge');
    
    if(notifBtn) {
        let notifBox = document.createElement('div');
        notifBox.id = 'notifDropdown';
        notifBox.style.cssText = "display:none; position:absolute; top:60px; right:70px; width:280px; background:#fff; border:3px solid #66bb6a; border-radius:8px; z-index:9999; box-shadow:0 5px 15px rgba(0,0,0,0.2); max-height:300px; overflow-y:auto;";
        document.body.appendChild(notifBox);

        loadNotifications(true); 

        notifBtn.addEventListener('click', (e) => {
            e.preventDefault();
            if(notifBox.style.display === 'block') notifBox.style.display = 'none';
            else { notifBox.style.display = 'block'; loadNotifications(false); }
        });

        document.addEventListener('click', (e) => {
            if (!notifBtn.contains(e.target) && !notifBox.contains(e.target)) notifBox.style.display = 'none';
        });

        async function loadNotifications(onlyCount = false) {
            try {
                const res = await fetch('/api/notifications');
                const data = await res.json();
                
                // Update Badge
                const unreadCount = data.filter(n => !n.is_read).length;
                if(notifBadge) {
                    notifBadge.innerText = unreadCount;
                    notifBadge.style.display = unreadCount > 0 ? 'block' : 'none';
                }

                if(onlyCount) return;

                // Render List
                notifBox.innerHTML = '';
                if(data.length === 0) { 
                    notifBox.innerHTML = '<div style="padding:10px; font-size:12px; color:#888;">No notifications.</div>'; 
                } else {
                    data.forEach(n => {
                        const item = document.createElement('div');
                        item.style.cssText = `padding:10px; border-bottom:1px solid #eee; cursor:pointer; background:${n.is_read ? '#fff' : '#e8f5e9'};`;
                        
                        // CEK TIPE NOTIFIKASI
                        if (n.type === 'invite') {
                            item.innerHTML = `
                                <div style="font-weight:bold; font-size:11px; color:#1565c0;">📩 ${n.title}</div>
                                <div style="font-size:10px; color:#333; margin-bottom:5px;">${n.message}</div>
                                <div style="display:flex; gap:5px;">
                                    <button onclick="respondInvite(${n.id}, 'accept')" class="btn btn-sm btn-success badge-pixel p-1" style="font-size:9px;">ACCEPT</button>
                                    <button onclick="respondInvite(${n.id}, 'reject')" class="btn btn-sm btn-danger badge-pixel p-1" style="font-size:9px;">REJECT</button>
                                </div>
                            `;
                        } else {
                            item.innerHTML = `
                                <div style="font-weight:bold; font-size:11px; color:#2e7d32;">${n.title}</div>
                                <div style="font-size:10px; color:#333;">${n.message}</div>
                            `;
                            item.addEventListener('click', async () => {
                                const token = document.querySelector('meta[name="csrf-token"]')?.content;
                                if(token) await fetch(`/api/notifications/${n.id}/read`, {method:'POST', headers:{'X-CSRF-TOKEN':token}});
                                if(n.link && n.link !== '#') window.location.href = n.link;
                            });
                        }
                        notifBox.appendChild(item);
                    });
                }
                const seeAllDiv = document.createElement('div');
                seeAllDiv.style.cssText = "padding:10px; text-align:center; background:#f1f8e9; cursor:pointer; font-weight:bold; font-size:10px; color:#1b5e20;";
                seeAllDiv.innerText = "VIEW ALL HISTORY 📜";
                seeAllDiv.addEventListener('click', () => window.location.href = '/notifications');
                notifBox.appendChild(seeAllDiv);

            } catch(e) { console.error(e); }
        }

        // FUNGSI RESPON UNDANGAN
        window.respondInvite = async (id, action) => {
            const token = document.querySelector('meta[name="csrf-token"]')?.content;
            if(!confirm(action === 'accept' ? "Terima undangan kegiatan?" : "Tolak undangan?")) return;

            try {
                const res = await fetch(`/api/invite/${id}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
                    body: JSON.stringify({ action: action })
                });
                if(res.ok) {
                    alert("Response Sent!");
                    loadNotifications(false); 
                    if(action === 'accept') initCalendar(); 
                }
            } catch(e) { alert("Error sending response"); }
        };
    }
});