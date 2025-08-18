function theme_toggle() {
    var element = document.body;
    element.classList.toggle("theme-toggle");

let element = document.body;
let is_darkmode_enable = element.classList.toggle("theme-toggle");
document.cookie = "darkmode_enable=" + is_darkmode_enable;
localStorage.setItem("darkmode_enable", is_darkmode_enable);

let darkmode_default = localStorage.getItem("darkmode_enable") || false;
let element = document.body;
element.classList.toggle("theme-toggle", darkmode_default);

}
