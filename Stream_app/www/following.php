<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

$DB_HOST = getenv('DB_HOST') ?: 'db';
$DB_NAME = getenv('DB_NAME') ?: 'stream_app';
$DB_USER = getenv('DB_USER') ?: 'appuser';
$DB_PASS = getenv('DB_PASS') ?: 'apppass';

try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("
        SELECT u.id, u.username, u.first_name, u.last_name 
        FROM followers f
        JOIN users u ON f.following_id = u.id
        WHERE f.follower_id = ?
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Σφάλμα: " . $e->getMessage());
}
?>
<!doctype html>
<html lang="el">
<head>
<meta charset="utf-8">
<title>Ποιους ακολουθώ</title>
</head>
<body>
<h1>👥 Ποιους ακολουθώ</h1>
<a href="protected.php">⬅ Επιστροφή στο μενού</a>
<hr>

<?php if ($users): ?>
    <ul>
    <?php foreach ($users as $user): ?>
        <li>
            <?php echo htmlspecialchars($user['username']); ?> (<?php echo htmlspecialchars($user['first_name'] . " " . $user['last_name']); ?>)
            <a href="unfollow.php?id=<?php echo $user['id']; ?>" style="color:red;">❌ Διαγραφή</a>
        </li>
    <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>Δεν ακολουθείς κανέναν.</p>
<?php endif; ?>

</body>
</html>
