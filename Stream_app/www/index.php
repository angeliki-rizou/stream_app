<?php
session_start();
?>
<!doctype html>
<html lang="el">
<head>
<meta charset="utf-8">
<title>Stream App - Κεντρικό Μενού</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f8f9fa;
        text-align: center;
        padding-top: 50px;
    }
    h1 {
        color: #333;
    }
    .menu {
        display: inline-block;
        text-align: left;
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }
    .menu a {
        display: block;
        padding: 10px;
        margin: 5px 0;
        background: #007BFF;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        text-align: center;
    }
    .menu a:hover {
        background: #0056b3;
    }
</style>
</head>
<body>

<h1>📺 Stream App</h1>
<p>Καλώς ήρθες!</p>

<div class="menu">
    <?php if (!isset($_SESSION['user_id'])): ?>
        <a href="login.html">🔑 Σύνδεση</a>
        <a href="register.html">📝 Εγγραφή</a>
    <?php else: ?>
        <a href="protected.php">🏠 Κεντρική Σελίδα Χρήστη</a>
        <a href="view_contents.php">📺 Δες το περιεχόμενο</a>
        <a href="youtube_search.php">🔍 Αναζήτηση στο YouTube</a>
        <a href="my_lists.php">📋 Οι λίστες μου</a>
        <a href="logout.php">🚪 Αποσύνδεση</a>
    <?php endif; ?>
</div>

</body>
</html>
