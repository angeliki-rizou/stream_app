let element = document.body;
let is_darkmode_enable = element.classList.toggle("dark-mode");
document.cookie = "darkmode_enable=" + is_darkmode_enable;
localStorage.setItem("darkmode_enable", is_darkmode_enable);
