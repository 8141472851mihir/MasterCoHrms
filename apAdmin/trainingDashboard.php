<div class="content-wrapper">
    <div class="container-fluid">
        <?php
        extract($_REQUEST);
        $countryId = (isset($_GET['countryId']) && $_GET['countryId'] > 0) ? $d->sanitizeReportFilterIdAsInt($_GET['countryId'], 101) : 101;
$sId = isset($_GET['sId']) ? $d->sanitizeReportFilterIdAsInt($_GET['sId']) : (isset($sId) ? $d->sanitizeReportFilterIdAsInt($sId) : 0);
$cId = isset($_GET['cId']) ? $d->sanitizeReportFilterIdAsInt($_GET['cId']) : (isset($cId) ? $d->sanitizeReportFilterIdAsInt($cId) : 0);
        ?>
        <form action="" method="get" accept-charset="utf-8">

            <div class="form-group row">
                <label for="country_id" class="col-sm-1 col-form-label mb-2"> Country <span class="required">*</span></label>
                <div class="col-sm-2">
                    <select type="text" required="" id="country_id" onchange="this.form.submit()"
                        class="form-control single-select" name="countryId">
                        <option value="">-- Select --</option>
                        <?php
                        $qc = $d->select("countries", "flag=1");
                        while ($cData = mysqli_fetch_array($qc)) {
                        ?>
                            <option <?php if (isset($countryId) && $cData['country_id'] == $countryId) {
                                        echo "selected";
                                    } ?> value="<?php echo $cData['country_id']; ?>">
                                <?php echo $cData['name']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <label for="state_id" class="col-sm-1 col-form-label mb-2"> State </label>
                <div class="col-sm-2">
                    <?php if (isset($countryId)) {
                    ?>
                        <select type="text" onchange="this.form.submit()" required="" class="form-control single-select"
                            id="state_id" name="sId">
                            <option value=""> All</option>
                            <?php
                            $qs = $d->select("states", "country_id='$countryId'");
                            while ($sData = mysqli_fetch_array($qs)) {
                            ?>
                                <option <?php if (isset($_GET['sId']) && $sData['state_id'] == $_GET['sId']) {
                                            echo "selected";
                                        } ?> value="<?php echo $sData['state_id']; ?>">
                                    <?php echo $sData['name']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    <?php } else { ?>
                        <select type="text" onchange="getCity();" required="" class="form-control single-select"
                            id="state_id" name="sId">
                            <option value="">-- Select --</option>
                        </select>
                    <?php } ?>
                </div>

                <label for="input-101" class="col-sm-1 col-form-label mb-2"> City</label>
                <div class="col-sm-2">
                    <?php if (isset($_GET['cId']) && $sId > 0) {

                    ?>
                        <select onchange="this.form.submit()" type="text" required="" class="form-control single-select"
                            id="city_id" name="cId">
                            <option value=""> All</option>
                            <?php
                            if (isset($sId) && $sId > 0) {
                                $appendStateQueryFilter = "state_id='$sId'";
                            }

                            $qcity = $d->select("cities", " $appendStateQueryFilter");
                            while ($cityData = mysqli_fetch_array($qcity)) {
                            ?>
                                <option <?php if (isset($_GET['cId']) && $cityData['city_id'] == $_GET['cId']) {
                                            echo "selected";
                                        } ?> value="<?php echo $cityData['city_id']; ?>">
                                    <?php echo $cityData['name']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    <?php } else { ?>
                        <select onchange="this.form.submit()" type="text" required="" class="form-control single-select"
                            name="cId" id="city_id">
                            <option value="">-- Select --</option>

                        </select>
                    <?php } ?>
                </div>

                <label for="duration_type" class="col-sm-1 col-form-label mb-2"> Duration </label>
                <div class="col-sm-2">
                    <select type="text" required="" id="duration_type" onchange="this.form.submit()"
                        class="form-control single-select" name="duration_type">
                        <option value="0" <?php echo (!isset($_GET['duration_type']) || $_GET['duration_type'] == '0') ? "selected" : ""; ?>>All</option>
                        <option value="1" <?php echo (isset($_GET['duration_type']) && $_GET['duration_type'] == '1') ? "selected" : ""; ?>>Today</option>
                        <option value="2" <?php echo (isset($_GET['duration_type']) && $_GET['duration_type'] == '2') ? "selected" : ""; ?>>Current Month</option>
                        <option value="3" <?php echo (isset($_GET['duration_type']) && $_GET['duration_type'] == '3') ? "selected" : ""; ?>>Current Year</option>
                    </select>
                </div>

                <label for="rise_filter" class="col-sm-1 col-form-label mb-2"> Myco Rise </label>
                <div class="col-sm-2">
                    <select id="rise_filter" name="rise_filter" class="form-control single-select" onchange="this.form.submit()">
                        <option value="yes" <?php echo (!isset($_GET['rise_filter']) || $_GET['rise_filter'] === "yes") ? 'selected' : ''; ?>>Myco Rise</option>
                        <option value="no" <?php echo (isset($_GET['rise_filter']) && $_GET['rise_filter'] === 'no') ? 'selected' : ''; ?>>Before Myco Rise</option>
                        <option value="all" <?php echo (isset($_GET['rise_filter']) && $_GET['rise_filter'] === 'all') ? 'selected' : ''; ?>>All</option>
                    </select>
                </div>

                <label for="product_type" class="col-sm-1 col-form-label mb-2"> Product </label>
                <div class="col-sm-2">
                    <select id="product_type" name="product_type" class="form-control single-select" onchange="this.form.submit()">
                        <option value="hrms" <?php echo (!isset($_GET['product_type']) || $_GET['product_type'] === "hrms") ? 'selected' : ''; ?>>HRMS</option>
                        <option value="crm" <?php echo (isset($_GET['product_type']) && $_GET['product_type'] === 'crm') ? 'selected' : ''; ?>>CRM</option>
                    </select>
                </div>

                <label for="expiry_filter" class="col-sm-1 col-form-label mb-2"> Expiry </label>
                <div class="col-sm-2">
                    <select id="expiry_filter" name="expiry_filter" class="form-control single-select" onchange="this.form.submit()">
                        <option value="not_expired" <?php echo (!isset($_GET['expiry_filter']) || $_GET['expiry_filter'] === 'not_expired') ? 'selected' : ''; ?>>Not Expired</option>
                        <option value="expired" <?php echo (isset($_GET['expiry_filter']) && $_GET['expiry_filter'] === 'expired') ? 'selected' : ''; ?>>Expired</option>
                        <option value="all" <?php echo (isset($_GET['expiry_filter']) && $_GET['expiry_filter'] === 'all') ? 'selected' : ''; ?>>All</option>
                    </select>
                </div>

            </div>
        </form>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

        <?php
        $durationText = '';
        if (isset($_GET['duration_type'])) {
            if ($_GET['duration_type'] == '1') {
                $durationText = ' for today';
            } elseif ($_GET['duration_type'] == '2') {
                $durationText = ' for current month';
            } elseif ($_GET['duration_type'] == '3') {
                $durationText = ' for current year';
            }
        }

        // Build return URL with filters
        $returnParams = [];
        if (isset($_GET['countryId']) && $_GET['countryId'] > 0) $returnParams['countryId'] = $_GET['countryId'];
        if (isset($_GET['sId']) && $_GET['sId'] > 0) $returnParams['sId'] = $_GET['sId'];
        if (isset($_GET['cId']) && $_GET['cId'] > 0) $returnParams['cId'] = $_GET['cId'];
        if (isset($_GET['duration_type'])) $returnParams['duration_type'] = $_GET['duration_type'];
        if (isset($_GET['rise_filter'])) $returnParams['rise_filter'] = $_GET['rise_filter'];
        if (isset($_GET['product_type'])) $returnParams['product_type'] = $_GET['product_type'];
        if (isset($_GET['expiry_filter'])) $returnParams['expiry_filter'] = $_GET['expiry_filter'];
        $returnUrl = 'trainingDashboard' . (!empty($returnParams) ? '?' . http_build_query($returnParams) : '');
        
        // Get product type filter
        $productType = isset($_GET['product_type']) ? $_GET['product_type'] : 'hrms';
        ?>

        <div class="row pt-2 pb-2">
            <div class="col-9">
                <h5 class="text-dark">Implementation Status</h5>
            </div>
            <?php if ($productType !== 'crm'): ?>
            <div class="col-3">
                <div class="btn-group float-sm-right">
                    <a href="slotAvailability?return=<?php echo urlencode($returnUrl); ?>" class="btn btn-info btn-sm">
                        <i class="fa fa-calendar-check-o mr-1"></i>Check Slot Availability & Status
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <div class="container-fluid">
            <div class="row g-3">
                <div class="col-12 my-1">
                    <div class="row g-3 mx-2 my-0 py-0" id="implementationStatusContainer"></div>
                </div>
            </div>
        </div>

        <?php if ($productType === 'crm'): ?>
        <!-- CRM Companies Section - Only show for CRM product type -->
        <div class="row mt-4">
            <div class="col-12">
                <h5 class="text-dark">CRM Companies</h5>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row g-3">
                <div class="col-12 my-1">
                    <div class="row">
                        <!-- Total CRM Companies -->
                        <div class="col-md-6">
                            <a href="companyOnboarding?product_type=crm" data-toggle="tooltip" title="Displays the total number of companies integrated with CRM<?php echo $durationText; ?>" class="text-primary">
                                <div class="card shadow-sm border-0 p-2 mb-2 pb-2">
                                    <div class="card-body d-flex justify-content-between align-items-center p-2">
                                        <div class="text-success">
                                            <h6 class="font-weight-bold mb-1">Total CRM Companies</h6>
                                            <h6 class="mb-0" id="crmCount">0</h6>
                                        </div>
                                        <div class="text-success d-flex align-items-center h3 mb-0">
                                            <i class="bi bi-database-check"></i>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($productType !== 'crm'): ?>
        <!-- Not Responding Companies Section -->
        <div class="col-12 my-1">
            <h6 class="text-dark">Not Responding Companies</h6>
            <div class="row">
                <div class="col-md-12">
                    <div class="card shadow-sm border-danger p-2 mb-2 pb-2">
                        <div class="card-body d-flex justify-content-between align-items-center p-2">
                            <div class="text-danger">
                                <h6 class="font-weight-bold mb-1">Total Not Responding Companies</h6>
                                <h6 class="mb-0" id="notRespondingCount">0</h6>
                            </div>
                            <div class="d-flex align-items-center">
                                <button type="button" class="btn btn-danger btn-sm mr-2" data-toggle="modal" data-target="#notRespondingCompaniesModal" id="viewNotRespondingBtn">
                                    <i class="bi bi-eye"></i> View List
                                </button>
                                <div class="text-danger d-flex align-items-center h3 mb-0">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <h5 class="text-dark">Data Receive & Upload Dashboard</h5>
        </div>
        <?php endif; ?>

        <?php if ($productType !== 'crm'): ?>
        <div class="container-fluid">
            <div class="row g-3">
                <div class="col-12 my-1">
                    <h6 class="text-dark">Sessions</h6>
                    <div class="row">

                        <!-- Total Sessions -->
                        <div class="col-md-4">
                            <a href="manageTraining" data-toggle="tooltip" title="Displays the total number of data receive & upload sessions scheduled or planned<?php echo $durationText; ?>" class="text-primary">
                                <div class="card shadow-sm border-0 p-2 mb-2 pb-2">
                                    <div class="card-body d-flex justify-content-between align-items-center p-2">
                                        <div class="text-info">
                                            <h6 class="font-weight-bold mb-1">Total Data Receive & Upload Sessions</h6>
                                            <h6 class="mb-0" id="totalSetupCount">0</h6>
                                        </div>
                                        <div class="text-info d-flex align-items-center h3 mb-0">
                                            <i class="bi bi-graph-up"></i>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <!-- Completed Sessions -->
                        <div class="col-md-4">
                            <a href="manageTraining" data-toggle="tooltip" title="Shows the total number of data receive & upload sessions that have been successfully completed<?php echo $durationText; ?>" class="text-primary">
                                <div class="card shadow-sm border-0 p-2 mb-2 pb-2">
                                    <div class="card-body d-flex justify-content-between align-items-center p-2">
                                        <div class="text-success">
                                            <h6 class="font-weight-bold mb-1">Total Completed Sessions</h6>
                                            <h6 class="mb-0" id="completedSetupCount">0</h6>
                                        </div>
                                        <div class="text-success d-flex align-items-center h3 mb-0">
                                            <i class="bi bi-check2-circle"></i>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <!-- Pending Sessions -->
                        <div class="col-md-4">
                            <a href="manageTraining" data-toggle="tooltip" title="Indicates the number of data receive & upload sessions that are still pending or yet to be completed<?php echo $durationText; ?>" class="text-primary">
                                <div class="card shadow-sm border-0 p-2 mb-2 pb-2">
                                    <div class="card-body d-flex justify-content-between align-items-center p-2">
                                        <div class="text-danger">
                                            <h6 class="font-weight-bold mb-1">Total Pending Sessions</h6>
                                            <h6 class="mb-0" id="pendingSetupSessions">0</h6>
                                        </div>
                                        <div class="text-danger d-flex align-items-center h3 mb-0">
                                            <i class="bi bi-clock"></i>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                    </div>
                </div>


                <!-- Companies Section -->
                <div class="col-12 my-1">
                    <h6 class="text-dark">Companies</h6>
                    <div class="row">
                        <!-- Total Companies -->
                        <div class="col-md-4">
                            <a href="companyOnboarding" data-toggle="tooltip" title="Displays the total number of companies<?php echo $durationText; ?>" class="text-primary">
                                <div class="card shadow-sm border-0 p-2 mb-2 pb-2">
                                    <div class="card-body d-flex justify-content-between align-items-center p-2">
                                        <div class="text-info">
                                            <h6 class="font-weight-bold mb-1">Total Companies</h6>
                                            <h6 class="mb-0" id="totalCompanyCount">0</h6>
                                        </div>
                                        <div class="text-info d-flex align-items-center h3 mb-0">
                                            <i class="bi bi-building"></i>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <!-- Completed Companies -->
                        <div class="col-md-4">
                            <a href="companyOnboarding" data-toggle="tooltip" title="Shows the total number of companies that have completed their setup process<?php echo $durationText; ?>" class="text-primary">
                                <div class="card shadow-sm border-0 p-2 mb-2 pb-2">
                                    <div class="card-body d-flex justify-content-between align-items-center p-2">
                                        <div class="text-success">
                                            <h6 class="font-weight-bold mb-1">Setup Completed Companies</h6>
                                            <h6 class="mb-0" id="totalDoneCompanies">0</h6>
                                        </div>
                                        <div class="text-success d-flex align-items-center h3 mb-0">
                                            <i class="bi bi-check-circle"></i>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <!-- Pending Companies -->
                        <div class="col-md-4">
                            <a href="companyOnboarding" data-toggle="tooltip" title="Indicates the number of companies that have not yet completed their setup<?php echo $durationText; ?>" class="text-primary">
                                <div class="card shadow-sm border-0 p-2 mb-2 pb-2">
                                    <div class="card-body d-flex justify-content-between align-items-center p-2">
                                        <div class="text-warning">
                                            <h6 class="font-weight-bold mb-1">Setup Pending Companies</h6>
                                            <h6 class="mb-0" id="totalPendingCompanies">0</h6>
                                        </div>
                                        <div class="text-warning d-flex align-items-center h3 mb-0">
                                            <i class="bi bi-clock-history"></i>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Key Companies Section -->
                <div class="col-12 my-1">
                    <h6 class="text-dark">Key Companies</h6>
                    <div class="row">

                        <!-- Total Key Companies -->
                        <div class="col-md-4">
                            <a href="companyOnboarding" data-toggle="tooltip" title="Displays the total number of key companies<?php echo $durationText; ?>" class="text-primary">
                                <div class="card shadow-sm border-0 p-2 mb-2 pb-2">
                                    <div class="card-body d-flex justify-content-between align-items-center p-2">
                                        <div class="text-info">
                                            <h6 class="font-weight-bold mb-1">Total Key Companies</h6>
                                            <h6 class="mb-0" id="keyAccountsCount">0</h6>
                                        </div>
                                        <div class="text-info d-flex align-items-center h3 mb-0">
                                            <i class="bi bi-building-fill-gear"></i>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <!-- Key Done -->
                        <div class="col-md-4">
                            <a href="companyOnboarding" data-toggle="tooltip" title="Shows the total number of key companies that have completed their setup process<?php echo $durationText; ?>" class="text-primary">
                                <div class="card shadow-sm border-0 p-2 mb-2 pb-2">
                                    <div class="card-body d-flex justify-content-between align-items-center p-2">
                                        <div class="text-success">
                                            <h6 class="font-weight-bold mb-1">Total Key Setup Completed Companies</h6>
                                            <h6 class="mb-0" id="keyAccountsDoneCount">0</h6>
                                        </div>
                                        <div class="text-success d-flex align-items-center h3 mb-0">
                                            <i class="bi bi-check2-square"></i>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <!-- Key Pending -->
                        <div class="col-md-4">
                            <a href="companyOnboarding" data-toggle="tooltip" title="Indicates the number of key companies whose setup is still pending or incomplete<?php echo $durationText; ?>" class="text-primary">
                                <div class="card shadow-sm border-0 p-2 mb-2 pb-2">
                                    <div class="card-body d-flex justify-content-between align-items-center p-2">
                                        <div class="text-warning">
                                            <h6 class="font-weight-bold mb-1">Total Key Setup Pending Companies</h6>
                                            <h6 class="mb-0" id="keyAccountsPendingCount">0</h6>
                                        </div>
                                        <div class="text-warning d-flex align-items-center h3 mb-0">
                                            <i class="bi bi-hourglass-split"></i>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>


                    </div>
                </div>
            </div>
        </div>



        <div class="row mx-2 mt-3 mb-0">
            <div class="col-12">
                <h6 class="fw-light pt-2">Data Receive & Upload Progress</h6>
            </div>
        </div>



        <div class="row g-3 mx-2 my-0 py-0" id="setupContainer">

        </div>

        <br>
        <div class="row my-0">
            <h5 class="text-dark">Product Training Dashboard</h5>
        </div>

        <div class="container-fluid">
            <div class="row g-3">
                <div class="col-12 my-1">
                    <h6 class="text-dark">Training Meetings</h6>
                    <div class="row">
                        <div class="col-md-4">
                            <a href="manageTrainingSlots" data-toggle="tooltip" title="Displays the total number of product training meetings scheduled or planned<?php echo $durationText; ?>" class="text-primary">
                                <div class="card shadow-sm border-0 p-2 mb-2 pb-2">
                                    <div class="card-body d-flex justify-content-between align-items-center p-2">
                                        <div class="text-info">
                                            <h6 class="font-weight-bold mb-1">Total Training Meetings</h6>
                                            <h6 class="mb-0" id="totalMeetingsCount">0</h6>
                                        </div>
                                        <div class="text-info d-flex align-items-center h3 mb-0">
                                            <i class="bi bi-calendar-event"></i>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <!-- Completed Meetings -->
                        <div class="col-md-4">
                            <a href="manageTrainingSlots" data-toggle="tooltip" title="Shows the total number of product training meetings that have been successfully completed<?php echo $durationText; ?>" class="text-primary">
                                <div class="card shadow-sm border-0 p-2 mb-2 pb-2">
                                    <div class="card-body d-flex justify-content-between align-items-center p-2">
                                        <div class="text-success">
                                            <h6 class="font-weight-bold mb-1">Total Completed Training Meetings</h6>
                                            <h6 class="mb-0" id="completedMeetingsCount">0</h6>
                                        </div>
                                        <div class="text-success d-flex align-items-center h3 mb-0">
                                            <i class="bi bi-check2-square"></i>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <!-- Pending Meetings -->
                        <div class="col-md-4">
                            <a href="manageTrainingSlots" data-toggle="tooltip" title="Indicates the number of product training meetings that are still pending or yet to be held<?php echo $durationText; ?>" class="text-primary">
                                <div class="card shadow-sm border-0 p-2 mb-2 pb-2">
                                    <div class="card-body d-flex justify-content-between align-items-center p-2">
                                        <div class="text-warning">
                                            <h6 class="font-weight-bold mb-1">Total Pending Training Meetings</h6>
                                            <h6 class="mb-0" id="pendingMeetingsCount">0</h6>
                                        </div>
                                        <div class="text-warning d-flex align-items-center h3 mb-0">
                                            <i class="bi bi-hourglass"></i>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-12 my-1">
                    <!-- Product Implementation Companies -->
                    <h6 class="text-dark">Product Implementation Companies</h6>
                    <div class="row mb-0 pb-0">

                        <!-- Total Companies -->
                        <div class="col-md-6 my-0 py-0">
                            <a href="companyOnboarding" data-toggle="tooltip" title="Displays the total number of companies<?php echo $durationText; ?>" class="text-primary">
                                <div class="card shadow-sm border-0 p-2 my-1">
                                    <div class="card-body d-flex justify-content-between align-items-center p-2">
                                        <div class="text-info">
                                            <h6 class="font-weight-bold mb-1">Total Training Companies</h6>
                                            <h6 class="mb-0" id="totalCompanies">0</h6>
                                        </div>
                                        <div class="text-info h3 mb-0">
                                            <i class="bi bi-buildings"></i>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <!-- Pending Companies -->
                        <div class="col-md-6 my-0 py-0">
                            <a href="companyOnboarding" data-toggle="tooltip" title="Indicates the number of companies that have not yet started their product training<?php echo $durationText; ?>" class="text-primary">
                                <div class="card shadow-sm border-0 p-2 my-1">
                                    <div class="card-body d-flex justify-content-between align-items-center p-2">
                                        <div class="text-warning">
                                            <h6 class="font-weight-bold mb-1">Total Training Pending Companies</h6>
                                            <h6 class="mb-0" id="productImplementationPendingCount">0</h6>
                                        </div>
                                        <div class="text-warning h3 mb-0">
                                            <i class="bi bi-hourglass-split"></i>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6 my-0 py-0">
                            <a href="companyOnboarding" data-toggle="tooltip" title="Shows the total number of companies currently undergoing product training<?php echo $durationText; ?>" class="text-primary">
                                <div class="card shadow-sm border-0 p-2 my-1">
                                    <div class="card-body d-flex justify-content-between align-items-center p-2">
                                        <div class="text-primary">
                                            <h6 class="font-weight-bold mb-1">Total Training Running Companies</h6>
                                            <h6 class="mb-0" id="productImplementationRunningCount">0</h6>
                                        </div>
                                        <div class="text-primary h3 mb-0">
                                            <i class="bi bi-play-circle"></i>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <!-- Completed Companies -->
                        <div class="col-md-6 my-0 py-0">
                            <a href="companyOnboarding" data-toggle="tooltip" title="Shows the total number of companies that have completed their product training<?php echo $durationText; ?>" class="text-primary">
                                <div class="card shadow-sm border-0 p-2 my-1">
                                    <div class="card-body d-flex justify-content-between align-items-center p-2">
                                        <div class="text-success">
                                            <h6 class="font-weight-bold mb-1">Total Training Completed Companies</h6>
                                            <h6 class="mb-0" id="productImplementationDoneCount">0</h6>
                                        </div>
                                        <div class="text-success h3 mb-0">
                                            <i class="bi bi-check2-circle"></i>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php if ($productType !== 'crm'): ?>
    <div class="modal fade" id="moduleCompaniesModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-primary">
                <div class="modal-header bg-primary">
                    <h4 class="modal-title text-white" id="moduleCompaniesModalLabel">Overdue Companies</h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="moduleCompaniesModalBody"></div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <?php if ($productType !== 'crm'): ?>
    <div class="mx-2 px-2 mt-3 mb-0">
        <div class="col-12">
            <h6 class="fw-light px-2">Product Training Progress</h6>
        </div>
    </div>
    <div class="row g-3 mx-2 my-0 py-0 px-4" id="sessionStatsContainer">
    </div>
    <div class="mx-2 px-2 mt-3 mb-0">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h6 class="fw-light px-2 mb-0">Overdue Views</h6>
            <div class="px-2">
                <form>
                    <select id="overdueViewSelector" class="form-control form-control-sm" style="min-width:220px;">
                        <option value="module" selected>Module-wise Overdue</option>
                        <option value="company">Company-wise Overdue</option>
                        <option value="employee">Employee-wise Company Overdue</option>
                    </select>
                </form>
            </div>
        </div>
    </div>

    <div id="moduleOverdueSection" class="mx-2 px-2">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <table id="moduleOverdueTable" class="table table-bordered table-striped table-sm mb-0" style="width:100%">
                    <thead>
                        <tr>
                            <th>Module</th>
                            <th>Days</th>
                            <th>Missed</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="companyOverdueSection" class="mx-2 px-2" style="display:none;">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <table id="companyOverdueTable" class="table table-bordered table-striped table-sm mb-0" style="width:100%">
                    <thead>
                        <tr>
                            <th>Company</th>
                            <th>City</th>
                            <th>Overdue</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="employeeOverdueSection" class="mx-2 px-2" style="display:none;">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <table id="employeeOverdueTable" class="table table-bordered table-striped table-sm mb-0" style="width:100%">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Overdue Companies</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="modal fade" id="employeeCompaniesModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-primary">
                <div class="modal-header bg-primary">
                    <h4 class="modal-title text-white" id="employeeCompaniesModalLabel">Overdue Companies</h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="employeeCompaniesModalBody"></div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="customGroupModal">
        <div class="modal-dialog modal-md">
            <div class="modal-content border-primary">
                <div class="modal-header bg-primary">
                    <h4 class="modal-title text-white">
                        Add Custom Group
                    </h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="customGroupForm" action="" method="post">
                        <div class="form-group row">
                            <div class="col-md-12" id="sessionField">
                                <label for="session_id" class="col-form-label">Select Participant Types<span
                                        class="required">*</span></label>
                                <select class="form-control multiple-select" multiple="multiple" name="session_id[]"
                                    id="session_id" required>
                                    <?php
                                    $participantsResult = $d->select("training_participants_type", "status = 0");
                                    while ($row = mysqli_fetch_assoc($participantsResult)) {
                                    ?>
                                        <option value="<?php echo $row['participant_name']; ?>">
                                            <?php echo $row['participant_name']; ?>
                                        </option>
                                    <?php
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-footer text-center">
                            <button type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i><span
                                    id="submitButton">ADD</span></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="companyModulesModal">
        <div class="modal-dialog modal-md">
            <div class="modal-content border-primary">
                <div class="modal-header bg-primary">
                    <h4 class="modal-title text-white" id="companyModulesModalLabel">Overdue Modules</h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="companyModulesModalBody"></div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="notRespondingCompaniesModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content border-danger">
                <div class="modal-header bg-danger">
                    <h4 class="modal-title text-white">Not Responding Companies</h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table id="notRespondingCompaniesTable" class="table table-bordered table-striped table-sm mb-0" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Company ID</th>
                                    <th>Company Name</th>
                                    <th>City</th>
                                    <th>Implementation Person</th>
                                    <th>Created Date</th>
                                    <th>Sales Closure Date</th>
                                    <th>Welcome Email</th>
                                    <th>WhatsApp Group</th>
                                    <th>Setup Status</th>
                                    <th>Training Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <!-- Companies List Modal - Available for both HRMS and CRM -->
    <div class="modal fade" id="companiesListModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h4 class="modal-title text-white" id="companiesListModalTitle">Companies</h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table id="companiesListTable" class="table table-bordered table-striped table-sm mb-0" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Company ID</th>
                                    <th>Company Name</th>
                                    <th>City</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

    <script>
        $("#customGroupForm").on("submit", function(e) {
            e.preventDefault();
            let cityId = $('#city_id').val();
            let stateId = $('#state_id').val();
            let durationId = $('#duration_type').val();
            const csrf = $("input[name='csrf']").val();
            const selectedTypes = $("#session_id").val();
            const selectedNames = $("#session_id option:selected")
                .map(function() {
                    return $(this).text();
                }).get().join(", ");

            $.ajax({
                url: "get_training_dashboard_counts.php",
                type: "GET",
                dataType: "json",
                    data: {
                        customGroupTypes: selectedTypes,
                        cId: cityId,
                        sId: stateId,
                        duration_type: durationId,
                        rise_filter: ($('#rise_filter').length ? $('#rise_filter').val() : 'yes'),
                        expiry_filter: ($('#expiry_filter').length ? $('#expiry_filter').val() : 'not_expired'),
                        csrf: csrf
                    },
                success: function(response) {
                    if (response.customGroupStats) {
                        const stats = response.customGroupStats;
                        const customGroupCard = `
                    <div class="col-6 col-lg-2 my-1 custom-group-card">
                        <div class="border rounded p-3 shadow-sm bg-white h-100">
                            <h6 class="text-purple mb-2">${selectedNames}</h6>
                            <div>Running: <span>${stats.running}</span></div>
                            <div>Completed: <span>${stats.finished}</span></div>
                            <div>Pending: <span>${stats.pending}<a/span></div>
                        </div>
                    </div>`;
                        const customGroupButtonCard = $("#sessionStatsContainer .text-primary:contains('Custom Group')")
                            .closest(".col-6.col-lg-2");

                        $(customGroupCard).insertBefore(customGroupButtonCard);

                        $('#customGroupModal').modal('hide');
                    }
                },
                error: function() {
                    console.error("Failed to fetch custom group stats.");
                }
            });
        });
        $(document).ready(function() {
            function showOverdueSection(view) {
                $('#moduleOverdueSection').css('display', view === 'module' ? '' : 'none');
                $('#companyOverdueSection').css('display', view === 'company' ? '' : 'none');
                $('#employeeOverdueSection').css('display', view === 'employee' ? '' : 'none');
            }

            function getSelectedOverdueView() {
                return ($('#overdueViewSelector').val() || 'module');
            }

            function capitalize(text) {
                return text.charAt(0).toUpperCase() + text.slice(1);
            }

            function getRoleColor(role) {
                const colors = ["primary", "secondary", "success", "danger", "warning", "info", "dark"];
                let hash = 0;
                for (let i = 0; i < role.length; i++) {
                    hash = role.charCodeAt(i) + ((hash << 5) - hash);
                }
                const index = Math.abs(hash) % colors.length;
                return colors[index];
            }

            function getImplementationColor(stage) {
                const s = String(stage || '').toLowerCase();
                if (s === 'all companies' || s.includes('full implementation')) return 'primary';
                if (s.includes('handover') || s.includes('support team')) return 'info';
                if (s.includes('welcome')) return 'info';
                if (s.includes('whatsapp')) return 'success';
                if (s.includes('data') || s.includes('setup')) return 'warning';
                // Product training topics and others
                return 'secondary';
            }

            function createSessionCard(role, stats, requiredCount, isAllCompanies) {
                const countLabel = (typeof requiredCount === 'number') ? (isAllCompanies ? `${requiredCount}` : `${requiredCount}`) : '';
                return `
				<div class="col-6 col-lg-2 my-1">
					<div class="border border-primary rounded p-3 shadow-sm bg-white h-100">
						<div class="d-flex justify-content-between align-items-center mb-2">
							<h6 class="text-${getRoleColor(role)} mb-0">${capitalize(role)}</h6>
							${countLabel !== '' ? `<span  class="badge badge-secondary" data-toggle="tooltip" data-placement="top" title="Modules: ${countLabel}">  ${countLabel}</span>` : ''}
						</div>
						<div>Running: <span>${stats.running}</span></div>
						<div>Completed: <span>${stats.finished}</span></div>
						<div>Pending: <span>${stats.pending}</span></div>
					</div>
				</div>`;
            }

            function createImplementationStatusCard(stageName, stats) {
                const color = getImplementationColor(stageName);
                const runningCount = stats.running || 0;
                const completedCount = stats.completed || 0;
                const pendingCount = stats.pending || 0;
                return `
				<div class="col-6 col-lg-2 my-1">
					<div class="border border-${color} rounded p-3 shadow-sm bg-white h-100">
						<h6 class="text-${color} mb-2">${capitalize(stageName)}</h6>
						<div>Running: <span class="clickable-count" data-stage="${stageName}" data-status="running" style="cursor: pointer; text-decoration: underline;" title="Click to view companies">${runningCount}</span></div>
						<div>Completed: <span class="clickable-count" data-stage="${stageName}" data-status="completed" style="cursor: pointer; text-decoration: underline;" title="Click to view companies">${completedCount}</span></div>
						<div>Pending: <span class="clickable-count" data-stage="${stageName}" data-status="pending" style="cursor: pointer; text-decoration: underline;" title="Click to view companies">${pendingCount}</span></div>
					</div>
				</div>`;
            }

            function createImplBlock(role, stats) {
                return `
                <div class="row mx-0"><h6 class="text-muted mt-4">${capitalize(role)}</h6></div>
                <div class="row g-3 mb-3">
                    <div class="col-6 col-md-3">
                        <div class="border rounded p-3 shadow-sm bg-white h-100">
                            <h6 class="text-info">Total Companies</h6>
                            <div>${stats.total}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="border rounded p-3 shadow-sm bg-white h-100">
                            <h6 class="text-warning">Companies In Progress</h6>
                            <div>${stats.inProgress}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="border rounded p-3 shadow-sm bg-white h-100">
                            <h6 class="text-danger">Companies Pending</h6>
                            <div>${stats.pending}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="border rounded p-3 shadow-sm bg-white h-100">
                            <h6 class="text-success">Companies Completed</h6>
                            <div>${stats.completed}</div>
                        </div>
                    </div>
                </div>`;
            }

            function fetchCounts(selectedViewParam) {
                let cityId = $('#city_id').val();
                let stateId = $('#state_id').val();
                let durationId = $('#duration_type').val();
                let productType = $('#product_type').val() || 'hrms';
                const csrf = $("input[name='csrf']").val();

                $.ajax({
                    url: "get_training_dashboard_counts.php",
                    method: "GET",
                    dataType: "json",
                    data: {
                        cId: cityId,
                        sId: stateId,
                        duration_type: durationId,
                        rise_filter: ($('#rise_filter').length ? $('#rise_filter').val() : 'yes'),
                        expiry_filter: ($('#expiry_filter').length ? $('#expiry_filter').val() : 'not_expired'),
                        product_type: productType,
                        overdue_view: (selectedViewParam || getSelectedOverdueView()),
                        csrf: csrf
                    },
                    success: function(response) {
                        response = response || {};
                        const selectedView = (selectedViewParam || getSelectedOverdueView());
                        showOverdueSection(selectedView);
                        // Setup
                        $("#totalSetupCount").text(response.totalSetup || 0);
                        $("#completedSetupCount").text(response.completedSetup || 0);
                        $("#pendingSetupSessions").text(response.pendingSetupSessions || 0);
                        $("#totalCompanyCount").text(response.totalCompanies || 0);
                        $("#totalDoneCompanies").text(response.totalDoneCompanies || 0);
                        $("#totalPendingCompanies").text(response.totalPendingCompanies || 0);
                        $("#keyAccountsCount").text(response.keyAccounts || 0);
                        $("#keyAccountsDoneCount").text(response.keyAccountsDone || 0);
                        $("#keyAccountsPendingCount").text(response.keyAccountsPending || 0);
                        $("#crmCount").text(response.crm || 0);
                        $("#notCrmCount").text(response.notCrm || 0);
                        $("#notRespondingCount").text(response.notRespondingCount || 0);

                        // Training
                        $("#totalMeetingsCount").text(response.totalMeetings || 0);
                        $("#completedMeetingsCount").text(response.completedMeetings || 0);
                        $("#totalCompanies").text(response.totalCompanies || 0);
                        $("#pendingMeetingsCount").text(response.pendingMeetings || 0);
                        $("#productImplementationDoneCount").text(response.productImplementationDone || 0);
                        $("#productImplementationPendingCount").text(response.productImplementationPending || 0);
                        $("#productImplementationRunningCount").text(response.productImplementationRunning || 0);


                        // Session stats by role
                        let sessionHTML = "";
                        const sessionStatsData = (response.sessionStats || {});
                        const requiredCounts = (response.requiredModuleCounts || {});
                        const perParticipantReq = requiredCounts.perParticipant || {};
                        const allReqTotal = requiredCounts.allCompanies || 0;
                        for (const role in sessionStatsData) {
                            const requiredCount = (role === 'All Companies') ? allReqTotal : (perParticipantReq[role] || 0);
                            sessionHTML += createSessionCard(role, sessionStatsData[role], requiredCount, role === 'All Companies');
                        }

                        let setupHTML = "";

                        const setupSessionStatsData = (response.setupSessionStats || {});
                        for (const setupSession in setupSessionStatsData) {
                            setupHTML += createSessionCard(setupSession, setupSessionStatsData[setupSession], null, false);
                        }

                        $("#setupContainer").html(setupHTML);

                        sessionHTML += `
                        <div class="col-6 col-lg-2 my-1">
                            <div class="border rounded p-3 shadow-sm bg-white text-center d-flex flex-column justify-content-center align-items-center"
                                data-toggle="modal" data-target="#customGroupModal"
                                style="cursor: pointer; height: 100%;">
                                <div class="d-flex justify-content-center align-items-center mb-2"
                                    style="width: 40px; height: 40px; border-radius: 50%; background-color: rgb(221, 225, 230); color: #007bff; font-size: 24px;">
                                    +
                                </div>
                                <h6 class="text-primary mb-1">Custom Group</h6>
                            </div>
                        </div>`;

                        $("#sessionStatsContainer").html(sessionHTML);

                        // Implementation Status
                        let implStatusHTML = "";
                        const implStatusData = (response.implementationStatus || {});
                        // Store implementation status globally for click handlers
                        window.currentImplementationStatus = implStatusData;
                        // Debug: Log structure for first stage to verify data
                        if (Object.keys(implStatusData).length > 0) {
                            const firstStage = Object.keys(implStatusData)[0];
                            // console.log('Sample stage data:', firstStage, implStatusData[firstStage]);
                        }
                        for (const stage in implStatusData) {
                            implStatusHTML += createImplementationStatusCard(stage, implStatusData[stage]);
                        }
                        $("#implementationStatusContainer").html(implStatusHTML);

                        // Only show overdue sections for HRMS
                        const productType = $('#product_type').val() || 'hrms';
                        if (productType !== 'crm' && selectedView === 'module') {
                            const modules = (response.missedTimelineModules || []).filter(function(m) {
                                return parseInt(m.overdue_count) > 0;
                            });
                            const tbody = $('#moduleOverdueTable tbody');
                            tbody.empty();
                            if (modules.length === 0) {
                                tbody.append('<tr><td class="text-muted">No missed timelines.</td><td></td><td></td><td></td></tr>');
                            } else {
                                modules.forEach(function(m) {
                                    const safeName = $('<div>').text(m.module_name || '').html();
                                    const viewBtn = `<button type=\"button\" class=\"btn btn-sm btn-outline-primary btn-view-module-companies\" data-module-id=\"${m.module_id}\" data-completion-days=\"${m.completion_days}\"><i class=\"bi bi-eye\"></i></button>`;
                                    tbody.append(`<tr><td>${safeName}</td><td>${m.completion_days}</td><td>${m.overdue_count}</td><td class=\"text-center\">${viewBtn}</td></tr>`);
                                });
                            }
                            if ($.fn.DataTable.isDataTable('#moduleOverdueTable')) {
                                $('#moduleOverdueTable').DataTable().destroy();
                            }
                            $('#moduleOverdueTable').DataTable({
                                pageLength: 25,
                                lengthMenu: [
                                    [25, 50, 100],
                                    [25, 50, 100]
                                ],
                                order: [
                                    [2, 'desc']
                                ],
                                columnDefs: [{
                                    targets: 3,
                                    orderable: false,
                                    searchable: false
                                }]
                            });

                            $(document).off('click', '.btn-view-module-companies').on('click', '.btn-view-module-companies', function() {
                                const moduleId = parseInt($(this).data('module-id'), 10);
                                const days = $(this).data('completion-days');
                                $('#moduleCompaniesModalLabel').text('Overdue Companies for Module (' + days + ' days)');
                                const body = $('#moduleCompaniesModalBody');
                                body.html('<div class="text-muted">Loading...</div>');
                                $.ajax({
                                    url: 'get_missed_timelines_by_module.php',
                                    method: 'GET',
                                    data: {
                                        countryId: $('#country_id').val(),
                                        sId: stateId,
                                        cId: cityId,
                                        module_id: moduleId
                                    },
                                    success: function(html) {
                                        const tableHtml = `
                                            <div class=\"table-responsive\">
                                                <table id=\"moduleCompaniesTable\" class=\"table table-sm table-bordered table-striped mb-0\" style=\"width:100%\">
                                                    <thead> 
                                                    <tr>
                                                    <th  style=\\\"width:50%\\\">Company</
                                                    th><th>City</th><th>Created</th><th>Due</th><th>Days Over</th></tr></thead><tbody>${html}</tbody></table></div>`;
                                        body.html(tableHtml);
                                        if ($.fn.DataTable.isDataTable('#moduleCompaniesTable')) {
                                            $('#moduleCompaniesTable').DataTable().destroy();
                                        }
                                        $('#moduleCompaniesTable').DataTable({
                                            pageLength: 10,
                                            lengthMenu: [
                                                [10, 25, 50, 100],
                                                [10, 25, 50, 100]
                                            ],
                                            order: [
                                                [3, 'desc']
                                            ]
                                        });
                                    },
                                    error: function() {
                                        body.html('<div class="text-danger">Failed to load.</div>');
                                    }
                                });
                                $('#moduleCompaniesModal').modal('show');
                            });
                        }

                        // Company-wise overdue section as DataTable with modal details
                        if (productType !== 'crm' && selectedView === 'company') {
                            window.companyOverdueData = response.companyOverdues || [];
                            const tbody = $('#companyOverdueTable tbody');
                            tbody.empty();
                            if (!window.companyOverdueData.length) {
                                tbody.append('<tr><td class="text-muted">No company overdues.</td><td></td><td></td><td></td></tr>');
                            } else {
                                window.companyOverdueData.forEach(function(c) {
                                    const safeName = $('<div>').text(c.company_name || '').html();
                                    const safeCity = $('<div>').text(c.city_name || '').html();
                                    const viewBtn = `<button type="button" class="btn btn-sm btn-outline-primary btn-view-mods" data-company-id="${c.company_id}"><i class="bi bi-eye"></i></button>`;
                                    tbody.append(`<tr><td>${safeName}</td><td>${safeCity}</td><td>${c.overdue_count}</td><td class="text-center">${viewBtn}</td></tr>`);
                                });
                            }
                            if ($.fn.DataTable.isDataTable('#companyOverdueTable')) {
                                $('#companyOverdueTable').DataTable().destroy();
                            }
                            $('#companyOverdueTable').DataTable({
                                pageLength: 25,
                                lengthMenu: [
                                    [25, 50, 100],
                                    [25, 50, 100]
                                ],
                                order: [
                                    [2, 'desc']
                                ],
                                columnDefs: [{
                                    targets: 3,
                                    orderable: false,
                                    searchable: false
                                }]
                            });

                            $(document).off('click', '.btn-view-mods').on('click', '.btn-view-mods', function() {
                                const cid = parseInt($(this).data('company-id'), 10);
                                const row = (window.companyOverdueData || []).find(r => parseInt(r.company_id, 10) === cid);
                                if (!row) return;
                                $('#companyModulesModalLabel').text(row.company_name + (row.city_name ? ' (' + row.city_name + ')' : ''));
                                const body = $('#companyModulesModalBody');
                                if (!row.modules || row.modules.length === 0) {
                                    body.html('<div class="text-muted">No overdue modules.</div>');
                                } else {
                                    const rows = row.modules.map(m => `<tr><td>${$('<div>').text(m.module_name).html()}</td><td>${m.days_overdue}</td></tr>`).join('');
                                    body.html(`
                                        <div class="table-responsive">
                                            <table id="companyModulesTable" class="table table-sm table-bordered table-striped mb-0" style="width:100%">
                                                <thead><tr><th>Module</th><th>Days Over</th></tr></thead>
                                                <tbody>${rows}</tbody>
                                            </table>
                                        </div>`);
                                    if ($.fn.DataTable.isDataTable('#companyModulesTable')) {
                                        $('#companyModulesTable').DataTable().destroy();
                                    }
                                    $('#companyModulesTable').DataTable({
                                        pageLength: 10,
                                        lengthMenu: [
                                            [10, 25, 50, 100],
                                            [10, 25, 50, 100]
                                        ],
                                        order: [
                                            [1, 'desc']
                                        ]
                                    });
                                }
                                $('#companyModulesModal').modal('show');
                            });
                        }

                        // Employee-wise overdue (by implementation_name from society_master)
                        if (productType !== 'crm' && selectedView === 'employee') {
                            window.employeeOverdueData = response.employeeOverdues || [];
                            const tbody = $('#employeeOverdueTable tbody');
                            tbody.empty();
                            if (!window.employeeOverdueData.length) {
                                tbody.append('<tr><td class="text-muted">No employee overdues.</td><td></td><td></td></tr>');
                            } else {
                                window.employeeOverdueData.forEach(function(e) {
                                    const safeEmp = $('<div>').text(e.employee_name || '').html();
                                    const viewBtn = `<button type="button" class="btn btn-sm btn-outline-primary btn-view-employee-companies" data-employee-name="${safeEmp}"><i class="bi bi-eye"></i></button>`;
                                    tbody.append(`<tr><td>${safeEmp}</td><td>${e.overdue_count}</td><td class="text-center">${viewBtn}</td></tr>`);
                                });
                            }
                            if ($.fn.DataTable.isDataTable('#employeeOverdueTable')) {
                                $('#employeeOverdueTable').DataTable().destroy();
                            }
                            $('#employeeOverdueTable').DataTable({
                                pageLength: 25,
                                lengthMenu: [
                                    [25, 50, 100],
                                    [25, 50, 100]
                                ],
                                order: [
                                    [1, 'desc']
                                ],
                                columnDefs: [{
                                    targets: 2,
                                    orderable: false,
                                    searchable: false
                                }]
                            });

                            $(document).off('click', '.btn-view-employee-companies').on('click', '.btn-view-employee-companies', function() {
                                const ename = $(this).data('employee-name');
                                const row = (window.employeeOverdueData || []).find(r => r.employee_name === ename);
                                if (!row) return;
                                $('#employeeCompaniesModalLabel').text('Overdue Companies - ' + ename);
                                const body = $('#employeeCompaniesModalBody');
                                if (!row.companies || row.companies.length === 0) {
                                    body.html('<div class="text-muted">No overdue companies.</div>');
                                } else {
                                    const rows = row.companies.map(c => `<tr><td>${$('<div>').text(c.company_name).html()}</td><td>${$('<div>').text(c.city_name).html()}</td><td>${c.overdue_modules}</td></tr>`).join('');
                                    body.html(`
                                        <div class="table-responsive">
                                            <table id="employeeCompaniesTable" class="table table-sm table-bordered table-striped mb-0" style="width:100%">
                                                <thead><tr><th>Company</th><th>City</th><th>Overdue Modules</th></tr></thead>
                                                <tbody>${rows}</tbody>
                                            </table>
                                        </div>`);
                                    if ($.fn.DataTable.isDataTable('#employeeCompaniesTable')) {
                                        $('#employeeCompaniesTable').DataTable().destroy();
                                    }
                                    $('#employeeCompaniesTable').DataTable({
                                        pageLength: 10,
                                        lengthMenu: [
                                            [10, 25, 50, 100],
                                            [10, 25, 50, 100]
                                        ],
                                        order: [
                                            [2, 'desc']
                                        ]
                                    });
                                }
                                $('#employeeCompaniesModal').modal('show');
                            });
                        }

                        let implHTML = "";
                        const implStatsData = (response.implStats || {});
                        for (const role in implStatsData) {
                            implHTML += createImplBlock(role, implStatsData[role]);
                        }
                        $("#implStatsContainer").html(implHTML);
                    },
                    error: function() {
                        console.error("Failed to fetch dashboard response.");
                    }
                });

            }

            // Overdue view selector change handler: fetch data only for selected view
            $(document).on('change', '#overdueViewSelector', function() {
                const productType = $('#product_type').val() || 'hrms';
                if (productType !== 'crm') {
                    const view = getSelectedOverdueView();
                    showOverdueSection(view);
                    fetchCounts(view);
                }
            });

            function loadMissedTimelines() {
                const cityId = $('#city_id').val();
                const stateId = $('#state_id').val();
                $("#missedTimelinesContainer").html('<div class="row g-3 mx-2 my-0 py-0 px-2"><div class="col-12 text-muted px-2 py-2">Loading...</div></div>');
                $.ajax({
                    url: "get_training_dashboard_counts.php",
                    method: "GET",
                    dataType: "json",
                    data: {
                        cId: cityId,
                        sId: stateId,
                        rise_filter: ($('#rise_filter').length ? $('#rise_filter').val() : 'yes'),
                        expiry_filter: ($('#expiry_filter').length ? $('#expiry_filter').val() : 'not_expired')
                    },
                    success: function(response) {
                        if (!response.missedTimelineModules) {
                            $("#missedTimelinesContainer").html('<div class="mx-2 px-2 text-muted">No data.</div>');
                            return;
                        }
                        const modules = response.missedTimelineModules.filter(function(m) {
                            return parseInt(m.overdue_count) > 0;
                        });
                        if (modules.length === 0) {
                            $("#missedTimelinesContainer").html('<div class="mx-2 px-2 text-muted">No missed timelines.</div>');
                            return;
                        }
                        let missedHTML = '<div class="row g-3 mx-2 my-0 py-0 px-2"><div class="col-12">';
                        modules.forEach(function(m) {
                            const collapseId = 'overdueModule_' + m.module_id;
                            missedHTML += `
                            <div class="card shadow-sm border-0 mb-2">
                                <div class="card-body py-2 px-3 d-flex justify-content-between align-items-center" data-toggle="collapse" data-target="#${collapseId}" style="cursor:pointer;">
                                    <div>
                                        <h6 class="mb-0 text-dark">${m.module_name} <small class="text-muted">(${m.completion_days} days)</small></h6>
                                    </div>
                                    <div>
                                        <span class="badge badge-danger">${m.overdue_count} Missed</span>
                                    </div>
                                </div>
                                <div id="${collapseId}" class="collapse" data-module-id="${m.module_id}">
                                    <div class="px-3 pb-2 text-muted">Click to load companies...</div>
                                    <div class="table-responsive px-3 pb-2 d-none">
                                        <table class="table table-sm table-bordered mb-0">
                                            <thead>
                                                <tr>
                                                    <th style=\"width: 50%\">Company</th>
                                                    <th>Created</th>
                                                    <th>Due</th>
                                                    <th>Days Over</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>`;
                        });
                        missedHTML += '</div></div>';
                        $("#missedTimelinesContainer").html(missedHTML);

                        $(document).off('show.bs.collapse', '[id^="overdueModule_"]').on('show.bs.collapse', '[id^="overdueModule_"]', function() {
                            const wrap = $(this);
                            if (wrap.data('loaded')) return;
                            const moduleId = wrap.data('module-id');
                            const tableWrap = wrap.find('.table-responsive');
                            const tbody = wrap.find('tbody');
                            wrap.find('.text-muted').text('Loading...');
                            $.ajax({
                                url: 'get_missed_timelines_by_module.php',
                                method: 'GET',
                                data: {
                                    countryId: $('#country_id').val(),
                                    sId: stateId,
                                    cId: cityId,
                                    module_id: moduleId
                                },
                                success: function(html) {
                                    tbody.html(html);
                                    tableWrap.removeClass('d-none');
                                    wrap.find('.text-muted').addClass('d-none');
                                    wrap.data('loaded', true);
                                },
                                error: function() {
                                    wrap.find('.text-muted').text('Failed to load.');
                                }
                            });
                        });
                    },
                    error: function() {
                        $("#missedTimelinesContainer").html('<div class="mx-2 px-2 text-danger">Failed to load.</div>');
                    }
                });
            }

            $(document).off('click', '#btnLoadMissedTimelines').on('click', '#btnLoadMissedTimelines', function() {
                loadMissedTimelines();
            });

            const productType = $('#product_type').val() || 'hrms';
            if (productType !== 'crm') {
                const initialView = getSelectedOverdueView();
                showOverdueSection(initialView);
                fetchCounts(initialView);
            } else {
                // For CRM, only fetch implementation status
                fetchCounts();
            }

            // Not Responding Companies Modal
            let notRespondingTable = null;
            // Handle click on implementation status counts
            $(document).on('click', '.clickable-count', function(e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                
                const stageName = $(this).data('stage');
                const status = $(this).data('status');
                const count = parseInt($(this).text()) || 0;
                
                if (count === 0) {
                    return false;
                }
                
                // Get implementation status data from the current response
                if (!window.currentImplementationStatus) {
                    console.error('Implementation status data not loaded');
                    alert('Data not loaded. Please wait for the page to finish loading and try again.');
                    return;
                }
                
                if (!window.currentImplementationStatus[stageName]) {
                    console.error('Stage not found:', stageName);
                    // console.log('Available stages:', Object.keys(window.currentImplementationStatus));
                    alert('Stage data not found. Please refresh the page.');
                    return;
                }
                
                const stageData = window.currentImplementationStatus[stageName];
                
                // Check if companies data exists
                if (!stageData.companies) {
                    console.error('Companies data not found for stage:', stageName);
                    // console.log('StageData structure:', stageData);
                    alert('Company data not available for this stage.');
                    return;
                }
                
                const companies = stageData.companies[status] || [];
                
                // Destroy existing DataTable if present
                if ($.fn.DataTable.isDataTable('#companiesListTable')) {
                    $('#companiesListTable').DataTable().destroy();
                }
                
                // Update modal title and populate table
                $('#companiesListModalTitle').text(`${stageName} - ${status.charAt(0).toUpperCase() + status.slice(1)} (${count})`);
                const tbody = $('#companiesListTable tbody');
                tbody.empty();
                
                if (companies.length === 0) {
                    tbody.append('<tr><td colspan="3" class="text-center text-muted">No companies found. Count shows ' + count + ' but no company data available.</td></tr>');
                } else {
                    companies.forEach(function(company) {
                        const tr = $('<tr></tr>');
                        tr.append('<td>' + (company.company_id || '') + '</td>');
                        tr.append('<td>' + (company.company_name || '') + '</td>');
                        tr.append('<td>' + (company.city_name || '') + '</td>');
                        tbody.append(tr);
                    });
                }
                
                // Ensure modal exists and is ready
                const $modal = $('#companiesListModal');
                if ($modal.length === 0) {
                    console.error('Modal element not found');
                    alert('Modal not found. Please refresh the page.');
                    return false;
                }
                
                // console.log('About to show modal. Modal element:', $modal.length, 'Visible:', $modal.is(':visible'));
                
                // Show modal using Bootstrap modal API
                try {
                    // Remove any existing event handlers to avoid duplicates
                    $modal.off('shown.bs.modal');
                    
                    // Show the modal
                    $modal.modal('show');
                    
                    // console.log('Modal show() called');
                    
                    // Initialize DataTable after modal is fully shown
                    $modal.on('shown.bs.modal', function() {
                        // console.log('Modal shown event fired');
                        const $table = $('#companiesListTable');
                        if ($table.length > 0 && !$.fn.DataTable.isDataTable('#companiesListTable')) {
                            try {
                                $table.DataTable({
                                    pageLength: 25,
                                    lengthMenu: [[25, 50, 100], [25, 50, 100]],
                                    order: [[1, 'asc']],
                                    responsive: true,
                                    destroy: false
                                });
                                // console.log('DataTable initialized successfully');
                            } catch(e) {
                                console.error('DataTable initialization error:', e);
                            }
                        }
                    });
                } catch(e) {
                    console.error('Error showing modal:', e);
                    console.error('Error stack:', e.stack);
                    alert('Error displaying companies: ' + e.message);
                }
                
                return false;
            });
            
            $(document).on('show.bs.modal', '#notRespondingCompaniesModal', function() {
                if (notRespondingTable) {
                    notRespondingTable.destroy();
                }
                const tbody = $('#notRespondingCompaniesTable tbody');
                tbody.html('<tr><td colspan="11" class="text-center text-muted">Loading...</td></tr>');

                let cityId = $('#city_id').val();
                let stateId = $('#state_id').val();
                let durationId = $('#duration_type').val();
                const csrf = $("input[name='csrf']").val();

                $.ajax({
                    url: "get_training_dashboard_counts.php",
                    method: "GET",
                    dataType: "json",
                    data: {
                        cId: cityId,
                        sId: stateId,
                        duration_type: durationId,
                        rise_filter: ($('#rise_filter').length ? $('#rise_filter').val() : 'yes'),
                        expiry_filter: ($('#expiry_filter').length ? $('#expiry_filter').val() : 'not_expired'),
                        csrf: csrf
                    },
                    success: function(response) {
                        tbody.empty();
                        const companies = response.notRespondingCompanies || [];

                        if (companies.length != 0) {
                            companies.forEach(function(company) {
                                const safeName = $('<div>').text(company.company_name || '').html();
                                const safeCity = $('<div>').text(company.city_name || '').html();
                                const safeImpl = $('<div>').text(company.implementation_name || 'Unassigned').html();
                                const createdDate = company.created_date ? new Date(company.created_date).toLocaleDateString() : '-';
                                const closureDate = company.sales_closure_date ? new Date(company.sales_closure_date).toLocaleDateString() : '-';
                                const welcomeStatus = company.welcome_email_sent ? '<span class="badge badge-success">Sent</span>' : '<span class="badge badge-warning">Pending</span>';
                                const whatsappStatus = company.whatsapp_created ? '<span class="badge badge-success">Created</span>' : '<span class="badge badge-warning">Pending</span>';
                                const setupStatus = company.setup_status === 1 ? '<span class="badge badge-success">Completed</span>' : '<span class="badge badge-warning">Pending</span>';
                                const trainingStatus = company.training_status === 1 ? '<span class="badge badge-success">Completed</span>' : '<span class="badge badge-warning">Pending</span>';
                                const viewBtn = `<a href="companyOnboarding?countryId=${$('#country_id').val()}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i> View</a>`;

                                tbody.append(`
                                    <tr>
                                        <td>${company.company_id || ''}</td>
                                        <td>${safeName}</td>
                                        <td>${safeCity}</td>
                                        <td>${safeImpl}</td>
                                        <td>${createdDate}</td>
                                        <td>${closureDate}</td>
                                        <td class="text-center">${welcomeStatus}</td>
                                        <td class="text-center">${whatsappStatus}</td>
                                        <td class="text-center">${setupStatus}</td>
                                        <td class="text-center">${trainingStatus}</td>
                                        <td class="text-center">${viewBtn}</td>
                                    </tr>
                                `);
                            });
                        }

                        notRespondingTable = $('#notRespondingCompaniesTable').DataTable({
                            pageLength: 25,
                            lengthMenu: [
                                [25, 50, 100],
                                [25, 50, 100]
                            ],
                            order: [
                                [0, 'desc']
                            ],
                            columnDefs: [{
                                targets: 10,
                                orderable: false,
                                searchable: false
                            }]
                        });
                    },
                    error: function() {
                        tbody.html('<tr><td colspan="11" class="text-center text-danger">Failed to load data.</td></tr>');
                    }
                });
            });
        });
    </script>