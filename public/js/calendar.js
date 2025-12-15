// Data
let events = {};
let currentDate = new Date();
let currentView = 'month';

// DOM elements
const datesGrid = document.getElementById('datesGrid');
const monthYearLabel = document.getElementById('monthYear');
const sidebarMonthYear = document.getElementById('sidebarMonthYear');
const prevBtn = document.getElementById('prevBtn');
const nextBtn = document.getElementById('nextBtn');
const todayBtn = document.getElementById('todayBtn');
const viewMonthBtn = document.getElementById('viewMonth');
const viewWeekBtn = document.getElementById('viewWeek');
const weekdaysEl = document.getElementById('weekdays');
const btnCreateEvent = document.getElementById('btnCreateEvent');
const toggleSidebarBtn = document.getElementById('toggleSidebarBtn');
const sidebar = document.getElementById('sidebar');
const mainContent = document.getElementById('main-content');

const modalDetail = new bootstrap.Modal(document.getElementById('eventDetailModal'));
const modalDetailTitle = document.getElementById('eventDetailModalLabel');
const modalEventList = document.getElementById('modalEventList');

const modalAdd = new bootstrap.Modal(document.getElementById('addEventModal'));
const addEventForm = document.getElementById('addEventForm');
const eventDateInput = document.getElementById('eventDateInput');
const eventTextInput = document.getElementById('eventTextInput');

function isSameDate(d1, d2) {
  return d1.getDate() === d2.getDate()
    && d1.getMonth() === d2.getMonth()
    && d1.getFullYear() === d2.getFullYear();
}
function formatDate(date) {
  return date.toISOString().slice(0, 10);
}
function addDays(date, days) {
  const d = new Date(date);
  d.setDate(d.getDate() + days);
  return d;
}

function renderWeekdays() {
  const days = ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"];
  weekdaysEl.innerHTML = '';
  days.forEach(day => {
    const d = document.createElement('div');
    d.textContent = day;
    weekdaysEl.appendChild(d);
  });
}

function renderMonthView(date) {
  datesGrid.className = 'dates-grid month';
  renderWeekdays();

  const year = date.getFullYear();
  const month = date.getMonth();
  const firstDay = new Date(year, month, 1);
  const startDay = firstDay.getDay();
  const daysInMonth = new Date(year, month + 1, 0).getDate();

  monthYearLabel.textContent = date.toLocaleDateString('en-US', {month: 'long', year:'numeric'});
  sidebarMonthYear.textContent = monthYearLabel.textContent;

  datesGrid.innerHTML = '';
  const totalCells = 35; // 5 baris

  for(let i=0; i < totalCells; i++) {
    const cell = document.createElement('div');
    cell.className = 'date-cell';

    const dayNum = i - startDay + 1;
    if(i < startDay || dayNum > daysInMonth) {
      cell.classList.add('empty');
    } else {
      const cellDateStr = formatDate(new Date(year, month, dayNum));
      cell.dataset.date = cellDateStr;
      cell.innerHTML = `<div class="date-number">${dayNum}</div>`;
      if(isSameDate(new Date(year, month, dayNum), new Date())) cell.classList.add('today');

      cell.ondblclick = onDateDoubleClick;
      cell.onclick = onDateClick;
      enableDrop(cell);
    }
    datesGrid.appendChild(cell);
  }
  renderEvents();
}

function renderWeekView(date) {
  datesGrid.className = 'dates-grid week';
  renderWeekdays();

  const dayIdx = date.getDay();
  const sunday = addDays(date, -dayIdx);

  monthYearLabel.textContent = `Week of ${sunday.toLocaleDateString('en-US', {month:'short', day:'numeric', year: 'numeric'})}`;
  sidebarMonthYear.textContent = monthYearLabel.textContent;

  datesGrid.innerHTML = '';

  for(let i=0; i < 7; i++) {
    const dayDate = addDays(sunday, i);
    const cell = document.createElement('div');
    cell.className = 'date-cell';
    const cellDateStr = formatDate(dayDate);
    cell.dataset.date = cellDateStr;
    cell.innerHTML = `<div class="date-number">${dayDate.getDate()}</div>`;
    if(isSameDate(dayDate, new Date())) cell.classList.add('today');

    cell.ondblclick = onDateDoubleClick;
    cell.onclick = onDateClick;
    enableDrop(cell);
    datesGrid.appendChild(cell);
  }
  renderEvents();
}

function renderEvents() {
  const eventEls = datesGrid.querySelectorAll('.event');
  eventEls.forEach(el => el.remove());

  for(const dateKey in events) {
    const cell = datesGrid.querySelector(`[data-date="${dateKey}"]`);
    if(cell) {
      events[dateKey].forEach(ev => {
        const evEl = document.createElement('div');
        evEl.className = 'event';
        evEl.textContent = ev.text;
        evEl.draggable = true;
        evEl.dataset.eventId = ev.id;
        evEl.dataset.eventDate = dateKey;
        evEl.addEventListener('dragstart', dragStart);
        evEl.addEventListener('dragend', dragEnd);
        cell.appendChild(evEl);
      });
    }
  }
}

let draggedEvent = null;
function dragStart(e) {
  draggedEvent = this;
  e.dataTransfer.effectAllowed = 'move';
  setTimeout(() => this.classList.add('dragging'), 0);
}
function dragEnd(e) {
  if(draggedEvent) draggedEvent.classList.remove('dragging');
  draggedEvent = null;
}
function enableDrop(cell) {
  cell.ondragover = e => {
    if(draggedEvent) e.preventDefault();
  };
  cell.ondrop = e => {
    e.preventDefault();
    if(!draggedEvent) return;
    const oldDate = draggedEvent.dataset.eventDate;
    const eventId = draggedEvent.dataset.eventId;
    const newDate = cell.dataset.date;
    if(newDate && oldDate !== newDate) {
      moveEvent(eventId, oldDate, newDate);
      renderEvents();
    }
  };
}
function moveEvent(eventId, oldDate, newDate) {
  const oldEvents = events[oldDate];
  if(!oldEvents) return;
  const idx = oldEvents.findIndex(ev => ev.id === eventId);
  if(idx === -1) return;
  const evObj = oldEvents.splice(idx, 1)[0];
  evObj.date = newDate;
  if(!events[newDate]) events[newDate] = [];
  events[newDate].push(evObj);
  if(oldEvents.length === 0) delete events[oldDate];
}

function onDateDoubleClick(e) {
  e.stopPropagation();
  const dateStr = this.dataset.date;
  if(!dateStr) return;
  let text = prompt(`Create a new event on ${dateStr}:`);
  if(text && text.trim()) createEvent(dateStr, text.trim());
}

function onDateClick(e) {
  if(e.target.classList.contains('event')) return;
  const dateStr = this.dataset.date;
  if(!dateStr) return;
  showEventDetails(dateStr);
}

function createEvent(dateStr, text) {
  if(!events[dateStr]) events[dateStr] = [];
  const id = 'e' + Date.now() + Math.floor(Math.random()*1000);
  events[dateStr].push({id, text, date: dateStr});
  renderEvents();
}

function showEventDetails(dateStr) {
  modalDetailTitle.textContent = `Events on ${dateStr}`;
  modalEventList.innerHTML = '';

  if(!events[dateStr] || events[dateStr].length === 0) {
    modalEventList.innerHTML = '<p>No events on this day.</p>';
  } else {
    events[dateStr].forEach(ev => {
      const div = document.createElement('div');
      div.className = 'modal-event-item';

      const spanText = document.createElement('div');
      spanText.className = 'modal-event-text';
      spanText.textContent = ev.text;

      const btnDel = document.createElement('button');
      btnDel.className = 'delete-event-btn';
      btnDel.textContent = 'Delete';
      btnDel.onclick = (evt) => {
        evt.stopPropagation();
        if(confirm(`Delete event "${ev.text}"?`)) {
          deleteEvent(ev.id, dateStr);
          showEventDetails(dateStr); // Refresh modal content
          renderEvents(); // Refresh calendar view
        }
      };

      div.appendChild(spanText);
      div.appendChild(btnDel);
      modalEventList.appendChild(div);
    });
  }
  modalDetail.show();
}

function deleteEvent(eventId, dateStr) {
  const evArr = events[dateStr];
  if(!evArr) return;
  const index = evArr.findIndex(ev => ev.id === eventId);
  if(index !== -1) {
    evArr.splice(index, 1);
    if(evArr.length === 0) delete events[dateStr];
  }
}

btnCreateEvent.onclick = () => {
  eventDateInput.value = formatDate(currentDate);
  eventTextInput.value = '';
  modalAdd.show();
};

addEventForm.onsubmit = (e) => {
  e.preventDefault();
  const dateVal = eventDateInput.value;
  const textVal = eventTextInput.value.trim();
  if(dateVal && textVal) {
    createEvent(dateVal, textVal);
    modalAdd.hide();
    renderCurrentView();
  }
};

prevBtn.onclick = () => {
  if(currentView === 'month') currentDate.setMonth(currentDate.getMonth() - 1);
  else currentDate.setDate(currentDate.getDate() - 7);
  renderCurrentView();
};
nextBtn.onclick = () => {
  if(currentView === 'month') currentDate.setMonth(currentDate.getMonth() + 1);
  else currentDate.setDate(currentDate.getDate() + 7);
  renderCurrentView();
};
todayBtn.onclick = () => {
  currentDate = new Date();
  renderCurrentView();
};

viewMonthBtn.onclick = () => {
  currentView = 'month';
  viewMonthBtn.classList.add('active');
  viewWeekBtn.classList.remove('active');
  renderCurrentView();
};
viewWeekBtn.onclick = () => {
  currentView = 'week';
  viewWeekBtn.classList.add('active');
  viewMonthBtn.classList.remove('active');
  renderCurrentView();
};

toggleSidebarBtn.onclick = () => {
  if(sidebar.classList.contains('hidden')) {
    sidebar.classList.remove('hidden');
  } else {
    sidebar.classList.add('hidden');
  }
};

function renderCurrentView() {
  if(currentView === 'month') renderMonthView(currentDate);
  else renderWeekView(currentDate);
}

// Initial render
renderCurrentView();