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

    if(isset($addPreviousTds)) {
      
      
      $m->set_data('society_id', $society_id);
      $m->set_data('user_id', $user_id);
      $m->set_data('form16_year', $form16_year);
      $m->set_data('previous_tds', $previous_tds);
      $m->set_data('created_date', date('Y-m-d H:i:s'));
      $m->set_data('updated_date', date('Y-m-d H:i:s'));
      $m->set_data('created_by_id', $_COOKIE['bms_admin_id']);
      $m->set_data('updated_by_id', $_COOKIE['bms_admin_id']);
      $m->set_data('created_by_name', $created_by);
      $m->set_data('updated_by_name', $created_by);

      
      $previous_tds_document = "";
      
      if ($_FILES["previous_tds_document"]["tmp_name"] != null) {
        
        $file11=$_FILES["previous_tds_document"]["tmp_name"];
        $extId = pathinfo($_FILES['previous_tds_document']['name'], PATHINFO_EXTENSION);
        $extAllow=array("png","jpg","jpeg","pdf","doc","docx","ppt","pptx","csv","xls","xlsx","PNG","JPG","JPEG","PDF","DOC","DOCX","PPT","PPTX","XLS","XLSX");
        
        if(in_array($extId,$extAllow)) {
          
          $temp = explode(".", $_FILES["previous_tds_document"]["name"]);
          $previous_tds_document = "previous_tds_document_".$user_id.round(microtime(true)) . '.' . end($temp);
          $res = move_uploaded_file($_FILES["previous_tds_document"]["tmp_name"], "../../img/users/" . $previous_tds_document);
          
        }
      }
      $a = array(
        'society_id' => $m->get_data('society_id'),
        'user_id' => $m->get_data('user_id'),
        'form16_year' => $m->get_data('form16_year'),
        'previous_tds' => $m->get_data('previous_tds'),
        'previous_tds_document' => $previous_tds_document,
        'updated_date' => $m->get_data('updated_date'),
        'updated_by_id' => $m->get_data('updated_by_id'),
        'updated_by_name' => $m->get_data('updated_by_name'),
      );
      $sql = $d->select("form16_generated_master","user_id='$user_id' AND form16_year='$form16_year'");
      if(mysqli_num_rows($sql)>0){
        $q = $d->update("form16_generated_master",$a,"user_id='$user_id' AND form16_year='$form16_year'");
      }else{
        $a['created_date'] = date('Y-m-d H:i:s');
        $a['created_by_name'] =  $created_by;
        $a['created_by_id'] =  $_COOKIE['bms_admin_id'];
        $q = $d->insert("form16_generated_master",$a);
        $form16_generated_id = $con->insert_id;
      }
        
      if($q) {
          $d->insert_log("","$society_id","$_COOKIE[bms_admin_id]","$created_by","Previous TDS Added Successfully($form16_generated_id)");
          $_SESSION['msg']="Previous TDS Added Successfully";
      } else {
        $_SESSION['msg'] = "Something Wrong";
      }
      header("Location: ../generateForm16?bId=$bId&dId=$dId&year=$form16_year");
  }

  if(isset($form16Generate)) {

    //=========generate form16 data json
    $year = $form16_year;
    $yearArray = explode('-', $year);

    $startYear = $yearArray[0] . '-04';
    $endYear = $yearArray[1] . '-03';

    $userQ = $d->selectRow("um.user_id,um.society_id,um.member_date_of_birth,um.tax_regime,um.last_address,um.user_full_name,um.user_mobile,um.user_email,um.user_joining_date,um.metro_city_type,society_master.society_name,society_master.pan_number,society_master.tan_number,society_master.society_address,society_master.socieaty_logo,society_master.society_address,society_master.secretary_mobile,society_master.secretary_email,ubm.pan_card_no,ubm.pf_no,(SELECT IF(SUM(total_earning_salary),SUM(total_earning_salary),0) FROM salary_slip_master WHERE user_id='$user_id' AND (DATE_FORMAT(salary_start_date,'%Y-%m')>='$startYear' AND DATE_FORMAT(salary_start_date,'%Y-%m')<='$endYear')) AS joining_gross_salary,fgm.previous_tds,fgm.user_tax_regime,fgm.metro_city_type AS user_metro_city_type", "users_master AS um LEFT JOIN form16_generated_master AS fgm ON (fgm.user_id=um.user_id AND fgm.form16_year='$year') LEFT JOIN user_bank_master AS ubm ON (ubm.user_id=um.user_id AND ubm.is_primary=1 AND ubm.is_delete=0),block_master,floors_master,society_master", "um.block_id = block_master.block_id AND floors_master.floor_id = um.floor_id AND society_master.society_id=um.society_id AND um.user_id='$user_id' AND  um.delete_status=0");
    $userData = mysqli_fetch_array($userQ);

    
    $array_data = array();

    if ($userData) {
      if($userData['joining_gross_salary']>0)
      {
        
        $joining_gross_salary = $userData['joining_gross_salary'];
        

        // $tax_regime = 0;
        // $joining_gross_salary = round(750001);

        $array_data['Year'] = $year;
        $array_data['Tax Regime'] = $tax_regime;
        $array_data['Metro City Type'] = $metro_city_type;
        $array_data['Joining Gross Salary'] = $joining_gross_salary;


        $age = "";
        if ($tax_regime == '1' && $userData['member_date_of_birth'] != "0000-00-00" && $userData['member_date_of_birth'] != null && $userData['member_date_of_birth'] != '') {
            $date1 = date_create($userData['member_date_of_birth']);
            $date2 = date_create(date("Y-m-d"));
            $diff = date_diff($date1, $date2);
            $age = $diff->format("%Y");
        }


      // Calculate Deduction On Gross Salary
      $totalDeductions = 0.00;

      $i = 'a';
      $fromSalaryQ = $d->select('tax_benefit_category', "society_id='$society_id' AND active_status=1 AND delete_status=0 AND rule_type!=2 AND rule_type!=4  AND (applicable_for=2 OR applicable_for='$tax_regime') AND tax_benefit_year = '$year'", "ORDER BY tax_benefit_order ASC");

      $claimApprovedByCategory = array();
      $claimAllByCategory = array();
      $claimApprovedQ = $d->selectRow(
          "tax_benefit_category_id, SUM(approved_amount) AS approved_amount",
          "tax_benefit_document",
          "document_status=1 AND society_id='$society_id' AND tax_benefit_year='$year' AND user_id='$user_id'",
          "GROUP BY tax_benefit_category_id"
      );
      while ($claimApprovedRow = mysqli_fetch_assoc($claimApprovedQ)) {
          $claimApprovedByCategory[$claimApprovedRow['tax_benefit_category_id']] = $claimApprovedRow['approved_amount'];
      }
      $claimAllQ = $d->selectRow(
          "tax_benefit_category_id, IFNULL(SUM(approved_amount), 0) AS approved_amount",
          "tax_benefit_document",
          "society_id='$society_id' AND tax_benefit_year='$year' AND user_id='$user_id'",
          "GROUP BY tax_benefit_category_id"
      );
      while ($claimAllRow = mysqli_fetch_assoc($claimAllQ)) {
          $claimAllByCategory[$claimAllRow['tax_benefit_category_id']] = $claimAllRow['approved_amount'];
      }

      if (mysqli_num_rows($fromSalaryQ) > 0) {
          while ($fromSalaryData = mysqli_fetch_array($fromSalaryQ)) {
            $amount = 0.00;
            if ($fromSalaryData['rule_type'] == 0) {  // Flat Amount
                $amount = $fromSalaryData['amount'];
            } else if ($fromSalaryData['rule_type'] == 1) {  // From Salary
                $formulaArray = json_decode($fromSalaryData['formula_json'], TRUE);
                
                if($fromSalaryData['amount_type'] == 0){
                    if (isset($formulaArray['head_id']) && $formulaArray['head_id'] != '') {

                        $head_ids = $formulaArray['head_id'];

                        $sql = "SELECT IF(SUM(salary_slip_sub_master.earning_deduction_amount),SUM(salary_slip_sub_master.earning_deduction_amount),0) AS total_amount FROM salary_slip_sub_master 
                            INNER JOIN salary_slip_master ON salary_slip_master.salary_slip_id=salary_slip_sub_master.salary_slip_id
                            WHERE salary_slip_sub_master.user_id='$user_id' AND salary_slip_sub_master.society_id='$society_id' AND DATE_FORMAT(salary_slip_master.salary_start_date,'%Y-%m')>='$startYear' AND DATE_FORMAT(salary_slip_master.salary_start_date,'%Y-%m')<='$endYear' AND FIND_IN_SET(salary_earning_deduction_id,'$head_ids') ";

                        $data = $d->executeSql($sql, "row_array");

                        if (isset($data['total_amount'])) {
                            $amount = $data['total_amount'];
                        }
                    }
                }else if($fromSalaryData['amount_type'] == 1){
                    $amountArray = array();

                    if (isset($formulaArray['case_1'])) {

                        $claimAmount = isset($claimApprovedByCategory[$fromSalaryData['tax_benefit_category_id']]) ? $claimApprovedByCategory[$fromSalaryData['tax_benefit_category_id']] : 0.00;

                        $head_ids = $formulaArray['case_1']['head_id'];

                        $sql = "SELECT IF(SUM(salary_slip_sub_master.earning_deduction_amount),SUM(salary_slip_sub_master.earning_deduction_amount),0) AS total_amount FROM salary_slip_sub_master 
                            INNER JOIN salary_slip_master ON salary_slip_master.salary_slip_id=salary_slip_sub_master.salary_slip_id
                            WHERE salary_slip_sub_master.user_id='$user_id' AND salary_slip_sub_master.society_id='$society_id' AND DATE_FORMAT(salary_slip_master.salary_start_date,'%Y-%m')>='$startYear' AND DATE_FORMAT(salary_slip_master.salary_start_date,'%Y-%m')<='$endYear' AND FIND_IN_SET(salary_earning_deduction_id,'$head_ids') ";
                            
                        $data = $d->executeSql($sql, "row_array");
                        $amount = $data['total_amount'];
                        $formula = 0.00;
                        if ($formulaArray['case_1']['formula_modular_1'] != "" && $formulaArray['case_1']['value'] != "") {
                            if ($formulaArray['case_1']['formula_modular_1'] == '+') {
                                $formula = $amount + $formulaArray['case_1']['value'];
                            } else if ($formulaArray['case_1']['formula_modular_1'] == '-') {
                                $formula = $amount - $formulaArray['case_1']['value'];
                            } else if ($formulaArray['case_1']['formula_modular_1'] == '*') {
                                $formula = $amount * $formulaArray['case_1']['value'];
                            } else {
                                $formula = $amount / $formulaArray['case_1']['value'];
                            }
                            if ($formulaArray['case_1']['formula_modular_2'] != "") {
                                $formula = $formula / 100;
                            }
                            if($claimAmount > $formula){
                                $formula = $claimAmount - $formula;
                            }

                        }
                        
                        array_push($amountArray,$formula);
                        

                    }
                    if (isset($formulaArray['case_2'])) {
                        $head_ids = $formulaArray['case_2']['head_id'];

                        $sql = "SELECT IF(SUM(salary_slip_sub_master.earning_deduction_amount),SUM(salary_slip_sub_master.earning_deduction_amount),0) AS total_amount FROM salary_slip_sub_master 
                            INNER JOIN salary_slip_master ON salary_slip_master.salary_slip_id=salary_slip_sub_master.salary_slip_id
                            WHERE salary_slip_sub_master.user_id='$user_id' AND salary_slip_sub_master.society_id='$society_id' AND DATE_FORMAT(salary_slip_master.salary_start_date,'%Y-%m')>='$startYear' AND DATE_FORMAT(salary_slip_master.salary_start_date,'%Y-%m')<='$endYear' AND FIND_IN_SET(salary_earning_deduction_id,'$head_ids') ";

                        $data = $d->executeSql($sql, "row_array");
                        $amount = $data['total_amount'];
                        $formula = 0.00;
                        if($metro_city_type==0){
                            $metro_city_value = $formulaArray['case_2']['value'];
                        }else{
                            $metro_city_value = $formulaArray['case_2']['value_1'];
                        }
                        if ($formulaArray['case_2']['formula_modular_1'] != "" && $metro_city_value != "") {
                            if ($formulaArray['case_2']['formula_modular_1'] == '+') {
                                $formula = $amount + $metro_city_value;
                            } else if ($formulaArray['case_2']['formula_modular_1'] == '-') {
                                $formula = $amount - $metro_city_value;
                            } else if ($formulaArray['case_2']['formula_modular_1'] == '*') {
                                $formula = $amount * $metro_city_value;
                            } else {
                                $formula = $amount / $metro_city_value;
                            }
                            if ($formulaArray['case_2']['formula_modular_2'] != "") {
                                $formula = $formula / 100;
                            }
                        }
                        
                        array_push($amountArray,$formula);

                    }
                    if (isset($formulaArray['case_3'])) {
                        $head_ids = $formulaArray['case_3']['head_id'];

                        $sql = "SELECT IF(SUM(salary_slip_sub_master.earning_deduction_amount),SUM(salary_slip_sub_master.earning_deduction_amount),0) AS total_amount FROM salary_slip_sub_master 
                            INNER JOIN salary_slip_master ON salary_slip_master.salary_slip_id=salary_slip_sub_master.salary_slip_id
                            WHERE salary_slip_sub_master.user_id='$user_id' AND salary_slip_sub_master.society_id='$society_id' AND DATE_FORMAT(salary_slip_master.salary_start_date,'%Y-%m')>='$startYear' AND DATE_FORMAT(salary_slip_master.salary_start_date,'%Y-%m')<='$endYear' AND FIND_IN_SET(salary_earning_deduction_id,'$head_ids') ";

                        $data = $d->executeSql($sql, "row_array");
                        $amount = $data['total_amount'];
                        $formula = $amount;

                        array_push($amountArray,$formula);
                    }
                    
                    $amount = !empty($amountArray)?min($amountArray):0.00;
                    if ($amount > $fromSalaryData['max_tax_benefit_amount']) {
                        $amount = $fromSalaryData['max_tax_benefit_amount'];
                    }
                }
                

            }else if ($fromSalaryData['rule_type'] == 3) { // Claim By Employee

              $amount = isset($claimAllByCategory[$fromSalaryData['tax_benefit_category_id']]) ? $claimAllByCategory[$fromSalaryData['tax_benefit_category_id']] : 0;
              if($amount>$fromSalaryData['max_tax_benefit_amount']){
                  $amount=$fromSalaryData['max_tax_benefit_amount'];
              }
            }
            $totalDeductions += $amount;

            $array_data[$fromSalaryData['tax_benefit_category_name']] = number_format($amount, 2, '.', '');
          }
        }

        $totalDeductions = number_format($totalDeductions, 2, '.', '');
        $taxableIncome = number_format(($joining_gross_salary - $totalDeductions), 2, '.', '');
        $array_data['Aggregate of deductible amount'] = number_format($taxableIncome, 2, '.', '');

        $remainingAmount = 0.00;
        $taxAmount = 0.00;
        $appendAgeQry = "";
        if ($tax_regime == '1' && $age < 60) {
            $appendAgeQry .= " AND tax_slab_age_group=1 ";
        } else if ($tax_regime == '1' && $age >= 60 && $age <= 80) {
            $appendAgeQry .= " AND tax_slab_age_group=2 ";
        } else if ($tax_regime == '1' && $age > 80) {
            $appendAgeQry .= " AND tax_slab_age_group=3 ";
        }
        $taxSlabQ = $d->select("tax_slab_master", "society_id='$society_id' AND tax_slab_range_start<='$taxableIncome'  AND tax_slab_type='$tax_regime' AND tax_slab_year = '$year'  $appendAgeQry", "ORDER BY tax_slab_range_start ASC");

        $j = 0;

        while ($taxSlabData = mysqli_fetch_array($taxSlabQ)) {
          if ($j == 0) {
              $diff = $taxSlabData['tax_slab_range_end'] - $taxSlabData['tax_slab_range_start'];
              $remainingAmount = $taxableIncome - $diff;
          } else {
              if($taxableIncome < $taxSlabData['tax_slab_range_end'])
              {
                  $diff = $taxableIncome - $taxSlabData['tax_slab_range_start'];
              }else{
                  $diff = $taxSlabData['tax_slab_range_end'] - $taxSlabData['tax_slab_range_start'];
              }
              if ($taxSlabData['tax_slab_range_end'] > 0) {
                  $taxAmount += $diff * $taxSlabData['tax_slab_percentage'] / 100;
                  $remainingAmount = $remainingAmount - ($taxSlabData['tax_slab_range_end'] - $taxSlabData['tax_slab_range_start']);
              } else {
                  $taxAmount += $remainingAmount * $taxSlabData['tax_slab_percentage'] / 100;
              }
          }
          
          $j++;
        }
        $calculatedTax = $taxAmount;

        $array_data['Tax on total income (As Per Slab)'] = number_format($calculatedTax, 2, '.', '');

        // Calculate Extra Deduction On Calculated Tax Amount
        $taxBenefitQry = $d->select('tax_benefit_category', "society_id = '$society_id' AND active_status = 1 AND delete_status = 0 AND rule_type=4 AND (applicable_for = 2 OR applicable_for = $tax_regime) AND tax_benefit_year = '$year'", "ORDER BY tax_benefit_order ASC");
        if (mysqli_num_rows($taxBenefitQry) > 0) {
          $maxRelief = 0;
          $deductionOnCalculatedTax = 0;
          while ($taxBenefitData = mysqli_fetch_array($taxBenefitQry)) {
            if ($taxBenefitData['amount_type'] == 2) { // Max Relief
                $maxRelief = 0;
                if ($calculatedTax <= $taxBenefitData['amount']) {
                    $deductionOnCalculatedTax = $calculatedTax;
                    $maxRelief = $calculatedTax;
                    $calculatedTax -= $maxRelief;
                }
                
            } else if ($taxBenefitData['amount_type'] == 3) { // Relief Apply On
                $maxRelief = 0;
                if ($deductionOnCalculatedTax == 0 && $taxableIncome > $taxBenefitData['amount']) {
                    $actualTaxLiability = $taxableIncome - $taxBenefitData['amount'];

                    $maxRelief = (($calculatedTax - $actualTaxLiability) > 0) ? ($calculatedTax - $actualTaxLiability) : 0;
                    $deductionOnCalculatedTax = $maxRelief;
                    $calculatedTax -= $maxRelief;
                }                                                        
            }

            $array_data[$taxBenefitData['tax_benefit_category_name']] = number_format($maxRelief, 2, '.', '');
          }
        }

        $totalTaxPayable = number_format($calculatedTax, 2, '.', '');
        $fromTaxQ1 = $d->select('tax_benefit_category', "society_id='$society_id' AND active_status=1 AND tax_benefit_category.delete_status=0 AND rule_type=2  AND (applicable_for=2 OR applicable_for='$tax_regime')","ORDER BY tax_benefit_order ASC");

        if (mysqli_num_rows($fromTaxQ1) > 0) {
          while ($fromTaxData = mysqli_fetch_array($fromTaxQ1)) {
            
            $fromTaxAmount = 0.00;
            $formulaArray = json_decode($fromTaxData['formula_json'], TRUE);
            if ($fromTaxData['amount_type'] == 1 && $fromTaxData['formula_json'] != '') {

                if (isset($formulaArray['value']) && $formulaArray['value'] != '') {
                    $formula_type = (isset($formulaArray['formula_type']) && $formulaArray['formula_type'] != '') ? $formulaArray['formula_type'] : 0;

                    if ($formula_type == 1) {
                      
                        $formula = 0.00;
                        $percent = "";
                        if ($formulaArray['formula_modular_1'] != "" && $formulaArray['value'] != "") {

                            $formulaValue = 0;
                            foreach ($formulaArray['value'] as $formulaArrayValueData) {
                                if ($taxableIncome > $formulaArrayValueData['min'] && $taxableIncome <= $formulaArrayValueData['max']) {
                                    $formulaValue = $formulaArrayValueData['value'];
                                }
                            }

                            if ($formulaArray['formula_modular_1'] == '+') {
                                $formula = $totalTaxPayable + $formulaValue;
                            } else if ($formulaArray['formula_modular_1'] == '-') {
                                $formula = $totalTaxPayable - $formulaValue;
                            } else if ($formulaArray['formula_modular_1'] == '*') {
                                $formula = $totalTaxPayable * $formulaValue;
                            } else {
                                $formula = $totalTaxPayable / $formulaValue;
                            }
                            
                            if ($formulaArray['formula_modular_2'] != ""  && $formulaArray['formula_modular_1'] == '*') {
                                $formula = $formula / 100;
                            }
                        }
                        $fromTaxAmount = $formula;
                        $totalTaxPayable += $fromTaxAmount;
                        
                    } else if ($formula_type == 0) {
                        $formula = 0.00;
                        $percent = "";
                        if ($formulaArray['formula_modular_1'] != "" && $formulaArray['value'] != "") {
                          
                            if ($formulaArray['formula_modular_1'] == '+') {
                                $formula = $totalTaxPayable + $formulaArray['value'];
                            } else if ($formulaArray['formula_modular_1'] == '-') {
                                $formula = $totalTaxPayable - $formulaArray['value'];
                            } else if ($formulaArray['formula_modular_1'] == '*') {
                                $formula = $totalTaxPayable * $formulaArray['value'];
                            } else {
                                $formula = $totalTaxPayable / $formulaArray['value'];
                            }
                            // print_r($totalTaxPayable);  
                            
                            if ($formulaArray['formula_modular_2'] != ""  && $formulaArray['formula_modular_1'] == '*') {
                                $percent = ' ('.$formulaArray['value'] . '%)';
                                $formula = $formula / 100;
                            }
                        }
                        $fromTaxAmount = $formula;
                        $totalTaxPayable += $fromTaxAmount;
                    }
                }
            }
            $array_data[$fromTaxData['tax_benefit_category_name'].$percent] = number_format($fromTaxAmount, 2, '.', '');
          }
        }
        $netPayble = number_format($totalTaxPayable, 2, '.', '');

        $array_data['Total Tax'] = number_format($netPayble, 2, '.', '');

        // Calculate TDS Amount
        $previousTDS = $userData['previous_tds']!=null?$userData['previous_tds']:0.00;
        if ($userData['user_joining_date'] > date('Y-m-d',strtotime($startYear.'-01'))) {
          $array_data['Previous TDS'] = number_format($previousTDS, 2, '.', '');

          if ($previousTDS > $netPayble) {
            $netPayble = 0.00;
          } else {
              $netPayble = $netPayble - $previousTDS;
          }
        }

        $tdsPaid=0.00;
        $tdsQry = $d->selectRow("IFNULL(SUM(salary_slip_sub_master.earning_deduction_amount), 0) AS tdsPaid", "salary_slip_sub_master, salary_slip_master, salary_earning_deduction_type_master", "salary_slip_master.salary_slip_id = salary_slip_sub_master.salary_slip_id AND salary_slip_master.user_id = '$user_id' AND DATE_FORMAT(salary_slip_master.salary_start_date,'%Y-%m')>='$startYear' AND DATE_FORMAT(salary_slip_master.salary_start_date,'%Y-%m')<='$endYear' AND salary_slip_sub_master.salary_earning_deduction_id = salary_earning_deduction_type_master.salary_earning_deduction_id AND salary_earning_deduction_type_master.is_tds_head = 1");

        if(mysqli_num_rows($tdsQry)>0)
        {
            $tdsData = mysqli_fetch_array($tdsQry);
            $tdsPaid = $tdsData['tdsPaid'];

            if ($tdsPaid > $netPayble) {
                $netPayble = 0.00;
            } else {
                $netPayble = $netPayble - $tdsPaid;
            }
        }

        $array_data['TDS Deducted'] = number_format($tdsPaid, 2, '.', '');
        $array_data['Net Payable Tax'] = number_format($netPayble, 2, '.', '');
        
      }

    }

    $json_data = json_encode($array_data);

    //==================================

    if($form16_generated_id>0){
      $a = array(
        'is_generated' => 1,
        'form16_year' => $form16_year,
        'user_tax_regime' => $tax_regime,
        'metro_city_type' => $metro_city_type,
        'json_data' => $json_data,
        'updated_date' => date('Y-m-d H:i:s'),
        'updated_by_id' =>  $_COOKIE['bms_admin_id'],
        'updated_by_name' => $created_by,
      );
      $q = $d->update("form16_generated_master",$a,"form16_generated_id='$form16_generated_id'");
    }else{

      $sql = $d->select("form16_generated_master","user_id='$user_id' AND form16_year='$form16_year'");

      if(mysqli_num_rows($sql)>0){

        $a = array(
          'is_generated' => 1,
          'user_tax_regime' => $tax_regime,
          'metro_city_type' => $metro_city_type,
          'json_data' => $json_data,
          'updated_date' => date('Y-m-d H:i:s'),
          'updated_date' => date('Y-m-d H:i:s'),
          'updated_by_id' =>  $_COOKIE['bms_admin_id'],
          'updated_by_name' => $created_by,
        );

        $q = $d->update("form16_generated_master",$a,"user_id='$user_id' AND form16_year='$form16_year'");
      }else{

        $m->set_data('society_id', $society_id);
        $m->set_data('user_id', $user_id);
        $m->set_data('form16_year', $form16_year);
        $m->set_data('is_generated', 1);
        $m->set_data('user_tax_regime', $tax_regime);
        $m->set_data('metro_city_type', $metro_city_type);
        $m->set_data('created_date', date('Y-m-d H:i:s'));
        $m->set_data('updated_date', date('Y-m-d H:i:s'));
        $m->set_data('created_by_id', $_COOKIE['bms_admin_id']);
        $m->set_data('updated_by_id', $_COOKIE['bms_admin_id']);
        $m->set_data('created_by_name', $created_by);
        $m->set_data('updated_by_name', $created_by);

        $a = array(
          'society_id' => $m->get_data('society_id'),
          'user_id' => $m->get_data('user_id'),
          'form16_year' => $m->get_data('form16_year'),
          'is_generated' => $m->get_data('is_generated'),
          'user_tax_regime' => $m->get_data('user_tax_regime'),
          'metro_city_type' => $m->get_data('metro_city_type'),
          'json_data' => $json_data,
          'updated_date' => date('Y-m-d H:i:s'),
          'created_date' => $m->get_data('created_date'),
          'updated_date' => $m->get_data('updated_date'),
          'created_by_id' => $m->get_data('created_by_id'),
          'updated_by_id' => $m->get_data('updated_by_id'),
          'created_by_name' => $m->get_data('created_by_name'),
          'updated_by_name' => $m->get_data('updated_by_name'),
        );

        $q = $d->insert("form16_generated_master",$a);
        $form16_generated_id = $con->insert_id;
        
      }
    } 

    
    if($q) {
      $d->insert_log("","$society_id","$_COOKIE[bms_admin_id]","$created_by","Form16 Generated Successfully($form16_generated_id)");
      $_SESSION['msg']="Form16 Generated Successfully";    
      echo 1;
    } else {
      $_SESSION['msg'] = "Something Wrong";
      echo 0;
    }
    
    // header("Location: ../generateForm16?bId=$bId&dId=$dId&year=$form16_year");
}

  if ($_POST['status'] == "shareWithUser") {
    $a1 = array('
      share_with_user' => 1,
      'updated_date' => date('Y-m-d H:i:s'),
      'updated_by_id' => $_COOKIE['bms_admin_id'],
      'updated_by_name' => $created_by,
    );
    $q = $d->update('form16_generated_master', $a1, "form16_generated_id='$id'");
    if ($q > 0) {
        $d->insert_log("", "$society_id", "$_COOKIE[bms_admin_id]", "$created_by", "Shared With User ($id)");
        echo 1;
    } else {
        echo 0;
    }
  }
  if ($_POST['status'] == "notShareWithUser") {
      $a1 = array(
        'share_with_user' => 0,
        'updated_date' => date('Y-m-d H:i:s'),
        'updated_by_id' => $_COOKIE['bms_admin_id'],
        'updated_by_name' => $created_by,

      );
      $q = $d->update('form16_generated_master', $a1, "form16_generated_id='$id'");
      if ($q > 0) {
          $d->insert_log("", "$society_id", "$_COOKIE[bms_admin_id]", "$created_by", "Shared With User ($id)");
          echo 1;
      } else {
          echo 0;
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