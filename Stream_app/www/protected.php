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
<meta charset="utf-8">
<title>Κεντρικό Μενού</title>
</head>
<body>
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

