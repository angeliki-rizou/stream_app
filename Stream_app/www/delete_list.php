<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

if (!isset($_GET['id'])) {
    die("Λείπει το ID λίστας.");
}

$DB_HOST = getenv('DB_HOST') ?: 'db';
$DB_NAME = getenv('DB_NAME') ?: 'stream_app';
$DB_USER = getenv('DB_USER') ?: 'appuser';
$DB_PASS = getenv('DB_PASS') ?: 'apppass';

try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Σβήνουμε μόνο αν ανήκει στον χρήστη
    $stmt = $pdo->prepare("SELECT id FROM watchlists WHERE id = ? AND user_id = ?");
    $stmt->execute([$_GET['id'], $_SESSION['user_id']]);
    $list = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$list) {
        die("Δεν βρέθηκε η λίστα ή δεν έχετε δικαίωμα διαγραφής.");
    }

    // Σβήνουμε όλες τις συνδέσεις της λίστας με περιεχόμενο
    $stmt = $pdo->prepare("DELETE FROM list_contents WHERE watchlist_id = ?");
    $stmt->execute([$_GET['id']]);

    // Σβήνουμε τη λίστα
    $stmt = $pdo->prepare("DELETE FROM watchlists WHERE id = ?");
    $stmt->execute([$_GET['id']]);

} catch (PDOException $e) {
    die("Σφάλμα: " . $e->getMessage());
}

header("Location: my_lists.php");
exit;


