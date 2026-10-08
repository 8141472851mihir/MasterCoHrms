<?php
include '../common/objectController.php';
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
if (isset($_POST) && !empty($_POST)) {
    extract($_POST);

    if (isset($addDeductionRule)) {
        // echo "<pre>";
        // print_r($_POST);
        // die;

        $formulaArray = array();

        $a = array(
            "tax_benefit_category_name" => $tax_benefit_category_name,
            "applicable_for" => $applicable_for,
            "max_tax_benefit_amount" => $max_tax_benefit_amount,
            "rule_type" => $rule_type,
            "amount_type" => $amount_type,
            "tax_benefit_year" => $tax_benefit_year,
            "tax_benefit_order" => $tax_benefit_order,
        );

        if ($rule_type == 0) {
            $a['amount'] = $amount;
        } else if ($rule_type == 1) {
            if ($amount_type == 0) {
                $head_id = '';
                if ($deduction_head) {
                    $head_id = implode(',', $deduction_head);
                }
                $formulaArray = array('head_id' => $head_id);

            } else if ($amount_type == 1) {


                $head_id_1 = '';
                if ($deduction_head_1) {
                    $head_id_1 = implode(',', $deduction_head_1);
                }
                $head_id_2 = '';
                if ($deduction_head_2) {
                    $head_id_2 = implode(',', $deduction_head_2);
                }
                $head_id_3 = '';
                if ($deduction_head_3) {
                    $head_id_3 = implode(',', $deduction_head_3);
                }

                $formulaArray['case_1'] = array(
                    'head_id' => $head_id_1,
                    'formula_modular_1' => $formula_modular_11,
                    'value' => $value_1,
                    'formula_modular_2' => $formula_modular_21,
                );

                $formulaArray['case_2'] = array(
                    'head_id' => $head_id_2,
                    'formula_modular_1' => $formula_modular_12,
                    'value' => $value_2,
                    'value_1' => $value_3,
                    'formula_modular_2' => $formula_modular_22,
                );

                $formulaArray['case_3'] = array(
                    'head_id' => $head_id_3,
                );

            }
        } else if ($rule_type == 2) {
            if ($amount_type == 1) {
                if ($formula_type == 0) {
                    $formulaArray = array(
                        'head_id' => '',
                        'formula_modular_1' => $formula_modular_1,
                        'formula_type' => $formula_type,
                        'value' => $value,
                        'formula_modular_2' => $formula_modular_2,
                    );
                } else if ($formula_type == 1) {
                    $valueArray = array();
                    if (count($min) > 0 && count($min) == count($max) && count($min) == count($slab_value)) {
                        for ($i = 0; $i < count($min); $i++) {
                            $val = array(
                                'min' => $min[$i],
                                'max' => $max[$i],
                                'value' => $slab_value[$i],
                            );
                            array_push($valueArray, $val);
                        }
                    }

                    $formulaArray = array(
                        'head_id' => '',
                        'formula_modular_1' => $slab_formula_modular_1,
                        'formula_type' => $formula_type,
                        'value' => ($valueArray),
                        'formula_modular_2' => $slab_formula_modular_2,
                    );
                }


            }

        } else if ($rule_type == 3) {
            $head_id = '';
            if ($pf_deduction_head) {
                $head_id = implode(',', $pf_deduction_head);
            }
            $formulaArray = array('head_id' => $head_id);

        } else if ($rule_type == 4) {
            $a['amount'] = $amount;
        }

        if (!empty($formulaArray)) {
            $formula_json = json_encode($formulaArray);
            $a['formula_json'] = $formula_json;
        }

        if ($tax_benefit_category_id > 0) {
            $a['updated_date'] = date('Y-m-d H:i:s');
            $a['updated_by_id'] = $bms_admin_id;
            $a['updated_by_name'] = $created_by;
            $q = $d->update("tax_benefit_category", $a, "tax_benefit_category_id = '$tax_benefit_category_id'");

            if ($q > 0) {
                $_SESSION['msg'] = "Updated successfully";
                header("Location: ../taxBenefitCategory?year=$tax_benefit_year");
                exit;
            } else {
                $_SESSION['msg1'] = "Something Went Wrong";
                header("Location: ../taxBenefitCategory?year=$tax_benefit_year");
                exit;
            }

        } else {
            $a['created_date'] = date('Y-m-d H:i:s');
            $a['created_by_id'] = $bms_admin_id;
            $a['created_by_name'] = $created_by;

            $q = $d->insert("tax_benefit_category", $a);
            if ($q > 0) {
                $_SESSION['msg'] = "Added successfully";
                header("Location: ../taxBenefitCategory?year=$tax_benefit_year");
                exit;
            } else {

                $_SESSION['msg1'] = "Something Went Wrong";
                header("Location: ../taxBenefitCategory?year=$tax_benefit_year");
                exit;
            }
        }
    }

    if (isset($importTaxBenefitCategory)) {
        $idCount = count($tax_benefit_category_id);
        $categoryIdsInt = array_map('intval', $tax_benefit_category_id);
        $categoryData = array();
        if (!empty($categoryIdsInt)) {
            $categoryIdsIn = implode(',', $categoryIdsInt);
            $q1 = $d->select("tax_benefit_category", "tax_benefit_category_id IN ($categoryIdsIn)");
            while ($row = mysqli_fetch_array($q1)) {
                $categoryData[(int)$row['tax_benefit_category_id']] = $row;
            }
        }
        $subCategoryQry = $d->select("tax_benefit_sub_category", "tax_benefit_category_id IN ('".implode("','", array_map('intval', $tax_benefit_category_id))."') AND delete_status = 0");
        $subCategoryData = array();
        while ($row = mysqli_fetch_array($subCategoryQry)) {
            $subCategoryData[$row['tax_benefit_category_id']][] = $row;
        }

        for ($i = 0; $i < $idCount; $i++) {
            $category_id = (int)$tax_benefit_category_id[$i];
            $data = $categoryData[$category_id] ?? null;
            if (!$data) {
                continue;
            }
            $a = array(
                "tax_benefit_category_name" => $data['tax_benefit_category_name'],
                "applicable_for" => $data['applicable_for'],
                "max_tax_benefit_amount" => $data['max_tax_benefit_amount'],
                "rule_type" => $data['rule_type'],
                "amount_type" => $data['amount_type'],
                "amount" => $data['amount'],
                "formula_json" => $data['formula_json'],
                "tax_benefit_order" => $data['tax_benefit_order'],
                "tax_benefit_year" => $year,
                "created_date" => date('Y-m-d H:i:s'),
                "created_by_id" => $bms_admin_id,
                "created_by_name" => $created_by,
            );

            $q = $d->insert("tax_benefit_category", $a);
            $new_category_id = $con->insert_id;
            foreach ($subCategoryData[$category_id] ?? [] as $subCategory) {
                $a2 = array(
                    "tax_benefit_category_id" => $new_category_id,
                    "tax_benefit_sub_category_name" => $subCategory['tax_benefit_sub_category_name'],
                    "benefit_percent" => $subCategory['benefit_percent'],
                    "created_date" => date('Y-m-d H:i:s'),
                    "created_by_id" => $bms_admin_id,
                    "created_by_name" => $created_by,
                );
                $q2 = $d->insert("tax_benefit_sub_category", $a2);
            }

        }

        if ($q > 0) {

            $_SESSION['msg'] = "Success";
            header("Location: ../taxBenefitCategory?year=$year");
            exit;
        } else {

            $_SESSION['msg1'] = "Something Went Wrong";
            header("Location: ../taxBenefitCategory?year=$year");
            exit;
        }
    }
} else {
    $_SESSION['msg1'] = "Invalid Request Method";
    header("Location: ../taxBenefitCategory");
    exit;
}
?>