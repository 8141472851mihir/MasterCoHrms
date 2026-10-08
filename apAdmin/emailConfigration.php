<?php
  $getData = $d->select("email_configuration", "", "");
  $data = mysqli_fetch_array($getData);

  $smtpTabActive = 'active';
  $smtpPaneActive = 'show active';
  $googleTabActive = '';
  $googlePaneActive = '';

  if (isset($_GET['tab']) && $_GET['tab'] === 'google') {
      $smtpTabActive = '';
      $smtpPaneActive = '';
      $googleTabActive = 'active';
      $googlePaneActive = 'show active';
  }
?>



<?php
  $getData = $d->select("email_configuration", "", "");
  $data = mysqli_fetch_array($getData);
?>

<div class="content-wrapper">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">

            <h4 class="form-header text-uppercase">
              <i class="fa fa-envelope"></i> Sender Email Configuration
            </h4>

            <ul class="nav nav-tabs nav-tabs-primary top-icon nav-justified">
              <li class="nav-item">
              <a href="#smtp" data-toggle="tab" class="nav-link <?= $smtpTabActive ?>">
                  <i class="fa fa-android"></i> SMTP
                </a>
              </li>
              <li class="nav-item">
              <a href="#google_login" data-toggle="tab" class="nav-link <?= $googleTabActive ?>">
                  <i class="fa fa-user"></i> Google Login
                </a>
              </li>
            </ul>



            <div class="tab-content mt-3">
            <div class="tab-pane fade <?= $smtpPaneActive ?>" id="smtp">
                <form action="controller/emailConfigrationController.php" method="POST">
                  <input type="hidden" name="configuration_id" value="<?= $data['configuration_id']; ?>">

                  <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Sender Email ID<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                      <input type="email" name="smtp_sender_email_id" class="form-control" value="<?= $data['sender_email_id'] ?>" required>
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Password<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                      <input type="password" name="smtp_email_password" class="form-control" value="<?= $data['email_password'] ?>" required>
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-2 col-form-label">SMTP<span class="text-danger">*</span></label>
                    <div class="col-sm-4">
                      <input type="text" name="smtp_email_smtp" class="form-control" value="<?= $data['email_smtp'] ?>" required>
                    </div>
                    <label class="col-sm-2 col-form-label">SMTP Type<span class="text-danger">*</span></label>
                    <div class="col-sm-4">
                      <input type="text" name="smtp_smtp_type" class="form-control" value="<?= $data['smtp_type'] ?>" required>
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Email Port<span class="text-danger">*</span></label>
                    <div class="col-sm-4">
                      <input type="text" name="smtp_email_port" class="form-control" value="<?= $data['email_port'] ?>" required>
                    </div>
                    <label class="col-sm-2 col-form-label">Sender Name</label>
                    <div class="col-sm-4">
                      <input type="text" name="smtp_sender_name" class="form-control" value="<?= $data['sender_name'] ?>" required>
                    </div>
                  </div>

                  <div class="form-footer text-center">
                    <button type="submit" name="updateSMTPConfig" class="btn btn-success" >
                      <i class="fa fa-check-square-o"></i> Update SMTP
                    </button>
                    <input type="hidden" name="tab" value="smtp">
                  </div>
                </form>
              </div>

              <!-- GOOGLE LOGIN FORM -->
              <div class="tab-pane fade <?= $googlePaneActive ?>" id="google_login">
                <form action="controller/emailConfigrationController.php" method="POST">
                  <input type="hidden" name="configuration_id" value="<?= $data['configuration_id']; ?>">

                  <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Sender Email ID <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                      <input type="email" name="google_sender_email_id" class="form-control" value="<?= $data['sender_email_id'] ?>" required>
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Sender Name<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                      <input type="text" name="google_sender_name" class="form-control" value="<?= $data['sender_name'] ?>" required>
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Client ID<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                      <input type="text" name="client_id" class="form-control" value="<?= $data['client_id'] ?>" required minlength="5">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Client Secret<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                      <input type="text" name="client_secret_key" class="form-control" value="<?= $data['client_secret'] ?>" required minlength="5">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Redirect URL<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                      <input type="text" name="redirect_url" class="form-control" value="<?= $data['redirect_url'] ?>" required minlength="5">
                      <small class="form-text text-muted">
                        Use `<?= htmlspecialchars(rtrim($m->base_url(), '/') . '/callback.php') ?>` as the Google auth redirect URL.
                      </small>
                    </div>
                  </div>
                  <div class="form-footer text-center">
                      <button type="submit" name="updateGoogleConfig" class="btn btn-success" >
                        <i class="fa fa-check-square-o"></i> Update Google Config
                      </button>
                      <?php                      
                        require_once __DIR__ . '/../apAdmin/vendor/autoload.php';
                        // Fetch configuration from the database
                        $googleAuthUrl = "";
                        try {
                            if (!empty($data['client_id']) && !empty($data['client_secret']) && !empty($data['redirect_url'])) {
                                // Initialize the Google client
                                $client = new Google\Client();
                                $client->setApplicationName('test123');
                                $client->setClientId($data['client_id']);
                                $client->setClientSecret($data['client_secret']);
                                $client->setRedirectUri($data['redirect_url']);  // Fetching redirect URI from the database
                                $client->addScope('https://www.googleapis.com/auth/gmail.send');
                                $client->addScope('https://www.googleapis.com/auth/gmail.modify');
                                $client->addScope('https://www.googleapis.com/auth/gmail.compose');
                                $client->setAccessType('offline');
                                $client->setPrompt('consent');
                                $client->setState('http://localhost/masterMyco/apAdmin/emailConfigration');
                                // Generate the Google login URL
                                $googleAuthUrl = $client->createAuthUrl();
                            }
                        } catch (Exception $e) {
                            echo "Error generating Google Auth URL: " . $e->getMessage();
                        }
                        ?>
                        <?php if (!empty($googleAuthUrl) && empty($data['refresh_token'])): ?>
                            <a href="<?= htmlspecialchars($googleAuthUrl) ?>" class="btn btn-success ml-2">Activate</a>
                          <?php else: ?>
                                <form action="controller/emailConfigrationController.php" method="POST" class="d-inline">
                                    <input type="hidden" name="revokeAccess" value="1">
                                    <button type="submit" class="btn btn-danger ml-2">Deactivate</button>
                                </form>
                            <?php endif; ?>
                  </div>
                </form>
              </div>
            </div> <!-- tab-content -->
          </div>
        </div>
      </div>
      </div><!--End Row-->
  </div>
</div>

