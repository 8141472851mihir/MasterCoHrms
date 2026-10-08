<?php
extract(array_map("test_input", $_REQUEST));
?>
<div class="content-wrapper">
  <div class="container-fluid">
    <div class="row pt-2 pb-2">
      <div class="col-sm-9 col-5">
        <h4 class="page-title">Company Report</h4>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <table id="reportTable" class="table table-bordered">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Company ID</th>
                    <th>Name</th>
                    <th>Company Type</th>
                    <th>Address</th>
                    <th>City</th>
                    <th>Email</th>
                    <th>Phone No</th>
                    <th>Latitute</th>
                    <th>Longitute</th>
                    <th>Package Type</th>
                    <th>Plan Expire Days</th>
                    <th>Employee Registration Limit</th>
                    <th>Employee Tracking Limit</th>
                    <th>Per Employee</th>
                    <th>Yearly Ticket Size</th>
                    <th>Received Ticket Size</th>
                    <th>Contact Person Name</th>
                    <th>Sales Person Name</th>
                    <th>Implementation Person Name</th>
                    <th>Support Person Name</th>
                    <th>Support Number</th>
                    <th>Reference from</th>
                    <th>Account Type</th>
                    <th>Rating</th>
                    <th>Created Date</th>
                    <th>Lead Sources</th>
                    <th>Region</th>
                  </tr>
                </thead>
                <tfoot>
                  <tr>
                    <th class="no-search-box"></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                  </tr>
                </tfoot>
                <tbody>
                  <?php
                  $i = 1;
                  $q = $d->selectRow("society_master.*,business_entity_master.name AS business_entity_name","society_master LEFT JOIN business_entity_master ON society_master.industry_type=business_entity_master.b_id", "", "ORDER BY society_id DESC");
                  while ($data = mysqli_fetch_array($q)) {
                    extract($data);

                    if($package_id==0){
                      $package_data = "Trial";
                    }else{
                      $package_data = "Paid";
                    }
                  ?>
                    <tr>
                      <td><?php echo $i++; ?></td>
                      <td><?php echo '' . $d->short_app_name() . '_' . $society_id; ?></td>
                      <td><?php echo $society_name; ?></td>
                      <td><?php echo $business_entity_name; ?></td>
                      <td><?php echo $society_address; ?></td>
                      <td><?php echo $city_name; ?></td>
                      <td><?php echo $secretary_email; ?></td>
                      <td><?php echo $secretary_mobile; ?></td>
                      <td><?php echo $society_latitude; ?></td>
                      <td><?php echo $society_longitude; ?></td>
                      <td><?php echo $package_data; ?></td>
                      <td><?php echo $plan_expire_date; ?></td>
                      <td><?php echo $employee_registration_limit; ?></td>
                      <td><?php echo $employee_tracking_limit; ?></td>
                      <td><?php echo $per_employee_price; ?></td>
                      <td><?php echo $yearly_ticket_size; ?></td>
                      <td><?php echo $received_ticket_size; ?></td>
                      <td><?php echo $secretary_name; ?></td>
                      <td><?php echo $sales_person_name; ?></td>
                      <td><?php echo $implementation_name; ?></td>
                      <td><?php echo $support_name; ?></td>
                      <td><?php echo $support_country_code . ' ' . $support_mobile_no; ?></td>
                      <td><?php echo $reference_from; ?></td>
                      <td><?php echo ($account_type) == 0 ? 'Normal' : 'Key'; ?></td>
                      <td><?php echo $society_rating; ?></td>
                      <?php
                      if ($created_date != "") {
                        $display_date = date('d-m-Y', strtotime($created_date));
                      } else {
                        $display_date = "-";
                      }
                      ?>
                      <td class="tableWidth"><?php echo $display_date; ?></td>
                      <td><?php
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
                      echo $leadSources[$lead_sources] ?? '';
                      ?></td>
                      <td><?php 
                      echo $region_name; 
                      ?></td>
                    </tr>

                    <?php
                  } // end while loop
                    ?>
                    
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>