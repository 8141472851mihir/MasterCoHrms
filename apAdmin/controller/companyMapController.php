<?php 
include '../common/objectController.php';

if(isset($_POST) && !empty($_POST)) {
    
    if(isset($_POST['action']) && $_POST['action'] == "getCompanies" && $_POST['csrf'] == $_SESSION['token']) {
        
        try {
            // Get all companies with their location data
            $result = $d->selectRow(
                "sm.society_id, sm.society_name, sm.society_address, sm.society_latitude, sm.society_longitude, sm.society_status, sm.secretary_name, sm.secretary_mobile, sm.secretary_email, sm.country_id, sm.state_id, sm.city_id, c.name as country_name, s.name as state_name, ct.name as city_name, sm.created_date, sm.plan_expire_date",
                "society_master sm LEFT JOIN countries c ON sm.country_id = c.country_id LEFT JOIN states s ON sm.state_id = s.state_id LEFT JOIN cities ct ON sm.city_id = ct.city_id",
                "sm.society_status IN (0, 1) AND sm.is_demo_society=0",
                "ORDER BY sm.society_name ASC"
            );
            
            if($result && mysqli_num_rows($result) > 0) {
                $companies = array();
                while($row = mysqli_fetch_assoc($result)) {
                    // Decrypt sensitive data if needed
                    $row['secretary_mobile'] = $d->encryptDecrypt("decrypt", $row['secretary_mobile']);
                    $row['secretary_email'] = $d->encryptDecrypt("decrypt", $row['secretary_email']);
                    
                    $companies[] = $row;
                }
                
                $response = array(
                    'status' => 'success',
                    'message' => 'Companies retrieved successfully',
                    'data' => $companies,
                    'total_count' => count($companies)
                );
            } else {
                $response = array(
                    'status' => 'success',
                    'message' => 'No companies found',
                    'data' => array(),
                    'total_count' => 0
                );
            }
            
        } catch (Exception $e) {
            $response = array(
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage(),
                'data' => array()
            );
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    if(isset($_POST['action']) && $_POST['action'] == "getCompanyDetails" && $_POST['csrf'] == $_SESSION['token']) {
        
        $society_id = isset($_POST['society_id']) ? $d->sanitizeActionIdAsInt($_POST['society_id']) : 0;
        
        if(empty($society_id)) {
            $response = array(
                'status' => 'error',
                'message' => 'Society ID is required'
            );
        } else {
            try {
                $result = $d->selectRow(
                    "sm.society_id, sm.society_name, sm.society_address, sm.society_latitude, sm.society_longitude, sm.society_status, sm.secretary_name, sm.secretary_mobile, sm.secretary_email, sm.country_id, sm.state_id, sm.city_id, sm.society_pincode, sm.sub_domain, sm.plan_expire_date, sm.created_date, c.name as country_name, s.name as state_name, ct.name as city_name",
                    "society_master sm LEFT JOIN countries c ON sm.country_id = c.country_id LEFT JOIN states s ON sm.state_id = s.state_id LEFT JOIN cities ct ON sm.city_id = ct.city_id",
                    "sm.society_id = '$society_id'"
                );
                
                if($result && mysqli_num_rows($result) > 0) {
                    $company = mysqli_fetch_assoc($result);
                    
                    // Decrypt sensitive data
                    $company['secretary_mobile'] = $d->encryptDecrypt("decrypt", $company['secretary_mobile']);
                    $company['secretary_email'] = $d->encryptDecrypt("decrypt", $company['secretary_email']);
                    
                    $response = array(
                        'status' => 'success',
                        'message' => 'Company details retrieved successfully',
                        'data' => $company
                    );
                } else {
                    $response = array(
                        'status' => 'error',
                        'message' => 'Company not found'
                    );
                }
                
            } catch (Exception $e) {
                $response = array(
                    'status' => 'error',
                    'message' => 'Database error: ' . $e->getMessage()
                );
            }
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    if(isset($_POST['action']) && $_POST['action'] == "updateCompanyLocation" && $_POST['csrf'] == $_SESSION['token']) {
        
        $society_id = isset($_POST['society_id']) ? $d->sanitizeActionIdAsInt($_POST['society_id']) : 0;
        $latitude = isset($_POST['latitude']) ? $_POST['latitude'] : '';
        $longitude = isset($_POST['longitude']) ? $_POST['longitude'] : '';
        
        if(empty($society_id) || empty($latitude) || empty($longitude)) {
            $response = array(
                'status' => 'error',
                'message' => 'Society ID, latitude and longitude are required'
            );
        } else {
            try {
                $updateData = array(
                    'society_latitude' => $latitude,
                    'society_longitude' => $longitude,
                    'updated_date' => date('Y-m-d H:i:s')
                );
                
                $result = $d->update("society_master", $updateData, "society_id = '$society_id'");
                
                if($result > 0) {
                    // Log the action
                    $d->insert_log("$society_id", "$bms_admin_id", "$created_by", "Company location updated");
                    
                    $response = array(
                        'status' => 'success',
                        'message' => 'Company location updated successfully'
                    );
                } else {
                    $response = array(
                        'status' => 'error',
                        'message' => 'Failed to update company location'
                    );
                }
                
            } catch (Exception $e) {
                $response = array(
                    'status' => 'error',
                    'message' => 'Database error: ' . $e->getMessage()
                );
            }
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    if(isset($_POST['action']) && $_POST['action'] == "getMapStatistics" && $_POST['csrf'] == $_SESSION['token']) {
        
        try {
            // Get total companies
            $totalResult = $d->selectRow("COUNT(*) as total", "society_master", "society_status IN (0, 1) AND sm.is_demo_society=0");
            $totalCompanies = mysqli_fetch_assoc($totalResult)['total'];
            
            // Get active companies
            $activeResult = $d->selectRow("COUNT(*) as active", "society_master", "society_status = 0 AND sm.is_demo_society=0");
            $activeCompanies = mysqli_fetch_assoc($activeResult)['active'];
            
            // Get companies with location
            $withLocationResult = $d->selectRow("COUNT(*) as with_location", "society_master", "society_status IN (0, 1) AND society_latitude IS NOT NULL AND society_longitude IS NOT NULL AND society_latitude != '0' AND society_longitude != '0' AND sm.is_demo_society=0");
            $withLocation = mysqli_fetch_assoc($withLocationResult)['with_location'];
            
            // Get companies without location
            $withoutLocation = $totalCompanies - $withLocation;
            
            // Get companies by country
            $countryResult = $d->selectRow("c.name as country_name, COUNT(sm.society_id) as company_count", "society_master sm LEFT JOIN countries c ON sm.country_id = c.country_id", "sm.society_status IN (0, 1) AND sm.is_demo_society=0", "GROUP BY sm.country_id, c.name ORDER BY company_count DESC LIMIT 10");
            $countries = array();
            while($row = mysqli_fetch_assoc($countryResult)) {
                $countries[] = $row;
            }
            
            $response = array(
                'status' => 'success',
                'message' => 'Statistics retrieved successfully',
                'data' => array(
                    'total_companies' => $totalCompanies,
                    'active_companies' => $activeCompanies,
                    'inactive_companies' => $totalCompanies - $activeCompanies,
                    'with_location' => $withLocation,
                    'without_location' => $withoutLocation,
                    'countries' => $countries
                )
            );
            
        } catch (Exception $e) {
            $response = array(
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage()
            );
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    if(isset($_POST['action']) && $_POST['action'] == "searchCompanies" && $_POST['csrf'] == $_SESSION['token']) {
        
        $searchTerm = isset($_POST['search_term']) ? $_POST['search_term'] : '';
        $countryFilter = isset($_POST['country_filter']) ? $d->sanitizeActionIdAsInt($_POST['country_filter']) : 0;
        $statusFilter = isset($_POST['status_filter']) ? $_POST['status_filter'] : '';
        
        try {
            $whereConditions = array("sm.society_status IN (0, 1) AND sm.is_demo_society=0");
            
            if(!empty($searchTerm)) {
                $searchTerm = $d->escapeSqlLike($searchTerm);
                $whereConditions[] = "(sm.society_name LIKE '%$searchTerm%' OR sm.society_address LIKE '%$searchTerm%')";
            }
            
            if($countryFilter > 0) {
                $whereConditions[] = "sm.country_id = '$countryFilter'";
            }
            
            if($statusFilter !== '' && in_array((string)$statusFilter, ['0', '1'], true)) {
                $whereConditions[] = "sm.society_status = '$statusFilter'";
            }
            
            $whereClause = implode(" AND ", $whereConditions);
            
            $result = $d->selectRow(
                "sm.society_id, sm.society_name, sm.society_address, sm.society_latitude, sm.society_longitude, sm.society_status, sm.secretary_name, sm.secretary_mobile, sm.secretary_email, sm.country_id, sm.state_id, sm.city_id, c.name as country_name, s.name as state_name, ct.name as city_name",
                "society_master sm LEFT JOIN countries c ON sm.country_id = c.country_id LEFT JOIN states s ON sm.state_id = s.state_id LEFT JOIN cities ct ON sm.city_id = ct.city_id",
                $whereClause,
                "ORDER BY sm.society_name ASC"
            );
            
            if($result && mysqli_num_rows($result) > 0) {
                $companies = array();
                while($row = mysqli_fetch_assoc($result)) {
                    // Decrypt sensitive data
                    $row['secretary_mobile'] = $d->encryptDecrypt("decrypt", $row['secretary_mobile']);
                    $row['secretary_email'] = $d->encryptDecrypt("decrypt", $row['secretary_email']);
                    
                    $companies[] = $row;
                }
                
                $response = array(
                    'status' => 'success',
                    'message' => 'Search completed successfully',
                    'data' => $companies,
                    'total_count' => count($companies)
                );
            } else {
                $response = array(
                    'status' => 'success',
                    'message' => 'No companies found matching the criteria',
                    'data' => array(),
                    'total_count' => 0
                );
            }
            
        } catch (Exception $e) {
            $response = array(
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage(),
                'data' => array()
            );
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
} else {
    header('location:../login');
}

// Add global error handling
if (!function_exists('handleError')) {
    function handleError($e) {
        $response = array(
            'status' => 'error',
            'message' => 'System error: ' . $e->getMessage(),
            'data' => array()
        );
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}
?>
