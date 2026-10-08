<?php 
include '../common/objectController.php';
if(isset($_POST) && !empty($_POST))
{
	extract($_POST);
	$country_id = isset($country_id) ? $d->sanitizeActionIdAsInt($country_id) : 0;
	$state_id = isset($state_id) ? $d->sanitizeActionIdAsInt($state_id) : 0;
	$city_id = isset($city_id) ? $d->sanitizeActionIdAsInt($city_id) : 0;
	$domain_id = (isset($domain_id) && $domain_id !== '') ? $d->sanitizeActionIdAsInt($domain_id) : null;
	if(isset($AddCity))
	{
		if(!empty($name))
		{
			$nameEsc = $d->escapeSqlString($name);
			$city = $d->select("cities","name='$nameEsc' AND state_id='$state_id'");
			if(mysqli_num_rows($city) == 0)
			{
				$a1 = array(
					'country_id'=>$country_id,
					'state_id'=>$state_id,
					'name'=>$name,
					'domain_id'=>$domain_id,
				);
				$q = $d->insert("cities",$a1);
				if($q > 0)
				{
					$_SESSION['msg']="City Added Successfully.";
					$d->insert_log("0","$bms_admin_id","$created_by","City Added Successfully.");
					header("location:../manageCity?countryId=$country_id&sId=$state_id");  
					exit();
				}else{
					$_SESSION['msg1']="Something Went Wrong?";
					header("location:../manageCity?countryId=$country_id&sId=$state_id");  
					exit();
				}
			}else{
				$_SESSION['msg1']="City Already Added";
				header("location:../manageCity?countryId=$country_id&sId=$state_id");
				exit();
			}
		}else{
			$_SESSION['msg1']="City Name Required?";
			header("location:../manageCity?countryId=$country_id&sId=$state_id");  
			exit();
		}
	}
	
	if(isset($EditCity))
	{
		if(!empty($name))
		{
			$nameEsc = $d->escapeSqlString($name);
			$city = $d->select("cities","name='$nameEsc' AND state_id='$state_id' AND city_id !='$city_id'");
			if(mysqli_num_rows($city) == 0)
			{
				$a1 = array(
					'country_id'=>$country_id,
					'state_id'=>$state_id,
					'name'=>$name,
					'domain_id'=>$domain_id,
				);
				$q = $d->update("cities",$a1,"city_id=$city_id");
				if($q > 0)
				{
					$_SESSION['msg']="City Updated Successfully.";
					$d->insert_log("0","$bms_admin_id","$created_by","City Updated Successfully.");
					header("location:../manageCity?countryId=$country_id&sId=$state_id");  
					exit();
				}else{
					$_SESSION['msg1']="Something Went Wrong?";
					header("location:../manageCity?countryId=$country_id&sId=$state_id");  
					exit();
				}
			}else{
				$_SESSION['msg1']="State Already Added";
				header("location:../manageCity?countryId=$country_id&sId=$state_id");
				exit();
			}
		}else{
			$_SESSION['msg1']="City Name Required?";
			header("location:../manageCity?countryId=$country_id&sId=$state_id");
			exit();
		}
	}

	if(isset($delectCity))
	{
		$q1 = $d->select("society_master","city_id=$city_id");
		if(mysqli_num_rows($q1) == 0)
		{
			$q = $d->delete("cities","country_id=$country_id AND state_id=$state_id AND city_id=$city_id");
			if($q > 0)
			{
				$_SESSION['msg']="City Deleted Successfully.";
				$d->insert_log("0","$bms_admin_id","$created_by","City Deleted Successfully.");
				header("location:../manageCity?countryId=$country_id&sId=$state_id");
				exit();
			}else{
				$_SESSION['msg1']="Something Went Wrong?";
				header("location:../manageCity?countryId=$country_id&sId=$state_id");
				exit();
			}
		}else{
			$_SESSION['msg1']="Delete First Society Then Delete City";
			header("location:../manageCity?countryId=$country_id&sId=$state_id");
			exit();
		}
	}

	if(isset($EditCityDomain))
	{
		if(!empty($city_id))
		{
			$a1 = array(
				'domain_id'=>$domain_id,
			);
			$q = $d->update("cities",$a1,"city_id=$city_id");
			$redirect_sId = isset($_POST['sId']) && $_POST['sId'] != '' ? $d->sanitizeReportFilterIdAsInt($_POST['sId']) : '';
			$redirect_url = "../cities" . ($redirect_sId != '' ? "?sId=" . $redirect_sId : '');
			
			if($q > 0)
			{
				$_SESSION['msg']="City Domain Updated Successfully.";
				$d->insert_log("0","$bms_admin_id","$created_by","City Domain Updated Successfully.");
				header("location:" . $redirect_url);  
				exit();
			}else{
				$_SESSION['msg1']="Something Went Wrong?";
				header("location:" . $redirect_url);  
				exit();
			}
		}else{
			$_SESSION['msg1']="City ID Required?";
			$redirect_sId = isset($_POST['sId']) && $_POST['sId'] != '' ? $d->sanitizeReportFilterIdAsInt($_POST['sId']) : '';
			$redirect_url = "../cities" . ($redirect_sId != '' ? "?sId=" . $redirect_sId : '');
			header("location:" . $redirect_url);  
			exit();
		}
	}
}