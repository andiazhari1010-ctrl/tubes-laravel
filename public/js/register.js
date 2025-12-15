document.getElementById("registerForm").addEventListener("submit", function (e) {
  e.preventDefault();

  const regUser = document.getElementById("regUsername").value.trim();
  const regPass = document.getElementById("regPassword").value.trim();
  const regMsg = document.getElementById("regMsg");

  if (!regUser || !regPass) {
    regMsg.textContent = "❌ Username dan password wajib diisi!";
    return;
  }

  let users = JSON.parse(localStorage.getItem("users")) || [];

  const exist = users.find(u => u.username === regUser);
  if (exist) {
    regMsg.textContent = "❌ Username sudah digunakan!";
    return;
  }

  users.push({ username: regUser, password: regPass });
  localStorage.setItem("users", JSON.stringify(users));

  regMsg.textContent = "✅ Akun berhasil dibuat! Silakan login.";
});

