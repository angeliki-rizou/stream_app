<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.html');
    exit;
}

$first = trim($_POST['first_name'] ?? '');
$last  = trim($_POST['last_name'] ?? '');
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$password_confirm = $_POST['password_confirm'] ?? '';

$errors = [];

if (!$first || !$last || !$username || !$email || !$password) {
    $errors[] = 'Όλα τα πεδία είναι υποχρεωτικά.';
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Μη έγκυρο email.';
}

if ($password !== $password_confirm) {
    $errors[] = 'Οι κωδικοί δεν ταιριάζουν.';
}

if (strlen($password) < 8) {
    $errors[] = 'Ο κωδικός πρέπει να έχει τουλάχιστον 8 χαρακτήρες.';
}

if ($errors) {
    foreach ($errors as $err) {
        echo "<p style='color:red;'>$err</p>";
    }
    echo "<p><a href='register.html'>Πίσω</a></p>";
    exit;
}

$stmt = $pdo->prepare('SELECT id FROM users WHERE username = :u OR email = :e LIMIT 1');
$stmt->execute([':u' => $username, ':e' => $email]);
if ($stmt->fetch()) {
    echo "<p style='color:red;'>Το username ή το email υπάρχει ήδη.</p>";
    echo "<p><a href='register.html'>Πίσω</a></p>";
    exit;
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$pdo->prepare('INSERT INTO users (first_name, last_name, username, password, email) VALUES (?, ?, ?, ?, ?)')
    ->execute([$first, $last, $username, $hash, $email]);

echo "<p>Επιτυχής εγγραφή!</p><p><a href='register.html'>Πίσω</a></p>";
