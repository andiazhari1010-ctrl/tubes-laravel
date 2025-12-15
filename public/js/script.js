// ======================================================
// SCRIPT GLOBAL (Dashboard & Navigasi)
// ======================================================

// 1. NAVIGASI MENU BAWAH
document.querySelectorAll(".menu-item").forEach((item) => {
  item.addEventListener("click", (e) => {
    e.preventDefault();
    // Mengambil href dari blade (yang sudah format {{ url(...) }})
    window.location.href = item.getAttribute("href");
  });
});

// 2. TOMBOL ADD FRIEND (Header)
const addFriendBtn = document.getElementById("addFriendBtn");
if (addFriendBtn) {
  addFriendBtn.addEventListener("click", (e) => {
    e.preventDefault();
    window.location.href = "/add-friend"; // Ubah ke route Laravel
  });
}

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
  const logoutIcon = document.getElementById("logoutBtn"); // Sesuai ID di Blade
  const popup = document.getElementById("logoutPopup");
  const logoutYes = document.getElementById("logoutYes");
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

    // Yes Logout
    logoutYes.addEventListener("click", () => {
      // Hapus data dummy localStorage (opsional)
      localStorage.removeItem("loggedInUsername");

      // Redirect ke Route Logout Laravel
      window.location.href = "/logout"; 
    });
  }

  // ---------------------------------------------------
  // B. JAM DIGITAL
  // ---------------------------------------------------
  const clockElement = document.getElementById("clock");
  if (clockElement) {
    function updateClock() {
      const now = new Date();
      const hours = String(now.getHours()).padStart(2, "0");
      const minutes = String(now.getMinutes()).padStart(2, "0");
      clockElement.textContent = `${hours} : ${minutes}`;
    }
    updateClock();
    setInterval(updateClock, 1000);
  }

  // ---------------------------------------------------
  // C. KALENDER MINI WIDGET
  // ---------------------------------------------------
  const calendarElement = document.getElementById("calendar");
  if (calendarElement) {
    function renderCalendar() {
      const now = new Date();
      const days = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
      const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
      const dateStr = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;
      calendarElement.innerHTML = `<strong>${dateStr}</strong>`;
    }
    renderCalendar();
  }

  

  // ---------------------------------------------------
  // D. SEARCH BAR FEATURE (Routing Fixed)
  // ---------------------------------------------------
  const searchBar = document.querySelector(".search-bar");
  const searchHistoryList = document.getElementById("searchHistory");

  if (searchBar) {
    // Render History saat load
    let history = JSON.parse(localStorage.getItem("searchHistory")) || [];
    
    // Fungsi Render History ke UI
    const renderHistory = () => {
      if(!searchHistoryList) return;
      searchHistoryList.innerHTML = "";
      history.forEach(item => {
        const li = document.createElement("li");
        li.textContent = item;
        // Klik history item -> langsung search/isi value
        li.addEventListener("click", () => {
          searchBar.value = item;
          // Trigger enter manual atau biarkan user tekan enter
        });
        searchHistoryList.appendChild(li);
      });
    };
    renderHistory();

    // Event Listener Enter Key
    searchBar.addEventListener("keydown", (e) => {
      if (e.key === "Enter") {
        const query = searchBar.value.toLowerCase().trim();
        if (!query) return;

        // Simpan ke localStorage
        // Hapus jika duplikat biar rapi
        history = history.filter(item => item !== query); 
        history.unshift(query);
        history = history.slice(0, 5); // Max 5 item
        localStorage.setItem("searchHistory", JSON.stringify(history));
        renderHistory();

        // LOGIKA ROUTING PENCARIAN (Updated to Laravel Routes)
        if (query.includes("pomo") || query.includes("waktu") || query.includes("timer")) {
          window.location.href = "/pomodoro"; // Route Laravel
        } 
        else if (query.includes("note") || query.includes("catat") || query.includes("tulisan")) {
          window.location.href = "/notes"; // Route Laravel
        } 
        else if (query.includes("cale") || query.includes("tanggal") || query.includes("event")) {
          window.location.href = "/calendar"; // Route Laravel
        } 
        else if (query.includes("friend") || query.includes("teman")) {
            window.location.href = "/add-friend"; // Route Laravel
        }
        else {
          alert("No matching feature found for: " + query);
        }
      }
    });
  }

  // ---------------------------------------------------
  // E. LOGIC HALAMAN NOTES (Opsional di Script Global)
  // ---------------------------------------------------
  // Logic ini sebenarnya sudah ada di notes.js, tapi 
  // saya biarkan di sini dengan pengecekan elemen agar tidak error di dashboard.
  
  const noteContent = document.getElementById("noteContent");
  const saveNoteBtn = document.getElementById("saveNoteBtn");
  
  if (noteContent && saveNoteBtn) {
    // Logic notes hanya jalan jika elemennya ada (artinya sedang di halaman Notes)
    // ... (Kode logic notes diserahkan ke notes.js agar tidak duplikat) ...
    console.log("Notes page detected."); 
  }

});