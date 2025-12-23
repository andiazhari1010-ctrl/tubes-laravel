<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Community Hub</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <style>
        body { background-color: #e8f5e9; font-family: 'Poppins', sans-serif; }
        
        /* Pixel Header */
        .pixel-header { font-family: 'Press Start 2P'; color: #1b5e20; margin-bottom: 20px; }

        /* Custom Tabs Pixel Style */
        .nav-tabs { border-bottom: 4px solid #1b5e20; }
        .nav-link {
            border: 4px solid transparent; 
            color: #555; font-family: 'Press Start 2P'; font-size: 10px;
            margin-bottom: -4px; border-radius: 0;
        }
        .nav-link.active {
            background-color: #fff;
            border-color: #1b5e20 #1b5e20 #fff #1b5e20;
            color: #1b5e20;
        }
        .nav-link:hover { border-color: #ddd; }

        /* Card Style */
        .card-pixel {
            background: #fff; border: 4px solid #1b5e20;
            padding: 15px; margin-bottom: 15px;
            box-shadow: 6px 6px 0 rgba(0,0,0,0.1);
            transition: transform 0.1s;
        }
        .card-pixel:hover { transform: translate(-2px, -2px); box-shadow: 8px 8px 0 rgba(0,0,0,0.1); }

        .btn-pixel {
            border: 2px solid #1b5e20; border-radius: 0;
            font-family: 'Press Start 2P'; font-size: 8px;
            box-shadow: 3px 3px 0 rgba(0,0,0,0.2);
        }
        .btn-pixel:active { transform: translate(2px, 2px); box-shadow: 1px 1px 0 rgba(0,0,0,0.2); }

        /* --- PIXEL SEARCH BAR STYLES (BARU) --- */
        .pixel-search-group {
            box-shadow: 6px 6px 0 rgba(0,0,0,0.15); /* Bayangan kotak kasar */
            transition: all 0.2s;
        }

        .pixel-search-group:hover {
            transform: translate(-2px, -2px); /* Efek melayang saat di-hover */
            box-shadow: 8px 8px 0 rgba(0,0,0,0.2);
        }

        .pixel-input-icon {
            background-color: #1b5e20; /* Warna hijau tua */
            border: 4px solid #1b5e20;
            border-radius: 0; /* Sudut tajam */
            color: #fff;
            font-size: 1.2rem;
        }

        .pixel-input-field {
            border: 4px solid #1b5e20;
            border-radius: 0;
            font-family: 'Poppins', sans-serif; 
            font-size: 14px;
            background-color: #fff;
            color: #333;
        }

        .pixel-input-field:focus {
            box-shadow: none;
            border-color: #1b5e20;
            background-color: #f1f8e9; /* Hijau sangat muda saat aktif */
        }

        .pixel-btn-action {
            background-color: #2e7d32;
            color: white;
            border: 4px solid #1b5e20;
            border-left: none;
            border-radius: 0;
            font-family: 'Press Start 2P', cursive;
            font-size: 10px;
            padding: 0 20px;
        }

        .pixel-btn-action:hover {
            background-color: #1b5e20;
            color: #fff;
        }
    </style>
</head>
<body class="pb-5">

    <div class="container mt-4">
        <div class="d-flex align-items-center justify-content-between pixel-header">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ url('/index') }}" class="text-decoration-none btn btn-dark rounded-0" style="font-family:'Press Start 2P'; font-size:10px;">⬅ BACK</a>
                <h4 class="m-0" style="font-size:16px; line-height: 1.5;">COMMUNITY HUB</h4>
            </div>
            <div class="badge bg-success rounded-0 p-2" style="font-family:'Press Start 2P'; font-size:10px;">ONLINE</div>
        </div>

        <div class="mb-4 position-relative">
            <div class="input-group pixel-search-group">
                <span class="input-group-text pixel-input-icon">
                    🔎
                </span>
                
                <input type="text" 
                       id="pageSearchInput" 
                       class="form-control pixel-input-field" 
                       placeholder="FIND USERNAME..." 
                       autocomplete="off">
                       
                <button class="btn pixel-btn-action" type="button">SEARCH</button>
            </div>

            <div id="pageSearchResults" class="position-absolute w-100 mt-2" style="z-index: 999;"></div>
        </div>

        <ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="friends-tab" data-bs-toggle="tab" data-bs-target="#friends-pane" type="button">🤝 MY SQUAD</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="requests-tab" data-bs-toggle="tab" data-bs-target="#requests-pane" type="button">📬 INBOX <span id="reqCount" class="badge bg-danger rounded-0" style="display:none">0</span></button>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">
            
            <div class="tab-pane fade show active" id="friends-pane">
                <div id="friendList" class="row">
                    <div class="col-12 text-center text-muted p-5">Loading friends...</div>
                </div>
            </div>

            <div class="tab-pane fade" id="requests-pane">
                <div id="requestList" class="row">
                    <div class="col-12 text-center text-muted p-5">No pending requests.</div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        document.addEventListener('DOMContentLoaded', () => {
            loadFriends();
            loadRequests();
        });

        // 1. LOAD DAFTAR TEMAN
        async function loadFriends() {
            const list = document.getElementById('friendList');
            try {
                const res = await fetch('/api/friend/list');
                const data = await res.json();
                
                list.innerHTML = '';
                if(data.length === 0) {
                    list.innerHTML = '<div class="text-center text-muted fst-italic p-5">You have no friends yet. Use search to find some!</div>';
                    return;
                }

                data.forEach(f => {
                    list.innerHTML += `
                    <div class="col-md-6">
                        <div class="card-pixel d-flex flex-row align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <div style="background:#42a5f5; color:white; width:40px; height:40px; display:grid; place-items:center; border:2px solid black; font-weight:bold;">${f.name[0].toUpperCase()}</div>
                                <div>
                                    <div class="fw-bold text-dark">${f.name}</div>
                                    <div class="text-muted" style="font-size:10px;">${f.email}</div>
                                </div>
                            </div>
                            <button class="btn btn-sm btn-outline-danger btn-pixel rounded-0" 
                                    onclick="unfriendUser(${f.id}, '${f.name}')">UNFRIEND</button>
                        </div>
                    </div>`;
                });
            } catch(e){ console.error(e); }
        }

        // 2. LOAD REQUEST MASUK
        async function loadRequests() {
            const list = document.getElementById('requestList');
            try {
                const res = await fetch('/api/friend/requests');
                const data = await res.json();
                
                // Update Badge Counter
                const badge = document.getElementById('reqCount');
                if(data.length > 0) { badge.innerText = data.length; badge.style.display = 'inline-block'; }
                else { badge.style.display = 'none'; }

                list.innerHTML = '';
                if(data.length === 0) {
                    list.innerHTML = '<div class="text-center text-muted fst-italic p-5">No new requests.</div>';
                    return;
                }

                data.forEach(req => {
                    list.innerHTML += `
                    <div class="col-md-12">
                        <div class="card-pixel" style="background:#fff3e0;">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div style="background:#ff7043; color:white; width:40px; height:40px; display:grid; place-items:center; border:2px solid black; font-weight:bold;">${req.name[0].toUpperCase()}</div>
                                    <div>
                                        <div class="fw-bold text-dark">${req.name}</div>
                                        <div class="text-muted" style="font-size:10px;">Wants to connect with you!</div>
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <button onclick="respond(${req.id}, 'accept')" class="btn btn-success btn-pixel">ACCEPT ✔</button>
                                    <button onclick="respond(${req.id}, 'reject')" class="btn btn-danger btn-pixel">REJECT ✖</button>
                                </div>
                            </div>
                        </div>
                    </div>`;
                });
            } catch(e){ console.error(e); }
        }

        // 3. RESPOND (TERIMA/TOLAK REQUEST)
        async function respond(id, action) {
            if(!confirm(action === 'accept' ? "Accept request?" : "Reject request?")) return;
            try {
                // Perbaikan: Endpoint API menerima ID User pengirim (req.id), bukan ID friendship
                await fetch(`/api/friend/${action}/${id}`, { 
                    method: 'POST', 
                    headers: {'X-CSRF-TOKEN': csrfToken} 
                });
                
                // Refresh data setelah respond
                loadRequests(); 
                loadFriends();  
            } catch(e) { alert("Error processing request"); }
        }

        // 4. SEARCH LOGIC
        const searchInput = document.getElementById('pageSearchInput');
        const searchResults = document.getElementById('pageSearchResults');
        let timeout = null;

        searchInput.addEventListener('input', function() {
            clearTimeout(timeout);
            const query = this.value.trim();
            if(!query) { searchResults.innerHTML = ''; return; }
            
            // Delay 500ms agar tidak spam API
            timeout = setTimeout(async () => {
                const res = await fetch(`/api/search?q=${query}`);
                const users = await res.json();
                searchResults.innerHTML = '';
                
                if(users.length === 0) {
                    searchResults.innerHTML = '<div class="card-pixel bg-white p-2 text-muted text-center">No users found.</div>';
                    return;
                }

                users.forEach(u => {
                    const div = document.createElement('div');
                    // Perbaikan: Tambahkan bg-white agar teks terbaca jelas karena position-absolute
                    div.className = 'card-pixel p-2 mb-2 d-flex justify-content-between align-items-center bg-white'; 
                    div.style.borderWidth = '2px';
                    // Pastikan Z-Index tinggi agar muncul di atas tab
                    div.style.zIndex = '1000'; 
                    
                    div.innerHTML = `
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:30px; height:30px; background:#1b5e20; color:white; border-radius:50%; display:grid; place-items:center; font-weight:bold; font-size:10px;">${u.name[0].toUpperCase()}</div>
                            <span class="fw-bold text-dark" style="font-size:12px;">${u.name}</span>
                        </div>
                        <button class="btn btn-sm btn-success btn-pixel" onclick="addFriend(${u.id}, '${u.name}')">+ ADD</button>
                    `;
                    searchResults.appendChild(div);
                });
            }, 500);
        });

        async function addFriend(id, name) {
            if(!confirm("Add " + name + "?")) return;
            try {
                const res = await fetch('/api/friend/add', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ friend_id: id })
                });
                const data = await res.json();
                
                if(res.ok) {
                    alert("Request sent!");
                    searchInput.value = '';
                    searchResults.innerHTML = '';
                } else {
                    alert(data.message);
                }
            } catch(e) { 
                alert("Network Error"); 
            }
        }

        // 5. UNFRIEND LOGIC
        async function unfriendUser(id, name) {
            if(!confirm(`Are you sure you want to unfriend ${name}? 😢`)) return;
            
            try {
                const res = await fetch('/api/friend/unfriend', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ friend_id: id })
                });

                if(res.ok) {
                    alert(`${name} has been removed from your friends.`);
                    loadFriends();
                } else {
                    alert("Failed to unfriend.");
                }
            } catch(e) { 
                alert("Network Error"); 
            }
        }

    </script>
</body>
</html>