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

    $results = [];
    if (!empty($_GET['q'])) {
        $q = "%" . $_GET['q'] . "%";
        $stmt = $pdo->prepare("SELECT id, username, first_name, last_name FROM users WHERE username LIKE ? AND id != ?");
        $stmt->execute([$q, $_SESSION['user_id']]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (PDOException $e) {
    die("Σφάλμα: " . $e->getMessage());
}
?>
<!doctype html>
<html lang="el">
<head>
<meta charset="utf-8">
<title>Αναζήτηση χρηστών</title>
</head>
<body>
<h1>🔍 Αναζήτηση χρηστών</h1>
<a href="protected.php">⬅ Επιστροφή στο μενού</a>
<hr>

<form method="get">
    <input type="text" name="q" placeholder="Username" required>
    <button type="submit">Αναζήτηση</button>
</form>

<?php if ($results): ?>
    <ul>
    <?php foreach ($results as $user): ?>
        <li>
            <?php echo htmlspecialchars($user['username']); ?> (<?php echo htmlspecialchars($user['first_name'] . " " . $user['last_name']); ?>)
            <a href="follow.php?id=<?php echo $user['id']; ?>">➕ Ακολούθησε</a>
        </li>
    <?php endforeach; ?>
    </ul>
<?php elseif (!empty($_GET['q'])): ?>
    <p>Δεν βρέθηκαν χρήστες.</p>
<?php endif; ?>

</body>
</html>
