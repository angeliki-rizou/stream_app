<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}
?>
<!doctype html>
<html lang="el">
<head>
    <style>
  body {
    font-family: Arial, sans-serif;
    background: #fff;
    color: #000;
    transition: background 0.3s, color 0.3s;
  }
  .theme-toggle {
    background: #121212;
    color: #eee;
  }
  .theme-toggle a {
    color: #80cbc4;
  }
  button {
    margin: 5px 0;
    padding: 6px 12px;
    cursor: pointer;
  }
</style>

<meta charset="utf-8">
<title>Κεντρικό Μενού</title>
<script src="theme_toggle.js"></script>
</head>
<body>

<button onclick="theme_toggle()">Toggle</button>
   

<h1>👋 Καλώς ήρθες, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
<hr>

<h2>📺 Περιεχόμενο</h2>
<ul>
    <li><a href="youtube_search.php">🔍 Αναζήτηση YouTube</a></li>
    <li><a href="view_contents.php">📂 Δες όλα τα περιεχόμενα</a></li>
    <li><a href="my_lists.php">📑 Οι λίστες μου</a></li>
    <li><a href="search_lists.php">🔎 Αναζήτηση σε λίστες</a></li>
</ul>

<h2>👥 Χρήστες</h2>
<ul>
    <li><a href="search_users.php">🔎 Βρες χρήστες</a></li>
    <li><a href="following.php">➡ Ποιους ακολουθώ</a></li>
    <li><a href="followers.php">⬅ Οι ακόλουθοί μου</a></li>
    <li><a href="profile.php">⚙ Προφίλ</a></li>
</ul>

<h2>📤 Open Data</h2>
<ul>
    <li><a href="export_yaml.php">📂 Εξαγωγή όλων των λιστών & περιεχομένων (YAML)</a></li>
</ul>

<hr>
<a href="logout.php">🚪 Αποσύνδεση</a>

</body>
</html>



