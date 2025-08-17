<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    die("Πρέπει να είστε συνδεδεμένος στο Stream App για να προσθέσετε περιεχόμενο.");
}

$title = $_GET['title'] ?? '';
$desc = $_GET['desc'] ?? '';
$video_id = $_GET['id'] ?? '';

if (!$title || !$video_id) {
    die("Λείπουν δεδομένα.");
}

$full_desc = $desc . "\nYouTube Link: https://www.youtube.com/watch?v=" . $video_id;

$stmt = $pdo->prepare('INSERT INTO contents (title, description, category, created_by) VALUES (?, ?, ?, ?)');
$stmt->execute([$title, $full_desc, 'other', $_SESSION['user_id']]);

echo "✅ Το βίντεο προστέθηκε με επιτυχία! <a href='view_contents.php'>Δες το περιεχόμενο</a>";
