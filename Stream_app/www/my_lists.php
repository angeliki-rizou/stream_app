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

    $stmt = $pdo->prepare("SELECT * FROM watchlists WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $lists = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Σφάλμα: " . $e->getMessage());
}
?>
<!doctype html>
<html lang="el">
<head>
<meta charset="utf-8">
<title>Οι λίστες μου</title>
</head>
<body>
<h1>Οι λίστες μου</h1>
<a href="protected.php">⬅ Επιστροφή στο μενού</a>
<hr>

<?php if ($lists): ?>
    <ul>
    <?php foreach ($lists as $list): ?>
        <li>
            <a href="view_list.php?id=<?php echo $list['id']; ?>" style="font-weight:bold; text-decoration:none;">
                <?php echo htmlspecialchars($list['name']); ?>
            </a>
            <a href="edit_list.php?id=<?php echo $list['id']; ?>">✏ Επεξεργασία</a>
            <a href="delete_list.php?id=<?php echo $list['id']; ?>" style="color:red;" onclick="return confirm('Σίγουρα θέλεις να διαγράψεις αυτή τη λίστα;');">🗑 Διαγραφή</a>
        </li>
    <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>Δεν έχεις δημιουργήσει καμία λίστα.</p>
<?php endif; ?>

<hr>
<h2>Δημιουργία νέας λίστας</h2>
<form method="post" action="create_list.php">
    <input type="text" name="name" placeholder="Όνομα λίστας" required>
    <button type="submit">➕ Δημιουργία</button>
</form>

</body>
</html>

