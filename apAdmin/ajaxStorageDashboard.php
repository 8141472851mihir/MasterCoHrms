<?php

include_once 'common/object.php';
header('Content-Type: application/json');

$society_id = isset($_GET['society_id']) ? $d->sanitizeReportFilterIdAsInt($_GET['society_id']) : 0;
$folder_path = isset($_GET['folder_path']) ? json_decode($_GET['folder_path'], true) : [];

function convertToMB($sizeStr)
{
    $sizeStr = trim($sizeStr);
    if (strpos($sizeStr, 'GB') !== false) {
        return round(floatval($sizeStr) * 1000, 2);
    }
    if (strpos($sizeStr, 'MB') !== false) {
        return round(floatval($sizeStr), 2);
    }
    if (strpos($sizeStr, 'KB') !== false) {
        return round(floatval($sizeStr) / 1000, 4);
    }
    if (strpos($sizeStr, 'B') !== false && $sizeStr !== '0 B') {
        return round(floatval($sizeStr) / 1000 / 1000, 6);
    }
    return 0;
}

function traverseToPath($folder, $path)
{
    foreach ($path as $segment) {
        $found = false;
        if (!empty($folder['subfolders'])) {
            foreach ($folder['subfolders'] as $sub) {
                if (isset($sub['folder']) && $sub['folder'] === $segment) {
                    $folder = $sub;
                    $found = true;
                    break;
                }
            }
        }
        if (!$found)
            break;
    }
    return $folder;
}

function extractFiles($folder, &$result)
{
    if (!empty($folder['subfolders'])) {
        foreach ($folder['subfolders'] as $sub) {
            $subResult = [];
            extractFiles($sub, $subResult);
            $result[] = [
                'type' => 'folder',
                'name' => $sub['folder'],
                'size' => convertToMB($sub['size']),
                'file_count' => $sub['file_count'] ?? 0,
                'subfolders' => $subResult
            ];
        }
    }

    if (!empty($folder['files'])) {
        foreach ($folder['files'] as $file) {
            $result[] = [
                'type' => 'file',
                'name' => $file['name'],
                'size' => convertToMB($file['size']),
                'file_count' => 1
            ];
        }
    }
}

if ($society_id > 0) {
    $result = $d->select("society_analytics_master", "society_id = $society_id");
    if ($row = mysqli_fetch_array($result)) {
        $storageData = json_decode($row['storage_data'], true);

        if (is_array($storageData)) {
            $targetFolder = traverseToPath($storageData, $folder_path);

            $mainFolder = [
                'size' => convertToMB($targetFolder['size'] ?? '0 MB'),
                'file_count' => $targetFolder['file_count'] ?? 0,
                'subfolder_count' => isset($targetFolder['subfolders']) ? count($targetFolder['subfolders']) : 0
            ];

            $files = [];
            extractFiles($targetFolder, $files);

            echo json_encode([
                'main_folder' => $mainFolder,
                'files' => $files
            ]);
            exit;
        }
    }
} else if (isset($_GET['allServersSummary'])) {
    $servers = [];
    $serverQuery = $d->select("server_master", "");
    $serverRows = [];
    while ($server = mysqli_fetch_array($serverQuery)) {
        $serverRows[] = $server;
    }

    // Prefetch all societies with server_id once
    $societiesByServer = [];
    $allSocietyIds = [];
    $societyQuery = $d->selectRow(
        "society_master.society_id,domain_master.server_id",
        "society_master LEFT JOIN domain_master ON domain_master.domain_id=society_master.domain_id",
        "1=1"
    );
    while ($society = mysqli_fetch_array($societyQuery)) {
        $sid = (int)$society['society_id'];
        $srvId = (int)$society['server_id'];
        $societiesByServer[$srvId][] = $sid;
        $allSocietyIds[] = $sid;
    }

    // Prefetch analytics for all societies once
    $storageBySociety = [];
    if (!empty($allSocietyIds)) {
        $societyIdsIn = implode(',', array_map('intval', array_unique($allSocietyIds)));
        $analyticsQuery = $d->selectRow("society_id,storage_data", "society_analytics_master", "society_id IN ($societyIdsIn)");
        while ($analytics = mysqli_fetch_array($analyticsQuery)) {
            $storageBySociety[(int)$analytics['society_id']] = $analytics['storage_data'];
        }
    }

    foreach ($serverRows as $server) {
        $server_id = (int)$server['server_id'];
        $server_name = $server['server_name'];
        $total_size = 0;
        $total_files = 0;
        $total_subfolders = 0;

        foreach ($societiesByServer[$server_id] ?? [] as $society_id) {
            if (!isset($storageBySociety[$society_id])) {
                continue;
            }
            $mainFolder = json_decode($storageBySociety[$society_id], true);
            if (is_array($mainFolder)) {
                $total_size += convertToMB($mainFolder['size'] ?? '0 MB');
                $total_files += intval($mainFolder['file_count'] ?? 0);
            }
        }
        $servers[] = [
            'server_name' => $server_name,
            'total_size' => ($total_size > 1000 ? round($total_size / 1000, 2) . ' GB' : round($total_size, 2) . ' MB'),
            'total_files' => $total_files,
            'total_subfolders' => $total_subfolders
        ];
    }
    echo json_encode($servers);
    exit;
}
echo json_encode([
    'main_folder' => [
        'size' => '0 MB',
        'file_count' => 0,
        'subfolder_count' => 0
    ],
    'files' => []
]);
exit;
