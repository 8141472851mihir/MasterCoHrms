<?php
include '../common/objectController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    extract($_POST);

    if (isset($addTaxLimit) && $addTaxLimit === "addTaxLimit") {
        $m->set_data('tax_year', test_input($tax_year));
        $m->set_data('new_regime_amount', test_input($new_regime_amount));
        $m->set_data('old_regime_amount', test_input($old_regime_amount));
        $existing = $d->selectRow("*", "tax_limit_master", "tax_year = '" . $m->get_data('tax_year') . "'");
        if (mysqli_num_rows($existing) > 0) {
            $_SESSION['msg1'] = "Tax Year Already Exists!";
            header("Location: ../taxLimit");
            exit;
        }

        $dataToInsert = [
            'tax_year' => $m->get_data('tax_year'),
            'new_regime_amount' => $m->get_data('new_regime_amount'),
            'old_regime_amount' => $m->get_data('old_regime_amount'),
        ];

        $result = $d->insert("tax_limit_master", $dataToInsert);

        $_SESSION['msg'] = $result ? "Tax Limit Added Successfully" : "Something Went Wrong";
        header("Location: ../taxLimit");
        exit;
    } elseif (isset($editTaxLimit) && $editTaxLimit === "editTaxLimit") {
        $m->set_data('tax_year', test_input($tax_year));
        $m->set_data('new_regime_amount', test_input($new_regime_amount));
        $m->set_data('old_regime_amount', test_input($old_regime_amount));
        $existing = $d->selectRow("*", "tax_limit_master", "tax_year = '" . $m->get_data('tax_year') . "' AND tax_limit_id != $tax_limit_id");
        if (mysqli_num_rows($existing) > 0) {
            $_SESSION['msg1'] = "Tax Year Already Exists!";
            header("Location: ../taxLimit");
            exit;
        }

        $dataToUpdate = [
            'tax_year' => $m->get_data('tax_year'),
            'new_regime_amount' => $m->get_data('new_regime_amount'),
            'old_regime_amount' => $m->get_data('old_regime_amount'),
        ];

        $result = $d->update("tax_limit_master", $dataToUpdate, "tax_limit_id = $tax_limit_id");

        $_SESSION['msg'] = $result ? "Tax Limit Updated Successfully" : "Something Went Wrong";
        header("Location: ../taxLimit");
        exit;
    } elseif (isset($deleteTaxLimit) && $deleteTaxLimit === "deleteTaxLimit") {
        $result = $d->delete("tax_limit_master", "tax_limit_id = $tax_limit_id");

        $_SESSION['msg'] = $result ? "Tax Limit Deleted Successfully" : "Something Went Wrong";
        header("Location: ../taxLimit");
        exit;
    } else {
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