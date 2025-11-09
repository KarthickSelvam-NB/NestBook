<?php
$client_id = 'YOUR_CLIENT_ID';
$redirect_uri = 'http://localhost/book_app/google-callback.php';
$scope = 'email profile';
$response_type = 'code';

$auth_url = "https://accounts.google.com/o/oauth2/v2/auth?" . http_build_query([
    'client_id' => $client_id,
    'redirect_uri' => $redirect_uri,
    'response_type' => $response_type,
    'scope' => $scope,
    'access_type' => 'offline',
    'prompt' => 'select_account'
]);

header('Location: ' . $auth_url);
exit;
?>
