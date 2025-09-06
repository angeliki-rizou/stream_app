<!DOCTYPE html>
<html lang="el">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo isset($page_title) ? $page_title : 'StreamApp'; ?></title>
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
body,h1,h2,h3,h4,h5,h6 {font-family: "Raleway", sans-serif}
body.dark-mode {background-color: #121212; color: #f1f1f1;}
.w3-bar .w3-button {padding:16px}

/* Νέα CSS για το footer */
html, body {
    height: 100%;
    margin: 0;
}
body {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}
.main-content {
    flex: 1;
}
footer {
    margin-top: auto;
}
</style>
</head>
<body class="<?php echo isset($_COOKIE['darkmode_enable']) && $_COOKIE['darkmode_enable']=='true' ? 'dark-mode' : ''; ?>">

<!-- Navbar -->
<div class="w3-top">
  <div class="w3-bar w3-black w3-card" id="myNavbar">
    <a href="protected.php" class="w3-bar-item w3-button w3-wide">StreamApp</a>
    <div class="w3-right w3-hide-small">
      <a href="youtube_search.php" class="w3-bar-item w3-button"><i class="fa fa-search"></i> Αναζήτηση</a>
      <a href="my_lists.php" class="w3-bar-item w3-button"><i class="fa fa-list"></i> Οι Λίστες μου</a>
      <a href="followers.php" class="w3-bar-item w3-button"><i class="fa fa-users"></i> Ακόλουθοι</a>
      <a href="profile.php" class="w3-bar-item w3-button"><i class="fa fa-user"></i> Προφίλ</a>
      <a href="logout.php" class="w3-bar-item w3-button"><i class="fa fa-sign-out"></i> Έξοδος</a>
      <button onclick="theme_toggle()" class="w3-bar-item w3-button" title="Εναλλαγή θέματος">
        <i class="fa fa-moon-o"></i>
      </button>
    </div>
  </div>
</div>

<!-- Main Content -->
<div style="margin-top:80px" class="w3-container main-content">
<?php
if (isset($content) && !empty($content)) {
    echo $content;
} else if (isset($content_file) && file_exists($content_file)) {
    include($content_file);
} else {
    echo "<p class='w3-center w3-text-red'>Δεν βρέθηκε περιεχόμενο.</p>";
}
?>
</div>

<!-- Footer -->
<footer class="w3-center w3-black w3-padding-16">
  <p>&copy; <?php echo date("Y"); ?> StreamApp</p>
</footer>

<script>
function theme_toggle() {
    var element = document.body;
    var isDarkMode = element.classList.toggle("dark-mode");
    document.cookie = "darkmode_enable=" + isDarkMode + "; path=/";
    localStorage.setItem("darkmode_enable", isDarkMode);
}
window.onload = function() {
    let darkmode_default = localStorage.getItem("darkmode_enable") === "true";
    document.body.classList.toggle("dark-mode", darkmode_default);
}
</script>

</body>
</html>

