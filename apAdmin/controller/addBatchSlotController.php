<?php
include '../common/objectController.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST)) {
    if (isset($selected_dates) && !empty($selected_dates)) {
        $selectedDates = json_decode($selected_dates, true);
        $society_ids = $d->sanitizeActionIds(isset($_POST['society_id']) ? $_POST['society_id'] : []);
        $reference_society_ids = $d->sanitizeActionIds(isset($_POST['reference_society_id']) ? $_POST['reference_society_id'] : []);

        foreach ($reference_society_ids as $key => $ref_id) {
            if (in_array($ref_id, $society_ids)) {
                unset($reference_society_ids[$key]);
            }
        }

        $company_ids_combined = array_merge($society_ids, $reference_society_ids);
        $combined_company_id = !empty($company_ids_combined) ? implode(',', array_unique($company_ids_combined)) : "";

        $company_id = $combined_company_id;
        $reference_company_ids = !empty($reference_society_ids) ? implode(',', $reference_society_ids) : "";

        $m->set_data('batch_id', $batch_id);
        $m->set_data('city', $city);
        $m->set_data('start_date', $start_date);
        $m->set_data('meeting_names', $meeting_name);
        $m->set_data('from_times', $meeting_from_time);
        $m->set_data('to_times', $meeting_to_time);
        $m->set_data('meeting_trainer_ids', $meeting_trainer_id);
        $m->set_data('company_id', $company_id);
        $m->set_data('reference_company_ids', $reference_company_ids);

        $batch_id = $m->get_data('batch_id');
        $city = $m->get_data('city');
        $start_date = $m->get_data('start_date');
        $meeting_names = $m->get_data('meeting_names');
        $from_times = $m->get_data('from_times');
        $to_times = $m->get_data('to_times');
        $meeting_trainer_ids = $m->get_data('meeting_trainer_ids');


        $batchQuery = $d->select("training_batch_master", "batch_id='$batch_id'");
        $batchRow = mysqli_fetch_assoc($batchQuery);
        $batch_name = strtoupper($batchRow['batch_name']);


        $insertSuccess = true;

        // Prefetch existing slot names for all selected dates once
        $existingSlotsByDate = [];
        $dateList = [];
        foreach ($selectedDates as $schedule_date) {
            $dateEsc = $d->escapeSqlString($schedule_date);
            $dateList[] = "'$dateEsc'";
        }
        if (!empty($dateList)) {
            $datesIn = implode(',', $dateList);
            $allSlotsQ = $d->selectRow(
                "date, slot_name",
                "batch_slot_master",
                "date IN ($datesIn)"
            );
            while ($es = mysqli_fetch_assoc($allSlotsQ)) {
                $existingSlotsByDate[$es['date']][] = $es['slot_name'];
            }
        }

        foreach ($selectedDates as $dayIndex => $schedule_date) {
            // Allow adding same batch for same dates - removed duplicate check

            $dayNumber = $dayIndex + 1;

            $trainingMonth = date("M", strtotime($schedule_date));
            $trainingYear = date("Y", strtotime($schedule_date));

            for ($meetingIndex = 0; $meetingIndex < count($meeting_names); $meetingIndex++) {
                $meeting_number = $meetingIndex + 1;
                $base_slot_name = "{$batch_name}S{$dayNumber}-{$trainingMonth}-M{$meeting_number}";
                
                // Resolve unique slot name from prefetched names for this date
                $slot_name = $base_slot_name;
                $maxCount = 0;
                foreach ($existingSlotsByDate[$schedule_date] ?? [] as $existingSlotName) {
                    if ($existingSlotName == $base_slot_name) {
                        $maxCount = max($maxCount, 1);
                    } elseif (preg_match('/^' . preg_quote($base_slot_name, '/') . '-(\d+)$/', $existingSlotName, $matches)) {
                        $maxCount = max($maxCount, intval($matches[1]));
                    }
                }
                if ($maxCount > 0) {
                    $slot_name = $base_slot_name . "-" . ($maxCount + 1);
                }

                // Track newly chosen names so later meetings/dates in same request stay unique
                $existingSlotsByDate[$schedule_date][] = $slot_name;

                if (!isset($meeting_trainer_ids[$meetingIndex])) {
                    $insertSuccess = false;
                    continue;
                }

                $trainer_id_for_meeting = $meeting_trainer_ids[$meetingIndex];

                // Setting data for insertion
                $m->set_data('slot_name', $slot_name);
                $m->set_data('trainer_id', $trainer_id_for_meeting);
                $m->set_data('month', $trainingMonth);
                $m->set_data('year', $trainingYear);
                $m->set_data('date', $schedule_date);
                $m->set_data('from_time', $from_times[$meetingIndex]);
                $m->set_data('to_time', $to_times[$meetingIndex]);
                $m->set_data('meeting_count', $meeting_number);
                $m->set_data('meeting_day', $dayNumber);
                $m->set_data('company_id', $company_id);
                $m->set_data('reference_company_ids', $reference_company_ids);
                $m->set_data('created_by', $bms_admin_id);
                $m->set_data('created_date', date("Y-m-d H:i:s"));

                // Inserting the new schedule slot
                $data = array(
                    'batch_id' => $batch_id,
                    'slot_name' => $m->get_data('slot_name'),
                    'trainer_id' => $m->get_data('trainer_id'),
                    'city' => $city,
                    'month' => $m->get_data('month'),
                    'year' => $m->get_data('year'),
                    'date' => $m->get_data('date'),
                    'start_date' => $start_date,
                    'from_time' => $m->get_data('from_time'),
                    'to_time' => $m->get_data('to_time'),
                    'meeting_count' => $m->get_data('meeting_count'),
                    'meeting_day' => $m->get_data('meeting_day'),
                    'company_id' => $m->get_data('company_id'),
                    'reference_company_ids' => $m->get_data('reference_company_ids'),
                    'created_by' => $m->get_data('created_by'),
                    'created_date' => $m->get_data('created_date'),
                );

                $q = $d->insert("batch_slot_master", $data);

                if (!$q) {
                    $insertSuccess = false;
                    $_SESSION['msg1'] = "Something went wrong.";
                    header("Location: ../addBatchSlot");
                    exit();
                }
            }
        }

        // Redirect if insert is successful
        if ($insertSuccess) {
            $_SESSION['msg'] = "Batch slot successfully added.";
            header("Location: ../manageTrainingSlots");
            exit();
        } else {
            $_SESSION['msg1'] = "Something went wrong.";
            header("Location: ../addBatchSlot");
            exit();
        }
    }
}
