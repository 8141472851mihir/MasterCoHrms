<?php
extract($_POST);
if (isset($developer_note_id) && $developer_note_id != "") {

    $q = $d->selectRow("DNM.*, SM.society_name, SM.society_id", "developer_note_master DNM, society_master SM", "DNM.company_selection = SM.society_id AND DNM.developer_note_id=$developer_note_id", "ORDER BY DNM.developer_note_id ASC");
    ?>
    <div class="content-wrapper">
        <div class="container-fluid">
            <!-- Breadcrumb-->
            <div class="row pt-2 pb-2">
                <div class="col-sm-12">
                    <h4 class="page-title">View Developer Notes</h4>
                </div>
            </div>
            <!-- End Breadcrumb-->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <?php
                            $data = mysqli_fetch_array($q);
                                extract($data);
                                ?>
                                <div class="row ">
                                    <div class="col-md-6 py-3 px-2">
                                        <span class="font-weight-bold text-capitalize fs-1">Company Name: </span>
                                        <?php echo $society_name; ?>
                                    </div>
                                    <div class="col-md-6 py-3 px-2">
                                        <span class="font-weight-bold text-capitalize fs-1">Integration Name: </span>
                                        <?php echo $integration_name; ?>
                                    </div>
                                </div>
                                <div class="row ">
                                    <div class="col-md-6 py-3 px-2">
                                        <span class="font-weight-bold text-capitalize fs-1">Contact Person Name: </span>
                                        <?php echo $contact_person_name; ?>
                                    </div>
                                    <div class="col-md-6 py-3 px-2">
                                        <span class="font-weight-bold text-capitalize fs-1">Contact Person Number: </span>
                                        <?php echo ($contact_person_number>0) ? $contact_person_number: '' ; ?>
                                    </div>
                                </div>
                                <div class="row ">
                                    <div class="col-md-6 py-3 px-2">
                                        <span class="font-weight-bold text-capitalize fs-1">Integration Type: </span>
                                        <?php if ($integration_type == '1') {
                                            echo "Client Side exe";
                                        } elseif ($integration_type == '2') {
                                            echo "Get Data For Cron Job";
                                        } else {
                                            echo "Webhook Push Data";
                                        } ?>
                                    </div>
                                    <div class="col-md-6 py-3 px-2">
                                        <span class="font-weight-bold text-capitalize fs-1">Api Document URL: </span>
                                        <a href="<?php echo $api_document_url; ?>" target="_blank"><?php echo $api_document_url; ?></a>
                                    </div>
                                </div>
                                <div class="row ">
                                    <div class="col-md-6 py-3 px-2">
                                        <span class="font-weight-bold text-capitalize fs-1">AnyDesk ID: </span>
                                        <?php echo ($anydesk_id>0) ? $anydesk_id: '' ; ?>
                                    </div>
                                    <div class="col-md-6 py-3 px-2">
                                        <span class="font-weight-bold text-capitalize fs-1">AnyDesk Password: </span>
                                        <?php echo $anydesk_password; ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 py-3 px-2">
                                        <span class="font-weight-bold text-capitalize fs-1">Api Document File: </span>
                                        <?php if ($api_document_file!="") { ?>
                                        <a href="../img/api_document_file/<?php echo $api_document_file; ?>" target="_blank" class="btn btn-sm btn-primary"><i class="fa fa-paperclip"></i></a>
                                        <?php } ?>
                                    </div>
                                    <div class="col-md-6 py-3 px-2">
                                        <span class="font-weight-bold text-capitalize fs-1">Api Postment Collection: </span>
                                        <?php if ($api_postmen_collection!="") { ?>

                                        <a href="../img/api_postmen_collection/<?php echo $api_postmen_collection; ?>" target="_blank" class="btn btn-sm btn-primary"><i class="fa fa-paperclip"></i></a>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 py-3 px-2">
                                        <span class="font-weight-bold text-capitalize fs-1">Integration Description: </span>
                                        <?php echo $integration_description; ?>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>