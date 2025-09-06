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

    // Get user's watchlists
    $stmt = $pdo->prepare("SELECT * FROM watchlists WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user_lists = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Σφάλμα σύνδεσης: " . $e->getMessage());
}

// YouTube API Key
$apiKey = "AIzaSyACEeNAnLjJ8opYP5YLrEu4Xh-4_JCLfKE";

// Αναζήτηση στο YouTube
$searchResults = [];
$nextPage = null;
$prevPage = null;

if (!empty($_GET['query'])) {
    $query = urlencode($_GET['query']);
    $maxResults = 10; 
    $pageToken = isset($_GET['page']) ? $_GET['page'] : '';

    $url = "https://www.googleapis.com/youtube/v3/search?part=snippet&q={$query}&type=video&maxResults={$maxResults}&key={$apiKey}";
    if ($pageToken) {
        $url .= "&pageToken=" . urlencode($pageToken);
    }

    $json = file_get_contents($url);
    $data = json_decode($json, true);

    if (!empty($data['items'])) {
        foreach ($data['items'] as $item) {
            $searchResults[] = [
                'videoId' => $item['id']['videoId'],
                'title' => $item['snippet']['title'],
                'description' => $item['snippet']['description'],
                'thumbnail' => $item['snippet']['thumbnails']['high']['url'] // Χρησιμοποιούμε higher quality thumbnail
            ];
        }
    }

    // αποθήκευσε tokens για σελιδοποίηση
    $nextPage = $data['nextPageToken'] ?? null;
    $prevPage = $data['prevPageToken'] ?? null;
}

// τίτλος σελίδας
$page_title = "Αναζήτηση YouTube ";

ob_start();
?>
<h1><i  style="color: #FF0000;"></i> Αναζήτηση YouTube</h1>
<div class="w3-margin-bottom">
    <a href="protected.php" class="w3-button w3-blue">⬅ Επιστροφή</a>
    <a href="my_lists.php" class="w3-button w3-green">📋 Οι λίστες μου</a>
</div>
<hr>

<form method="get" class="w3-container w3-card w3-padding">
    <div class="w3-row">
        <div class="w3-col s10">
            <input class="w3-input w3-border" type="text" name="query" 
                   value="<?php echo isset($_GET['query']) ? htmlspecialchars($_GET['query']) : ''; ?>" 
                   placeholder="Αναζήτηση βίντεο..." required>
        </div>
        <div class="w3-col s2">
            <button type="submit" class="w3-button w3-red w3-block">
                <i class="fa fa-search"></i> Αναζήτηση
            </button>
        </div>
    </div>
</form>

<hr>

<?php if (!empty($searchResults)): ?>
    <div class="w3-container">
        <h3>Αποτελέσματα αναζήτησης:</h3>
        
        <?php foreach ($searchResults as $video): ?>
        <div class="w3-card w3-padding w3-margin-bottom w3-hover-shadow">
            <div class="w3-row">
                <div class="w3-col s12 m4 l3">
                    <img src="<?php echo htmlspecialchars($video['thumbnail']); ?>" 
                         alt="thumbnail" class="w3-image w3-round" style="width: 100%;">
                </div>
                <div class="w3-col s12 m8 l9">
                    <h4><?php echo htmlspecialchars($video['title']); ?></h4>
                    <p class="w3-text-gray"><?php echo nl2br(htmlspecialchars(mb_strimwidth($video['description'], 0, 200, '...'))); ?></p>
                    
                    <div class="w3-margin-top">
                        <a href="https://www.youtube.com/watch?v=<?php echo $video['videoId']; ?>" 
                           target="_blank" class="w3-button w3-red w3-round">
                           <i ></i> ▶ Δες στο YouTube
                        </a>
                        
                        <?php if (!empty($user_lists)): ?>
                        <div class="w3-margin-top">
                            <form method="post" action="add_youtube_to_list.php" class="w3-container w3-light-grey w3-padding w3-round">
                                <input type="hidden" name="title" value="<?php echo htmlspecialchars($video['title'], ENT_QUOTES); ?>">
                                <input type="hidden" name="desc" value="<?php echo htmlspecialchars($video['description'], ENT_QUOTES); ?>">
                                <input type="hidden" name="youtube_id" value="<?php echo htmlspecialchars($video['videoId']); ?>">
                                
                                <div class="w3-row">
                                    <div class="w3-col s8">
                                        <select class="w3-select w3-border" name="list_id" required>
                                            <option value="">-- Επιλέξτε λίστα --</option>
                                            <?php foreach ($user_lists as $list): ?>
                                                <option value="<?php echo $list['id']; ?>"><?php echo htmlspecialchars($list['name']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="w3-col s4">
                                        <button type="submit" class="w3-button w3-green w3-block">
                                            <i class="fa fa-plus"></i> Προσθήκη
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <?php else: ?>
                        <div class="w3-panel w3-yellow w3-round">
                            <p><i class="fa fa-info-circle"></i> Δεν έχεις λίστες. <a href="my_lists.php">Δημιούργησε μία πρώτα</a>.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <!-- Σελιδοποίηση -->
        <div class="w3-center w3-margin-top">
            <div class="w3-bar">
                <?php if ($prevPage): ?>
                    <a href="?query=<?php echo urlencode($_GET['query']); ?>&page=<?php echo $prevPage; ?>" 
                       class="w3-button w3-blue">&laquo; Προηγούμενα</a>
                <?php endif; ?>

                <?php if ($nextPage): ?>
                    <a href="?query=<?php echo urlencode($_GET['query']); ?>&page=<?php echo $nextPage; ?>" 
                       class="w3-button w3-blue">Επόμενα &raquo;</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

<?php elseif (isset($_GET['query'])): ?>
    <div class="w3-container">
        <div class="w3-panel w3-yellow w3-round">
            <h3>Δεν βρέθηκαν αποτελέσματα</h3>
            <p>Δοκιμάστε να αλλάξετε τους όρους αναζήτησής σας.</p>
        </div>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();

// layout
include("layout.php");
?>
