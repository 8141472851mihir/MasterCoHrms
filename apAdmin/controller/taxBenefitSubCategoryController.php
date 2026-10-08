<?php
include '../common/objectController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    extract($_POST);

    if (isset($addTaxBenefitSubCategory) && $addTaxBenefitSubCategory === "addTaxBenefitSubCategory") {
        $m->set_data('tax_benefit_sub_category_name', test_input($tax_benefit_sub_category_name));
        $m->set_data('tax_benefit_category_id', test_input($tax_benefit_category_id));
        $m->set_data('benefit_percent', test_input($benefit_percent));
        $m->set_data('created_date', date('Y-m-d H:i:s'));

        $dataToInsert = [
            'tax_benefit_sub_category_name' => $m->get_data('tax_benefit_sub_category_name'),
            'tax_benefit_category_id' => $m->get_data('tax_benefit_category_id'),
            'benefit_percent' => $m->get_data('benefit_percent'),
            'created_date' => $m->get_data('created_date')
        ];

        $result = $d->insert("tax_benefit_sub_category", $dataToInsert);

        $_SESSION['msg'] = $result ? "Sub Category Added Successfully" : "Something Went Wrong";
        header("Location: ../taxBenefitSubCategory");
        exit;
    }

    elseif (isset($editTaxBenefitSubCategory) && $editTaxBenefitSubCategory === "editTaxBenefitSubCategory") {
        $m->set_data('tax_benefit_sub_category_name', test_input($tax_benefit_sub_category_name));
        $m->set_data('tax_benefit_category_id', test_input($tax_benefit_category_id));
        $m->set_data('benefit_percent', test_input($benefit_percent));
        $m->set_data('updated_date', date('Y-m-d H:i:s'));

        $dataToUpdate = [
            'tax_benefit_sub_category_name' => $m->get_data('tax_benefit_sub_category_name'),
            'tax_benefit_category_id' => $m->get_data('tax_benefit_category_id'),
            'benefit_percent' => $m->get_data('benefit_percent'),
            'updated_date' => $m->get_data('updated_date')
        ];

        $result = $d->update("tax_benefit_sub_category", $dataToUpdate, "tax_benefit_sub_category_id = $tax_benefit_sub_category_id");

        $_SESSION['msg'] = $result ? "Sub Category Updated Successfully" : "Something Went Wrong";
        header("Location: ../taxBenefitSubCategory");
        exit;
    }

    elseif (isset($deleteTaxBenefitSubCategory) && $deleteTaxBenefitSubCategory === "deleteTaxBenefitSubCategory") {
        $result = $d->delete("tax_benefit_sub_category", "tax_benefit_sub_category_id = $tax_benefit_sub_category_id");

        $_SESSION['msg'] = $result ? "Sub Category Deleted Successfully" : "Something Went Wrong";
        header("Location: ../taxBenefitSubCategory");
        exit;
    }

    else {
        $_SESSION['msg1'] = "Invalid Action";
        header("Location: ../taxBenefitSubCategory");
        exit;
    }
} else {
    $_SESSION['msg1'] = "Invalid Request Method";
    header("Location: ../taxBenefitSubCategory");
    exit;
}
?>