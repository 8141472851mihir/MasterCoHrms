<?php
include '../common/objectController.php';

if (isset($_POST) && !empty($_POST)) {
    extract($_POST);

    if (isset($_POST['festivalNameCheck'])) {
        $festival_name_check = $d->escapeSqlString(trim($_POST['festival_name_check'] ?? ''));
        $holiday_id = $d->sanitizeActionIdAsInt($_POST['edit_holiday_id'] ?? 0);
        $country_id = $d->sanitizeActionIdAsInt($_POST['country_id'] ?? 0);

        $where = "festival_name = '$festival_name_check'";
        if ($country_id > 0) {
            $where .= " AND country_id = '$country_id'";
        }
        if ($holiday_id > 0) {
            $where .= " AND holiday_id != '$holiday_id'";
        }
        $existing_name = $d->select("holidays_master", $where);
        echo json_encode(['valid' => mysqli_num_rows($existing_name) === 0]);
        exit;
    } elseif (isset($_POST['festivalDateCheck'])) {
        $holiday_date_check = $d->escapeSqlString(trim($_POST['holiday_date_check'] ?? ''));
        $holiday_id = $d->sanitizeActionIdAsInt($_POST['edit_holiday_id'] ?? 0);
        $country_id = $d->sanitizeActionIdAsInt($_POST['country_id'] ?? 0);

        $where = "holiday_date = '$holiday_date_check'";
        if ($country_id > 0) {
            $where .= " AND country_id = '$country_id'";
        }
        if ($holiday_id > 0) {
            $where .= " AND holiday_id != '$holiday_id'";
        }
        $existing_date = $d->select("holidays_master", $where);
        echo json_encode(['valid' => mysqli_num_rows($existing_date) === 0]);
        exit;
    } elseif (isset($_POST['addHolidayData']) && $_POST['addHolidayData'] == "addHolidayData") {
        $file_image = $_FILES["festival_image"]["tmp_name"] ?? '';
        $image = '';
        $dirPath = "../../img/master/holiday/";

        if (file_exists($file_image)) {
            $acceptable = ["jpeg", "jpg", "png"];
            $extId = pathinfo($_FILES['festival_image']['name'], PATHINFO_EXTENSION);
            if (in_array($extId, $acceptable)) {
                $temp = explode(".", $_FILES["festival_image"]["name"]);
                $user_name = str_replace(' ', '_', $festival_name);
                $image = $user_name . '_' . round(microtime(true)) . '.' . end($temp);
                $destinationPath = $dirPath . $image;
                $d->resizeImage($file_image, $destinationPath, 1280, 720, $extId);
            } else {
                $_SESSION['msg1'] = "Invalid Photo";
                header("location:../holidays");
                exit;
            }
        }

        $m->set_data("festival_name", test_input($festival_name));
        $m->set_data("country_id", test_input($country_id));
        $m->set_data("holiday_year", date("Y", strtotime($holiday_date)));
        $m->set_data("holiday_date", date("Y-m-d", strtotime($holiday_date)));
        $m->set_data("holiday_desc", test_input($holiday_desc));
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

        $q = $d->insert("holidays_master", $add_data);

        if ($q > 0) {
            $_SESSION['msg'] = "Holiday Added Successfully.";
            $d->insert_log("0", "$bms_admin_id", "$created_by", "Holiday Added Successfully.");
        } else {
            $_SESSION['msg1'] = "Something Went Wrong?";
        }

        $redirectCountryId = (int)$m->get_data('country_id');
        header("location:../holidays?countryId=$redirectCountryId");
        exit;
    } elseif (isset($_POST['editHolidayData']) && $_POST['editHolidayData'] == "editHolidayData") {
        $file_image = $_FILES["festival_image"]["tmp_name"] ?? '';
        $image = $old_image ?? '';
        $dirPath = "../../img/master/holiday/";

        if (file_exists($file_image)) {
            $acceptable = ["jpeg", "jpg", "png"];
            $extId = pathinfo($_FILES['festival_image']['name'], PATHINFO_EXTENSION);
            if (in_array($extId, $acceptable)) {
                if (!empty($old_image) && file_exists($dirPath . $old_image)) {
                    unlink($dirPath . $old_image);
                }
                $temp = explode(".", $_FILES["festival_image"]["name"]);
                $user_name = str_replace(' ', '_', $festival_name);
                $image = $user_name . '_' . round(microtime(true)) . '.' . end($temp);
                $destinationPath = $dirPath . $image;
                $d->resizeImage($file_image, $destinationPath, 1280, 720, $extId);
            } else {
                $_SESSION['msg1'] = "Invalid Photo";
                header("location:../holidays");
                exit;
            }
        }

        $m->set_data("festival_name", test_input($festival_name));
        $m->set_data("country_id", test_input($country_id));
        $m->set_data("holiday_year", date("Y", strtotime($holiday_date)));
        $m->set_data("holiday_date", date("Y-m-d", strtotime($holiday_date)));
        $m->set_data("holiday_desc", test_input($holiday_desc));
        $m->set_data("festival_image", test_input($image));

        $edit_data = [
            'country_id' => $m->get_data('country_id'),
            'festival_name' => $m->get_data('festival_name'),
            'holiday_year' => $m->get_data('holiday_year'),
            'holiday_date' => $m->get_data('holiday_date'),
            'holiday_desc' => $m->get_data('holiday_desc'),
            'festival_image' => $m->get_data('festival_image'),
            'updated_by' => $bms_admin_id,
            'updated_date' => date("Y-m-d H:i:s")
        ];

        $q = $d->update("holidays_master", $edit_data, "holiday_id = '$holiday_id'");

        if ($q > 0) {
            $_SESSION['msg'] = "Holiday Updated Successfully.";
            $d->insert_log("0", "$bms_admin_id", "$created_by", "Holiday Updated Successfully.");
        } else {
            $_SESSION['msg1'] = "Something Went Wrong?";
        }

        $redirectCountryId = (int)$m->get_data('country_id');
        header("location:../holidays?countryId=$redirectCountryId");
        exit;
    } elseif (isset($_POST['deleteHoliday']) && $_POST['deleteHoliday'] == "deleteHoliday") {
        $holiday_id = $d->sanitizeActionIdAsInt($_POST['holiday_id'] ?? ($holiday_id ?? 0));
        $q = $d->delete("holidays_master", "holiday_id = '$holiday_id'");

        if ($q > 0) {
            $_SESSION['msg'] = "Holiday Deleted Successfully.";
            $d->insert_log("0", "$bms_admin_id", "$created_by", "Holiday Deleted Successfully.");
        } else {
            $_SESSION['msg1'] = "Something Went Wrong?";
        }

        $redirectCountryId = (isset($country_id) && (int)$country_id > 0) ? (int)$country_id : 101;
        header("location:../holidays?countryId=$redirectCountryId");
        exit;
    } else {
        $_SESSION['msg1'] = "Invalid Action";
        header("location:../holidays");
        exit;
    }

} else {
    $_SESSION['msg1'] = "Invalid Request Method";
    header("location:../holidays");
    exit;
}
?>