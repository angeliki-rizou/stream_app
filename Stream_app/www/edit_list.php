<?php
ob_start();

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
        $is_public = isset($_POST['is_public']) ? 1 : 0;

        $stmt = $pdo->prepare("UPDATE watchlists SET name = ?, is_public = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$name, $is_public, $id, $_SESSION['user_id']]);

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

//τίτλος σελίδας
$page_title = "Επεξεργασία Λίστας - StreamApp";

ob_start();
?>
<div class="w3-container">
    <div class="w3-row">
        <div class="w3-col s12 m8">
            <h1><i class="fa fa-edit"></i> Επεξεργασία Λίστας</h1>
        </div>
        <div class="w3-col s12 m4 w3-right-align">
            <a href="my_lists.php" class="w3-button w3-blue">
                <i class="fa fa-arrow-left"></i> Επιστροφή
            </a>
        </div>
    </div>

    <hr>

    <div class="w3-card w3-padding w3-round" style="max-width: 600px; margin: 0 auto;">
        <form method="post">
            <input type="hidden" name="id" value="<?php echo $list['id']; ?>">
            
            <div class="w3-section">
                <label><b><i class="fa fa-list"></i> Όνομα Λίστας</b></label>
                <input class="w3-input w3-border w3-round w3-margin-bottom" 
                       type="text" 
                       name="name" 
                       value="<?php echo htmlspecialchars($list['name']); ?>" 
                       placeholder="Π.χ. Αγαπημένα Βίντεο" 
                       required
                       style="padding: 12px;">
            </div>

            <div class="w3-section">
                <label class="w3-checkbox">
                    <input type="checkbox" name="is_public" value="1" <?php echo $list['is_public'] ? 'checked' : ''; ?>>
                    <span class="w3-checkmark"></span>
                    <i class="fa fa-globe"></i> Δημόσια λίστα
                </label>
                <p class="w3-small w3-text-gray">
                    <i class="fa fa-info-circle"></i> Οι δημόσιες λίστες είναι ορατές από άλλους χρήστες.
                </p>
            </div>

            <div class="w3-center w3-margin-top">
                <button type="submit" class="w3-button w3-green w3-round w3-large">
                    <i class="fa fa-save"></i> Αποθήκευση Αλλαγών
                </button>
            </div>
        </form>
    </div>

</div>

<?php
$content = ob_get_clean();
ob_end_flush(); 

// layout
include("layout.php");
?>
