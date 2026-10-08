<?php
extract($_REQUEST);
if (isset($editTemplate) && $editTemplate=='editTemplate' && isset($template_id) && $template_id!='') {
    $q = $d->selectRow(
        "template_name,
        template_sub,
        template_text",
        "template_master",
        "template_master.template_id='$template_id' AND template_status='0'"
    );
    $data = mysqli_fetch_assoc($q);
    extract($data);
}
?>
<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Breadcrumb-->
        <div class="row pt-2 pb-2">
            <div class="col-sm-9">
                <h4 class="page-title">Add/Edit Template</h4>
            </div>
        </div>
        <!-- End Breadcrumb-->
        <div class="row">
            <div class="col">
                <div class="card">
                    <form  id="addEditTemplateForm" enctype="multipart/form-data" action="controller/templateController.php" method="post">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <label for="template_name" class="col-form-label">Template Name <span class="text-danger">*</span></label>
                                   <input type="text" class="form-control" autocomplete="off" name="template_name"  id="template_name"  maxlength="50" placeholder="Template Name" required value="<?php echo $template_name; ?>" >
                                </div>

                                <div class="col-sm-12">
                                    <label for="template_sub" class="col-form-label">Email Subject <span class="text-danger">*</span></label>
                                   <input type="text" class="form-control" autocomplete="off" name="template_sub"  id="template_sub"  maxlength="200" placeholder="Enter Subject" required value="<?php echo $template_sub; ?>" >
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                     <div>
                                     <span class="text-danger">Note : You can copy variable from Variable fields from Email Content editor to email subject using {VARIABLE_NAME}</span>
                                 </div>
                                    <label for="template_text" class="col-form-label">Email <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="summernoteEditor1" name="template_text" minlength="100" placeholder="Type the content here!" required><?php echo $template_text; ?>
                                 </textarea>
                                    <input type="hidden" id="template_text_error" name="template_text_error"/>
                                </div>
                            </div>
                                <div class="form-footer text-center">
                                <?php if ($template_id != "") { ?>
                                    <input type="hidden" name="template_id" value="<?php echo $template_id; ?>">
                                    <button type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i>update</button>
                                <?php } else { ?>
                                    <button type="submit" class="btn btn-success" onclick="return validateForm()"><i class="fa fa-check-square-o"></i>ADD</button>
                                <?php } ?>
                                <input type="hidden" name="addTemplate" value="addTemplate">
                            </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div><!-- End container-fluid-->
</div><!--End content-wrapper-->
<!-- <script>
 function validateForm() {
        var templateTitle = $('#template_name').val();
        var templateText = $('#summernoteEditor').summernote('code');
        if (!templateTitle.trim() && !templateText.trim()) {
            swal('', 'All the field are Required', 'error');
            return false;
        }
        if (!templateTitle.trim() || templateTitle.trim().indexOf(' ') !== -1) {
            swal('', 'The Template Name Cannot Be Empty!', 'error');
            return false;
        }
        if (!templateText.trim() || templateText.trim().indexOf(' ') !== -1) {
            swal('', 'The Email Content Cannot Be empty!', 'error');
            return false;
        }
        return true;
    }
</script> -->