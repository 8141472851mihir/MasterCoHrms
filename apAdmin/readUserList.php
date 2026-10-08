<?php extract($_GET);?>
<div class="content-wrapper">
	<div class="container-fluid">
		<!-- Breadcrumb-->
		<div class="row pt-2 pb-2">
			<div class="col-sm-12">
				<h4 class="page-title">Read User List (#<?=$id?>)</h4>
			</div>
		</div>
		<!-- End Breadcrumb-->
		<div class="row">
			<div class="col-lg-12">
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table id="example" class="table table-bordered">
								<thead>
									<tr>
										<th>#</th>
										<th>User Name</th>
										<th>Ticket Read Status</th>
										<th>Read By Last Status</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$i = 1;
									$data = $d->selectRow("bms_admin_master.admin_name,bms_admin_master.admin_id,read_by.*","read_by LEFT JOIN bms_admin_master ON bms_admin_master.admin_id=read_by.read_user_id","read_by.feedback_id='$id'","ORDER BY read_by.updated_date DESC");
									while($row = mysqli_fetch_array($data)){
										extract($row);
										if($feedback_id > 0){
											?>
											<tr>
												<td><?=$i++;?></td>
												<td><?=$admin_name?></td>
												<td><?=$date_time?></td>
												<td><?=$updated_date?></td>
											</tr>
											<?php 
										}
									}
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
