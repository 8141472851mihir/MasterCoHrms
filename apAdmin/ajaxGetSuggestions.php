<?php

include_once 'common/object.php';
error_reporting(0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $suggestions = [
        "ticketClosingReasons" => [],
        "ticketReplySuggestions" => []
    ];

    $where = "suggestion_status = 0";
    $result = $d->select("suggestion_master", $where);

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            if ($row['suggestion_type'] == 0) {
                $suggestions["ticketClosingReasons"][] = $row['suggestion_name'];
            } elseif ($row['suggestion_type'] == 1) {
                $suggestions["ticketReplySuggestions"][] = $row['suggestion_name'];
            }
        }
    }

    echo json_encode([
        'status' => true,
        'message' => "Suggestions fetched successfully.",
        'data' => $suggestions
    ]);
    exit();
} else {
    echo json_encode([
        'status' => false,
        'message' => "Invalid request method."
    ]);
    exit();
}
?>
