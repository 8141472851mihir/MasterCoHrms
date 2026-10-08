<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Breadcrumb-->
        <div class="row pt-2 pb-2">
            <div class="col-sm-9">
                <?php if (isset($_POST['edit_developer'])) { ?>
                    <h4 class="page-title">Edit Developer Notes</h4>
                <?php } else { ?>
                    <h4 class="page-title">Add Developer Notes</h4>
                <?php } ?>
                
            </div>
        </div>
        <!-- End Breadcrumb-->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form id="developerNotes" action="controller/developerNotesController.php" method="post"
                            enctype="multipart/form-data">
                            <?php
                            if (isset($_POST['edit_developer'])) {
                                extract(array_map("test_input", $_POST));
                                $q = $d->select("developer_note_master", "developer_note_id='$developer_note_id'");
                                $data = mysqli_fetch_array($q);
                            }
                            ?>
                            <?php if (isset($_POST['edit_developer'])) { ?>
                                <h4 class="form-header text-uppercase"><i class="fa fa-file"></i> Edit Developer Notes</h4>
                            <?php } else { ?>
                                <h4 class="form-header text-uppercase"><i class="fa fa-file"></i> Add Developer Notes</h4>
                            <?php } ?>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Company <span class="required">*</span></label>
                                <div class="col-sm-4">
                                    <select required="" id="company_selection" name="company_selection"
                                        class="form-control single-select">
                                        <option value="">-- Select -- </option>
                                        <?php
                                        $i = 1;
                                        $qcompany = $d->select("society_master", "society_status=0");
                                        while ($data1 = mysqli_fetch_array($qcompany)) {
                                            ?>
                                            <option <?php if ($data1['society_id'] == $data['company_selection']) {
                                                echo 'selected="selected"';
                                            } ?> value="<?php echo $data1['society_id']; ?>">
                                                <?php echo $data1['society_name']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <label class="col-sm-2 col-form-label">Integration Type <span
                                        class="required">*</span></label>
                                <div class="col-sm-4">
                                    <select required="" id="integration_type" name="integration_type"
                                        class="form-control single-select">
                                        <option selected value="">-- Select -- </option>
                                        <option <?php if ($data['integration_type'] == 1) {
                                            echo 'selected="selected"';
                                        } ?>
                                            value="1">Client Side exe </option>
                                        <option <?php if ($data['integration_type'] == 2) {
                                            echo 'selected="selected"';
                                        } ?>
                                            value="2">Get Data For Cron Job </option>
                                        <option <?php if ($data['integration_type'] == 3) {
                                            echo 'selected="selected"';
                                        } ?>
                                            value="3">Webhook Push Data </option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Integration Name <span
                                        class="required">*</span></label>
                                <div class="col-sm-4">
                                    <input type="text" autocomplete="off" maxlength="200" class="form-control"
                                        name="integration_name" value="<?php echo $data['integration_name']; ?>"
                                        required="">
                                </div>
                                <label class="col-sm-2 col-form-label">Api Document Url</label>
                                <div class="col-sm-4">
                                    <input type="url" autocomplete="off" maxlength="250" class="form-control"
                                        name="api_document_url" value="<?php echo $data['api_document_url']; ?>">
                                </div>
                            </div>
                            <div class="form-group row">

                                <label class="col-sm-2 col-form-label">Integration Description <span
                                        class="required">*</span></label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" name="integration_description" maxlength="10000"
                                        rows="8"><?php echo $data['integration_description'] ?></textarea>
                                </div>

                            </div>
                            <div class="form-group row">
                                
                                <label class="col-sm-2 col-form-label">Api Document File</label>
                                <div class="col-sm-4">
                                    <input type="file" autocomplete="off" maxlength="200" class="form-control"
                                        name="api_document_file" value="<?php echo $data['api_document_file']; ?>"
                                        accept=".pdf,.PDF,.DOC,.doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" >
                                </div>
                                <label class="col-sm-2 col-form-label">Api Postman Collection</label>
                                <div class="col-sm-4">
                                    <input type="file" autocomplete="off" maxlength="200" class="form-control"
                                        name="api_postmen_collection"
                                        value="<?php echo $data['api_postmen_collection']; ?>" accept=".json">

                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">AnyDesk ID</label>
                                <div class="col-sm-4">
                                    <input type="text" autocomplete="off" maxlength="40" class="form-control onlyNumber"
                                        name="anydesk_id" value="<?php echo $data['anydesk_id']; ?>">
                                </div>
                            
                                <label class="col-sm-2 col-form-label">AnyDesk Password</label>
                                <div class="col-sm-4">
                                    <input type="text" autocomplete="off" maxlength="40" class="form-control"
                                        name="anydesk_password" value="<?php echo $data['anydesk_password']; ?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Contact Name</label>
                                <div class="col-sm-4">
                                    <input type="text" autocomplete="off" maxlength="40" class="form-control"
                                        name="contact_person_name" value="<?php echo $data['contact_person_name']; ?>">
                                </div>
                            
                                <label class="col-sm-2 col-form-label">Contact Number</label>
                                <div class="col-sm-4">
                                    <input type="text" autocomplete="off" maxlength="10" class="form-control onlyNumber"
                                        name="contact_person_number"
                                        value="<?php echo $data['contact_person_number']; ?>">
                                </div>
                            </div>
                            <div class="form-footer text-center">
                                <?php if (isset($_POST['edit_developer'])) { ?>
                                    <input name="editDeveloperNotes" type="hidden">
                                    <input name="developer_note_id" type="hidden"
                                        value="<?php echo $data['developer_note_id']; ?>">
                                    <input name="api_postmen_collection_old" type="hidden"
                                        value="<?php echo $data['api_postmen_collection']; ?>">
                                    <input name="api_document_file_old" type="hidden"
                                        value="<?php echo $data['api_document_file']; ?>">
                                    <button type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i>
                                        UPDATE</button>
                                <?php } else { ?>
                                    <button name="addDeveloperNotes" type="submit" class="btn btn-success"><i
                                            class="fa fa-check-square-o"></i> ADD</button>
                                <?php } ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>