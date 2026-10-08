<?php
include '../common/objectController.php';

if (isset($_POST) && !empty($_POST)) {
    extract($_POST);
    if (isset($addIdProofType) && $addIdProofType == 'addIdProofType') {
        $m->set_data('country_id', test_input($country_id));
        $m->set_data('id_proof_name', test_input($id_proof_name));
        $m->set_data('document_short_name',test_input($document_short_name));
        $m->set_data('id_proof_number_required', test_input($id_proof_number_required));
        $m->set_data('document_number_pattern',$document_number_pattern);
        $m->set_data('id_proof_pages', test_input($id_proof_pages));
        $m->set_data('created_by', $bms_admin_id);
        $m->set_data('id_proof_created_date', date('Y-m-d H:i:s'));

        if($document_min_length == '' || $document_min_length <= 0){
            $document_min_length = 1;
        }
        if($document_max_length == '' || $document_max_length <= 0){
            $document_max_length = 30;
        }

        $insert_data = array(
            "country_id" => $m->get_data('country_id'),
            "id_proof_name" => $m->get_data('id_proof_name'),
            "document_short_name" => $m->get_data('document_short_name'),
            "id_proof_number_required" => $m->get_data('id_proof_number_required'),
            "document_number_pattern" => $m->get_data('document_number_pattern'),
            "id_proof_pages" => $m->get_data('id_proof_pages'),
            "created_by" => $m->get_data('created_by'),
            "id_proof_created_date" => $m->get_data('id_proof_created_date')
        );

        $insert_data['document_min_length'] = $document_min_length;
        $insert_data['document_max_length'] = $document_max_length;

        $q = $d->insert("id_proof_master", $insert_data);
        if ($q === TRUE) {
            $_SESSION['msg'] = "ID Proof Type Added Successfully";
            $d->insert_log($society_id, $bms_admin_id, $created_by, "ID Proof Type $id_proof_name Added");
        } else {
            $_SESSION['msg1'] = "Something Went Wrong";
        }
        $redirectCountryId = (int)$m->get_data('country_id');
        header("Location: ../idProof?countryId=$redirectCountryId");
    } else if (isset($editIdProofType) && $editIdProofType == 'editIdProofType') {

        $m->set_data('id_proof_id', test_input($id_proof_id));
        $m->set_data('country_id', test_input($country_id));
        $m->set_data('id_proof_name', test_input($id_proof_name));
        $m->set_data('document_short_name',test_input($document_short_name));
        $m->set_data('id_proof_number_required', test_input($id_proof_number_required));
        $m->set_data('document_number_pattern',$document_number_pattern);
        $m->set_data('id_proof_pages', test_input($id_proof_pages));
        $m->set_data('updated_by', $bms_admin_id);
        $m->set_data('updated_date', date('Y-m-d H:i:s'));

        if($document_min_length == '' || $document_min_length <= 0){
            $document_min_length = 1;
        }
        if($document_max_length == '' || $document_max_length <= 0){
            $document_max_length = 30;
        }

        $update_data = array(
            "country_id" => $m->get_data('country_id'),
            "id_proof_name" => $m->get_data('id_proof_name'),
            "document_short_name" => $m->get_data('document_short_name'),
            "id_proof_number_required" => $m->get_data('id_proof_number_required'),
            "document_number_pattern" => $m->get_data('document_number_pattern'),
            "id_proof_pages" => $m->get_data('id_proof_pages'),
            "updated_by" => $m->get_data('updated_by'),
            "updated_date" => $m->get_data('updated_date')
        );

        $update_data['document_min_length'] = $document_min_length;
        $update_data['document_max_length'] = $document_max_length;

        $where = "id_proof_id = '" . $m->get_data('id_proof_id') . "'";
        $q = $d->update("id_proof_master", $update_data, $where);

        if ($q === TRUE) {
            $_SESSION['msg'] = "ID Proof Type Updated Successfully";
            $d->insert_log($society_id, $bms_admin_id, $created_by, "ID Proof Type {$m->get_data('id_proof_name')} Updated");
        } else {
            $_SESSION['msg1'] = "Something Went Wrong";
        }

        $redirectCountryId = (int)$m->get_data('country_id');
        header("Location: ../idProof?countryId=$redirectCountryId");
    } else if (isset($deleteIdProofType) && $deleteIdProofType == 'deleteIdProofType') {
        $m->set_data('id_proof_id', test_input($id_proof_id));
        $delete_data = array(
            "is_deleted" => 1,
        );
        $where = "id_proof_id = '" . $m->get_data('id_proof_id') . "'";
        $q = $d->update("id_proof_master", $delete_data, $where);
        if ($q === TRUE) {
            $_SESSION['msg'] = "ID Proof Type Deleted Successfully";
            $d->insert_log($society_id, $bms_admin_id, $created_by, "ID Proof Type {$m->get_data('id_proof_name')} Deleted");
        } else {
            $_SESSION['msg1'] = "Something Went Wrong";
        }

        $redirectCountryId = (isset($country_id) && (int)$country_id > 0) ? (int)$country_id : 101;
        header("Location: ../idProof?countryId=$redirectCountryId");
    }
}
