<style type="text/css">
    .tableWidth{
        word-break: break-all;
    }
</style>
<?php 

include_once 'common/object.php';
session_start();
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
extract(array_map("test_input" , $_POST));
if(isset($request_society_id)) {
    if(isset($is_pending) && $is_pending==true){
        $data=$d->selectArray("society_master","society_id = '$request_society_id'");
        $country_id = $data['country_id'];
        $state_id = $data['state_id'];
        $city_id = $data['city_id'];
        $request_sub_domain = $data['sub_domain'];
        $request_society_name=$data['society_name'];
        $request_society_logo=$data['socieaty_logo'];
        $request_society_address=$data['society_address'];
        $request_society_pincode=$data['society_pincode'];
        $request_package_id=$data['package_id'];
        $request_trial_days=$data['trial_days'];
        $payment_status=$data['payment_status']??"";
        $payment_amount=$data['payment_amount']??"";
        $request_secretary_name=$data['secretary_name'];
        $request_secretary_mobile=$data['secretary_mobile'];
        $request_secretary_email=$data['secretary_email'];
        // Jainish Start
        $account_type=$data['account_type'];
        // Jainish End
        $society_rating=$data['society_rating'];
        $employee_tracking_limit=$data['employee_tracking_limit'];
        $employee_registration_limit=$data['employee_registration_limit'];
        $request_society_create_status=$data['society_create_status']??"";
        $reject_reason=$data['reject_reason']??"";
        $lead_sources=$data['lead_sources'];
        $region=$data['region_name'];
        $search_society_code=$data['search_society_code'];
        $society_code=$data['society_code'];

    }else{
        $data=$d->selectArray("society_master_requests","request_society_id = '$request_society_id'");
        $country_id = $data['request_country_id'];
        $state_id = $data['request_state_id'];
        $city_id = $data['request_city_id'];
        $request_sub_domain = $data['request_sub_domain'];
        $request_society_name=$data['request_society_name'];
        $request_society_logo=$data['request_society_logo'];
        $request_society_address=$data['request_society_address'];
        $request_society_pincode=$data['request_society_pincode'];
        $request_package_id=$data['request_package_id'];
        $request_trial_days=$data['request_trial_days'];
        $payment_status=$data['payment_status'];
        $payment_amount=$data['payment_amount'];
        $request_secretary_name=$data['request_secretary_name'];
        $request_secretary_mobile=$data['request_secretary_mobile'];
        $request_secretary_email=$data['request_secretary_email'];
        // Jainish Start
        $account_type=$data['account_type'];
        // Jainish End
        $society_rating=$data['society_rating'];
        $employee_tracking_limit=$data['employee_tracking_limit'];
        $employee_registration_limit=$data['employee_registration_limit'];
        $request_society_create_status=$data['request_society_create_status'];
        $reject_reason=$data['reject_reason'];
        $lead_sources=$data['requests_lead_sources'];
        $region=$data['request_region_name'];
        $search_society_code=$data['request_search_society_code'];
        $society_code=$data['request_society_code'];

    }
    $charge = explode('/', $request_sub_domain);
    $charge = $charge[2]; 
    $domainTemp = "https://".$charge;


    $qdomain = $d->selectRow("domain_master.domain_id,server_master.server_name,server_master.server_ip,domain_master.domain_name","domain_master LEFT JOIN server_master ON server_master.server_id=domain_master.server_id","domain_master.domain_name LIKE '%$domainTemp%'");
    $domainData = mysqli_fetch_array($qdomain);
    $domain_id = $domainData['domain_id'];


    $qc=$d->select("countries","flag=1 AND country_id='$country_id'");
    $cData=mysqli_fetch_array($qc);
    $country_name= $cData['name'];

    $qs=$d->select("states","state_id='$state_id'");
    $sData=mysqli_fetch_array($qs);
    $state_name= $sData['name'];


    $qcity=$d->select("cities","city_id='$city_id'");
    $cityData=mysqli_fetch_array($qcity);

    $city_name= $cityData['name'];

    $leadSources = [
        1 => 'Meta',
        2 => 'Inbound',
        3 => 'Walk IN',
        4 => 'Cold Data',
        5 => 'BA / Director Reference',
        6 => 'BNI Reference',
        7 => 'Event - Exhibitor',
        8 => 'Event - Exhibitor ( Exhibitor Cards )',
        9 => 'Event - Exhibitor ( Visitor Cards )',
        10 => 'Event - Industry Specific',
        11 => 'Event - Networking',
        12 => 'Existing Client',
        13 => 'Nikseam BPO',
        14 => 'Old Lead ( Any Source)',
        15 => 'Personal Reference',
        16 => 'Reference from demo client',
        17 => 'Reference from existing client',
        18 => 'Reference from Implementation team',
        19 => 'Rise',
        20 => 'Tech Imply',
        21 => 'Tech Jockey',
        22 => 'Website / Landing Page',
        23 => 'Website / Landing Page / ChatBot /MyCo App',
        24 => 'Whatsapp Bulkshoot'
    ];
    $lead_sources_data = $leadSources[$lead_sources] ?? '-';

    if($search_society_code==0){
        $society_search_code = "No";
    }else{
        $society_search_code = "Yes";
    }
?>
<style>
    td{
        padding: 12px !important;
    }
</style>
<div class="m-2">
    <div class="text-center m-2">
        <h5><?php echo $request_society_name ?></h5>
        <?php if($request_society_logo!=''){ ?>
        <img src="../img/society_requests/<?php echo $request_society_logo ?>" width="100">
        <?php } ?>
    </div>
    <table class="table">
        <tr>
            <th class="tableWidth">Country : </th>
            <td class="tableWidth"><?php 
                
                echo $country_name; ?>
            </td>
            <th class="tableWidth">State : </th>
            <td class="tableWidth"><?php 
                echo $state_name; ?>
            </td>
        </tr>
        <tr>
            <th class="tableWidth">City : </th>
            <td class="tableWidth"><?php 
                echo $city_name; ?>
            </td>
            <th class="tableWidth">Address : </th>
            <td class="tableWidth"><?php echo $request_society_address ?></td>
        </tr>
        <tr>
            <th class="tableWidth">Pincode : </th>
            <td class="tableWidth"><?php echo $request_society_pincode ?></td>
            <th class="tableWidth"></th>
            <td class="tableWidth"></td>
        </tr>
        <tr>
            <th class="tableWidth">Base URL : </th>
            <td colspan="3" class="tableWidth"><a href="<?php echo $request_sub_domain ?>apAdmin/" target="_blank"><?php echo $request_sub_domain ?></a>
            <?php if (mysqli_num_rows($qdomain)==0) {
                echo "<b class='text-danger'><br>This Domain Not Added in domain master</b>";
            } ?>
            </td>
        </tr>
        <tr>
            <th class="tableWidth">Server  : </th>
            <td colspan="3" class="tableWidth"><?php echo $domainData['server_name'] ?> - <?php echo $domainData['server_ip'] ?>
            </td>
        </tr>
        <tr>
            <th class="tableWidth">Plan : </th>
            <td class="tableWidth"><?php if ($request_package_id==0) {
                    echo "Trial";
                } else{
                    $pack = $d->selectArray("manage_plan","plan_value='$request_package_id'");
                    echo $pack['plan_name'];
                } ?>
            </td>
            <?php if ($request_package_id==0) { ?>
                <th class="tableWidth">Trial Days : </th>
                <td class="tableWidth"><?php echo $request_trial_days ?></td>
            <?php } else { ?>
                <th class="tableWidth">Payment : </th>
                <td class="tableWidth"><?php if ($payment_status==1) {
                    echo $payment_amount;
                } else{
                    echo "Pending";
                } ?></td>
            <?php } ?>
        </tr>
        <tr>
            <th class="tableWidth">Employee Tracking Limit : </th>
            <td class="tableWidth"><?php echo $employee_tracking_limit ?></td>
            <th class="tableWidth">Employee Registration Limit : </th>
            <td class="tableWidth"><?php echo $employee_registration_limit ?></td>
        </tr>
        <tr>
            <th class="tableWidth">Lead Sources : </th>
            <td class="tableWidth"><?php echo $lead_sources_data; ?></td>
            <th class="tableWidth">Region : </th>
            <td class="tableWidth"><?php echo $region; ?></td>
        </tr>
        <tr>
            <th class="tableWidth">Search Company Code</th>
            <td class="tableWidth"><?php echo $society_search_code; ?></td>
            <th class="tableWidth">Company Code</th>
            <td class="tableWidth"><?php echo $society_code; ?></td>
        </tr>
    </table>
    <hr>
    <div class="text-center"><b>Admin Details</b></div>
    <table class="table">
        <tr>
            <th class="tableWidth">Name : </th>
            <td class="tableWidth"><?php echo $request_secretary_name ?></td>
            <th class="tableWidth">Mobile : </th>
            <td class="tableWidth"><?php echo $request_secretary_mobile ?></td>
        </tr>
        <!-- Jainish Start -->
        <tr>
            <th class="tableWidth">Account Type : </th>
            <td class="tableWidth"><?php echo ($account_type == 0) ? "Normal Account" : "Key Account"; ?></td>
            <th></th>
            <td></td>
        </tr>
        <!-- Jainish End -->
        <tr>
            <th class="tableWidth">Email : </th>
            <td class="tableWidth"><?php echo $request_secretary_email ?></td>
            <th></th>
            <td></td>
        </tr>
        <tr>
            <th class="tableWidth">Company Priority : </th>
            <td class="tableWidth"><?php $rating = (int)$society_rating;
            echo str_repeat('<i class="fa fa-star fa-lg text-warning" aria-hidden="true"></i>', $rating) . str_repeat('<i class="fa fa-star-o fa-lg" aria-hidden="true"></i>', 10 - $rating); ?></td>
            <th></th>
            <td></td>
        </tr>
    </table>
    <hr>
    
    <?php if ($request_society_create_status==2) { ?>
        <div class="text-center text-danger"><b>Reason : <?php echo $reject_reason ?></b></div>
    <?php } ?>
</div>

<?php } 
// Mukesh start 5-6-24
else if(isset($setting_society_id)) {
    $data=$d->selectArray("society_master","society_id = '$setting_society_id'");
    extract($data);

?>
<div class="m-2">
        <div class="form-group row"> 
            <div class="col-sm-4">
                <label for="Company Name" class="col-form-label">Company Name<span class="text-danger">*</span> :</label>
                <input autocomplete="off" maxlength="50" type="text" class="form-control"  name="society_name"  value="<?php echo $society_name; ?>" required="" />
            </div>
            <div class="col-sm-4">
                <label for="Company Full Name" class="col-form-label">Company 
                Full Name :</label>
                <input autocomplete="off" maxlength="250" type="text" class="form-control"  name="company_full_name"  value="<?php echo $company_full_name; ?>"  />
            </div>
             <div class="col-sm-4">
                <label for="Company Name" class="col-form-label">Company Address<span class="text-danger">*</span> :</label>
                <textarea autocomplete="off" maxlength="300"  id="society_address" class="form-control" name="society_address"  required="" ><?php echo $society_address; ?></textarea>
            </div>
        </div>
        <div class="form-group row">           
            <div class="col-sm-4">
            <label for="secretary_name" class="col-form-label">Contact Person Name<span class="text-danger">*</span> :</label>
                <input autocomplete="off"  maxlength="100" type="text" class="form-control"  name="secretary_name"  value="<?php echo $secretary_name; ?>" required=""/>
            </div>
            <div class="col-sm-4">
                <label for="Secretary Email" class="col-form-label">Contact Person Email<span class="text-danger">*</span> :</label>
                <input autocomplete="off" maxlength="50"  type="text" class="form-control"  name="secretary_email"  value="<?php echo $secretary_email; ?>" />
            </div>
            <div class="col-sm-2">
                <label for="country_code" class="col-form-label">Country Code<span class="text-danger">*</span> :</label>
                <input type="hidden" value="<?php echo $country_code; ?>" id="country_code_cs" name="">
                <select name="country_code" class="form-control country_code-select" id="country_code" required="">
                    <?php include 'country_code_option_list.php'; ?>
                </select>
            </div>
            <div class="col-sm-2">
            <label for="secretary_mobile" class="col-form-label">Contact Person Mobile<span class="text-danger">*</span> :</label>
                <input autocomplete="off" maxlength="15" minlength="8" type="text" class="form-control onlyNumber"  name="secretary_mobile"  value="<?php echo $secretary_mobile; ?>" />
            </div>
        </div>
        <div class="form-group row"> 
            <div class="col-sm-4">
                <label for="pincode" class="col-form-label">Pincode<span class="text-danger">*</span> :</label>
                <input autocomplete="off" maxlength="6" type="text" class="form-control onlyNumber"  name="society_pincode"  value="<?php echo $society_pincode; ?>"   required />
            </div>
            <div class="col-sm-4">
                <label for="login_via" class="col-form-label">Login Via<span class="text-danger">*</span> :</label>
                <select autocomplete="off" class="form-control " name="login_via" required >
                    <option value="">Select </option>
                    <option <?php if($login_via == 0){echo "selected";} ?> value="0">Phone</option>
                    <option <?php if($login_via == 1){echo "selected";} ?> value="1">Email</option>
                </select>
            </div>
            <div class="col-sm-4">
                <label for="google_login" class="col-form-label">Google login<span class="text-danger">*</span> :</label>
                <select autocomplete="off" class="form-control" name="google_login" required >
                    <option value="">Select </option>
                    <option <?php if($google_login == 0){echo "selected";} ?> value="0">Yes</option>
                    <option <?php if($google_login == 1){echo "selected";} ?> value="1">No</option>
                </select>
            </div>
        </div>
        <div class="form-group row">            
            <div class="col-sm-4">
                <label for="industry_type" class="col-form-label">Industry Type<span class="text-danger">*</span> :</label>
                <select autocomplete="off" class="form-control single-select" name="industry_type" id="industry_type" required >
                    <?php $qi = $d->select("business_entity_master", "business_entity_master.status='0'", "ORDER BY name ASC");
                    ?>
                    <option value="">-- Select --</option>
                    <?php
                    while ($iData = mysqli_fetch_array($qi)) {?>
                    <option <?php if($iData['b_id']==$industry_type) { echo 'selected'; } ?> value="<?php echo $iData['b_id']; ?>"> <?php echo $iData['name']; ?></option>
                <?php } ?>
                </select>
            </div>
             <div class="col-sm-4">
                <label for="gst_number" class="col-form-label">GST/Tax Number :</label>
                <input  autocomplete="off" maxlength="50" type="text"  class="form-control text-uppercase"  name="gst_number"  value="<?php echo $gst_number; ?>"    />
            </div>
            <div class="col-sm-4">
                 <label for="input-10" class="col-form-label">Travel KM Mode <span class="text-danger">*</span> :</label>
                    <select required="" autocomplete="off" id="distance_get_type" class="form-control single-select" name="distance_get_type">
                        <!-- <option <?php echo ($distance_get_type == 0) ? 'selected' : ''; ?> value="0">GPS </option> -->
                        <!-- <option <?php echo ($distance_get_type == 1) ? 'selected' : ''; ?> value="1">Distancematrix</option> -->
                        <!-- <option <?php echo ($distance_get_type == 2) ? 'selected' : ''; ?> value="2">Here Map</option> -->
                        <option <?php echo ($distance_get_type == 3) ? 'selected' : ''; ?> value="3">Graphhopper</option>
                    </select>
            </div>
        </div>
        <div class="form-group row"> 
            <div class="col-sm-4">
                 <label for="input-10" class="col-form-label">Visit Calculation Method <span class="text-danger">*</span> :</label>
                    <select required="" autocomplete="off" id="visit_calculation_method" class="form-control single-select" name="visit_calculation_method">
                        <option <?php echo ($visit_calculation_method == 0) ? 'selected' : ''; ?> value="0">Entire Day</option>
                        <option <?php echo ($visit_calculation_method == 1) ? 'selected' : ''; ?> value="1">Visit to Visit</option>
                    </select>
            </div>
            <div class="col-sm-4">
                <label for="input-10" class="col-form-label"> Search With Company Code <span class="text-danger">*</span> :</label>
                <select required="" autocomplete="off" id="search_society_code" class="form-control single-select" name="search_society_code">
                    <option <?php echo ($search_society_code == 0) ? 'selected' : ''; ?> value="0">No</option>
                    <option <?php echo ($search_society_code == 1) ? 'selected' : ''; ?> value="1">Yes</option>
                </select>
            </div>
            <div class="col-sm-4">
                <label for="input-10" class="col-form-label"> Company Code :</label>
                <input autocomplete="off" maxlength="50"  type="text" class="form-control" required=""  name="society_code"  value="<?php echo $society_code; ?>" />
            </div>
        </div>

        <div class="form-footer text-center">
            <input type="hidden" name="action" value="update_company_settings" />
            <input type="hidden" name="countryId" value="<?php echo $country_id ; ?>" />
            <input type="hidden" name="visit_calculation_method_old" value="<?php echo $visit_calculation_method ; ?>" />
            <input type="hidden" name="distance_get_type_old" value="<?php echo $distance_get_type ; ?>" />
            <input type="hidden" name="sid" value="<?php echo $state_id ; ?>" />
            <input type="hidden" name="cid" value="<?php echo $city_id ; ?>" />           
            <input type="hidden" name="society_id" value="<?php echo $setting_society_id; ?>" />
            <input type="hidden" name="csrf" value="<?php echo $_SESSION["token"]; ?>" id="token">
            
            
            <button type="submit"   class="btn btn-success"><i class="fa fa-check-square-o"></i> Update </button>
        </div>
</div>
<script>
    
     $(".country_code-select,.single-select").select2({placeholder: "--SELECT--",});
     $(".onlyNumber,#secretary_mobile").keydown(function (e) {
      // Allow: backspace, delete, tab, escape, enter and .
      if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
          // Allow: Ctrl+A, Command+A
          (e.keyCode == 65 && ( e.ctrlKey === true || e.metaKey === true ) ) || 
          // Allow: home, end, left, right, down, up
          (e.keyCode >= 35 && e.keyCode <= 40)) {
              // let it happen, don't do anything
            return;
          }
      // Ensure that it is a number and stop the keypress
      if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
        e.preventDefault();
      }
    });
    var country_code_cs = $('#country_code_cs').val();
    
    $("#country_code").val(country_code_cs); 
</script>

<?php } else{
    echo "Something Wrong. Try again after sometime.";
} 

?>
