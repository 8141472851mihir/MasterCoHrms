<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require '../vendor/autoload.php';
include_once '../lib/dao.php';
include_once '../lib/model.php';
$d = new dao();
$m = new model();
use Google\Client;
$q=$d->select("email_configuration","");
$data=mysqli_fetch_array($q);
extract($data);
$clientId = $data['client_id'];
$clientSecret = $data['client_secret'];
$redirectUri = $data['redirect_url'];
// Set up the client
$client = new Client();
$client->setClientId($clientId);   
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope('https://www.googleapis.com/auth/gmail.send');
$client->addScope('https://www.googleapis.com/auth/gmail.modify');
$client->addScope('https://www.googleapis.com/auth/gmail.compose');
$client->setAccessType('offline');
$client->setPrompt('consent');
if (isset($_GET['code'])) {
    $authCode = $_GET['code'];
    $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
    $refreshToken = $accessToken['refresh_token']; 
    echo "Your refresh token: " . $refreshToken;
}else{
	// Generate the authorization URL
	$authUrl = $client->createAuthUrl();
	header('Location: ' . $authUrl);
	exit();
}
