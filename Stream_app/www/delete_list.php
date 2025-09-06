<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

if (!isset($_GET['id'])) {
    die("Λείπει το ID λίστας.");
}

$DB_HOST = getenv('DB_HOST') ?: 'db';
$DB_NAME = getenv('DB_NAME') ?: 'stream_app';
$DB_USER = getenv('DB_USER') ?: 'appuser';
$DB_PASS = getenv('DB_PASS') ?: 'apppass';

try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Παίρνουμε το όνομα της λίστας για να το εμφανίσουμε
    $stmt = $pdo->prepare("SELECT id, name FROM watchlists WHERE id = ? AND user_id = ?");
    $stmt->execute([$_GET['id'], $_SESSION['user_id']]);
    $list = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$list) {
        die("Δεν βρέθηκε η λίστα ή δεν έχετε δικαίωμα διαγραφής.");
    }

    // Έλεγχος αν ο χρήστης έχει επιβεβαιώσει τη διαγραφή
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
        // Σβήνουμε όλες τις συνδέσεις της λίστας με περιεχόμενο
        $stmt = $pdo->prepare("DELETE FROM list_contents WHERE list_id = ?");
        $stmt->execute([$_GET['id']]);

        // Σβήνουμε τη λίστα
        $stmt = $pdo->prepare("DELETE FROM watchlists WHERE id = ?");
        $stmt->execute([$_GET['id']]);

        header("Location: my_lists.php?deleted=1");
        exit;
    }

} catch (PDOException $e) {
    die("Σφάλμα: " . $e->getMessage());
}

// τίτλος σελίδας
$page_title = "Διαγραφή Λίστας - StreamApp";

ob_start();
?>
<div class="w3-container">
    <div class="w3-row">
        <div class="w3-col s12">
            <h1><i class="fa fa-exclamation-triangle w3-text-red"></i> Διαγραφή Λίστας</h1>
        </div>
    </div>

    <div class="w3-panel w3-red w3-round">
        <h3><i class="fa fa-warning"></i> Προσοχή!</h3>
        <p>Είστε έτοιμος να διαγράψετε τη λίστα: <strong><?php echo htmlspecialchars($list['name']); ?></strong></p>
    </div>

    <div class="w3-card w3-padding w3-round">
        <h3><i class="fa fa-info-circle"></i> Τι θα συμβεί:</h3>
        <ul class="w3-ul">
            <li><i class="fa fa-times w3-text-red"></i> Η λίστα <strong>"<?php echo htmlspecialchars($list['name']); ?>"</strong> θα διαγραφεί οριστικά</li>
            <li><i class="fa fa-chain-broken w3-text-orange"></i> Όλα τα περιεχόμενα θα αποσπαστούν από αυτή τη λίστα</li>
            <li><i class="fa fa-exclamation w3-text-orange"></i> Τα ίδια τα περιεχόμενα <strong>ΔΕΝ</strong> θα διαγραφούν από το σύστημα</li>
            <li><i class="fa fa-undo w3-text-gray"></i> Αυτή η ενέργεια <strong>ΔΕΝ</strong> μπορεί να αναιρεθεί</li>
        </ul>
    </div>

    <div class="w3-panel w3-center" style="margin-top: 30px;">
        <form method="post" style="display: inline-block;">
            <input type="hidden" name="confirm" value="1">
            <button type="submit" class="w3-button w3-red w3-large w3-round" 
                    onclick="return confirm('Είστε ΑΒΣΟΛΥΤΑ σίγουρος ότι θέλετε να διαγράψετε οριστικά τη λίστα;');">
                <i class="fa fa-trash"></i> ΝΑΙ, ΔΙΑΓΡΑΦΗ
            </button>
        </form>
        
        <a href="my_lists.php" class="w3-button w3-green w3-large w3-round" style="margin-left: 20px;">
            <i class="fa fa-times"></i> ΑΚΥΡΟ
        </a>
    </div>

    <div class="w3-panel w3-light-grey w3-round">
        <h4><i class="fa fa-question-circle"></i> Ερωτήσεις;</h4>
        <p>Αν δεν είστε σίγουρος, επιλέξτε "ΑΚΥΡΟ" και η λίστα σας θα παραμείνει ασφαλής.</p>
    </div>
</div>

<?php
$content = ob_get_clean();

// layout
include("layout.php");
?>
