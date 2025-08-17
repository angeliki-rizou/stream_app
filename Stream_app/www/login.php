<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.html');
    exit;
}

$identifier = trim($_POST['identifier'] ?? '');
$password   = $_POST['password'] ?? '';

if (!$identifier || !$password) {
    echo "<p style='color:red;'>Συμπλήρωσε όλα τα πεδία.</p>";
    echo "<p><a href='login.html'>Πίσω</a></p>";
    exit;
}

// Έλεγχος αν υπάρχει χρήστης με αυτό το username ή email
$stmt = $pdo->prepare('SELECT * FROM users WHERE username = :username OR email = :email LIMIT 1');
$stmt->execute([':username' => $identifier, ':email' => $identifier]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    echo "<p style='color:red;'>Λάθος στοιχεία σύνδεσης.</p>";
    echo "<p><a href='login.html'>Προσπάθησε ξανά</a></p>";
    exit;
}

// Δημιουργία session
$_SESSION['user_id'] = $user['id'];
$_SESSION['username'] = $user['username'];

header('Location: protected.php');
exit;
