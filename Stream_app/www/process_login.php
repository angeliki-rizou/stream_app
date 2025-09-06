<?php
session_start();

$DB_HOST = getenv('DB_HOST') ?: 'db';
$DB_NAME = getenv('DB_NAME') ?: 'stream_app';
$DB_USER = getenv('DB_USER') ?: 'appuser';
$DB_PASS = getenv('DB_PASS') ?: 'apppass';

try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Σφάλμα σύνδεσης: " . $e->getMessage());
}

// Αν η φόρμα δεν στάλθηκε σωστά
if (empty($_POST['username']) || empty($_POST['password'])) {
    header("Location: login.php?error=1");
    exit;
}

$username = trim($_POST['username']);
$password = $_POST['password'];

// Αναζητούμε τον χρήστη
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password'])) {
    // Αποθηκεύουμε τα στοιχεία στο session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];

    // Ανακατεύθυνση στο dashboard (protected)
    header("Location: protected.php");
    exit;
} else {
    // Αν τα στοιχεία δεν είναι σωστά
    header("Location: login.php?error=1");
    exit;
}

