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

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'];
        $name = trim($_POST['name']);

        $stmt = $pdo->prepare("UPDATE watchlists SET name = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$name, $id, $_SESSION['user_id']]);

        header("Location: my_lists.php");
        exit;
    }

    if (!isset($_GET['id'])) {
        die("Λείπει το ID λίστας.");
    }

    $stmt = $pdo->prepare("SELECT * FROM watchlists WHERE id = ? AND user_id = ?");
    $stmt->execute([$_GET['id'], $_SESSION['user_id']]);
    $list = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$list) {
        die("Δεν βρέθηκε η λίστα ή δεν έχετε πρόσβαση.");
    }

} catch (PDOException $e) {
    die("Σφάλμα: " . $e->getMessage());
}
?>
<!doctype html>
<html lang="el">
<head>
<meta charset="utf-8">
<title>Επεξεργασία λίστας</title>
</head>
<body>
<h1>Επεξεργασία λίστας</h1>
<form method="post">
    <input type="hidden" name="id" value="<?php echo $list['id']; ?>">
    <input type="text" name="name" value="<?php echo htmlspecialchars($list['name']); ?>" required>
    <button type="submit">💾 Αποθήκευση</button>
</form>
<a href="my_lists.php">⬅ Επιστροφή στις λίστες</a>
</body>
</html>
