<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
$st = $d->sanitizeReportFilterIdAsInt($_REQUEST['st'] ?? 0);
if (date('m') >= 4) {
    $currentYear = date('Y') . '-' . date('Y', strtotime('+1 year'));
    $previousYear = date('Y', strtotime('-1 year')) . '-' . date('Y');
    $nextYear = date('Y', strtotime('+1 year')) . '-' . date('Y', strtotime('+2 year'));
} else {
    $currentYear = date('Y', strtotime('-1 year')) . '-' . date('Y');
    $previousYear = date('Y', strtotime('-2 year')) . '-' . date('Y', strtotime('-1 year'));
    $nextYear = date('Y') . '-' . date('Y', strtotime('+1 year'));
}
$year = $d->sanitizeReportFilterFiscalYear(isset($_REQUEST['year']) ? $_REQUEST['year'] : '', $currentYear);
$country_id = 101;
?>
<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Breadcrumb-->
        <div class="row pt-2 pb-2">
            <div class="col-sm-3 col-md-4 col-6">
                <h4 class="page-title">Income Tax Slabs</h4>
            </div>
            
            <div class="col-sm-3 col-md-4 col-6">
            <?php if($country_id == '101'){ ?>
                <form action="" method="get">
                    <input type="hidden" name="st" value="<?php echo $st; ?>">
                    <select name="year" class="form-control single-select" onchange="this.form.submit();">
                        <option <?php echo ($year == $previousYear) ? 'selected' : ''; ?> value="<?php echo $previousYear ?>"><?php echo $previousYear ?></option>
                        <option <?php echo ($year == $currentYear) ? 'selected' : ''; ?> value="<?php echo $currentYear ?>"><?php echo $currentYear ?></option>
                        <option <?php echo ($year == $nextYear) ? 'selected' : ''; ?> value="<?php echo $nextYear ?>"><?php echo $nextYear ?></option>
                    </select>
                </form>
                <?php } ?>
            </div>
            
            <div class="col-sm-3 col-md-4 col-6">
                <div class="btn-group float-sm-right">
                    <?php if($country_id == '101'){ ?>
                    <a href="javascript:void(0);" data-toggle="modal" data-target="#importSlabModal" onclick="importData();" class="btn mr-1 btn-sm btn-warning waves-effect waves-light"> Copy Slab</a>
                    <?php } ?>
                    <a href="syncSlabs" class="btn mr-1 btn-sm btn-danger waves-effect waves-light"><i class="fa fa-plus mr-1"></i>Sync Slab</a>
                    <a href="addIncomeTaxSlab?st=<?php echo $st; ?>" class="btn mr-1 btn-sm btn-primary waves-effect waves-light"><i class="fa fa-plus mr-1"></i>Add</a>
                </div>
            </div>
        </div>

        <div class="row blockList">
        <?php if($country_id == '101'){ ?>
            <div class="<?php echo $country_id == '101'?'col-6':'col-12'; ?>">
                <a href="incomeTaxSlabs?st=0&year=<?php echo $year;?>">
                    <div class="mb-3 card <?php echo $st == '0' ? 'bg-primary' : 'bg-google-plus'; ?> ">
                        <div class="card-body text-center text-white">
                            <h6 class="mt-2 text-white">New Tax Regime</h6>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6">
                <a href="incomeTaxSlabs?st=1&year=<?php echo $year;?>">
                    <div class="mb-3 card <?php echo $st == '1' ? 'bg-primary' : 'bg-google-plus'; ?> ">
                        <div class="card-body text-center text-white">
                            <h6 class="mt-2 text-white">Old Tax Regime</h6>
                        </div>
                    </div>
                </a>
            </div>
            <?php } ?>
        </div>

        <?php 
        $slabAppendQuery = "";
        if($country_id == '101'){ 
            $slabAppendQuery = "  AND tax_slab_type = '$st' AND tax_slab_year = '$year'";
        }else{
            $slabAppendQuery = " AND tax_slab_year = ''";
        }
        
        if ($st == 1) {
            
            $q = $d->selectRow("tax_slab_master.*", "tax_slab_master", "0=0 $slabAppendQuery ", "ORDER BY tax_slab_range_start ASC");
            $below60YearsArray = array();
            $between60to80YearsArray = array();
            $above80YearsArray = array();

            while ($data = mysqli_fetch_array($q)) {
                if ($data['tax_slab_age_group'] == 1) {
                    array_push($below60YearsArray, $data);
                }
                if ($data['tax_slab_age_group'] == 2) {
                    array_push($between60to80YearsArray, $data);
                }
                if ($data['tax_slab_age_group'] == 3) {
                    array_push($above80YearsArray, $data);
                }
            } ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <ul class="nav nav-pills nav-tabs-primary  nav-justified">
                                <li class="nav-item">
                                    <a href="javascript:void();" data-target="#below60Years" data-toggle="pill" class="nav-link active"><span class="hidden-xs">Below 60 Years</span></a>
                                </li>
                                <li class="nav-item">
                                    <a href="javascript:void();" data-target="#between60to80Years" data-toggle="pill" class="nav-link "><span class="hidden-xs">60 to 80 Years</span></a>
                                </li>
                                <li class="nav-item">
                                    <a href="javascript:void();" data-target="#above80Years" data-toggle="pill" class="nav-link "><span class="hidden-xs">Above 80 Years</span></a>
                                </li>
                            </ul>
                            <div class="tab-content p-3">
                                <div class="tab-pane active" id="below60Years">
                                    <div class="table-responsive">
                                        <table class="table table-bordered exampleReport">
                                            <thead>
                                                <tr>
                                                    <th>Sr. No</th>
                                                    <th>Slab Range</th>
                                                    <th>Percentage</th>
                                                    <th>Slab Year</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $slabArray = array();
                                                
                                                foreach ($below60YearsArray as $key => $data) { 
                                                    
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $key + 1; ?></td>
                                                        <td>
                                                            <?php
                                                            $slab_range = $currency . ' ' . $data['tax_slab_range_start'] . ' - ' . $currency . ' ' . $data['tax_slab_range_end'];
                                                            if ($data['tax_slab_range_start'] == 0) {
                                                                $slab_range = 'Up to ' . $currency . ' ' . $data['tax_slab_range_end'];
                                                            }
                                                            if ($data['tax_slab_range_end'] == 0) {
                                                                $slab_range = $currency . ' ' . $data['tax_slab_range_start'] . ' and above';
                                                            }
                                                            echo $slab_range;
                                                            ?>
                                                        </td>
                                                        <td><?php echo $data['tax_slab_percentage']; ?>%</td>
                                                        <td><?php echo $data['tax_slab_year']; ?></td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <form action="addIncomeTaxSlab" method="post">
                                                                    <input type="hidden" name="tax_slab_id" value="<?php echo $data['tax_slab_id']; ?>">
                                                                    <input type="hidden" name="editIncomeTaxSlab" value="editIncomeTaxSlab">
                                                                    <button type="submit" class="btn btn-sm btn-primary mr-2"> <i class="fa fa-pencil"></i></button>
                                                                </form>
                                                                <form action="controller/IncomeTaxSlabController.php" method="post">
                                                                    <input type="hidden" name="tax_slab_id" value="<?php echo $data['tax_slab_id']; ?>">
                                                                    <input type="hidden" name="tax_slab_type" value="<?php echo $data['tax_slab_type']; ?>">
                                                                    <input type="hidden" name="deleteIncomeTaxSlab" value="deleteIncomeTaxSlab">
                                                                    <button type="submit" class="btn btn-sm btn-danger mr-2 form-btn"> <i class="fa fa-trash"></i></button>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php 
                                                $slabData = array(
                                                    "tax_slab_id" => $data['tax_slab_id'],
                                                    "slab" => $slab_range." (Below 60 Years)",
                                                );
                                                array_push($slabArray,$slabData);
                                            } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane" id="between60to80Years">
                                    <div class="table-responsive">
                                        <table class="table table-bordered exampleReport">
                                            <thead>
                                                <tr>
                                                    <th>Sr. No</th>
                                                    <th>Slab Range</th>
                                                    <th>Percentage</th>
                                                    <th>Slab Year</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                
                                                foreach ($between60to80YearsArray as $key => $data) { 
                                                    
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $key + 1; ?></td>
                                                        <td>
                                                            <?php
                                                            $slab_range = $currency . ' ' . $data['tax_slab_range_start'] . ' - ' . $currency . ' ' . $data['tax_slab_range_end'];
                                                            if ($data['tax_slab_range_start'] == 0) {
                                                                $slab_range = 'Up to ' . $currency . ' ' . $data['tax_slab_range_end'];
                                                            }
                                                            if ($data['tax_slab_range_end'] == 0) {
                                                                $slab_range = $currency . ' ' . $data['tax_slab_range_start'] . ' and above';
                                                            }
                                                            echo $slab_range;
                                                            ?>
                                                        </td>
                                                        <td><?php echo $data['tax_slab_percentage']; ?>%</td>
                                                        <td><?php echo $data['tax_slab_year']; ?></td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <form action="addIncomeTaxSlab" method="post">
                                                                    <input type="hidden" name="tax_slab_id" value="<?php echo $data['tax_slab_id']; ?>">
                                                                    <input type="hidden" name="editIncomeTaxSlab" value="editIncomeTaxSlab">
                                                                    <button type="submit" class="btn btn-sm btn-primary mr-2"> <i class="fa fa-pencil"></i></button>
                                                                </form>
                                                                <form action="controller/IncomeTaxSlabController.php" method="post">
                                                                    <input type="hidden" name="tax_slab_id" value="<?php echo $data['tax_slab_id']; ?>">
                                                                    <input type="hidden" name="tax_slab_type" value="<?php echo $data['tax_slab_type']; ?>">
                                                                    <input type="hidden" name="deleteIncomeTaxSlab" value="deleteIncomeTaxSlab">
                                                                    <button type="submit" class="btn btn-sm btn-danger mr-2 form-btn"> <i class="fa fa-trash"></i></button>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php 
                                                $slabData = array(
                                                    "tax_slab_id" => $data['tax_slab_id'],
                                                    "slab" => $slab_range." (60 to 80 Years)",
                                                );
                                                array_push($slabArray,$slabData);
                                            } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane" id="above80Years">
                                    <div class="table-responsive">
                                        <table class="table table-bordered exampleReport">
                                            <thead>
                                                <tr>
                                                    <th>Sr. No</th>
                                                    <th>Slab Range</th>
                                                    <th>Percentage</th>
                                                    <th>Slab Year</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                
                                                foreach ($above80YearsArray as $key => $data) {
                                                    
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $key + 1; ?></td>
                                                        <td>
                                                            <?php
                                                            $slab_range = $currency . ' ' . $data['tax_slab_range_start'] . ' - ' . $currency . ' ' . $data['tax_slab_range_end'];
                                                            if ($data['tax_slab_range_start'] == 0) {
                                                                $slab_range = 'Up to ' . $currency . ' ' . $data['tax_slab_range_end'];
                                                            }
                                                            if ($data['tax_slab_range_end'] == 0) {
                                                                $slab_range = $currency . ' ' . $data['tax_slab_range_start'] . ' and above';
                                                            }
                                                            echo $slab_range;
                                                            ?>
                                                        </td>
                                                        <td><?php echo $data['tax_slab_percentage']; ?>%</td>
                                                        <td><?php echo $data['tax_slab_year']; ?></td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <form action="addIncomeTaxSlab" method="post">
                                                                    <input type="hidden" name="tax_slab_id" value="<?php echo $data['tax_slab_id']; ?>">
                                                                    <button type="submit" class="btn btn-sm btn-primary mr-2"> <i class="fa fa-pencil"></i></button>
                                                                </form>
                                                                <form action="controller/IncomeTaxSlabController.php" method="post">
                                                                    <input type="hidden" name="tax_slab_id" value="<?php echo $data['tax_slab_id']; ?>">
                                                                    <input type="hidden" name="tax_slab_type" value="<?php echo $data['tax_slab_type']; ?>">
                                                                    <input type="hidden" name="deleteIncomeTaxSlab" value="deleteIncomeTaxSlab">
                                                                    <button type="submit" class="btn btn-sm btn-danger mr-2 form-btn"> <i class="fa fa-trash"></i></button>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php 
                                                $slabData = array(
                                                    "tax_slab_id" => $data['tax_slab_id'],
                                                    "slab" => $slab_range." (Above 80 Years)",
                                                );
                                                array_push($slabArray,$slabData);
                                            } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } else { ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="example" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Sr. No</th>
                                            <th>Slab Range</th>
                                            <th>Percentage</th>
                                            <?php if($country_id == '101'){ ?>
                                            <th>Slab Year</th>
                                            <th>Action</th>
                                            <?php } ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 1;
                                        $q = $d->selectRow("tax_slab_master.*", "tax_slab_master", "0=0 $slabAppendQuery", "ORDER BY tax_slab_range_start ASC");
                                        $slabArray = array();
                                        $counter = 1;
                                        while ($data = mysqli_fetch_array($q)) { 
                                            
                                            ?>
                                            <tr>
                                                <td><?php echo $counter++; ?></td>
                                                <td>
                                                    <?php
                                                    $slab_range = $currency . ' ' . $data['tax_slab_range_start'] . ' - ' . $currency . ' ' . $data['tax_slab_range_end'];
                                                    if ($data['tax_slab_range_start'] == 0) {
                                                        $slab_range = 'Up to ' . $currency . ' ' . $data['tax_slab_range_end'];
                                                    }
                                                    if ($data['tax_slab_range_end'] == 0) {
                                                        $slab_range = $currency . ' ' . $data['tax_slab_range_start'] . ' and above';
                                                    }
                                                    echo $slab_range;
                                                    ?>
                                                </td>
                                                <td><?php echo $data['tax_slab_percentage']; ?>%</td>
                                                <?php if($country_id == '101'){ ?>
                                                <td><?php echo $data['tax_slab_year']; ?></td>
                                                <?php } ?>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <form action="addIncomeTaxSlab" method="post">
                                                            <input type="hidden" name="tax_slab_id" value="<?php echo $data['tax_slab_id']; ?>">
                                                            <input type="hidden" name="editIncomeTaxSlab" value="editIncomeTaxSlab">
                                                            <button type="submit" class="btn btn-sm btn-primary mr-2"> <i class="fa fa-pencil"></i></button>
                                                        </form>
                                                        <form action="controller/IncomeTaxSlabController.php" method="post">
                                                            <input type="hidden" name="tax_slab_id" value="<?php echo $data['tax_slab_id']; ?>">
                                                            <input type="hidden" name="tax_slab_type" value="<?php echo $data['tax_slab_type']; ?>">
                                                            <input type="hidden" name="deleteIncomeTaxSlab" value="deleteIncomeTaxSlab">
                                                            <button type="submit" class="btn btn-sm btn-danger mr-2 form-btn"> <i class="fa fa-trash"></i></button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php 
                                        $slabData = array(
                                            "tax_slab_id" => $data['tax_slab_id'],
                                            "slab" => $slab_range,
                                        );
                                        array_push($slabArray,$slabData);
                                    } ?>
                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php }  ?>

    </div>
    <!-- End container-fluid-->

</div>

<?php
$yearArray = explode('-',$year);

$nextYear = date('Y', strtotime($yearArray[1].'-01-01')) . '-' . date('Y', strtotime($yearArray[1].'-01-01'.' +1 year'));
$nextYear1 = date('Y', strtotime($yearArray[1].'-01-01'.' +1 year')) . '-' . date('Y', strtotime($yearArray[1].'-01-01'.' +2 year'));
$nextYear2 = date('Y', strtotime($yearArray[1].'-01-01'.' +2 year')) . '-' . date('Y', strtotime($yearArray[1].'-01-01'.' +3 year'));
?>

<!-- copy slab -->

<div class="modal fade" id="importSlabModal">
    <div class="modal-dialog ">
        <div class="modal-content border-primary">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white">Copy Income Tax Slab</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body"  style="align-content: center;">
                <div class="card-body">

                    <form id="importIncomeTaxSlabForm" action="controller/form16Controller.php" enctype="multipart/form-data" method="post">
                        <div class="form-group row">
                            <label for="input-10" class="col-lg-12 col-md-12 col-form-label">Year <span class="required">*</span></label>
                            <div class="col-lg-12 col-md-12" id="">
                                <select name="year" id="year" class="form-control single-select">
                                    <option value="<?php echo $nextYear; ?>" selected ><?php echo $nextYear; ?></option>
                                    <option value="<?php echo $nextYear1; ?>"><?php echo $nextYear1; ?></option>
                                    <option value="<?php echo $nextYear2; ?>"><?php echo $nextYear2; ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="input-10" class="col-lg-12 col-md-12 col-form-label">Slab <span class="required">*</span></label>
                            <div class="col-lg-12 col-md-12">
                                <select type="text"  class="form-control multiple-select" multiple  name="tax_slab_id[]" id="tax_slab_id">
                                    <?php
                                    
                                    foreach($slabArray as $val){
                                    ?>
                                    <option selected value="<?php echo $val['tax_slab_id'];?>"> <?php echo $val['slab'];?></option>
                                    <?php } ?>

                                </select>
                            </div>
                        </div>
                        <div class="form-footer text-center">
                            <input type="hidden" name="st" value="<?php echo $_GET['st'];?>">
                            <input type="hidden" name="importTaxSlab" value="importTaxSlab">
                            <button type="submit" class="btn btn-success hideAdd"><i class="fa fa-check-square-o"></i> SUBMIT</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="exportSlabModal">
    <div class="modal-dialog ">
        <div class="modal-content border-primary">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white">Copy Income Tax Slab</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body"  style="align-content: center;">
                <div class="card-body">

                    <form id="importIncomeTaxSlabForm" action="controller/form16Controller.php" enctype="multipart/form-data" method="post">
                        <div class="form-group row">
                            <label for="company" class="col-lg-12 col-md-12 col-form-label">Company <span class="required">*</span></label>
                            <div class="col-lg-12 col-md-12" id="">
                                <select name="company" id="company" class="form-control single-select">
                                    
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="input-10" class="col-lg-12 col-md-12 col-form-label">Slab <span class="required">*</span></label>
                            <div class="col-lg-12 col-md-12">
                                <select type="text"  class="form-control multiple-select" multiple  name="tax_slab_id[]" id="tax_slab_id">
                                    <?php
                                    
                                    foreach($slabArray as $val){
                                    ?>
                                    <option selected value="<?php echo $val['tax_slab_id'];?>"> <?php echo $val['slab'];?></option>
                                    <?php } ?>

                                </select>
                            </div>
                        </div>
                        <div class="form-footer text-center">
                            <input type="hidden" name="st" value="<?php echo $_GET['st'];?>">
                            <input type="hidden" name="importTaxSlab" value="importTaxSlab">
                            <button type="submit" class="btn btn-success hideAdd"><i class="fa fa-check-square-o"></i> SUBMIT</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- copy slab -->

<script>
    function importData(){
        $("#year").val("<?php echo $nextYear; ?>").trigger("change");
        $("#tax_slab_id  option").prop("selected", "selected");
        $("#tax_slab_id").trigger("change");
        
    }
</script>