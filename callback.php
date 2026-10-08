<?php
/**
 * Google OAuth callback — master portal email + company HR email.
 *
 * Master email: state = return URL (plain string)
 * Company HR:   state = HMAC-signed payload (society_id, company_api, OAuth creds, …)
 */

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/apAdmin/vendor/autoload.php';
require_once __DIR__ . '/apAdmin/lib/dao.php';
require_once __DIR__ . '/apAdmin/lib/model.php';

use Google\Client;
use Google\Service\Gmail;

define('GMAIL_HR_OAUTH_STATE_SECRET', (string) EnvLoader::get('GMAIL_HR_OAUTH_STATE_SECRET'));

function gmailHrOAuthBase64UrlDecode($data)
{
    $remainder = strlen($data) % 4;
    if ($remainder) {
        $data .= str_repeat('=', 4 - $remainder);
    }
    return base64_decode(strtr($data, '-_', '+/'));
}

function parseGmailHrOAuthState($secret, $state)
{
    $secret = trim((string)$secret);
    if ($secret === '' || $state === '') {
        return null;
    }

    $parts = explode('.', (string)$state, 2);
    if (count($parts) !== 2) {
        return null;
    }

    $json = gmailHrOAuthBase64UrlDecode($parts[0]);
    $sig = gmailHrOAuthBase64UrlDecode($parts[1]);
    if ($json === false || $sig === false) {
        return null;
    }

    $expectedSig = hash_hmac('sha256', $json, $secret, true);
    if (!hash_equals($expectedSig, $sig)) {
        return null;
    }

    $payload = json_decode($json, true);
    if (!is_array($payload) || empty($payload['exp']) || (int)$payload['exp'] < time()) {
        return null;
    }

    return $payload;
}

function exchangeGmailHrAuthCode(array $config, $code)
{
    $clientId = trim((string)($config['client_id'] ?? ''));
    $clientSecret = trim((string)($config['client_secret'] ?? ''));
    $redirectUrl = trim((string)($config['redirect_url'] ?? ''));

    if ($clientId === '' || $clientSecret === '' || $redirectUrl === '' || $code === '') {
        return null;
    }

    $client = new Client();
    $client->setClientId($clientId);
    $client->setClientSecret($clientSecret);
    $client->setRedirectUri($redirectUrl);
    $client->setAccessType('offline');
    $client->setPrompt('consent');
    $client->addScope(Gmail::GMAIL_SEND);

    $token = $client->fetchAccessTokenWithAuthCode($code);
    if (isset($token['error'])) {
        return null;
    }

    return $token;
}

function gmailHrOAuthRedirect($returnUrl, $status, $message = '')
{
    $separator = (strpos($returnUrl, '?') !== false) ? '&' : '?';
    $location = $returnUrl . $separator . 'oauth=' . rawurlencode($status);
    if ($message !== '') {
        $location .= '&oauth_msg=' . rawurlencode($message);
    }
    header('Location: ' . $location);
    exit;
}

function resolveCompanyApiKey($d, $m, $societyId, $companyApi)
{
    $societyId = (int)$societyId;
    if ($societyId > 0) {
        $qr = $d->selectRow('api_key, sub_domain', 'society_master', "society_id='$societyId'", 'LIMIT 1');
        $row = mysqli_fetch_array($qr);
        if ($row && !empty($row['api_key'])) {
            $subDomain = rtrim((string)($row['sub_domain'] ?? ''), '/');
            $encryptedSuffix = 'masterApi/hrEmailOAuthController.php';
            $legacySuffix = 'apAdmin/controller/hrEmailOAuthController.php';
            if ($subDomain !== '' && strpos($companyApi, $subDomain) === 0
                && (strpos($companyApi, $encryptedSuffix) !== false || strpos($companyApi, $legacySuffix) !== false)) {
                return trim((string)$row['api_key']);
            }
        }
    }

    return trim((string)$m->api_key());
}

function resolveCompanyBaseUrl($d, $societyId, $companyApi)
{
    $societyId = (int)$societyId;
    if ($societyId > 0) {
        $qr1 = $d->selectRow('sub_domain', 'society_master', "society_id='$societyId'", 'LIMIT 1');
        $row = mysqli_fetch_array($qr1);
        if ($row && !empty($row['sub_domain'])) {
            return rtrim((string)$row['sub_domain'], '/') . '/';
        }
    }

    $legacySuffix = 'apAdmin/controller/hrEmailOAuthController.php';
    $encryptedSuffix = 'masterApi/hrEmailOAuthController.php';
    foreach (array($legacySuffix, $encryptedSuffix) as $suffix) {
        $pos = strpos($companyApi, $suffix);
        if ($pos !== false) {
            return substr($companyApi, 0, $pos);
        }
    }

    return '';
}

function handleCompanyHrGmailOAuth($d, $m)
{
    $error = isset($_GET['error']) ? trim($_GET['error']) : '';
    $stateRaw = isset($_GET['state']) ? trim($_GET['state']) : '';
    $code = isset($_GET['code']) ? trim($_GET['code']) : '';

    $statePayload = parseGmailHrOAuthState(GMAIL_HR_OAUTH_STATE_SECRET, $stateRaw);
    if (!is_array($statePayload) || empty($statePayload['company_api'])) {
        return false;
    }

    $fallbackReturn = rtrim($m->base_url(), '/') . '/apAdmin/emailConfigration';
    $returnUrl = !empty($statePayload['return_url'])
        ? $statePayload['return_url']
        : $fallbackReturn;

    if ($error !== '') {
        gmailHrOAuthRedirect($returnUrl, 'error', $error);
    }

    if ($code === '') {
        gmailHrOAuthRedirect($returnUrl, 'error', 'Invalid OAuth state');
    }

    $oauthConfig = array(
        'client_id' => trim((string)($statePayload['client_id'] ?? '')),
        'client_secret' => trim((string)($statePayload['client_secret'] ?? '')),
        'redirect_url' => trim((string)($statePayload['redirect_url'] ?? '')),
    );

    if ($oauthConfig['client_id'] === '' || $oauthConfig['client_secret'] === '' || $oauthConfig['redirect_url'] === '') {
        gmailHrOAuthRedirect($returnUrl, 'error', 'Missing OAuth credentials in state');
    }

    $token = exchangeGmailHrAuthCode($oauthConfig, $code);
    if ($token === null || empty($token['refresh_token'])) {
        gmailHrOAuthRedirect($returnUrl, 'error', 'No refresh token received. Please reconnect and approve all permissions.');
    }

    $googleEmail = '';
    if (!empty($token['id_token'])) {
        $parts = explode('.', $token['id_token']);
        if (count($parts) >= 2) {
            $claims = json_decode(gmailHrOAuthBase64UrlDecode($parts[1]), true);
            if (is_array($claims) && !empty($claims['email'])) {
                $googleEmail = $claims['email'];
            }
        }
    }

    $companyApi = $statePayload['company_api'];
    $societyId = (int)($statePayload['society_id'] ?? 0);
    $apiKey = resolveCompanyApiKey($d, $m, $societyId, $companyApi);

    $payload = array(
        'saveHrGmailOAuthConfig' => 'saveHrGmailOAuthConfig',
        'society_id' => $societyId,
        'sender_name' => $statePayload['sender_name'] ?? '',
        'sender_email_id' => $statePayload['sender_email'] ?? '',
        'refresh_token' => $token['refresh_token'],
        'redirect_url' => $oauthConfig['redirect_url'],
        'google_email' => $googleEmail,
    );

    $companyBaseUrl = resolveCompanyBaseUrl($d, $societyId, $companyApi);
    $decoded = $d->callCompanyApiEnc($companyBaseUrl, 'hrEmailOAuthController.php', $payload, $apiKey);
    $postResult = array(
        'ok' => is_array($decoded) && ($decoded['status'] ?? '') === '200',
        'message' => is_array($decoded) ? ($decoded['message'] ?? 'Company API rejected request') : 'Company API request failed',
    );


    if (!$postResult['ok']) {
        gmailHrOAuthRedirect($returnUrl, 'error', $postResult['message']);
    }

    gmailHrOAuthRedirect($returnUrl, 'connected');
}

$d = new dao();
$m = new model();

handleCompanyHrGmailOAuth($d, $m);

$getData = $d->select("email_configuration", "", "");
$data = mysqli_fetch_array($getData);

if ($data) {
    $client_id = $data['client_id'];
    $client_secret = $data['client_secret'];
    $redirect_url = $data['redirect_url'];
} else {
    $_SESSION['msg1'] = "Google OAuth credentials not found in the database.";
    header("Location: /apAdmin/emailConfigration");
    exit();
}

if (isset($_GET['code']) && isset($_GET['state'])) {
    try {
        $client = new Google\Client();
        $client->setClientId($client_id);
        $client->setClientSecret($client_secret);
        $client->setRedirectUri($redirect_url);
        $client->setAccessType("offline");
        $client->setPrompt("consent");

        $accessToken = $client->fetchAccessTokenWithAuthCode($_GET['code']);

        if (isset($accessToken['error'])) {
            $_SESSION['msg1'] = "Error fetching access token: " . $accessToken['error_description'];
        } elseif (isset($accessToken['refresh_token'])) {
            $refresh_token = $accessToken['refresh_token'];
            $_SESSION['msg'] = "Mail Updated Successfully";
            $update_data = ['refresh_token' => $refresh_token];
            $d->update("email_configuration", $update_data, "");
        } else {
            $_SESSION['msg1'] = "No refresh token returned. Try revoking access and logging in again.";
        }

        header("Location: " . $_GET['state'] . "?tab=google");
        exit();
    } catch (Exception $e) {
        $_SESSION['msg1'] = "OAuth error: " . $e->getMessage();
        header("Location: " . $_GET['state'] . "?tab=google");
        exit();
    }
} else {
    $_SESSION['msg1'] = "Missing authorization code or state.";
    header("Location: /apAdmin/emailConfigration?tab=google");
    exit();
}
