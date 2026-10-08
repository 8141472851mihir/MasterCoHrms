<?php
include '../common/objectController.php';

if (isset($_POST) && !empty($_POST)) {
    extract($_POST);

    if (isset($_POST['expenseTitleCheck'])) {
        $expense_title_check = $d->escapeSqlString(trim($_POST['expense_title_check'] ?? ''));
        $edit_expense_id = $d->sanitizeActionIdAsInt($_POST['edit_expense_id'] ?? 0);

        $where = "expense_title = '$expense_title_check'";
        if ($edit_expense_id > 0) {
            $where .= " AND expense_id != '$edit_expense_id'";
        }

        $existing = $d->select("expense_master", $where);
        echo json_encode(['valid' => mysqli_num_rows($existing) == 0]);
        exit;


    } elseif (isset($_POST['addExpenseData']) && $_POST['addExpenseData'] == "addExpenseData") {

        $image = $_POST['expense_icon'] ?? '';

        if (isset($_FILES["expense_icon"]) && $_FILES["expense_icon"]["tmp_name"] != '') {
            $file_image = $_FILES["expense_icon"]["tmp_name"];
            $acceptable = ['jpeg', 'jpg', 'png'];
            $extId = strtolower(pathinfo($_FILES['expense_icon']['name'], PATHINFO_EXTENSION));

            if (!in_array($extId, $acceptable)) {
                $_SESSION['msg1'] = "Invalid Photo format. Allowed: jpeg, jpg, png.";
                header("location:../expense");
                exit;
            }

            $temp = explode(".", $_FILES["expense_icon"]["name"]);
            $user_name = str_replace(' ', '_', $expense_title);
            $image = $user_name . '_' . round(microtime(true)) . '.' . end($temp);
            $destinationPath = "../../img/master/expense/" . $image;
            $d->resizeImage($file_image, $destinationPath, 1280, 720, $extId);
        }

        $m->set_data("expense_title", test_input($expense_title));
        $m->set_data("expense_icon", test_input($image));

        $add_data = [
            'expense_title' => $m->get_data('expense_title'),
            'expense_icon' => $m->get_data('expense_icon'),
            'added_by' => $bms_admin_id,
            'added_date' => date("Y-m-d H:i:s")
        ];

        $q = $d->insert("expense_master", $add_data);

        if ($q > 0) {
            $_SESSION['msg'] = "Expense Added Successfully.";
            $d->insert_log("0", $bms_admin_id, $created_by, "Expense '$expense_title' Added");
        } else {
            $_SESSION['msg1'] = "Something Went Wrong.";
        }
        header("location:../expense");
        exit;

    } elseif (isset($_POST['updateExpenseData']) && $_POST['updateExpenseData'] == "updateExpenseData") {
        $image = $_POST['expense_icon'] ?? '';

        if (isset($_FILES["expense_icon"]) && $_FILES["expense_icon"]["tmp_name"] != '') {
            $file_image = $_FILES["expense_icon"]["tmp_name"];
            $acceptable = ['jpeg', 'jpg', 'png'];
            $extId = strtolower(pathinfo($_FILES['expense_icon']['name'], PATHINFO_EXTENSION));

            if (!in_array($extId, $acceptable)) {
                $_SESSION['msg1'] = "Invalid Photo format. Allowed: jpeg, jpg, png.";
                header("location:../expense");
                exit;
            }

            $dirPath = "../../img/master/expense/";
            if (!empty($image) && file_exists($dirPath . $image)) {
                unlink($dirPath . $image);
            }

            $temp = explode(".", $_FILES["expense_icon"]["name"]);
            $user_name = str_replace(' ', '_', $expense_title);
            $image = $user_name . '_' . round(microtime(true)) . '.' . end($temp);
            $destinationPath = $dirPath . $image;
            $d->resizeImage($file_image, $destinationPath, 1280, 720, $extId);
        }

        $m->set_data("expense_title", test_input($expense_title));
        $m->set_data("expense_icon", test_input($image));

        $edit_data = [
            'expense_title' => $m->get_data('expense_title'),
            'expense_icon' => $m->get_data('expense_icon'),
            'updated_by' => $bms_admin_id,
            'updated_date' => date("Y-m-d H:i:s")
        ];

        $q = $d->update("expense_master", $edit_data, "expense_id = '$expense_id'");

        if ($q > 0) {
            $_SESSION['msg'] = "Expense Updated Successfully.";
            $d->insert_log("0", $bms_admin_id, $created_by, "Expense '$expense_title' Updated");
        } else {
            $_SESSION['msg1'] = "Something Went Wrong.";
        }
        header("location:../expense");
        exit;

    } elseif (isset($_POST['deleteExpense']) && $_POST['deleteExpense'] == "deleteExpense") {
        $expense_id = $d->sanitizeActionIdAsInt($_POST['expense_id'] ?? ($expense_id ?? 0));
        $q = $d->delete("expense_master", "expense_id = $expense_id");

        if ($q > 0) {
            $_SESSION['msg'] = "Expense Deleted Successfully.";
            $d->insert_log("0", $bms_admin_id, $created_by, "Expense ID $expense_id Deleted");
        } else {
            $_SESSION['msg1'] = "Something Went Wrong.";
        }
        header("location:../expense");
        exit;

    } elseif (isset($_POST['uploadIconData']) && $_POST['uploadIconData'] == "uploadIconData") {
        $icon_name = $_POST['iconName'] ?? '';
        $icon_image = '';

        if (isset($_FILES["uploadIcon"]) && $_FILES["uploadIcon"]["tmp_name"] != '') {
            $file_image = $_FILES["uploadIcon"]["tmp_name"];
            $acceptable = ['jpeg', 'jpg', 'png', 'svg'];
            $extId = strtolower(pathinfo($_FILES['uploadIcon']['name'], PATHINFO_EXTENSION));

            if (!in_array($extId, $acceptable)) {
                echo "Invalid image format. Allowed: jpeg, jpg, png, svg.";
                exit;
            }

            $clean_name = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $icon_name);
            $icon_image = $clean_name . '.' . $extId;
            $destinationPath = "../../img/emp_icon/" . $icon_image;

            if ($extId === 'svg') {
                move_uploaded_file($file_image, $destinationPath);
            } else {
                $d->resizeImage($file_image, $destinationPath, 1280, 720, $extId);
            }

            header('Content-Type: application/json');
            echo json_encode([
                "status" => "success",
                "message" => "Icon '$icon_name' uploaded successfully!",
                "iconName" => $icon_image,
                "iconUrl" => "../img/emp_icon/" . $icon_image
            ]);
            exit;
        } else {
            echo "Icon image not found.";
            exit;
        }

    } else {
        $_SESSION['msg1'] = "Invalid Action";
        header("Location: ../expense");
        exit;
    }

} else {
    $_SESSION['msg1'] = "Invalid Method";
    header("Location: ../expense");
    exit;
}

?>