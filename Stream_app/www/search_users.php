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

    $results = [];
    if (!empty($_GET['q'])) {
        $q = "%" . $_GET['q'] . "%";
        $stmt = $pdo->prepare("SELECT id, username, first_name, last_name FROM users WHERE username LIKE ? AND id != ?");
        $stmt->execute([$q, $_SESSION['user_id']]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (PDOException $e) {
    die("Σφάλμα: " . $e->getMessage());
}

//τίτλος σελίδας
$page_title = "Αναζήτηση χρηστών";


ob_start();
?>
<h1>Αναζήτηση χρηστών</h1>
<a href="protected.php" class="w3-button w3-blue">⬅ Επιστροφή στο μενού</a>
<hr>

<form method="get" class="w3-container w3-padding">
    <div class="w3-row">
        <div class="w3-col s10">
            <input class="w3-input w3-border" type="text" name="q" placeholder="Username" required value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
        </div>
        <div class="w3-col s2">
            <button type="submit" class="w3-button w3-blue w3-block">Αναζήτηση</button>
        </div>
    </div>
</form>

<?php if ($results): ?>
    <div class="w3-container w3-margin-top">
        <div class="w3-row-padding">
            <?php foreach ($results as $user): ?>
            <div class="w3-col s12 m6 l4 w3-margin-bottom">
                <div class="w3-card w3-padding w3-hover-shadow">
                    <h4><?php echo htmlspecialchars($user['username']); ?></h4>
                    <p><?php echo htmlspecialchars($user['first_name'] . " " . $user['last_name']); ?></p>
                    <a href="follow.php?id=<?php echo $user['id']; ?>" class="w3-button w3-green w3-small">➕ Ακολούθησε</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php elseif (!empty($_GET['q'])): ?>
    <div class="w3-container w3-margin-top">
        <div class="w3-panel w3-yellow">
            <p>Δεν βρέθηκαν χρήστες.</p>
        </div>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();

// layout
include("layout.php");
?>
