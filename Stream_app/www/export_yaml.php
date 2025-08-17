<?php
// Δεν χρειάζεται login γιατί είναι open data
header('Content-Type: application/x-yaml; charset=utf-8');
header('Content-Disposition: attachment; filename="stream_app_export.yaml"');

$DB_HOST = getenv('DB_HOST') ?: 'db';
$DB_NAME = getenv('DB_NAME') ?: 'stream_app';
$DB_USER = getenv('DB_USER') ?: 'appuser';
$DB_PASS = getenv('DB_PASS') ?: 'apppass';

try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Παίρνουμε όλες τις λίστες με τα περιεχόμενα και τους χρήστες
    $stmt = $pdo->query("
        SELECT w.id AS list_id, w.name AS watchlist_name, w.created_at AS watchlist_date,
               u.id AS user_id, u.username,
               c.id AS content_id, c.title AS content_title, c.youtube_id, c.created_at AS content_date
        FROM watchlists w
        JOIN users u ON w.user_id = u.id
        LEFT JOIN list_contents wc ON w.id = wc.list_id
        LEFT JOIN contents c ON wc.content_id = c.id
        ORDER BY w.id, c.id
    ");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $data = [];
    foreach ($rows as $row) {
        $user_hash = hash('sha256', $row['user_id'] . $row['username']); // μοναδικό αναγνωριστικό

        if (!isset($data[$row['list_id']])) {
            $data[$row['list_id']] = [
                'list_id'   => $row['list_id'],
                'watchlist_name' => $row['watchlist_name'],
                'watchlist_date' => $row['watchlist_date'],
                'user_hash'      => $user_hash,
                'contents'       => []
            ];
        }

        if ($row['content_id']) {
            $data[$row['list_id']]['contents'][] = [
                'content_id'    => $row['content_id'],
                'title'         => $row['content_title'],
                'youtube_id'    => $row['youtube_id'],
                'created_at'    => $row['content_date']
            ];
        }
    }

    // Μετατροπή σε YAML
    if (function_exists('yaml_emit')) {
        echo yaml_emit(array_values($data), YAML_UTF8_ENCODING);
    } else {
        // fallback σε απλό export αν δεν υπάρχει extension
        echo "---\n";
        foreach ($data as $list) {
            echo "- list_id: {$list['list_id']}\n";
            echo "  watchlist_name: \"{$list['watchlist_name']}\"\n";
            echo "  watchlist_date: {$list['watchlist_date']}\n";
            echo "  user_hash: {$list['user_hash']}\n";
            echo "  contents:\n";
            foreach ($list['contents'] as $c) {
                echo "    - content_id: {$c['content_id']}\n";
                echo "      title: \"{$c['title']}\"\n";
                echo "      youtube_id: {$c['youtube_id']}\n";
                echo "      created_at: {$c['created_at']}\n";
            }
        }
    }

} catch (PDOException $e) {
    die("Σφάλμα: " . $e->getMessage());
}
