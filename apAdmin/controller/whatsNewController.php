<?php
include '../common/objectController.php';
$bms_admin_id = $bms_admin_id;
extract($_POST);
if (isset($_POST['checkVersionValid']) && $checkVersionValid == "checkVersionValid") {
    if (isset($version) && $version != "") {
        $version = $d->escapeSqlString($version);
        $platform = isset($platform) ? $d->escapeSqlString($platform) : '';
        if (isset($patch_id) && $patch_id != "") {
            echo json_encode(['valid' => true]);
            exit;
        } else if (isset($platform) && $platform != "" && $platform != "2") {
            $patch_qry = $d->selectRow("version", "patch_master", "platform='$platform'", "ORDER BY patch_id DESC LIMIT 1");
            if (mysqli_num_rows($patch_qry) > 0) {
                $data = mysqli_fetch_array($patch_qry);
                $currentVersion = $version;
                $dataVersion = $data['version'];
                if (version_compare($currentVersion, $dataVersion, '<=')) {
                    $patch_qry_count = $d->count_data_direct("version", "patch_master", "platform='$platform' AND version='$currentVersion'");
                    if ($patch_qry_count > 0) {
                        echo json_encode(['valid' => false, 'max_version' => $data['version']]);
                        exit;
                    } else {
                        echo json_encode(['valid' => true]);
                        exit;
                    }
                } else {
                    echo json_encode(['valid' => true]);
                    exit;
                }
            } else {
                echo json_encode(['valid' => true]);
                exit;
            }
        } else {
            echo json_encode(['valid' => true]);
            exit;
        }
    } else {
        echo json_encode(['valid' => true]);
        exit;
    }
} else if (isset($_POST['addWhatsNew']) && $_POST['addWhatsNew'] == "addWhatsNew") {
    $platform = test_input($_POST['platform'] ?? '');
    $title = test_input($_POST['title'] ?? '');
    $version = test_input($_POST['version'] ?? '');
    // $patch_id = $_POST['patch_id'] ?? '';
    $language_ids = $_POST['language_id'] ?? [];
    $created_by = $bms_admin_id;
    $created_date = date("Y-m-d H:i:s");
    if ($platform != "" && $title != "" && !empty($language_ids)) {
        if ($platform == '2') {
            $max_version = $d->selectRow("max(version)", "patch_master", "platform='2'");
            $web_ver_data = mysqli_fetch_array($max_version);
            $max_version_value = ($web_ver_data['max(version)'] != '') ? ++$web_ver_data['max(version)'] : "0";
        }
        $a = [
            'patch_title' => $title,
            'platform' => $platform,
            'version' => ($platform != '2' && !empty($version)) ? $version : $max_version_value,
            'created_date' => $created_date,
            'created_by' => $created_by,
        ];
        $i = 0;
        foreach ($language_ids as $index => $language_id) {
            $whats_new_key = "whats_new_updates_" . $i++;
            $whats_new_value = $_POST[$whats_new_key];
            if (empty($whats_new_value)) {
                continue;
            }
            $a['language_id'] = $language_id;
            $a['whats_new'] = $whats_new_value;
            $insert_patch = $d->insert("patch_master", $a);
            if (!$insert_patch) {
                $_SESSION['msg1'] = "Something went wrong";
                header("location:../addWhatsNew");
                exit;
            }
        }
        $d->insert_log("$society_id", "$bms_admin_id", "$created_by", "What's New Added.");
        $_SESSION['msg'] = "Added Successfully.";
        header("location:../whatsNew");
    } else {
        $_SESSION['msg1'] = "Please fill mandatory fields.";
        header("location:../addWhatsNew");
    }
} elseif (isset($_POST['editWhatsNew']) && $_POST['editWhatsNew'] == "editWhatsNew") {
    // print_r($_POST);exit;
    $title = test_input($_POST['title'] ?? '');
    $patch_id = test_input($_POST['patch_id'] ?? '');
    $platform = test_input($_POST['platform'] ?? '');
    $language_ids = $_POST['language_id'] ?? [];
    $updated_by = $bms_admin_id;
    $updated_date = date("Y-m-d H:i:s");
    if (isset($platform) && $platform != "" && isset($title) && $title != "" && !empty($language_ids)) {
        $platform = $d->escapeSqlString($platform);
        $version = $d->escapeSqlString($version);
        $langIdsInt = array_values(array_unique(array_map('intval', $language_ids)));
        $existingPatchByLang = [];
        if (!empty($langIdsInt)) {
            $langIdsIn = implode(',', $langIdsInt);
            $existing_patch = $d->selectRow(
                "patch_id, language_id",
                "patch_master",
                "platform = '$platform' AND version = '$version' AND language_id IN ($langIdsIn)"
            );
            while ($er = mysqli_fetch_assoc($existing_patch)) {
                $existingPatchByLang[(int)$er['language_id']] = true;
            }
        }
        $i = 0;
        foreach ($language_ids as $index => $language_id) {
            $language_id = $d->sanitizeActionIdAsInt($language_id);
            $whats_new_key = "whats_new_updates_" . $i++;
            $whats_new_value = $_POST[$whats_new_key];
            if (empty($whats_new_value)) {
                continue;
            }
            $a = [
                'patch_title' => $title,
                'whats_new' => $whats_new_value,
            ];
            if (!empty($existingPatchByLang[$language_id])) {
                $a['updated_date'] = $updated_date;
                $a['updated_by'] = $updated_by;
                $update_patch = $d->update("patch_master", $a, "platform = '$platform' AND language_id = '$language_id' AND version = '$version'");
                if (!$update_patch) {
                    $_SESSION['msg1'] = "Something went wrong";
                    header("location:../whatsNew");
                    exit;
                }
            } else {
                $a['version'] = $version;
                $a['platform'] = $platform;
                $a['created_date'] = $updated_date;
                $a['created_by'] = $updated_by;
                $a['language_id'] = $language_id;
                $insert_patch = $d->insert("patch_master", $a);
                if (!$insert_patch) {
                    $_SESSION['msg1'] = "Something went wrong";
                    header("location:../whatsNew");
                    exit;
                }
                $existingPatchByLang[$language_id] = true;
            }
        }
        $d->insert_log("$society_id", "$bms_admin_id", "$updated_by", "What's New Updated/Inserted.");
        $_SESSION['msg'] = "Updated Successfully.";
        header("location:../whatsNew");
    } else {
        $_SESSION['msg1'] = "Please fill mandatory fields.";
        header("location:../whatsNew");
    }
} elseif (isset($_POST['singleDelete']) && $singleDelete == "singleDelete") {
    if (isset($patch_id) && $patch_id != "") {
        $patch_id = $d->sanitizeActionIdAsInt($patch_id);
        $insert_patch = $d->delete("patch_master", "patch_id='$patch_id'");
        if ($insert_patch) {
            $d->insert_log("$society_id", "$bms_admin_id", "$created_by", "What's New Deleted.");
            $_SESSION['msg'] = "Deleted Successfully.";
            echo 1;
        } else {
            $_SESSION['msg1'] = "Something went wrong.";
            echo 0;
        }
    }
} else {
    $_SESSION['msg1'] = "Something wrong. Try again after sometime";
    header("location:../welcome");
}
