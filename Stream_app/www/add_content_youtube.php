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

// τίτλος σελίδας
$page_title = "Προσθήκη περιεχομένου ";

ob_start();
?>
<div class="w3-container">
    <div class="w3-panel w3-green">
        <h3>✅ Επιτυχία!</h3>
        <p>Το βίντεο προστέθηκε με επιτυχία!</p>
    </div>
    <p>
        <a href='view_contents.php' class="w3-button w3-blue">Δες το περιεχόμενο</a>
        <a href='youtube_search.php' class="w3-button w3-green">Προσθήκη άλλου βίντεο</a>
    </p>
</div>
<?php
$content = ob_get_clean();

// layout
include("layout.php");
?>
