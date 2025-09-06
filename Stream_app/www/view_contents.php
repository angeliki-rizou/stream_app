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

    // Παίρνουμε όλα τα περιεχόμενα από όλες τις λίστες μαζί με πληροφορίες λίστας και χρήστη
    $stmt = $pdo->prepare("
        SELECT 
            c.id, 
            c.title, 
            c.description, 
            c.category, 
            c.created_at, 
            c.youtube_id,
            w.name as list_name,
            u.username as owner_username
        FROM contents c
        JOIN list_contents lc ON c.id = lc.content_id
        JOIN watchlists w ON lc.list_id = w.id
        JOIN users u ON w.user_id = u.id
        ORDER BY c.created_at DESC
    ");
    $stmt->execute();
    $contents = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Σφάλμα σύνδεσης στη βάση: " . $e->getMessage());
}

// τίτλος σελίδας
$page_title = "Όλα τα Περιεχόμενα - StreamApp";

ob_start();
?>
<div class="w3-container">
    <div class="w3-row">
        <div class="w3-col s12 m8">
            <h1><i class="fa fa-film"></i> Όλα τα Περιεχόμενα</h1>
            <p class="w3-text-gray">Περιεχόμενα από όλες τις δημόσιες λίστες του StreamApp</p>
        </div>
        <div class="w3-col s12 m4 w3-right-align">
            <a href="protected.php" class="w3-button w3-blue">
                <i class="fa fa-arrow-left"></i> Επιστροφή
            </a>
            <a href="youtube_search.php" class="w3-button w3-green">
                <i class="fa fa-search"></i> Νέα Αναζήτηση
            </a>
        </div>
    </div>

    <div class="w3-panel w3-light-grey w3-round">
        <p><i class="fa fa-info-circle"></i> Σύνολο: <strong><?php echo count($contents); ?></strong> περιεχόμενα από όλες τις λίστες</p>
    </div>

    <hr>

    <?php if (empty($contents)): ?>
        <div class="w3-panel w3-yellow w3-round">
            <h3><i class="fa fa-info-circle"></i> Δεν υπάρχουν περιεχόμενα</h3>
            <p>Δεν έχουν προστεθεί ακόμα περιεχόμενα στο StreamApp.</p>
            <div class="w3-center w3-margin-top">
                <a href="youtube_search.php" class="w3-button w3-blue w3-round">
                    <i class="fa fa-youtube"></i> Προσθήκη Περιεχομένου
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="w3-row-padding">
            <?php foreach ($contents as $item): ?>
            <div class="w3-col s12 m6 l4 w3-margin-bottom">
                <div class="w3-card w3-padding w3-hover-shadow">
                    <!-- YouTube Thumbnail (αν υπάρχει) -->
                    <?php if (!empty($item['youtube_id'])): ?>
                    <div class="w3-center w3-margin-bottom" style="position: relative;">
                        <img src="https://img.youtube.com/vi/<?php echo $item['youtube_id']; ?>/mqdefault.jpg" 
                             alt="<?php echo htmlspecialchars($item['title']); ?>" 
                             class="w3-image w3-round" style="width: 100%; height: 180px; object-fit: cover;">
                        <div class="w3-tag w3-red w3-small" style="position: absolute; top: 10px; right: 10px;">
                            <i class="fa fa-youtube"></i> YouTube
                        </div>
                    </div>
                    <?php endif; ?>

                    <h4 class="w3-center"><?php echo htmlspecialchars($item['title']); ?></h4>
                    
                    <div class="w3-padding-small">
                        <p class="w3-text-gray" style="height: 60px; overflow: hidden; text-overflow: ellipsis;">
                            <?php echo nl2br(htmlspecialchars(mb_strimwidth($item['description'], 0, 120, '...'))); ?>
                        </p>
                    </div>

                    <!-- Πληροφορίες λίστας και χρήστη -->
                    <div class="w3-small w3-text-gray w3-margin-bottom">
                        <div class="w3-row">
                            <div class="w3-col s12">
                                <i class="fa fa-list"></i> 
                                Λίστα: <strong><?php echo htmlspecialchars($item['list_name']); ?></strong>
                            </div>
                            <div class="w3-col s12">
                                <i class="fa fa-user"></i> 
                                Προστέθηκε από: <strong><?php echo htmlspecialchars($item['owner_username']); ?></strong>
                            </div>
                        </div>
                    </div>

                    <div class="w3-center">
                        <a href="watch.php?id=<?php echo $item['id']; ?>" 
                           class="w3-button w3-green w3-round w3-small">
                           <i class="fa fa-play"></i> ▶ Δες το
                        </a>
                    </div>

                    <div class="w3-small w3-text-gray w3-margin-top">
                        <div class="w3-row">
                            <div class="w3-col s6">
                                <i class="fa fa-folder"></i> 
                                <?php echo !empty($item['category']) ? htmlspecialchars($item['category']) : 'Χωρίς κατηγορία'; ?>
                            </div>
                            <div class="w3-col s6 w3-right-align">
                                <i class="fa fa-calendar"></i> 
                                <?php echo date('d/m/Y', strtotime($item['created_at'])); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();

// layout
include("layout.php");
?>
