<?php
include '../common/objectController.php';

/**
 * Merge per-meeting training status with stored master row.
 * Values: 0=pending, 1=completed, 2=na, 3=later on — not monotonic (3 is not "greater" than 1).
 * Without this, completed (1) never replaces later on (3) because 1 > 3 is false.
 */
function merge_training_status_meeting($prev, $new)
{
    $prev = (int) $prev;
    $new = (int) $new;
    $doneStates = array(1, 2);
    if (in_array($new, $doneStates, true)) {
        return $new;
    }
    if (in_array($prev, $doneStates, true)) {
        return $prev;
    }
    return $new;
}

if (isset($_POST) && !empty($_POST)) {
    if (isset($_POST['addBatch']) &&  isset($_POST['batch_name']) && isset($_POST['training_days']) && isset($_POST['batch_type']) && isset($_POST['participant_name'])) {
        $count = 0;
        while (isset($_POST['day' . ($count + 1)])) {
            $count++;
        }
        if ($_POST['training_days'] == $count) {
            $batch_name = $_POST['batch_name'];
            $training_days = $_POST['training_days'];
            $batch_type = $_POST['batch_type'];
            if (is_array($_POST['participant_name'])) {
                $allowed_participants = implode(",", $_POST['participant_name']);
            } else {
                $allowed_participants = $_POST['participant_name'];
            }
            $created_date = date("Y-m-d H:i:s");
            $m->set_data('batch_name', $batch_name);
            $m->set_data('training_days', $training_days);
            $m->set_data('batch_type', $batch_type);
            $m->set_data('allowed_participants', $allowed_participants);
            $m->set_data('created_date', $created_date);

            $batch_data = array(
                'batch_name' => $m->get_data('batch_name'),
                'training_days' => $m->get_data('training_days'),
                'batch_type' => $m->get_data('batch_type'),
                'allowed_participants' => $m->get_data('allowed_participants'),
                'created_date' => $m->get_data('created_date')
            );

            $q = $d->insert("training_batch_master", $batch_data);
            if ($q > 0) {
                $batch_id = $d->getInsertId();
                for ($i = 1; $i <= $_POST['training_days']; $i++) {
                    if (isset($_POST['day' . $i])) {
                        $module_ids = $_POST['day' . $i];
                    } else {
                        $module_ids = [];
                    }

                    if (!empty($module_ids)) {
                        $module_ids_str = trim(implode(',', $module_ids), ',');
                    } else {
                        $module_ids_str = '';
                    }

                    $module_data = array(
                        'batch_id' => $batch_id,
                        'module_ids' => $module_ids_str,
                        'day_number' => $i
                    );
                    $d->insert("batch_module_master", $module_data);
                }
                $_SESSION['msg'] = "New Batch successfully added.";
                header("location:../manageBatch");
                exit;
            } else {
                $_SESSION['msg1'] = "Something went wrong while adding batch.";
                header("location:../addBatch");
                exit;
            }
        } else {
            $_SESSION['msg1'] = "Please Select Day Wise Modules.";
            header("location:../addBatch");
            exit;
        }
    } else if (isset($_POST['editBatch'])) {
        $count = 0;
        while (isset($_POST['day' . ($count + 1)])) {
            $count++;
        }
        if ($_POST['training_days'] == $count) {
            $batch_id = $_POST['batch_id'];
            $batch_name = $_POST['batch_name'];
            $batch_type = $_POST['batch_type'];
            $allowed_participants = $_POST['participant_name'];
            $training_days = $_POST['training_days'];
            $batch_name = test_input($batch_name);
            $batch_type = test_input($batch_type);
            if (is_array($allowed_participants)) {
                $allowed_participants = implode(',', $allowed_participants);
            }
            $allowed_participants = test_input($allowed_participants);
            if (is_array($training_days)) {
                $training_days = implode(',', $training_days);
            }
            $training_days = test_input($training_days);
            $update_data = [
                'batch_name' => $batch_name,
                'batch_type' => $batch_type,
                'allowed_participants' => $allowed_participants,
                'training_days' => $training_days
            ];

            $update_query = $d->update("training_batch_master", $update_data, "batch_id = '$batch_id'");

            if ($update_query > 0) {
                $d->delete("batch_module_master", "batch_id = '$batch_id'");
                for ($i = 1; $i <= $training_days; $i++) {
                    $day_key = "day" . $i;
                    if (isset($_POST[$day_key]) && !empty($_POST[$day_key])) {
                        $module_ids = $_POST[$day_key];
                        $module_ids_str = trim(implode(',', $module_ids), ',');
                        $module_data = array(
                            'batch_id' => $batch_id,
                            'module_ids' => $module_ids_str,
                            'day_number' => $i
                        );
                        $d->insert("batch_module_master", $module_data);
                    }
                }
                $_SESSION['msg'] = "Batch successfully updated.";
                header("location:../manageBatch");
                exit;
            } else {
                $_SESSION['msg1'] = "Something went wrong while adding batch.";
                header("location:../addBatch?batch_id=$batch_id");
                exit;
            }
        } else {
            $_SESSION['msg1'] = "Please Select Day Wise Modules.";
            header("location:../addBatch?batch_id=$batch_id");
            exit;
        }
    } else if (isset($_POST['startBatchMeeting'])) {
        // echo "<pre>";
        // print_r($_POST);
        // exit;
        $company_ids = isset($_POST['company_ids']) ? (is_array($_POST['company_ids']) ? $_POST['company_ids'] : explode(',', $_POST['company_ids'])) : [];
        $slot_id = isset($_POST['slot_id']) ? intval($_POST['slot_id']) : 0;
        $trainer_id = isset($_POST['trainer_id']) ? intval($_POST['trainer_id']) : 0;
        $recording_link = isset($_POST['recording_link']) ? $_POST['recording_link'] : '';
        $training_password = isset($_POST['training_password']) ? $_POST['training_password'] : '';
        $batch_id = isset($_POST['batch_id']) ? intval($_POST['batch_id']) : 0;
        $present_participants = isset($_POST['present_participants']) && is_array($_POST['present_participants']) ? $_POST['present_participants'] : [];

        $mom_attachment = '';
        $uploadDir = "../../img/training_meetings/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
            chmod($uploadDir, 0777);
        }
        if (!isset($_FILES['mom_attachment']['tmp_name']) || empty($_FILES['mom_attachment']['tmp_name']) || !file_exists($_FILES['mom_attachment']['tmp_name'])) {
            $_SESSION['msg1'] = "MOM attachment screenshot is required.";
            header("Location:../manageTrainingMeeting?meeting_slot_id=$slot_id");
            exit;
        }
        $momFile = $_FILES['mom_attachment']['tmp_name'];
        $acceptableMomExt = array("jpeg", "jpg", "png");
        $momExt = strtolower(pathinfo($_FILES['mom_attachment']['name'], PATHINFO_EXTENSION));
        if (!in_array($momExt, $acceptableMomExt)) {
            $_SESSION['msg1'] = "MOM attachment must be an image (JPG, JPEG, PNG).";
            header("Location:../manageTrainingMeeting?meeting_slot_id=$slot_id");
            exit;
        }
        $momFilename = 'MOM_' . round(microtime(true)) . '_' . rand(1000, 9999) . '.' . $momExt;
        $momDestinationPath = $uploadDir . $momFilename;
        if (in_array($momExt, array('jpeg', 'jpg', 'png'))) {
            $d->resizeImage($momFile, $momDestinationPath, 800, 800, $momExt);
        } else {
            move_uploaded_file($momFile, $momDestinationPath);
        }
        $mom_attachment = $momFilename;

        // Merge companies referenced anywhere in payload (attendance_*, participants, module arrays)
        $referenced_company_ids = [];
        foreach (array_keys($_POST) as $postKey) {
            if (preg_match('/^attendance_(\d+)$/', $postKey, $mCompany)) {
                $cid = (int)$mCompany[1];
                if ($cid > 0) {
                    $referenced_company_ids[] = (string)$cid;
                }
            }
        }
        if (isset($_POST['present_participants']) && is_array($_POST['present_participants'])) {
            foreach (array_keys($_POST['present_participants']) as $cid) {
                if (is_numeric($cid)) {
                    $referenced_company_ids[] = (string)intval($cid);
                }
            }
        }
        if (isset($_POST['training_status_module']) && is_array($_POST['training_status_module'])) {
            foreach (array_keys($_POST['training_status_module']) as $cid) {
                if (is_numeric($cid)) {
                    $referenced_company_ids[] = (string)intval($cid);
                }
            }
        }
        if (isset($_POST['training_status_subtopic']) && is_array($_POST['training_status_subtopic'])) {
            foreach (array_keys($_POST['training_status_subtopic']) as $cid) {
                if (is_numeric($cid)) {
                    $referenced_company_ids[] = (string)intval($cid);
                }
            }
        }
        if (isset($_POST['module_remark']) && is_array($_POST['module_remark'])) {
            foreach (array_keys($_POST['module_remark']) as $cid) {
                if (is_numeric($cid)) {
                    $referenced_company_ids[] = (string)intval($cid);
                }
            }
        }
        $company_ids = array_values(array_unique(array_merge($company_ids, $referenced_company_ids)));
        if (isset($company_ids) && is_array($company_ids)) {
            $update_companyids = implode(',', $company_ids);
            $a = array(
                'company_id' => $update_companyids,
            );
            $update = $d->update("batch_slot_master", $a, "slot_id='$slot_id'");

            if (isset($_POST['reference_company_ids']) && !empty($_POST['reference_company_ids'])) {
                $reference_company_ids = $_POST['reference_company_ids'];
                $b = array(
                    'reference_company_ids' => $reference_company_ids,
                );
                $updateRef = $d->update("batch_slot_master", $b, "slot_id='$slot_id'");
            }

            $training_status = isset($training_status) && is_array($training_status) ? $training_status : [];
            $ts_module = isset($_POST['training_status_module']) && is_array($_POST['training_status_module']) ? $_POST['training_status_module'] : [];
            $ts_sub = isset($_POST['training_status_subtopic']) && is_array($_POST['training_status_subtopic']) ? $_POST['training_status_subtopic'] : [];

            if (!empty($ts_module) || !empty($ts_sub)) {
                foreach ($company_ids as $cidMerge) {
                    $companyMerged = [];
                    if (isset($ts_module[$cidMerge]) && is_array($ts_module[$cidMerge])) {
                        $companyMerged = $ts_module[$cidMerge];
                    }
                    if (isset($ts_sub[$cidMerge]) && is_array($ts_sub[$cidMerge])) {
                        foreach ($ts_sub[$cidMerge] as $moduleKey => $subs) {
                            if (!is_array($subs)) {
                                continue;
                            }
                            foreach ($subs as $subtopicId => $statusVal) {
                                $companyMerged[(int)$subtopicId] = $statusVal;
                            }
                        }
                    }
                    if (!empty($companyMerged)) {
                        if (!isset($training_status[$cidMerge]) || !is_array($training_status[$cidMerge])) {
                            $training_status[$cidMerge] = [];
                        }
                        foreach ($companyMerged as $k => $v) {
                            $training_status[$cidMerge][$k] = $v;
                        }
                    }
                }
            }

            // Prefetch existing rows for all companies in this save (avoid N+1 existence checks)
            $companyIdsInt = array_values(array_unique(array_map('intval', $company_ids)));
            $companyIdsIn = !empty($companyIdsInt) ? implode(',', $companyIdsInt) : '0';
            $existingMeetingSubtopics = []; // company_id => [subtopic_id => id]
            $existingSubtopicCompany = []; // company_id => [subtopic_id => [participant_id => row]]
            $existingBatchTraining = []; // company_id => [module_id => [participant_id => row]]
            $existingAttendByCompany = []; // company_id => training_attend_master_id
            if (!empty($companyIdsInt)) {
                $emsQ = $d->selectRow(
                    "id, company_id, subtopic_id",
                    "training_meeting_subtopics",
                    "slot_id='$slot_id' AND company_id IN ($companyIdsIn)"
                );
                while ($er = mysqli_fetch_assoc($emsQ)) {
                    $existingMeetingSubtopics[(int)$er['company_id']][(int)$er['subtopic_id']] = (int)$er['id'];
                }

                $escQ = $d->selectRow(
                    "id, company_id, subtopic_id, participant_id, subtopic_company_status, completed_at, completed_by",
                    "training_subtopic_company_status",
                    "company_id IN ($companyIdsIn)"
                );
                while ($er = mysqli_fetch_assoc($escQ)) {
                    $existingSubtopicCompany[(int)$er['company_id']][(int)$er['subtopic_id']][(int)$er['participant_id']] = $er;
                }

                $ebtQ = $d->selectRow(
                    "batch_training_status_id, company_id, module_id, participant_id, training_status, training_date",
                    "batch_training_status_master",
                    "company_id IN ($companyIdsIn)"
                );
                while ($er = mysqli_fetch_assoc($ebtQ)) {
                    $existingBatchTraining[(int)$er['company_id']][(int)$er['module_id']][(int)$er['participant_id']] = $er;
                }

                $eatQ = $d->selectRow(
                    "training_attend_master_id, society_id",
                    "training_attend_master",
                    "training_slot_id='$slot_id' AND society_id IN ($companyIdsIn) AND attend_type='1'"
                );
                while ($er = mysqli_fetch_assoc($eatQ)) {
                    $existingAttendByCompany[(int)$er['society_id']] = (int)$er['training_attend_master_id'];
                }
            }

            // Hoist total required modules once (same for every company)
            $totalModulesQuery = $d->selectRow(
                "COUNT(DISTINCT tmm.training_module_id) AS total_modules",
                "training_module_master tmm 
                JOIN training_module_priority_master tmpm ON tmm.module_priority = tmpm.priority_id",
                "tmm.module_type = 1 
                AND tmm.training_module_status = 0 
                AND tmpm.is_required = 1"
            );
            $totalModulesGlobal = 0;
            if ($row = mysqli_fetch_assoc($totalModulesQuery)) {
                $totalModulesGlobal = (int) $row['total_modules'];
            }

            $companiesNeedingProgressUpdate = [];
            foreach ($company_ids as $company_id) {
                $computedModuleStatuses = [];
                if (isset($training_status[$company_id]) && is_array($training_status[$company_id])) {
                    $statusItems = $training_status[$company_id];
                    if (isset($ts_module[$company_id]) && is_array($ts_module[$company_id])) {
                        foreach ($ts_module[$company_id] as $explicitModuleId => $explicitStatusVal) {
                            $explicitModuleId = (int)$explicitModuleId;
                            $explicitStatusVal = (int)$explicitStatusVal;
                            if ($explicitModuleId > 0) {
                                $computedModuleStatuses[$explicitModuleId] = $explicitStatusVal;
                            }
                        }
                    }

                    $subtopicToModule = [];
                    if (isset($ts_sub[$company_id]) && is_array($ts_sub[$company_id])) {
                        foreach ($ts_sub[$company_id] as $moduleKey => $subs) {
                            $moduleKey = (int)$moduleKey;
                            if (!is_array($subs)) {
                                continue;
                            }
                            foreach ($subs as $subtopicKey => $val) {
                                $subtopicToModule[(int)$subtopicKey] = $moduleKey;
                            }
                        }
                    }

                    $subtopicStatusesByModule = [];
                    foreach ($statusItems as $keyId => $statusVal) {
                        $keyId = (int)$keyId;
                        $statusVal = (int)$statusVal;
                        if (isset($subtopicToModule[$keyId])) {
                            $timestamp = ($statusVal === 1) ? date('Y-m-d H:i:s') : null;
                            $currentDateTime = date('Y-m-d H:i:s');

                            $existingId = $existingMeetingSubtopics[(int)$company_id][$keyId] ?? null;
                            if ($existingId) {
                                $updateData = [
                                    'is_completed' => $statusVal,
                                    'updated_at' => $currentDateTime,
                                    'updated_by' => $bms_admin_id
                                ];
                                if ($timestamp !== null) {
                                    $updateData['completed_at'] = $timestamp;
                                    $updateData['completed_by'] = $bms_admin_id;
                                }
                                $d->update("training_meeting_subtopics", $updateData, "id='" . $existingId . "'");
                            } else {
                                $insertData = [
                                    'slot_id' => $slot_id,
                                    'company_id' => $company_id,
                                    'subtopic_id' => $keyId,
                                    'is_completed' => $statusVal,
                                    'created_at' => $currentDateTime,
                                    'created_by' => $bms_admin_id,
                                    'updated_at' => $currentDateTime,
                                    'updated_by' => $bms_admin_id
                                ];
                                if ($timestamp !== null) {
                                    $insertData['completed_at'] = $timestamp;
                                    $insertData['completed_by'] = $bms_admin_id;
                                }
                                $d->insert("training_meeting_subtopics", $insertData);
                                // Keep map in sync for subsequent keys in same request
                                $newId = $d->getInsertId();
                                $existingMeetingSubtopics[(int)$company_id][$keyId] = $newId > 0 ? $newId : null;
                            }

                            if (isset($present_participants[$company_id]) && is_array($present_participants[$company_id])) {
                                foreach ($present_participants[$company_id] as $participant_id) {
                                    $rowCompany = $existingSubtopicCompany[(int)$company_id][$keyId][(int)$participant_id] ?? null;
                                    if ($rowCompany && !empty($rowCompany['id']) && $rowCompany['id'] !== true) {
                                        $prevStatus = isset($rowCompany['subtopic_company_status']) ? (int)$rowCompany['subtopic_company_status'] : 0;
                                        $effectiveStatus = merge_training_status_meeting($prevStatus, $statusVal);
                                        $updateCompanyData = [
                                            'subtopic_company_status' => $effectiveStatus,
                                            'updated_at' => $currentDateTime,
                                            'updated_by' => $bms_admin_id
                                        ];
                                        $prevWasDone = in_array($prevStatus, array(1, 2), true);
                                        $effectiveIsDone = in_array($effectiveStatus, array(1, 2), true);
                                        if ($effectiveIsDone && (empty($rowCompany['completed_at']) || !$prevWasDone || $effectiveStatus !== $prevStatus)) {
                                            $updateCompanyData['completed_at'] = $currentDateTime;
                                            $updateCompanyData['completed_by'] = $bms_admin_id;
                                        }
                                        $d->update("training_subtopic_company_status", $updateCompanyData, "id='" . $rowCompany['id'] . "'");
                                        $existingSubtopicCompany[(int)$company_id][$keyId][(int)$participant_id]['subtopic_company_status'] = $effectiveStatus;
                                    } else {
                                        $insertCompanyData = [
                                            'company_id' => $company_id,
                                            'subtopic_id' => $keyId,
                                            'participant_id' => $participant_id,
                                            'subtopic_company_status' => $statusVal,
                                            'created_at' => $currentDateTime,
                                            'created_by' => $bms_admin_id,
                                            'updated_at' => $currentDateTime,
                                            'updated_by' => $bms_admin_id
                                        ];
                                        if ($statusVal !== 0) {
                                            $insertCompanyData['completed_at'] = $currentDateTime;
                                            $insertCompanyData['completed_by'] = $bms_admin_id;
                                        }
                                        $d->insert("training_subtopic_company_status", $insertCompanyData);
                                        $newCompanyId = $d->getInsertId();
                                        $existingSubtopicCompany[(int)$company_id][$keyId][(int)$participant_id] = [
                                            'id' => $newCompanyId > 0 ? $newCompanyId : null,
                                            'subtopic_company_status' => $statusVal,
                                            'completed_at' => $statusVal !== 0 ? $currentDateTime : null
                                        ];
                                    }
                                }
                            }

                            $moduleForSub = $subtopicToModule[$keyId];
                            if (!isset($subtopicStatusesByModule[$moduleForSub])) {
                                $subtopicStatusesByModule[$moduleForSub] = [];
                            }
                            $subtopicStatusesByModule[$moduleForSub][] = $statusVal;
                        } else {
                            $computedModuleStatuses[$keyId] = $statusVal;
                        }
                    }
                    foreach ($subtopicStatusesByModule as $moduleIdAgg => $statuses) {
                        if (!array_key_exists($moduleIdAgg, $computedModuleStatuses)) {
                            if (in_array(0, $statuses, true)) {
                                $computedModuleStatuses[$moduleIdAgg] = 0;
                                continue;
                            }
                            $allNA = !empty($statuses) && count(array_unique($statuses)) === 1 && $statuses[0] === 2;
                            if ($allNA) {
                                $computedModuleStatuses[$moduleIdAgg] = 2;
                                continue;
                            }
                            $computedModuleStatuses[$moduleIdAgg] = 1;
                        }
                    }
                }
                if (!empty($computedModuleStatuses)) {
                    foreach ($computedModuleStatuses as $module_id => $status) {
                        if (empty($module_id) || !is_numeric($module_id) || (int)$module_id <= 0) {
                            continue;
                        }

                        $created_by = $bms_admin_id;
                        $training_date = date('Y-m-d H:i:s');
                        $created_date = date('Y-m-d H:i:s');
                        $module_remark = '';
                        if (isset($_POST['module_remark'][$company_id][$module_id]) && !empty(trim($_POST['module_remark'][$company_id][$module_id]))) {
                            $module_remark = trim($_POST['module_remark'][$company_id][$module_id]);
                        }
                        $m->set_data('slot_id', $slot_id);
                        $m->set_data('company_id', $company_id);
                        $m->set_data('module_id', $module_id);
                        $m->set_data('status', $status);
                        $m->set_data('training_date', $training_date);
                        $m->set_data('trainer_id', $trainer_id);
                        $m->set_data('created_date', $created_date);
                        $m->set_data('created_by', $created_by);
                        $data = array(
                            'slot_id' => $m->get_data('slot_id') ?: $slot_id,
                            'company_id' => $m->get_data('company_id') ?: $company_id,
                            'module_id' => $m->get_data('module_id') ?: $module_id,
                            'training_status' => $m->get_data('status') ?: $status,
                            'training_date' => $m->get_data('training_date') ?: date('Y-m-d H:i:s'),
                            'trainer_id' => $m->get_data('trainer_id') ?: $trainer_id,
                            'created_date' => $m->get_data('created_date') ?: date('Y-m-d H:i:s'),
                            'created_by' => $m->get_data('created_by') ?: $bms_admin_id
                        );
                        if (!empty($module_remark)) {
                            $data['module_remark'] = $module_remark;
                        }
                        $insert = $d->insert("batch_training_status", $data);
                    }
                }

                if (isset($present_participants[$company_id]) && is_array($present_participants[$company_id]) && !empty($computedModuleStatuses)) {
                    $joined_participants = $present_participants[$company_id];
                    foreach ($joined_participants as $joined_participant) {
                        foreach ($computedModuleStatuses as $module_id => $status) {
                            if (empty($module_id) || !is_numeric($module_id) || (int)$module_id <= 0) {
                                continue;
                            }

                            $check_exist_data = $existingBatchTraining[(int)$company_id][(int)$module_id][(int)$joined_participant] ?? null;
                            if ($check_exist_data && !empty($check_exist_data['batch_training_status_id']) && $check_exist_data['batch_training_status_id'] !== true) {
                                $batch_training_status_id = $check_exist_data['batch_training_status_id'];
                                $prevModuleStatus = isset($check_exist_data['training_status']) ? (int)$check_exist_data['training_status'] : 0;
                                $effectiveModuleStatus = merge_training_status_meeting($prevModuleStatus, (int) $status);
                                $batch_training_array = array(
                                    'training_status' => $effectiveModuleStatus,
                                    'added_date' => date('Y-m-d H:i:s'),
                                    'added_by' => $bms_admin_id
                                );
                                $prevDate = isset($check_exist_data['training_date']) ? trim($check_exist_data['training_date']) : '';
                                if ($effectiveModuleStatus !== $prevModuleStatus || $prevDate === '' || $prevDate === '0000-00-00 00:00:00') {
                                    $batch_training_array['training_date'] = date('Y-m-d H:i:s');
                                }
                                $d->update("batch_training_status_master", $batch_training_array, "batch_training_status_id='$batch_training_status_id'");
                                $existingBatchTraining[(int)$company_id][(int)$module_id][(int)$joined_participant]['training_status'] = $effectiveModuleStatus;
                            } else {
                                $batch_training_array = array(
                                    'company_id' => $company_id,
                                    'module_id' => $module_id,
                                    'training_status' => $status,
                                    'participant_id' => $joined_participant,
                                    'training_date' => date('Y-m-d H:i:s'),
                                    'added_date' => date('Y-m-d H:i:s'),
                                    'added_by' => $bms_admin_id
                                );
                                $insert = $d->insert("batch_training_status_master", $batch_training_array);
                                $newBtsId = $d->getInsertId();
                                $existingBatchTraining[(int)$company_id][(int)$module_id][(int)$joined_participant] = [
                                    'batch_training_status_id' => $newBtsId > 0 ? $newBtsId : null,
                                    'training_status' => $status,
                                    'training_date' => date('Y-m-d H:i:s')
                                ];
                            }
                        }
                    }
                }

                if (isset($_POST["attendance_$company_id"]) && $_POST["attendance_$company_id"] != '') {
                    $attendance = isset($_POST["attendance_$company_id"]) && $_POST["attendance_$company_id"] == 'present' ? 1 : 0;
                    $reason = $attendance == 0 ? (($_POST["reason_$company_id"] ?? '')) : '';
                    $m->set_data('training_slot_id', $slot_id);
                    $m->set_data('society_id', $company_id);
                    $m->set_data('attendance', $attendance);
                    $m->set_data('reason', $reason);

                    $dataAttend = array(
                        'training_slot_id' => $m->get_data('training_slot_id') ?: $slot_id,
                        'society_id' => $m->get_data('society_id') ?: $company_id,
                        'absent_present' => $m->get_data('attendance') ?: $attendance,
                        'absent_reason' => $m->get_data('reason') ?: $reason,
                        'attend_type' => '1',
                    );
                    $existingAttendId = $existingAttendByCompany[(int)$company_id] ?? null;
                    if ($existingAttendId && $existingAttendId !== true) {
                        $d->update(
                            'training_attend_master',
                            [
                                'absent_present' => $dataAttend['absent_present'],
                                'absent_reason' => $dataAttend['absent_reason']
                            ],
                            "training_attend_master_id='" . $existingAttendId . "'"
                        );
                    } else {
                        $d->insert('training_attend_master', $dataAttend);
                        $newAttendId = $d->getInsertId();
                        $existingAttendByCompany[(int)$company_id] = $newAttendId > 0 ? $newAttendId : null;
                    }
                }
                if (isset($participant_Name[$company_id]) && is_array($participant_Name[$company_id])) {
                    foreach ($participant_Name[$company_id] as $key => $name) {
                        $participant_type = isset($participant_Type[$company_id][$key]) ? $participant_Type[$company_id][$key] : '';
                        $participant_designation = isset($participant_Designation[$company_id][$key]) ? $participant_Designation[$company_id][$key] : '';
                        $participant_contact = isset($participant_Contact[$company_id][$key]) ? $participant_Contact[$company_id][$key] : '';
                        $no_of_participants = isset($no_of_participant[$company_id][$key]) ? $no_of_participant[$company_id][$key] : '';
                        $remark = isset($Remark[$company_id][$key]) ? $Remark[$company_id][$key] : '';

                        $m->set_data('company_id', $company_id);
                        $m->set_data('batch_id', $batch_id);
                        $m->set_data('slot_id', $slot_id);
                        $m->set_data('participant_name', $name);
                        $m->set_data('participant_type', $participant_type);
                        $m->set_data('participant_designation', $participant_designation);
                        $m->set_data('no_of_participants', $no_of_participants);
                        $m->set_data('participant_contact', $participant_contact);
                        $m->set_data('remark', $remark);

                        $participant_data = array(
                            'company_id' => $m->get_data('company_id') ?: $company_id,
                            'batch_id' => $m->get_data('batch_id') ?: $batch_id,
                            'slot_id' => $m->get_data('slot_id') ?: $slot_id,
                            'participant_name' => $m->get_data('participant_name') ?: $name,
                            'participant_type' => $m->get_data('participant_type') ?: $participant_type,
                            'participant_designation' => $m->get_data('participant_designation') ?: $participant_designation,
                            'no_of_participants' => $m->get_data('no_of_participants') ?: $no_of_participants,
                            'participant_contact' => $m->get_data('participant_contact') ?: $participant_contact,
                            'remark' => $m->get_data('remark') ?: $remark,
                            'added_date' => date('Y-m-d H:i:s'),
                            'added_by' => $bms_admin_id
                        );
                        $insert_participant = $d->insert("training_participant_master", $participant_data);
                    }
                }
                // UPDATE SOCIETY DATA  (New FOR single Participants)
                // Defer progress recalc when totalModules > 0 so we can batch across companies
                $totalModules = $totalModulesGlobal;

                if ($totalModules === 0) {
                    $participantData = [];
                    $jsonData = json_encode($participantData, JSON_PRETTY_PRINT);

                    $updateData = [
                        'training_percentage' => 0,
                        'training_status' => 1,
                        'training_data' => $jsonData,
                    ];
                    $d->update('society_master', $updateData, "society_id='$company_id'");
                } else {
                    $companiesNeedingProgressUpdate[] = (int)$company_id;
                }         
            }

            // Batch society training progress for companies that still need recalc
            if (!empty($companiesNeedingProgressUpdate) && $totalModulesGlobal > 0) {
                $progressCompanyIds = array_values(array_unique(array_map('intval', $companiesNeedingProgressUpdate)));
                $progressCompanyIdsIn = implode(',', $progressCompanyIds);

                $participantDataByCompany = [];
                $completedModulesQuery = $d->selectRow(
                    "sm.society_id AS company_id, tp.participants_type_id AS participant_id, tp.participant_name AS participant_name,  COUNT(DISTINCT CASE WHEN btsm.training_status IN (1, 2) THEN tmm.training_module_id  END) AS completed_modules,  COUNT(DISTINCT tmm.training_module_id) AS total_modules",
                    "society_master sm CROSS JOIN training_participants_type tp LEFT JOIN training_module_topics tmt  ON tmt.participant_type = tp.participants_type_id AND tmt.topic_type = '0' LEFT JOIN training_module_master tmm  ON tmm.topic_id = tmt.topic_id AND tmm.training_module_status = 0 AND tmm.module_type = 1 LEFT JOIN training_module_priority_master tmpm  ON tmpm.priority_id = tmm.module_priority AND tmpm.is_required = 1 LEFT JOIN batch_training_status_master btsm  ON btsm.company_id = sm.society_id AND btsm.participant_id = tp.participants_type_id AND btsm.module_id = tmm.training_module_id",
                    "sm.society_id IN ($progressCompanyIdsIn) AND tmpm.is_required = 1 GROUP BY sm.society_id, tp.participants_type_id"
                );
                while ($row = mysqli_fetch_assoc($completedModulesQuery)) {
                    $cid = (int)$row['company_id'];
                    $participantDataByCompany[$cid][] = [
                        'participant_id' => $row['participant_id'],
                        'participant_name' => $row['participant_name'],
                        'completed_percentage' => (int)$row['completed_modules'],
                        'total_participant_modules' => (int)$row['total_modules'],
                    ];
                }

                $modulesCompletedByCompany = [];
                $modulesCompletedQuery = $d->selectRow(
                    "company_id, COUNT(*) AS modules_completed_by_all",
                    "(
                        SELECT btsm.company_id, btsm.module_id
                        FROM batch_training_status_master btsm
                        INNER JOIN training_module_master tmm
                            ON btsm.module_id = tmm.training_module_id
                        INNER JOIN training_module_priority_master tmpm
                            ON tmm.module_priority = tmpm.priority_id
                        WHERE 
                            btsm.company_id IN ($progressCompanyIdsIn)
                            AND btsm.training_status IN (1, 2) AND btsm.training_status != 3
                            AND tmpm.is_required = 1
                        GROUP BY btsm.company_id, btsm.module_id
                    ) AS completed",
                    "",
                    "GROUP BY company_id"
                );
                while ($row = mysqli_fetch_assoc($modulesCompletedQuery)) {
                    $modulesCompletedByCompany[(int)$row['company_id']] = (int)$row['modules_completed_by_all'];
                }

                foreach ($progressCompanyIds as $cid) {
                    $participantData = $participantDataByCompany[$cid] ?? [];
                    $jsonData = json_encode($participantData, JSON_PRETTY_PRINT);
                    $trainingPercentage = $modulesCompletedByCompany[$cid] ?? 0;
                    $trainingStatus = ($trainingPercentage == $totalModulesGlobal) ? 1 : 0;
                    $updateData = [
                        'training_percentage' => $trainingPercentage,
                        'training_status' => $trainingStatus,
                        'training_data' => $jsonData,
                    ];
                    if ($trainingStatus == 1) {
                        $updateData['training_completion_date'] = date('Y-m-d H:i:s');
                    }
                    $d->update('society_master', $updateData, "society_id='$cid'");
                }
            }

            $m->set_data('recording_link', $recording_link);
            $m->set_data('training_password', $training_password);
            $m->set_data('mom_attachment', $mom_attachment);

            $slot_data = array(
                'meeting_status' => "1",
                'recording_link' => $m->get_data('recording_link'),
                'training_password' => $m->get_data('training_password'),
                'mom_attachment' => $m->get_data('mom_attachment'),
            );

            $insert = $d->update("batch_slot_master", $slot_data, "slot_id='$slot_id'");
            $_SESSION['msg'] = "Meeting successfully completed.";
            header("Location:../manageTrainingSlots");
            exit;
        } else {
            $_SESSION['msg1'] = "Something Went Wrong.";
            header("Location:../manageTrainingSlots");
            exit;
        }
    }
}
