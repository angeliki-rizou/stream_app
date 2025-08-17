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

    if (!isset($_GET['id'])) {
        die("Λείπει το ID λίστας.");
    }

    $list_id = $_GET['id'];

    // Παίρνουμε τα στοιχεία της λίστας
    $stmt = $pdo->prepare("SELECT * FROM watchlists WHERE id = ? AND user_id = ?");
    $stmt->execute([$list_id, $_SESSION['user_id']]);
    $list = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$list) {
        die("Δεν βρέθηκε η λίστα ή δεν έχετε πρόσβαση.");
    }

    // Παίρνουμε τα περιεχόμενα της λίστας
    $stmt = $pdo->prepare("
        SELECT c.* 
        FROM contents c
        JOIN list_contents wc ON wc.content_id = c.id
        WHERE wc.list_id = ?
    ");
    $stmt->execute([$list_id]);
    $contents = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Σφάλμα: " . $e->getMessage());
}
?>
<!doctype html>
<html lang="el">
<head>
<meta charset="utf-8">
<title><?php echo htmlspecialchars($list['name']); ?> - Περιεχόμενα</title>
</head>
<body>
<h1><?php echo htmlspecialchars($list['name']); ?></h1>

<!-- Φόρμα αλλαγής ονόματος λίστας -->
<form method="post" action="edit_list.php" style="display:inline;">
    <input type="hidden" name="id" value="<?php echo $list['id']; ?>">
    <input type="text" name="name" value="<?php echo htmlspecialchars($list['name']); ?>" required>
    <button type="submit">✏ Αποθήκευση ονόματος</button>
</form>

<!-- Κουμπί διαγραφής λίστας -->
<a href="delete_list.php?id=<?php echo $list['id']; ?>" 
   style="color:red; margin-left:10px;"
   onclick="return confirm('Σίγουρα θέλεις να διαγράψεις ΟΛΗ τη λίστα; Αυτή η ενέργεια δεν μπορεί να αναιρεθεί.');">
   🗑 Διαγραφή λίστας
</a>

<hr>
<a href="my_lists.php">⬅ Επιστροφή στις λίστες</a>

<?php if ($contents): ?>
    <ul>
    <?php foreach ($contents as $item): ?>
        <li>
            <?php echo htmlspecialchars($item['title']); ?>
            <?php if (!empty($item['youtube_id'])): ?>
                <a href="watch.php?id=<?php echo $item['id']; ?>">▶ Δες το</a>
            <?php endif; ?>
            <!-- Διαγραφή από τη λίστα -->
            <a href="remove_from_list.php?list_id=<?php echo $list['id']; ?>&content_id=<?php echo $item['id']; ?>" 
               style="color:red;" 
               onclick="return confirm('Σίγουρα θέλεις να αφαιρέσεις αυτό το βίντεο;');">
               🗑 Αφαίρεση
            </a>
        </li>
    <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>Η λίστα είναι άδεια.</p>
<?php endif; ?>

</body>
</html>



