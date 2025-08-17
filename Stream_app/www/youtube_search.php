<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

// DB connection
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
                'thumbnail' => $item['snippet']['thumbnails']['default']['url']
            ];
        }
    }

    // αποθήκευσε tokens για σελιδοποίηση
    $nextPage = $data['nextPageToken'] ?? null;
    $prevPage = $data['prevPageToken'] ?? null;
}
?>
<!doctype html>
<html lang="el">
<head>
<meta charset="utf-8">
<title>Αναζήτηση YouTube</title>
</head>
<body>
<h1>Αναζήτηση YouTube</h1>
<a href="protected.php">⬅ Επιστροφή</a> | <a href="my_lists.php">📋 Οι λίστες μου</a>
<hr>

<form method="get">
    <input type="text" name="query" value="<?php echo isset($_GET['query']) ? htmlspecialchars($_GET['query']) : ''; ?>" placeholder="Αναζήτηση..." required>
    <button type="submit">Αναζήτηση</button>
</form>

<hr>

<?php if (!empty($searchResults)): ?>
    <?php foreach ($searchResults as $video): ?>
        <div style="margin-bottom:20px;">
            <img src="<?php echo htmlspecialchars($video['thumbnail']); ?>" alt="thumbnail">
            <h3><?php echo htmlspecialchars($video['title']); ?></h3>
            <p><?php echo nl2br(htmlspecialchars($video['description'])); ?></p>
            <a href="https://www.youtube.com/watch?v=<?php echo $video['videoId']; ?>" target="_blank">▶ Δες το στο YouTube</a>
            <br><br>
            <?php if (!empty($user_lists)): ?>
                <form method="post" action="add_youtube_to_list.php">
                    <input type="hidden" name="title" value="<?php echo htmlspecialchars($video['title'], ENT_QUOTES); ?>">
                    <input type="hidden" name="desc" value="<?php echo htmlspecialchars($video['description'], ENT_QUOTES); ?>">
                    <input type="hidden" name="youtube_id" value="<?php echo htmlspecialchars($video['videoId']); ?>">
                    <label>Επιλέξτε λίστα:</label>
                    <select name="list_id" required>
                        <?php foreach ($user_lists as $list): ?>
                            <option value="<?php echo $list['id']; ?>"><?php echo htmlspecialchars($list['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit">➕ Προσθήκη σε λίστα</button>
                </form>
            <?php else: ?>
                <p><em>Δεν έχεις λίστες. Δημιούργησε μία πρώτα.</em></p>
            <?php endif; ?>
        </div>
        <hr>
    <?php endforeach; ?>

    <!-- Σελιδοποίηση -->
    <div style="margin-top:20px;">
        <?php if ($prevPage): ?>
            <a href="?query=<?php echo urlencode($_GET['query']); ?>&page=<?php echo $prevPage; ?>">⬅ Προηγούμενα</a>
        <?php endif; ?>

        <?php if ($nextPage): ?>
            <a href="?query=<?php echo urlencode($_GET['query']); ?>&page=<?php echo $nextPage; ?>">Επόμενα ➡</a>
        <?php endif; ?>
    </div>

<?php elseif (isset($_GET['query'])): ?>
    <p>Δεν βρέθηκαν αποτελέσματα.</p>
<?php endif; ?>

</body>
</html>



