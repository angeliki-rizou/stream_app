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

    $stmt = $pdo->prepare("
        SELECT u.id, u.username, u.first_name, u.last_name 
        FROM followers f
        JOIN users u ON f.following_id = u.id
        WHERE f.follower_id = ?
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Σφάλμα: " . $e->getMessage());
}
// τίτλος σελίδας
$page_title = "Ποιους ακολουθώ";


ob_start();
?>
<h1> Ποιους ακολουθώ</h1>
<a href="protected.php" class="w3-button w3-blue">⬅ Επιστροφή στο μενού</a>
<hr>

<?php if ($users): ?>
    <div class="w3-container">
        <div class="w3-panel w3-blue w3-round">
            <h3>Ακολουθείς: <?php echo count($users); ?> χρήστες</h3>
        </div>
        
        <div class="w3-row-padding">
            <?php foreach ($users as $user): ?>
            <div class="w3-col s12 m6 l4 w3-margin-bottom">
                <div class="w3-card w3-padding w3-hover-shadow">
                    <div class="w3-center">
                        <div class="w3-circle w3-blue w3-padding" style="width: 60px; height: 60px; line-height: 60px;">
                            <i class="fa fa-user" style="font-size: 24px;"></i>
                        </div>
                    </div>
                    <h3 class="w3-center"><?php echo htmlspecialchars($user['username']); ?></h3>
                    <p class="w3-center w3-text-gray"><?php echo htmlspecialchars($user['first_name'] . " " . $user['last_name']); ?></p>
                    <div class="w3-center">
                        <a href="view_user.php?id=<?php echo $user['id']; ?>" class="w3-button w3-blue w3-small w3-round">👁️ Προβολή προφίλ</a>
                        <a href="unfollow.php?id=<?php echo $user['id']; ?>" 
                           class="w3-button w3-red w3-small w3-round"
                           onclick="return confirm('Σίγουρα θέλεις να σταματήσεις να ακολουθείς τον χρήστη <?php echo htmlspecialchars($user['username']); ?>;');">
                           ❌ Διαγραφή
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php else: ?>
    <div class="w3-container">
        <div class="w3-panel w3-yellow w3-round">
            <h3>Δεν ακολουθείς κανέναν ακόμα</h3>
            <p>Αναζητήστε άλλους χρήστες για να αρχίσετε να τους ακολουθείτε!</p>
        </div>
        <div class="w3-center">
            <a href="search_users.php" class="w3-button w3-blue w3-round">🔍 Αναζήτηση χρηστών</a>
        </div>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();

// layout
include("layout.php");
?>
