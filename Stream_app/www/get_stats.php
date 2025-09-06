<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    exit;
}

$DB_HOST = getenv('DB_HOST') ?: 'db';
$DB_NAME = getenv('DB_NAME') ?: 'stream_app';
$DB_USER = getenv('DB_USER') ?: 'appuser';
$DB_PASS = getenv('DB_PASS') ?: 'apppass';

try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Αριθμός λιστών χρήστη
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM watchlists WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $list_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Αριθμός περιεχομένου χρήστη
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM contents WHERE created_by = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $content_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Αριθμός ακολούθων
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM followers WHERE following_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $follower_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

} catch (PDOException $e) {
    // Αν υπάρξει σφάλμα, θέτουμε τις τιμές στα 0
    $list_count = $content_count = $follower_count = 0;
}
?>
<div class="w3-third">
    <div class="stats-card">
        <div class="feature-icon">📋</div>
        <div class="stats-number"><?php echo $list_count; ?></div>
        <div class="stats-label">Λίστες</div>
    </div>
</div>
<div class="w3-third">
    <div class="stats-card">
        <div class="feature-icon">📺</div>
        <div class="stats-number"><?php echo $content_count; ?></div>
        <div class="stats-label">Περιεχόμενα</div>
    </div>
</div>
<div class="w3-third">
    <div class="stats-card">
        <div class="feature-icon">👥</div>
        <div class="stats-number"><?php echo $follower_count; ?></div>
        <div class="stats-label">Ακόλουθοι</div>
    </div>
</div>