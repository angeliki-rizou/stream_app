<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

$DB_HOST = getenv('DB_HOST') ?: 'db';
$DB_NAME = getenv('DB_NAME') ?: 'stream_app';
$DB_USER = getenv('DB_USER') ?: 'appuser';
$DB_PASS = getenv('DB_PASS') ?: 'apppass';

try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $list_id = $_GET['list_id'] ?? null;
    $content_id = $_GET['content_id'] ?? null;

    if (!$list_id || !$content_id) {
        die("Λείπουν δεδομένα.");
    }

    // Έλεγχος αν η λίστα ανήκει στον χρήστη
    $check = $pdo->prepare("SELECT id FROM watchlists WHERE id = ? AND user_id = ?");
    $check->execute([$list_id, $_SESSION['user_id']]);
    if (!$check->fetch()) {
        die("Δεν έχετε πρόσβαση σε αυτή τη λίστα.");
    }

    // Διαγραφή σύνδεσης από list_contents
    $stmt = $pdo->prepare("DELETE FROM list_contents WHERE list_id = ? AND content_id = ?");
    $stmt->execute([$list_id, $content_id]);

    header("Location: view_list.php?id=" . $list_id);
    exit;

} catch (PDOException $e) {
    die("Σφάλμα: " . $e->getMessage());
}
