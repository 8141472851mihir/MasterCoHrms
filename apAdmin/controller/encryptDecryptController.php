<?php 
include '../common/objectController.php';
if(isset($_POST) && !empty($_POST) )
{
  $compress = (isset($_POST['compress']) && (string) $_POST['compress'] === '1') ? 1 : 0;

  if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['key']) && isset($_POST['encryptData'])) {
    if(isset($useDefaultKey) && $useDefaultKey=='true'){
      $enc_key = $d->get_encrypt_key();
      $enc_iv = $d->get_encrypt_iv();
    }else{
      $enc_key = $_POST['enc_key'];
      $enc_iv = $_POST['enc_iv'];
    }
    $response = [];
    foreach ($_POST['key'] as $index => $k) {
      if($index!="") {
        $response[$k] = $_POST['value'][$index];
      }
    }

    $encrypted_data = $d->manage_encryption('1', $response, $compress, $enc_key, $enc_iv);
    $decrypted_data = $d->manage_decryption('1', $encrypted_data, 0, $enc_key, $enc_iv);
    echo json_encode([
      "encrypted_data" => $encrypted_data,
      "decrypted_data" => json_decode($decrypted_data, true)
    ]);
    exit;
  }
  elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['enc_key']) && isset($_POST['decryptData'])) {
    if(isset($useDefaultKey) && $useDefaultKey=='true'){
      $enc_key = $d->get_encrypt_key();
      $enc_iv = $d->get_encrypt_iv();
    }else{
      $enc_key = $_POST['enc_key'];
      $enc_iv = $_POST['enc_iv'];
    }
    $encrypted_input = $_POST['encrypted_input'];

    $decrypted_data = $d->manage_decryption('1', $encrypted_input, 0, $enc_key, $enc_iv);
    echo json_encode([
      "decrypted_data" => json_decode($decrypted_data, true)
    ]);
    exit;
  }
  elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['textEncryptData'])) {
    // Use default key/iv or manual
    if (isset($_POST['useDefaultKey']) && $_POST['useDefaultKey'] == 'true') {
        $enc_key = $d->get_encrypt_key();
        $enc_iv = $d->get_encrypt_iv();
    } else {
        $enc_key = $_POST['enc_key'];
        $enc_iv = $_POST['enc_iv'];
    }

    $text = isset($_POST['multipleInsert_input']) ? $_POST['multipleInsert_input'] : '';

    $encrypted_data = $d->encryptDecrypt('encrypt', $text, $enc_key, $enc_iv);

    $decrypted_data = $d->encryptDecrypt('decrypt', $encrypted_data, $enc_key, $enc_iv);

    echo json_encode([
        "encrypted_data" => $encrypted_data,
        "decrypted_data" => $decrypted_data
    ]);
    exit;
  }
  elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['textDecryptData'])) {
    if (isset($_POST['useDefaultKey']) && $_POST['useDefaultKey'] == 'true') {
        $enc_key = $d->get_encrypt_key();
        $enc_iv = $d->get_encrypt_iv();
    } else {
        $enc_key = $_POST['enc_key'];
        $enc_iv = $_POST['enc_iv'];
    }

    $encrypted_text = isset($_POST['text_decrypt_input']) ? $_POST['text_decrypt_input'] : '';

    $decrypted_data = $d->encryptDecrypt('decrypt', $encrypted_text, $enc_key, $enc_iv);

    echo json_encode([
        "decrypted_data" => $decrypted_data
    ]);
    exit;
  }
} else{
  header('location:../login');
}
?>