<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(["error" => "Not authorized"]);
    exit;
}

header("Content-Type: application/json; charset=utf-8");

$q = $_GET['q'] ?? '';
if (!$q) {
    echo json_encode([]);
    exit;
}

$API_KEY = "AIzaSyACEeNAnLjJ8opYP5YLrEu4Xh-4_JCLfKE";
$url = "https://www.googleapis.com/youtube/v3/search?part=snippet&type=video&maxResults=5&q=" . urlencode($q) . "&key=" . $API_KEY;

$response = file_get_contents($url);
$data = json_decode($response, true);

$results = [];
if (!empty($data['items'])) {
    foreach ($data['items'] as $item) {
        $results[] = [
            "youtube_id" => $item['id']['videoId'],
            "title" => $item['snippet']['title'],
            "description" => $item['snippet']['description']
        ];
    }
}

echo json_encode($results);
