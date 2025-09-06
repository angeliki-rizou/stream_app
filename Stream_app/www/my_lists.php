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

    $stmt = $pdo->prepare("SELECT * FROM watchlists WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $lists = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Σφάλμα: " . $e->getMessage());
}

// τίτλος σελίδας
$page_title = "Οι Λίστες Μου - StreamApp";

ob_start();
?>
<div class="w3-container">
    <div class="w3-row">
        <div class="w3-col s12 m8">
            <h1><i class="fa fa-list"></i> Οι Λίστες Μου</h1>
        </div>
        <div class="w3-col s12 m4 w3-right-align">
            <a href="protected.php" class="w3-button w3-blue">
                <i class="fa fa-arrow-left"></i> Επιστροφή
            </a>
        </div>
    </div>

    <div class="w3-panel w3-light-grey w3-round">
        <p><i class="fa fa-info-circle"></i> Σύνολο λιστών: <strong><?php echo count($lists); ?></strong></p>
    </div>

    <hr>

    <?php if ($lists): ?>
        <div class="w3-row-padding">
            <?php foreach ($lists as $list): ?>
            <div class="w3-col s12 m6 l4 w3-margin-bottom">
                <div class="w3-card w3-padding w3-hover-shadow">
                    <div class="w3-row">
                        <div class="w3-col s10">
                            <h3 style="margin: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                <?php echo htmlspecialchars($list['name']); ?>
                            </h3>
                        </div>
                        <div class="w3-col s2 w3-right-align">
                            <span class="w3-tag w3-<?php echo $list['is_public'] ? 'green' : 'gray'; ?> w3-tiny w3-round">
                                <?php echo $list['is_public'] ? 'Δημόσια' : 'Ιδιωτική'; ?>
                            </span>
                        </div>
                    </div>
                    
                    <p class="w3-small w3-text-gray">
                        <i class="fa fa-calendar"></i> Δημιουργήθηκε: <?php echo date('d/m/Y', strtotime($list['created_at'])); ?>
                    </p>

                    <div class="w3-center">
                        <a href="view_list.php?id=<?php echo $list['id']; ?>" 
                           class="w3-button w3-blue w3-small w3-round" title="Προβολή λίστας">
                           <i class="fa fa-eye"></i> Προβολή
                        </a>
                        
                        <a href="edit_list.php?id=<?php echo $list['id']; ?>" 
                           class="w3-button w3-orange w3-small w3-round" title="Επεξεργασία λίστας">
                           <i class="fa fa-edit"></i> Επεξεργασία
                        </a>
                        
                        <a href="delete_list.php?id=<?php echo $list['id']; ?>" 
                           class="w3-button w3-red w3-small w3-round" 
                           title="Διαγραφή λίστας"
                           onclick="return confirm('Σίγουρα θέλεις να διαγράψεις τη λίστα \"<?php echo addslashes($list['name']); ?>\"?');">
                           <i class="fa fa-trash"></i> Διαγραφή
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="w3-container">
            <div class="w3-panel w3-yellow w3-round">
                <h3><i class="fa fa-info-circle"></i> Δεν έχεις δημιουργήσει καμία λίστα</h3>
                <p>Δημιούργησε την πρώτη σου λίστα για να οργανώσεις τα αγαπημένα σου περιεχόμενα!</p>
            </div>
        </div>
    <?php endif; ?>

    <hr>

    <div class="w3-card w3-padding w3-light-grey w3-round">
        <h2><i class="fa fa-plus-circle"></i> Δημιουργία Νέας Λίστας</h2>
        
        <form method="post" action="add_list.php">
            <div class="w3-section">
                <label><b>Όνομα λίστας:</b></label>
                <input class="w3-input w3-border w3-round" 
                       type="text" 
                       name="name" 
                       placeholder="Π.χ. Αγαπημένα Βίντεο, Μουσική, κλπ..." 
                       required
                       style="padding: 10px;">
            </div>
            
            <div class="w3-section">
                <label class="w3-checkbox">
                    <input type="checkbox" name="is_public" value="1">
                    <span class="w3-checkmark"></span>
                    <i class="fa fa-globe"></i> Δημόσια λίστα (ορατή από άλλους χρήστες)
                </label>
            </div>

            <div class="w3-center">
                <button type="submit" class="w3-button w3-green w3-round w3-large">
                    <i class="fa fa-plus"></i> ➕ Δημιουργία Λίστας
                </button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();

// layout
include("layout.php");
?>
