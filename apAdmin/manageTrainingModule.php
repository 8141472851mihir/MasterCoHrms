<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// Persist manageTrainingModule filters so redirects keep context
$defaultFilters = [
    'session_name' => '',
    'module_priority' => '',
    'training_module_type' => ''
];
$filters = [];
foreach ($defaultFilters as $key => $default) {
    $value = $default;
    if (isset($_GET[$key]) && $_GET[$key] !== '' && $_GET[$key] !== 'all') {
        $value = $_GET[$key];
    } elseif (isset($_SESSION['manageTrainingModule_filters'][$key])) {
        $value = $_SESSION['manageTrainingModule_filters'][$key];
    }
    $filters[$key] = $value;
}
$_SESSION['manageTrainingModule_filters'] = $filters;

$showTopic = (isset($_GET["training_module_type"]) && ($_GET["training_module_type"] === "1" || $_GET["training_module_type"] === "2"));
?>
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row pt-2 pb-2">
            <div class="col-sm-3">
                <h4 class="page-title">Manage Modules</h4>
            </div>
            <div class="col-sm-6">
                <form action="" method="get" accept-charset="utf-8">
                    <div class="row">
                        <div class="col-sm-4">
                            <select type="text" required="" id="session_name" onchange="this.form.submit()"
                                class="form-control single-select" name="session_name">
                                <option value="all">-- All --</option>
                                <?php
                                $qt = $d->select("session_master", "session_status='0'");
                                while ($Data = mysqli_fetch_array($qt)) {
                                    $selected = (isset($_GET['session_name']) && $_GET['session_name'] == $Data['session_id']) ? 'selected' : '';
                                    echo "<option value=\"{$Data['session_id']}\" $selected>";
                                    echo $Data['session_name'] . '(' . $Data['session_days'] . ' - ' . date("h:i A", strtotime($Data['start_time'])) . ' - ' . date("h:i A", strtotime($Data['end_time'])) . ')';
                                    echo "</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-sm-4">
                            <select type="text" required="" id="module_priority" onchange="this.form.submit()"
                                class="form-control single-select" name="module_priority">
                                <option value="all">-- All --</option>
                                <?php
                                $qt = $d->select("training_module_priority_master");
                                while ($Data = mysqli_fetch_array($qt)) {
                                    $selected = (isset($_GET['module_priority']) && $_GET['module_priority'] == $Data['priority_id']) ? 'selected' : '';
                                    echo "<option value=\"{$Data['priority_id']}\" $selected>";
                                    echo $Data['priority_name'];
                                    echo "</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-sm-4">
                            <select type="text" required="" id="training_module_type" onchange="this.form.submit()"
                                class="form-control single-select" name="training_module_type">
                                <option value="all">-- All --</option>
                                <option value="0" <?php echo (isset($_GET['training_module_type']) && $_GET['training_module_type'] == "0") ? 'selected' : ''; ?>>
                                    Setup (HRMS)
                                </option>
                                <option value="1" <?php echo (isset($_GET['training_module_type']) && $_GET['training_module_type'] == "1") ? 'selected' : ''; ?>>
                                    Training (HRMS)
                                </option>
                                <option value="2" <?php echo (isset($_GET['training_module_type']) && $_GET['training_module_type'] == "2") ? 'selected' : ''; ?>>
                                    CRM
                                </option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-sm-3 col-7">
                <div class="btn-group float-sm-right">
                    <a href="javaScript:void();" data-toggle="modal" onclick="addForm()" data-target="#addModule"
                        class="btn btn-primary btn-sm waves-effect waves-light" title="Add Module">
                        <i class="fa fa-plus mr-1"></i> Add
                    </a>
                    <?php
                    // Build URL with current filters for trainingModuleOrder
                    $orderUrl = 'trainingModuleOrder';
                    $orderParams = [];
                    if (!empty($filters['training_module_type'])) {
                        // Map training_module_type to product_type for trainingModuleOrder
                        if ($filters['training_module_type'] == '2') {
                            $orderParams['product_type'] = 'crm';
                        } else {
                            $orderParams['product_type'] = 'hrms';
                        }
                    }
                    if (!empty($orderParams)) {
                        $orderUrl .= '?' . http_build_query($orderParams);
                    }
                    ?>
                    <a href="<?php echo htmlspecialchars($orderUrl); ?>" class="btn btn-info btn-sm ml-2" title="Manage Training Module Order">
                        <i class="fa fa-sort mr-1"></i> Order
                    </a>

                </div>
            </div>
        </div>

        <?php
        $where = "";
        if (isset($_GET["session_name"]) && $_GET["session_name"] != "" && $_GET["session_name"] != "all") {
            $session_name = $_GET["session_name"];
            $where .= "  AND FIND_IN_SET('$session_name', training_module_master.session_id) > 0";
        }
        if (isset($_GET["training_module_type"]) && $_GET["training_module_type"] != "" && $_GET["training_module_type"] != "all") {
            $module_type = $_GET["training_module_type"];
            $where .= " AND training_module_master.module_type='$module_type'";
        }
        if (isset($_GET["module_priority"]) && $_GET["module_priority"] != "" && $_GET["module_priority"] != "all") {
            $module_priority = $_GET["module_priority"];
            $where .= " AND training_module_master.module_priority='$module_priority'";
        }
        ?>


        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="example" class="table table-bordered">
                                <thead>
                                    <tr>

                                        <th>Sr.No</th>
                                        <th>Module Name</th>
                                        <th>Session / Topic Name</th>
                                        <th>Module Type</th>
                                        <th>Module Priority</th>
                                        <th>Order</th>
                                        <?php if ($showTopic) { ?>
                                            <th>Topic</th>
                                        <?php } ?>
                                        <th>Completion Days</th>
                                        <th>Estimated Minutes</th>
                                        <th>Sub-Topics</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                        <th>URL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    $orderAndGroup = $showTopic
                                        ? "GROUP BY training_module_master.training_module_id, training_module_master.module_priority, training_module_master.topic_id 
                                           ORDER BY (training_module_topics.topic_name IS NULL), training_module_topics.topic_name ASC, training_module_priority_master.priority_id ASC, training_module_master.training_module_name ASC"
                                        : "GROUP BY training_module_master.training_module_id, training_module_master.module_priority 
                                           ORDER BY training_module_priority_master.priority_id ASC, training_module_master.training_module_id DESC";

                                    $q = $d->selectRow(
                                        "training_module_master.*, 
                                        GROUP_CONCAT(session_master.session_name ORDER BY session_master.session_id ASC) AS session_names,
                                        training_module_priority_master.priority_name,
                                        training_module_topics.topic_name,
                                        (SELECT COUNT(*) FROM training_module_subtopics WHERE training_module_id = training_module_master.training_module_id) as subtopic_count",
                                        "training_module_master 
                                        LEFT JOIN session_master ON FIND_IN_SET(session_master.session_id, training_module_master.session_id) > 0
                                        LEFT JOIN training_module_priority_master ON training_module_master.module_priority = training_module_priority_master.priority_id
                                        LEFT JOIN training_module_topics ON training_module_topics.topic_id = training_module_master.topic_id",
                                        "1 $where",
                                        $orderAndGroup
                                    );

                                    $currentTopic = null;
                                    while ($data = mysqli_fetch_array($q)) {
                                    ?>
                                        <tr>
                                            <td><?php echo $i++; ?></td>
                                            <td><?php echo $data['training_module_name']; ?></td>
                                            <td>
                                                <?php
                                                if ($data['module_type'] == 1 || $data['module_type'] == 2) {
                                                    // For training modules (HRMS) or CRM modules, show topic name
                                                    echo !empty($data['topic_name']) ? htmlspecialchars($data['topic_name']) : '<span class="text-muted">No Topic</span>';
                                                } else {
                                                    // For setup modules, show session names
                                                    if (!empty($data['session_names'])) {
                                                        $names = array_map('trim', explode(',', $data['session_names']));
                                                        echo implode('<br>', array_map('htmlspecialchars', $names));
                                                    } else {
                                                        echo '';
                                                    }
                                                }
                                                ?>
                                            </td>

                                            <td>
                                                <?php
                                                if ($data['module_type'] == 0) {
                                                    echo "Setup (HRMS)";
                                                } elseif ($data['module_type'] == 1) {
                                                    echo "Training (HRMS)";
                                                } else {
                                                    echo "CRM";
                                                }
                                                ?>
                                            </td>
                                            <td><?php echo $data['priority_name']; ?></td>
                                            <td>
                                                <?php
                                                if ($data['module_type'] == 1 || $data['module_type'] == 2) {
                                                    $orderVal = isset($data['training_module_order']) && $data['training_module_order'] !== ''
                                                        ? intval($data['training_module_order'])
                                                        : (isset($data['module_priority']) ? intval($data['module_priority']) : '');
                                                    echo $orderVal;
                                                } else {
                                                    echo '';
                                                }
                                                ?>
                                            </td>
                                            <?php if ($showTopic) { ?>
                                                <td><?php echo !empty($data['topic_name']) ? htmlspecialchars($data['topic_name']) : '<span class="text-muted">Ungrouped</span>'; ?></td>
                                            <?php } ?>

                                            <td>
                                                <?php
                                                if ($data['module_type'] == 1 || $data['module_type'] == 2) {
                                                    echo isset($data['completion_days']) && $data['completion_days'] !== '' ? intval($data['completion_days']) : '';
                                                } else {
                                                    echo '';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo isset($data['estimated_minutes']) && $data['estimated_minutes'] !== '' ? intval($data['estimated_minutes']) . ' min' : '';
                                                ?>
                                            </td>

                                            <td>
                                                <?php if ($data['module_type'] == 1 || $data['module_type'] == 2): ?>
                                                    <a href="javascript:void(0);"
                                                        class="btn btn-sm btn-outline-info btn-manage-subtopics d-inline-flex align-items-center"
                                                        title="Manage Sub-Topics"
                                                        data-module-id="<?php echo htmlspecialchars((string)$data['training_module_id']); ?>"
                                                        data-module-name="<?php echo htmlspecialchars($data['training_module_name'], ENT_QUOTES); ?>">
                                                        <i class="fa fa-cog mr-1"></i>
                                                        Sub-Topics
                                                        <span class="badge badge-light ml-2"><?php echo (int)$data['subtopic_count']; ?></span>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted"></span>
                                                <?php endif; ?>
                                            </td>

                                            <td>
                                                <?php
                                                $buttonClass = ($data['training_module_status'] == "0") ? 'btn-success-new' : 'btn-danger';
                                                $buttonCondition = ($data['training_module_status'] == "0") ? 'Active' : 'Deactive';
                                                $status = ($data['training_module_status'] == "0") ? 'moduleStatusDeactive' : 'moduleStatusActive';
                                                $newStatus = ($data['training_module_status'] == "0") ? 'moduleStatusActive' : 'moduleStatusDeactive';
                                                $newStatusVal = ($data['training_module_status'] == "0") ? '1' : '0';
                                                $statusValue = ($data['training_module_status'] == "0") ? '0' : '1';
                                                ?>

                                                <input type="button"
                                                    class="btn btn-sm pl-1 pr-1 <?php echo $buttonClass ?>"
                                                    id="<?php echo 'training_module_' . $data['training_module_id']; ?>"
                                                    onclick="changeStatusNew('<?php echo $data['training_module_id']; ?>','<?php echo $status; ?>','<?php echo $newStatus; ?>','<?php echo $statusValue; ?>','<?php echo $newStatusVal; ?>','<?php echo 'training_module_' . $data['training_module_id']; ?>');"
                                                    data-size="small" value="<?php echo $buttonCondition ?>" />
                                            </td>

                                            <td>
                                                <a href="javascript:void(0);"
                                                    class="btn btn-sm btn-primary btn-edit-module"
                                                    title="Edit Module"
                                                    data-id="<?php echo htmlspecialchars((string)$data['training_module_id']); ?>"
                                                    data-name="<?php echo htmlspecialchars($data['training_module_name']); ?>"
                                                    data-session="<?php echo htmlspecialchars((string)$data['session_id']); ?>"
                                                    data-type="<?php echo htmlspecialchars((string)$data['module_type']); ?>"
                                                    data-priority="<?php echo htmlspecialchars((string)$data['module_priority']); ?>"
                                                    data-session-day="<?php echo htmlspecialchars((string)$data['session_day_id']); ?>"
                                                    data-completion-days="<?php echo htmlspecialchars(isset($data['completion_days']) ? (string)$data['completion_days'] : ''); ?>"
                                                    data-url="<?php echo htmlspecialchars(isset($data['module_url']) ? (string)$data['module_url'] : ''); ?>"
                                                    data-estimated-minutes="<?php echo htmlspecialchars(isset($data['estimated_minutes']) ? (string)$data['estimated_minutes'] : ''); ?>"
                                                    data-topic-id="<?php echo htmlspecialchars(isset($data['topic_id']) ? (string)$data['topic_id'] : ''); ?>">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                            </td>
                                            <td>
                                                <?php
                                                $moduleUrl = isset($data['module_url']) ? trim($data['module_url']) : '';
                                                if ($moduleUrl !== '') {
                                                    $displayUrl = htmlspecialchars(mb_strimwidth($moduleUrl, 0, 35, '...'));
                                                    echo '';
                                                    echo '<button type="button" class="btn btn-sm btn-primary btn-copy-url" data-url="' . htmlspecialchars($moduleUrl) . '">Copy</button>';
                                                } else {
                                                    echo '';
                                                }
                                                ?>
                                            </td>

                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="addModule">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-primary">
                <div class="modal-header bg-primary">
                    <h4 class="modal-title text-white">
                        Add Module
                    </h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="scheduleModuleValidation" action="controller/trainingController.php" method="post">
                        <?php
                        // Add hidden inputs to preserve filters on form submission
                        foreach ($filters as $key => $value) {
                            if ($value != '' && $value != 'all') {
                                echo '<input type="hidden" name="' . htmlspecialchars($key, ENT_QUOTES) . '" value="' . htmlspecialchars($value, ENT_QUOTES) . '">';
                            }
                        }
                        ?>
                        <div class="form-group row">

                            <div class="col-md-6">
                                <label for="module_type" class="col-form-label">Module Type <span
                                        class="required">*</span></label>
                                <select class="form-control single-select" name="module_type" id="module_type" required onchange="toggleSessionField()">
                                    <option value="">-- Select --</option>
                                    <option value="0" <?php echo ($module_type == "0") ? 'selected' : ''; ?>>Setup (HRMS)
                                    </option>
                                    <option value="1" <?php echo ($module_type == "1") ? 'selected' : ''; ?>>Training (HRMS)
                                    </option>
                                    <option value="2" <?php echo ($module_type == "2") ? 'selected' : ''; ?>>CRM
                                    </option>
                                </select>
                            </div>

                            <input type="hidden" name="start_time" id="start_time">
                            <input type="hidden" name="end_time" id="end_time">

                            <div class="col-md-6">
                                <label for="module_name" class="col-form-label">Module Name <span
                                        class="required">*</span></label>
                                <input type="text" autocomplete="off" class="form-control" name="training_module_name"
                                    id="module_name" value="<?= htmlspecialchars($training_module_name); ?>" required>
                            </div>

                            <div class="col-md-6" id="topicField" style="display: none;">
                                <label for="topic_id" class="col-form-label">Topic <span class="required">*</span></label>
                                <select class="form-control single-select" name="topic_id" id="topic_id">
                                    <option value="">-- Select Topic --</option>
                                    <!-- Topics will be loaded dynamically via JavaScript based on module_type -->
                                </select>
                            </div>

                        </div>
                        <div class="form-group row">
                            <div class="col-md-6">
                                <label for="priority_id" class="col-form-label">Priority Name <span
                                        class="required">*</span></label>
                                <select class="form-control single-select" name="priority_id" id="priority_id" required>
                                    <option value="">-- Select --</option>
                                    <?php
                                    $qt = $d->select("training_module_priority_master");
                                    while ($Data = mysqli_fetch_array($qt)) {
                                        $selected = ($Data['priority_id'] == $module_priority) ? 'selected' : '';
                                    ?>
                                        <option value="<?php echo $Data['priority_id']; ?>" <?php echo $selected; ?>>
                                            <?php echo htmlspecialchars($Data['priority_name']); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>


                            <div class="col-md-6" id="completionDaysField">
                                <label for="completion_days" class="col-form-label">Completion Days <span class="required">*</span>
                                    <span class="ml-1" title="Number of days within which this module should be completed."><i class="fa fa-info-circle"></i></span>
                                </label>
                                <input type="text" min="0" maxlength="3" class="form-control onlyNumber" name="completion_days" id="completion_days" value="">
                            </div>

                            <div class="col-md-6">
                                <label for="module_url" class="col-form-label">Module URL</label>
                                <input type="text" autocomplete="off" maxlength="1000" minlength="3" class="form-control" name="module_url" id="module_url" value="" placeholder="https://...">
                            </div>

                            <div class="col-md-6" id="estimatedMinutesField">
                                <label for="estimated_minutes" class="col-form-label">Estimated Minutes <span class="required">*</span> <span class="ml-1" title="Estimated Minutes within which this module should atleast take during training."><i class="fa fa-info-circle"></i></span></label>
                                <input type="text" min="0" maxlength="4" class="form-control onlyNumber" name="estimated_minutes" id="estimated_minutes" value="" required>
                            </div>

                            <div class="col-md-6" id="session_day">
                                <label for="session_day_id" class="col-form-label">Session Day <span class="required">*</span></label>
                                <select class="form-control single-select" name="session_day_id" id="session_day_id" required onchange="filterSessions()">
                                    <option value="">-- Select --</option>
                                    <?php
                                    $qt = $d->select("session_day_master", "session_day_status='0'");
                                    while ($Data = mysqli_fetch_array($qt)) {
                                        $selected = ($Data['session_day_id'] == $session_day_id) ? 'selected' : '';
                                    ?>
                                        <option value="<?php echo $Data['session_day_id']; ?>" <?php echo $selected; ?>>
                                            <?php echo htmlspecialchars($Data['session_day_name']); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="col-md-6" id="sessionField">
                                <label for="session_id" class="col-form-label">Session Name<span class="required">*</span></label>
                                <select class="form-control multiple-select" multiple="multiple" name="session_id[]" id="session_id" required onchange="setSessionTimes()">
                                </select>
                            </div>



                        </div>
                        <div class="form-footer text-center">
                            <input type="hidden" name="scheduleModule" id="scheduleModule" value="scheduleModule">
                            <input type="hidden" name="editId" id="editId" value="" />
                            <button type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i><span
                                    id="submitButton">ADD</span></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="manageSubtopicsModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-primary">
                <div class="modal-header bg-primary">
                    <h4 class="modal-title text-white">
                        Manage Sub-Topics
                    </h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <h5 id="moduleNameDisplay"></h5>
                        </div>
                        <div class="col-md-4 text-right">
                            <button class="btn btn-sm btn-success" onclick="addNewSubtopic()">
                                <i class="fa fa-plus"></i> Add Sub-Topic
                            </button>
                        </div>
                    </div>

                    <div id="subtopicsList" class="mb-3">
                    </div>
                    <div id="subtopicFormContainer" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 id="subtopicFormTitle" class="mb-0">Add New Sub-Topic</h6>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="closeSubtopicForm()" title="Close Form">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                        <form id="subtopicForm">
                            <div class="form-group">
                                <label for="subtopic_name">Sub-Topic Name <span class="required">*</span></label>
                                <input type="text" class="form-control" id="subtopic_name" name="subtopic_name" required>
                            </div>
                            <div class="form-group">
                                <label for="subtopic_description">Description</label>
                                <textarea class="form-control" id="subtopic_description" name="subtopic_description" rows="3"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="subtopic_minutes">Estimated Minutes <span class="required">*</span></label>
                                <input type="text" class="form-control onlyNumber" minlength="1" maxlength="4" id="subtopic_minutes" name="subtopic_minutes" min="1" value="" required>
                            </div>
                            <div class="form-group">
                                <label for="subtopic_order">Display Order</label>
                                <input type="text" class="form-control onlyNumber" minlength="1" maxlength="3" id="subtopic_order" name="subtopic_order" min="1" value="1">
                            </div>
                            <div class="form-group">
                                <label for="subtopic_status">Status</label>
                                <select class="form-control" id="subtopic_status" name="subtopic_status">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                            <input type="hidden" id="subtopic_id" name="subtopic_id" value="">
                            <input type="hidden" id="module_id" name="module_id" value="">

                            <div class="text-center">
                                <button type="button" class="btn btn-primary" id="saveSubtopicBtn" onclick="saveSubtopic()">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function addForm() {
        $("#scheduleModule").val("scheduleModule");
        $("#module_name").val("");
        $("#session_id").val(null).trigger("change");
        $("#module_type").val(null).trigger("change");
        $("#priority_id").val(null).trigger("change");
        $("#session_day_id").val(null).trigger("change");
        $("#module_url").val("");
        $("#estimated_minutes").val("");
        $("#editId").val("");
        $("#topic_id").val(null).trigger("change");
        $("#submitButton").text("ADD");
        $(".modal-title").text("Add Module");
    }

    function setSessionTimes() {
        const sessionSelect = document.getElementById('session_id');
        const selectedOption = sessionSelect.options[sessionSelect.selectedIndex];

        if (selectedOption) {
            const startTime = selectedOption.getAttribute('data-start-time');
            const endTime = selectedOption.getAttribute('data-end-time');

            document.getElementById('start_time').value = startTime || '';
            document.getElementById('end_time').value = endTime || '';
        }
    }

    function manageSubtopics(moduleId, moduleName) {
        $('#module_id').val(moduleId);
        $('#moduleNameDisplay').text('Module: ' + moduleName);
        $('#subtopicModalTitle').text('Manage Sub-Topics: ' + moduleName);
        loadSubtopics(moduleId);
        $('#manageSubtopicsModal').modal('show');
    }

    function loadSubtopics(moduleId) {
        $.ajax({
            url: 'controller/trainingController.php',
            type: 'POST',
            data: {
                action: 'getSubtopics',
                module_id: moduleId,
                csrf: csrf
            },
            success: function(response) {
                $('#subtopicsList').html(response);
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error loading sub-topics. Please try again.',
                    confirmButtonColor: '#3085d6'
                });
                $('#subtopicsList').html('<div class="alert alert-danger">Error loading sub-topics</div>');
            }
        });
    }

    function addNewSubtopic() {
        $('#subtopicFormContainer').show();
        $('#subtopicFormTitle').text('Add New Sub-Topic');
        $('#subtopicForm')[0].reset();
        $('#subtopic_id').val('');
        $('#module_id').val($('#module_id').val());
        $('#subtopic_order').val($('#subtopicsList .list-group-item').length + 1);
    }

    function closeSubtopicForm() {
        $('#subtopicFormContainer').hide();
        $('#subtopicForm')[0].reset();
    }

    function editSubtopic(subtopicId) {
        $.ajax({
            url: 'controller/trainingController.php',
            type: 'POST',
            data: {
                action: 'getSubtopic',
                subtopic_id: subtopicId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#subtopicFormContainer').show();
                    $('#subtopicFormTitle').text('Edit Sub-Topic');
                    $('#subtopic_id').val(response.data.subtopic_id);
                    $('#subtopic_name').val(response.data.subtopic_name);
                    $('#subtopic_description').val(response.data.subtopic_description);
                    $('#subtopic_minutes').val(response.data.estimated_minutes);
                    $('#subtopic_order').val(response.data.display_order);
                    $('#subtopic_status').val(response.data.subtopic_status);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error loading sub-topic: ' + response.message,
                        confirmButtonColor: '#3085d6'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error loading sub-topic. Please try again.',
                    confirmButtonColor: '#3085d6'
                });
            }
        });
    }

    function saveSubtopic() {
        var $btn = $('#saveSubtopicBtn');
        if ($btn.prop('disabled')) { return; }
        // Validate form
        if (!$('#subtopic_name').val().trim()) {
            Swal.fire({
                icon: 'warning',
                text: 'Please enter a sub-topic name',
                confirmButtonColor: '#3085d6'
            });
            return;
        }

        const formData = $('#subtopicForm').serialize();

        $btn.prop('disabled', true).text('Saving...');
        $.ajax({
            url: 'controller/trainingController.php',
            type: 'POST',
            data: formData + '&action=saveSubtopic',
            success: function(response) {
                try {
                    const result = JSON.parse(response);
                    if (result.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: result.message || 'Sub-topic saved successfully',
                            confirmButtonColor: '#3085d6'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                loadSubtopics($('#module_id').val());
                                $('#subtopicFormContainer').hide();
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error saving sub-topic: ' + result.message,
                            confirmButtonColor: '#3085d6'
                        });
                    }
                } catch (e) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Invalid response from server',
                        confirmButtonColor: '#3085d6'
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error saving sub-topic: ' + error,
                    confirmButtonColor: '#3085d6'
                });
            },
            complete: function() {
                $btn.prop('disabled', false).text('Save');
            }
        });
    }

    function deleteSubtopic(subtopicId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'controller/trainingController.php',
                    type: 'POST',
                    data: {
                        action: 'deleteSubtopic',
                        subtopic_id: subtopicId
                    },
                    success: function(response) {
                        try {
                            const result = JSON.parse(response);
                            if (result.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: result.message || 'Sub-topic has been deleted.',
                                    confirmButtonColor: '#3085d6'
                                }).then(() => {
                                    loadSubtopics($('#module_id').val());
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Error deleting sub-topic: ' + result.message,
                                    confirmButtonColor: '#3085d6'
                                });
                            }
                        } catch (e) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Invalid response from server',
                                confirmButtonColor: '#3085d6'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error deleting sub-topic: ' + error,
                            confirmButtonColor: '#3085d6'
                        });
                    }
                });
            }
        });
    }

    function addNewSubtopic() {
        $('#subtopicFormContainer').show();
        $('#subtopicFormTitle').text('Add New Sub-Topic');
        $('#subtopicForm')[0].reset();
        $('#subtopic_id').val('');
        $('#module_id').val($('#module_id').val());
        $('#subtopic_order').val($('#subtopicsList .list-group-item').length + 1);
        addCancelButton();
    }

    function copyModuleUrl(url) {
        if (!url) {
            return;
        }
        const temp = document.createElement('input');
        temp.type = 'text';
        temp.value = url;
        document.body.appendChild(temp);
        temp.select();
        temp.setSelectionRange(0, 99999);
        try {
            document.execCommand('copy');
        } catch (e) {}
        document.body.removeChild(temp);
        if (typeof Lobibox !== 'undefined' && Lobibox.notify) {
            Lobibox.notify('success', {
                pauseDelayOnHover: true,
                size: 'mini',
                position: 'top right',
                msg: 'URL copied to clipboard'
            });
        } else if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'Copied',
                text: 'URL copied to clipboard',
                timer: 1200,
                showConfirmButton: false
            });
        }
    }
</script>