// ======================================================
// SCRIPT GLOBAL (Dashboard & Navigasi)
// ======================================================

// 1. NAVIGASI MENU BAWAH
document.querySelectorAll(".menu-item").forEach((item) => {
  item.addEventListener("click", (e) => {
    // Biarkan default behavior <a> jalan, kecuali mau animasi transisi khusus
    // e.preventDefault(); 
    // window.location.href = item.getAttribute("href");
  });
});

// 2. TOMBOL ADD FRIEND (Header) & ICON LAINNYA
// Pastikan tombol header mengarah ke link href-nya masing-masing
const iconLinks = document.querySelectorAll(".icon-box");
iconLinks.forEach(icon => {
    icon.addEventListener("click", (e) => {
        const href = icon.getAttribute("href");
        if(href && href !== "#") {
            window.location.href = href;
        }
    });
});

// 3. INTERAKSI AVATAR (Animasi)
const avatarBox = document.querySelector(".avatar-box");
if (avatarBox) {
  avatarBox.addEventListener("mouseenter", () => {
    avatarBox.style.transition = "transform 0.1s";
    avatarBox.style.transform = "scale(1.05)";
  });
  avatarBox.addEventListener("mouseleave", () => {
    avatarBox.style.transform = "scale(1)";
  });
  avatarBox.addEventListener("click", () => {
    avatarBox.classList.toggle("rotate");
  });
}

// Inject CSS untuk rotasi avatar
const style = document.createElement("style");
style.innerHTML = `
.rotate { animation: spin 5s linear infinite; }
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
`;
document.head.appendChild(style);


// ======================================================
// MAIN LOGIC ( dijalankan saat DOM siap )
// ======================================================
document.addEventListener("DOMContentLoaded", () => {

  // ---------------------------------------------------
  // A. FITUR LOGOUT (POPUP)
  // ---------------------------------------------------
  const logoutIcon = document.getElementById("logoutBtn"); 
  const popup = document.getElementById("logoutPopup");
  const logoutYes = document.getElementById("logoutYes"); // Ini skrg <a> href, jadi aman
  const logoutCancel = document.getElementById("logoutCancel");

  if (logoutIcon && popup) {
    // Buka Popup
    logoutIcon.addEventListener("click", () => {
      popup.classList.remove("hidden");
    });

    // Cancel
    logoutCancel.addEventListener("click", () => {
      popup.classList.add("hidden");
    });
    
    // Klik area gelap untuk tutup
    popup.addEventListener("click", (e) => {
        if(e.target === popup) popup.classList.add("hidden");
    });
  }

  // ---------------------------------------------------
  // B. JAM DIGITAL
  // ---------------------------------------------------
  const clockElement = document.getElementById("clock");
  if (clockElement) {
    function updateClock() {
      const now = new Date();
      // Format HH:MM:SS
      const timeString = now.toLocaleTimeString('en-US', { hour12: false });
      clockElement.textContent = timeString;
    }
    updateClock();
    setInterval(updateClock, 1000);
  }

  // ---------------------------------------------------
  // C. KALENDER WIDGET (LOGIKA BARU - GRID SYSTEM)
  // ---------------------------------------------------
  const calendarEl = document.getElementById("calendar");
  if (calendarEl) {
    const date = new Date();
    const currentMonth = date.getMonth();
    const currentYear = date.getFullYear();
    
    // Nama Bulan Kapital (Pixel Style)
    const monthNames = ["JANUARY", "FEBRUARY", "MARCH", "APRIL", "MAY", "JUNE", "JULY", "AUGUST", "SEPTEMBER", "OCTOBER", "NOVEMBER", "DECEMBER"];
    
    // 1. Render Header & Grid Container
    calendarEl.innerHTML = `
        <div class="calendar-header">
            <span>${monthNames[currentMonth]} ${currentYear}</span>
        </div>
        <div class="calendar-grid" id="calendarGrid">
            <div class="calendar-day-name">S</div>
            <div class="calendar-day-name">M</div>
            <div class="calendar-day-name">T</div>
            <div class="calendar-day-name">W</div>
            <div class="calendar-day-name">T</div>
            <div class="calendar-day-name">F</div>
            <div class="calendar-day-name">S</div>
        </div>
    `;

    const grid = document.getElementById('calendarGrid');
    
    // 2. Hitung hari pertama bulan ini (0-6)
    const firstDay = new Date(currentYear, currentMonth, 1).getDay();
    // 3. Hitung total hari bulan ini (28/30/31)
    const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
    
    // 4. Render Kotak Kosong (Padding Awal)
    for (let i = 0; i < firstDay; i++) {
        const emptyCell = document.createElement('div');
        emptyCell.classList.add('calendar-day', 'empty');
        grid.appendChild(emptyCell);
    }

    // 5. Render Tanggal 1 - Akhir
    const todayDate = date.getDate();
    for (let i = 1; i <= daysInMonth; i++) {
        const dayCell = document.createElement('div');
        dayCell.classList.add('calendar-day');
        dayCell.textContent = i;
        
        // Highlight Hari Ini
        if (i === todayDate) {
            dayCell.classList.add('today');
        }
        grid.appendChild(dayCell);
    }
  }

  // ---------------------------------------------------
  // D. SEARCH BAR FEATURE (LOGIKA BARU - API BACKEND)
  // ---------------------------------------------------
  const searchInput = document.getElementById("searchInput");
  const searchHistoryList = document.getElementById("searchHistory");
  let searchTimeout = null;

  if (searchInput && searchHistoryList) {
    
    // Event listener saat mengetik
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        // Clear timeout lama (Debounce) biar server gak berat
        clearTimeout(searchTimeout);

        // Jika input kosong atau < 2 huruf, sembunyikan dropdown
        if (query.length < 2) {
            searchHistoryList.style.display = 'none';
            searchHistoryList.innerHTML = '';
            return;
        }

        // Tunggu 500ms setelah berhenti mengetik baru request
        searchTimeout = setTimeout(async () => {
            try {
                // Fetch ke API Laravel
                const res = await fetch(`/api/search?q=${query}`);
                
                if (res.ok) {
                    const users = await res.json();
                    renderSearchResults(users);
                }
            } catch (error) {
                console.error("Search error:", error);
            }
        }, 500);
    });

    // Sembunyikan dropdown kalau klik di luar area search
    document.addEventListener('click', (e) => {
        if (!searchInput.contains(e.target) && !searchHistoryList.contains(e.target)) {
            searchHistoryList.style.display = 'none';
        }
    });

    // Fungsi Render Hasil ke Dropdown
    function renderSearchResults(users) {
        searchHistoryList.innerHTML = '';
        
        if (users.length === 0) {
            const li = document.createElement('li');
            li.style.padding = '10px';
            li.style.color = '#999';
            li.style.fontSize = '12px';
            li.textContent = 'No user found.';
            searchHistoryList.appendChild(li);
        } else {
            users.forEach(user => {
                const initial = user.name.charAt(0).toUpperCase();
                
                // Buat Item List sebagai Link
                const a = document.createElement('a');
                a.href = '#'; // Nanti arahkan ke profile/add friend
                a.classList.add('search-item');
                // Style inline untuk layout baris
                a.style.display = 'flex'; 
                a.style.alignItems = 'center';
                a.style.padding = '10px';
                a.style.textDecoration = 'none';
                a.style.color = '#333';
                a.style.borderBottom = '1px solid #eee';
                
                a.innerHTML = `
                    <div style="width:30px; height:30px; background:${getRandomColor()}; border-radius:50%; margin-right:10px; display:flex; justify-content:center; align-items:center; color:white; font-weight:bold; font-size:12px;">
                        ${initial}
                    </div>
                    <span>${user.name}</span>
                `;
                
                // Klik user -> bisa diarahkan ke Add Friend nanti
                a.addEventListener('click', (e) => {
                    e.preventDefault();
                    alert(`Selected user: ${user.name} (ID: ${user.id}) \nFitur Add Friend akan segera hadir!`);
                    searchInput.value = user.name;
                    searchHistoryList.style.display = 'none';
                });

                const li = document.createElement('li');
                li.style.listStyle = 'none'; // Pastikan list style hilang
                li.appendChild(a);
                searchHistoryList.appendChild(li);
            });
        }
        searchHistoryList.style.display = 'block';
    }

    // Helper warna acak untuk avatar
    function getRandomColor() {
        const colors = ['#ef5350', '#66bb6a', '#42a5f5', '#ffca28', '#ab47bc', '#8d6e63'];
        return colors[Math.floor(Math.random() * colors.length)];
    }
  }

  // ---------------------------------------------------
  // E. LOGIC HALAMAN NOTES (Opsional)
  // ---------------------------------------------------
  const noteContent = document.getElementById("noteContent");
  if (noteContent) {
    console.log("Notes page active."); 
  }

});