<?php
include '../common/objectController.php';
if(isset($_POST) && !empty($_POST))
{
    extract($_POST);
    if(isset($moveToInProgress))
    {
        $a = array(
          'status'=> 2,
          'modified_by'=> $_SESSION['bms_admin_id'],
          'modified_date'=> date("Y-m-d H:i:s")
        );
        $q = $d->update("demo_request_from_chpl",$a,"demo_id = '$demo_id'");

        if($q)
        {
            $_SESSION['msg']="Moved To In Progress";
            $d->insert_log_specific("$society_id","$_SESSION[bms_admin_id]","$created_by","$demo_id moved to In Progress","6");
            header("Location: ../demoRequestChpl");exit;
        }
        else
        {
            $_SESSION['msg1']="Something Wrong";
            header("Location: ../demoRequestChpl");exit;
        }
    }
    elseif(isset($moveToCompleted))
    {
        $a = array(
          'status'=> 3,
          'modified_by'=> $_SESSION['bms_admin_id'],
          'modified_date'=> date("Y-m-d H:i:s")
        );
        $q = $d->update("demo_request_from_chpl",$a,"demo_id = '$demo_id'");

        if($q)
        {
            $_SESSION['msg']="Moved To Completed";
            $d->insert_log_specific("$society_id","$_SESSION[bms_admin_id]","$created_by","$demo_id moved to Completed","6");
            header("Location: ../demoRequestChpl");exit;
        }
        else
        {
            $_SESSION['msg1']="Something Wrong";
            header("Location: ../demoRequestChpl");exit;
        }
    }

}
?>