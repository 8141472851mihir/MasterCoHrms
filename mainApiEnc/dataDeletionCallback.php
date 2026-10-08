<?php
// used on meta app
header('Content-Type: application/json; charset=utf-8');

include_once '../apAdmin/lib/dao.php';
include_once '../apAdmin/lib/model.php';

$d = new dao();
$m = new model();
$secret = "c03505abac6684c09bb205074493a23f";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(array(
        'status' => '201',
        'message' => 'Invalid request method'
    ));
    exit();
}

$signed_request = isset($_POST['signed_request']) ? trim($_POST['signed_request']) : '';

if ($signed_request === '') {
    http_response_code(400);
    echo json_encode(array(
        'status' => '201',
        'message' => 'signed_request is required'
    ));
    exit();
}

$data = parse_signed_request($signed_request);
if ($data === null || !isset($data['user_id']) || $data['user_id'] === '') {
    http_response_code(400);
    echo json_encode(array(
        'status' => '201',
        'message' => 'Invalid signed_request'
    ));
    exit();
}

$user_id = (string)$data['user_id'];
$algorithm = isset($data['algorithm']) ? (string)$data['algorithm'] : '';
$expires = isset($data['expires']) ? (string)$data['expires'] : '';
$issued_at = isset($data['issued_at']) ? (string)$data['issued_at'] : '';

$confirmation_code = bin2hex(random_bytes(12));
$status_url = $m->base_url().'crmInquiryDeletion?id=' . $confirmation_code;
$selectQry = $d->selectRow("id", "crm_inquiry_deletion_requests", "user_id = '$user_id'");
if(mysqli_num_rows($selectQry) > 0){
    echo json_encode(array(
        'status' => '201',
        'message' => 'User already has a deletion request'
    ));
    exit();
}
$row = array(
    'user_id' => $user_id,
    'algorithm' => $algorithm,
    'expires_at_unix' => $expires,
    'issued_at_unix' => $issued_at,
    'signed_request' => $signed_request,
    'raw_payload_json' => json_encode($data, JSON_UNESCAPED_SLASHES),
    'status_url' => $status_url,
    'confirmation_code' => $confirmation_code
);

$d->insert('crm_inquiry_deletion_requests', $row);

echo json_encode(array(
    'url' => $status_url,
    'confirmation_code' => $confirmation_code
));
exit();

function parse_signed_request($signed_request)
{
    global $secret;

    $parts = explode('.', $signed_request, 2);
    if (count($parts) !== 2) {
        error_log('Bad Signed JSON format!');
        return null;
    }

    list($encoded_sig, $payload) = $parts;

    $sig = base64_url_decode($encoded_sig);
    $data = json_decode(base64_url_decode($payload), true);

    if (!is_array($data)) {
        error_log('Invalid Signed JSON payload!');
        return null;
    }

    $expected_sig = hash_hmac('sha256', $payload, $secret, true);
    if (!hash_equals($expected_sig, $sig)) {
        error_log('Bad Signed JSON signature!');
        return null;
    }

    return $data;
}

function base64_url_decode($input)
{
    $remainder = strlen($input) % 4;
    if ($remainder) {
        $input .= str_repeat('=', 4 - $remainder);
    }

    return base64_decode(strtr($input, '-_', '+/'));
}
