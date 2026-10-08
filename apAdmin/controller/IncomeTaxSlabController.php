<?php
include '../common/objectController.php';
extract($_POST);

/* ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL); */

if (isset($_POST) && !empty($_POST)) //it can be $_GET doesn't matter
{
    if (isset($_POST['incomeTaxSlab'])) {

        if($tax_slab_range_start==0 && $tax_slab_range_end==0){
            $_SESSION['msg1'] = "Set Range"; 
            header("Location: ../addIncomeTaxSlab?st=$tax_slab_type");
            exit;
        }
        $m->set_data('tax_slab_range_start', $tax_slab_range_start);
        $m->set_data('tax_slab_range_end', $tax_slab_range_end);
        $m->set_data('tax_slab_percentage', $tax_slab_percentage);
        $m->set_data('tax_slab_year', $tax_slab_year);
        $m->set_data('tax_slab_type', $tax_slab_type);
        $m->set_data('tax_slab_age_group', $tax_slab_age_group);
        $m->set_data('tax_slab_remark', $tax_slab_remark);
        $m->set_data('added_by', $bms_admin_id);
        $m->set_data('added_date', date("Y-m-d H:i:s"));

        $a1 = array(
            'tax_slab_range_start' => $m->get_data('tax_slab_range_start'),
            'tax_slab_range_end' => $m->get_data('tax_slab_range_end'),
            'tax_slab_percentage' => $m->get_data('tax_slab_percentage'),
            'tax_slab_year' => $m->get_data('tax_slab_year'),
            'tax_slab_type' => $m->get_data('tax_slab_type'),
            'tax_slab_age_group' => $m->get_data('tax_slab_age_group'),
            'tax_slab_remark' => $m->get_data('tax_slab_remark'),
        );

        if ($tax_slab_id > 0) {
            $tax_slab_id = $d->sanitizeActionIdAsInt($tax_slab_id);
            $a1['modify_by'] = $m->get_data('added_by');
            $a1['modify_date'] = $m->get_data('added_date');
            $q = $d->update("tax_slab_master", $a1, "tax_slab_id = '$tax_slab_id'");
            $message = "Tax Slab Updated Successfully";
        } else {
            $a1['added_by'] = $m->get_data('added_by');
            $a1['added_date'] = $m->get_data('added_date');
            $q = $d->insert("tax_slab_master", $a1);
            $message = "Tax Slab Added Successfully";
        }

        if ($q == true) {
            $_SESSION['msg'] = $message;
            $d->insert_log("$society_id", "$bms_admin_id", "$created_by", $message);
            header("Location: ../incomeTaxSlabs?st=$tax_slab_type");
            exit();
        } else {
            $_SESSION['msg1'] = "Something Went Wrong"; 
            header("Location: ../incomeTaxSlabs?st=$tax_slab_type");
        }
    }

    if (isset($_POST['deleteIncomeTaxSlab'])) {
        $tax_slab_id = $d->sanitizeActionIdAsInt($_POST['tax_slab_id'] ?? ($tax_slab_id ?? 0));

        $q = $d->delete("tax_slab_master", "tax_slab_id = '$tax_slab_id'");
        $message = "Tax Slab Deleted Successfully";

        if ($q == true) {
            $_SESSION['msg'] = $message; 
            $d->insert_log("$society_id", "$bms_admin_id", "$created_by", $message);
            header("Location: ../incomeTaxSlabs?st=$tax_slab_type");
            exit();
        } else {
            $_SESSION['msg1'] = "Something Went Wrong"; 
            header("Location: ../incomeTaxSlabs?st=$tax_slab_type");
        }
    }
}