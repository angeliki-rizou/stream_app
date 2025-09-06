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

    // Σελιδοποίηση
    $perPage = isset($_GET['perPage']) ? (int)$_GET['perPage'] : 10;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($page < 1) $page = 1;
    $offset = ($page - 1) * $perPage;

    // Φίλτρα
    $conditions = [];
    $params = [];

    if (!empty($_GET['q'])) {
        $conditions[] = "(c.title LIKE ? OR w.name LIKE ?)";
        $params[] = "%{$_GET['q']}%";
        $params[] = "%{$_GET['q']}%";
    }

    if (!empty($_GET['date_from']) && !empty($_GET['date_to'])) {
        $conditions[] = "c.created_at BETWEEN ? AND ?";
        $params[] = $_GET['date_from'] . " 00:00:00";
        $params[] = $_GET['date_to'] . " 23:59:59";
    }

    if (!empty($_GET['user'])) {
        $conditions[] = "(u.username LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ?)";
        for ($i=0; $i<4; $i++) {
            $params[] = "%" . $_GET['user'] . "%";
        }
    }

    $where = $conditions ? "WHERE " . implode(" AND ", $conditions) : "";

    // Query με join
    $sql = "
        SELECT c.id AS content_id, c.title, c.created_at, u.username, w.name AS list_name
        FROM list_contents wc
        JOIN contents c ON wc.content_id = c.id
        JOIN watchlists w ON wc.list_id = w.id
        JOIN users u ON w.user_id = u.id
        $where
        ORDER BY c.created_at DESC
        LIMIT $perPage OFFSET $offset
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Για μέτρημα όλων των αποτελεσμάτων
    $countStmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM list_contents wc
        JOIN contents c ON wc.content_id = c.id
        JOIN watchlists w ON wc.list_id = w.id
        JOIN users u ON w.user_id = u.id
        $where
    ");
    $countStmt->execute($params);
    $total = $countStmt->fetchColumn();
    $totalPages = ceil($total / $perPage);

} catch (PDOException $e) {
    die("Σφάλμα: " . $e->getMessage());
}

// τίτλος σελίδας
$page_title = "Αναζήτηση σε λίστες - StreamApp";


ob_start();
?>
<h1>🔍 Αναζήτηση σε λίστες περιεχομένου</h1>
<a href="protected.php" class="w3-button w3-blue">⬅ Επιστροφή στο μενού</a>
<hr>

<form method="get" class="w3-container w3-card w3-padding">
    <div class="w3-row-padding">
        <div class="w3-col s12 m6">
            <label>Κείμενο:</label>
            <input class="w3-input w3-border" type="text" name="q" placeholder="Τίτλος ή όνομα λίστας" value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>">
        </div>
        <div class="w3-col s12 m6">
            <label>Χρήστης:</label>
            <input class="w3-input w3-border" type="text" name="user" placeholder="Username, όνομα ή email" value="<?php echo htmlspecialchars($_GET['user'] ?? ''); ?>">
        </div>
    </div>
    
    <div class="w3-row-padding w3-margin-top">
        <div class="w3-col s12 m4">
            <label>Από:</label>
            <input class="w3-input w3-border" type="date" name="date_from" value="<?php echo htmlspecialchars($_GET['date_from'] ?? ''); ?>">
        </div>
        <div class="w3-col s12 m4">
            <label>Έως:</label>
            <input class="w3-input w3-border" type="date" name="date_to" value="<?php echo htmlspecialchars($_GET['date_to'] ?? ''); ?>">
        </div>
        <div class="w3-col s12 m4">
            <label>Ανά σελίδα:</label>
            <select class="w3-select w3-border" name="perPage">
                <option value="10" <?php if(($perPage??10)==10) echo 'selected'; ?>>10</option>
                <option value="25" <?php if(($perPage??10)==25) echo 'selected'; ?>>25</option>
                <option value="50" <?php if(($perPage??10)==50) echo 'selected'; ?>>50</option>
            </select>
        </div>
    </div>
    
    <div class="w3-center w3-margin-top">
        <button type="submit" class="w3-button w3-blue w3-round">🔍 Αναζήτηση</button>
        <a href="search_lists.php" class="w3-button w3-gray w3-round">❌ Καθαρισμός</a>
    </div>
</form>

<hr>

<?php if ($results): ?>
    <div class="w3-container">
        <h3>Αποτελέσματα (<?php echo $total; ?>):</h3>
        
        <?php foreach ($results as $r): ?>
        <div class="w3-card w3-padding w3-margin-bottom w3-hover-shadow">
            <h4><strong><?php echo htmlspecialchars($r['title']); ?></strong></h4>
            <p>
                <span class="w3-tag w3-blue">Λίστα: <?php echo htmlspecialchars($r['list_name']); ?></span>
                <span class="w3-tag w3-green">Χρήστης: <?php echo htmlspecialchars($r['username']); ?></span>
                <span class="w3-tag w3-orange">Ημ/νία: <?php echo date('d/m/Y H:i', strtotime($r['created_at'])); ?></span>
            </p>
            <a href="watch.php?id=<?php echo $r['content_id']; ?>" class="w3-button w3-green w3-small">▶ Δες το βίντεο</a>
        </div>
        <?php endforeach; ?>

        <!-- Σελιδοποίηση -->
        <div class="w3-center w3-margin-top">
            <div class="w3-bar">
                <?php if ($page > 1): ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET,['page'=>1])); ?>" class="w3-button w3-blue">&laquo;&laquo;</a>
                    <a href="?<?php echo http_build_query(array_merge($_GET,['page'=>$page-1])); ?>" class="w3-button w3-blue">&laquo;</a>
                <?php endif; ?>
                
                <?php 
                $start = max(1, $page - 2);
                $end = min($totalPages, $start + 4);
                $start = max(1, $end - 4);
                
                for ($i = $start; $i <= $end; $i++): 
                    $class = $i == $page ? 'w3-button w3-blue w3-hover-blue' : 'w3-button';
                ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET,['page'=>$i])); ?>" class="<?php echo $class; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
                
                <?php if ($page < $totalPages): ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET,['page'=>$page+1])); ?>" class="w3-button w3-blue">&raquo;</a>
                    <a href="?<?php echo http_build_query(array_merge($_GET,['page'=>$totalPages])); ?>" class="w3-button w3-blue">&raquo;&raquo;</a>
                <?php endif; ?>
            </div>
            <p>Σελίδα <?php echo $page; ?> από <?php echo $totalPages; ?> (Σύνολο: <?php echo $total; ?> αποτελέσματα)</p>
        </div>
    </div>
<?php elseif (!empty($_GET)): ?>
    <div class="w3-container">
        <div class="w3-panel w3-yellow">
            <h3>Δεν βρέθηκαν αποτελέσματα</h3>
            <p>Δοκιμάστε να αλλάξετε τα κριτήρια αναζήτησής σας.</p>
        </div>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();

// layout
include("layout.php");
?>

