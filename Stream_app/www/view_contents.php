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

    $stmt = $pdo->query("SELECT id, title, description, category, created_at FROM contents ORDER BY created_at DESC");
    $contents = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Σφάλμα σύνδεσης στη βάση: " . $e->getMessage());
}
?>
<!doctype html>
<html lang="el">
<head>
<meta charset="utf-8">
<title>Προβολή Περιεχομένου</title>
</head>
<body>
<h1>Περιεχόμενο Stream App</h1>
<a href="protected.php">⬅ Επιστροφή</a>
<hr>

<?php if (empty($contents)): ?>
    <p>Δεν υπάρχει περιεχόμενο ακόμα.</p>
<?php else: ?>
    <ul>
        <?php foreach ($contents as $item): ?>
            <li style="margin-bottom: 15px;">
                <strong><?php echo htmlspecialchars($item['title']); ?></strong><br>
                <?php echo nl2br(htmlspecialchars($item['description'])); ?><br>
                <a href="watch.php?id=<?php echo $item['id']; ?>" 
           style="display:inline-block;margin-top:5px;padding:6px 10px;background:#28a745;color:white;text-decoration:none;border-radius:4px;">
           ▶ Δες το
        </a>
                <em>Κατηγορία:</em> <?php echo htmlspecialchars($item['category']); ?><br>
                <em>Ημερομηνία:</em> <?php echo $item['created_at']; ?><br>
                <hr>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

</body>
</html>
