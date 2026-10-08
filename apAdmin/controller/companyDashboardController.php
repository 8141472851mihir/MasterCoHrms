<?php
include '../common/objectController.php';
$durationType = $_POST['duration_type'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST)) {
    extract($_POST);

    if (isset($_POST['getCompanyDashboardData'])) {
        $companyType = $_POST['company_type'] ?? '0';
        $durationType = $_POST['duration_type'] ?? '1';

        $monthYearList = [];
        $monthWiseRegistrationCount = [];

        if ($durationType == '1') {
            $startDate = date('Y-m-d', strtotime('-29 days'));
            for ($i = 0; $i < 30; $i++) {
                $dayLabel = date('d-M', strtotime("-$i days"));
                $monthYearList[] = $dayLabel;
            }
            $monthYearList = array_reverse($monthYearList);
            $monthWiseRegistrationCount = array_fill_keys($monthYearList, 0);
        } else {
            $baseDate = date('Y-m-01');
            for ($i = 0; $i < 13; $i++) {
                $monthYear = date('M-Y', strtotime("-$i months", strtotime($baseDate)));
                $monthYearList[] = $monthYear;
            }
            $monthYearList = array_reverse($monthYearList);
            $monthWiseRegistrationCount = array_fill_keys($monthYearList, 0);
        }

        if ($companyType == '0') {
            // NEW REGISTRATION (created_date)
            $dateField = "created_date";
            $where = ($durationType == '1') ? "$dateField >= '$startDate'" :
                (($durationType == '2') ? "$dateField >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)" : "1");

            $dataQry = $d->selectRow(
                ($durationType == '1' ?
                    "DATE_FORMAT($dateField, '%d-%b') AS label" :
                    "DATE_FORMAT($dateField, '%b-%Y') AS label") .
                ", COUNT(DISTINCT society_id) AS count",
                "society_master",
                "$where GROUP BY " .
                ($durationType == '1' ? "DATE($dateField)" : "YEAR($dateField), MONTH($dateField)"),
                "ORDER BY $dateField"
            );

        } elseif ($companyType == '1') {
            // TRIAL (is_renewal = 2)
            $dateField = "transection_date";
            $where = "is_renewal = 2 AND " .
                (($durationType == '1') ? "$dateField >= '$startDate'" :
                    (($durationType == '2') ? "$dateField >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)" : "1"));

            $dataQry = $d->selectRow(
                ($durationType == '1' ?
                    "DATE_FORMAT($dateField, '%d-%b') AS label" :
                    "DATE_FORMAT($dateField, '%b-%Y') AS label") .
                ", COUNT(DISTINCT society_id) AS count",
                "transection_master",
                "$where GROUP BY " .
                ($durationType == '1' ? "DATE($dateField)" : "YEAR($dateField), MONTH($dateField)"),
                "ORDER BY $dateField"
            );

        } elseif ($companyType == '2') {
            // New registrations: created_date from society_master + first transaction is is_renewal = 0
            $dateField = "created_date";

            $where = "EXISTS (
                SELECT 1 
                FROM transection_master tm 
                WHERE tm.society_id = society_master.society_id 
                AND tm.is_renewal = 0
                AND tm.transection_date = (
                    SELECT MIN(transection_date) 
                    FROM transection_master 
                    WHERE society_id = society_master.society_id
                )
             )";

            // Add duration filter
            if ($durationType == '1') {
                $startDate = date('Y-m-d', strtotime('-29 days'));
                $where .= " AND $dateField >= '$startDate'";
                $groupBy = "DATE($dateField)";
                $labelFormat = "DATE_FORMAT($dateField, '%d-%b') AS label";
            } elseif ($durationType == '2') {
                $where .= " AND $dateField >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)";
                $groupBy = "YEAR($dateField), MONTH($dateField)";
                $labelFormat = "DATE_FORMAT($dateField, '%b-%Y') AS label";
            } else {
                $groupBy = "YEAR($dateField), MONTH($dateField)";
                $labelFormat = "DATE_FORMAT($dateField, '%b-%Y') AS label";
            }

            $dataQry = $d->selectRow(
                "$labelFormat, COUNT(DISTINCT society_id) AS count",
                "society_master",
                "$where GROUP BY $groupBy",
                "ORDER BY $dateField"
            );
        } elseif ($companyType == '3') {
            // TRIAL → RENEWAL: use renewal date, only if first is trial

            $dateField = "t1.transection_date";
            $where = "t1.is_renewal = 1
        AND EXISTS (
            SELECT 1 FROM transection_master t2
            WHERE t2.society_id = t1.society_id
              AND t2.is_renewal = 2
              AND t2.transection_date = (
                  SELECT MIN(transection_date)
                  FROM transection_master t3
                  WHERE t3.society_id = t1.society_id
              )
        )";

            if ($durationType == '1') {
                $startDate = date('Y-m-d', strtotime('-29 days'));
                $where .= " AND $dateField >= '$startDate'";
                $groupBy = "DATE($dateField)";
                $labelFormat = "DATE_FORMAT($dateField, '%d-%b') AS label";
            } elseif ($durationType == '2') {
                $where .= " AND $dateField >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)";
                $groupBy = "YEAR($dateField), MONTH($dateField)";
                $labelFormat = "DATE_FORMAT($dateField, '%b-%Y') AS label";
            } else {
                $groupBy = "YEAR($dateField), MONTH($dateField)";
                $labelFormat = "DATE_FORMAT($dateField, '%b-%Y') AS label";
            }

            $dataQry = $d->selectRow(
                "$labelFormat, COUNT(DISTINCT t1.society_id) AS count",
                "transection_master t1",
                "$where GROUP BY $groupBy",
                "ORDER BY $dateField"
            );
        } elseif ($companyType == '4') {
            $comboCounts = [];

            // 1. New Registration (via society_master.created_date + first transaction is is_renewal = 0)
            $dateField1 = "sm.created_date";
            $subquery1 = "SELECT 1 FROM transection_master tm
                  WHERE tm.society_id = sm.society_id
                  AND tm.is_renewal = 0
                  AND tm.transection_date = (
                      SELECT MIN(transection_date)
                      FROM transection_master
                      WHERE society_id = sm.society_id
                  )";

            $where1 = "EXISTS ($subquery1) AND " .
            (($durationType == '1') ? "$dateField1 >= '$startDate'" :
                (($durationType == '2') ? "$dateField1 >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)" : "1"));

            $qry1 = $d->selectRow(
                ($durationType == '1'
                    ? "DATE_FORMAT($dateField1, '%d-%b') AS label"
                    : "DATE_FORMAT($dateField1, '%b-%Y') AS label") .
                ", COUNT(DISTINCT sm.society_id) AS count",
                "society_master sm",
                "$where1 GROUP BY " .
                ($durationType == '1' ? "DATE($dateField1)" : "YEAR($dateField1), MONTH($dateField1)"),
                "ORDER BY $dateField1"
            );

            if (mysqli_num_rows($qry1) > 0) {
                while ($r1 = mysqli_fetch_assoc($qry1)) {
                    $comboCounts[$r1['label']] = ((int) ($comboCounts[$r1['label']] ?? 0)) + (int) $r1['count'];
                }
            }

            // 2. Trial → Renewal (based on renewal date)
            $dateField2 = "t1.transection_date";
            $where2 = "t1.is_renewal = 1
        AND EXISTS (
            SELECT 1 FROM transection_master t2
            WHERE t2.society_id = t1.society_id
              AND t2.is_renewal = 2
              AND t2.transection_date = (
                  SELECT MIN(transection_date)
                  FROM transection_master t3
                  WHERE t3.society_id = t1.society_id
              )
        )";

            if ($durationType == '1') {
                $where2 .= " AND $dateField2 >= '$startDate'";
                $groupBy = "DATE($dateField2)";
                $labelFormat = "DATE_FORMAT($dateField2, '%d-%b') AS label";
            } elseif ($durationType == '2') {
                $where2 .= " AND $dateField2 >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)";
                $groupBy = "YEAR($dateField2), MONTH($dateField2)";
                $labelFormat = "DATE_FORMAT($dateField2, '%b-%Y') AS label";
            } else {
                $groupBy = "YEAR($dateField2), MONTH($dateField2)";
                $labelFormat = "DATE_FORMAT($dateField2, '%b-%Y') AS label";
            }

            $qry2 = $d->selectRow(
                "$labelFormat, COUNT(DISTINCT t1.society_id) AS count",
                "transection_master t1",
                "$where2 GROUP BY $groupBy",
                "ORDER BY $dateField2"
            );

            if (mysqli_num_rows($qry2) > 0) {
                while ($r2 = mysqli_fetch_assoc($qry2)) {
                    $comboCounts[$r2['label']] = ((int) ($comboCounts[$r2['label']] ?? 0)) + (int) $r2['count'];
                }
            }

            // Final population into month-wise array
            foreach ($monthYearList as $label) {
                $monthWiseRegistrationCount[$label] = $comboCounts[$label] ?? 0;
            }

            echo json_encode([
                'monthNameList' => array_values($monthYearList),
                'monthWiseRegistrationCount' => array_values($monthWiseRegistrationCount)
            ], JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);
            exit;
        }

        if (isset($dataQry) && mysqli_num_rows($dataQry) > 0) {
            while ($row = mysqli_fetch_assoc($dataQry)) {
                $label = $row['label'];
                if (isset($monthWiseRegistrationCount[$label])) {
                    $monthWiseRegistrationCount[$label] = (int) $row['count'];
                }
            }
        }

        echo json_encode([
            'monthNameList' => array_values($monthYearList),
            'monthWiseRegistrationCount' => array_values($monthWiseRegistrationCount)
        ], JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);
        exit;
    } else if (isset($_POST['getCompanyDashboardRequestData'])) {

        $monthYearRequestList = [];
        $monthWiseRegistrationRequestCount = [];

        if ($durationType == '1') {
            $startDate = date('Y-m-d', strtotime('-29 days'));
            $where = "requested_date >= '$startDate'";

            for ($i = 0; $i < 30; $i++) {
                $dayLabel = date('d-M', strtotime("-$i days"));
                $monthYearRequestList[] = $dayLabel;
            }
            $monthYearRequestList = array_reverse($monthYearRequestList);
            $monthWiseRegistrationRequestCount = array_fill_keys($monthYearRequestList, 0);

            $dataQry = $d->selectRow(
                "DATE_FORMAT(requested_date, '%d-%b') AS day_label, COUNT(request_society_id) AS count",
                "society_master_requests",
                "$where GROUP BY DATE(requested_date)",
                "ORDER BY requested_date"
            );

            if (mysqli_num_rows($dataQry) > 0) {
                while ($row = mysqli_fetch_assoc($dataQry)) {
                    $label = $row['day_label'];
                    if (isset($monthWiseRegistrationRequestCount[$label])) {
                        $monthWiseRegistrationRequestCount[$label] = (int) $row['count'];
                    }
                }
            }

        } else {
            $baseDate = date('Y-m-01');
            for ($i = 0; $i < 13; $i++) {
                $monthYearRequest = date('M-Y', strtotime("-$i months", strtotime($baseDate)));
                $monthYearRequestList[] = $monthYearRequest;
            }
            $monthYearRequestList = array_reverse($monthYearRequestList);
            $monthWiseRegistrationRequestCount = array_fill_keys($monthYearRequestList, 0);

            if ($durationType == '2') {
                $where = "requested_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)";
            } else {
                $where = "1";
            }

            $dataQry = $d->selectRow(
                "DATE_FORMAT(requested_date, '%b-%Y') AS month_year, COUNT(request_society_id) AS count",
                "society_master_requests",
                "$where GROUP BY YEAR(requested_date), MONTH(requested_date)",
                "ORDER BY requested_date"
            );

            if (mysqli_num_rows($dataQry) > 0) {
                while ($row = mysqli_fetch_assoc($dataQry)) {
                    $monthLabel = $row['month_year'];
                    if (isset($monthWiseRegistrationRequestCount[$monthLabel])) {
                        $monthWiseRegistrationRequestCount[$monthLabel] = (int) $row['count'];
                    }
                }
            }
        }

        $monthWiseRegistrationRequestData = [
            'monthNameRequestList' => array_values($monthYearRequestList),
            'monthWiseRegistrationRequestCount' => array_values($monthWiseRegistrationRequestCount),
        ];

        echo json_encode($monthWiseRegistrationRequestData, JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);
        exit;
    } else {
        header("Location: ../welcome.php");
        exit;
    }
} else {
    header("Location: ../welcome.php");
    exit;
}
