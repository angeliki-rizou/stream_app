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
?>
<!doctype html>
<html lang="el">
<head>
<meta charset="utf-8">
<title>Αναζήτηση σε λίστες</title>
</head>
<body>
<h1>🔍 Αναζήτηση σε λίστες περιεχομένου</h1>
<a href="protected.php">⬅ Επιστροφή στο μενού</a>
<hr>

<form method="get">
    <label>Κείμενο: <input type="text" name="q" value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>"></label><br>
    <label>Από: <input type="date" name="date_from" value="<?php echo htmlspecialchars($_GET['date_from'] ?? ''); ?>"></label>
    <label>Έως: <input type="date" name="date_to" value="<?php echo htmlspecialchars($_GET['date_to'] ?? ''); ?>"></label><br>
    <label>Χρήστης: <input type="text" name="user" value="<?php echo htmlspecialchars($_GET['user'] ?? ''); ?>"></label><br>
    <label>Ανά σελίδα: 
        <select name="perPage">
            <option value="10" <?php if(($perPage??10)==10) echo 'selected'; ?>>10</option>
            <option value="25" <?php if(($perPage??10)==25) echo 'selected'; ?>>25</option>
        </select>
    </label>
    <button type="submit">Αναζήτηση</button>
</form>

<hr>

<?php if ($results): ?>
    <ul>
    <?php foreach ($results as $r): ?>
        <li>
            <strong><?php echo htmlspecialchars($r['title']); ?></strong>
            (Λίστα: <?php echo htmlspecialchars($r['list_name']); ?>, 
            Χρήστης: <?php echo htmlspecialchars($r['username']); ?>, 
            Ημ/νία: <?php echo htmlspecialchars($r['created_at']); ?>)
            <a href="watch.php?id=<?php echo $r['content_id']; ?>">▶ Δες το</a>
        </li>
    <?php endforeach; ?>
    </ul>

    <p>Σελίδα <?php echo $page; ?> από <?php echo $totalPages; ?></p>
    <?php for ($i=1; $i <= $totalPages; $i++): ?>
        <a href="?<?php echo http_build_query(array_merge($_GET,['page'=>$i])); ?>">
            <?php echo $i; ?>
        </a>
    <?php endfor; ?>

<?php else: ?>
    <p>Δεν βρέθηκαν αποτελέσματα.</p>
<?php endif; ?>

</body>
</html>
