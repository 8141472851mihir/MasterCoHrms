<?php
$from = $d->sanitizeReportFilterDate(isset($_GET['from']) ? $_GET['from'] : '', '');
$toDate = $d->sanitizeReportFilterDate(isset($_GET['toDate']) ? $_GET['toDate'] : '', '');
?>
<div class="content-wrapper">
    <div class="container-fluid">
      	<!-- Breadcrumb-->
      	<div class="row ">
	        <div class="col-sm-3">
	          <h4 class="page-title">CRM Report</h4>
	        </div>
	        <!-- <div class="col-sm-9">
	          	<form action="" class="branchDeptFilter">
		            <div class="row ">
		            	<div class="col-md-2 col-6 form-group">
		                	<input type="text" class="form-control" autocomplete="off" id="autoclose-datepickerFrom"  name="from" value="<?php if(isset($_GET['from']) && $_GET['from'] != ''){ echo $from = $_GET['from'];} ?>">   
		              	</div>
		              	<div class="col-md-2 col-6 form-group">
		                	<input  type="text" class="form-control" autocomplete="off" id="autoclose-datepickerTo"  name="toDate" value="<?php if(isset($_GET['toDate']) && $_GET['toDate'] != ''){ echo $toDate= $_GET['toDate'];} ?>">  
		              	</div>          
		              	<div class="col-md-3 form-group">
		               		<input class="btn btn-success btn-sm " type="submit" name="getReport" value="Get">
		              	</div>
		            </div>
	          	</form>
	        </div> -->
      	</div>

      	<div class="row mt-2">
        	<div class="col-lg-12">
          		<div class="card">
            		<div class="card-body">
              			<div class="table-responsive">
              				<table id="reportTable" class="table table-bordered">
                				<thead>
                  					<tr>
                  						<th>#</th>
                  						<th>Id</th>
                  						<th>CRM Name</th>
                  						<th>City</th>
                  						<th>Mobile</th>
                  						<th>Plan</th>
                  						<th>CRM Create Date</th>
                  						<th>Received Ticket Size</th>
                  						<th>CRM Plan Expire Date</th>
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
                					</tr>
              					</tfoot>
                				<tbody>
                					<?php
                						$i=1;
                						$q = $d->selectRow("society_master.*,manage_plan.plan_value,manage_plan.plan_name","society_master LEFT JOIN manage_plan ON manage_plan.plan_value = society_master.crm_package_id","society_id!=0 AND crm_created=1","order by plan_expire_date ASC");
                						while ($data=mysqli_fetch_array($q)) {
                  							extract($data);
                						?>
                						<tr>
                							<td><?php echo $i++; ?></td>
                							<td><?php echo ''.$d->short_app_name().'_'.$society_id; ?></td>
                							<td><?php echo $society_name; ?></td>
                							<td><?php echo $city_name;; ?></td>
                							<td><?php echo $secretary_mobile; ?></td>
                							<td>
                								<?php 
                      								if($plan_value == 0){
                      									echo "Custom Plan";
                      								}else{
                        								echo $plan_name;
                      								}
                    							?>
                							</td>
                							<td><?php echo date('d-m-Y', strtotime($crm_created_date)); ?></td>
                							<td><?php echo $received_ticket_size; ?></td>
                							<td><?php 
                									if (!empty($crm_plan_expiring_date)) {
        												echo date('d-m-Y', strtotime($crm_plan_expiring_date));
    												} else {
        												echo ''; // leave it blank if the date is empty or invalid
    												} 
    											?>    												
    										</td>
                						</tr>
                					<?php } ?>
                				</tbody>
            				</table>
          				</div>
          			</div>
        		</div>
      		</div>
    	</div>
    </div>
</div>