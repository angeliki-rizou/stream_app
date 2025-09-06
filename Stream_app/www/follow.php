<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

if (!isset($_GET['id'])) {
    die("Λείπει το ID χρήστη.");
}

$DB_HOST = getenv('DB_HOST') ?: 'db';
$DB_NAME = getenv('DB_NAME') ?: 'stream_app';
$DB_USER = getenv('DB_USER') ?: 'appuser';
$DB_PASS = getenv('DB_PASS') ?: 'apppass';

try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("INSERT IGNORE INTO followers (follower_id, following_id) VALUES (?, ?)");
    $stmt->execute([$_SESSION['user_id'], $_GET['id']]);

} catch (PDOException $e) {
    die("Σφάλμα: " . $e->getMessage());
}

header("Location: search_users.php?q=");
exit;
