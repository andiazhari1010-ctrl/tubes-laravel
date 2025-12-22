<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Friend List - Pixel Dashboard</title>
    
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <style>
        /* CSS Khusus Halaman Ini (Override dikit biar rapi) */
        body { overflow-y: auto; padding-bottom: 50px; }
        
        .friend-container { 
            width: 90%; max-width: 800px; 
            margin: 100px auto 20px auto; /* Jarak dari top bar */
        }

        .section-title { 
            font-family: 'Press Start 2P', cursive;
            font-size: 14px; 
            color: #1b5e20;
            margin-bottom: 15px; 
            border-bottom: 4px solid #66bb6a; 
            padding-bottom: 10px; 
            display: inline-block;
            text-shadow: 2px 2px 0 rgba(255,255,255,0.5);
        }
        
        /* Grid Layout */
        .friend-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); 
            gap: 20px; 
            margin-bottom: 50px; 
        }
        
        /* Card Style */
        .friend-card { 
            background: rgba(255, 255, 255, 0.9); 
            border: 3px solid #66bb6a; 
            border-radius: 12px; 
            padding: 15px; 
            display: flex; align-items: center; gap: 15px; 
            box-shadow: 4px 4px 0 rgba(0,0,0,0.1); 
            transition: transform 0.1s;
        }
        .friend-card:hover { transform: translateY(-2px); }

        .f-avatar { 
            width: 45px; height: 45px; 
            background: #ccc; border-radius: 50%; 
            display: flex; align-items: center; justify-content: center; 
            color: #fff; font-weight: bold; font-family: 'Press Start 2P'; font-size: 16px;
            border: 3px solid #333;
        }
        
        .f-info { flex: 1; overflow: hidden; }
        .f-name { 
            font-size: 14px; font-weight: bold; color: #1b5e20;
            margin-bottom: 2px; 
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis; 
            font-family: 'Poppins', sans-serif; 
        }
        .f-email { font-size: 10px; color: #666; font-family: 'Poppins', sans-serif; }
        
        /* Buttons */
        .action-btn { 
            border: 2px solid rgba(0,0,0,0.2); 
            padding: 8px; width: 35px; height: 35px;
            font-family: 'Press Start 2P', cursive; font-size: 10px; 
            cursor: pointer; border-radius: 8px; color: white; margin-left: 5px; 
            display: flex; align-items: center; justify-content: center;
        }
        .btn-accept { background: #66bb6a; box-shadow: 0 4px 0 #388e3c; }
        .btn-reject { background: #ef5350; box-shadow: 0 4px 0 #c62828; }
        
        .btn-accept:active, .btn-reject:active { 
            transform: translateY(4px); box-shadow: none; 
        }

        .empty-msg { 
            font-family: 'Poppins', sans-serif; font-size: 14px; color: #555; 
            background: rgba(255,255,255,0.6); padding: 15px; border-radius: 8px; 
            text-align: center; border: 2px dashed #aaa;
        }
    </style>
</head>
<body>

    <header class="top-bar">
        <a href="{{ url('/index') }}" class="icon-box" title="Back to Dashboard">⬅️</a>
        <div style="flex:1; text-align:center; font-size:14px; color:#1b5e20; font-weight:bold;">
            👥 SOCIAL AREA
        </div>
        <div style="width:50px;"></div>
    </header>

    <div class="friend-container">
        
        <div class="section-title">📬 Incoming Requests</div>
        <div id="requestList" class="friend-grid">
            <div class="empty-msg">Loading requests...</div>
        </div>

        <div class="section-title">🤝 My Friends</div>
        <div id="friendList" class="friend-grid">
            <div class="empty-msg">Loading friends...</div>
        </div>

    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        loadRequests();
        loadFriends();
    });

    // Ambil Token CSRF
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // 1. Load Requests (Pending)
    async function loadRequests() {
        const container = document.getElementById('requestList');
        try {
            const res = await fetch('/api/friend/requests');
            const data = await res.json();

            container.innerHTML = '';
            if(data.length === 0) {
                container.innerHTML = '<div class="empty-msg">Tidak ada permintaan pertemanan baru.</div>';
                return;
            }

            data.forEach(req => {
                const html = `
                <div class="friend-card" style="background:#fff3e0; border-color:#ffb74d;">
                    <div class="f-avatar" style="background:#ff7043;">${req.name[0].toUpperCase()}</div>
                    <div class="f-info">
                        <div class="f-name">${req.name}</div>
                        <div class="f-email">Ingin menjadi temanmu</div>
                    </div>
                    <div style="display:flex;">
                        <button class="action-btn btn-accept" onclick="acceptFriend(${req.id})" title="Terima">✔</button>
                        <button class="action-btn btn-reject" onclick="rejectFriend(${req.id})" title="Tolak">✖</button>
                    </div>
                </div>`;
                container.innerHTML += html;
            });
        } catch(e) { console.error(e); }
    }

    // 2. Load Friends (Accepted)
    async function loadFriends() {
        const container = document.getElementById('friendList');
        try {
            const res = await fetch('/api/friend/list');
            const data = await res.json();

            container.innerHTML = '';
            if(data.length === 0) {
                container.innerHTML = '<div class="empty-msg">Belum punya teman. Cari teman di Dashboard!</div>';
                return;
            }

            data.forEach(fr => {
                const html = `
                <div class="friend-card">
                    <div class="f-avatar" style="background:#42a5f5;">${fr.name[0].toUpperCase()}</div>
                    <div class="f-info">
                        <div class="f-name">${fr.name}</div>
                        <div class="f-email">${fr.email}</div>
                    </div>
                </div>`;
                container.innerHTML += html;
            });
        } catch(e) { console.error(e); }
    }

    // 3. Aksi Accept
    window.acceptFriend = async (id) => {
        if(!confirm("Terima pertemanan?")) return;
        await fetch(`/api/friend/accept/${id}`, {
            method: 'POST', headers: {'X-CSRF-TOKEN': csrfToken}
        });
        loadRequests(); // Refresh request
        loadFriends();  // Refresh friend list
    };

    // 4. Aksi Reject
    window.rejectFriend = async (id) => {
        if(!confirm("Tolak permintaan?")) return;
        await fetch(`/api/friend/reject/${id}`, {
            method: 'POST', headers: {'X-CSRF-TOKEN': csrfToken}
        });
        loadRequests();
    };
    </script>
</body>
</html>