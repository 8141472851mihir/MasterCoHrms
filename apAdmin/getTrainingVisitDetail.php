<?php
	
include_once 'common/object.php';
error_reporting(0);
	extract(array_map("test_input" , $_POST));
	if (isset($society_id) && $society_id!='') {
?>
<div class="modal-body">
    <div class="row justify-content-center ml-4">
        <div class="col-12 col-md-11">
            <table class="table table-responsive">
                <thead>
                    <tr class="text-center">
                        <th>#</th>
                        <th>Training</th>
                        <th>Status</th>
                        <th>Person Name</th>
                        <th>Person Contact</th>
                        <th>Training Date Time</th>
                        <th>Training Data</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    $q = $d->selectRow("training_visit_master_id,visit_start_datetime,template_name,employee_name,employee_mobile","training_visit_master","society_id='$society_id'");
                    if(mysqli_num_rows($q) > 0){
                        while ($data = mysqli_fetch_array($q)) {
                    ?>
                    <tr class="text-center">
                        <td><?php echo $i++; ?></td>
                        <td><?php echo $data['template_name']; ?></td>
                        <td><span class="badge badge-warning p-2">Pending</span></td>
                        <td><?php echo $data['employee_name']; ?></td>
                        <td><?php echo $data['employee_mobile']; ?></td>
                        <td><?php echo $data['visit_start_datetime']; ?></td>
                   <td>
                 <form action="viewTrainingProcess" method="POST">
        <input type="hidden" name="training_visit_master_id" value="<?php echo $data['training_visit_master_id']; ?>">
        <button type="submit" class="btn btn-primary btn-sm">View</button>
    </form>
</td>


                    </tr>
                    <?php }} else { ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted">No Data Available!</td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
 <?php
}
?>