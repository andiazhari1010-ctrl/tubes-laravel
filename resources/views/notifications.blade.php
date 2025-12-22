<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Notifications</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        body { background-color: #e8f5e9; font-family: 'Poppins', sans-serif; }
        .pixel-header { font-family: 'Press Start 2P'; color: #1b5e20; margin-bottom: 30px; }
        .notif-card {
            border: 4px solid #1b5e20; background: #fff; padding: 15px; margin-bottom: 15px;
            box-shadow: 4px 4px 0 rgba(0,0,0,0.1); transition: transform 0.1s;
        }
        .notif-card:hover { transform: translate(-2px, -2px); box-shadow: 6px 6px 0 rgba(0,0,0,0.1); }
        .notif-unread { border-left: 10px solid #ef5350; background: #fff3e0; }
        .btn-pixel { border: 2px solid #1b5e20; border-radius: 0; font-size: 10px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container mt-5 mb-5">
        <div class="d-flex align-items-center gap-3 pixel-header">
            <a href="{{ url('/index') }}" class="text-decoration-none btn btn-dark rounded-0" style="font-size:12px;">⬅ BACK</a>
            <h4 class="m-0" style="font-size:16px; line-height: 1.5;">NOTIFICATIONS CENTER</h4>
        </div>

        <div class="row">
            <div class="col-md-8 mx-auto">
                @forelse($notifs as $n)
                    <div class="notif-card {{ $n->is_read ? '' : 'notif-unread' }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong style="color: {{ $n->type == 'invite' ? '#1565c0' : '#2e7d32' }};">
                                    {{ $n->type == 'invite' ? '📩 ' : '' }} {{ $n->title }}
                                </strong>
                                
                                <div class="mt-1 text-dark" style="font-size: 14px;">{{ $n->message }}</div>
                                
                                @if($n->type == 'invite')
                                    <div class="mt-2 d-flex gap-2">
                                        <button onclick="respondInvite({{ $n->id }}, 'accept')" class="btn btn-sm btn-success btn-pixel">ACCEPT</button>
                                        <button onclick="respondInvite({{ $n->id }}, 'reject')" class="btn btn-sm btn-danger btn-pixel">REJECT</button>
                                    </div>
                                @endif
                            </div>
                            <small class="text-muted" style="font-size: 10px;">{{ $n->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted fst-italic p-5" style="border: 2px dashed #aaa;">
                        No notifications history found.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        async function respondInvite(id, action) {
            if(!confirm(action === 'accept' ? "Terima undangan?" : "Tolak undangan?")) return;
            
            const token = document.querySelector('meta[name="csrf-token"]').content;
            
            try {
                const res = await fetch(`/api/invite/${id}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
                    body: JSON.stringify({ action: action })
                });

                if(res.ok) {
                    alert("Response Sent!");
                    location.reload(); // Refresh halaman biar statusnya update
                } else {
                    alert("Error processing request.");
                }
            } catch(e) {
                console.error(e);
                alert("Network Error");
            }
        }
    </script>
</body>
</html>