<?php
extract(array_map("test_input" , $_REQUEST));
error_reporting(0);
?>
<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Breadcrumb-->
        <div class="row pt-2 pb-2">
            <div class="col-sm-9">
                <h4 class="page-title">Demo Requests</h4>
            </div>
            <div class="col-sm-3">
                <div class="btn-group float-sm-right">
                    <a href="javascript:void(0)" onclick="DeleteAll('deleteDemoRequest');" class="btn  btn-sm btn-danger pull-right"><i class="fa fa-trash-o fa-lg"></i> Delete </a>
                </div>
            </div>
        </div>
        <!-- End Breadcrumb-->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <ul class="nav nav-tabs nav-tabs-info nav-justified">
                            <?php
                            error_reporting(0);
                            extract($_REQUEST);
                            $pending_table="";
                            $withDeveloper="";
                            $solved_table="";
                            if($tab==0){
                            $pending_table="active";
                            } else if($tab==1){
                            $withDeveloper="active";
                            } else if($tab==2){
                            $solved_table="active";
                            } else {
                            $pending_table="active";
                            }
                            ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $pending_table;?>" data-toggle="tab" href="#pending_tab"><i class="fa fa-spinner"></i> <span>Pending</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $withDeveloper;?>" data-toggle="tab" href="#withDeveloper_tab"><i class="fa fa-user"></i> <span>In Progress</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $solved_table;?>" data-toggle="tab" href="#solved_tab"><i class="fa fa-check"></i> <span >Completed</span></a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div id="pending_tab" class=" tab-pane <?php echo $pending_table;?> show">
                                <div class="table-responsive">
                                    <table id="example" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="deleteTh">#</th>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Mobile</th>
                                                <th>Email</th>
                                                <th>Message</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $i=1;
                                            $q=$d->select("demo_request_from_chpl","status = 1","ORDER BY demo_id DESC");
                                            while ($data = $q->fetch_assoc())
                                            {
                                            extract($data);
                                            ?>
                                            <tr>
                                                <td class='text-center'>
                                                    <?php if($role_id==1) { ?>
                                                    <input type="checkbox" class="multiDelteCheckbox"  value="<?php echo $data['demo_id']; ?>">
                                                    <?php } ?>
                                                </td>
                                                <td><?php echo $i++; ?></td>
                                                <td class="tableWidth"><?php echo $name; ?></td>
                                                <td class="tableWidth"><?php echo $mobile_number; ?></td>
                                                <td class="tableWidth"><?php echo $email; ?></td>
                                                <td class="tableWidth"><?php echo $message; ?></td>
                                                <td>
                                                    <form class="d-inline-block" action="controller/chplDemoController.php" method="post">
                                                        <input type="hidden" name="demo_id" value="<?php echo $demo_id; ?>">
                                                        <input type="hidden" name="moveToInProgress">
                                                        <button type="submit" title="Move To In Progress?" name="" class="btn btn-dark btn-sm form-btn"><i class="fa fa-arrow-right fa-lg"> </i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <?php if($tab = 'withDeveloper_tab'){ ?>
                            <div id="withDeveloper_tab" class=" tab-pane <?php echo $withDeveloper;?> fade show">
                                <div class="table-responsive">
                                    <table id="example" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="deleteTh">#</th>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Mobile</th>
                                                <th>Email</th>
                                                <th>Message</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $i=1;
                                            $q=$d->select("demo_request_from_chpl","status = 2","ORDER BY demo_id DESC");
                                            while ($data = $q->fetch_assoc())
                                            {
                                            extract($data);
                                            ?>
                                            <tr>
                                                <td class='text-center'>
                                                    <?php if($role_id==1) { ?>
                                                    <input type="checkbox" class="multiDelteCheckbox"  value="<?php echo $data['demo_id']; ?>">
                                                    <?php } ?>
                                                </td>
                                                <td><?php echo $i++; ?></td>
                                                <td class="tableWidth"><?php echo $name; ?></td>
                                                <td class="tableWidth"><?php echo $mobile_number; ?></td>
                                                <td class="tableWidth"><?php echo $email; ?></td>
                                                <td class="tableWidth"><?php echo $message; ?></td>
                                                <td>
                                                    <form class="d-inline-block" action="controller/chplDemoController.php" method="post">
                                                        <input type="hidden" name="demo_id" value="<?php echo $demo_id; ?>">
                                                        <input type="hidden" name="moveToCompleted">
                                                        <button type="submit" title="Move To Completed?" name="" class="btn btn-dark btn-sm form-btn"><i class="fa fa-arrow-right fa-lg"> </i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <?php } ?>
                            <?php if($tab = 'solved_tab'){ ?>
                            <div id="solved_tab" class="container tab-pane <?php echo $solved_table;?> fade show">
                                <div class="table-responsive">
                                    <table id="example" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="deleteTh">#</th>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Mobile</th>
                                                <th>Email</th>
                                                <th>Message</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $i=1;
                                            $q=$d->select("demo_request_from_chpl","status = 3","ORDER BY demo_id DESC");
                                            while ($data = $q->fetch_assoc())
                                            {
                                            extract($data);
                                            ?>
                                            <tr>
                                                <td>
                                                    <?php if($role_id==1) { ?>
                                                    <input type="checkbox" class="multiDelteCheckbox"  value="<?php echo $data['demo_id']; ?>">
                                                    <?php } ?>
                                                </td>
                                                <td><?php echo $i++; ?></td>
                                                <td class="tableWidth"><?php echo $name; ?></td>
                                                <td class="tableWidth"><?php echo $mobile_number; ?></td>
                                                <td class="tableWidth"><?php echo $email; ?></td>
                                                <td class="tableWidth"><?php echo $message; ?></td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                </div><!-- End Row-->
            </div>
        </div>