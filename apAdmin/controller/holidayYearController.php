<?php
include '../common/objectController.php';
if (isset($_POST) && !empty($_POST)) {
    extract($_POST);
    if (isset($_POST['festivalYearNameCheck'])) {
        $festival_name_check = $d->escapeSqlString(trim($_POST['festival_year_name_check'] ?? ''));
        $holiday_date_check = trim($_POST['holiday_year_date_check'] ?? '');
        $country_id = $d->sanitizeActionIdAsInt($_POST['country_id'] ?? 0);

        if (!empty($festival_name_check) && !empty($holiday_date_check)) {
            $year = (int)date('Y', strtotime($holiday_date_check));
            $where = "festival_name = '$festival_name_check' AND YEAR(holiday_date) = '$year'";
            if ($country_id > 0) {
                $where .= " AND country_id = '$country_id'";
            }

            $existing_name = $d->select("holidays_master", $where);

            echo json_encode([
                'valid' => mysqli_num_rows($existing_name) === 0,
            ]);
        }
        exit;
    } elseif (isset($_POST['festivalYearDateCheck'])) {
        $holiday_date_check = $d->escapeSqlString(trim($_POST['holiday_year_date_check'] ?? ''));
        $country_id = $d->sanitizeActionIdAsInt($_POST['country_id'] ?? 0);

        $where = "holiday_date = '$holiday_date_check'";
        if ($country_id > 0) {
            $where .= " AND country_id = '$country_id'";
        }

        $existing_date = $d->select("holidays_master", $where);
        echo json_encode(['valid' => mysqli_num_rows($existing_date) === 0]);
        exit;
    } else if (isset($_POST['copyMultipleHolidays']) && isset($_POST['selected_rows']) && is_array($_POST['selected_rows'])) {
        $selectedRows = $_POST['selected_rows'];
        $holidayIds = $_POST['holiday_id'];
        $holidayDates = $_POST['holiday_date'];
        $festivalNames = $_POST['festival_name'];
        $holidayDescs = $_POST['holiday_desc'];
        $country_id = isset($_POST['country_id']) ? test_input($_POST['country_id']) : '101';

        $inserted = 0;

        foreach ($selectedRows as $index) {
            $image = '';
            $dirPath = "../../img/master/holiday/";

            if (!empty($_POST['festival_image'][$index])) {
                $originalImage = basename($_POST['festival_image'][$index]);
                $sourcePath = $dirPath . $originalImage;

                $extId = pathinfo($originalImage, PATHINFO_EXTENSION);
                $acceptable = ["jpeg", "jpg", "png"];

                if (in_array(strtolower($extId), $acceptable) && file_exists($sourcePath)) {
                    $user_name = str_replace(' ', '_', $festivalNames[$index]);
                    $image = $user_name . '_' . round(microtime(true)) . '.' . $extId;
                    $destinationPath = $dirPath . $image;
                    copy($sourcePath, $destinationPath);
                }
            }

            $m->set_data("festival_name", test_input($festivalNames[$index]));
            $m->set_data("country_id", $country_id);
            $m->set_data("holiday_year", date("Y", strtotime($holidayDates[$index])));
            $m->set_data("holiday_date", date("Y-m-d", strtotime($holidayDates[$index])));
            $m->set_data("holiday_desc", test_input($holidayDescs[$index]));
            $m->set_data("festival_image", test_input($image));

            $add_data = [
                'country_id' => $m->get_data('country_id'),
                'festival_name' => $m->get_data('festival_name'),
                'holiday_year' => $m->get_data('holiday_year'),
                'holiday_date' => $m->get_data('holiday_date'),
                'holiday_desc' => $m->get_data('holiday_desc'),
                'festival_image' => $m->get_data('festival_image'),
                'added_by' => $bms_admin_id,
                'added_date' => date("Y-m-d H:i:s")
            ];
            $d->insert("holidays_master", $add_data);
            $inserted++;
        }
        echo "1";
        exit;
    } else {
        echo "No holidays selected or invalid request.";
        exit;
    }
} else {
    $_SESSION['msg1'] = "Invalid Request Method";
    header("location:../holidays");
    exit;
}
