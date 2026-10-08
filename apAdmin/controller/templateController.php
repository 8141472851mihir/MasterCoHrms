<?php
include '../common/objectController.php';
if(isset($_POST) && !empty($_POST))
{
    if (isset($addTemplate) && $addTemplate == 'addTemplate') {
    $template_text = html_entity_decode(trim($_POST['template_text']));
    $len = strlen($template_text);
    if ($len < 100) {
        $_SESSION['msg1'] = "Minimum 100 letters required.";
        header("Location: ../addTemplate");
        exit;
    }
    $template_sub = trim($_POST['template_sub']);

    $m->set_data('template_name', $template_name);
    $m->set_data('template_sub', $template_sub);
    $m->set_data('template_text', $template_text);
    $data = array(
        'template_name' => $m->get_data('template_name'),
        'template_sub' => $m->get_data('template_sub'),
        'template_text' => $m->get_data('template_text')
    );
    if (isset($template_id) && $template_id != '') {
        $data['updated_dt'] = date("Y-m-d H:i:s");
        $data['updated_by_id'] = $bms_admin_id;
        $query = $d->update("template_master", $data, "template_id='$template_id'");
        $successMessage = "Template updated successfully";
        $failureMessage = "Something went wrong!";
        $redirectLocation = "../manageTemplate";
    } else {
        $data['created_dt'] = date("Y-m-d H:i:s");
        $data['created_by_id'] = $bms_admin_id;
        $query = $d->insert("template_master", $data);
        $successMessage = "Template added successfully";
        $failureMessage = "Something went wrong!";
        $redirectLocation = "../manageTemplate";
    }
    if ($query === true) {
        $_SESSION['msg'] = $successMessage;
    } else {
        $_SESSION['msg'] = $failureMessage;
    }
    header("Location: $redirectLocation");
    exit();
}else if(isset($deleteTemplate) && $deleteTemplate =='deleteTemplate' && isset($template_id) && $template_id!=''){
        $d1 =array(
        'template_status'=> 1,
         );
         $que=$d->update("template_master",$d1,"template_id='$template_id'");
         if($que==true){
            $_SESSION['msg'] = "template deleted successfully";
             header("location:../manageTemplate");
             exit(); 
         }else{
            $_SESSION['msg'] = "something Went Wrong!!";
            header("location:../manageTemplate");
            exit(); 
         }

    }
    }
?>