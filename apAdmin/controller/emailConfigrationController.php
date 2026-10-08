<?php 
include '../common/objectController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Update SMTP Configuration
    if (isset($_POST['updateSMTPConfig'])) {
        // Validation
        if (empty(trim($_POST['smtp_sender_email_id'])) || !filter_var(trim($_POST['smtp_sender_email_id']), FILTER_VALIDATE_EMAIL)) {
            $_SESSION['msg1'] = "Invalid or missing sender email ID.";
            header('Location: ../emailConfigration?tab=smtp');
            exit;
        }
        if (empty(trim($_POST['smtp_email_password']))) {
            $_SESSION['msg1'] = "Email password is required.";
            header('Location: ../emailConfigration?tab=smtp');
            exit;
        }
        if (empty(trim($_POST['smtp_email_smtp']))) {
            $_SESSION['msg1'] = "SMTP server is required.";
            header('Location: ../emailConfigration?tab=smtp');
            exit;
        }
        if (empty(trim($_POST['smtp_email_port'])) || !is_numeric(trim($_POST['smtp_email_port']))) {
            $_SESSION['msg1'] = "Invalid or missing email port.";
            header('Location: ../emailConfigration?tab=smtp');
            exit;
        }
        if (empty(trim($_POST['smtp_sender_name']))) {
            $_SESSION['msg1'] = "Sender name is required.";
            header('Location: ../emailConfigration?tab=smtp');
            exit;
        }

        $m->set_data('sender_email_id', trim($_POST['smtp_sender_email_id']));
        $m->set_data('email_password', trim($_POST['smtp_email_password']));
        $m->set_data('email_smtp', trim($_POST['smtp_email_smtp']));
        $m->set_data('smtp_type', trim($_POST['smtp_smtp_type']));
        $m->set_data('email_port', trim($_POST['smtp_email_port']));
        $m->set_data('sender_name', trim($_POST['smtp_sender_name']));

        $smtpData = array(
            'sender_email_id' => $m->get_data('sender_email_id'),
            'email_password'   => $m->get_data('email_password'),
            'email_smtp'       => $m->get_data('email_smtp'),
            'smtp_type'        => $m->get_data('smtp_type'),
            'email_port'       => $m->get_data('email_port'),
            'sender_name'      => $m->get_data('sender_name')
        );

        $updateSMTP = $d->update("email_configuration", $smtpData, "");

        if ($updateSMTP) {
            $_SESSION['msg'] = "SMTP Configuration Updated Successfully";
        } else {
            $_SESSION['msg1'] = "SMTP Configuration Update Failed";
        }

        header('Location: ../emailConfigration?tab=smtp'); // Redirect to SMTP tab
        exit;
    }

    // Update Google Login Configuration
    if (isset($_POST['updateGoogleConfig'])) {
        // Validation
        if (empty(trim($_POST['google_sender_email_id'])) || !filter_var(trim($_POST['google_sender_email_id']), FILTER_VALIDATE_EMAIL)) {
            $_SESSION['msg1'] = "Invalid or missing sender email ID.";
            header('Location: ../emailConfigration?tab=google');
            exit;
        }
        if (empty(trim($_POST['google_sender_name']))) {
            $_SESSION['msg1'] = "Sender name is required.";
            header('Location: ../emailConfigration?tab=google');
            exit;
        }
        if (empty(trim($_POST['client_id']))) {
            $_SESSION['msg1'] = "Client ID is required.";
            header('Location: ../emailConfigration?tab=google');
            exit;
        }
        if (empty(trim($_POST['client_secret_key']))) {
            $_SESSION['msg1'] = "Client secret key is required.";
            header('Location: ../emailConfigration?tab=google');
            exit;
        }
        if (empty(trim($_POST['redirect_url'])) || !filter_var(trim($_POST['redirect_url']), FILTER_VALIDATE_URL)) {
            $_SESSION['msg1'] = "Invalid or missing redirect URL.";
            header('Location: ../emailConfigration?tab=google');
            exit;
        }

        // Fetch old values from DB
        $existing = $d->select("email_configuration", "1", "LIMIT 1");
        $old = mysqli_fetch_array($existing);

        $new_client_id = trim($_POST['client_id']);
        $new_client_secret = trim($_POST['client_secret_key']);
        $new_redirect_url = trim($_POST['redirect_url']);

        $m->set_data('sender_email_id', trim($_POST['google_sender_email_id']));
        $m->set_data('sender_name', trim($_POST['google_sender_name']));
        $m->set_data('client_id', $new_client_id);
        $m->set_data('client_secret', $new_client_secret);
        $m->set_data('redirect_url', $new_redirect_url);

        // Prepare update array
        $googleData = array(
            'sender_email_id' => $m->get_data('sender_email_id'),
            'sender_name'     => $m->get_data('sender_name'),
            'client_id'       => $m->get_data('client_id'),
            'client_secret'   => $m->get_data('client_secret'),
            'redirect_url'    => $m->get_data('redirect_url')
        );

        // Check if config changed
        if (
            $new_client_id !== $old['client_id'] ||
            $new_client_secret !== $old['client_secret'] ||
            $new_redirect_url !== $old['redirect_url']
        ) {
            $googleData['refresh_token'] = ''; // Clear refresh token
        }

        $updateGoogle = $d->update("email_configuration", $googleData, "");

        if ($updateGoogle) {
            setcookie('google_login_ready', 'true', time() + 3600, '/');
            $_SESSION['msg'] = "Google Login Config Updated Successfully";
        } else {
            unset($_COOKIE['google_login_ready']);
            $_SESSION['msg1'] = "Google Login Config Update Failed";
        }

        header('Location: ../emailConfigration?tab=google'); // Redirect to Google Login tab
        exit;
    }

    // Revoke Google Access 
    if (isset($_POST['revokeAccess'])) {
        $update = $d->update("email_configuration", ["refresh_token" => ""],"");
        if ($update) {
            $_SESSION['msg'] = "Google access revoked successfully.";
        } else {
            $_SESSION['msg1'] = "Failed to revoke Google access.";
        }
        header('Location: ../emailConfigration?tab=google'); // Redirect to Google Login tab
        exit();
    }

}
