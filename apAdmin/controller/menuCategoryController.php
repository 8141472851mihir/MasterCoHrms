<?php
include '../common/objectController.php';

if (isset($_POST) && !empty($_POST)) {
    extract($_POST);
    if (isset($_POST['addMenuCategory']) && $_POST['addMenuCategory'] == "addMenuCategory") {
        $m->set_data("menu_category_name", test_input($menu_category_name));
        $m->set_data("menu_category_key", test_input($menu_category_key));
        $m->set_data("menu_category_icon", test_input($menu_category_icon));

        $add_data = [
            'menu_category_name' => $m->get_data('menu_category_name'),
            'menu_category_key' => $m->get_data('menu_category_key'),
            'menu_category_icon' => $m->get_data('menu_category_icon'),
            'menu_category_status' => 0,
            'created_by' => $bms_admin_id,
            'created_date' => date("Y-m-d H:i:s")
        ];

        $q = $d->insert("menu_category_master", $add_data);

        if ($q > 0) {
            $_SESSION['msg'] = "Menu Category Added Successfully.";
            $d->insert_log("0", "$bms_admin_id", "$created_by", "Menu Category Added Successfully.");
        } else {
            $_SESSION['msg1'] = "Something Went Wrong?";
        }

        header("location:../menuCategory");
        exit;
    }elseif (isset($_POST['editMenuCategory']) && $_POST['editMenuCategory'] == "editMenuCategory") {
        $m->set_data("menu_category_name", test_input($menu_category_name));
        $m->set_data("menu_category_key", test_input($menu_category_key));
        $m->set_data("menu_category_icon", test_input($menu_category_icon));

        $add_data = [
            'menu_category_name' => $m->get_data('menu_category_name'),
            'menu_category_key' => $m->get_data('menu_category_key'),
            'menu_category_icon' => $m->get_data('menu_category_icon'),
            'updated_by' => $bms_admin_id,
            'updated_date' => date("Y-m-d H:i:s")
        ];

        $q = $d->update("menu_category_master", $add_data,"menu_category_id='$menu_category_id'");

        if ($q > 0) {
            $_SESSION['msg'] = "Menu Category Updated Successfully.";
            $d->insert_log("0", "$bms_admin_id", "$created_by", "Menu Category Updated Successfully.");
        } else {
            $_SESSION['msg1'] = "Something Went Wrong?";
        }

        header("location:../menuCategory");
        exit;
    }elseif (isset($_POST['changeCategory']) && $_POST['changeCategory'] == "changeCategory") {
        $m->set_data("menu_category_id", test_input($menu_category_id));
        $add_data = [
            'menu_category_id' => $m->get_data('menu_category_id'),
        ];
        if($assign_type=="utility"){
            $q = $d->update("resident_app_menu_utility", $add_data,"app_menu_id='$app_menu_id'");
        }else{
            $q = $d->update("resident_app_menu", $add_data,"app_menu_id='$app_menu_id'");
        }

        if ($q > 0) {
            $_SESSION['msg'] = "Menu Category Added Successfully.";
            $d->insert_log("0", "$bms_admin_id", "$created_by", "Menu Category Added Successfully.");
        } else {
            $_SESSION['msg1'] = "Something Went Wrong?";
        }

        header("location:../masterAppMenu");
        exit;
    } else {
        $_SESSION['msg1'] = "Invalid Action";
        header("location:../menuCategory");
        exit;
    }
} else {
    $_SESSION['msg1'] = "Invalid Request Method";
    header("location:../menuCategory");
    exit;
}
