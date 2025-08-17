<?php
session_start();

$client_id = '372054234011-g3tnr27jself4q3plfldc3hfmnrlq93k.apps.googleusercontent.com';
$client_secret = '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';
$redirect_uri = 'http://localhost:8080/youtube_callback.php';

if (!isset($_GET['code'])) {
    die('No code parameter returned');
}

$code = $_GET['code'];

// Ανταλλαγή code για access token
$token_url = 'https://oauth2.googleapis.com/token';
$post_data = [
    'code' => $code,
    'client_id' => $client_id,
    'client_secret' => $client_secret,
    'redirect_uri' => $redirect_uri,
    'grant_type' => 'authorization_code'
];

$ch = curl_init($token_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

if (!isset($data['access_token'])) {
    die('Error retrieving access token: ' . $response);
}

$_SESSION['youtube_access_token'] = $data['access_token'];

echo "✅ Συνδεθήκατε με επιτυχία στο YouTube! <a href='youtube_search.php'>Αναζήτηση</a>";
