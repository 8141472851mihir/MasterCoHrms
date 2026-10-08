<?php
// Get filters from session or current GET parameters
$backFilters = [];
if (isset($_SESSION['manageTrainingModule_filters'])) {
    $backFilters = $_SESSION['manageTrainingModule_filters'];
}
// Map product_type back to training_module_type if needed
if (isset($_GET['product_type'])) {
    if ($_GET['product_type'] === 'crm') {
        $backFilters['training_module_type'] = '2';
    } else {
        $backFilters['training_module_type'] = '1';
    }
}
// Build back URL with filters
$backUrl = 'manageTrainingModule';
$backParams = [];
foreach ($backFilters as $key => $value) {
    if ($value != '' && $value != 'all') {
        $backParams[$key] = $value;
    }
}
if (!empty($backParams)) {
    $backUrl .= '?' . http_build_query($backParams);
}
?>
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row pt-2 pb-2">
            <div class="col-sm-6 col-5">
                <h4 class="page-title">Training Module Order Management</h4>
            </div>
            <div class="col-sm-3">
                <form action="" method="get" accept-charset="utf-8">
                    <select id="product_type_filter" name="product_type" class="form-control single-select" onchange="this.form.submit()">
                        <option value="hrms" <?php echo (!isset($_GET['product_type']) || $_GET['product_type'] === 'hrms') ? 'selected' : ''; ?>>HRMS</option>
                        <option value="crm" <?php echo (isset($_GET['product_type']) && $_GET['product_type'] === 'crm') ? 'selected' : ''; ?>>CRM</option>
                    </select>
                </form>
            </div>
            <div class="col-sm-3 col-7">
                <div class="btn-group float-sm-right">
                    <a href="<?php echo htmlspecialchars($backUrl); ?>" class="btn btn-primary btn-sm">
                        <i class="fa fa-arrow-left mr-1"></i> Back
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="mb-3 text-center">
                            <button type="button" id="collapseAllGroups" class="btn btn-primary mr-2">
                                <i class="fa fa-compress mr-2"></i>Collapse All Groups
                            </button>
                            <button type="button" id="expandAllGroups" class="btn btn-primary mr-2">
                                <i class="fa fa-expand mr-2"></i>Expand All Groups
                            </button>
                            <button type="button" class="btn btn-success" onclick="saveAllOrder()">
                                <i class="fa fa-save mr-2"></i>Save Current Order
                            </button>
                            <button type="button" class="btn btn-secondary ml-2" onclick="openMergeTopicModal()">
                                <i class="fa fa-object-group mr-2"></i>Merge to Topic
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="4%" class="text-center"><input type="checkbox" id="selectAllModules" onclick="toggleSelectAll(this)"></th>
                                        <th width="6%">Order</th>
                                        <th width="40%">Module Name</th>
                                        <th width="15%">Priority</th>
                                        <th width="15%">Completion Days</th>
                                        <th width="20%">Topic</th>
                                    </tr>
                                </thead>
                                <?php
                                // Get product type filter: default to HRMS
                                $productType = isset($_GET['product_type']) ? $_GET['product_type'] : 'hrms';
                                // Filter by module_type: 1 = Training HRMS, 2 = CRM
                                $moduleTypeFilter = ($productType === 'crm') ? "tmm.module_type = 2" : "tmm.module_type = 1";
                                // Topic type: 0 = HRMS, 1 = CRM
                                $topicTypeFilter = ($productType === 'crm') ? "1" : "0";
                                
                                // Get all training modules grouped by topic and ordered within groups
                                $modulesQuery = $d->selectRow(
                                    "tmm.training_module_id, tmm.training_module_name, tmm.training_module_order,
                                     tmm.completion_days, tmm.module_priority, tmm.topic_id,
                                     tmpm.priority_name, tmpm.priority_id, tmt.topic_name, 
                                     tmt.completion_days as topic_completion_days, tmt.next_start_days, 
                                     tpt.participant_name",
                                    "training_module_master tmm
                                     LEFT JOIN training_module_priority_master tmpm ON tmm.module_priority = tmpm.priority_id
                                     LEFT JOIN training_module_topics tmt ON tmt.topic_id = tmm.topic_id AND tmt.topic_type = '$topicTypeFilter'
                                     LEFT JOIN training_participants_type tpt ON tpt.participants_type_id = tmt.participant_type",
                                    "$moduleTypeFilter AND tmm.training_module_status = 0",
                                    "ORDER BY COALESCE(tmm.training_module_order, tmm.module_priority) ASC, tmm.training_module_id ASC"
                                );

                                $orderNumber = 1;
                                $currentTopicKey = null;
                                $openedGroup = false;
                                while ($module = mysqli_fetch_assoc($modulesQuery)) {
                                    $moduleId = $module['training_module_id'];
                                    $moduleName = htmlspecialchars($module['training_module_name']);
                                    $moduleOrder = isset($module['training_module_order']) && $module['training_module_order'] !== null ? intval($module['training_module_order']) : intval($module['module_priority']);
                                    $completionDays = isset($module['completion_days']) && $module['completion_days'] !== null ? $module['completion_days'] : 'N/A';
                                    $priorityName = isset($module['priority_name']) ? $module['priority_name'] : 'N/A';
                                    $topicId = isset($module['topic_id']) ? intval($module['topic_id']) : 0;
                                    $topicNameRaw = isset($module['topic_name']) && $module['topic_name'] !== '' ? $module['topic_name'] : null;
                                    $topicLabel = $topicNameRaw ? htmlspecialchars($topicNameRaw) : 'Ungrouped';
                                    
                                    // Get topic details
                                    $topicCompletionDays = isset($module['topic_completion_days']) && $module['topic_completion_days'] !== null ? $module['topic_completion_days'] : 'N/A';
                                    $topicNextStartDays = isset($module['next_start_days']) && $module['next_start_days'] !== null ? $module['next_start_days'] : 'N/A';
                                    $participantName = isset($module['participant_name']) && $module['participant_name'] !== '' ? htmlspecialchars($module['participant_name']) : 'N/A';

                                    $topicKey = $topicId . '|' . ($topicNameRaw ?: 'Ungrouped');
                                    if ($currentTopicKey !== $topicKey) {
                                        if ($openedGroup) {
                                            echo "</tbody>"; // close previous group
                                        }
                                        $currentTopicKey = $topicKey;
                                        $openedGroup = true;
                                        echo '<tbody class="module-group" data-topic-id="' . $topicId . '">';
                                        
                                        // Create topic details string
                                        $topicDetails = '';
                                        if ($topicNameRaw) {
                                            $topicDetails = '<small class="text-muted ml-3">';
                                            $topicDetails .= '<span class="badge badge-info mr-2">Completion: ' . $topicCompletionDays . ' days</span>';
                                            $topicDetails .= '<span class="badge badge-warning mr-2">Next Start: ' . $topicNextStartDays . ' days</span>';
                                            // Only show participant for HRMS topics (CRM topics don't have participants)
                                            if ($productType === 'hrms' && $participantName !== 'N/A') {
                                                $topicDetails .= '<span class="badge badge-secondary">Participant: ' . $participantName . '</span>';
                                            }
                                            $topicDetails .= '</small>';
                                        }
                                        
                                        echo '<tr class="table-active group-header" style="cursor: move;">'
                                            . '<td class="text-center align-middle"><input type="checkbox" class="groupCheckbox"></td>'
                                            . '<td colspan="5"><strong>Topic: ' . $topicLabel . '</strong>' . $topicDetails . '</td>'
                                            . '</tr>';
                                    }
                                    ?>
                                    <tr class="module-row" data-module-id="<?php echo $moduleId; ?>" data-order="<?php echo $moduleOrder; ?>">
                                        <td class="text-center align-middle"><input type="checkbox" class="moduleCheckbox" value="<?php echo $moduleId; ?>"></td>
                                        <td class="text-center">
                                            <span class="badge badge-primary order-badge"><?php echo $orderNumber++; ?></span>
                                        </td>
                                        <td>
                                            <strong><?php echo $moduleName; ?></strong>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-secondary"><?php echo $priorityName; ?></span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-info"><?php echo $completionDays; ?> days</span>
                                        </td>
                                        <td><?php echo $topicLabel; ?></td>
                                    </tr>
                                    <?php
                                }
                                if ($openedGroup) {
                                    echo "</tbody>";
                                }
                                ?>
                            </table>
                        </div>

                        <!-- Topics Management -->
                        <div class="card mt-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Topics</h5>
                                <button type="button" class="btn btn-sm btn-primary" onclick="openTopicModal()">
                                    <i class="fa fa-plus mr-1"></i> Add Topic
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="topicsTable">
                                        <thead class="thead-light">
                                            <tr>
                                                <th width="30%">Topic Name</th>
                                                <th width="10%" class="text-center">Type</th>
                                                <th width="12%" class="text-center">Completion Days</th>
                                                <th width="12%" class="text-center">Next Start Days</th>
                                                <th width="13%" class="text-center">Participant Type</th>
                                                <th width="23%" class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="topicsTbody">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Add/Edit Topic Modal -->
                        <div class="modal fade" id="topicModal">
                            <div class="modal-dialog">
                                <div class="modal-content border-primary">
                                    <div class="modal-header bg-primary">
                                        <h4 class="modal-title text-white" id="topicModalTitle">Add Topic</h4>
                                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form id="topicForm">
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label for="topic_name">Topic Name <span class="required">*</span></label>
                                                <input type="text" class="form-control" id="topic_name" name="topic_name" required>
                                            </div>
                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label for="topic_completion_days">Completion Days <span class="required">*</span></label>
                                                    <input type="text" class="form-control onlyNumber" id="topic_completion_days" name="completion_days" maxlength="3" required>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="topic_next_start_days">Next Start Days <span class="required">*</span></label>
                                                    <input type="text" class="form-control onlyNumber" id="topic_next_start_days" name="next_start_days" maxlength="3" required>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="topic_type">Topic Type <span class="required">*</span></label>
                                                <select class="form-control single-select" id="topic_type" name="topic_type" required onchange="toggleParticipantField()">
                                                    <option value="0">HRMS</option>
                                                    <option value="1">CRM</option>
                                                </select>
                                            </div>
                                            <div class="form-group" id="participant_type_group">
                                                <label for="participant_type">Participant Type <span class="required">*</span></label>
                                                <select class="form-control single-select" id="participant_type" name="participant_type">
                                                    <option value="">-- Select --</option>
                                                </select>
                                            </div>
                                            <input type="hidden" id="topic_id" name="topic_id" value="">
                                        </div>
                                        <div class="modal-footer text-center">
                                            <button type="submit" class="btn btn-success">Save</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Merge Modal -->
                        <div class="modal fade" id="mergeTopicModal">
                            <div class="modal-dialog">
                                <div class="modal-content border-primary">
                                    <div class="modal-header bg-primary">
                                        <h4 class="modal-title text-white">Merge Modules into Topic</h4>
                                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form id="mergeTopicForm" action="#" method="post" onsubmit="return false;">
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label for="existing_topic">Select Existing Topic</label>
                                                <select id="existing_topic" name="existing_topic" class="form-control single-select">
                                                    <option value="-1">None</option>
                                                </select>
                                            </div>
                                            <div class="text-center mb-2">OR</div>
                                            <div class="form-group">
                                                <label for="new_topic_name">Create New Topic</label>
                                                <input type="text" id="new_topic_name" name="new_topic_name" class="form-control" placeholder="Enter new topic name" autocomplete="off">
                                            </div>
                                            <small class="text-muted">If both are provided, a new topic will be created and used.</small>
                                        </div>
                                        <div class="modal-footer text-center">
                                            <button type="button" class="btn btn-success" onclick="mergeSelectedToTopic()">Merge</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="assets/js/jquery.min.js"></script>

<script>
function saveAllOrder() {
    const moduleIds = [];
    let counter = 0;
    document.querySelectorAll('tbody.module-group').forEach(group => {
        group.querySelectorAll('tr.module-row').forEach(row => {
            const moduleId = row.getAttribute('data-module-id');
            counter++;
            moduleIds.push({ id: moduleId, order: counter });
        });
    });

    const saveButton = event.target;
    const originalText = saveButton.innerHTML;
    saveButton.innerHTML = '<i class="fa fa-spinner fa-spin mr-2"></i>Saving...';
    saveButton.disabled = true;

    $.post('controller/trainingController.php', {
        action: 'saveTrainingModuleOrder',
        module_orders: moduleIds
    }, function(response) {
        try {
            const result = typeof response === 'string' ? JSON.parse(response) : response;
            if (result.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Module order saved successfully'
                }).then(() => { 
                    const productType = document.getElementById('product_type_filter') ? document.getElementById('product_type_filter').value : 'hrms';
                    window.location.href = 'trainingModuleOrder?product_type=' + productType;
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: result.message || 'Failed to save module order'
                });
            }
        } catch (e) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Invalid response from server' });
        }
    }).fail(function() {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to connect to server' });
    }).always(function() {
        saveButton.innerHTML = originalText;
        saveButton.disabled = false;
    });
}

$(document).ready(function() {
    if (typeof $.ui !== 'undefined' && $.ui.sortable) {
        // Sort rows within each group
        $('tbody.module-group').each(function() {
            $(this).sortable({
                items: 'tr.module-row',
                handle: 'td',
                animation: 150,
                stop: function() { updateOrderNumbers(); }
            });
        });

        // Sort whole groups (tbodies) using their header row as handle
        const $table = $('table.table.table-bordered');
        // Wrap tbodies in a container to allow sorting if necessary
        const $tbodies = $table.children('tbody.module-group');
        $table.sortable({
            items: 'tbody.module-group',
            handle: 'tr.group-header',
            animation: 150,
            helper: function(e, item){
                // Keep visual width while dragging tbody
                const $helper = $('<div class="bg-light border"></div>');
                $helper.width($(item).width());
                $helper.height($(item).height());
                $helper.text($(item).find('tr.group-header strong').text());
                return $helper;
            },
            start: function(e, ui){ ui.placeholder = $('<tbody class="module-group"></tbody>'); },
            stop: function(){ updateOrderNumbers(); }
        });
    } else {
        console.warn('jQuery UI sortable not available. Drag and drop functionality disabled.');
    }

    // Collapse/Expand All handlers
    $('#collapseAllGroups').on('click', function(){
        $('tbody.module-group').each(function(){
            $(this).data('collapsed', true);
            $(this).find('tr.module-row').hide();
        });
    });
    $('#expandAllGroups').on('click', function(){
        $('tbody.module-group').each(function(){
            $(this).data('collapsed', false);
            $(this).find('tr.module-row').show();
        });
    });

    // Toggle single group on header click
    $(document).on('click', 'tr.group-header', function(e){
        // Avoid toggling when dragging (mousedown handled by sortable). Only toggle on simple click without drag.
        if ($(e.target).closest('button, a, input, select, textarea').length) return;
        const $tbody = $(this).closest('tbody.module-group');
        const isCollapsed = $tbody.data('collapsed') === true;
        if (isCollapsed) {
            $tbody.data('collapsed', false);
            $tbody.find('tr.module-row').show();
        } else {
            $tbody.data('collapsed', true);
            $tbody.find('tr.module-row').hide();
        }
    });

    // Group checkbox toggles rows in that group
    $(document).on('change', '.groupCheckbox', function(){
        const $tbody = $(this).closest('tbody.module-group');
        const checked = this.checked;
        $tbody.find('.moduleCheckbox').prop('checked', checked);
        updateGlobalSelectAllState();
        updateGroupIndeterminate($tbody);
    });

    // Row checkbox updates group and global states
    $(document).on('change', '.moduleCheckbox', function(){
        const $tbody = $(this).closest('tbody.module-group');
        updateGroupCheckboxState($tbody);
        updateGlobalSelectAllState();
    });

    // Load topics list initially
    loadTopics();
    
    // Default collapse all groups
    $('tbody.module-group').each(function(){
        $(this).data('collapsed', true);
        $(this).find('tr.module-row').hide();
    });
});

function updateOrderNumbers() {
    let index = 0;
    document.querySelectorAll('tbody.module-group tr.module-row').forEach((row) => {
        index++;
        const badge = row.querySelector('.order-badge');
        if (badge) { badge.textContent = index; }
    });
}

function toggleSelectAll(source) {
    const checkboxes = document.querySelectorAll('.moduleCheckbox');
    checkboxes.forEach(cb => cb.checked = source.checked);
    // Update all group checkboxes to reflect global state
    document.querySelectorAll('tbody.module-group').forEach(tb => {
        const groupCb = tb.querySelector('.groupCheckbox');
        if (groupCb) {
            groupCb.checked = source.checked;
            groupCb.indeterminate = false;
        }
    });
}

function openMergeTopicModal() {
    const selected = getSelectedModules();
    if (selected.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Select modules',
            text: 'Please select at least one module to merge.'
        });
        return;
    }
    loadTopicsIntoSelect();
    $('#mergeTopicModal').modal('show');
}

function getSelectedModules() {
    const ids = [];
    document.querySelectorAll('.moduleCheckbox:checked').forEach(cb => ids.push(cb.value));
    return ids;
}

function loadTopicsIntoSelect() {
    const productType = document.getElementById('product_type_filter') ? document.getElementById('product_type_filter').value : 'hrms';
    const topicType = (productType === 'crm') ? 1 : 0; // 0 = HRMS, 1 = CRM
    $.post('controller/trainingController.php', {
        action: 'getTopics',
        topic_type: topicType
    }, function(resp) {
        try {
            const res = (typeof resp === 'string') ? JSON.parse(resp) : resp;
            const sel = document.getElementById('existing_topic');
            sel.innerHTML = '<option value="-1">None</option>';
            if (res.success && Array.isArray(res.data)) {
                res.data.forEach(t => {
                    const opt = document.createElement('option');
                    opt.value = t.topic_id;
                    opt.textContent = t.topic_name;
                    sel.appendChild(opt);
                });
            }
        } catch (e) {}
    });
}

function mergeSelectedToTopic() {
    const selectedIds = getSelectedModules();
    let topicId = document.getElementById('existing_topic').value;
    const newTopicName = document.getElementById('new_topic_name').value.trim();

    const proceed = (finalTopicId) => {
        $.post('controller/trainingController.php', {
            action: 'assignModulesToTopic',
            topic_id: finalTopicId,
            module_ids: selectedIds
        }, function(resp) {
            try {
                const res = (typeof resp === 'string') ? JSON.parse(resp) : resp;
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Merged',
                        text: 'Modules merged into topic'
                    }).then(() => {
                        const productType = document.getElementById('product_type_filter') ? document.getElementById('product_type_filter').value : 'hrms';
                        window.location.href = 'trainingModuleOrder?product_type=' + productType;
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: res.message || 'Failed to merge modules'
                    });
                }
            } catch (e) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Invalid response from server'
                });
            }
        });
    };

    if (newTopicName) {
        $.post('controller/trainingController.php', {
            action: 'createTopic',
            topic_name: newTopicName
        }, function(resp) {
            try {
                const res = (typeof resp === 'string') ? JSON.parse(resp) : resp;
                if (res.success && res.data && res.data.topic_id) {
                    proceed(res.data.topic_id);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: res.message || 'Could not create topic'
                    });
                }
            } catch (e) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Invalid response while creating topic'
                });
            }
        });
        return;
    }

    if (!topicId) {
        Swal.fire({
            icon: 'warning',
            title: 'Select or enter topic',
            text: 'Choose existing topic or enter a new topic name.'
        });
        return;
    }
    proceed(parseInt(topicId, 10));
}

function updateGroupCheckboxState($tbody){
    const $rows = $tbody.find('.moduleCheckbox');
    const total = $rows.length;
    const checked = $rows.filter(':checked').length;
    const groupCb = $tbody.find('.groupCheckbox').get(0);
    if (!groupCb) return;
    if (checked === 0) {
        groupCb.checked = false;
        groupCb.indeterminate = false;
    } else if (checked === total) {
        groupCb.checked = true;
        groupCb.indeterminate = false;
    } else {
        groupCb.checked = false;
        groupCb.indeterminate = true;
    }
}

function updateGroupIndeterminate($tbody){
    // Ensure indeterminate reflects mixed state after bulk toggle
    updateGroupCheckboxState($tbody);
}

// Topics CRUD
function openTopicModal(topic){
    if (topic) {
        $('#topicModalTitle').text('Edit Topic');
        $('#topic_id').val(topic.topic_id);
        $('#topic_name').val(topic.topic_name);
        $('#topic_completion_days').val(topic.completion_days);
        $('#topic_next_start_days').val(topic.next_start_days);
        
        // Set topic_type (0 = HRMS, 1 = CRM)
        const topicType = topic.topic_type !== undefined ? topic.topic_type : 0;
        $('#topic_type').val(topicType).trigger('change');
        
        // Load participant types only for HRMS topics
        if (topicType == 0) {
            loadParticipantTypes(function() {
                $('#participant_type').val(topic.participants_type_id || '').trigger('change');
            });
        } else {
            // CRM topics don't have participants
            $('#participant_type').empty().append('<option value="">-- N/A (CRM Topic) --</option>');
        }
    } else {
        $('#topicModalTitle').text('Add Topic');
        $('#topicForm')[0].reset();
        $('#topic_id').val('');
        $('#topic_type').val('0').trigger('change');
        loadParticipantTypes();
    }
    
    $('#topicModal').modal('show');
}

function toggleParticipantField() {
    const topicType = $('#topic_type').val();
    const participantGroup = $('#participant_type_group');
    const participantSelect = $('#participant_type');
    
    if (topicType == '1') {
        // CRM topics don't have participants
        participantGroup.hide();
        participantSelect.removeAttr('required');
        participantSelect.empty().append('<option value="">-- N/A (CRM Topic) --</option>');
    } else {
        // HRMS topics require participants
        participantGroup.show();
        participantSelect.attr('required', 'required');
        if (participantSelect.find('option').length <= 1) {
            loadParticipantTypes();
        }
    }
}

function loadTopics(){
    const productType = document.getElementById('product_type_filter') ? document.getElementById('product_type_filter').value : 'hrms';
    const topicType = (productType === 'crm') ? 1 : 0; // 0 = HRMS, 1 = CRM
    $.post('controller/trainingController.php', { 
        action: 'getTopicsWithMeta',
        topic_type: topicType,
        module_type: (productType === 'crm') ? 2 : 1
    }, function(resp){
        try {
            const res = (typeof resp === 'string') ? JSON.parse(resp) : resp;
            const $tbody = $('#topicsTbody');
            $tbody.empty();
            if (res.success && Array.isArray(res.data)) {
                res.data.forEach(t => {
                    const tr = $('<tr></tr>');
                    tr.append('<td>' + escapeHtml(t.topic_name || '') + '</td>');
                    // Show topic type: 0 = HRMS, 1 = CRM
                    const topicType = t.topic_type !== undefined ? t.topic_type : 0;
                    const typeBadge = topicType == 1 ? '<span class="badge badge-info">CRM</span>' : '<span class="badge badge-secondary">HRMS</span>';
                    tr.append('<td class="text-center">' + typeBadge + '</td>');
                    tr.append('<td class="text-center">' + (t.completion_days ?? '—') + '</td>');
                    tr.append('<td class="text-center">' + (t.next_start_days ?? '—') + '</td>');
                    // CRM topics don't have participants
                    const participantDisplay = topicType == 1 ? '—' : escapeHtml(t.participant_type_name || '—');
                    tr.append('<td class="text-center">' + participantDisplay + '</td>');
                    const actionTd = $('<td class="text-center"></td>');
                    const editBtn = $('<button class="btn btn-sm btn-outline-primary mr-2"><i class="fa fa-pencil"></i> Edit</button>');
                    editBtn.on('click', function(){ openTopicModal(t); });
                    const delBtn = $('<button class="btn btn-sm btn-outline-danger"><i class="fa fa-trash"></i> Delete</button>');
                    delBtn.on('click', function(){ deleteTopic(t.topic_id); });
                    actionTd.append(editBtn).append(delBtn);
                    tr.append(actionTd);
                    $tbody.append(tr);
                });
            } else {
                $tbody.append('<tr><td colspan="6" class="text-center text-muted">No topics found</td></tr>');
            }
        } catch(e) {
            $('#topicsTbody').html('<tr><td colspan="5" class="text-center text-danger">Failed to parse topics</td></tr>');
        }
    }).fail(function(){
        $('#topicsTbody').html('<tr><td colspan="5" class="text-center text-danger">Failed to load topics</td></tr>');
    });
}

function saveTopic(){
    const topicId = $('#topic_id').val();
    const name = $('#topic_name').val().trim();
    const completion = $('#topic_completion_days').val().trim();
    const nextStart = $('#topic_next_start_days').val().trim();
    const participant = $('#participant_type').val();
    const topicType = $('#topic_type').val(); // 0 = HRMS, 1 = CRM

    $.post('controller/trainingController.php', {
        action: 'saveTopicWithMeta',
        topic_id: topicId || undefined,
        topic_name: name,
        completion_days: completion,
        next_start_days: nextStart,
        participants_type_id: participant,
        topic_type: topicType
    }, function(resp){
        try {
            const res = (typeof resp === 'string') ? JSON.parse(resp) : resp;
            if (res.success) {
                Swal.fire({ icon:'success', title:'Saved', text:'Topic saved successfully' }).then(() => { 
                    $('#topicModal').modal('hide');
                    loadTopics();
                    const productType = document.getElementById('product_type_filter') ? document.getElementById('product_type_filter').value : 'hrms';
                    window.location.href = 'trainingModuleOrder?product_type=' + productType;
                });
                $(".ajax-loader").hide();
            } else {
                Swal.fire({ icon:'error', title:'Error', text: res.message || 'Failed to save topic' });
            }
        } catch(e){
            Swal.fire({ icon:'error', title:'Error', text:'Invalid response from server' });
        }
    }).fail(function(){
        Swal.fire({ icon:'error', title:'Error', text:'Failed to connect to server' });
    });
}

function deleteTopic(topicId){
    if (!topicId) return;
    
    // First check if topic contains modules
    $.post('controller/trainingController.php', { action:'checkTopicModules', topic_id: topicId }, function(resp){
        try {
            const r = (typeof resp === 'string') ? JSON.parse(resp) : resp;
            console.log('Parsed response:', r); // Debug log
            if (r.success && r.hasModules) {
                Swal.fire({
                    icon:'warning',
                    title:'Cannot Delete Topic',
                    text:'This topic contains training modules. Please remove or reassign the modules before deleting the topic.',
                    confirmButtonText:'OK',
                    confirmButtonColor:'#3085d6'
                });
                return;
            }
            
            // If no modules, proceed with deletion confirmation
            Swal.fire({
                title:'Delete topic?',
                text:"This action cannot be undone",
                icon:'warning',
                showCancelButton:true,
                confirmButtonText:'Delete',
                confirmButtonColor:'#d33'
            }).then((res)=>{
                if (!res.isConfirmed) return;
                $.post('controller/trainingController.php', { action:'deleteTopicWithMeta', topic_id: topicId }, function(resp){
                    try {
                        const r = (typeof resp === 'string') ? JSON.parse(resp) : resp;
                        if (r.success) {
                            Swal.fire({ icon:'success', title:'Deleted', text:'Topic deleted' }).then(()=>{ 
                                loadTopics(); 
                                const productType = document.getElementById('product_type_filter') ? document.getElementById('product_type_filter').value : 'hrms';
                                window.location.href = 'trainingModuleOrder?product_type=' + productType;
                            });
                        } else {
                            Swal.fire({ icon:'error', title:'Error', text: r.message || 'Failed to delete topic' });
                        }
                    } catch(e){
                        Swal.fire({ icon:'error', title:'Error', text:'Invalid response from server' });
                    }
                }).fail(function(){
                    Swal.fire({ icon:'error', title:'Error', text:'Failed to connect to server' });
                });
            });
        } catch(e){
            console.error('Error parsing response:', e);
            console.error('Raw response:', resp);
            Swal.fire({ 
                icon:'error', 
                title:'Error', 
                text:'Invalid response from server: ' + e.message 
            });
        }
    }).fail(function(xhr, status, error){
        console.error('AJAX failed:', {xhr, status, error});
        Swal.fire({ 
            icon:'error', 
            title:'Error', 
            text:'Failed to connect to server: ' + error 
        });
    });
}

function loadParticipantTypes(callback){
    $.post('controller/trainingController.php', { action: 'getParticipantTypes' }, function(resp){
        try {
            const res = (typeof resp === 'string') ? JSON.parse(resp) : resp;
            const $select = $('#participant_type');
            $select.find('option:not(:first)').remove();
            if (res.success && Array.isArray(res.data)) {
                res.data.forEach(p => {
                    $select.append(`<option value="${p.id}">${escapeHtml(p.name)}</option>`);
                });
            }
            // Execute callback after options are loaded
            if (typeof callback === 'function') {
                callback();
            }
        } catch(e) {
            console.error('Failed to load participant types:', e);
        }
    }).fail(function(){
        console.error('Failed to load participant types');
    });
}

function escapeHtml(str){
    if (str === null || str === undefined) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function updateGlobalSelectAllState(){
    const all = document.querySelectorAll('.moduleCheckbox');
    const allChecked = Array.from(all).length > 0 && Array.from(all).every(cb => cb.checked);
    const anyChecked = Array.from(all).some(cb => cb.checked);
    const global = document.getElementById('selectAllModules');
    if (!global) return;
    if (allChecked) {
        global.checked = true;
        global.indeterminate = false;
    } else if (anyChecked) {
        global.checked = false;
        global.indeterminate = true;
    } else {
        global.checked = false;
        global.indeterminate = false;
    }
}
</script>

