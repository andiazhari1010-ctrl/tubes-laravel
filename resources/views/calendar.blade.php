<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Full Calendar</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <style>
      .calendar-grid { height: 60vh; }
      .calendar-grid > div { display: flex; align-items: center; justify-content: center; font-size: 14px !important; }
  </style>
</head>
<body style="background-color: #e8f5e9;">

    <div class="container mt-4 mb-3">
        <a href="{{ url('/index') }}" class="text-decoration-none btn btn-dark rounded-0" style="font-family:'Press Start 2P'; font-size:10px;">⬅ BACK TO DASHBOARD</a>
    </div>

    <div class="container">
        <div class="card p-4" style="border: 4px solid #1b5e20; border-radius: 0; box-shadow: 8px 8px 0 rgba(0,0,0,0.1);">
            <div id="calendar"></div>
        </div>
    </div>

    <div id="eventModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999;">
        <div style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); background:#fff; border:4px solid #1b5e20; padding:20px; width:300px; text-align:center; box-shadow:8px 8px 0 rgba(0,0,0,0.2);">
            
            <h4 style="font-family:'Press Start 2P'; font-size:12px; margin-bottom:15px; color:#1b5e20;">AGENDA</h4>
            
            <input type="hidden" id="eventDateInput">
            <input type="hidden" id="eventIdInput">
            
            <div style="margin-bottom:10px; font-family:'Press Start 2P'; font-size:10px; color:#555;" id="eventDateLabel">Date: -</div>
            
            <input type="text" id="eventTitleInput" placeholder="Event Name..." style="width:100%; padding:10px; font-family:'Poppins'; border:2px solid #1b5e20; margin-bottom:10px; outline:none;">
            
            <label style="font-family:'Press Start 2P'; font-size:8px; color:#555; display:block; text-align:left; margin-bottom:5px;">COLLAB WITH:</label>
        
            <div style="position: relative;">
                <input type="text" id="friendNameInput" class="form-control rounded-0 mb-0" 
                      placeholder="Type to search friend..." autocomplete="off"
                      style="border:2px solid #1b5e20; font-family:'Poppins'; font-size:12px;">
                
                <div id="friendSuggestions" class="suggestion-box"></div>
            </div>

            <input type="hidden" id="eventFriendId">
            <div style="display:flex; justify-content:center; gap:10px; margin-top: 20px;">
                <button id="saveEventBtn" class="btn btn-sm btn-success rounded-0 badge-pixel">SAVE</button>
                <button id="deleteEventBtn" class="btn btn-sm btn-danger rounded-0 badge-pixel" style="display:none;">DELETE</button>
                <button onclick="document.getElementById('eventModal').style.display='none'" class="btn btn-sm btn-secondary rounded-0 badge-pixel">CLOSE</button>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/script.js') }}"></script>
</body>
</html>