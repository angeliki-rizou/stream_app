<?php
session_start();

// Αν είναι ήδη συνδεδεμένος → dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

// Σύνδεση με τη βάση δεδομένων
$DB_HOST = getenv('DB_HOST') ?: 'db';
$DB_NAME = getenv('DB_NAME') ?: 'stream_app';
$DB_USER = getenv('DB_USER') ?: 'appuser';
$DB_PASS = getenv('DB_PASS') ?: 'apppass';

try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    header("Location: register.php?error=" . urlencode("Σφάλμα σύνδεσης με τη βάση δεδομένων."));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Λήψη και καθαρισμός δεδομένων
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    // Βασικοί έλεγχοι
    $errors = [];

    // Έλεγχος για κενά πεδία
    if (empty($first_name) || empty($last_name) || empty($username) || empty($email) || empty($password) || empty($password_confirm)) {
        $errors[] = "Όλα τα πεδία είναι υποχρεωτικά.";
    }

    // Έλεγχος email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Το email δεν είναι έγκυρο.";
    }

    // Έλεγχος κωδικών
    if ($password !== $password_confirm) {
        $errors[] = "Οι κωδικοί δεν ταιριάζουν.";
    }

    if (strlen($password) < 6) {
        $errors[] = "Ο κωδικός πρέπει να έχει τουλάχιστον 6 χαρακτήρες.";
    }

    // Έλεγχος αν το username ή email υπάρχουν ήδη
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            
            if ($stmt->fetch()) {
                $errors[] = "Το username ή το email χρησιμοποιείται ήδη.";
            }
        } catch (PDOException $e) {
            $errors[] = "Σφάλμα κατά τον έλεγχο των δεδομένων.";
        }
    }

    // Αν υπάρχουν σφάλματα, επιστροφή στη φόρμα
    if (!empty($errors)) {
        $error_string = implode(" ", $errors);
        header("Location: register.php?error=" . urlencode($error_string));
        exit;
    }

    // Καταχώρηση του χρήστη στη βάση
    try {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, username, email, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$first_name, $last_name, $username, $email, $hashed_password]);
        
        // Αυτόματη σύνδεση μετά την εγγραφή
        $user_id = $pdo->lastInsertId();
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        $_SESSION['first_name'] = $first_name;
        
        header("Location: protected.php?success=" . urlencode("Εγγραφήκατε με επιτυχία! Καλώς ήρθατε, " . $first_name . "!"));
        exit;
        
    } catch (PDOException $e) {
        header("Location: register.php?error=" . urlencode("Σφάλμα κατά την εγγραφή. Παρακαλώ δοκιμάστε ξανά."));
        exit;
    }
} else {
    // Αν δεν είναι POST request, επιστροφή στη φόρμα
    header("Location: register.php");
    exit;
}