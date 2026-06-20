<?php
require 'vendor/autoload.php';

$client = new Google_Client();
$client->setClientId("YOUR_GOOGLE_CLIENT_ID");
$client->setClientSecret("YOUR_GOOGLE_CLIENT_SECRET");
$client->setRedirectUri("http://localhost/google-callback.php");

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token);

    $oauth = new Google_Service_Oauth2($client);
    $userInfo = $oauth->userinfo->get();

    echo "Welcome " . $userInfo->name . " (" . $userInfo->email . ")";
}
