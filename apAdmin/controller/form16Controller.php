<?php 
include '../common/objectController.php';
/*ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);*/
extract($_POST);
  
if(isset($_POST) && !empty($_POST) )
{
    if(isset($changeTaxRegime)) {
        $a = array('tax_regime' => $tax_regime);
        $q = $d->update("users_master",$a,"user_id='$user_id' AND society_id='$society_id'");
        if($q) {
          if($tax_regime==0){
            $type = "New";
          }else{
            $type = "Old";
          }
            $d->insert_log("","$society_id","$_COOKIE[bms_admin_id]","$created_by","$type Tax Regime Change Successfully($user_id)");
            $_SESSION['msg']="$type Tax Regime Change Successfully";
            echo 1;
            
          } else {
            
            $_SESSION['msg'] = "Something Wrong";
            echo 0;
          }
    }

    if(isset($changeMetroCityType)) {
        $a = array('metro_city_type' => $metro_city_type);
        $q = $d->update("users_master",$a,"user_id='$user_id' AND society_id='$society_id'");
        if($q) {
          if($metro_city_type==0){
            $type = "Non-Metro City";
          }else{
            $type = "Metro City";
          }
            $d->insert_log("","$society_id","$_COOKIE[bms_admin_id]","$created_by","$type Successfully($user_id)");
            $_SESSION['msg']="$type Change Successfully";
            echo 1;
            
          } else {
            
            $_SESSION['msg'] = "Something Wrong";
            echo 0;
          }
    }
    
    if(isset($addDeductionRule)) {
      // echo "<pre>";
      // print_r($_POST);
      // die;

      $formulaArray = array();
     
      $a = array(
        "society_id" => $society_id,
        "tax_benefit_category_name" => $tax_benefit_category_name,
        "applicable_for" => $applicable_for,
        "max_tax_benefit_amount" => $max_tax_benefit_amount,
        "rule_type" => $rule_type,
        "amount_type" => $amount_type,
        "tax_benefit_year" => $tax_benefit_year,
        "tax_benefit_order" => $tax_benefit_order,
      );

      if($rule_type==0){
        $a['amount'] = $amount;
      }else if($rule_type==1){
        if($amount_type==0){
          $head_id = '';
          if($deduction_head)
          {
            $head_id = implode(',',$deduction_head);
          }
          $formulaArray = array('head_id'=>$head_id);

        }else if($amount_type==1){

          
            $head_id_1 = '';
            if($deduction_head_1)
            {
              $head_id_1 = implode(',',$deduction_head_1);
            }
            $head_id_2 = '';
            if($deduction_head_2)
            {
              $head_id_2 = implode(',',$deduction_head_2);
            }
            $head_id_3 = '';
            if($deduction_head_3)
            {
              $head_id_3 = implode(',',$deduction_head_3);
            }

            $formulaArray['case_1'] = array(
              'head_id'=>$head_id_1,
              'formula_modular_1'=>$formula_modular_11,
              'value'=>$value_1,
              'formula_modular_2'=>$formula_modular_21,
            );

            $formulaArray['case_2'] = array(
              'head_id'=>$head_id_2,
              'formula_modular_1'=>$formula_modular_12,
              'value'=>$value_2,
              'value_1'=>$value_3,
              'formula_modular_2'=>$formula_modular_22,
            );

            $formulaArray['case_3'] = array(
              'head_id'=>$head_id_3,
            );

        }
      }else if($rule_type==2){
        if($amount_type==1){
          if($formula_type==0){
            
            $formulaArray = array(
              'head_id'=>'',
              'formula_modular_1'=>$formula_modular_1,
              'formula_type'=>$formula_type,
              'value'=>$value,
              'formula_modular_2'=>$formula_modular_2,
            );
          }else if($formula_type==1){
            $valueArray = array();
            if(count($min)>0 && count($min)==count($max) && count($min)==count($slab_value)){
              for($i=0;$i<count($min);$i++){
                $val = array(
                  'min'=>$min[$i],
                  'max'=>$max[$i],
                  'value'=>$slab_value[$i],
                );
                array_push($valueArray,$val);
              }
            }

            $formulaArray = array(
              'head_id'=>'',
              'formula_modular_1'=>$slab_formula_modular_1,
              'formula_type'=>$formula_type,
              'value'=>($valueArray),
              'formula_modular_2'=>$slab_formula_modular_2,
            );
          }

          
        }
        
      }else if($rule_type==4){
        $a['amount'] = $amount;
      }

      if(!empty($formulaArray))
      {
        $formula_json = json_encode($formulaArray);
        $a['formula_json'] = $formula_json;
      }
      
      if($tax_benefit_category_id>0){
        $a['updated_date'] = date('Y-m-d H:i:s');
        $a['updated_by_id'] = $_COOKIE['bms_admin_id'];
        $a['updated_by_name'] = $created_by;
        $q = $d->update("tax_benefit_category",$a,"tax_benefit_category_id = '$tax_benefit_category_id'");

        if ($q>0) {
            $d->insert_log("", "$society_id", "$_COOKIE[bms_admin_id]", "$created_by", "Tax Benefit Category Updated ($tax_benefit_category_id)");

            $_SESSION['msg'] = "Tax Benefit Category Successfully Updated.";
            header("Location: ../taxBenefitCategory?year=$tax_benefit_year");
            exit;
        } else {

            $_SESSION['msg1'] = "Something Wrong!";
            header("Location: ../taxBenefitCategory?year=$tax_benefit_year");
            exit;
        }

      }else{
        $a['created_date'] = date('Y-m-d H:i:s');
        $a['created_by_id'] = $_COOKIE['bms_admin_id'];
        $a['created_by_name'] = $created_by;
        
        $q = $d->insert("tax_benefit_category",$a);
        

        if ($q>0) {
          $tax_benefit_category_id = $con->insert_id;
          $d->insert_log("", "$society_id", "$_COOKIE[bms_admin_id]", "$created_by", "Tax Benefit Category Added ($tax_benefit_category_id)");

          $_SESSION['msg'] = "Tax Benefit Category Successfully Added.";
          header("Location: ../taxBenefitCategory?year=$tax_benefit_year");
          exit;
        } else {

            $_SESSION['msg1'] = "Something Wrong!";
            header("Location: ../taxBenefitCategory?year=$tax_benefit_year");
            exit;
        }
      }

      
        
    }

  if(isset($changeTdsRuleType)) {
    
    

    $q1 = $d->select("tds_deduction_rules_master","user_id='$user_id' AND society_id='$society_id'");
    
    if(mysqli_num_rows($q1)>0){
      $a = array(
        'rule_type' => $rule_type,
        'updated_date' => date('Y-m-d H:i:s'),
        'updated_by_id' => $_COOKIE['bms_admin_id'],
        'updated_by_name' => $created_by,
  
      );
      $q = $d->update("tds_deduction_rules_master",$a,"user_id='$user_id' AND society_id='$society_id'");
    }else{
      
      $a = array(
        'rule_type' => $rule_type,
        'user_id' => $user_id,
        'society_id' => $society_id,
        'created_date' => date('Y-m-d H:i:s'),
        'created_by_id' => $_COOKIE['bms_admin_id'],
        'created_by_name' => $created_by,
        'updated_date' => date('Y-m-d H:i:s'),
        'updated_by_id' => $_COOKIE['bms_admin_id'],
        'updated_by_name' => $created_by,
  
      );
      $q = $d->insert("tds_deduction_rules_master",$a);
    }
    
    if($q) {
      
      $d->insert_log("","$society_id","$_COOKIE[bms_admin_id]","$created_by","TDS Deduction Rule Change Successfully($user_id)");
      $_SESSION['msg']="$type TDS Deduction Rule Change Successfully";
      echo 1;
      
    } else {
      
      $_SESSION['msg'] = "Something Wrong";
      echo 0;
    }
  }

  if(isset($importTaxBenefitCategory)) {
    

    $idCount = count($tax_benefit_category_id);
    $importCategoryIds = array_map('intval', (array)$tax_benefit_category_id);
    $importCategoryIds = array_values(array_filter($importCategoryIds));
    $taxBenefitCategoryMap = array();
    if (!empty($importCategoryIds)) {
      $importCategoryIdsIn = implode(',', $importCategoryIds);
      $q1Prefetch = $d->select("tax_benefit_category", "tax_benefit_category_id IN ($importCategoryIdsIn)");
      while ($catRow = mysqli_fetch_array($q1Prefetch)) {
        $taxBenefitCategoryMap[(int)$catRow['tax_benefit_category_id']] = $catRow;
      }
    }
    for ($i = 0; $i < $idCount; $i++) {
      $data = isset($taxBenefitCategoryMap[(int)$tax_benefit_category_id[$i]]) ? $taxBenefitCategoryMap[(int)$tax_benefit_category_id[$i]] : null;
      if (!$data) {
        continue;
      }
      $a = array(
        "society_id" => $society_id,
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
        "created_by_id" => $_COOKIE['bms_admin_id'],
        "created_by_name" => $created_by,
      );
    
      $q = $d->insert("tax_benefit_category",$a);
      if ($q>0) {
        $id = $con->insert_id;
        $d->insert_log("", "$society_id", "$_COOKIE[bms_admin_id]", "$created_by", "Tax Benefit Category Added ($id)");
      }
    }

      if ($q>0) {
        
        $_SESSION['msg'] = "Tax Benefit Category Import Successfully.";
        header("Location: ../taxBenefitCategory?year=$year");
        exit;
      } else {

          $_SESSION['msg1'] = "Something Wrong!";
          header("Location: ../taxBenefitCategory?year=$year");
          exit;
      }
    

    
      
  }

  if(isset($importTaxSlab)) {
    

    $idCount = count($tax_slab_id);
    $importSlabIds = array_map('intval', (array)$tax_slab_id);
    $importSlabIds = array_values(array_filter($importSlabIds));
    $taxSlabMap = array();
    if (!empty($importSlabIds)) {
      $importSlabIdsIn = implode(',', $importSlabIds);
      $q1Prefetch = $d->select("tax_slab_master", "tax_slab_id IN ($importSlabIdsIn)");
      while ($slabRow = mysqli_fetch_array($q1Prefetch)) {
        $taxSlabMap[(int)$slabRow['tax_slab_id']] = $slabRow;
      }
    }
    for ($i = 0; $i < $idCount; $i++) {
      $data = isset($taxSlabMap[(int)$tax_slab_id[$i]]) ? $taxSlabMap[(int)$tax_slab_id[$i]] : null;
      if (!$data) {
        continue;
      }
      $a = array(
        "society_id" => $society_id,
        "tax_slab_range_start" => $data['tax_slab_range_start'],
        "tax_slab_range_end" => $data['tax_slab_range_end'],
        "tax_slab_percentage" => $data['tax_slab_percentage'],
        "tax_slab_type" => $data['tax_slab_type'],
        "tax_slab_age_group" => $data['tax_slab_age_group'],
        "tax_slab_remark" => $data['tax_slab_remark'],
        "tax_slab_year" => $year,
      );
    
      $q = $d->insert("tax_slab_master",$a);
      if ($q>0) {
        $id = $con->insert_id;
        $d->insert_log("", "$society_id", "$_COOKIE[bms_admin_id]", "$created_by", "Income Tax Slab Added ($id)");
      }
    }

      if ($q>0) {
        
        $_SESSION['msg'] = "Income Tax Slab Import Successfully.";
        header("Location: ../incomeTaxSlabs?year=$year&st=$st");
        exit;
      } else {

          $_SESSION['msg1'] = "Something Wrong!";
          header("Location: ../incomeTaxSlabs?year=$year&st=$st");
          exit;
      }
    

    
      
  }
  if(isset($importTaxBenefitSubCategory)) {
    

    $idCount = count($tax_benefit_sub_category_id);
    $importSubCategoryIds = array();
    for ($i = 0; $i < $idCount; $i++) {
      $idArray = explode('~', $tax_benefit_sub_category_id[$i]);
      if (isset($idArray[0]) && (int)$idArray[0] > 0) {
        $importSubCategoryIds[] = (int)$idArray[0];
      }
    }
    $importSubCategoryIds = array_values(array_unique($importSubCategoryIds));
    $taxBenefitSubCategoryMap = array();
    if (!empty($importSubCategoryIds)) {
      $importSubCategoryIdsIn = implode(',', $importSubCategoryIds);
      $q1Prefetch = $d->select("tax_benefit_sub_category", "tax_benefit_sub_category_id IN ($importSubCategoryIdsIn)");
      while ($subRow = mysqli_fetch_array($q1Prefetch)) {
        $taxBenefitSubCategoryMap[(int)$subRow['tax_benefit_sub_category_id']] = $subRow;
      }
    }
    for ($i = 0; $i < $idCount; $i++) {
      $idArray = explode('~',$tax_benefit_sub_category_id[$i]);
      $data = isset($taxBenefitSubCategoryMap[(int)$idArray[0]]) ? $taxBenefitSubCategoryMap[(int)$idArray[0]] : null;
      if (!$data) {
        continue;
      }

      $a = array(
        "society_id" => $society_id,
        "tax_benefit_category_id" => $idArray[1],
        "tax_benefit_sub_category_name" => $data['tax_benefit_sub_category_name'],
        "created_date" => date('Y-m-d H:i:s'),
        "updated_date" => date('Y-m-d H:i:s'),
        "created_by_id" => $_COOKIE['bms_admin_id'],
        "updated_by_id" => $_COOKIE['bms_admin_id'],
        "created_by_name" => $created_by,
        "updated_by_name" => $created_by,
      );
    
      $q = $d->insert("tax_benefit_sub_category",$a);
      if ($q>0) {
        $id = $con->insert_id;
        $d->insert_log("", "$society_id", "$_COOKIE[bms_admin_id]", "$created_by", "Tax Benefit Sub-Category Added ($id)");
      }
    }

      if ($q>0) {
        
        $_SESSION['msg'] = "Tax Benefit Sub-Category Import Successfully.";
        header("Location: ../taxBenefitSubCategory?year=$year");
        exit;
      } else {

          $_SESSION['msg1'] = "Something Wrong!";
          header("Location: ../taxBenefitSubCategory?year=$year");
          exit;
      }
    

    
      
  }



}
?>