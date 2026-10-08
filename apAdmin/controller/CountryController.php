<?php 
include '../common/objectController.php';
if(isset($_POST) && !empty($_POST))
{
	extract($_POST);
	$country_id = isset($country_id) ? $d->sanitizeActionIdAsInt($country_id) : 0;

	if(isset($AddCountry))
	{
		if(!empty($name))
		{
			$nameEsc = $d->escapeSqlString($name);
			$country = $d->select("countries","name='$nameEsc'");
			if(mysqli_num_rows($country) == 0)
			{
				$a1 = array(
					'name'=>$name,
					'iso3'=>$iso3,
					'iso2'=>$iso2,
					'phonecode'=>$phonecode,
					'capital'=>$capital,
					'currency'=>$currency,
				);
				$q = $d->insert("countries",$a1);
				if($q > 0)
				{
					$_SESSION['msg']="Country Added Successfully.";
					$d->insert_log("0","$bms_admin_id","$created_by","Country Added Successfully.");
					header("location:../manageCountry");
					exit();
				}else{
					$_SESSION['msg1']="Something Went Wrong?";
					header("location:../manageCountry");
					exit();
				}
			}else{
				$_SESSION['msg1']="Country Already Added";
				header("location:../manageCountry");
				exit();
			}
		}else{
			$_SESSION['msg1']="Country Name Required?";
			header("location:../manageCountry");
			exit();
		}
	}
	
	if(isset($EditCountry))
	{
		if(!empty($name))
		{
			$nameEsc = $d->escapeSqlString($name);
			$country = $d->select("countries","name='$nameEsc' AND country_id !='$country_id'");
			if(mysqli_num_rows($country) == 0)
			{
				$a1 = array(
					'name'=>$name,
					'iso3'=>$iso3,
					'iso2'=>$iso2,
					'phonecode'=>$phonecode,
					'capital'=>$capital,
					'currency'=>$currency,
				);
				$q = $d->update("countries",$a1,"country_id=$country_id");
				if($q > 0)
				{
					$_SESSION['msg']="Country Updated Successfully.";
					$d->insert_log("0","$bms_admin_id","$created_by","Country Updated Successfully.");
					header("location:../manageCountry");
					exit();
				}else{
					$_SESSION['msg1']="Something Went Wrong?";
					header("location:../manageCountry");
					exit();
				}
			}else{
				$_SESSION['msg1']="Country Already Added";
				header("location:../manageCountry");
				exit();
			}
		}else{
			$_SESSION['msg1']="Country Name Required?";
			header("location:../manageCountry");
			exit();
		}
	}

	if(isset($delectCountry))
	{
		$q1 = $d->select("states","country_id=$country_id");
		if(mysqli_num_rows($q1) == 0)
		{
			$q = $d->delete("countries","country_id=$country_id");
			if($q > 0)
			{
				$_SESSION['msg']="Country Deleted Successfully.";
				$d->insert_log("0","$bms_admin_id","$created_by","Country Deleted Successfully.");
				header("location:../manageCountry");
				exit();
			}else{
				$_SESSION['msg1']="Something Went Wrong?";
				header("location:../manageCountry");
				exit();
			}
		}else{
			$_SESSION['msg1']="Delete First State Then Delete Country";
			header("location:../manageCountry");
			exit();
		}
	}
}