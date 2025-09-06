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

    // Παίρνουμε τα στοιχεία του χρήστη
    if (!isset($_GET['id'])) {
        die("Λείπει το ID του χρήστη.");
    }

    $user_id = $_GET['id'];

    $stmt = $pdo->prepare("SELECT id, username, first_name, last_name, email, created_at FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("Δεν βρέθηκε ο χρήστης.");
    }

    // Παίρνουμε τις λίστες του χρήστη
    $stmt = $pdo->prepare("SELECT id, name, created_at FROM watchlists WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    $user_lists = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Έλεγχος αν ο τρέχων χρήστης ακολουθεί αυτόν τον χρήστη
    $is_following = false;
    if ($user_id != $_SESSION['user_id']) {
        $stmt = $pdo->prepare("SELECT id FROM followers WHERE follower_id = ? AND following_id = ?");
        $stmt->execute([$_SESSION['user_id'], $user_id]);
        $is_following = $stmt->fetch() !== false;
    }

    // Αριθμός ακολούθων
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM followers WHERE following_id = ?");
    $stmt->execute([$user_id]);
    $follower_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    // Αριθμός που ακολουθεί
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM followers WHERE follower_id = ?");
    $stmt->execute([$user_id]);
    $following_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

} catch (PDOException $e) {
    die("Σφάλμα: " . $e->getMessage());
}

// Ορίζουμε τον τίτλο της σελίδας
$page_title = "Προφίλ - " . htmlspecialchars($user['username']) . " - StreamApp";

ob_start();
?>
<div class="w3-container">
    <!-- Πληροφορίες Χρήστη -->
    <div class="w3-card w3-padding w3-margin-bottom">
        <div class="w3-row">
            <div class="w3-col s12 m3">
                <div class="w3-center">
                    <div class="w3-circle w3-blue w3-padding" style="width: 120px; height: 120px; line-height: 120px; margin: 0 auto;">
                        <i class="fa fa-user" style="font-size: 60px;"></i>
                    </div>
                </div>
            </div>
            <div class="w3-col s12 m9">
                <h2><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h2>
                <p class="w3-text-gray">@<?php echo htmlspecialchars($user['username']); ?></p>
                <p><i class="fa fa-envelope"></i> <?php echo htmlspecialchars($user['email']); ?></p>
                <p><i class="fa fa-calendar"></i> Μέλος από: <?php echo date('d/m/Y', strtotime($user['created_at'])); ?></p>
                
                <div class="w3-row w3-margin-top">
                    <div class="w3-col s4">
                        <div class="w3-center">
                            <div class="w3-large"><?php echo $follower_count; ?></div>
                            <div class="w3-small">Ακόλουθοι</div>
                        </div>
                    </div>
                    <div class="w3-col s4">
                        <div class="w3-center">
                            <div class="w3-large"><?php echo $following_count; ?></div>
                            <div class="w3-small">Ακολουθεί</div>
                        </div>
                    </div>
                    <div class="w3-col s4">
                        <div class="w3-center">
                            <div class="w3-large"><?php echo count($user_lists); ?></div>
                            <div class="w3-small">Λίστες</div>
                        </div>
                    </div>
                </div>

                <?php if ($user_id != $_SESSION['user_id']): ?>
                <div class="w3-margin-top">
                    <?php if ($is_following): ?>
                        <a href="unfollow.php?id=<?php echo $user_id; ?>" class="w3-button w3-red w3-round">
                            <i class="fa fa-user-times"></i> Unfollow
                        </a>
                    <?php else: ?>
                        <a href="follow.php?id=<?php echo $user_id; ?>" class="w3-button w3-green w3-round">
                            <i class="fa fa-user-plus"></i> Follow
                        </a>
                    <?php endif; ?>
                </div>
                <?php else: ?>
                <div class="w3-margin-top">
                    <a href="profile.php" class="w3-button w3-blue w3-round">
                        <i class="fa fa-edit"></i> Επεξεργασία Προφίλ
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Λίστες Χρήστη -->
    <div class="w3-card w3-padding">
        <h3><i class="fa fa-list"></i> Λίστες του χρήστη</h3>
        
        <?php if ($user_lists): ?>
            <div class="w3-row-padding">
                <?php foreach ($user_lists as $list): ?>
                <div class="w3-col s12 m6 l4 w3-margin-bottom">
                    <div class="w3-card w3-padding w3-hover-shadow">
                        <h4><?php echo htmlspecialchars($list['name']); ?></h4>
                        <p class="w3-small w3-text-gray">
                            <i class="fa fa-calendar"></i> Δημιουργήθηκε: <?php echo date('d/m/Y', strtotime($list['created_at'])); ?>
                        </p>
                        <div class="w3-center">
                            <a href="view_list.php?id=<?php echo $list['id']; ?>" class="w3-button w3-blue w3-small w3-round">
                                <i class="fa fa-eye"></i> Προβολή
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="w3-panel w3-yellow w3-round">
                <p>Ο χρήστης δεν έχει δημιουργήσει ακόμα λίστες.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Navigation -->
    <div class="w3-center w3-margin-top">
        <a href="search_users.php" class="w3-button w3-blue">
            <i class="fa fa-arrow-left"></i> Επιστροφή στην αναζήτηση
        </a>
        <?php if ($user_id != $_SESSION['user_id']): ?>
        <a href="following.php" class="w3-button w3-green">
            <i class="fa fa-users"></i> Οι ακόλουθοι μου
        </a>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include("layout.php");
?>