<?php
include_once 'lib.php';
$txt = "Company Plan Exrpire Reminder Cron Start " . date("Y-m-d h:i:s A");
$myfile = file_put_contents('../img/whatsapplog.txt', $txt . PHP_EOL, FILE_APPEND | LOCK_EX);

$day = 5;
$mobile = "7698483849";
$country_code = "+91";

$today = date('Y-m-d');
$result = $d->selectRow("society_name, city_name, plan_expire_date", "society_master", "society_status = 0 AND plan_expire_date BETWEEN '$today' AND DATE_ADD('$today', INTERVAL '$day' DAY)");
$companyCount = mysqli_num_rows($result);
$societies = [];
while ($row = mysqli_fetch_assoc($result)) {
    $societies[] = $row;
}
$pdfContent = generateExpiryPdfContent($societies);

$pdfFileName = "expiry_report_" . date('Ymd_His') . ".pdf";
$pdfFilePathServer = "../img/" . $pdfFileName;

file_put_contents($pdfFilePathServer, $pdfContent);
$pdfFilePath = $m->base_url() . "img/" . $pdfFileName;
if (method_exists($sms_api, 'sendWhatsAppMessage')) {
    $response = $sms_api->sendWhatsAppMessage($mobile, "53", "682801111491424", [$companyCount, $day], ['document_path' => $pdfFilePath,], $d, $country_code);
}else{
    exit;
}
if ($response['http_code'] == '200') {
    $txt = "Company Plan Reminder Sent to $mobile on " . date("Y-m-d h:i:s A") . " for $companyCount Companies| Response: " . json_encode($response);
} else {
    $txt = "Company Plan Reminder failed to send to $mobile on " . date("Y-m-d h:i:s A") . " for $companyCount Companies| Response: " . json_encode($response);
}
file_put_contents('../img/whatsapplog.txt', $txt . PHP_EOL, FILE_APPEND | LOCK_EX);

if (file_exists($pdfFilePathServer)) {
    unlink($pdfFilePathServer);
}

$txt = "Company Plan Exrpire Reminder Cron End " . date("Y-m-d h:i:s A");
$myfile = file_put_contents('../img/whatsapplog.txt', $txt . PHP_EOL, FILE_APPEND | LOCK_EX);
exit;

function generateExpiryPdfContent($societies)
{
    $pdf = "%PDF-1.3\n";
    $objects = [];

    $objects[] = "<< /Type /Catalog /Pages 2 0 R >>";
    $objects[] = "<< /Type /Pages /Kids [3 0 R] /Count 1 >>";
    $objects[] = "<< /Type /Page /Parent 2 0 R /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> /MediaBox [0 0 612 792] >>";

    $y = 750;
    $lines = [];

    $lines[] = "BT /F1 16 Tf 200 $y Td (Upcoming Plan Expiry Report) Tj ET";
    $y -= 25;
    $lines[] = "BT /F1 10 Tf 220 $y Td (Generated on: " . date('d M Y') . ") Tj ET";
    $y -= 30;

    $left = 81;
    $col1 = $left;
    $col2 = $col1 + 50;
    $col3 = $col2 + 150;
    $col4 = $col3 + 100;
    $col5 = $col4 + 90;
    $right = $col5 + 60;
    $rowHeight = 22;

    $lines[] = "$left $y m $right $y l S";
    $lines[] = "$left " . ($y - $rowHeight) . " m $right " . ($y - $rowHeight) . " l S";

    foreach ([$left, $col2, $col3, $col4, $col5, $right] as $x) {
        $lines[] = "$x $y m $x " . ($y - $rowHeight) . " l S";
    }

    $headerY = $y - 15;
    $lines[] = "BT /F1 10 Tf " . ($col1 + 5) . " $headerY Td (No) Tj ET";
    $lines[] = "BT /F1 10 Tf " . ($col2 + 5) . " $headerY Td (Society Name) Tj ET";
    $lines[] = "BT /F1 10 Tf " . ($col3 + 5) . " $headerY Td (City) Tj ET";
    $lines[] = "BT /F1 10 Tf " . ($col4 + 5) . " $headerY Td (Expiry Date) Tj ET";
    $lines[] = "BT /F1 10 Tf " . ($col5 + 5) . " $headerY Td (Days Left) Tj ET";
    $y -= $rowHeight;

    if (!empty($societies)) {
        $i = 1;
        foreach ($societies as $society) {
            if ($y < 50)
                break;

            $lines[] = "$left $y m $right $y l S";
            foreach ([$left, $col2, $col3, $col4, $col5, $right] as $x) {
                $lines[] = "$x $y m $x " . ($y - $rowHeight) . " l S";
            }

            $daysLeft = ceil((strtotime($society['plan_expire_date']) - strtotime(date('Y-m-d'))) / 86400);
            $daysLeftText = ($daysLeft == 0) ? "Today" : "$daysLeft days";
            $contentY = $y - 15;

            $lines[] = "BT /F1 10 Tf " . ($col1 + 5) . " $contentY Td ($i) Tj ET";
            $lines[] = "BT /F1 10 Tf " . ($col2 + 5) . " $contentY Td (" . substr($society['society_name'], 0, 30) . ") Tj ET";
            $lines[] = "BT /F1 10 Tf " . ($col3 + 5) . " $contentY Td (" . substr($society['city_name'], 0, 20) . ") Tj ET";
            $lines[] = "BT /F1 10 Tf " . ($col4 + 5) . " $contentY Td (" . date('d M Y', strtotime($society['plan_expire_date'])) . ") Tj ET";
            $lines[] = "BT /F1 10 Tf " . ($col5 + 15) . " $contentY Td ($daysLeftText) Tj ET";

            $y -= $rowHeight;
            $i++;
        }
        $lines[] = "$left $y m $right $y l S";
    } else {
        $lines[] = "BT /F1 12 Tf 200 $y Td (No expiring plans found) Tj ET";
    }

    $content = implode("\n", $lines);
    $stream = "stream\n$content\nendstream";
    $objects[] = "<< /Length " . strlen($content) . " >>\n$stream";

    $objects[] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>";

    $xref = "xref\n0 " . (count($objects) + 1) . "\n0000000000 65535 f \n";
    $offsets = [];
    $pdfOutput = $pdf;
    $pos = strlen($pdfOutput);

    foreach ($objects as $i => $obj) {
        $offsets[] = sprintf("%010d 00000 n \n", $pos);
        $pdfOutput .= ($i + 1) . " 0 obj\n" . $obj . "\nendobj\n";
        $pos = strlen($pdfOutput);
    }

    $pdfOutput .= $xref . implode('', $offsets);
    $pdfOutput .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\n";
    $pdfOutput .= "startxref\n$pos\n%%EOF";

    return $pdfOutput;
}
