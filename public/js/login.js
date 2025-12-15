document.getElementById("loginForm").addEventListener("submit", function (e) {
  e.preventDefault();

  const username = document.getElementById("username").value.trim();
  const password = document.getElementById("password").value.trim();
  const errorMsg = document.getElementById("errorMsg");

  const users = JSON.parse(localStorage.getItem("users")) || [];

  const found = users.find(
    u => u.username === username && u.password === password
  );

  if (found) {
    localStorage.setItem("loggedInUsername", found.username);
    
    
    window.location.href = "/index";
} else {
    errorMsg.textContent = "❌ Username atau password salah!";
  }
});
