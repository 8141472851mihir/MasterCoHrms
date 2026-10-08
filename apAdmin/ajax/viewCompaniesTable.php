<?php
include_once('../common/objectController.php');
header('Content-Type: application/json');
// ini_set('display_errors', '1');
// ini_set('display_startup_errors', '1');
// error_reporting(E_ALL);
// Get DataTables server-side processing parameters
$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 25;
$searchValue = isset($_POST['search']['value']) ? $d->escapeSqlLike($_POST['search']['value']) : '';
$orderColumn = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
$orderDir = $d->sanitizeDatatableOrderDir(isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'ASC');

// Get filter parameters
$countryId =  (isset($_POST['countryId']) && $_POST['countryId'] > 0) ? $d->sanitizeReportFilterIdAsInt($_POST['countryId'], 101) : 101;
$sId = isset($_POST['sId']) ? $d->sanitizeReportFilterIdAsInt($_POST['sId']) : 0;
$cId = isset($_POST['cId']) ? $d->sanitizeReportFilterIdAsInt($_POST['cId']) : 0;

$whereClause = "1=1";

if(isset($countryId) && $countryId > 0) {
    $whereClause .= " AND country_id='$countryId'";
}

if (isset($sId) && $sId > 0) {
    $whereClause .= " AND state_id='$sId'";
}

if (isset($cId) && $cId > 0) {
    $whereClause .= " AND city_id='$cId'";
}

// $searchValue = trim($searchValue);
$searchValue = mysqli_real_escape_string($con, $searchValue);
$searchId = (strpos($searchValue, '_') !== false && is_numeric(end(explode('_', $searchValue)))) ? end(explode('_', $searchValue)) : '';

if (!empty($searchValue)) {
    $whereClause .= " AND (society_name LIKE '%$searchValue%'
        OR society_code LIKE '%$searchValue%'
        OR society_id LIKE '%$searchValue%'
        OR city_name LIKE '%$searchValue%'
        OR '$searchId' != '' AND society_id = '$searchId')";
}

// Get total records
$totalResult = $d->selectRow("COUNT(*) as total", "society_master", "$whereClause");
$totalRecords = mysqli_fetch_assoc($totalResult)['total'];
$filteredRecords = $totalRecords;

$dataResult = $d->selectRow("society_master.*", "society_master", "$whereClause", "ORDER BY society_id $orderDir LIMIT $start, $length");

$data = [];
$rowIndex = $start + 1;
$short_app_name = $d->short_app_name();
$srNo = $start + 1;

while ($row = mysqli_fetch_assoc($dataResult)) {

    $society_id = $row['society_id'];
    $company_id_display ='<span style="display:none;">' . $society_id . '</span>' . $short_app_name . '_' . $society_id;
    $company_name = '<a href="' . $row['sub_domain'] . 'apAdmin/" target="_blank">' .htmlspecialchars($row['society_name']) .'</a>';
    $company_app_menu ='<a href="companyAppMenu?sId=' . $society_id . '" class="btn btn-primary btn-sm" title="Company App Menu"><i class="fa fa-eye"></i></a>';

    $banners =
        '<form action="companyBanners" method="GET">
            <input type="hidden" name="id" value="' . $society_id . '">
            <button class="btn btn-primary btn-sm" title="Company Banners"><i class="fa fa-eye"></i></button>
        </form>';

    $splash =
        (($row['splash_colour'] == '' && $row['splash_image'] == '') ? 'Default ' : '') .
        '<button data-toggle="modal" data-target="#splashModal" onclick="changeSplash(\'' . $society_id . '\');" title="Set/Remove Splash" class="btn btn-warning btn-sm">
            <i class="fa fa-pencil"></i>
        </button>';

    $buttonClass     = ($row['from_rise_event'] == "1") ? 'btn-success-new' : 'btn-danger';
    $buttonCondition = ($row['from_rise_event'] == "1") ? 'Yes' : 'No';
    $status          = ($row['from_rise_event'] == "1") ? 'removeFromRise' : 'addToRise';
    $newStatus       = ($row['from_rise_event'] == "1") ? 'addToRise' : 'removeFromRise';
    $newStatusVal    = ($row['from_rise_event'] == "1") ? '1' : '0';
    $statusValue     = ($row['from_rise_event'] == "1") ? '0' : '1';

    $rise_event =
        '<input type="button" class="btn btn-sm pl-1 pr-1 w-50 ' . $buttonClass . '" id="rise_company_id_' . $society_id . '"
            onclick="changeStatusNew(\'' . $society_id . '\', \'' . $status . '\', \'' . $newStatus . '\', \'' . $statusValue . '\', \'' . $newStatusVal . '\', \'rise_company_id_' . $society_id . '\', \'\', \'\', \'./controller/statusController.php\', \'No\',\'Yes\' );" value="' . $buttonCondition . '">';

    $settings ='<a href="javascript:void(0)" data-toggle="modal" data-target="#settingModal" onclick="getAllBuildingData(' . $society_id . ')" class="btn btn-info btn-sm" title="Settings"> <i class="fa fa-gear"></i> </a>';

    $institute = '';
    if ($role_id == 1 && $row['institute_username'] == '') {
        $institute = '<a href="javascript:void(0)" data-toggle="modal" data-target="#addInstituteModel" onclick="AddInstitute(' . $society_id . ')" class="btn btn-warning btn-sm" title="Add Institute"><i class="fa fa-university"></i></a>';
    }

    $crm = '';
    if ($role_id == 1) {
        if ($row['crm_created'] == "0") {
            $crm ='<a href="javascript:void(0)" data-toggle="modal" data-target="#createCrm" onclick="createCrm(\'' . $society_id . '\',\'' . addslashes($row['society_name']) . '\',\'' . $row['sub_domain'] . '\')" class="btn btn-primary btn-sm" title="Create CRM"> <i class="fa fa-users"></i> </a>';
        } else {
            $crm =
                '<div class="d-flex align-items-center">
                    <span>Created</span>
                    <form action="controller/buildingController.php" method="post" class="ml-1">
                        <input type="hidden" name="deleteCRM" value="deleteCRM">
                        <input type="hidden" name="companyId" value="' . $society_id . '">
                        <button type="submit" class="btn btn-danger btn-sm" title="Delete CRM"> <i class="fa fa-trash-o"></i> </button>
                    </form>
                </div>';
        }
    }

    $server_url = '';
    if ($role_id == 1) {
        if ($row['created_on_society_server'] == 1) {
            $server_url = 'Created';
        } else {
            $server_url =
                '<form action="controller/createSocietyAutoController.php" method="post">
                    <input type="hidden" name="society_id" value="' . $society_id . '">
                    <input type="hidden" name="createSoceitySubdomain" value="createSoceitySubdomain">
                    <button class="btn btn-danger btn-sm"> Create </button>
                </form>';
        }
    }

    $delete_company = '';
    if ($role_id == 1) {
        $delete_company =
            '<form action="controller/buildingController.php" method="post">
                <input type="hidden" name="society_id_delete" value="' . $society_id . '">
                <input type="hidden" name="country_id" value="' . $row['country_id'] . '">
                <input type="hidden" name="state_id" value="' . $row['state_id'] . '">
                <input type="hidden" name="city_id" value="' . $row['city_id'] . '">
                <input type="hidden" name="sName" value="' . htmlspecialchars($row['society_name']) . '">
                <button type="submit" class="btn btn-danger btn-sm" title="Delete Company"> <i class="fa fa-trash-o"></i> </button>
            </form>';
    }

    $company_details = '';
    if ($role_id == 1) {
        $company_details =
            '<form method="GET" action="companyDetails">
                <input type="hidden" name="society_id" value="' . $society_id . '">
                <button class="btn btn-primary btn-sm" title="Company Details"> <i class="fa fa-eye"></i></button>
            </form>';
    }

    $data[] = [
        'company_id_display'      => $company_id_display,
        'company_name'    => $company_name,
        'society_code'    => $row['society_code'],
        'city_name'       => $row['city_name'],
        'app_menu'        => $company_app_menu,
        'banners'         => $banners,
        'splash'          => $splash,
        'rise_event'      => $rise_event,
        'settings'        => $settings,
        'institute'       => $institute,
        'crm'             => $crm,
        'server_url'      => $server_url,
        'delete_company' => $delete_company,
        'company_details'=> $company_details   
    ];
}
function utf8ize($mixed) {
    if (is_array($mixed)) {
        foreach ($mixed as $key => $value) {
            $mixed[$key] = utf8ize($value);
        }
    } elseif (is_string($mixed)) {
        return mb_convert_encoding($mixed, 'UTF-8', 'UTF-8');
    }
    return $mixed;
}

$data = utf8ize($data);

echo json_encode([
    'draw' => $draw,
    'recordsTotal' => $totalRecords,
    'recordsFiltered' => $filteredRecords,
    'data' => $data
]);
