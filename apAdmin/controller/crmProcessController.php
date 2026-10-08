<?php
include '../common/objectController.php';
if (isset($_POST) && !empty($_POST)) {
    extract($_POST);
    if (isset($_POST['action']) && $_POST['action'] == 'get_crm_training_modules') {
        $society_id = isset($_POST['society_id']) ? $d->sanitizeActionIdAsInt($_POST['society_id']) : 0;
        if ($society_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid society ID']);
            exit;
        }
        try {
            $modulesQuery = $d->selectRow(
                "tmm.training_module_id, tmm.training_module_name, tmm.topic_id, tmm.completion_days as module_completion_days,
                 tmm.module_priority, tmpm.priority_name, tmpm.priority_id,
                 tmt.topic_id, tmt.topic_name, tmt.completion_days as topic_completion_days,
                 tmm.training_module_order",
                "training_module_master tmm
                 LEFT JOIN training_module_topics tmt ON tmm.topic_id = tmt.topic_id AND tmt.topic_type = 1
                 LEFT JOIN training_module_priority_master tmpm ON tmm.module_priority = tmpm.priority_id",
                "tmm.module_type = 2 AND tmm.training_module_status = 0",
                "ORDER BY COALESCE(tmt.topic_name, ''), COALESCE(tmm.training_module_order, tmm.module_priority) ASC, tmm.training_module_id ASC"
            );
            $topicsMap = [];
            $modulesList = [];
            $moduleIds = [];
            $modulesData = [];
            
            if (mysqli_num_rows($modulesQuery) > 0) {
                while ($module = mysqli_fetch_assoc($modulesQuery)) {
                    $moduleId = intval($module['training_module_id']);
                    $moduleIds[] = $moduleId;
                    $modulesData[] = $module;
                }
            }
            $subtopicsMap = [];
            if (!empty($moduleIds)) {
                $moduleIdsStr = implode(',', array_map('intval', $moduleIds));
                $subtopicsQuery = $d->selectRow(
                    "subtopic_id, training_module_id, subtopic_name, subtopic_description, display_order, estimated_minutes",
                    "training_module_subtopics",
                    "training_module_id IN ($moduleIdsStr)",
                    "ORDER BY training_module_id ASC, display_order ASC, subtopic_id ASC"
                );
                
                if (mysqli_num_rows($subtopicsQuery) > 0) {
                    while ($subtopic = mysqli_fetch_assoc($subtopicsQuery)) {
                        $moduleId = intval($subtopic['training_module_id']);
                        if (!isset($subtopicsMap[$moduleId])) {
                            $subtopicsMap[$moduleId] = [];
                        }
                        $subtopicsMap[$moduleId][] = [
                            'subtopic_id' => intval($subtopic['subtopic_id']),
                            'subtopic_name' => $subtopic['subtopic_name'],
                            'subtopic_description' => $subtopic['subtopic_description'] ?? '',
                            'display_order' => intval($subtopic['display_order'] ?? 0),
                            'estimated_minutes' => isset($subtopic['estimated_minutes']) ? intval($subtopic['estimated_minutes']) : null
                        ];
                    }
                }
            }
            
            foreach ($modulesData as $module) {
                $moduleId = intval($module['training_module_id']);
                $topicId = isset($module['topic_id']) && $module['topic_id'] ? intval($module['topic_id']) : 0;
                $topicName = isset($module['topic_name']) && $module['topic_name'] ? $module['topic_name'] : 'Ungrouped';
                
                $subtopics = isset($subtopicsMap[$moduleId]) ? $subtopicsMap[$moduleId] : [];
                
                    $modulesList[] = [
                        'module_id' => $moduleId,
                        'module_name' => $module['training_module_name'],
                        'module_completion_days' => isset($module['module_completion_days']) && $module['module_completion_days'] !== null ? intval($module['module_completion_days']) : null,
                        'module_priority' => isset($module['priority_name']) && $module['priority_name'] ? $module['priority_name'] : null,
                        'module_priority_id' => isset($module['priority_id']) && $module['priority_id'] !== null ? intval($module['priority_id']) : null,
                        'topic_id' => $topicId,
                        'topic_name' => $topicName,
                        'topic_completion_days' => isset($module['topic_completion_days']) && $module['topic_completion_days'] !== null ? intval($module['topic_completion_days']) : null,
                        'subtopics' => $subtopics
                    ];
            }
            
            foreach ($modulesList as $module) {
            $topicKey = $module['topic_id'] . '_' . $module['topic_name'];
            if (!isset($topicsMap[$topicKey])) {
                $topicsMap[$topicKey] = [
                    'topic_id' => $module['topic_id'],
                    'topic_name' => $module['topic_name'],
                    'topic_completion_days' => $module['topic_completion_days'],
                    'modules' => []
                ];
            }
                $topicsMap[$topicKey]['modules'][] = $module;
            }
            
            $progressMap = [];
            $progressQuery = $d->selectRow(
                "progress_id, module_id, subtopic_id, status, remark, completed_date",
                "crm_training_progress",
                "society_id = $society_id"
            );
            
            if (mysqli_num_rows($progressQuery) > 0) {
                while ($progress = mysqli_fetch_assoc($progressQuery)) {
                    $moduleId = intval($progress['module_id']);
                    $subtopicId = isset($progress['subtopic_id']) ? intval($progress['subtopic_id']) : 0;
                    $key = $moduleId . '_' . ($subtopicId > 0 ? $subtopicId : 'module');
                    $progressMap[$key] = [
                        'progress_id' => intval($progress['progress_id']),
                        'status' => intval($progress['status']),
                        'remark' => isset($progress['remark']) && $progress['remark'] !== null && $progress['remark'] !== '' ? $progress['remark'] : '',
                        'completed_date' => isset($progress['completed_date']) && $progress['completed_date'] !== null ? $progress['completed_date'] : null
                    ];
                }
            }
            
            echo json_encode([
                'success' => true,
                'topics' => array_values($topicsMap),
                'progress' => $progressMap
            ], JSON_UNESCAPED_UNICODE);
            exit;
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            exit;
        }
        
    } else if (isset($_POST['action']) && $_POST['action'] == 'save_crm_training_progress') {
        $society_id = isset($_POST['society_id']) ? $d->sanitizeActionIdAsInt($_POST['society_id']) : 0;
        $module_id = isset($_POST['module_id']) ? intval($_POST['module_id']) : 0;
        $subtopic_id = isset($_POST['subtopic_id']) && $_POST['subtopic_id'] != '' ? intval($_POST['subtopic_id']) : null;
        $status = isset($_POST['status']) ? intval($_POST['status']) : 0;
        $remark = isset($_POST['remark']) ? trim($_POST['remark']) : '';
        
        if ($society_id <= 0 || $module_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
            exit;
        }
        
        if (!in_array($status, [0, 1, 2, 3])) {
            echo json_encode(['success' => false, 'message' => 'Invalid status value']);
            exit;
        }
        
        $subtopicIdValue = ($subtopic_id !== null && $subtopic_id !== '') ? intval($subtopic_id) : 0;
        
        $m->set_data('society_id', $society_id);
        $m->set_data('module_id', $module_id);
        $m->set_data('subtopic_id', $subtopicIdValue);
        $m->set_data('status', $status);
        $m->set_data('remark', $remark ? $remark : null);
        
        $whereClause = "society_id = '$society_id' AND module_id = '$module_id' AND subtopic_id = '$subtopicIdValue'";
        
        try {
            $existingQuery = $d->selectRow("progress_id, status, remark, completed_date", "crm_training_progress", $whereClause, "LIMIT 1");
            
            $data = array(
                'society_id' => $m->get_data('society_id'),
                'module_id' => $m->get_data('module_id'),
                'subtopic_id' => $m->get_data('subtopic_id'),
                'status' => $m->get_data('status'),
                'remark' => $m->get_data('remark')
            );
            
            if (mysqli_num_rows($existingQuery) > 0) {
                $existing = mysqli_fetch_assoc($existingQuery);
                $progress_id = intval($existing['progress_id']);
                $existingStatus = intval($existing['status']);
                $existingRemark = isset($existing['remark']) && $existing['remark'] !== null ? trim($existing['remark']) : '';
                $currentRemark = $remark ? trim($remark) : '';
                
                // Check if status or remark has changed
                $statusChanged = ($status != $existingStatus);
                $remarkChanged = ($currentRemark !== $existingRemark);
                $hasChanges = $statusChanged || $remarkChanged;
                
                // Only update updated_by and updated_date if there are actual changes
                if ($hasChanges) {
                    $data['updated_by'] = $bms_admin_id;
                    $data['updated_date'] = date('Y-m-d H:i:s');
                }
                // If no changes, don't include updated_by and updated_date in the update
                
                // Handle completed_date: update when status changes
                if ($statusChanged) {
                    $data['completed_date'] = date('Y-m-d H:i:s');
                } else {
                    $existingCompletedDate = isset($existing['completed_date']) && $existing['completed_date'] !== null ? $existing['completed_date'] : null;
                    if ($existingCompletedDate !== null) {
                        $data['completed_date'] = $existingCompletedDate;
                    }
                }
                
                // Only perform update if there are actual changes
                if ($hasChanges) {
                    $result = $d->update("crm_training_progress", $data, "progress_id='$progress_id'");
                } else {
                    // No changes, return success without updating
                    echo json_encode(['success' => true, 'message' => 'No changes detected']);
                    exit;
                }
            } else {
                // Insert new record
                $m->set_data('created_by', $bms_admin_id);
                $m->set_data('created_date', date('Y-m-d H:i:s'));
                $m->set_data('updated_by', $bms_admin_id);
                $m->set_data('updated_date', date('Y-m-d H:i:s'));
                $data['created_by'] = $m->get_data('created_by');
                $data['created_date'] = $m->get_data('created_date');
                $data['updated_by'] = $m->get_data('updated_by');
                $data['updated_date'] = $m->get_data('updated_date');
                $data['completed_date'] = date('Y-m-d H:i:s');
                $result = $d->insert("crm_training_progress", $data);
            }
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Progress saved successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to save progress']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
        
    } else if (isset($_POST['action']) && $_POST['action'] == 'get_steps') {
        echo json_encode(['success' => false, 'message' => 'Use get_crm_training_modules action instead']);
        exit;
    } else if (isset($_POST['action']) && $_POST['action'] == 'update_step') {
        echo json_encode(['success' => false, 'message' => 'Use save_crm_training_progress action instead']);
        exit;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

