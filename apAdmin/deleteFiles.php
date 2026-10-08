<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once('./common/object.php');
/*$files = [
    './ajaxGetSocietyDetailsKbg.php',
    './downloadKbgCSV.php',
    './downloadHousieCSV.php',
    './addHousieGame.php',
    './importHousieCSVData.php',
    './importKbgCSVData.php',
    './kbg.php',
    './kbgQuestions.php',
    './kbgResult.php',
    './manageHousie.php',
    './manageHousieQuestions.php',
    './housieQuestions.php',
    './manageKbg.php',
    './controller/kbgGameController.php',
    './controller/housieController.php',
    './controller/emerController.php',
];
$i = 0;
foreach ($files as $file) {
    if (file_exists($file)) {
        unlink($file);
        echo "<pre>";
        echo "success";
        echo "<br/>";
        $i++;
    } else {
        echo "<pre>";
        echo "failed";
        echo "<br/>";
    }

}
echo "<br/>";
echo $i . " Files Deleted Successfully"; 


$sliderList=$d->selectRow("DISTINCT slider_image_name","app_slider_master","");
$sliderImages = [];
if (mysqli_num_rows($sliderList)>0) {
    while ($row = mysqli_fetch_assoc($sliderList)) {
        $sliderImages[] = $row['slider_image_name'];
    }
    $sliderFolder = "../img/sliders/";
    $allFiles = glob($sliderFolder . "*");
    $i=0;
    $total_files=count($allFiles);
    foreach ($allFiles as $file) {
        $fileName = basename($file);
        if (!in_array($fileName, $sliderImages) && $fileName!="slider.jpg") {
            if (is_file($file)) {
                $i++;
                unlink($file);
            }
        }
    }
    echo "Total Slider Files: $total_files & Deleted Files: $i <br>";
}
*/


$feedbackList = $d->selectRow(
    "attachment, attachment_2, video, feedback_log_attachment",
    "feedback_master 
     LEFT JOIN feedback_log_master 
     ON feedback_log_master.feedback_id = feedback_master.feedback_id",
    "feedback_master.feedback_status = '2' 
     AND feedback_master.inquiry_type = '0'"
);

$feedImages = [];

if (mysqli_num_rows($feedbackList) > 0) {
    while ($row = mysqli_fetch_assoc($feedbackList)) {

        if (!empty($row['attachment'])) {
            $feedImages[] = $row['attachment'];
        }
        if (!empty($row['attachment_2'])) {
            $feedImages[] = $row['attachment_2'];
        }
        if (!empty($row['video'])) {
            $feedImages[] = $row['video'];
        }
        if (!empty($row['feedback_log_attachment'])) {
            $feedImages[] = $row['feedback_log_attachment'];
        }
    }

    $feedImages = array_unique($feedImages);

    $feedFolder = "../img/fin_support/";
    $deleted = 0;
    
    foreach ($feedImages as $fileName) {
        $fileName = basename($fileName); // safety
        $file = $feedFolder . $fileName;

        if (is_file($file)) {
            unlink($file);
            $deleted++;
        }
    }

    echo "Total Files From DB: " . count($feedImages) . " & Deleted Files: " . $deleted;
}
?>