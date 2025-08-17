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

    $content_id = $_GET['id'] ?? null;
    if (!$content_id) {
        die("Λείπει το ID του περιεχομένου.");
    }

    $stmt = $pdo->prepare("SELECT * FROM contents WHERE id = ?");
    $stmt->execute([$content_id]);
    $content = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$content) {
        die("Το περιεχόμενο δεν βρέθηκε.");
    }

} catch (PDOException $e) {
    die("Σφάλμα: " . $e->getMessage());
}

?>
<!doctype html>
<html lang="el">
<head>
<meta charset="utf-8">
<title><?php echo htmlspecialchars($content['title']); ?></title>
</head>
<body>
<h1><?php echo htmlspecialchars($content['title']); ?></h1>
<p><?php echo nl2br(htmlspecialchars($content['description'])); ?></p>

<?php if (!empty($content['youtube_id'])): ?>
    <iframe width="560" height="315"
            src="https://www.youtube.com/embed/<?php echo htmlspecialchars($content['youtube_id']); ?>"
            frameborder="0"
            allowfullscreen>
    </iframe>
<?php else: ?>
    <p>Δεν υπάρχει YouTube ID για αυτό το περιεχόμενο.</p>
<?php endif; ?>

<hr>
<a href="view_contents.php">⬅ Επιστροφή</a>
</body>
</html>


