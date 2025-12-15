// Efek interaktif tombol "Add Friend" & Update List
document.addEventListener("DOMContentLoaded", () => {
  const statusEl = document.getElementById("afStatus");
  const input = document.getElementById("friendNameInput");
  const addBtn = document.getElementById("uiAddFriendBtn");
  const friendList = document.getElementById("currentFriendList");

  addBtn.addEventListener("click", () => {
    const name = input.value.trim();
    
    // Validasi input kosong
    if (name === "") {
      statusEl.textContent = "⚠️ Please enter a friend's name.";
      statusEl.style.color = "#ffeb3b"; // Warna kuning peringatan
      return;
    }

    // Simulasi proses penambahan teman sukses
    statusEl.textContent = `✅ Success! ${name} is now your friend.`;
    statusEl.style.color = "#b2ff59";

    // TAMBAHKAN KE LIST DI BAWAH (DOM Manipulation)
    const newLi = document.createElement("li");
    newLi.innerHTML = `
      <div>
        <strong>${name}</strong><br><small>Status: Friends</small>
      </div>
      <span style="font-size: 20px;">🤝</span>
    `;
    
    // Efek animasi masuk simpel
    newLi.style.animation = "fadeIn 0.5s ease";
    friendList.prepend(newLi); // Tambah ke paling atas list

    // Reset Input
    input.value = "";
    
    // Hilangkan status text setelah 3 detik
    setTimeout(() => (statusEl.textContent = ""), 3000);
  });
});