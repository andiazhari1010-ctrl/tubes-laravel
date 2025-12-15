<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Pixel Theme Calendar with Toggle Sidebar</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('css/calendar.css') }}">
</head>
<body>

<div class="d-flex" id="app-container">
  <nav class="sidebar flex-shrink-0" id="sidebar">
    <button class="btn-create" id="btnCreateEvent">+ Create Event</button>
    <div style="margin-bottom: 1rem;">
      <h6 id="sidebarMonthYear">November 2025</h6>
      <div class="mini-month">
        <div>S</div><div>M</div><div>T</div><div>W</div><div>T</div><div>F</div><div>S</div>
        <div>26</div><div>27</div><div>28</div><div>29</div><div>30</div><div>31</div><div>1</div>
        <div>2</div><div>3</div><div>4</div><div>5</div><div>6</div><div>7</div><div>8</div>
        <div class="today">9</div><div>10</div><div>11</div><div>12</div><div>13</div><div>14</div><div>15</div>
        <div>16</div><div>17</div><div>18</div><div>19</div><div>20</div><div>21</div><div>22</div>
        <div>23</div><div>24</div><div>25</div><div>26</div><div>27</div><div>28</div><div>29</div>
        <div>30</div><div>1</div><div>2</div><div>3</div><div>4</div><div>5</div><div>6</div>
      </div>
    </div>
    </nav>

  <div class="flex-grow-1 d-flex flex-column" id="main-content" style="height: 100vh;">
    <div class="calendar-header">
      <button id="toggleSidebarBtn" title="Toggle sidebar">&#9776;</button>
      <div>
        <button id="prevBtn">&lt;</button>
        <button id="nextBtn">&gt;</button>
      </div>
      <div class="month-year" id="monthYear"></div>
      <div>
        <button class="view-btn active" id="viewMonth">Month</button>
        <button class="view-btn" id="viewWeek">Week</button>
      </div>
      <button id="todayBtn">TODAY</button>
    </div>
    <div class="calendar-main">
      <div class="weekdays" id="weekdays"></div>
      <div class="dates-grid month" id="datesGrid"></div>
    </div>
  </div>
</div>

<div class="modal fade" id="eventDetailModal" tabindex="-1" aria-labelledby="eventDetailModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="eventDetailModalLabel">Events on DATE</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="modalEventList"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="addEventModal" tabindex="-1" aria-labelledby="addEventModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="addEventForm" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addEventModalLabel">Add New Event</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <label for="eventDateInput" class="form-label">Date</label>
        <input type="date" id="eventDateInput" class="form-control" required />
        <label for="eventTextInput" class="form-label mt-3">Event Title</label>
        <input type="text" id="eventTextInput" class="form-control" minlength="1" required />
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary btn-sm">Add Event</button>
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
      </div>
    </form>
  </div>
</div>

<div class="bottom-menu">
    <a href="{{ url('/index') }}" class="menu-item">🏠 <span>Dashboard</span></a>
    <a href="{{ url('/pomodoro') }}" class="menu-item">🍅 <span>Pomodoro</span></a>
    <a href="{{ url('/notes') }}" class="menu-item">🗒️ <span>Notes</span></a>
    <a href="{{ url('/calendar') }}" class="menu-item">📅 <span>Calendar</span></a>
    <a href="{{ url('/info') }}" class="menu-item">ℹ️ <span>Info</span></a>
  </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/calendar.js') }}"></script>

</body>
</html>