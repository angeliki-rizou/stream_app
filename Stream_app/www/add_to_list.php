<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

require 'db.php';

$content_id = $_GET['content_id'] ?? null;

if (!$content_id) {
    die("Λείπει το ID περιεχομένου.");
}

// Παίρνουμε τις λίστες του χρήστη
$stmt = $pdo->prepare("SELECT * FROM lists WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$lists = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $list_id = $_POST['list_id'] ?? null;
    if ($list_id) {
        $check = $pdo->prepare("SELECT id FROM watchlists WHERE id = ? AND user_id = ?");
        $check->execute([$list_id, $_SESSION['user_id']]);
        if ($check->fetch()) {
            $insert = $pdo->prepare("INSERT INTO list_contents (list_id, content_id) VALUES (?, ?)");
            $insert->execute([$list_id, $content_id]);
            echo "✅ Το περιεχόμενο προστέθηκε στη λίστα! <a href='view_list.php?id=$list_id'>Δες τη λίστα</a>";
            exit;
        } else {
            $error = "Δεν έχετε πρόσβαση σε αυτή τη λίστα.";
        }
    }
}
?>
<!doctype html>
<html lang="el">
<head>
<meta charset="utf-8">
<title>Προσθήκη σε λίστα</title>
</head>
<body>
<h1>Προσθήκη σε λίστα</h1>
<a href="view_contents.php">⬅ Πίσω</a>
<hr>

<?php if (!empty($error)): ?>
<p style="color:red;"><?php echo $error; ?></p>
<?php endif; ?>

<form method="post">
    <label for="list_id">Επιλέξτε λίστα:</label>
    <select name="list_id" id="list_id" required>
        <?php foreach ($lists as $list): ?>
            <option value="<?php echo $list['id']; ?>">
                <?php echo htmlspecialchars($list['name']); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Προσθήκη</button>
</form>

</body>
</html>
