<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Breadcrumb-->
        <div class="row pt-2 pb-2">
            <div class="col-sm-4 d-flex align-items-center">
                <h4 class="page-title">Manage Training Meeting</h4>
            </div>
             <div class="col-sm-4">
                <?php
                $companyFilter = isset($_GET['company_filter']) ? $_GET['company_filter'] : '';
                $companyQuery = $d->select("society_master","society_status=0");
                ?>
                <select id="company_filter" class="form-control single-select">
                    <option value="all"
                        <?php echo ($companyFilter == 'all' || $companyFilter == '') ? 'selected' : ''; ?>>
                        All
                    </option>
                    <?php
                    while($companyData = mysqli_fetch_array($companyQuery)) {
                        $selected = '';
                        if ($companyFilter == $companyData['society_id']) {
                            $selected = 'selected';
                        }
                    ?>
                        <option value="<?php echo $companyData['society_id']; ?>"
                            <?php echo $selected; ?>>
                            <?php echo $companyData['society_name']; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-sm-4 text-right">
                <div class="btn-group float-sm-right">
                    <?php
                   $returnUrl = 'manageTrainingSlots';
                    $queryParams = [];
                    if (isset($_GET['activeTab'])) {
                        $queryParams[] =
                            'activeTab=' .
                            urlencode($_GET['activeTab']);
                    }
                   if (isset($_GET['company_filter']) && $_GET['company_filter'] != '' && $_GET['company_filter'] != 'all') {
                        $queryParams[] =
                            'company_filter=' .
                            urlencode($_GET['company_filter']);
                    }
                    if (!empty($queryParams)) {
                        $returnUrl .= '?' .
                            implode('&', $queryParams);
                    }
                    ?>
                    <a href="slotAvailability?return=<?php echo urlencode($returnUrl); ?>" class="btn btn-info btn-sm mr-2">
                        <i class="fa fa-calendar-check-o mr-1"></i>Check Slot Availability & Status
                    </a>
                    <a href="addBatchSlot" class="btn btn-primary btn-sm"><i class="fa fa-plus mr-1"></i>Add Batch Slot</a>
                </div>
            </div>
        </div>
        <!-- End Breadcrumb-->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <ul class="nav nav-tabs nav-tabs-info nav-justified">
                        <li class="nav-item">
                            <a class="nav-link <?php echo ((isset($_GET['activeTab']) && $_GET['activeTab'] == 'today') || !isset($_GET['activeTab'])) ? 'active show' : ''; ?>" data-toggle="tab" href="#tab-today">Today's Meetings</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo isset($_GET['activeTab']) && $_GET['activeTab'] == 'upcoming' ? 'active' : ''; ?>" data-toggle="tab" href="#tab-upcoming">Upcoming Meetings</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo isset($_GET['activeTab']) && $_GET['activeTab'] == 'completed' ? 'active' : ''; ?>" data-toggle="tab" href="#tab-completed">Previous Meetings</a>
                        </li>
                    </ul>
                    <div class="card-body">
                        <?php
                        $i = 1;
                        $q = $d->selectRow(
                            "batch_slot_master.*, bms_admin_master.admin_name AS trainer_name",
                            "batch_slot_master LEFT JOIN bms_admin_master ON bms_admin_master.admin_id=batch_slot_master.trainer_id",
                            "batch_slot_master.year = YEAR(CURDATE())",
                            ""
                        );

                        $today = date('Y-m-d');
                        $allMeetings = [];

                        while ($row = mysqli_fetch_array($q)) {
                            $meetingClass = '';
                            if ($row['date'] > $today) {
                                $meetingClass = 'upcoming';
                            } elseif ($row['date'] == $today) {
                                $meetingClass = 'today';
                            } else {
                                $meetingClass = 'completed';
                            }

                            $allMeetings[] = [
                                'meetingClass' => $meetingClass,
                                'slot_id' => $row['slot_id'],
                                'i' => $i++,
                                'slot_name' => $row['slot_name'], // Keep original for processing
                                'slot_name_display' => htmlspecialchars($row['slot_name']), // For display
                                'trainer_name' => htmlspecialchars($row['trainer_name']),
                                'trainer_id' => $row['trainer_id'],
                                'city' => htmlspecialchars($row['city']),
                                'date' => $row['date'],
                                'meeting_status' => $row['meeting_status'],
                                'from_time' => date('h:i:s A', strtotime($row['from_time'])),
                                'to_time' => date('h:i:s A', strtotime($row['to_time'])),
                                'start_date' => date('d F Y', strtotime($row['start_date'])),
                                'company_id' => $row['company_id'],
                                'recording_link' => $row['recording_link'],
                                'training_password' => $row['training_password'],
                                'mom_attachment' => $row['mom_attachment']
                            ];
                        }
                        ?>
                        <div class="tab-content">
                            <div id="tab-upcoming" class="container-fluid tab-pane fade <?php echo isset($_GET['activeTab']) && $_GET['activeTab'] == 'upcoming' ? 'show active' : ''; ?>">
                                <div class="table-responsive">
                                    <table id="default-datatable2" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Slot Name</th>
                                                <th>Trainer Name</th>
                                                <th>City</th>
                                                <th>Meeting Day</th>
                                                <th>Meeting Date</th>
                                                <th>Meeting Start Time</th>
                                                <th>Meeting End Time</th>
                                                <th>Batch Start Date</th>
                                                <th>Total Company</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $i = 1;

                                            foreach ($allMeetings as $row) {
                                               $companyMatch = true;
                                                if (!empty($companyFilter)) {
                                                    $companyIds = explode(',', $row['company_id']);
                                                    $companyIds = array_map('trim', $companyIds);
                                                    $companyMatch = in_array($companyFilter, $companyIds);
                                                }

                                                if ($row['meetingClass'] === 'upcoming' && $companyMatch){
                                                    $societies = explode(',', $row['company_id']);
                                                    $societies = array_filter($societies);
                                                    $totalCompanies = count($societies);
                                            ?>
                                                    <tr>
                                                        <td><?php echo $i++; ?></td>
                                                        <td>
                                                            <?php
                                                            // Format slot name: remove meeting number (M1, M2, etc.) and show count
                                                            $slotName = $row['slot_name'];
                                                            // Remove meeting number pattern like -M1, -M2, etc.
                                                            $slotName = preg_replace('/-M\d+(-?\d*)$/', '$1', $slotName);
                                                            // If there's a count suffix like -2, format it as " - 2"
                                                            if (preg_match('/^(.+)-(\d+)$/', $slotName, $matches)) {
                                                                $baseName = htmlspecialchars($matches[1]);
                                                                $countNum = htmlspecialchars($matches[2]);
                                                                echo $baseName . ' - ' . $countNum;
                                                            } else {
                                                                echo htmlspecialchars($slotName);
                                                            }
                                                            ?>
                                                        </td>

                                                        <td><?php echo $row['trainer_name'];
                                                            if ($row['meeting_status'] == '0') { ?>
                                                                <button data-toggle="modal" data-target="#changeTrainerModel"
                                                                    title="Change Trainer?"
                                                                    class="btn text-warning btn-sm ml-2"
                                                                    onclick="changeTrainerid('<?php echo $row['trainer_id']; ?>','<?php echo $row['slot_id']; ?>','<?php echo $row['meetingClass']; ?>')">
                                                                    <i class="fa fa-pencil"></i>
                                                                </button>
                                                            <?php } ?>
                                                        </td>
                                                        <td><?php echo $row['city'] ?></td>
                                                        <td><?php echo date('l', strtotime($row['date'])) ?></td>
                                                        <td><?php echo date('d F Y', strtotime($row['date'])) ?></td>
                                                        <td><?php echo $row['from_time'] ?></td>
                                                        <td><?php echo $row['to_time'] ?></td>
                                                        <td><?php echo $row['start_date'] ?></td>
                                                        <td><?= $totalCompanies ?></td>
                                                    </tr>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div id="tab-today" class="container-fluid tab-pane fade <?php echo !isset($_GET['activeTab']) || $_GET['activeTab'] == 'today' ? 'show active' : ''; ?>">
                                <div class="table-responsive">
                                    <table id="default-datatable3" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Action</th>
                                                <th>Slot Name</th>
                                                <th>Status</th>
                                                <th>Trainer Name</th>
                                                <th>City</th>
                                                <th>Meeting Day</th>
                                                <th>Meeting Date</th>
                                                <th>Meeting Start Time</th>
                                                <th>Meeting End Time</th>
                                                <th>Batch Start Date</th>
                                                <th>Total Company</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $i = 1;
                                            foreach ($allMeetings as $row) {
                                                $companyMatch = true;
                                                if (!empty($companyFilter)) {
                                                    $companyIds = explode(',', $row['company_id']);
                                                    $companyIds = array_map('trim', $companyIds);
                                                    $companyMatch = in_array($companyFilter, $companyIds);
                                                }
                                                if ($row['meetingClass'] === 'today' && $companyMatch) {
                                                    $societies = explode(',', $row['company_id']);
                                                    $societies = array_filter($societies);
                                                    $totalCompanies = count($societies);
                                            ?>
                                                    <tr>
                                                        <td><?php echo $i++; ?></td>
                                                        <td>
                                                            <?php if ($row['meeting_status'] != '1') {
                                                            ?>
                                                                <form action="manageTrainingMeeting" method="GET">
                                                                    <input type="hidden" name="meeting_slot_id" value="<?php echo htmlspecialchars($row['slot_id']); ?>">
                                                                       <input type="hidden" name="company_filter" value="<?php echo isset($_GET['company_filter']) ? $_GET['company_filter'] : ''; ?>">
                                                                    <button type="submit" title="Start Meeting" class="btn btn-primary btn-sm" style="width: 115px;">Start Meeting</button>
                                                                </form>
                                                            <?php
                                                            } else { ?>
                                                                <form action="viewCompletedMeetings" method="GET" class="d-inline-block">
                                                                    <input type="hidden" name="trainer_id" value="<?php echo htmlspecialchars($row['trainer_id']); ?>">
                                                                    <input type="hidden" name="meeting_slot_id" value="<?php echo htmlspecialchars($row['slot_id']); ?>">
                                                                      <input type="hidden" name="company_filter" value="<?php echo isset($_GET['company_filter']) ? $_GET['company_filter'] : ''; ?>">
                                                                    <button type="submit" title="view" class="btn btn-primary btn-sm" style="width: 115px;">view</button>
                                                                </form>
                                                                <?php if (!empty($row['mom_attachment'])): ?>
                                                                    <a href="../img/training_meetings/<?php echo htmlspecialchars($row['mom_attachment']); ?>" target="_blank" title="View MOM" class="btn btn-info btn-sm ml-1">
                                                                        <i class="fa fa-file-image-o"></i> MOM
                                                                    </a>
                                                                <?php endif; ?>
                                                            <?php } ?>

                                                        </td>
                                                        <td>
                                                            <?php
                                                            // Format slot name: remove meeting number (M1, M2, etc.) and show count
                                                            $slotName = $row['slot_name'];
                                                            // Remove meeting number pattern like -M1, -M2, etc.
                                                            $slotName = preg_replace('/-M\d+(-?\d*)$/', '$1', $slotName);
                                                            // If there's a count suffix like -2, format it as " - 2"
                                                            if (preg_match('/^(.+)-(\d+)$/', $slotName, $matches)) {
                                                                $baseName = htmlspecialchars($matches[1]);
                                                                $countNum = htmlspecialchars($matches[2]);
                                                                echo $baseName . ' - ' . $countNum;
                                                            } else {
                                                                echo htmlspecialchars($slotName);
                                                            }
                                                            ?>
                                                        </td>
                                                        <td><?php if ($row['meeting_status'] != '1') {
                                                                echo "<span class='text-danger'>Pending</span>";
                                                            } else {
                                                                echo "<span class='text-success'>Completed</span>";
                                                            } ?></td>
                                                        <td>
                                                            <?php echo $row['trainer_name'];
                                                            if ($row['meeting_status'] == '0') { ?>
                                                                <button data-toggle="modal" data-target="#changeTrainerModel"
                                                                    title="Change Trainer?"
                                                                    class="btn text-warning btn-sm ml-2"
                                                                    onclick="changeTrainerid('<?php echo $row['trainer_id']; ?>','<?php echo $row['slot_id']; ?>','<?php echo $row['meetingClass']; ?>')">
                                                                    <i class="fa fa-pencil"></i>
                                                                </button>
                                                            <?php } ?>
                                                        </td>
                                                        <td><?php echo $row['city'] ?></td>
                                                        <td><?php echo date('l', strtotime($row['date'])) ?></td>
                                                        <td><?php echo date('d F Y', strtotime($row['date'])) ?></td>
                                                        <td><?php echo $row['from_time'] ?></td>
                                                        <td><?php echo $row['to_time'] ?></td>
                                                        <td><?php echo $row['start_date'] ?></td>
                                                        <td><?= $totalCompanies ?></td>
                                                    </tr>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div id="tab-completed" class="container-fluid tab-pane fade <?php echo isset($_GET['activeTab']) && $_GET['activeTab'] == 'completed' ? 'show active' : ''; ?>">
                                <div class="table-responsive">
                                    <table id="default-datatable4" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Action</th>
                                                <th>Slot Name</th>
                                                <th>Status</th>
                                                <th>Trainer Name</th>
                                                <th>City</th>
                                                <th>Meeting Day</th>
                                                <th>Meeting Date</th>
                                                <th>Meeting Start Time</th>
                                                <th>Meeting End Time</th>
                                                <th>Batch Start Date</th>
                                                <th>Total Company</th>
                                                <th>Recording Link</th>
                                                <th>Recording Link Password</th>
                                                <th>MOM Attachment</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $i = 1;
                                            foreach ($allMeetings as $row) {
                                                $companyMatch = true;

                                                if (!empty($companyFilter)) {
                                                    $companyIds = explode(',', $row['company_id']);
                                                    $companyIds = array_map('trim', $companyIds);
                                                    $companyMatch = in_array($companyFilter, $companyIds);
                                                }

                                                if ($row['meetingClass'] === 'completed' && $companyMatch) {
                                                    $societies = explode(',', $row['company_id']);
                                                    $societies = array_filter($societies);
                                                    $totalCompanies = count($societies);
                                            ?>
                                                    <tr>
                                                        <td><?php echo $i++; ?></td>
                                                        <td>
                                                            <?php if ($row['meeting_status'] == '1') { ?>
                                                                <form action="viewCompletedMeetings" method="GET" class="d-inline-block">
                                                                    <input type="hidden" name="trainer_id" value="<?php echo htmlspecialchars($row['trainer_id']); ?>">
                                                                    <input type="hidden" name="meeting_slot_id" value="<?php echo htmlspecialchars($row['slot_id']); ?>">
                                                                      <input type="hidden" name="company_filter" value="<?php echo isset($_GET['company_filter']) ? $_GET['company_filter'] : ''; ?>">
                                                                    <button class="btn btn-sm btn-primary"><i class="fa fa-eye"></i></button>
                                                                </form>
                                                                <?php if (!empty($row['mom_attachment'])): ?>
                                                                    <a href="../img/training_meetings/<?php echo htmlspecialchars($row['mom_attachment']); ?>" target="_blank" title="View MOM" class="btn btn-sm btn-info ml-1">
                                                                        <i class="fa fa-file-image-o"></i>
                                                                    </a>
                                                                <?php endif; ?>
                                                            <?php } else {
                                                            ?>
                                                                <form action="manageTrainingMeeting" method="GET">
                                                                    <input type="hidden" name="meeting_slot_id" value="<?php echo htmlspecialchars($row['slot_id']); ?>">
                                                                       <input type="hidden" name="company_filter" value="<?php echo isset($_GET['company_filter']) ? $_GET['company_filter'] : ''; ?>">
                                                                    <button type="submit" title="Start Meeting" class="btn btn-primary btn-sm" style="width: 115px;">Start Meeting</button>
                                                                </form>
                                                            <?php
                                                            } ?>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            // Format slot name: remove meeting number (M1, M2, etc.) and show count
                                                            $slotName = $row['slot_name'];
                                                            // Remove meeting number pattern like -M1, -M2, etc.
                                                            $slotName = preg_replace('/-M\d+(-?\d*)$/', '$1', $slotName);
                                                            // If there's a count suffix like -2, format it as " - 2"
                                                            if (preg_match('/^(.+)-(\d+)$/', $slotName, $matches)) {
                                                                $baseName = htmlspecialchars($matches[1]);
                                                                $countNum = htmlspecialchars($matches[2]);
                                                                echo $baseName . ' - ' . $countNum;
                                                            } else {
                                                                echo htmlspecialchars($slotName);
                                                            }
                                                            ?>
                                                        </td>
                                                        <td><?php if ($row['meeting_status'] != '1') {
                                                                echo "<span class='text-danger'>Pending</span>";
                                                            } else {
                                                                echo "<span class='text-success'>Completed</span>";
                                                            } ?></td>
                                                        <td><?php echo $row['trainer_name'];
                                                            if ($row['meeting_status'] == '0') { ?>
                                                                <button data-toggle="modal" data-target="#changeTrainerModel"
                                                                    title="Change Trainer?"
                                                                    class="btn text-warning btn-sm ml-2"
                                                                    onclick="changeTrainerid('<?php echo $row['trainer_id']; ?>','<?php echo $row['slot_id']; ?>','<?php echo $row['meetingClass']; ?>')">
                                                                    <i class="fa fa-pencil"></i>
                                                                </button>
                                                            <?php } ?>
                                                        <td><?php echo $row['city'] ?></td>
                                                        <td><?php echo date('l', strtotime($row['date'])) ?></td>
                                                        <td><?php echo date('d F Y', strtotime($row['date'])) ?></td>
                                                        <td><?php echo $row['from_time'] ?></td>
                                                        <td><?php echo $row['to_time'] ?></td>
                                                        <td><?php echo $row['start_date'] ?></td>
                                                        <td><?= $totalCompanies ?></td>
                                                        <td>
                                                            <?php if (!empty($row['recording_link'])): ?>
                                                                <a href="<?= htmlspecialchars($row['recording_link']) ?>" target="_blank"><?= htmlspecialchars($row['recording_link']) ?></a>
                                                                <button type="button" class="btn btn-sm btn-primary" data-toggle="tooltip"
                                                                    data-placement="top" title="Copy"
                                                                    onclick="copyToClipboard('<?= addslashes($row['recording_link']) ?>')">
                                                                    <i class="fa fa-copy"></i>
                                                                </button>
                                                            <?php else: ?>
                                                                <span class="text-muted">No Link Available</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?php if (!empty($row['training_password'])): ?>
                                                                <span><?= htmlspecialchars($row['training_password']) ?></span>
                                                                <button type="button" class="btn btn-sm btn-primary" data-toggle="tooltip"
                                                                    data-placement="top" title="Copy"
                                                                    onclick="copyToClipboard('<?= addslashes($row['training_password']) ?>')">
                                                                    <i class="fa fa-copy"></i>
                                                                </button>
                                                            <?php else: ?>
                                                                <span class="text-muted">No Password Available</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?php if (!empty($row['mom_attachment'])): ?>
                                                                <a href="../img/training_meetings/<?= htmlspecialchars($row['mom_attachment']) ?>" target="_blank" class="btn btn-sm btn-info" title="View MOM">
                                                                    <i class="fa fa-eye"></i> View MOM
                                                                </a>
                                                            <?php else: ?>
                                                                <span class="text-muted">No MOM Available</span>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                            <?php
                                                }
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
    </div>

    <!-- Modal -->
    <div class="modal fade" id="changeTrainerModel">
        <div class="modal-dialog modal-md">
            <div class="modal-content border-primary">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white">Change Trainer</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <form id="" method="POST" action="controller/trainingController.php">
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="trainer_id" class="col-form-label">Trainer</label>
                                    <select name="trainer_id" id="trainer_id" class="form-control single-select" required>
                                        <option value="">-- Select Trainer --</option>
                                        <?php
                                        $trainers = $d->select("bms_admin_master", "(role_id != 1) AND active_status='0'");
                                        while ($row2 = mysqli_fetch_assoc($trainers)) {
                                        ?>
                                            <option value="<?php echo htmlspecialchars($row2['admin_id']); ?>">
                                                <?php echo htmlspecialchars($row2['admin_name']); ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-footer text-center mt-3">
                                <input type="hidden" name="changeTrainers" value="changeTrainers">
                                <input type="hidden" name="slot_id" id="slot_id_edit">
                                <input type="hidden" name="activeTab" id="active_Tab">
                                <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-check-square-o"></i> Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Companies List Modal -->
    <div class="modal fade" id="companiesListModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white" id="companiesModalTitle">Companies List</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="companiesModalBody">
                    <!-- Content will be loaded via AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="assets/js/jquery.min.js"></script>
<script>
    function changeTrainerid(id, slotID, activeTab) {
        $("#trainer_id").val(id).trigger('change');
        $("#slot_id_edit").val(slotID);
        $("#active_Tab").val(activeTab);
    }


    function showSlotCompanies(slotId, slotName) {
        $('#companiesModalTitle').text('Companies for: ' + slotName);
        $('#companiesModalBody').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i> Loading...</div>');
        $('#companiesListModal').modal('show');

        $.ajax({
            url: 'ajaxGetSlotCompanies.php',
            type: 'POST',
            data: {
                slot_id: slotId
            },
            success: function(response) {
                $('#companiesModalBody').html(response);
            },
            error: function() {
                $('#companiesModalBody').html('<div class="alert alert-danger">Error loading companies.</div>');
            }
        });
    }
</script>
<script>

$(document).ready(function () {
    $('#company_filter').on('change', function () {
        let companyId = $(this).val();
        let url = 'manageTrainingSlots';
        if (companyId != '' && companyId != 'all') {
            url += '?company_filter=' + companyId;
        }
        window.location.href = url;
    });

});

</script>