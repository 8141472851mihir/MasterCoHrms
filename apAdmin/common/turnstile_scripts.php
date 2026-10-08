<?php
if (!isset($turnstile)) {
    $turnstile = isset($d) ? $d->turnstile_site() : ['site_key' => false, 'secret_key' => false];
}
if (!empty($turnstile['site_key'])) { ?>
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
<script>
function turnstileCompleted() {
  var el = document.querySelector('input[name="cf-turnstile-response"]');
  return !el || (el.value && el.value.length > 0);
}
function requireTurnstile(message) {
  if (!turnstileCompleted()) {
    var msg = message || "Please complete the security check.";
    if (typeof swal !== 'undefined') {
      swal(msg, { icon: "error" });
    } else {
      alert(msg);
    }
    return false;
  }
  return true;
}
function resetTurnstileWidget() {
  if (typeof turnstile !== 'undefined') {
    turnstile.reset();
  }
}
</script>
<?php } ?>
