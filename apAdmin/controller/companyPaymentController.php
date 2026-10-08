<?php
include '../common/objectController.php';
if (isset($_POST) && !empty($_POST)) {
    extract($_POST);
    if (isset($addCompanyPayment)) {
        if (isset($payment_society_id) && $payment_society_id != "") {
            $society_id = $_POST['payment_society_id'];
            $society = $d->selectRow("sub_domain", "society_master", "society_id = $society_id");
            $societyRow = mysqli_fetch_assoc($society);

            $sub_domain = $societyRow['sub_domain'];
            $postData = array(
                'society_id' => $society_id,
                'company_transaction_amount' => $company_transaction_amount,
                'payment_id' => $payment_id,
                'addCompanyPayment' => 'addCompanyPayment',
            );

            $parsed = $d->callCompanyApiEnc($sub_domain, 'companyPaymentController.php', $postData);

            $company_response_status = isset($parsed['status']) ? $parsed['status'] : '201';
            if($company_response_status ==200){
                $company_response_status = 1;
            }else{
                $company_response_status=0;
            }
        
            $m->set_data('society_id', test_input($society_id));
            $m->set_data('company_transaction_amount', test_input($company_transaction_amount));
            $m->set_data('company_payment_id', test_input($payment_id));
            $m->set_data('company_transaction_date', date('Y-m-d H:i:s'));
            $m->set_data('company_response_status', $company_response_status);
            $m->set_data('company_transaction_status', 1);
            $a1 = array(
                'society_id' => $m->get_data('society_id'),
                'company_transaction_amount' => $m->get_data('company_transaction_amount'),
                'company_payment_id' => $m->get_data('company_payment_id'),
                'company_transaction_date' => $m->get_data('company_transaction_date'),
                'company_response_status' => $m->get_data('company_response_status'),
                'company_transaction_status' => $m->get_data('company_transaction_status')
            );
            $q = $d->insert("company_transaction_master", $a1);
            if ($company_response_status > 0) {
                $_SESSION['msg'] = "Company Payment Added Successfully.";
                header("location:../addCompanyPayment");
                exit();
            } else {
                $_SESSION['msg1'] = "Something Went Wrong";
                header("location:../addCompanyPayment");
                exit();
            }
        } else {
            $_SESSION['msg1'] = "Payment Failed";
            header("location:../addCompanyPayment");
            exit();
        }
    } else {
        $_SESSION['msg1'] = "Something Went Wrong";
        header("location:../addCompanyPayment");
        exit();
    }
} else {
    $_SESSION['msg1'] = "Something Went Wrong";
    header("location:../addCompanyPayment");
    exit();
}
