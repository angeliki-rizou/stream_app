<?php
session_start();

$client_id = '372054234011-g3tnr27jself4q3plfldc3hfmnrlq93k.apps.googleusercontent.com';
$redirect_uri = 'http://localhost:8080/youtube_callback.php';
$scope = 'https://www.googleapis.com/auth/youtube.readonly';

$params = [
    'client_id' => $client_id,
    'redirect_uri' => $redirect_uri,
    'response_type' => 'code',
    'scope' => $scope,
    'access_type' => 'offline',
    'prompt' => 'consent'
];

header('Location: https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params));
exit;
