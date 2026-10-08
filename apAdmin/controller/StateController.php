<?php 
include '../common/objectController.php';
if(isset($_POST) && !empty($_POST))
{
	extract($_POST);
	$country_id = isset($country_id) ? $d->sanitizeActionIdAsInt($country_id) : 0;
	$state_id = isset($state_id) ? $d->sanitizeActionIdAsInt($state_id) : 0;

	if(isset($AddState))
	{
		if(!empty($name))
		{
			$nameEsc = $d->escapeSqlString($name);
			$state = $d->select("states","name='$nameEsc' AND country_id='$country_id'");
			if(mysqli_num_rows($state) == 0)
			{
				$a1 = array(
					'country_id'=>$country_id,
					'name'=>$name,
				);
				$q = $d->insert("states",$a1);
				if($q > 0)
				{
					$_SESSION['msg']="State Added Successfully.";
					$d->insert_log("0","$bms_admin_id","$created_by","State Added Successfully.");
					header("location:../manageState?countryId=$country_id");
					exit();
				}else{
					$_SESSION['msg1']="Something Went Wrong?";
					header("location:../manageState?countryId=$country_id");
					exit();
				}
			}else{
				$_SESSION['msg1']="State Already Added";
				header("location:../manageState?countryId=$country_id");
				exit();
			}
		}else{
			$_SESSION['msg1']="State Name Required?";
			header("location:../manageState?countryId=$country_id");
			exit();
		}
	}
	
	if(isset($EditState))
	{
		if(!empty($name))
		{
			$nameEsc = $d->escapeSqlString($name);
			$state = $d->select("states","name='$nameEsc' AND country_id='$country_id' AND state_id !='$state_id'");
			if(mysqli_num_rows($state) == 0)
			{
				$a1 = array(
					'country_id'=>$country_id,
					'name'=>$name,
				);
				$q = $d->update("states",$a1,"state_id='$state_id'");
				if($q > 0)
				{
					$_SESSION['msg']="State Updated Successfully.";
					$d->insert_log("0","$bms_admin_id","$created_by","State Updated Successfully.");
					header("location:../manageState?countryId=$country_id");
					exit();
				}else{
					$_SESSION['msg1']="Something Went Wrong?";
					header("location:../manageState?countryId=$country_id");
					exit();
				}
			}else{
				$_SESSION['msg1']="State Already Added";
				header("location:../manageState?countryId=$country_id");
				exit();
			}
		}else{
			$_SESSION['msg1']="State Name required?";
			header("location:../manageState?countryId=$country_id");
			exit();
		}
	}
	
	if(isset($delectState))
	{
		$q1 = $d->select("cities","state_id=$state_id");
		if(mysqli_num_rows($q1) == 0)
		{
			$q = $d->delete("states","country_id=$country_id AND state_id=$state_id");
			if($q > 0)
			{
				$_SESSION['msg']="State Deleted Successfully.";
				$d->insert_log("0","$bms_admin_id","$created_by","State Deleted Successfully.");
				header("location:../manageState?countryId=$country_id");
				exit();
			}else{
				$_SESSION['msg1']="Something Went Wrong?";
				header("location:../manageState?countryId=$country_id");
				exit();
			}
		}else{
			$_SESSION['msg1']="Delete First City Then Delete State";
			header("location:../manageState?countryId=$country_id");
			exit();
		}
	}
}