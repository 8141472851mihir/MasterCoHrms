<?php
include '../common/objectController.php';
if (isset($_POST) && !empty($_POST)) {
    if (isset($setupAttendanceAdd) && $setupAttendanceAdd == 'setupAttendanceAdd') {
        $training_schedule_master_id = isset($_POST['training_schedule_master_id']) ? $d->sanitizeActionIdAsInt($_POST['training_schedule_master_id']) : 0;
        $session_id = isset($_POST['session_id']) ? $d->sanitizeActionIdAsInt($_POST['session_id']) : 0;

        if (!$training_schedule_master_id || !$session_id || !isset($_POST['society_id'])) {
            $_SESSION['error'] = "Missing required parameters.";
            header("Location: ../manageTraining");
            exit();
        }

        $society_ids = $d->sanitizeActionIds($_POST['society_id'] ?? []);
        $meetingGroups_new_meeting = [];
        foreach ($society_ids as $society_id_new_meeting) {
            if (
                isset($next_meeting_id_[$society_id_new_meeting]) &&
                $next_meeting_id_[$society_id_new_meeting] != "" &&
                $next_meeting_session_id_[$society_id_new_meeting] != "" &&
                $next_meeting_session_date_[$society_id_new_meeting] != ""
            ) {
                $session_id_new_meeting = $next_meeting_session_id_[$society_id_new_meeting];
                $session_date_new_meeting = $next_meeting_session_date_[$society_id_new_meeting];
                $groupKey_new_meeting = $session_id_new_meeting . '_' . date('Y-m-d', strtotime($session_date_new_meeting));

                if (!isset($meetingGroups_new_meeting[$groupKey_new_meeting])) {
                    $meetingGroups_new_meeting[$groupKey_new_meeting] = [
                        'session_id' => $session_id_new_meeting,
                        'session_date' => $session_date_new_meeting,
                        'societies' => [],
                        'is_new' => ($next_meeting_id_[$society_id_new_meeting] == "new_meeting"),
                        'next_meeting_id' => $next_meeting_id_[$society_id_new_meeting]
                    ];
                }

                $meetingGroups_new_meeting[$groupKey_new_meeting]['societies'][] = $society_id_new_meeting;
            }
        }

        // Prefetch sessions + existing schedules for all meeting groups
        $sessionIdsForGroups = [];
        $scheduleIdsForGroups = [];
        foreach ($meetingGroups_new_meeting as $meetingGroup_new_meeting) {
            $sessionIdsForGroups[] = (int)$meetingGroup_new_meeting['session_id'];
            if (!$meetingGroup_new_meeting['is_new'] && !empty($meetingGroup_new_meeting['next_meeting_id']) && $meetingGroup_new_meeting['next_meeting_id'] !== 'new_meeting') {
                $scheduleIdsForGroups[] = (int)$meetingGroup_new_meeting['next_meeting_id'];
            }
        }
        $sessionsById = [];
        $sessionIdsForGroups = array_values(array_unique(array_filter($sessionIdsForGroups)));
        if (!empty($sessionIdsForGroups)) {
            $sessIn = implode(',', $sessionIdsForGroups);
            $sessQ = $d->select("session_master", "session_id IN ($sessIn)");
            while ($sd = mysqli_fetch_array($sessQ)) {
                $sessionsById[(int)$sd['session_id']] = $sd;
            }
        }
        $schedulesById = [];
        $scheduleIdsForGroups = array_values(array_unique(array_filter($scheduleIdsForGroups)));
        if (!empty($scheduleIdsForGroups)) {
            $schIn = implode(',', $scheduleIdsForGroups);
            $schQ = $d->selectRow(
                "training_schedule_master_id, society_id",
                "training_schedule_master",
                "training_schedule_master_id IN ($schIn)"
            );
            while ($sr = mysqli_fetch_assoc($schQ)) {
                $schedulesById[(int)$sr['training_schedule_master_id']] = $sr;
            }
        }

        foreach ($meetingGroups_new_meeting as $groupIndex_new_meeting => $meetingGroup_new_meeting) {
            $sessionId_new_meeting = $meetingGroup_new_meeting['session_id'];
            $sessionDate_new_meeting = $meetingGroup_new_meeting['session_date'];
            $societyIds_new_meeting = $meetingGroup_new_meeting['societies'];
            $isNewGroup_new_meeting = $meetingGroup_new_meeting['is_new'];
            $nextMeetingId_new_meeting = $meetingGroup_new_meeting['next_meeting_id'];

            $sessionDetails_new_meeting = $sessionsById[(int)$sessionId_new_meeting] ?? null;
            if ($sessionDetails_new_meeting) {
                $sessionDayId_new_meeting = $sessionDetails_new_meeting["session_day_id"];
                $startTime_new_meeting = $sessionDetails_new_meeting["start_time"];
                $sessionName_new_meeting = $sessionDetails_new_meeting['session_name'];
                $endTime_new_meeting = $sessionDetails_new_meeting["end_time"];

                $dayAbbr_new_meeting = date('D', strtotime($sessionDate_new_meeting));
                $formattedDate_new_meeting = date('Y_m_d', strtotime($sessionDate_new_meeting));
                $meetingName_new_meeting = $sessionName_new_meeting . "_" . $dayAbbr_new_meeting . "_" . $formattedDate_new_meeting;

                $trainingScheduleId_new_meeting = null;

                if (!$isNewGroup_new_meeting && $nextMeetingId_new_meeting) {
                    $existingRow_new_meeting = $schedulesById[(int)$nextMeetingId_new_meeting] ?? null;

                    if ($existingRow_new_meeting) {
                        $existingSocietyIds_new_meeting = explode(",", $existingRow_new_meeting['society_id']);
                        $mergedSocietyIds_new_meeting = array_unique(array_merge($existingSocietyIds_new_meeting, $societyIds_new_meeting));
                        $mergedSocietyIdsStr_new_meeting = implode(",", $mergedSocietyIds_new_meeting);

                        $updateData_new_meeting = ["society_id" => $mergedSocietyIdsStr_new_meeting];
                        $d->update(
                            "training_schedule_master",
                            $updateData_new_meeting,
                            "training_schedule_master_id = $nextMeetingId_new_meeting"
                        );

                        $trainingScheduleId_new_meeting = $nextMeetingId_new_meeting;
                        $schedulesById[(int)$nextMeetingId_new_meeting]['society_id'] = $mergedSocietyIdsStr_new_meeting;
                    }
                } else {
                    $insertData_new_meeting = array(
                        "session_id" => $sessionId_new_meeting,
                        "training_date" => date('Y-m-d', strtotime($sessionDate_new_meeting)),
                        "society_id" => implode(",", $societyIds_new_meeting),
                        "host_id" => $bms_admin_id,
                        "session_day_id" => $sessionDayId_new_meeting,
                        "start_time" => $startTime_new_meeting,
                        "end_time" => $endTime_new_meeting,
                        "setup_meeting_name" => $meetingName_new_meeting,
                        "created_date" => date("Y-m-d H:i:s"),
                        "created_by" => $bms_admin_id
                    );
                    $trainingScheduleId_new_meeting = $d->insert("training_schedule_master", $insertData_new_meeting);
                }
            }
        }


        $society_ids = $d->sanitizeActionIds($_POST['society_id'] ?? []);

        // Prefetch existing status rows for all societies in this save
        $societyIdsIn = !empty($society_ids) ? implode(',', array_map('intval', $society_ids)) : '0';
        $existingTrainingStatus = []; // society_id => [module_id => row]
        $existingModuleStatusById = []; // module_training_status_id => row
        $existingModuleStatusByCompanyModule = []; // company_id => [module_id => row]
        if (!empty($society_ids)) {
            $tsQ = $d->select(
                "training_status_master",
                "session_id='$session_id' AND training_schedule_master_id='$training_schedule_master_id' AND society_id IN ($societyIdsIn)"
            );
            while ($tr = mysqli_fetch_assoc($tsQ)) {
                $existingTrainingStatus[(int)$tr['society_id']][(int)$tr['training_module_id']] = $tr;
            }

            $mtsQ = $d->select(
                "module_training_status_master",
                "company_id IN ($societyIdsIn)"
            );
            while ($mr = mysqli_fetch_assoc($mtsQ)) {
                $existingModuleStatusById[(int)$mr['module_training_status_id']] = $mr;
                $existingModuleStatusByCompanyModule[(int)$mr['company_id']][(int)$mr['module_id']] = $mr;
            }
        }

        // Hoist total required setup modules once (same for every society)
        $totalModulesQuery = $d->selectRow(
            "COUNT(*) AS total_modules",
            "training_module_master tmm JOIN training_module_priority_master tmpm ON tmm.module_priority = tmpm.priority_id",
            "tmm.module_type = 0 
            AND tmm.training_module_status = 0 
            AND tmpm.is_required = 1"
        );
        $totalModulesGlobal = 0;
        if ($row = mysqli_fetch_assoc($totalModulesQuery)) {
            $totalModulesGlobal = (int) $row['total_modules'];
        }

        $societiesNeedingSetupUpdate = [];

        foreach ($society_ids as $society_id) {
            $attendance = isset($_POST["attendance_$society_id"]) && $_POST["attendance_$society_id"] === 'present' ? 1 : 0;
            $reason = $attendance === 0 ? ($_POST["reason_$society_id"] ?? null) : null;

            $m->set_data('training_schedule_master_id', $training_schedule_master_id);
            $m->set_data('society_id', $society_id);
            $m->set_data('attendance', $attendance);
            $m->set_data('reason', $reason);


            $data = array(
                'training_schedule_master_id' => $m->get_data('training_schedule_master_id'),
                'society_id' => $m->get_data('society_id'),
                'absent_present' => $m->get_data('attendance'),
                'absent_reason' => $m->get_data('reason'),
                'attend_type' => '0',
            );

            $d->insert('training_attend_master', $data);

            if ($attendance === 1) {
                foreach ($company_module_ids[$society_id] as $moduleId) {

                    $trainingStatus = $_POST["user_training_status_{$society_id}_{$moduleId}"] ?? null;
                    $dataStatus = $_POST["user_data_status_{$society_id}_{$moduleId}"] ?? null;
                    $onboardingStatus = $_POST["user_onboarding_status_{$society_id}_{$moduleId}"] ?? null;

                    $currentDateTime = date('Y-m-d H:i:s');

                    $m->set_data('session_id', $session_id);
                    $m->set_data('training_schedule_master_id', $training_schedule_master_id);
                    $m->set_data('training_module_id', $moduleId);
                    $m->set_data('society_id', $society_id);
                    $m->set_data('training_status', $trainingStatus);
                    $m->set_data('training_date', $trainingStatus ? $currentDateTime : null);
                    $m->set_data('data_receive_status', $dataStatus);
                    $m->set_data('data_receive_date', $dataStatus ? $currentDateTime : null);
                    $m->set_data('onboarding_status', $onboardingStatus);
                    $m->set_data('onboarding_date', $onboardingStatus ? $currentDateTime : null);

                    $statusData = array(
                        'session_id' => $m->get_data('session_id'),
                        'training_schedule_master_id' => $m->get_data('training_schedule_master_id'),
                        'training_module_id' => $m->get_data('training_module_id'),
                        'society_id' => $m->get_data('society_id'),
                        'training_status' => $m->get_data('training_status'),
                        'training_date' => $m->get_data('training_date'),
                        'data_receive_status' => $m->get_data('data_receive_status'),
                        'data_receive_date' => $m->get_data('data_receive_date'),
                        'onboarding_status' => $m->get_data('onboarding_status'),
                        'onboarding_date' => $m->get_data('onboarding_date')
                    );

                    $row = $existingTrainingStatus[(int)$society_id][(int)$moduleId] ?? null;

                    if ($row) {
                        $updateData = [];
                        if ($trainingStatus !== null && $trainingStatus != $row['training_status']) {
                            $updateData['training_status'] = $trainingStatus;
                            $updateData['training_date'] = $currentDateTime;
                        }

                        if ($dataStatus !== null && $dataStatus != $row['data_receive_status']) {
                            $updateData['data_receive_status'] = $dataStatus;
                            $updateData['data_receive_date'] = $currentDateTime;
                        }

                        if ($onboardingStatus !== null && $onboardingStatus != $row['onboarding_status']) {
                            $updateData['onboarding_status'] = $onboardingStatus;
                            $updateData['onboarding_date'] = $currentDateTime;
                        }

                        if (!empty($updateData)) {
                            $d->update(
                                'training_status_master',
                                $updateData,
                                "session_id='$session_id' AND training_schedule_master_id='$training_schedule_master_id' AND society_id='$society_id' AND training_module_id='$moduleId'"
                            );
                        }
                    } else {
                        $d->insert('training_status_master', $statusData);
                        $existingTrainingStatus[(int)$society_id][(int)$moduleId] = $statusData;
                    }

                    $trainingStatus = $_POST["user_training_status_{$society_id}_{$moduleId}"] ?? null;
                    $dataStatus = $_POST["user_data_status_{$society_id}_{$moduleId}"] ?? null;
                    $onboardingStatus = $_POST["user_onboarding_status_{$society_id}_{$moduleId}"] ?? null;
                    $society_training_module_status_id = $_POST["training_module_status_{$society_id}_{$moduleId}"] ?? null;

                    $m->set_data('module_id', $moduleId);
                    $m->set_data('company_id', $society_id);
                    $m->set_data('training_status', $trainingStatus);
                    $m->set_data('training_date', $trainingStatus ? $currentDateTime : null);
                    $m->set_data('data_receive_status', $dataStatus);
                    $m->set_data('data_receive_date', $dataStatus ? $currentDateTime : null);
                    $m->set_data('onboarding_status', $onboardingStatus);
                    $m->set_data('onboarding_date', $onboardingStatus ? $currentDateTime : null);
                    $training_data = array(
                        'module_id' => $m->get_data('training_module_id'),
                        'company_id' => $m->get_data('company_id'),
                        'training_status' => $m->get_data('training_status'),
                        'training_date' => $m->get_data('training_date'),
                        'data_receive_status' => $m->get_data('data_receive_status'),
                        'data_receive_date' => $m->get_data('data_receive_date'),
                        'onboarding_status' => $m->get_data('onboarding_status'),
                        'onboarding_date' => $m->get_data('onboarding_date')
                    );

                    $row = null;
                    if (isset($society_training_module_status_id) && $society_training_module_status_id != '') {
                        $row = $existingModuleStatusById[(int)$society_training_module_status_id] ?? null;
                    }
                    if (!$row) {
                        $row = $existingModuleStatusByCompanyModule[(int)$society_id][(int)$moduleId] ?? null;
                    }

                    if ($row) {
                        $statusPk = !empty($society_training_module_status_id)
                            ? $society_training_module_status_id
                            : ($row['module_training_status_id'] ?? null);
                        if ($statusPk !== null && $statusPk !== '') {
                            $updateData = [];
                            if ($trainingStatus !== null && $trainingStatus != $row['training_status']) {
                                $updateData['training_status'] = $trainingStatus;
                                $updateData['training_date'] = $currentDateTime;
                            }

                            if ($dataStatus !== null && $dataStatus != $row['data_receive_status']) {
                                $updateData['data_receive_status'] = $dataStatus;
                                $updateData['data_receive_date'] = $currentDateTime;
                            }

                            if ($onboardingStatus !== null && $onboardingStatus != $row['onboarding_status']) {
                                $updateData['onboarding_status'] = $onboardingStatus;
                                $updateData['onboarding_date'] = $currentDateTime;
                            }

                            if (!empty($updateData)) {
                                $d->update(
                                    'module_training_status_master',
                                    $updateData,
                                    "module_training_status_id='$statusPk'"
                                );
                            }
                        }
                    } else {
                        $training_data['added_date'] = $currentDateTime;
                        $training_data['added_by'] = $bms_admin_id;
                        $query = $d->insert("module_training_status_master", $training_data);
                    }
                }
            }
            $societiesNeedingSetupUpdate[] = (int)$society_id;
        }

        // Batch society setup aggregates for all societies (one query, GROUP BY company_id)
        if (!empty($societiesNeedingSetupUpdate)) {
            $setupSocietyIdsIn = implode(',', array_map('intval', array_unique($societiesNeedingSetupUpdate)));
            $totalModules = $totalModulesGlobal;
            $totalSessionModulesQuery = $d->selectRow(
                "sm.society_id AS company_id,sdm.session_day_id,sdm.session_day_name,MAX(mtm.training_date) AS training_date,MAX(mtm.data_receive_date) AS data_receive_date,MAX(mtm.onboarding_date) AS onboarding_date,COUNT(DISTINCT CASE WHEN tmm.module_type = 0 AND tmm.training_module_status = 0 AND tmpm.is_required = 1 THEN tmm.training_module_id END) AS module_count, COUNT(DISTINCT CASE WHEN tmm.module_type = 0 AND tmm.training_module_status = 0 AND tmpm.is_required = 1 AND mtm.company_id = sm.society_id AND mtm.training_status IN (1, 2) AND mtm.training_status != 3 THEN tmm.training_module_id END) AS training_completed, COUNT(DISTINCT CASE WHEN tmm.module_type = 0 AND tmm.training_module_status = 0 AND tmpm.is_required = 1 AND mtm.company_id = sm.society_id AND mtm.data_receive_status IN (1, 2) THEN tmm.training_module_id  END) AS data_receive_completed, COUNT(DISTINCT CASE  WHEN tmm.module_type = 0 AND tmm.training_module_status = 0 AND tmpm.is_required = 1  AND mtm.company_id = sm.society_id AND mtm.onboarding_status IN (1, 2) THEN tmm.training_module_id  END ) AS onboarding_completed",
                "society_master sm CROSS JOIN session_day_master sdm LEFT JOIN training_module_master tmm  ON tmm.session_day_id = sdm.session_day_id LEFT JOIN training_module_priority_master tmpm ON tmm.module_priority = tmpm.priority_id LEFT JOIN module_training_status_master mtm  ON mtm.module_id = tmm.training_module_id",
                "sm.society_id IN ($setupSocietyIdsIn) AND sdm.session_day_status = 0",
                "GROUP BY sm.society_id, sdm.session_day_id, sdm.session_day_name ORDER BY sm.society_id ASC, sdm.session_day_id ASC"
            );
            $sessionModulesByCompany = [];
            while ($row = mysqli_fetch_array($totalSessionModulesQuery)) {
                $cid = (int)$row['company_id'];
                $totalSessionData = [];
                $totalSessionData['session_day_id'] = $row['session_day_id'];
                $totalSessionData['session_day_name'] = $row['session_day_name'];
                $totalSessionData['module_count'] = (int) $row['module_count'];
                $totalSessionData['training_completed'] = (int) $row['training_completed'];
                $totalSessionData['data_receive_completed'] = (int) $row['data_receive_completed'];
                $totalSessionData['onboarding_completed'] = (int) $row['onboarding_completed'];
                $totalSessionData['module_count'] = (int) $row['module_count'];
                $totalSessionData['total_modules'] = (int) $totalModules;
                $totalSessionData['last_training_date'] = $row['training_date'];
                $totalSessionData['last_data_receive_date'] = $row['data_receive_date'];
                $totalSessionData['last_onboarding_date'] = $row['onboarding_date'];
                $sessionModulesByCompany[$cid][] = $totalSessionData;
            }

            foreach (array_unique($societiesNeedingSetupUpdate) as $society_id) {
                $totalSessionModules = $sessionModulesByCompany[(int)$society_id] ?? [];
                $totalTrainingCompleted = 0;
                $totalDataReceiveCompleted = 0;
                $totalOnboardingCompleted = 0;

                foreach ($totalSessionModules as $session) {
                    $totalTrainingCompleted += $session['training_completed'];
                    $totalDataReceiveCompleted += $session['data_receive_completed'];
                    $totalOnboardingCompleted += $session['onboarding_completed'];
                }

                $trainingStatus = $totalTrainingCompleted == $totalModules ? 1 : 0;
                $dataReceiveStatus = $totalDataReceiveCompleted == $totalModules ? 1 : 0;
                $onboardingStatus = $totalOnboardingCompleted == $totalModules ? 1 : 0;

                $setup_data = json_encode($totalSessionModules, JSON_PRETTY_PRINT);
                $updateData = array(
                    'setup_training_percentage' => $totalTrainingCompleted,
                    'setup_data_receive_percentage' => $totalDataReceiveCompleted,
                    'setup_onboarding_percentage' => $totalOnboardingCompleted,
                    'setup_training_status' => $trainingStatus,
                    'setup_data_receive_status' => $dataReceiveStatus,
                    'setup_onboarding_status' => $onboardingStatus,
                    'setup_data' => $setup_data
                );
                $d->update('society_master', $updateData, "society_id='$society_id'");
            }
        }
        $m->set_data('meeting_status', '1');
        $m->set_data('training_password', $training_password);
        $m->set_data('training_link', $training_link);
        $data = array(
            'training_link' => $m->get_data('training_link'),
            'training_password' => $m->get_data('training_password'),
            'meeting_status' => $m->get_data('meeting_status'),
        );

        // print_r($data);exit;
        $d->update('training_schedule_master', $data, "training_schedule_master_id='$training_schedule_master_id'");

        $_SESSION['msg'] = "Training data saved successfully.";
        header("Location: ../manageTraining");
        exit();
    } else if (isset($setupSocietyAdd) && $setupSocietyAdd == 'setupSocietyAdd') {

        if (isset($training_schedule_master_id) && $training_schedule_master_id != '') {
            $training_schedule_master_id = $_POST['training_schedule_master_id'];
            $society_ids = $d->sanitizeActionIds($_POST['society_id'] ?? []);

            if (!empty($society_ids)) {
                $society_ids_str = implode(',', $society_ids);
                $d->update("training_schedule_master", [
                    "society_id" => $society_ids_str
                ], "training_schedule_master_id = $training_schedule_master_id");

                $_SESSION['msg'] = "Scheduled Company Updated Successfully";
                header("Location: ../manageAttendanceStatus?training_schedule_master_id=$training_schedule_master_id&csrf=$csrf");
                exit();
            } else {
                $_SESSION['msg1'] = "Something went wrong";
                header("Location: ../manageAttendanceStatus?training_schedule_master_id=$training_schedule_master_id&csrf=$csrf");
                exit();
            }
        } else {
            $_SESSION['msg1'] = "Something went wrong";
            header("Location: ../manageTraining");
            exit();
        }
    } else if (isset($_POST['getSetupStatus']) && !empty($_POST['getSetupStatus'])) {
        $company_id = $_POST['company_id'];
        $response = [];

        $query = $d->selectRow(
            "training_module_name, training_module_id, module_training_status_master.*",
            "training_module_master 
        LEFT JOIN module_training_status_master ON module_training_status_master.module_id=training_module_master.training_module_id 
        AND module_training_status_master.company_id='$company_id'",
            "training_module_master.module_type = 0 AND training_module_master.training_module_status=0"
        );

        $message = "";
        while ($module_name = mysqli_fetch_array($query)) {
            $message .= "<tr>
        <td>" . htmlspecialchars($module_name['training_module_name']) . "</td>
        <td>
        <input type='radio' id='user_data_status_" . htmlspecialchars($module_name['training_module_id']) . "_1'
        name='user_data_status_" . htmlspecialchars($module_name['training_module_id']) . "'
        value='1' " . ($module_name['data_receive_status'] == "1" ? "checked" : "") . ">
        </td>
        <td>
        <input type='radio' id='user_data_status_" . htmlspecialchars($module_name['training_module_id']) . "_0'
        name='user_data_status_" . htmlspecialchars($module_name['training_module_id']) . "'
        value='0' " . ($module_name['data_receive_status'] == "0" ? "checked" : "") . ">
        </td>
        <td>
        <input type='radio' id='user_data_status_" . htmlspecialchars($module_name['training_module_id']) . "_2'
        name='user_data_status_" . htmlspecialchars($module_name['training_module_id']) . "'
        value='2' " . ($module_name['data_receive_status'] == "2" ? "checked" : "") . ">
        </td>
        <td>
        <input type='radio' id='user_onboarding_status_" . htmlspecialchars($module_name['training_module_id']) . "_1'
        name='user_onboarding_status_" . htmlspecialchars($module_name['training_module_id']) . "'
        value='1' " . ($module_name['onboarding_status'] == "1" ? "checked" : "") . ">
        </td>
        <td>
        <input type='radio' id='user_onboarding_status_" . htmlspecialchars($module_name['training_module_id']) . "_0'
        name='user_onboarding_status_" . htmlspecialchars($module_name['training_module_id']) . "'
        value='0' " . ($module_name['onboarding_status'] == "0" ? "checked" : "") . ">
        </td>
        <td>
        <input type='radio' id='user_onboarding_status_" . htmlspecialchars($module_name['training_module_id']) . "_2'
        name='user_onboarding_status_" . htmlspecialchars($module_name['training_module_id']) . "'
        value='2' " . ($module_name['onboarding_status'] == "2" ? "checked" : "") . ">
        </td>
        </tr>";
        }

        echo $message;
        exit;
    } else if (isset($_POST['updateTrainingStatus']) && $_POST['updateTrainingStatus'] == 'updateTrainingStatus') {
        if (isset($_POST['executive_name']) && isset($_POST['society_id']) && isset($_POST['society_name'])) {
            $executive_name = $_POST['executive_name'];
            $society_id = $d->sanitizeActionIdAsInt($_POST['society_id'] ?? 0);
            $society_name = $_POST['society_name'];
            $currentDateTime = date("Y-m-d H:i:s");

            // Collect module IDs from POST and prefetch existing status rows once
            $updateModuleIds = [];
            foreach ($_POST as $key => $value) {
                if (strpos($key, 'user_data_status_') === 0) {
                    $mid = str_replace('user_data_status_', '', $key);
                    if (is_numeric($mid)) {
                        $updateModuleIds[] = (int)$mid;
                    }
                }
            }
            $existingByModule = [];
            if (!empty($updateModuleIds) && $society_id > 0) {
                $midsIn = implode(',', array_map('intval', $updateModuleIds));
                $existingQ = $d->selectRow(
                    "module_training_status_master.*",
                    "module_training_status_master",
                    "company_id='$society_id' AND module_id IN ($midsIn)"
                );
                while ($er = mysqli_fetch_assoc($existingQ)) {
                    $existingByModule[(int)$er['module_id']] = $er;
                }
            }

            foreach ($_POST as $key => $value) {
                if (strpos($key, 'user_data_status_') === 0) {
                    $module_id = str_replace('user_data_status_', '', $key);

                    $data_receive_status = $value;
                    $onboarding_status = $_POST["user_onboarding_status_{$module_id}"] ?? null;

                    $existingRecordData = $existingByModule[(int)$module_id] ?? null;

                    $data = [
                        'company_id' => $society_id,
                        'module_id' => $module_id,
                        'data_receive_status' => $data_receive_status,
                        'data_receive_date' => ($data_receive_status == 1 || $data_receive_status == 0 || $data_receive_status == 2) ? $currentDateTime : null,
                        'onboarding_status' => $onboarding_status,
                        'onboarding_date' => ($onboarding_status == 1 || $onboarding_status == 0 || $onboarding_status == 2) ? $currentDateTime : null,
                    ];
                    if ($existingRecordData) {
                        $module_training_status_id = $existingRecordData['module_training_status_id'];
                        $existing_data_receive_status = $existingRecordData['data_receive_status'];
                        $existing_onboarding_status = $existingRecordData['onboarding_status'];
                        $existing_data_receive_date = $existingRecordData['data_receive_date'];
                        $existing_onboarding_date = $existingRecordData['onboarding_date'];
                        if($existing_data_receive_status==$data_receive_status){
                            $data['data_receive_date']=$existing_data_receive_date;
                        }else{
                            $data['data_receive_change_by']=$bms_admin_id;
                        }
                        if($existing_onboarding_status==$onboarding_status){
                            $data['onboarding_date']=$existing_onboarding_date;
                        }else{
                            $data['onboarding_change_by']=$bms_admin_id;
                        }
                        $d->update("module_training_status_master", $data, "module_training_status_id='$module_training_status_id'");
                    } else {
                        $data['data_receive_change_by']=$bms_admin_id;
                        $data['onboarding_change_by']=$bms_admin_id;
                        $data['added_date'] = $currentDateTime;
                        $data['added_by'] = $executive_name;
                        $d->insert("module_training_status_master", $data);
                        $existingByModule[(int)$module_id] = $data;
                    }
                }
            }

            // **Updating Data to Society Master**
            $totalModulesQuery = $d->selectRow(
                "COUNT(*) AS total_modules",
                "training_module_master tmm JOIN training_module_priority_master tmpm ON tmm.module_priority = tmpm.priority_id",
                "tmm.module_type = 0 
            AND tmm.training_module_status = 0 
            AND tmpm.is_required = 1"
            );

            $totalModules = 0;
            if ($row = mysqli_fetch_assoc($totalModulesQuery)) {
                $totalModules = (int) $row['total_modules'];
            }
            $totalSessionModulesQuery = $d->selectRow(
                "sdm.session_day_id,sdm.session_day_name,MAX(mtm.training_date) AS training_date,MAX(mtm.data_receive_date) AS data_receive_date,MAX(mtm.onboarding_date) AS onboarding_date,COUNT(DISTINCT CASE WHEN tmm.module_type = 0 AND tmm.training_module_status = 0 AND tmpm.is_required = 1 THEN tmm.training_module_id END) AS module_count, COUNT(DISTINCT CASE WHEN tmm.module_type = 0 AND tmm.training_module_status = 0 AND tmpm.is_required = 1 AND mtm.company_id = '$society_id' AND mtm.training_status IN (1, 2) AND mtm.training_status != 3 THEN tmm.training_module_id END) AS training_completed, COUNT(DISTINCT CASE WHEN tmm.module_type = 0 AND tmm.training_module_status = 0 AND tmpm.is_required = 1 AND mtm.company_id = '$society_id' AND mtm.data_receive_status IN (1, 2) THEN tmm.training_module_id  END) AS data_receive_completed, COUNT(DISTINCT CASE  WHEN tmm.module_type = 0 AND tmm.training_module_status = 0 AND tmpm.is_required = 1  AND mtm.company_id = '$society_id' AND mtm.onboarding_status IN (1, 2) THEN tmm.training_module_id  END ) AS onboarding_completed",
                "session_day_master sdm LEFT JOIN training_module_master tmm  ON tmm.session_day_id = sdm.session_day_id LEFT JOIN training_module_priority_master tmpm ON tmm.module_priority = tmpm.priority_id LEFT JOIN module_training_status_master mtm  ON mtm.module_id = tmm.training_module_id",
                "sdm.session_day_status = 0",
                "GROUP BY sdm.session_day_id, sdm.session_day_name ORDER BY sdm.session_day_id ASC"
            );
            $totalSessionModules = [];
            while ($row = mysqli_fetch_array($totalSessionModulesQuery)) {
                $totalSessionData['session_day_id'] = $row['session_day_id'];
                $totalSessionData['session_day_name'] = $row['session_day_name'];
                $totalSessionData['module_count'] = (int) $row['module_count'];
                $totalSessionData['training_completed'] = (int) $row['training_completed'];
                $totalSessionData['data_receive_completed'] = (int) $row['data_receive_completed'];
                $totalSessionData['onboarding_completed'] = (int) $row['onboarding_completed'];
                $totalSessionData['module_count'] = (int) $row['module_count'];
                $totalSessionData['total_modules'] = (int) $totalModules;
                $totalSessionData['last_training_date'] = $row['training_date'];
                $totalSessionData['last_data_receive_date'] = $row['data_receive_date'];
                $totalSessionData['last_onboarding_date'] = $row['onboarding_date'];

                array_push($totalSessionModules, $totalSessionData);
            }
            $totalTrainingCompleted = 0;
            $totalDataReceiveCompleted = 0;
            $totalOnboardingCompleted = 0;

            foreach ($totalSessionModules as $session) {
                $totalTrainingCompleted += $session['training_completed'];
                $totalDataReceiveCompleted += $session['data_receive_completed'];
                $totalOnboardingCompleted += $session['onboarding_completed'];
            }

            $trainingStatus = $totalTrainingCompleted == $totalModules ? 1 : 0;
            $dataReceiveStatus = $totalDataReceiveCompleted == $totalModules ? 1 : 0;
            $onboardingStatus = $totalOnboardingCompleted == $totalModules ? 1 : 0;

            $setup_data = json_encode($totalSessionModules, JSON_PRETTY_PRINT);
            $updateData = array(
                'setup_training_percentage' => $totalTrainingCompleted,
                'setup_data_receive_percentage' => $totalDataReceiveCompleted,
                'setup_onboarding_percentage' => $totalOnboardingCompleted,
                'setup_training_status' => $trainingStatus,
                'setup_data_receive_status' => $dataReceiveStatus,
                'setup_onboarding_status' => $onboardingStatus,
                'setup_data' => $setup_data
            );
            $d->update('society_master', $updateData, "society_id='$society_id'");

            $_SESSION['msg'] = "Status updated successfully!";
            header("location: " . getCompanyOnboardingRedirectUrl());
            exit;
        } else {
            $_SESSION['msg1'] = "Something went wrong!";
            header("location: " . getCompanyOnboardingRedirectUrl());
            exit;
        }
    } else {
        $_SESSION['msg1'] = "Something went wrong";
        header("Location: ../welcome");
        exit();
    }
} else {
    $_SESSION['msg1'] = "Something went wrong";
    header("Location: ../welcome");
    exit();
}

// Helper function to build companyOnboarding redirect URL with filter parameters
function getCompanyOnboardingRedirectUrl() {
    $filters = [];
    $filterKeys = ['countryId', 'sId', 'cId', 'training_type', 'rise_filter', 'product_type'];
    foreach ($filterKeys as $key) {
        $value = '';
        if (isset($_POST[$key]) && $_POST[$key] != '') {
            $value = $_POST[$key];
        } elseif (isset($_GET[$key]) && $_GET[$key] != '') {
            $value = $_GET[$key];
        } elseif (isset($_SESSION['companyOnboarding_filters'][$key]) && $_SESSION['companyOnboarding_filters'][$key] != '') {
            $value = $_SESSION['companyOnboarding_filters'][$key];
        }
        if ($key === 'countryId') {
            if ($value != '' && $value > 0) {
                $filters[$key] = $value;
            } elseif (!isset($filters[$key])) {
                $filters[$key] = 101;
            }
        } elseif ($key === 'product_type') {
            if ($value === 'crm' || $value === 'hrms') {
                $filters[$key] = $value;
            } elseif (!isset($filters[$key])) {
                $filters[$key] = 'hrms';
            }
        } elseif ($value != '') {
            $filters[$key] = $value;
        }
    }
    if (!isset($filters['countryId'])) {
        $filters['countryId'] = 101;
    }
    if (!isset($filters['product_type'])) {
        $filters['product_type'] = 'hrms';
    }
    $queryString = !empty($filters) ? '?' . http_build_query($filters) : '';
    return '../companyOnboarding' . $queryString;
}