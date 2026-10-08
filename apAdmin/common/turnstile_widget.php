<?php
if (!isset($turnstile)) {
    $turnstile = isset($d) ? $d->turnstile_site() : ['site_key' => false, 'secret_key' => false];
}
if (!empty($turnstile['site_key'])) { ?>
<div class="row mb-2">
  <div class="col-12 d-flex justify-content-center">
    <div class="cf-turnstile" data-sitekey="<?php echo htmlspecialchars($turnstile['site_key'], ENT_QUOTES, 'UTF-8'); ?>"></div>
  </div>
</div>
<?php } ?>
