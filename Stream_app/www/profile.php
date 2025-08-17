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

    // Παίρνουμε στοιχεία χρήστη
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("Δεν βρέθηκε ο χρήστης.");
    }

    // Αν έγινε υποβολή φόρμας για επεξεργασία
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
        $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ? WHERE id = ?");
        $stmt->execute([$_POST['first_name'], $_POST['last_name'], $_POST['email'], $_SESSION['user_id']]);
        header("Location: profile.php");
        exit;
    }

} catch (PDOException $e) {
    die("Σφάλμα: " . $e->getMessage());
}
?>
<!doctype html>
<html lang="el">
<head>
<meta charset="utf-8">
<title>Προφίλ</title>
</head>
<body>
<h1>⚙ Το προφίλ μου</h1>
<a href="protected.php">⬅ Επιστροφή στο μενού</a>
<hr>

<form method="post">
    <label>Όνομα: <input type="text" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required></label><br>
    <label>Επώνυμο: <input type="text" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required></label><br>
    <label>Email: <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required></label><br>
    <button type="submit" name="update">💾 Αποθήκευση αλλαγών</button>
</form>

<hr>
<!-- Διαγραφή λογαριασμού -->
<a href="delete_profile.php" style="color:red;" onclick="return confirm('Σίγουρα θέλεις να διαγράψεις το προφίλ σου; Όλες οι λίστες και τα δεδομένα σου θα χαθούν!');">
    🗑 Διαγραφή προφίλ
</a>

</body>
</html>
