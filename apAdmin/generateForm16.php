
<style>
    .table .select2-selection__rendered{
        width:160px !important;
    }
</style>
<?php
// if (date('m') >= 4) {
    
//     $previousYear1 = date('Y', strtotime('-3 year')) . '-' . date('Y', strtotime('-2 year'));
//     $previousYear = date('Y', strtotime('-2 year')) . '-' . date('Y', strtotime('-1 year'));
//     $currentYear = date('Y', strtotime('-1 year')) . '-' . date('Y');
// } else {
//     $previousYear1 = date('Y', strtotime('-4 year')) . '-' . date('Y', strtotime('-3 year'));
//     $previousYear = date('Y', strtotime('-3 year')) . '-' . date('Y', strtotime('-2 year'));
//     $currentYear = date('Y', strtotime('-2 year')) . '-' . date('Y', strtotime('-1 year'));
// }


if (date('m') >= 4) {
    $previousYear = date('Y', strtotime('-1 year')) . '-' . date('Y');
    $currentYear = date('Y') . '-' . date('Y', strtotime('+1 year'));
    $nextYear = date('Y', strtotime('+1 year')) . '-' . date('Y', strtotime('+2 year'));
} else {
    $previousYear = date('Y', strtotime('-2 year')) . '-' . date('Y', strtotime('-1 year'));
    $currentYear = date('Y', strtotime('-1 year')) . '-' . date('Y');
    $nextYear =  date('Y') . '-' . date('Y', strtotime('+1 year'));
}
$bId = isset($_REQUEST['bId']) ? $d->sanitizeReportFilterIdAsInt($_REQUEST['bId']) : 0;
$dId = isset($_REQUEST['dId']) ? $d->sanitizeReportFilterIdAsInt($_REQUEST['dId']) : 0;
$uId = isset($_REQUEST['uId']) ? $d->sanitizeReportFilterIdAsInt($_REQUEST['uId']) : 0;
$year = $d->sanitizeReportFilterFiscalYear(isset($_REQUEST['year']) ? $_REQUEST['year'] : '', $currentYear);
$yearArray = explode('-',$year);
$start_year = $yearArray[0];
$end_year = $yearArray[1];
?>
<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Breadcrumb-->
        <div class="row pt-2 pb-2">
            <div class="col-sm-3 col-md-6 col-6">
                <h4 class="page-title">Generate Form 16</h4>
            </div>
            <div class="col-sm-3 col-md-6 col-6">
                <div class="btn-group float-sm-right">
                </div>
            </div>
        </div>

        <form class="branchDeptFilter" action="" method="get">
            <div class="row pt-2 pb-2">
                <?php include 'selectBranchDeptEmpForFilter.php'; ?>
                <div class="col-md-2 form-group col-6">
                    <select name="year" class="form-control single-select">
                        <option <?php echo $year == $previousYear ? 'selected' : ''; ?> value="<?php echo $previousYear ?>"><?php echo $previousYear ?></option>
                        <option <?php echo ($year == $currentYear) || ($year == '') ? 'selected' : ''; ?> value="<?php echo $currentYear ?>"><?php echo $currentYear ?></option>
                        <option <?php echo $year == $nextYear ? 'selected' : ''; ?> value="<?php echo $nextYear ?>"><?php echo $nextYear ?></option>
                    </select>
                </div>
                <div class="col-md-1 form-group">
                    <button class="btn btn-success btn-sm" type="submit" class="form-control"><i class="fa fa-search" aria-hidden="true"></i></button>
                </div>
            </div>
        </form>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <?php if ($bId > 0 && $dId > 0) {
                            
                            ?>
                            <div class="table-responsive">
                                <table id="example" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Sr.No</th>
                                            <th>Action</th>
                                            <th>Employee Name</th>
                                            <th>Branch</th>
                                            <th>Department</th>
                                            <?php if($start_year < date('Y')){ ?>
                                            <th>Share With User</th>
                                            <?php } ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $blockFilterQuery = '';
                                        $deptFilterQuery = '';
                                        $userFilterQuery = '';
                                        if (isset($bId) && $bId > 0) {
                                            $blockFilterQuery = " AND users_master.block_id='$bId'";
                                        }

                                        if (isset($dId) && $dId > 0) {
                                            $deptFilterQuery = " AND users_master.floor_id='$dId'";
                                        }

                                        if (isset($uId) && $uId > 0) {
                                            $userFilterQuery = "AND users_master.user_id='$uId'";
                                        }

                                        $startYear = $start_year . '-04';
                                        $endYear = $end_year . '-03';
                                        
                                        $q = $d->selectRow("users_master.user_id, users_master.user_full_name, users_master.user_designation, block_master.block_name, floors_master.floor_name,users_master.tax_regime,users_master.metro_city_type,users_master.user_joining_date,form16_generated_master.share_with_user,form16_generated_master.form16_generated_id, form16_generated_master.is_generated,(salary.total_ctc*12) AS total_ctc,(SELECT IF(SUM(total_earning_salary),SUM(total_earning_salary),0) FROM salary_slip_master WHERE user_id=users_master.user_id AND (DATE_FORMAT(salary_start_date,'%Y-%m')>='$startYear' AND DATE_FORMAT(salary_start_date,'%Y-%m')<='$endYear')) AS joining_gross_salary", "users_master LEFT JOIN form16_generated_master ON (form16_generated_master.user_id=users_master.user_id  AND form16_generated_master.form16_year='$year'),salary, block_master, floors_master", "salary.user_id=users_master.user_id AND salary.is_delete='0' AND salary.is_preivous_salary=0 AND users_master.block_id = block_master.block_id AND users_master.floor_id = floors_master.floor_id AND users_master.society_id='$society_id' AND users_master.active_status = 0 AND users_master.delete_status = 0  $blockAppendQueryUser $blockFilterQuery $deptFilterQuery $userFilterQuery", "HAVING total_ctc >= (SELECT MIN(tax_slab_range_end) FROM `tax_slab_master` WHERE tax_slab_range_start=0) ORDER BY users_master.user_id");

                                        $counter = 1;
                                        while ($data = mysqli_fetch_array($q)) {
                                            
                                            ?>
                                            <tr>
                                                <td><?php echo $counter++; ?></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                    <?php 
                                                    
                                                        if($data['is_generated']=='1' && ($end_year < date('Y') || ($end_year == date('Y') && date('m') > 4)))
                                                        {
                                                        ?>
                                                            <form method="get" action="form16PartAPrint.php">
                                                                <input type="hidden" name="year" value="<?php echo $year;?>">
                                                                <input type="hidden" name="bId" value="<?php echo $bId;?>">
                                                                <input type="hidden" name="dId" value="<?php echo $dId;?>">
                                                                <input type="hidden" name="user_id" value="<?php echo $data['user_id'];?>">
                                                                <button type="submit" class="btn btn-info btn-sm mr-2" >Generate Part-A</button>
                                                            </form>
                                                            <form method="get" action="form16Print.php">
                                                                <input type="hidden" name="year" value="<?php echo $year;?>">
                                                                <input type="hidden" name="bId" value="<?php echo $bId;?>">
                                                                <input type="hidden" name="dId" value="<?php echo $dId;?>">
                                                                <input type="hidden" name="user_id" value="<?php echo $data['user_id'];?>">
                                                                <button type="submit" class="btn btn-warning btn-sm" >Generate Part-B</button>
                                                            </form>
                                                        
                                                        <?php } else if ($end_year < date('Y') || ($end_year == date('Y') && date('m') > 4)) { ?>
                                                            
                                                            <form id="generateForm16Form_<?php echo $data['user_id'];?>">
                                                                <input type="hidden" name="form16_generated_id" value="<?php echo $data['form16_generated_id'];?>">
                                                                <input type="hidden" name="form16_year" value="<?php echo $year;?>">
                                                                <input type="hidden" name="user_id" value="<?php echo $data['user_id'];?>">
                                                                <input type="hidden" name="tax_regime" value="<?php echo $data['tax_regime'];?>">
                                                                <input type="hidden" name="metro_city_type" value="<?php echo $data['metro_city_type'];?>">
                                                                <input type="hidden" name="bId" value="<?php echo $bId;?>">
                                                                <input type="hidden" name="dId" value="<?php echo $dId;?>">
                                                                <input type="hidden" name="form16Generate" value="form16Generate">
                                                            
                                                                <button type="button" onclick="generateForm16('<?php echo $data['user_id'];?>','<?php echo $data['joining_gross_salary'];?>');" class="btn btn-danger btn-sm mr-2" >Generate</button>
                                                            </form>
                                                            
                                                        <?php }else if($start_year == date('Y')){ ?>
                                                            <form method="get" action="estimatedTax.php">
                                                                <input type="hidden" name="year" value="<?php echo $year;?>">
                                                                <input type="hidden" name="bId" value="<?php echo $bId;?>">
                                                                <input type="hidden" name="dId" value="<?php echo $dId;?>">
                                                                <input type="hidden" name="user_id" value="<?php echo $data['user_id'];?>">
                                                                <button type="submit" class="btn btn-warning btn-sm mr-2" >Estimated Tax</button>
                                                            </form>
                                                        <?php }
                                                        if($data['user_joining_date']!='0000-00-00' && $data['user_joining_date'] > date('Y-m-d',strtotime($start_year.'-04-01')) && $data['form16_generated_id']==null)
                                                        { 
                                                            /* data-toggle="modal" data-target="#addPreviousTDS"  */?>
                                                            <button type="button" onclick="addPreviousTds('<?php echo $data['user_id']; ?>','<?php echo $year; ?>');" class="btn btn-info btn-sm mr-2" >Previous TDS</button> (Joining Date : <?php echo $data['user_joining_date'];?>)
                                                        <?php
                                                        }
                                                     ?>
                                                     </div>
                                                </td>
                                                <td><?php echo $data['user_full_name']; ?> (<?php echo $data['user_designation']; ?>)</td>
                                                <td><?php echo $data['block_name']; ?></td>
                                                <td><?php echo $data['floor_name']; ?></td>
                                                <?php if($start_year < date('Y')){ ?>
                                                <td>
                                                <?php if($data['is_generated']=='1' && ($end_year < date('Y') || ($end_year == date('Y') && date('m') > 4))){ 
                                                    ?>
                                                    <?php if($data['share_with_user']==0){ ?>
                                                        <button title="Share Setting" type="button"  class="btn btn-success bg-success btn-sm mmw-80" onclick="changeShareWithUser('<?php echo $data['form16_generated_id']; ?>','shareWithUser')">Share</button> (Not Shared)
                                                    <?php }else{ ?>
                                                        <button title="Share Setting" type="button" class="btn btn-danger  btn-sm mmw-80" onclick="changeShareWithUser('<?php echo $data['form16_generated_id']; ?>','notShareWithUser')">Not Share</button> (Shared)
                                                    <?php } ?>
                                                <?php } ?>
                                                </td>
                                                <?php } ?>
                                            </tr>
                                        <?php } ?>
                                    </tbody>

                                </table>
                            </div>
                        <?php } else { ?>
                            <div class="" role="alert">
                                <span><strong>Note :</strong> Please Select Department</span>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Share with user -->
        <?php 
        if($start_year < date('Y'))
        {
        ?>
        <div class="row pt-2 pb-2">
            <div class="col-sm-3 col-md-6 col-6">
                <h4 class="page-title">Share With User</h4>
            </div>
            <div class="col-sm-3 col-md-6 col-6">
                <div class="btn-group float-sm-right">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <?php if ($bId > 0 && $dId > 0) {
                            
                            ?>
                            <div class="table-responsive">
                                <table id="example" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Sr.No</th>
                                            <th>Employee Name</th>
                                            <th>Branch</th>
                                            <th>Department</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $blockFilterQuery = '';
                                        $deptFilterQuery = '';
                                        $userFilterQuery = '';
                                        if (isset($bId) && $bId > 0) {
                                            $blockFilterQuery = " AND users_master.block_id='$bId'";
                                        }

                                        if (isset($dId) && $dId > 0) {
                                            $deptFilterQuery = " AND users_master.floor_id='$dId'";
                                        }

                                        if (isset($uId) && $uId > 0) {
                                            $userFilterQuery = "AND users_master.user_id='$uId'";
                                        }
                                        $q = $d->selectRow("users_master.user_id, users_master.user_full_name, users_master.user_designation, block_master.block_name, floors_master.floor_name,users_master.tax_regime,form16_generated_master.share_with_user,form16_generated_master.form16_generated_id", "users_master ,form16_generated_master, block_master, floors_master", "form16_generated_master.user_id=users_master.user_id AND users_master.block_id = block_master.block_id AND users_master.floor_id = floors_master.floor_id AND users_master.society_id='$society_id' AND users_master.active_status = 0 AND users_master.delete_status = 0 AND form16_generated_master.share_with_user=1 AND form16_generated_master.is_generated=1 AND form16_generated_master.form16_year='$year' $blockAppendQueryUser $blockFilterQuery $deptFilterQuery $userFilterQuery", "ORDER BY users_master.user_id");

                                       

                                        $counter = 1;
                                        while ($data = mysqli_fetch_array($q)) {
                                            
                                            ?>
                                            <tr>
                                                <td><?php echo $counter++; ?></td>
                                                
                                                <td><?php echo $data['user_full_name']; ?> (<?php echo $data['user_designation']; ?>)</td>
                                                <td><?php echo $data['block_name']; ?></td>
                                                <td><?php echo $data['floor_name']; ?></td>
                                                
                                            </tr>
                                        <?php } ?>
                                    </tbody>

                                </table>
                            </div>
                        <?php } else { ?>
                            <div class="" role="alert">
                                <span><strong>Note :</strong> Please Select Department</span>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>

    </div>
    <!-- End container-fluid-->

</div>

<div class="modal fade" id="addPreviousTDS">
  <div class="modal-dialog ">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white">Add Previous TDS</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="align-content: center;">
        <div class="card-body">
        
            <form id="addPreviousTDSForm"  method="POST" action="controller/form16Controller.php" enctype="multipart/form-data">
                <div class="form-group row">
                    <label for="input-10" class="col-sm-4 col-form-label">Previous TDS <span class="required">*</span></label>
                    <div class="col-sm-8">
                        <input type="text" required class="form-control numberEClass onlyNumber " min="0"  id="previous_tds" name="previous_tds" value="0">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="input-10" class="col-sm-4 col-form-label">Document </label>
                    <div class="col-sm-8">
                        <input type="file" name="previous_tds_document" class="form-control taxDocumentOnly taxDocument" accept=".jpeg, .jpg, .png, .pdf, .doc, .docx, .ppt, .pptx, .csv, .xls, .xlsx" >
                    </div>
                </div>
                
                <div class="form-footer text-center">
                <input type="hidden" name="form16_year" id="form16_year" >
                <input type="hidden" name="user_id" id="user_id" >
                <input type="hidden" name="bId" value="<?php echo $bId;?>">
                <input type="hidden" name="dId" value="<?php echo $dId;?>">
                <input type="hidden" name="addPreviousTds" value="addPreviousTds">
                <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-check"></i> Submit</button>
                </div>
            </form>
        </div>
      </div>

    </div>
  </div>
</div>


<script src="assets/js/jquery.min.js"></script>
<script type="text/javascript">


    function addPreviousTds(user_id,year){
        
        $('#addPreviousTDS').modal('show');
        $('#previous_tds').val(0);
        $('#user_id').val(user_id);
        $('#form16_year').val(year).trigger("change");
        
    }


function changeShareWithUser(id,status) {
  swal({
    title: "Are you sure?",
    text: "You want to change status!",
    icon: "warning",
    buttons: true,
    dangerMode: true
  })
  .then((willDelete) =>
  {
    if(willDelete)
    {
      var csrf =$('input[name="csrf"]').val();
      $(".ajax-loader").show();
      $.ajax({
        url: "controller/form16Controller.php",
        cache: false,
        type: "POST",
        data: {id : id,status
          :status, csrf:csrf},
        success: function(response)
        {
          $(".ajax-loader").hide();
          if(response==1)
          {
            swal("Status Changed",{
              icon: "success",
            });
            document.location.reload(true);
          }
          else
          {
            document.location.reload(true);
            swal("Something Wrong!",{
              icon: "error",
            });
          }
        }
      });
    }
  });
}

function generateForm16(user_id,joining_gross_salary) {
    if(joining_gross_salary > 0)
    {
        swal({
            title: "Are you sure?",
            text: "You want Generate!",
            icon: "warning",
            buttons: true,
            dangerMode: true
        })
        .then((willDelete) =>
        {
            if(willDelete)
            {
            // var csrf =$('input[name="csrf"]').val();
            $(".ajax-loader").show();
            $.ajax({
                url: "controller/form16Controller.php",
                cache: false,
                type: "POST",
                data: $("#generateForm16Form_"+user_id).serialize(),
                success: function(response)
                {
                $(".ajax-loader").hide();
                if(response==1)
                {
                    swal("Form16 Generated Successfully",{
                    icon: "success",
                    });
                    document.location.reload(true);
                }
                else
                {
                    document.location.reload(true);
                    swal("Something Wrong!",{
                    icon: "error",
                    });
                }
                }
            });
            }
        });
    }else{
        swal("Salary Not Generated!!!",{
            icon: "warning",
        })
    }
}
</script>