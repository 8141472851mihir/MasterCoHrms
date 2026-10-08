<?php
include_once 'common/object.php';

$society_id = isset($_GET['society_id']) ? $d->sanitizeReportFilterIdAsInt($_GET['society_id']) : 0;
if ($society_id <= 0) {
    echo "<div class='alert alert-danger'>Invalid Company ID.</div>";
    exit;
}

$societyQuery = $d->selectRow(
    "society_master.*, states.state_id, states.name as state_name,
    countries.country_id, countries.name as country_name,
    domain_master.domain_name, domain_master.domain_id,
    manage_plan.plan_value, manage_plan.plan_name,
    bms_admin_master.admin_id,bms_admin_master.admin_name as refund_person_name,
    business_entity_master.b_id,business_entity_master.name AS industry_type_name,
    server_master.server_name,server_master.server_ip",
    "society_master 
     LEFT JOIN states ON states.state_id = society_master.state_id
     LEFT JOIN domain_master ON domain_master.domain_id = society_master.domain_id
     LEFT JOIN countries ON countries.country_id = society_master.country_id
     LEFT JOIN manage_plan ON manage_plan.plan_value = society_master.package_id
     LEFT JOIN bms_admin_master ON bms_admin_master.admin_id = society_master.refund_person
     LEFT JOIN business_entity_master ON business_entity_master.b_id = society_master.industry_type
     LEFT JOIN server_master ON server_master.server_id=domain_master.server_id",
    "society_master.society_id = $society_id"
);
?>

<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <?php if ($societyQuery && mysqli_num_rows($societyQuery) > 0) {
                    while ($society = mysqli_fetch_assoc($societyQuery)) { ?>

                        <?php
                        $query = $d->select("role_master", "role_id = 1");
                        if ($query && mysqli_num_rows($query) > 0) {
                        ?>
                            <div class="card shadow-sm bg-white mb-4">
                                <div class="card-header bg-primary py-2 px-2">
                                    <h5 class="mb-0 fw-bold text-white">Company Profile</h5>
                                </div>
                                <div class="card-body  py-1">
                                    <div class="row mb-2 py-0">
                                        <div class="col-md-6 pl-md-3">
                                            <div class="row">
                                                <div class="col-5 fw-bold text-dark px-1">Company Name
                                                    <span class="float-right">:</span>
                                                </div>
                                                <div class="col-7 px-2">
                                                    <a href="<?php echo $society['sub_domain']; ?>apAdmin/"
                                                        target="_blank"><?php echo $society['society_name']; ?></a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 pl-md-3 border-left">
                                            <div class="row">
                                                <div class="col-5 fw-bold text-dark px-1">Server Name
                                                    <span class="float-right">:</span>
                                                </div>
                                                <div class="col-7 px-2">
                                                    <a target="_blank"
                                                        href="http://<?php echo $society['server_ip']; ?>/phpmyadmin"><?php echo $society['server_ip']; ?></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="card shadow-sm bg-white mb-4">
                            <div class="card-header bg-primary py-2 px-2">
                                <h5 class="mb-0 fw-bold text-white">Company Profile Details</h5>
                            </div>
                            <div class="card-body  py-1">
                                <div class="row mb-2 py-0">
                                    <div class="col-md-6 pl-md-3">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Company Name
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?php echo $society['society_name']; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3 border-left">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Country Name
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2"><?= $society['country_name'] ?></div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 pl-md-3">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">State
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2"><?= $society['state_name'] ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3 border-left">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">City
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2"><?= $society['city_name'] ?></div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 pl-md-3">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Address
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2"><?= $society['society_address'] ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3 border-left">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Email
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2"><?= $society['secretary_email'] ?></div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 pl-md-3">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Mobile Number
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2"><?= $society['country_code'] ?>
                                                <?= $society['secretary_mobile'] ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3 border-left">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Created Date
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?= isset($society['created_date']) ? date('d F Y, h:i A', strtotime($society['created_date'])) : '' ?>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>


                        <div class="card shadow-sm bg-white mb-4">
                            <div class="card-header bg-primary py-2 px-2">
                                <h5 class="mb-0 fw-bold text-white">Company Information</h5>
                            </div>
                            <div class="card-body  py-1">
                                <div class="row mb-2 py-0">
                                    
                                    <div class="col-md-6 pl-md-3 border-left">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Account Type
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?= isset($society['account_type'])
                                                    ? ($society['account_type'] == 1 ? 'Key Account' : 'Normal Account')
                                                    : '' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Company Full Name
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2"><?= $society['company_full_name'] ?></div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 pl-md-3">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Company Latitude
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2"><?= $society['society_latitude'] ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3 border-left">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Company Longitude
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2"><?= $society['society_longitude'] ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">
                                                Company Logo <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?php if (!empty($society['socieaty_logo'])): ?>
                                                    <img src="<?= $society['socieaty_logo'] ?>" alt="Company Logo"
                                                        style="max-height: 100px; max-width: 100%; height: auto; width: auto;">
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 pl-md-3 border-left">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Company Status
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?= isset($society['society_status'])
                                                    ? ($society['society_status'] == 1 ? 'Deactive' : 'Active')
                                                    : '' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Created On Company Server
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?=
                                                $society['created_on_society_server'] == 1 ? 'Yes' : 'No'
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3 border-left">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Demo Company
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?=
                                                $society['is_demo_society'] == 1 ? 'Yes' : 'No'
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Tracking Status
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?= isset($society['tracking_status'])
                                                    ? ($society['tracking_status'] == 1 ? 'Yes' : 'No')
                                                    : '' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3 border-left">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Expected Team Size
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2"><?= $society['expected_team_size'] ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Reference From
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2"><?= $society['reference_from'] ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3 border-left">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Implementation Name
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2"><?= $society['implementation_name'] ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">GST No
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2"><?= $society['gst_no'] ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3 border-left">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Distance Get Type
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?= isset($society['distance_get_type'])
                                                    ? ($society['distance_get_type'] == 0
                                                        ? 'Google'
                                                        : ($society['distance_get_type'] == 1
                                                            ? 'Distancematrix'
                                                            : ($society['distance_get_type'] == 2
                                                                ? 'Here'
                                                                : 'Graphhopper')))
                                                    : '' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3 border-left">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Visit Calculation Method
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?php
                                                $methods = [
                                                    0 => 'Entire Day',
                                                    1 => 'Visit to Visit'
                                                ];
                                                echo isset($society['visit_calculation_method']) && isset($methods[$society['visit_calculation_method']])
                                                    ? $methods[$society['visit_calculation_method']]
                                                    : '';
                                                ?>
                                            </div>

                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 pl-md-3 border-left">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Allow Institute
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?= isset($society['allow_institute'])
                                                    ? ($society['allow_institute'] == 1 ? 'Yes' : 'No')
                                                    : '' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3">
                                        <div class="row">

                                            <div class="col-5 fw-bold text-dark px-1">Institute Username
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2"><?= $society['institute_username'] ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3 border-left">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Company Rating
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2"><?= $society['society_rating'] ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3 border-left">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Lead Sources
                                                <span class="float-right">:</span>
                                            </div>
                                            <?php
                                            $leadSources = [
                                                1 => 'Meta',
                                                2 => 'Inbound',
                                                3 => 'Walk IN',
                                                4 => 'Cold Data',
                                                5 => 'BA / Director Reference',
                                                6 => 'BNI Reference',
                                                7 => 'Event - Exhibitor',
                                                8 => 'Event - Exhibitor ( Exhibitor Cards )',
                                                9 => 'Event - Exhibitor ( Visitor Cards )',
                                                10 => 'Event - Industry Specific',
                                                11 => 'Event - Networking',
                                                12 => 'Existing Client',
                                                13 => 'Nikseam BPO',
                                                14 => 'Old Lead ( Any Source)',
                                                15 => 'Personal Reference',
                                                16 => 'Reference from demo client',
                                                17 => 'Reference from existing client',
                                                18 => 'Reference from Implementation team',
                                                19 => 'Rise',
                                                20 => 'Tech Imply',
                                                21 => 'Tech Jockey',
                                                22 => 'Website / Landing Page',
                                                23 => 'Website / Landing Page / ChatBot /MyCo App',
                                                24 => 'Whatsapp Bulkshoot'
                                            ];
                                            ?>
                                            <div class="col-7 px-2"><?= $leadSources[$society['lead_sources']] ?? ''; ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3 border-left">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Region
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2"><?= $society['region_name'] ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3 border-left">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Search Company Code
                                                <span class="float-right">:</span>
                                            </div>
                                            <?php
                                            if ($society['search_society_code'] == 0) {
                                                $society_search_code = "No";
                                            } else {
                                                $society_search_code = "Yes";
                                            }
                                            ?>
                                            <div class="col-7 px-2"><?= $society_search_code ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3 border-left">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Company Code
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2"><?= $society['society_code'] ?></div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>


                        <div class="card shadow-sm bg-white mb-4">
                            <div class="card-header bg-primary py-2 px-2">
                                <h5 class="mb-0 fw-bold text-white">Admin & Package Information</h5>
                            </div>
                            <div class="card-body  py-1">
                                <div class="row mb-2 py-0">
                                    <div class="col-md-6 pl-md-3">
                                        <div class="row">

                                            <div class="col-5 fw-bold text-dark px-1">Admin Name
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2"><?= $society['secretary_name'] ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3 border-left">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Package Name
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2"><?= $society['plan_name'] ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Trial Days
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2"><?= $society['trial_days'] ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3 border-left">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Employee Tracking Limit
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2"><?= $society['employee_tracking_limit'] ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3">
                                        <div class="row">

                                            <div class="col-5 fw-bold text-dark px-1">Employee Registration Limit
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2"><?= $society['employee_registration_limit'] ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3 border-left">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Per Employee Price
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2"><?= $society['per_employee_price'] ?></div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>


                        <div class="card shadow-sm bg-white mb-4">
                            <div class="card-header bg-primary py-2 px-2">
                                <h5 class="mb-0 fw-bold text-white">Application Settings</h5>
                            </div>
                            <div class="card-body  py-1">
                                <div class="row mb-2 py-0">
                                    <div class="col-md-6 pl-md-3">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Currency
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2"><?= $society['currency'] ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3 border-left">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Group Chat Status
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?= isset($society['group_chat_status'])
                                                    ? ($society['group_chat_status'] == 1 ? 'Deactive' : 'Active')
                                                    : '' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Visitor
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?= isset($society['visitor_on_off'])
                                                    ? ($society['visitor_on_off'] == 1 ? 'Off' : 'On')
                                                    : '' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3 border-left">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Web AI Status
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?= isset($society['web_ai_status'])
                                                    ? ($society['web_ai_status'] == 1 ? 'On' : 'Off')
                                                    : 'Off' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Mobile AI Status
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?= isset($society['ai_status'])
                                                    ? ($society['ai_status'] == 1 ? 'On' : 'Off')
                                                    : 'Off' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3 border-left">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Resident
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?= isset($society['resident_in_out'])
                                                    ? ($society['resident_in_out'] == 1 ? 'Active' : 'Hide')
                                                    : '' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Screenshot Capture In Timeline
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?= isset($society['screen_sort_capture_in_timeline'])
                                                    ? ($society['screen_sort_capture_in_timeline'] == 1 ? 'On' : 'Off')
                                                    : '' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3 border-left">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Visitor Mobile Number Show Gatekeeper
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?= isset($society['visitor_mobile_number_show_gatekeeper'])
                                                    ? ($society['visitor_mobile_number_show_gatekeeper'] == 1 ? 'Yes' : 'No')
                                                    : '' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Create Group
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?= isset($society['create_group'])
                                                    ? ($society['create_group'] == 1 ? 'Yes' : 'No')
                                                    : '' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3 border-left">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Tenant Registration
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?= isset($society['tenant_registration'])
                                                    ? ($society['tenant_registration'] == 1 ? 'Yes' : 'No')
                                                    : '' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Registration Request From App
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?= isset($society['registration_request_from_app'])
                                                    ? ($society['registration_request_from_app'] == 1 ? 'Off' : 'On')
                                                    : '' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3 border-left">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Entry All Visitor Group
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?= isset($society['entry_all_visitor_group'])
                                                    ? ($society['entry_all_visitor_group'] == 1 ? 'Yes' : 'No')
                                                    : '' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Event Scanner For Gatekeeper
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?= isset($society['event_scanner_for_gatekeeper'])
                                                    ? ($society['event_scanner_for_gatekeeper'] == 1 ? 'No' : 'Yes')
                                                    : '' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3 border-left">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Member Document Access
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?= isset($society['member_document_access'])
                                                    ? ($society['member_document_access'] == 1 ? 'No' : 'Yes')
                                                    : '' ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow-sm bg-white mb-4">
                            <div class="card-header bg-primary py-2 px-2">
                                <h5 class="mb-0 fw-bold text-white">App Configuration</h5>
                            </div>
                            <div class="card-body  py-1">
                                <div class="row mb-2 py-0">
                                    <div class="col-md-6 pl-md-3">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Splash Image
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">

                                                <?php if (!empty($society['splash_image'])): ?>
                                                    <img src="<?= $society['splash_image'] ?>" alt="Splash Image"
                                                        style="max-height: 100px;">
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3 border-left">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Splash Color
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2"><?= $society['splash_colour'] ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Visible In Search List
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?= isset($society['visible_in_search_list'])
                                                    ? ($society['visible_in_search_list'] == 1 ? 'No' : 'Yes')
                                                    : '' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3 border-left">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Hide Branch
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?= isset($society['hide_branch'])
                                                    ? ($society['hide_branch'] == 1 ? 'Yes' : 'No')
                                                    : '' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">App Url Ios
                                                <span class="float-right">:</span>
                                            </div>

                                            <div class="col-7 px-2">
                                                <?php if (!empty($society['app_url_ios'])): ?>
                                                    <a href="<?= $society['app_url_ios'] ?>" target="_blank">
                                                        <?= $society['app_url_ios'] ?>
                                                    </a>
                                                <?php else: ?>

                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 pl-md-3 border-left">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">App Url Android
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?php if (!empty($society['app_url_android'])): ?>
                                                    <a href="<?= $society['app_url_android'] ?>" target="_blank">
                                                        <?= $society['app_url_android'] ?>
                                                    </a>
                                                <?php else: ?>

                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Login Via
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?= isset($society['login_via'])
                                                    ? ($society['login_via'] == 1 ? 'Email' : 'Phone')
                                                    : '' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3 border-left">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Google Login
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?= isset($society['google_login'])
                                                    ? ($society['google_login'] == 0 ? 'Yes' : 'No')
                                                    : '' ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow-sm bg-white mb-4">
                            <div class="card-header bg-primary py-2 px-2">
                                <h5 class="mb-0 fw-bold text-white">Attendance & Visit Settings</h5>
                            </div>
                            <div class="card-body  py-1">
                                <div class="row mb-2 py-0">
                                    <div class="col-md-6 pl-md-3">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Start Visit With Otp
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?=
                                                $society['start_visit_with_otp'] == 1 ? 'Yes' : 'No'
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3 border-left">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Attendance Map Type
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?= isset($society['attendance_map_type'])
                                                    ? ($society['attendance_map_type'] == 0
                                                        ? 'For Out Of Range Visible With Button'
                                                        : ($society['attendance_map_type'] == 1
                                                            ? 'For No Map Visible'
                                                            : ($society['attendance_map_type'] == 2
                                                                ? 'For Always Visible'
                                                                : 'For Out Of Range Visible')))
                                                    : '' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Visit Map Type
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?= isset($society['visit_map_type'])
                                                    ? ($society['visit_map_type'] == 0
                                                        ? 'For Out Of Range Visible With Button'
                                                        : ($society['visit_map_type'] == 1
                                                            ? 'For No Map Visible'
                                                            : ($society['visit_map_type'] == 2
                                                                ? 'For Always Visible'
                                                                : 'For Out Of Range Visible')))
                                                    : '' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3 border-left">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Clear Local Save Attendance Data FaceApp
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?= $society['clear_local_save_attendance_data_face_app'] ?><?= " Days" ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>

                        <div class="card shadow-sm bg-white mb-4">
                            <div class="card-header bg-primary py-2 px-2">
                                <h5 class="mb-0 fw-bold text-white">Support Information</h5>
                            </div>
                            <div class="card-body  py-1">
                                <div class="row mb-2 py-0">
                                    <div class="col-md-6 pl-md-3">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Support Name
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2"><?= $society['support_name'] ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3 border-left">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Support Mobile Number
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2"><?= $society['support_country_code'] ?>
                                                <?= $society['support_mobile_no'] ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card shadow-sm bg-white mb-4">
                            <div class="card-header bg-primary py-2 px-2">
                                <h5 class="mb-0 fw-bold text-white">Ticket & Industry Info</h5>
                            </div>
                            <div class="card-body  py-1">
                                <div class="row mb-2 py-0">
                                    <div class="col-md-6 pl-md-3">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Industry Type
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2"><?= $society['industry_type_name'] ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3 border-left">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Yearly Ticket Size
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2"><?= $society['yearly_ticket_size'] ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Received Ticket Size
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2"><?= $society['received_ticket_size'] ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3 border-left">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Calender Type
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?= isset($society['calender_type'])
                                                    ? ($society['calender_type'] == 1 ? 'Financially' : 'Yearly')
                                                    : '' ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow-sm bg-white mb-4">
                            <div class="card-header bg-primary py-2 px-2">
                                <h5 class="mb-0 fw-bold text-white">Followup Details</h5>
                            </div>
                            <div class="card-body  py-1">
                                <div class="row mb-2 py-0">
                                    <div class="col-md-6 pl-md-3">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Last Call Date
                                                <span class="float-right">:</span>
                                            </div>
                                            <?= !empty($society['last_call_date']) ? date('d F Y', strtotime($society['last_call_date'])) : '' ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3 border-left">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Last Call Remark
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2"><?= $society['last_call_remark'] ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Followup Date
                                                <span class="float-right">:</span>
                                            </div>
                                            <?= !empty($society['follow_up_date']) ? date('d F Y', strtotime($society['follow_up_date'])) : '' ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow-sm bg-white mb-4">
                            <div class="card-header bg-primary py-2 px-2">
                                <h5 class="mb-0 fw-bold text-white">Domain</h5>
                            </div>
                            <div class="card-body  py-1">
                                <div class="row mb-2 py-0">
                                    <div class="col-md-6 pl-md-3">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Domain
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?php if (!empty($society['domain_name'])): ?>
                                                    <a href="<?= $society['domain_name'] ?>" target="_blank">
                                                        <?= $society['domain_name'] ?>
                                                    </a>
                                                <?php else: ?>

                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3 border-left">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Sub Domain
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?php if (!empty($society['sub_domain'])): ?>
                                                    <a href="<?= $society['sub_domain'] ?>" target="_blank">
                                                        <?= $society['sub_domain'] ?>
                                                    </a>
                                                <?php else: ?>

                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow-sm bg-white mb-4">
                            <div class="card-header bg-primary py-2 px-2">
                                <h5 class="mb-0 fw-bold text-white">CRM</h5>
                            </div>
                            <div class="card-body  py-1">
                                <div class="row mb-2 py-0">
                                    <div class="col-md-6 pl-md-3">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Crm Limit
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2"><?= $society['crm_limit'] ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3 border-left">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Crm Created
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?=
                                                $society['crm_created'] == 1 ? 'Yes' : 'No'
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Crm Created By
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2"><?= $society['crm_created_by'] ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3 border-left">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Crm Created Date
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?= isset($society['crm_created_date']) ? date('d F Y, h:i A', strtotime($society['crm_created_date'])) : '' ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <!-- Setup & Onboarding -->
                        <div class="card shadow-sm bg-white mb-4">
                            <div class="card-header bg-primary py-2 px-2">
                                <h5 class="mb-0 fw-bold text-white">Setup & Onboarding</h5>
                            </div>
                            <div class="card-body  py-1">
                                <div class="row mb-2 py-0">
                                    <div class="col-md-6 pl-md-3">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Data Onboarding
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?= ($society['is_data_onboarding'] == 2
                                                    ? 'Yes'
                                                    : ($society['is_data_onboarding'] == 1
                                                        ? 'Pending'
                                                        : 'No')) ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3 border-left">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Data Onboarding Date
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?= isset($society['data_onboarding_date']) ? date('d F Y, h:i A', strtotime($society['data_onboarding_date'])) : '' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Setup Training Status
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?=
                                                $society['setup_training_status'] == 1 ? 'Completed' : 'Pending'
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3 border-left">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Setup Data Receive Status
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?=
                                                $society['setup_data_receive_status'] == 1 ? 'Completed' : 'Pending'
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Setup Onboarding Status
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?=
                                                $society['setup_onboarding_status'] == 1 ? 'Completed' : 'Pending'
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Training Info -->
                        <div class="card shadow-sm bg-white mb-4">
                            <div class="card-header bg-primary py-2 px-2">
                                <h5 class="mb-0 fw-bold text-white">Training Information</h5>
                            </div>
                            <div class="card-body  py-1">
                                <div class="row mb-2">
                                    <div class="col-md-6 pl-md-3">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Training Status
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?=
                                                $society['training_status'] == 1 ? 'Completed' : 'Pending'
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3 border-left">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Training Completion Date
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?= isset($society['training_completion_date']) ? date('d F Y, h:i A', strtotime($society['training_completion_date'])) : '' ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow-sm bg-white mb-4">
                            <div class="card-header bg-primary py-2 px-2">
                                <h5 class="mb-0 fw-bold text-white">Synced Data</h5>
                            </div>
                            <div class="card-body  py-1">
                                <div class="row mb-2">
                                    <div class="col-md-6 pl-md-3">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Slab Synced
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?=
                                                $society['slab_synced'] == 1 ? 'Yes' : 'No'
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3 border-left">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Leave Synced
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?=
                                                $society['leave_synced'] == 1 ? 'Yes' : 'No'
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Holiday Synced
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?=
                                                $society['holiday_created'] == 1 ? 'Yes' : 'No' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3 border-left">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Expense Synced
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?=
                                                $society['expense_created'] == 1 ? 'Yes' : 'No'
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Sales & Other Info -->
                        <div class="card shadow-sm bg-white mb-4">
                            <div class="card-header bg-primary py-2 px-2">
                                <h5 class="mb-0 fw-bold text-white">Sales Data</h5>
                            </div>
                            <div class="card-body  py-1">
                                <div class="row mb-2 py-0">
                                    <div class="col-md-6 pl-md-3">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Sales Person
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2"><?= $society['sales_person_name'] ?></div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 pl-md-3 border-left">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Sales Closure Date
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?= !empty($society['sales_closure_date']) ? date('d F Y', strtotime($society['sales_closure_date'])) : '' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Plan Expiry Date
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?= !empty($society['plan_expire_date']) ? date('d F Y', strtotime($society['plan_expire_date'])) : '' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3 border-left">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Last Renew Date
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?= !empty($society['last_renew_date']) ? date('d F Y', strtotime($society['last_renew_date'])) : '' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Welcome Email Sent
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2"> <?= $society['is_welcome_email_send'] == 1 ? 'Yes' : 'No'
                                                                        ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3 border-left">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Welcome Email Sent Date
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?= isset($society['welcome_email_send_date']) ? date('d F Y, h:i A', strtotime($society['welcome_email_send_date'])) : '' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Whatsapp Group Created
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2"> <?= isset($society['is_whatsapp_group_created'])
                                                                            ? ($society['is_whatsapp_group_created'] == 1 ? 'Yes' : 'No')
                                                                            : '' ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3 border-left">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Whatsapp Group Created Date
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?= isset($society['whatsapp_group_created_date']) ? date('d F Y, h:i A', strtotime($society['whatsapp_group_created_date'])) : '' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-3">
                                        <div class="row">
                                            <div class="col-5 fw-bold text-dark px-1">Refund Status
                                                <span class="float-right">:</span>
                                            </div>
                                            <div class="col-7 px-2">
                                                <?= $society['refund_status'] == 1 ? 'Yes' : 'No' ?>
                                            </div>
                                        </div>
                                    </div>


                                    <?php if (isset($society['refund_status']) && $society['refund_status'] == 1): ?>
                                        <div class="col-md-6 pl-md-3 border-left">
                                            <div class="row">
                                                <div class="col-5 fw-bold text-dark px-1">Refund Amount
                                                    <span class="float-right">:</span>
                                                </div>
                                                <div class="col-7 px-2"><?= $society['refund_amount'] ?></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 pl-md-3">
                                            <div class="row">
                                                <div class="col-5 fw-bold text-dark px-1">Refund Description
                                                    <span class="float-right">:</span>
                                                </div>
                                                <div class="col-7 px-2"><?= $society['refund_description'] ?></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 pl-md-3 border-left">
                                            <div class="row">
                                                <div class="col-5 fw-bold text-dark px-1">Refund Person Name
                                                    <span class="float-right">:</span>
                                                </div>
                                                <div class="col-7 px-2"><?= $society['refund_person_name'] ?></div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php
                        // Menu-wise usage (society_analytics_master)
                        $analyticsRow = null;
                        $analyticsQry = $d->selectRow("*", "society_analytics_master", "society_id = $society_id");
                        if ($analyticsQry) {
                            $analyticsRow = mysqli_fetch_assoc($analyticsQry);
                        }
                        ?>
                        <div class="card shadow-sm bg-white mb-4">
                            <div class="card-header bg-primary py-2 px-2">
                                <h5 class="mb-0 fw-bold text-white">Menu-wise Usage</h5>
                            </div>
                            <div class="card-body py-2 px-2">
                                <?php if ($analyticsRow) { ?>
                                    <?php
                                    $menuMap = [
                                        'Attendance' => (int)($analyticsRow['total_attendace'] ?? 0),
                                        'Work Report' => (int)($analyticsRow['total_work_report'] ?? 0),
                                        'Payroll' => (int)($analyticsRow['total_salary_slip'] ?? 0),
                                        'Leaves' => (int)($analyticsRow['total_leaves'] ?? 0),
                                        'Complaints' => (int)($analyticsRow['total_complains'] ?? 0),
                                        'Notice Board' => (int)($analyticsRow['total_notice_board'] ?? 0),
                                        'Events' => (int)($analyticsRow['total_events'] ?? 0),
                                        'Visitors' => (int)($analyticsRow['total_visitors'] ?? 0),
                                        'Timeline' => (int)($analyticsRow['total_timeline_post'] ?? 0),
                                        'Chats' => (int)($analyticsRow['total_chat_msg'] ?? 0),
                                        'Polls' => (int)($analyticsRow['total_polls'] ?? 0),
                                        'Documents' => (int)($analyticsRow['total_document'] ?? 0),
                                        'My Requests' => (int)($analyticsRow['total_my_request'] ?? 0),
                                    ];
                                    $maxVal = 0;
                                    foreach ($menuMap as $v) { if ($v > $maxVal) { $maxVal = $v; } }
                                    $topModules = [];
                                    if ($maxVal > 0) {
                                        foreach ($menuMap as $k => $v) { if ($v === $maxVal) { $topModules[] = $k; } }
                                    }
                                    ?>
                                    <?php if ($maxVal > 0) { ?>
                                        <div class="mb-2">
                                            <span class="text-dark font-weight-bold mr-2">Most used module(s):</span>
                                            <?php foreach ($topModules as $m) { ?>
                                                <span class="badge badge-primary mr-1"><?= htmlspecialchars($m, ENT_QUOTES, 'UTF-8'); ?></span>
                                            <?php } ?>
                                        </div>
                                        <?php
                                            arsort($menuMap);
                                            $topFive = array_slice($menuMap, 0, 5, true);
                                            $labels = array_keys($topFive);
                                            $values = array_values($topFive);
                                            $norm = [];
                                            foreach ($values as $v) { $norm[] = ($maxVal > 0) ? round(($v / $maxVal) * 100, 2) : 0; }
                                        ?>
                                        <div>
                                            <h6 class="text-dark mb-2">App Usage (Top Modules)</h6>
                                            <div style="height: 180px;">
                                                <canvas id="menuUsageTopChart"></canvas>
                                            </div>
                                        </div>
                                        <script src="assets/plugins/Chart.js/Chart.min.js"></script>
                                        <script>
                                            (function(){
                                                var ctx = document.getElementById('menuUsageTopChart').getContext('2d');
                                                var labels = <?php echo json_encode($labels); ?>;
                                                var values = <?php echo json_encode($norm); ?>; // normalized to 0-100
                                                var counts = <?php echo json_encode($values); ?>; // actual counts
                                                new Chart(ctx, {
                                                    type: 'horizontalBar',
                                                    data: {
                                                        labels: labels,
                                                        datasets: [{
                                                            data: values,
                                                            backgroundColor: 'rgba(37, 99, 235, 0.35)',
                                                            borderColor: 'rgba(37, 99, 235, 1)',
                                                            borderWidth: 1
                                                        }]
                                                    },
                                                    options: {
                                                        responsive: true,
                                                        maintainAspectRatio: false,
                                                        legend: { display: false },
                                                        tooltips: { enabled: false },
                                                        animation: {
                                                            onComplete: function() {
                                                                var chartInstance = this.chart;
                                                                var ctx = chartInstance.ctx;
                                                                ctx.save();
                                                                ctx.fillStyle = '#0f172a';
                                                                ctx.font = '12px sans-serif';
                                                                var meta = this.getDatasetMeta(0);
                                                                meta.data.forEach(function(bar, index){
                                                                    var val = counts[index];
                                                                    if (typeof val === 'undefined') return;
                                                                    var model = bar._model;
                                                                    var text = String(val);
                                                                    var textWidth = ctx.measureText(text).width;
                                                                    var padding = 6;
                                                                    var textX = Math.max(model.base + padding, model.x - textWidth - padding);
                                                                    var textY = model.y;
                                                                    ctx.textAlign = 'left';
                                                                    ctx.textBaseline = 'middle';
                                                                    ctx.fillText(text, textX, textY);
                                                                });
                                                                ctx.restore();
                                                            }
                                                        },
                                                        scales: {
                                                            xAxes: [{
                                                                ticks: { display: false, beginAtZero: true, max: 100 },
                                                                gridLines: { display: false }
                                                            }],
                                                            yAxes: [{
                                                                gridLines: { display: false }
                                                            }]
                                                        }
                                                    }
                                                });
                                            })();
                                        </script>
                                    <?php } else { ?>
                                        <div class="alert alert-info mb-0">No usage detected yet for this company.</div>
                                    <?php } ?>
                                <?php } else { ?>
                                    <div class="alert alert-warning mb-0">No analytics usage available for this company.</div>
                                <?php } ?>
                            </div>
                        </div>                           
                        
                        <?php
                        $companyCronsQ = $d->selectRow(
                            "cm.cron_id, cm.cron_name, cm.cron_category, cm.cron_url, cm.cron_status, cm.last_run_time,
                             csm.display_order_by, csm.active_status, csm.cron_user_count,
                             (SELECT cel.log_date FROM cron_error_logs cel
                              WHERE cel.cron_id = cm.cron_id AND cel.society_id = csm.society_id
                              ORDER BY cel.error_id DESC LIMIT 1) AS company_last_run,
                             (SELECT cel.status_code FROM cron_error_logs cel
                              WHERE cel.cron_id = cm.cron_id AND cel.society_id = csm.society_id
                              ORDER BY cel.error_id DESC LIMIT 1) AS company_last_status",
                            "crons_society_master csm
                             INNER JOIN crons_master cm ON cm.cron_id = csm.cron_id",
                            "csm.society_id = $society_id",
                            "ORDER BY cm.cron_category ASC, csm.display_order_by ASC, cm.cron_id ASC"
                        );
                        ?>
                        <div class="card shadow-sm bg-white mb-4">
                            <div class="card-header bg-primary py-2 px-2">
                                <h5 class="mb-0 fw-bold text-white">Registered Crons</h5>
                            </div>
                            <div class="card-body py-2 px-2">
                                <?php if ($companyCronsQ && mysqli_num_rows($companyCronsQ) > 0) { ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Sr.No</th>
                                                    <th>Cron ID</th>
                                                    <th>Cron Name</th>
                                                    <th>Category</th>
                                                    <th>Order</th>
                                                    <th>Status</th>
                                                    <th>Company Last Run</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $cronSr = 1;
                                                while ($cronRow = mysqli_fetch_assoc($companyCronsQ)) {
                                                    $isActive = ((int)$cronRow['active_status'] === 0);
                                                    $lastStatus = $cronRow['company_last_status'];
                                                    $lastRun = $cronRow['company_last_run'];
                                                    $statusColor = '';
                                                    if ($lastRun !== null && $lastStatus !== null) {
                                                        $statusColor = ((int)$lastStatus === 200) ? 'color:green;' : 'color:red;';
                                                    }
                                                ?>
                                                    <tr>
                                                        <td><?= $cronSr++; ?></td>
                                                        <td><?= (int)$cronRow['cron_id']; ?></td>
                                                        <td><?= htmlspecialchars($cronRow['cron_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?= htmlspecialchars($cronRow['cron_category'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?= (int)$cronRow['display_order_by']; ?></td>
                                                        <td>
                                                            <?php if ($isActive) { ?>
                                                                <span class="badge badge-success">Active</span>
                                                            <?php } else { ?>
                                                                <span class="badge badge-secondary">Inactive</span>
                                                            <?php } ?>
                                                        </td>
                                                        <td style="<?= $statusColor; ?>">
                                                            <?php
                                                            if (!empty($lastRun)) {
                                                                echo htmlspecialchars($lastRun, ENT_QUOTES, 'UTF-8');
                                                                if ($lastStatus !== null && $lastStatus !== '') {
                                                                    echo ' (' . (int)$lastStatus . ')';
                                                                }
                                                            } else {
                                                                echo '-';
                                                            }
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <a class="btn btn-sm btn-primary"
                                                               href="managecron?cron_id=<?= (int)$cronRow['cron_id']; ?>">
                                                                View
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php } else { ?>
                                    <div class="alert alert-info mb-0">This company is not registered in any cron.</div>
                                <?php } ?>
                            </div>
                        </div>

                        <?php
                        $contactPersons = $d->select("society_contact_person_details", "request_society_id = $society_id");
                        if (mysqli_num_rows($contactPersons) > 0) {
                        ?>
                            <div class="card shadow-sm bg-white mb-4">
                                <div class="card-header bg-primary py-2 px-2">
                                    <h5 class="mb-0 fw-bold text-white">Contact Person Details</h5>
                                </div>
                                <div class="card-body py-2 px-3">
                                    <?php
                                    $count = 1;
                                    while ($row = mysqli_fetch_assoc($contactPersons)) {
                                    ?>
                                        <div class="row mb-2 align-items-center">
                                            <div class="col-md-4">
                                                <?= $count ?>.
                                                <strong>Name:</strong>
                                                <span class="font-weight-normal"><?= $row['contact_person_name'] ?></span>
                                            </div>
                                            <div class="col-md-4 pl-md-3 border-left">
                                                <strong>Designation:</strong>
                                                <span class="font-weight-normal"><?= $row['designation'] ?></span>
                                            </div>
                                            <div class="col-md-4 pl-md-3 border-left">
                                                <strong>Phone:</strong>
                                                <span class="font-weight-normal"><?= $row['contact_person_no'] ?></span>
                                            </div>
                                        </div>
                                    <?php
                                        $count++;
                                    }
                                    ?>
                                </div>
                            </div>
                    <?php }
                    }
                } else { ?>
                    <div class="alert alert-warning text-center mt-4">No society found with the given ID.</div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>