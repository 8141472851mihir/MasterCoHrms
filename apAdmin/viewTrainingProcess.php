<?php 
if (isset($_POST['training_visit_master_id'])) {
    $training_visit_master_id = $_POST['training_visit_master_id'];
    $query = $d->selectRow('training_visit_data,template_name,employee_name,employee_mobile,visit_start_datetime,visit_end_datetime,visit_remark', 'training_visit_master', "training_visit_master_id = '$training_visit_master_id'");

    if (mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_array($query);

        $training_visit_data = json_decode($data['training_visit_data'], true);
    } else {
        echo "Error: Unable to decode JSON data.";
    }
} else {
    echo "No training visit master ID provided.";
}
?>

<div class="content-wrapper">
    <div class="container-fluid">

        <h2>Training Process Details</h2>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="example" class="table table-bordered">
                                 <h6>TemplateName : <?php echo $data['template_name'] ?></h6>
                                 <h6>EmployeeName : <?php echo $data['employee_name'] ?></h6>
                                 <h6>EmployeeNumber : <?php echo $data['employee_mobile'] ?></h6>
                                 <h6>VisitStartDate : <?php echo $data['visit_start_datetime'] ?></h6>
                                 <h6>VisitEndDate : <?php echo $data['visit_end_datetime'] ?></h6>
                                 <h6>VisitRemark : <?php echo $data['visit_remark'] ?></h6>

                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Template Questions</th>
                                        <th>Template Answers</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php       
                                    if (isset($training_visit_data) && $training_visit_data) {
                                        $i = 1;
                                        foreach ($training_visit_data as $item) {
                                            // if ($i == 1) {
                                            //     echo "<tr><td colspan='3'><strong>Template Name:</strong> " . htmlspecialchars($item['template_name']) . "</td></tr>";
                                            // }
                                            ?>
                                            <tr>
                                                <td><?php echo $i++; ?></td>
                                                <td><?php echo htmlspecialchars($item['template_question']); ?></td>
                                                <td><?php echo htmlspecialchars($item['employee_answer']); ?></td>
                                            </tr>
                                            <?php
                                        }
                                    } else {
                                        echo "<tr><td colspan='3'>No data found or unable to decode the JSON data.</td></tr>";
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
