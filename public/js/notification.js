// Efek animasi & tombol mark all read
document.addEventListener("DOMContentLoaded", () => {
  const notifItems = document.querySelectorAll(".notif-item");
  const markAll = document.getElementById("markAllRead");

  notifItems.forEach((item, index) => {
    item.style.animationDelay = `${index * 0.1}s`;
  });

  markAll.addEventListener("click", () => {
    notifItems.forEach(item => {
      item.style.opacity = "0.5";
      item.style.background = "#e8f5e9";
    });
    markAll.textContent = "✔ All marked as read";
    markAll.disabled = true;
  });
});
