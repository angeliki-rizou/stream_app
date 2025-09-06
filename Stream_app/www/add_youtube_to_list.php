<?php
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

    $title = $_POST['title'] ?? null;
    $description = $_POST['desc'] ?? '';
    $list_id = $_POST['list_id'] ?? null;
    $youtube_id = $_POST['youtube_id'] ?? null;

    if (!$title || !$list_id || !$youtube_id) {
        die("Λείπουν δεδομένα.");
    }

    // Check if list belongs to user
    $check = $pdo->prepare("SELECT id FROM watchlists WHERE id = ? AND user_id = ?");
    $check->execute([$list_id, $_SESSION['user_id']]);
    if (!$check->fetch()) {
        die("Δεν έχετε πρόσβαση σε αυτή τη λίστα.");
    }

    // Insert into contents with YouTube ID
    $insert_content = $pdo->prepare("INSERT INTO contents (title, description, created_by, youtube_id) VALUES (?, ?, ?, ?)");
    $insert_content->execute([$title, $description, $_SESSION['user_id'], $youtube_id]);
    $content_id = $pdo->lastInsertId();

    // Link content to list
    $insert_list = $pdo->prepare("INSERT INTO list_contents (list_id, content_id) VALUES (?, ?)");
    $insert_list->execute([$list_id, $content_id]);

    //τίτλος σελίδας
    $page_title = "Προσθήκη βίντεο - StreamApp";


    ob_start();
    ?>
    <div class="w3-container">
        <div class="w3-panel w3-green">
            <h3>✅ Επιτυχία!</h3>
            <p>Το βίντεο προστέθηκε στη λίστα!</p>
        </div>
        <p>
            <a href='view_list.php?id=<?php echo $list_id; ?>' class="w3-button w3-blue">Δες τη λίστα</a>
            <a href='youtube_search.php' class="w3-button w3-green">🔍 Νέα αναζήτηση</a>
        </p>
    </div>
    <?php
    $content = ob_get_clean();

    // layout
    include("layout.php");

} catch (PDOException $e) {
    //τίτλος σελίδας
    $page_title = "Σφάλμα - StreamApp";

    // σφάλμα
    ob_start();
    ?>
    <div class="w3-container">
        <div class="w3-panel w3-red">
            <h3>❌ Σφάλμα!</h3>
            <p>Σφάλμα: <?php echo $e->getMessage(); ?></p>
        </div>
        <p>
            <a href='youtube_search.php' class="w3-button w3-blue">🔍 Επιστροφή στην αναζήτηση</a>
        </p>
    </div>
    <?php
    $content = ob_get_clean();

    // layout
    include("layout.php");
}
?>

