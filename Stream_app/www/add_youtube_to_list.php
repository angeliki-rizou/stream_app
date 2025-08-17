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

    $title = $_POST['title'] ?? null;
    $description = $_POST['desc'] ?? '';
    $list_id = $_POST['list_id'] ?? null;
    $youtube_id = $_POST['youtube_id'] ?? null;

    if (!$title || !$list_id || !$youtube_id) {
        die("Λείπουν δεδομένα.");
    }

    // Check if list belongs to user
    $check = $pdo->prepare("SELECT id FROM watchlists WHERE id = ? AND user_id = ?");
    $check->execute([$list_id, $_SESSION['user_id']]);
    if (!$check->fetch()) {
        die("Δεν έχετε πρόσβαση σε αυτή τη λίστα.");
    }

    // Insert into contents with YouTube ID
    $insert_content = $pdo->prepare("INSERT INTO contents (title, description, created_by, youtube_id) VALUES (?, ?, ?, ?)");
    $insert_content->execute([$title, $description, $_SESSION['user_id'], $youtube_id]);
    $content_id = $pdo->lastInsertId();

    // Link content to list
    $insert_list = $pdo->prepare("INSERT INTO list_contents (list_id, content_id) VALUES (?, ?)");
    $insert_list->execute([$list_id, $content_id]);

    echo "✅ Το βίντεο προστέθηκε στη λίστα! <a href='view_list.php?id=$list_id'>Δες τη λίστα</a> | <a href='youtube_search.php'>🔍 Νέα αναζήτηση</a>";

} catch (PDOException $e) {
    die("Σφάλμα: " . $e->getMessage());
}


