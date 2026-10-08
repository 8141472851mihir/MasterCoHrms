<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
include 'common/object.php';
$language_id = $_COOKIE['language_id'];
$xml = $d->loadLanguageXmlFromS3($language_id, "xml");
if ($xml === false && $language_id != "") {
	$d->createLanguageFiles($language_id, $base_url, '', 'both');
	$xml = $d->loadLanguageXmlFromS3($language_id, "xml");
}

$searchTerm = isset($_GET['term']) ? $d->escapeSqlLike($_GET['term']) : '';

$sidebareData = file_get_contents("../img/menu_file_new_$global_role_id.json");
$sidebareData = json_decode($sidebareData,true);
$tutorialData = array();
foreach ($sidebareData as $menuItem) {
    // Search the main menu name
    if (stripos($menuItem['menu_name'], $searchTerm) !== false && $menuItem['menu_link']!="#" && $menuItem['menu_link']!="") {
        $data['id'] = $menuItem['menu_link'];
        $data['value'] = $menuItem['menu_name'];
        array_push($tutorialData, $data);
    }

    // Check subMenu if exists
    if (!empty($menuItem['subMenu'])) {
        foreach ($menuItem['subMenu'] as $subMenuItem) {
            if (stripos($subMenuItem['menu_name'], $searchTerm) !== false) {
                $data['id'] = $subMenuItem['menu_link'];
                $data['value'] = $subMenuItem['menu_name'];
                array_push($tutorialData, $data);
            }
        }
    }
}
if (preg_match('/\d|#TKT|TKT/i', $searchTerm)) {
    $search_feedback_id = preg_replace('/\D/', '', $searchTerm);
    $feedback_qry=$d->selectRow("feedback_id","feedback_master","feedback_id='$search_feedback_id'");
    while($feedback_data=mysqli_fetch_array($feedback_qry)){
        $feedback_id=$feedback_data['feedback_id'];
        $data['id'] = "feedbackTimeline?id=$feedback_id";
        $data['value'] = "#TKT$feedback_id";
        array_push($tutorialData, $data);
    }
}

$company_qry = $d->selectRow("society_id, society_name", "society_master", "company_full_name LIKE '%$searchTerm%' OR society_name LIKE '%$searchTerm%' OR society_id LIKE '%$searchTerm%'");
while ($society_data = mysqli_fetch_array($company_qry)) {
    $society_id = $society_data['society_id'];
    $society_name = $society_data['society_name'];
    $data['id'] = "companyDetails?society_id=$society_id";
    $data['value'] = $d->short_app_name() . '_' . $society_id . ' ' . $society_name;
    array_push($tutorialData, $data);
}
echo json_encode($tutorialData);