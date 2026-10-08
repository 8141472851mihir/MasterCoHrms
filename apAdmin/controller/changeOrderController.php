<?php 
include '../common/objectController.php';
extract($_POST);

if(isset($_POST) && !empty($_POST) )
{
    if(isset($_POST['changeSocietyMenuOrder']))
    {
        $position = $_POST['position'];
        $i=1;
        foreach($position as $k=>$v)
        {
            $explode_id = explode("-",$v);
            $a22 =array(
                'menu_sequence'=> $i
            );
            $d->update("resident_app_menu_society",$a22,"society_id='$explode_id[1]' and app_menu_society_id='$explode_id[0]'");
            $i++;
        }
        if($d>0){
            echo 1;
        }else{
            echo 0;
        }
    }
}
?>