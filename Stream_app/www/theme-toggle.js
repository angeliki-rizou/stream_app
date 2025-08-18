function theme_toggle() {
    let element = document.body;
    let is_darkmode_enable = element.classList.toggle("theme-toggle");

    document.cookie = "darkmode_enable=" + is_darkmode_enable;
    localStorage.setItem("darkmode_enable", is_darkmode_enable);
}

window.addEventListener("load", function () {
    let darkmode_default = localStorage.getItem("darkmode_enable") === "true";
    if (darkmode_default) {
        document.body.classList.add("theme-toggle");
    }
});

