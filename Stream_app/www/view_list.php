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

    if (!isset($_GET['id'])) {
        die("Λείπει το ID λίστας.");
    }

    $list_id = $_GET['id'];

    // Αλλαγή: Ελέγχουμε αν ο χρήστης είναι ο owner Ή αν η λίστα είναι δημόσια
    $stmt = $pdo->prepare("SELECT w.*, u.username 
                          FROM watchlists w 
                          JOIN users u ON w.user_id = u.id 
                          WHERE w.id = ? AND (w.user_id = ? OR w.is_public = 1)");
    $stmt->execute([$list_id, $_SESSION['user_id']]);
    $list = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$list) {
        die("Δεν βρέθηκε η λίστα ή δεν έχετε πρόσβαση.");
    }

    // Παίρνουμε τα περιεχόμενα της λίστας
    $stmt = $pdo->prepare("
        SELECT c.* 
        FROM contents c
        JOIN list_contents wc ON wc.content_id = c.id
        WHERE wc.list_id = ?
    ");
    $stmt->execute([$list_id]);
    $contents = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Σφάλμα: " . $e->getMessage());
}

// Ορίζουμε τον τίτλο της σελίδας
$page_title = htmlspecialchars($list['name']) . " - StreamApp";

ob_start();
?>
<div class="w3-container">
    <div class="w3-row">
        <div class="w3-col s12 m8">
            <h1><i class="fa fa-list"></i> <?php echo htmlspecialchars($list['name']); ?></h1>
            <p class="w3-text-gray">Δημιουργήθηκε από: <?php echo htmlspecialchars($list['username']); ?></p>
        </div>
        <div class="w3-col s12 m4 w3-right-align">
            <a href="javascript:history.back()" class="w3-button w3-blue">
                <i class="fa fa-arrow-left"></i> Πίσω
            </a>
            <?php if ($list['user_id'] == $_SESSION['user_id']): ?>
                <a href="my_lists.php" class="w3-button w3-green">
                    <i class="fa fa-list"></i> Οι λίστες μου
                </a>
            <?php endif; ?>
        </div>
    </div>

    <hr>

    <?php if ($contents): ?>
        <div class="w3-panel w3-green w3-round">
            <h3><i class="fa fa-film"></i> Περιεχόμενα (<?php echo count($contents); ?>)</h3>
        </div>
        
        <div class="w3-row-padding">
            <?php foreach ($contents as $item): ?>
            <div class="w3-col s12 m6 l4 w3-margin-bottom">
                <div class="w3-card w3-padding w3-hover-shadow">
                    <h4><?php echo htmlspecialchars($item['title']); ?></h4>
                    
                    <?php if (!empty($item['youtube_id'])): ?>
                    <div class="w3-center w3-margin-bottom">
                        <img src="https://img.youtube.com/vi/<?php echo $item['youtube_id']; ?>/mqdefault.jpg" 
                             alt="Thumbnail" class="w3-image w3-round" style="width: 100%;">
                    </div>
                    <?php endif; ?>
                    
                    <div class="w3-center">
                        <?php if (!empty($item['youtube_id'])): ?>
                        <a href="watch.php?id=<?php echo $item['id']; ?>" 
                           class="w3-button w3-green w3-small w3-round w3-margin-bottom">
                           <i class="fa fa-play"></i> ▶ Δες το
                        </a>
                        <?php endif; ?>
                        
                        <?php if ($list['user_id'] == $_SESSION['user_id']): ?>
                        <a href="remove_from_list.php?list_id=<?php echo $list['id']; ?>&content_id=<?php echo $item['id']; ?>" 
                           class="w3-button w3-red w3-small w3-round"
                           onclick="return confirm('Σίγουρα θέλεις να αφαιρέσεις αυτό το περιεχόμενο;');">
                           <i class="fa fa-times"></i> Αφαίρεση
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="w3-container">
            <div class="w3-panel w3-yellow w3-round">
                <h3><i class="fa fa-info-circle"></i> Η λίστα είναι άδεια</h3>
                <p>Ο χρήστης δεν έχει προσθέσει ακόμα περιεχόμενο σε αυτή τη λίστα.</p>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include("layout.php");
?>




