<?php
include '../common/objectController.php';

if (isset($_POST) && !empty($_POST)) {
    if (isset($addDeveloperNotes)) {
        //api document upload
        $allowedExtensions = array("pdf", "doc", "docx", "txt");
        $uploadedLayout = $_FILES['api_document_file']['tmp_name'];
        $layoutExtension = pathinfo($_FILES['api_document_file']['name'], PATHINFO_EXTENSION);

        if (file_exists($uploadedLayout)) {
            if (in_array($layoutExtension, $allowedExtensions)) {
                $newLayoutFileName = rand() . "_apidocument." . $layoutExtension;
                move_uploaded_file($uploadedLayout, "../../img/api_document_file/" . $newLayoutFileName);
                $api_document_file = $newLayoutFileName;
            } else {
                $_SESSION['msg1'] = "Failed to upload API Documents. Please try again.";
                header("Location: ../manageDevelopernotes");
                exit;
            }
        } else {
            $api_document_file = '';
        }
        // end api document upload
        //start api postman collection
        $allowedExtensions = array("json");
        $uploadedLayout = $_FILES['api_postmen_collection']['tmp_name'];
        $layoutExtension = pathinfo($_FILES['api_postmen_collection']['name'], PATHINFO_EXTENSION);

        if (file_exists($uploadedLayout)) {
            if (in_array($layoutExtension, $allowedExtensions)) {
                $newLayoutFileName = rand() . "_apicollection." . $layoutExtension;
                move_uploaded_file($uploadedLayout, "../../img/api_postmen_collection/" . $newLayoutFileName);
                $api_postmen_collection = $newLayoutFileName;
            } else {
                $_SESSION['msg1'] = "Failed to upload Postman Collection. Please try again.";
                header("Location: ../manageDevelopernotes");
                exit;
            }
        } else {
            $api_postmen_collection = '';
        }
        //end api postman collection

        $m->set_data('company_selection', test_input($company_selection));
        $m->set_data('integration_type', test_input($integration_type));
        $m->set_data('integration_name', test_input($integration_name));
        $m->set_data('integration_description', test_input($integration_description));
        $m->set_data('api_document_url', test_input($api_document_url));
        $m->set_data('api_document_file', $api_document_file);
        $m->set_data('api_postmen_collection', $api_postmen_collection);
        $m->set_data('anydesk_id', test_input($anydesk_id));
        $m->set_data('anydesk_password', test_input($anydesk_password));
        $m->set_data('contact_person_name', test_input($contact_person_name));
        $m->set_data('contact_person_number', test_input($contact_person_number));
        $m->set_data('created_date', date('Y-m-d H:i:s'));
        $a = array(
            'company_selection' => $m->get_data('company_selection'),
            'integration_type' => $m->get_data('integration_type'),
            'integration_name' => $m->get_data('integration_name'),
            'integration_description' => $m->get_data('integration_description'),
            'api_document_url' => $m->get_data('api_document_url'),
            'api_document_file' => $m->get_data('api_document_file'),
            'api_postmen_collection' => $m->get_data('api_postmen_collection'),
            'anydesk_id' => $m->get_data('anydesk_id'),
            'anydesk_password' => $m->get_data('anydesk_password'),
            'contact_person_name' => $m->get_data('contact_person_name'),
            'contact_person_number' => $m->get_data('contact_person_number'),
            'created_date' => $m->get_data('created_date')
        );
        $q = $d->insert("developer_note_master", $a);
        if ($q > 0) {
            $d->insert_log("$society_id", "$bms_admin_id", "$created_by", "Developer Notes Added");
            $_SESSION['msg'] = "Developer Notes Added";
            header("location:../manageDevelopernotes");
        } else {
            $_SESSION['msg1'] = "Something Wrong";
            header("location:../manageDevelopernotes");
        }
    }
    if (isset($editDeveloperNotes)) {


        $allowedExtensions = array("pdf", "doc", "docx", "txt");
        $uploadedLayout = $_FILES['api_document_file']['tmp_name'];
        $layoutExtension = pathinfo($_FILES['api_document_file']['name'], PATHINFO_EXTENSION);

        if (file_exists($uploadedLayout)) {
            if (in_array($layoutExtension, $allowedExtensions)) {
                $newLayoutFileName = rand() . "_apidocument." . $layoutExtension;
                move_uploaded_file($uploadedLayout, "../../img/api_document_file/" . $newLayoutFileName);
                $api_document_file = $newLayoutFileName;
            } else {
                $_SESSION['msg1'] = "Failed to upload API Documents. Please try again.";
                header("Location: ../manageDevelopernotes");
                exit;
            }
        } else {
            $api_document_file = $api_document_file_old;
        }
        
        $allowedExtensions = array("json");
        $uploadedLayout = $_FILES['api_postmen_collection']['tmp_name'];
        $layoutExtension = pathinfo($_FILES['api_postmen_collection']['name'], PATHINFO_EXTENSION);

        if (file_exists($uploadedLayout)) {
            if (in_array($layoutExtension, $allowedExtensions)) {
                $newLayoutFileName = rand() . "_apicollection." . $layoutExtension;
                move_uploaded_file($uploadedLayout, "../../img/api_postmen_collection/" . $newLayoutFileName);
                $api_postmen_collection = $newLayoutFileName;
            } else {
                $_SESSION['msg1'] = "Failed to upload Postman Collection. Please try again.";
                header("Location: ../manageDevelopernotes");
                exit;
            }
        } else {
            $api_postmen_collection = $api_postmen_collection_old;
        }


        $m->set_data('company_selection', test_input($company_selection));
        $m->set_data('integration_type', test_input($integration_type));
        $m->set_data('integration_name', test_input($integration_name));
        $m->set_data('integration_description', test_input($integration_description));
        $m->set_data('api_document_url', test_input($api_document_url));
        $m->set_data('api_document_file', $api_document_file);
        $m->set_data('api_postmen_collection', $api_postmen_collection);
        $m->set_data('anydesk_id', test_input($anydesk_id));
        $m->set_data('anydesk_password', test_input($anydesk_password));
        $m->set_data('contact_person_name', test_input($contact_person_name));
        $m->set_data('contact_person_number', test_input($contact_person_number));
        $m->set_data('created_date', date('Y-m-d H:i:s'));
        $a = array(
            'company_selection' => $m->get_data('company_selection'),
            'integration_type' => $m->get_data('integration_type'),
            'integration_name' => $m->get_data('integration_name'),
            'integration_description' => $m->get_data('integration_description'),
            'api_document_url' => $m->get_data('api_document_url'),
            'api_document_file' => $m->get_data('api_document_file'),
            'api_postmen_collection' => $m->get_data('api_postmen_collection'),
            'anydesk_id' => $m->get_data('anydesk_id'),
            'anydesk_password' => $m->get_data('anydesk_password'),
            'contact_person_name' => $m->get_data('contact_person_name'),
            'contact_person_number' => $m->get_data('contact_person_number'),
            'created_date' => $m->get_data('created_date')
        );

        $q = $d->update("developer_note_master", $a, "developer_note_id='$developer_note_id'");
        if ($q > 0) {
            $d->insert_log("$society_id", "$bms_admin_id", "$created_by", "Developer Notes Updated");
            $_SESSION['msg'] = "Developer Notes Updated";
            header("location:../manageDevelopernotes");
        } else {
            $_SESSION['msg1'] = "Something Wrong";
            header("location:../manageDevelopernotes");
        }
    } else if (isset($developer_note_id_delete)) {
        $developer_note_id_delete = $d->sanitizeActionIdAsInt($developer_note_id_delete);
        $q = $d->delete("developer_note_master", "developer_note_id='$developer_note_id_delete'");
        if ($q == TRUE) {
            $_SESSION['msg'] = "Developer Notes Deleted";
            header("Location: ../manageDevelopernotes");
        } else {
            $_SESSION['msg1'] = "Something Wrong";
            header("Location: ../manageDevelopernotes");
        }
    }
}
