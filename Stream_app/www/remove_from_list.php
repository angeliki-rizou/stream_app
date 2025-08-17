<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

if (!isset($_GET['list_id']) || !isset($_GET['content_id'])) {
    die("Λείπουν στοιχεία.");
}

$DB_HOST = getenv('DB_HOST') ?: 'db';
$DB_NAME = getenv('DB_NAME') ?: 'stream_app';
$DB_USER = getenv('DB_USER') ?: 'appuser';
$DB_PASS = getenv('DB_PASS') ?: 'apppass';

try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Διαγραφή μόνο αν η λίστα ανήκει στον χρήστη
    $stmt = $pdo->prepare("DELETE wc 
                           FROM list_contents wc
                           JOIN watchlists w ON wc.list_id = w.id
                           WHERE wc.list_id = ? AND wc.content_id = ? AND w.user_id = ?");
    $stmt->execute([$_GET['list_id'], $_GET['content_id'], $_SESSION['user_id']]);

} catch (PDOException $e) {
    die("Σφάλμα: " . $e->getMessage());
}

// Επιστροφή στη λίστα
header("Location: view_list.php?id=" . $_GET['list_id']);
exit;
