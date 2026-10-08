<?php
include '../common/objectController.php';
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

extract($_POST);
if (isset($_POST) && !empty($_POST)) {
    if ($_POST['importSalaryHead'] == "importSalaryHead") {

        $importCountryId = (isset($_POST['country_id']) && (int)$_POST['country_id'] > 0) ? (int)$_POST['country_id'] : 101;

        if (date('m') >= 4) {
            $tax_benefit_year = date('Y') . '-' . date('Y', strtotime('+1 year'));
        } else {
            $tax_benefit_year = date('Y', strtotime('-1 year')) . '-' . date('Y');
        }

        // $tax_benefit_year = '2023-2024';

        $post = array(
            'getTaxExemptionData' => 'getTaxExemptionData',
            'society_id' => 4,
            'tax_benefit_year' => $tax_benefit_year,
        );
        $demoCompanyUrl = 'https://ahmedabad.my-company.app/fincasys/';
        $responseTax = $d->callCompanyApiEnc($demoCompanyUrl, 'taxExemptionController.php', $post);

        if (isset($responseTax['earning_deduction_data']) && count($responseTax['earning_deduction_data']) > 0) {
            $earningDeductionQuery = $d->select("salary_earning_deduction_type_master", "country_id='$importCountryId' AND earn_deduct_is_delete=0");
            if (mysqli_num_rows($earningDeductionQuery) == 0) {
                foreach ($responseTax['earning_deduction_data'] as $val1) {
                    $a1 = array(
                        "salary_earning_deduction_id" => $val1['salary_earning_deduction_id'],
                        "country_id" => $importCountryId,
                        "earning_deduction_name" => $val1['earning_deduction_name'],
                        "earning_deduction_type" => $val1['earning_deduction_type'],
                        "salary_earning_deduction_status" => $val1['salary_earning_deduction_status'],
                        "salary_earning_deduction_created_date" => date('Y-m-d H:i:s'),
                        "salary_earning_deduction_created_by" => $val1['salary_earning_deduction_created_by'],
                        "earn_deduct_is_delete" => $val1['earn_deduct_is_delete'],
                        "is_tds_head" => $val1['is_tds_head'],
                        "is_bonus_allowance" => $val1['is_bonus_allowance'],
                        "special_allowance_for_balance_salary_figure" => $val1['special_allowance_for_balance_salary_figure'],
                    );

                    $d->insert("salary_earning_deduction_type_master", $a1);
                }

                $d->update("salary_earning_deduction_type_master", array("earning_deduction_name" => 'Provident Fund'), "salary_earning_deduction_id=4 AND country_id='$importCountryId'");
                $_SESSION['msg'] = "Imported successfully";
                header("Location: ../salaryHeadMaster?countryId=$importCountryId");
                exit;
            }
            $_SESSION['msg1'] = "Data already exists";
            header("Location: ../salaryHeadMaster?countryId=$importCountryId");
            exit;
        }
        $_SESSION['msg1'] = "Imported Failed";
        header("Location: ../salaryHeadMaster?countryId=$importCountryId");
        exit;
    }
    if ($_POST['status'] == "EarningDeductionStatusActive") {
        $a1 = array('salary_earning_deduction_status' => 0);
        $q = $d->update('salary_earning_deduction_type_master', $a1, "salary_earning_deduction_id='$id'");
        if ($q > 0) {
            $d->insert_log("", "$society_id", "$bms_admin_id", "$created_by", "Salary Earning/Deduction Type Activated ($id)");
            echo 1;
        } else {
            echo 0;
        }
        exit;
    }
    if ($_POST['status'] == "EarningDeductionStatusDeactive") {
        $a1 = array('salary_earning_deduction_status' => 1);
        $q = $d->update('salary_earning_deduction_type_master', $a1, "salary_earning_deduction_id='$id'");
        if ($q > 0) {
            $d->insert_log("", "$society_id", "$bms_admin_id", "$created_by", "Salary Earning/Deduction Type Deactivated ($id)");
            echo 1;
        } else {
            echo 0;
        }
        exit;
    }
    if ($_POST['action'] == "getEarningDeductionById") {
        if ($salary_earning_deduction_type_id != '') {
            $q = $d->selectRow('salary_earning_deduction_type_master.*', "salary_earning_deduction_type_master", "salary_earning_deduction_id=$salary_earning_deduction_type_id");
            $data = mysqli_fetch_assoc($q);
            $data = array_map("html_entity_decode", $data);
            if ($data) {
                $response["salary_earning_deduction_type_master"] = $data;
                $response["message"] = "salary_earning_deduction_type_master";
                $response["status"] = "200";
                echo json_encode($response);
            } else {
                $response["message"] = "salary_earning_deduction_type_master";
                $response["status"] = "201";
                echo json_encode($response);
            }
        } else {
            $response["message"] = "ID required";
            $response["status"] = "201";
            echo json_encode($response);
        }
    }
    if (isset($_POST['addEarnDeductType'])) {
        $earning_deduction_name = test_input($earning_deduction_name);
        $earning_deduction_description = test_input($earning_deduction_description);
        $country_id = (isset($country_id) && (int)$country_id > 0) ? (int)$country_id : 101;
        $appendNotEqQuery  = '';
        if (isset($earning_deduction_id) && $earning_deduction_id > 0) {
            $appendNotEqQuery = " AND salary_earning_deduction_id!='$earning_deduction_id'";
        }
        $qs = $d->select("salary_earning_deduction_type_master", "earning_deduction_name='$earning_deduction_name' AND earn_deduct_is_delete = 0 AND country_id='$country_id' $appendNotEqQuery ");
        if (mysqli_num_rows($qs) > 0) {
            $_SESSION['msg1'] = "already added";
            header("Location: ../salaryHeadMaster?countryId=$country_id");
            exit();
        }
        if ($earning_deduction_type == 0) {
            $eType = "Earning";
        } else if ($earning_deduction_type == 1) {
            $eType = "Deduction";
        } else if ($earning_deduction_type == 2) {
            $eType = "Non-Cash Benefit";
        }
        $is_bonus_allowance = 0;
        $special_allowance_for_balance_salary_figure = 0;
        if ($allowance_type == 1) {
            $is_bonus_allowance = 1;
            $special_allowance_for_balance_salary_figure = 0;
        } else if ($allowance_type == 2) {
            $is_bonus_allowance = 0;
            $special_allowance_for_balance_salary_figure = 1;
            $appendQ = "";
            if (isset($earning_deduction_id) && $earning_deduction_id > 0) {
                $appendQ = " AND salary_earning_deduction_id!='$earning_deduction_id'";
            }
            $tdsQ = $d->select("salary_earning_deduction_type_master", "special_allowance_for_balance_salary_figure=1 AND country_id='$country_id' $appendQ ");
            if (mysqli_num_rows($tdsQ) > 0) {
                $_SESSION['msg1'] = "already added";
                header("Location: ../salaryHeadMaster?countryId=$country_id");
                exit;
            }
        }
        $is_tds_head = 0;
        $is_gratuity_head = 0;
        $is_special_deduction_head = 0;
        if ($deduction_head_type == 1) {
            $is_tds_head = 1;
            $appendQ = "";
            if (isset($earning_deduction_id) && $earning_deduction_id > 0) {
                $appendQ = " AND salary_earning_deduction_id!='$earning_deduction_id'";
            }
            $tdsQ = $d->select("salary_earning_deduction_type_master", "is_tds_head=1 AND country_id='$country_id' $appendQ ");
            if (mysqli_num_rows($tdsQ) > 0) {
                $_SESSION['msg1'] = "already added";
                header("Location: ../salaryHeadMaster?countryId=$country_id");
                exit;
            }
        } else if ($deduction_head_type == 2) {
            $is_gratuity_head = 1;

            $appendQ = "";
            if (isset($earning_deduction_id) && $earning_deduction_id > 0) {
                $appendQ = " AND salary_earning_deduction_id!='$earning_deduction_id'";
            }

            $gratuityQ = $d->select("salary_earning_deduction_type_master", "is_gratuity_head=1 AND country_id='$country_id' $appendQ ");
            if (mysqli_num_rows($gratuityQ) > 0) {
                $_SESSION['msg1'] =  "already added";
                header("Location: ../salaryHeadMaster?countryId=$country_id");
                exit;
            }
        } else if ($deduction_head_type == 3) {
            $is_special_deduction_head = 1;

            $appendQ = "";
            if (isset($earning_deduction_id) && $earning_deduction_id > 0) {
                $appendQ = " AND salary_earning_deduction_id!='$earning_deduction_id'";
            }

            $specialDeductionQ = $d->select("salary_earning_deduction_type_master", "is_special_deduction_head=1 AND country_id='$country_id' $appendQ ");
            if (mysqli_num_rows($specialDeductionQ) > 0) {
                $_SESSION['msg1'] =  "already added";
                header("Location: ../salaryHeadMaster?countryId=$country_id");
                exit;
            }
        }

        $m->set_data('country_id', $country_id);
        $m->set_data('earning_deduction_id', $earning_deduction_id);
        $m->set_data('earning_deduction_type', $earning_deduction_type);
        $m->set_data('earning_deduction_name', $earning_deduction_name);
        $m->set_data('earning_deduction_description', $earning_deduction_description);
        $m->set_data('is_tds_head', $is_tds_head);
        $m->set_data('is_gratuity_head', $is_gratuity_head);
        $m->set_data('is_special_deduction_head', $is_special_deduction_head);
        $m->set_data('is_bonus_allowance', $is_bonus_allowance);
        $m->set_data('special_allowance_for_balance_salary_figure', $special_allowance_for_balance_salary_figure);
        $m->set_data('salary_earning_deduction_created_by', $bms_admin_id);
        $m->set_data('salary_earning_deduction_created_date', date("Y-m-d H:i:s"));

        $a1 = array(
            'country_id' => $m->get_data('country_id'),
            'earning_deduction_type' => $m->get_data('earning_deduction_type'),
            'earning_deduction_name' => $m->get_data('earning_deduction_name'),
            'earning_deduction_description' => $m->get_data('earning_deduction_description'),
            'is_tds_head' => $m->get_data('is_tds_head'),
            'is_gratuity_head' => $m->get_data('is_gratuity_head'),
            'is_special_deduction_head' => $m->get_data('is_special_deduction_head'),
            'is_bonus_allowance' => $m->get_data('is_bonus_allowance'),
            'special_allowance_for_balance_salary_figure' => $m->get_data('special_allowance_for_balance_salary_figure'),
            'salary_earning_deduction_created_by' => $m->get_data('salary_earning_deduction_created_by'),
            'salary_earning_deduction_created_date' => $m->get_data('salary_earning_deduction_created_date'),
        );

        if (isset($earning_deduction_id) && $earning_deduction_id > 0) {
            $q = $d->update("salary_earning_deduction_type_master", $a1, "salary_earning_deduction_id ='$earning_deduction_id'");
            $_SESSION['msg'] = "Updated successfully";
            $common_id = $earning_deduction_id;
            $old_data = '';
            $new_data = json_encode($a1);
            $module_name = 'Earning Deduction Type';
            $d->insert_log("", "$society_id", "$bms_admin_id", "$created_by", "$eType Type $earning_deduction_name Update");
        } else {

            //Get Max Position Order
            $check_earning_deduction_type = $m->get_data('earning_deduction_type');
            $maxOrderResult = $d->selectRow("MAX(earning_deduction_order) as max_order", " salary_earning_deduction_type_master", "earning_deduction_type = '$check_earning_deduction_type' AND country_id='$country_id'");

            if (mysqli_num_rows($maxOrderResult) > 0) {
                $maxOrderData = mysqli_fetch_array($maxOrderResult);
                $max_order = $maxOrderData['max_order'];

                $new_order = $max_order + 1;

                $a1['earning_deduction_order'] = $new_order;
            }

            $q = $d->insert("salary_earning_deduction_type_master", $a1);
            $_SESSION['msg'] = "Added successfully";
            $d->insert_log("", "$society_id", "$bms_admin_id", "$created_by", "$eType Type $earning_deduction_name Added");
        }

        $redirectCountryId = (int)$m->get_data('country_id');
        if ($q == TRUE) {
            header("Location: ../salaryHeadMaster?countryId=$redirectCountryId");
        } else {
            $_SESSION['msg1'] = "Something wrong";
            header("Location: ../salaryHeadMaster?countryId=$redirectCountryId");
        }
    }

    if (isset($_POST['deleteEarnDeductionType']) && $_POST['deleteEarnDeductionType'] == 'deleteEarnDeductionType') {
        $earning_deduction_id = $d->sanitizeActionIdAsInt($_POST['earning_deduction_id'] ?? ($earning_deduction_id ?? 0));

        $redirectCountryId = (isset($_POST['country_id']) && (int)$_POST['country_id'] > 0) ? (int)$_POST['country_id'] : 101;
        $q = $d->update("salary_earning_deduction_type_master", array("earn_deduct_is_delete" => '1', "is_tds_head" => '0', "special_allowance_for_balance_salary_figure" => '0', 'is_gratuity_head' => '0', 'is_special_deduction_head' => 0), "salary_earning_deduction_id  ='$salary_earning_deduction_id'");

        if ($q > 0) {
            $d->insert_log("", "$society_id", "$created_by_id", "$created_by", "Salary Earning Deduction Type Deleted ($salary_earning_deduction_id)");
            $_SESSION['msg'] = "Deleted successfully";
            header("Location: ../salaryHeadMaster?countryId=$redirectCountryId");
            exit;
        } else {
            $_SESSION['msg1'] = "Something wrong";
            header("Location: ../salaryHeadMaster?countryId=$redirectCountryId");
            exit;
        }
    }

    if (isset($_POST['earningDeductionType']) && $_POST['earningDeductionType'] == 'earningDeductionType' && isset($_POST['order']) && isset($_POST['earningdeduction'])) {

        $order = $_POST['order'];
        $earningdeduction = $_POST['earningdeduction'];

        $earning_order_status = false;

        foreach ($order as $item) {

            $earning_order_status = false;

            $id = (int)$item['id'];
            $position = (int)$item['position'];

            $update_arr = ['earning_deduction_order' => $position];

            $q = $d->update("salary_earning_deduction_type_master", $update_arr, "salary_earning_deduction_id = '$id'");

            if ($q) {
                $earning_order_status = true;
            }
        }
        if ($earning_order_status) {
            $response['message'] = "Updated successfully";
            $response['status'] = "200";
        } else {
            $response['message'] = "something wrong";
            $response['status'] = "201";
        }
        echo json_encode($response);
        exit();
    }
}
