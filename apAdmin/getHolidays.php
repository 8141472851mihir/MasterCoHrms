<?php

include_once 'common/object.php';
error_reporting(0);
extract(array_map("test_input" , $_POST));
?>
<?php
$year = $d->sanitizeReportFilterYear(isset($_POST['year']) ? $_POST['year'] : (date('Y') - 1), date('Y') - 1);
$country_id = isset($_POST['country_id']) ? $d->sanitizeReportFilterIdAsInt($_POST['country_id'], 101) : 101;

$result = $d->select("holidays_master", "YEAR(holiday_date) = '$year' AND country_id = '$country_id'", "ORDER BY holiday_date");

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $row['imagePath'] = "../img/master/holiday/" . $row['festival_image'];
    $row['imageSrc'] = $row['festival_image'] ? $row['imagePath'] : "../img/dummy-image.jpg";
    $data[] = $row;
}

echo json_encode($data);
?>