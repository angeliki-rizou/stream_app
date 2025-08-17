<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    if ($name) {
        $stmt = $pdo->prepare("INSERT INTO watchlists (user_id, name) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $name]);
        header('Location: my_lists.php');
        exit;
    } else {
        $error = "Το όνομα της λίστας δεν μπορεί να είναι κενό.";
    }
}
?>
<!doctype html>
<html lang="el">
<head>
<meta charset="utf-8">
<title>Δημιουργία Λίστας</title>
</head>
<body>
<h1>Δημιουργία Νέας Λίστας</h1>
<a href="my_lists.php">⬅ Πίσω στις λίστες</a>
<hr>
<?php if (!empty($error)): ?>
<p style="color:red;"><?php echo $error; ?></p>
<?php endif; ?>
<form method="post">
    <input type="text" name="name" placeholder="Όνομα λίστας" required>
    <button type="submit">Δημιουργία</button>
</form>
</body>
</html>

