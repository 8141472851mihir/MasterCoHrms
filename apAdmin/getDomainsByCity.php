<?php 

include_once 'common/object.php';
error_reporting(0);
extract(array_map("test_input" , $_POST));
?>
<option value="">-- Select Domain --</option>

<?php
$recommendedDomainName = '';
if (isset($city_id) && $city_id != '') {
    $conn = $d->dbCon();
    $cityIdEsc = mysqli_real_escape_string($conn, $city_id);
    $qRec = $d->selectRow(
        "domain_master.domain_name",
        "cities LEFT JOIN domain_master ON domain_master.domain_id = cities.domain_id",
        "cities.city_id='".$cityIdEsc."'"
    );
    if ($qRec && mysqli_num_rows($qRec) > 0) {
        $recRow = mysqli_fetch_array($qRec);
        if (!empty($recRow['domain_name'])) {
            $recommendedDomainName = $recRow['domain_name'];
        }
    }
}

$q = $d->select("domain_master", "domain_active_status=0", "ORDER BY domain_name ASC");
while($data = mysqli_fetch_array($q)) {
    $dn = $data['domain_name'];
    $selected = ($recommendedDomainName !== '' && $dn === $recommendedDomainName) ? ' selected' : '';
    $label = htmlspecialchars($dn);
    if ($selected !== '') { $label .= ' (Recommended)'; }
    echo '<option value="'.htmlspecialchars($dn, ENT_QUOTES).'"'.$selected.'>'.$label.'</option>';
}
?>
