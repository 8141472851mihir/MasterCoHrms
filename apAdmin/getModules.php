<?php



include_once 'common/object.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST)) {
    $edit_id = $_POST['edit_id'] ?? '';
    $value = $_POST['value'] ?? '';
    $trainingDays = $_POST['training_days'] ?? 1;

    $allModules = [];
    $allModulesResult = $d->select(
        "training_module_master",
        "module_type='1' AND training_module_status='0'",
        ""
    );

    if (mysqli_num_rows($allModulesResult) > 0) {
        while ($row = mysqli_fetch_assoc($allModulesResult)) {
            $allModules[] = $row;
        }
    }
    
    if ($edit_id) {
        $selectedModulesResult = $d->selectRow(
            "batch_module_master.day_number, batch_module_master.module_ids",
            "batch_module_master
            LEFT JOIN training_module_master ON batch_module_master.module_ids = training_module_master.training_module_id",
            "batch_module_master.batch_id = '$edit_id' 
            AND training_module_master.module_type = '1' 
            AND training_module_master.training_module_status = '0'
            GROUP BY batch_module_master.day_number"
        );

        $selectedModules = [];

        if (mysqli_num_rows($selectedModulesResult) > 0) {
            while ($row = mysqli_fetch_assoc($selectedModulesResult)) {
                $dayNumber = $row['day_number'];
                $moduleIds = explode(',', $row['module_ids']);
                $selectedModules[$dayNumber] = $moduleIds;
            }
        }

    }

    $html = '';
    for ($i = 1; $i <= $trainingDays; $i++) {
        $html .= '<div class="training-day-card">';
        $html .= '<div class="card-header">Day ' . $i . '</div>';

        $html .= '<select class="form-control multiple-select" id="day' . $i . '" name="day' . $i . '[]" required multiple="multiple">';
        $html .= '<option value="">Select Day ' . $i . '</option>';

        foreach ($allModules as $module) {
            $isSelected = (isset($selectedModules[$i]) && in_array($module['training_module_id'], $selectedModules[$i])) ? 'selected' : '';
            $html .= '<option value="' . $module['training_module_id'] . '" ' . $isSelected . '>' . $module['training_module_name'] . '</option>';
        }

        $html .= '</select>';
        $html .= '</div>';
    }

    header('Content-Type: application/json');
    echo json_encode(['html' => $html]);
}
?>