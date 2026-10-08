<?php
extract($_REQUEST);
if (isset($bank_id)) {
    $q = $d->selectRow('user_bank_master.*,users_master.user_id,users_master.floor_id,users_master.floor_id,users_master.block_id,users_master.user_full_name,floors_master.floor_id,floors_master.floor_name,block_master.block_id,block_master.block_name', "user_bank_master LEFT JOIN users_master ON users_master.user_id=user_bank_master.user_id LEFT JOIN floors_master ON floors_master.floor_id=users_master.floor_id LEFT JOIN block_master ON block_master.block_id=users_master.block_id", "user_bank_master.bank_id='$bank_id'");
    $data = mysqli_fetch_assoc($q);
    // print_r($data);die;
    extract($data);
}
?>

<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Breadcrumb-->
        <div class="row pt-2 pb-2">
            <div class="col-sm-9">
                <h4 class="page-title"> Add/Edit Employee Bank Details</h4>
            </div>
            <div class="col-sm-3">
            </div>
        </div>
        <!-- End Breadcrumb-->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">

                        <form id="addUserBank" name="addUserBank" action="controller/UserBankController.php" enctype="multipart/form-data" method="post">

                            <div class="form-group row">
                                <div class="col-md-4 ">
                                    <div class="form-group row w-100 mx-0">
                                        <label for="input-10" class="col-lg-12 col-md-12 col-form-label">Branch <span class="required">*</span></label>
                                        <div class="col-lg-12 col-md-12" id="">
                                            <select name="block_id" id="block_id" class="form-control single-select addRest" onchange="getVisitorFloorByBlockId(this.value)" required>
                                                <option value="">-- Select Branch --</option>
                                                <?php
                                                $qb = $d->select("block_master", "society_id='$society_id' $blockAppendQueryOnly");
                                                while ($blockData = mysqli_fetch_array($qb)) {
                                                ?>
                                                    <option <?php if ($block_id == $blockData['block_id']) {
                                                                echo 'selected';
                                                            } ?> value="<?php echo  $blockData['block_id']; ?>"><?php echo $blockData['block_name']; ?></option>
                                                <?php } ?>

                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4 ">
                                    <div class="form-group row w-100 mx-0">
                                        <label for="input-10" class="col-lg-4 col-md-4 col-form-label">Department <span class="required">*</span></label>
                                        <div class="col-lg-12 col-md-12" id="">
                                            <select name="floor_id" id="floor_id" class="form-control single-select floor_id addRest" onchange="getUser(this.value)">
                                                <option value="">--Select Department--</option>
                                                <?php
                                                $qd = $d->select("block_master,floors_master", "floors_master.society_id='$society_id' AND block_master.block_id=floors_master.block_id AND floors_master.block_id='$block_id'");
                                                while ($depaData = mysqli_fetch_array($qd)) {
                                                ?>
                                                    <option <?php if ($floor_id == $depaData['floor_id']) {
                                                                echo 'selected';
                                                            } ?> value="<?php echo  $depaData['floor_id']; ?>"><?php echo $depaData['floor_name'] . '-' . $depaData['block_name']; ?></option>
                                                <?php } ?>

                                            </select>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 ">
                                    <div class="form-group row w-100 mx-0">
                                        
                                        <label for="input-10" class="col-lg-6 col-md-6 col-form-label">Employee<span class="required">*</span></label>
                                        <div class="col-lg-12 col-md-12" id="">
                                            <select id="user_id" onchange="setAccountHolderName();" type="text" required="" class="form-control single-select addRest setAccountHolderName" name="user_id">
                                                <option value="">-- Select --</option>
                                                <?php
                                                $qb = $d->select("users_master", "society_id='$society_id' AND floor_id='$floor_id'  AND delete_status=0 AND active_status=0 $blockAppendQueryUser");
                                                while ($userData = mysqli_fetch_array($qb)) {
                                                ?>
                                                    <option <?php if ($user_id == $userData['user_id']) {
                                                                echo 'selected';
                                                            } ?> value="<?php echo  $userData['user_id']; ?>"><?php echo $userData['user_full_name']; ?></option>
                                                <?php } ?>

                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 ">
                                    <div class="form-group row w-100 mx-0">
                                        <label for="input-10" class="col-sm-6 col-form-label">Bank Name <span class="required">*</span></label>
                                        <div class="col-lg-12 col-md-12" id="">
                                            <input type="text" name="bank_name" id="bank_name" value="<?php echo $bank_name;?>" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 ">
                                    <div class="form-group row w-100 mx-0">
                                        <label for="input-10" class="col-sm-6 col-form-label">Branch Name <span class="required">*</span></label>
                                        <div class="col-lg-12 col-md-12" id="">
                                            <input type="text" name="bank_branch_name" id="bank_branch_name" value="<?php echo $bank_branch_name;?>" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 ">
                                    <div class="form-group row w-100 mx-0">
                                        <label for="input-10" class="col-sm-6 col-form-label ">Account Type<span class="required">*</span></label>
                                        <div class="col-lg-12 col-md-12" id="">
                                            <select name="account_type" id="account_type" class="form-control single-select">
                                                <option value="Current" <?php echo $account_type=='Current'?'selected':'';?>>Current</option>
                                                <option value="Saving" <?php echo $account_type=='Saving'?'selected':'';?>>Saving</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 ">
                                    <div class="form-group row w-100 mx-0">
                                        <label for="input-10" class="col-sm-6 col-form-label">Account Holder Name <span class="required">*</span></label>
                                        <div class="col-lg-12 col-md-12" id="">
                                            <input maxlength="50" type="text" name="account_holders_name" id="account_holders_name" value="<?php echo $account_holders_name;?>" class="form-control account_holders_name">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 ">
                                    <div class="form-group row w-100 mx-0">
                                        <label for="input-10" class="col-sm-6 col-form-label">Account No <span class="required">*</span></label>
                                        <div class="col-lg-12 col-md-12" id="">
                                            <input type="text" name="account_no" id="account_no" value="<?php echo $account_no;?>" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 ">
                                    <div class="form-group row w-100 mx-0">
                                        <label for="input-10" class="col-sm-6 col-form-label">IFSC Code <span class="required">*</span></label>
                                        <div class="col-lg-12 col-md-12" id="">
                                            <input type="text" maxlength="11" name="ifsc_code" id="ifsc_code" value="<?php echo $ifsc_code;?>" class="form-control text-uppercase">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 ">
                                    <div class="form-group row w-100 mx-0">
                                        <label for="input-10" class="col-sm-6 col-form-label">Customer Id/CRN No. </label>
                                        <div class="col-lg-12 col-md-12" id="">
                                            <input type="text" name="crn_no" id="crn_no" value="<?php echo $crn_no;?>" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 ">
                                    <div class="form-group row w-100 mx-0">
                                        <label for="input-10" class="col-sm-6 col-form-label">ESIC No </label>
                                        <div class="col-lg-12 col-md-12" id="">
                                            <input type="text" maxlength="10" name="esic_no" id="esic_no" value="<?php echo $esic_no;?>" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 ">
                                    <div class="form-group row w-100 mx-0">
                                        <label for="input-10" class="col-sm-6 col-form-label">Pan Card No </label>
                                        <div class="col-lg-12 col-md-12" id="">
                                            <input type="text" maxlength="10" name="pan_card_no" id="pf_no" value="<?php echo $pf_no;?>" class="form-control text-uppercase">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 ">
                                    <div class="form-group row w-100 mx-0">
                                        <label for="input-10" class="col-sm-6 col-form-label">PF/UAN No </label>
                                        <div class="col-lg-12 col-md-12" id="">
                                            <input type="text" maxlength="20" name="pf_no" id="pf_no" value="<?php echo $pf_no;?>" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-footer text-center">
                            <?php if ($data['bank_id'] != "") { ?>
                                    <input type="hidden" id="bank_id" name="bank_id" value="<?php echo $data['bank_id'];?>">
                                    <input type="hidden" name="user_id_edit" id="user_id_old" value="<?php echo $user_id;?>">
                                    <input type="hidden" name="floor_id_edit" id="floor_id_old" value="<?php echo $floor_id;?>">
                                    <input type="hidden" name="block_id_edit" id="block_id_old" value="<?php echo $block_id;?>">
                                    <button  type="submit" class="btn btn-success hideupdate"><i class="fa fa-check-square-o"></i> Update </button>
                                <?php }else{ ?>
                                    <button  type="submit" class="btn btn-success hideAdd"><i class="fa fa-check-square-o"></i> Add</button>
                                    <?php } ?>
                                    
                                <input type="hidden" name="addUserBank" value="addUserBank">
                                <button type="reset" value="add" class="btn btn-danger  cancel hideAdd" onclick="resetFrm('addHrDocumentAdd');"><i class="fa fa-check-square-o"></i> Reset</button>

                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>
        <!--End Row-->

    </div>
    <!-- End container-fluid-->

</div>
<!--End content-wrapper-->
<script src="assets/js/jquery.min.js"></script>
<script>
   
</script>