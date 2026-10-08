<?php 

include_once 'common/object.php';
error_reporting(0);
extract(array_map("test_input" , $_POST));
?>
<option value="">-- Select Domain --</option>

<?php
$q = $d->select("domain_master", "domain_active_status=0", "ORDER BY domain_name ASC");
while($data = mysqli_fetch_array($q)) {
    echo '<option value="'.htmlspecialchars($data['domain_name'], ENT_QUOTES).'">'.htmlspecialchars($data['domain_name']).'</option>';
}
?>
