<?php 
include '../common/objectController.php';
if(isset($_POST) && !empty($_POST) )
{
    if(isset($_POST['action']) && $_POST['action']=="addDynamicBlock"){
        $counter = $_POST['counter'];
        ?>
        <div class="dynamic-block m-5 border border-1 p-2" id="block-<?php echo $counter; ?>">
            <fieldset class="scheduler-border">
                <legend class="scheduler-border">Image Details</legend>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Image <span class="required">*</span></label>
                    <div class="col-sm-10">
                        <input class="form-control-file border background_image" id="background_image_<?php echo $counter; ?>" 
                        accept="image/*" type="file" name="background_image[<?php echo $counter; ?>]" required>
                    </div>
                </div>
            </fieldset>
            
            <fieldset class="scheduler-border">
                <legend class="scheduler-border">Name Details</legend>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Show To Name?</label>
                    <div class="col-sm-4">
                        <select class="form-control single-select" name="show_to_name[<?php echo $counter; ?>]" required>
                            <option value="No">No</option>
                            <option value="Yes">Yes</option>
                        </select>
                    </div>
                    <label class="col-sm-2 col-form-label">Show From Name?</label>
                    <div class="col-sm-4">
                        <select class="form-control single-select" name="show_from_name[<?php echo $counter; ?>]" required>
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>
                    </div>
                </div>
            </fieldset>

            <fieldset class="scheduler-border">
                <legend class="scheduler-border">Other Details</legend>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label form-control-label">Status</label>
                    <div class="col-lg-4">
                        <select class="form-control single-select" name="other_status[<?php echo $counter; ?>]">
                            <option value="Active">Active</option>
                            <option value="InActive">InActive</option>
                        </select>
                    </div>
                </div>
            </fieldset>
            
            <button type="button" class="btn btn-danger remove-block mt-2">
                <i class="bi bi-trash"></i> Remove
            </button>
        </div>
        <?php
    }elseif(isset($_POST['action']) && $_POST['action']=="removeDynamicBlock"){
        echo json_encode(['success' => true]);
        exit;
    }   
}else{
    $_SESSION['msg1']="Something Wrong";
    header("location:../seasonalGreet");
} 
?>