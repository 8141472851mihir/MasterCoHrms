<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row mb-0 pb-0">
            <div class="col-sm-9 col-12">
                <h4 class="page-title">Company Dashboard</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center mb-3">
                            <div class="col-md-6">
                                <h5 class="mb-2 mb-md-0">Company Creations</h5>
                            </div>

                            <div class="col-md-3">
                                <form action="" method="get">
                                    <input type="hidden" name="duration_type" value="<?= isset($_GET['duration_type']) ? $_GET['duration_type'] : '0' ?>">
                                    <input type="hidden" name="duration_request_type" value="<?= isset($_GET['duration_request_type']) ? $_GET['duration_request_type'] : '0' ?>">
                                    <select required id="company_registration" name="company_registration"
                                        onchange="this.form.submit()" class="form-control single-select">
                                        <option value="1" <?= (isset($_GET['company_registration']) && $_GET['company_registration'] == '1') ? "selected" : ""; ?>>
                                            Trail
                                        </option>
                                        <option value="0" <?= (!isset($_GET['company_registration']) || $_GET['company_registration'] == '0') ? "selected" : ""; ?>>
                                            All
                                        </option>
                                        <option value="2" <?= (isset($_GET['company_registration']) && $_GET['company_registration'] == '2') ? "selected" : ""; ?>>
                                            New Registration
                                        </option>
                                        <option value="3" <?= (isset($_GET['company_registration']) && $_GET['company_registration'] == '3') ? "selected" : ""; ?>>
                                            Trial->Renewal
                                        </option>
                                         <option value="4" <?= (isset($_GET['company_registration']) && $_GET['company_registration'] == '4') ? "selected" : ""; ?>>
                                             New Registration + Trial->Renewal
                                        </option>
                                    </select>
                                </form>
                            </div>

                            <div class="col-md-3">
                                <form action="" method="get">
                                     <input type="hidden" name="company_registration" value="<?= isset($_GET['company_registration']) ? $_GET['company_registration'] : '0' ?>">
                                     <input type="hidden" name="duration_request_type" value="<?= isset($_GET['duration_request_type']) ? $_GET['duration_request_type'] : '0' ?>">
                                    <select required id="duration_type" name="duration_type"
                                        onchange="this.form.submit()" class="form-control single-select">
                                        <option value="1" <?= (isset($_GET['duration_type']) && $_GET['duration_type'] == '1') ? "selected" : ""; ?>>
                                            Last 30 Days
                                        </option>
                                        <option value="0" <?= (!isset($_GET['duration_type']) || $_GET['duration_type'] == '0') ? "selected" : ""; ?>>
                                            Last 12 Months
                                        </option>
                                    </select>
                                </form>
                            </div>
                        </div>

                        <div id="company-registration"></div>
                    </div>
                </div>
            </div>
        </div>
     
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-3">
                            <h5 class="mb-2 mb-md-0">Company Registrations Requests</h5>
                            <form action="" method="get" class="col-12 col-md-2">
                                <input type="hidden" name="company_registration" value="<?= isset($_GET['company_registration']) ? $_GET['company_registration'] : '0' ?>">
                                <input type="hidden" name="duration_type" value="<?= isset($_GET['duration_type']) ? $_GET['duration_type'] : '0' ?>">
                                <select required id="duration_request_type" name="duration_request_type"
                                    onchange="this.form.submit()" class="form-control single-select">
                                    <option value="1" <?php echo (isset($_GET['duration_request_type']) && $_GET['duration_request_type'] == '1') ? "selected" : ""; ?>>
                                        Last 30 Days
                                    </option>
                                    <option value="0" <?php echo (!isset($_GET['duration_request_type']) || $_GET['duration_request_type'] == '0') ? "selected" : ""; ?>>
                                        Last 12 Months
                                    </option>
                                </select>
                            </form>
                        </div>
                        <div id="company-request"></div>
                    </div>
                </div>
            </div>
        </div>


    </div>
</div>
<script src="assets/js/jquery.min.js"></script>
<script src="assets/plugins/Chart.js/Chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script type="text/javascript">
    $(window).on("load", function () {
        let durationId = $('#duration_type').val();
        let companyId = $('#company_registration').val();

        $.ajax({
            url: "controller/companyDashboardController.php",
            cache: false,
            type: "POST",
            data: {
                getCompanyDashboardData: "getCompanyDashboardData",
                duration_type: durationId,
                company_type: companyId,
                csrf: csrf,
            },
            success: function (response) {
                try {
                    var data = JSON.parse(response);
                    if (!data || !data.monthNameList || !data.monthWiseRegistrationCount) {
                        // console.error("Invalid response data", data);
                        return;
                    }
                    var options = {
                        series: [{
                            name: 'Creations',
                            data: data.monthWiseRegistrationCount
                        }],
                        chart: {
                            foreColor: "#2f648e",
                            height: 250,
                            type: "line",
                            zoom: { enabled: false },
                            toolbar: { show: false },
                            dropShadow: {
                                enabled: true,
                                top: 3,
                                left: 14,
                                blur: 4,
                                opacity: 0.1
                            }
                        },
                        stroke: {
                            width: 3,
                            curve: "smooth"
                        },
                        xaxis: {
                            categories: data.monthNameList
                        },
                        markers: {
                            size: 5,
                            colors: ["#0d6efd"],
                            strokeColors: "#fff",
                            strokeWidth: 2,
                            hover: { size: 7 }
                        },
                        dataLabels: { enabled: false },
                        colors: ["#2f648e"],
                        responsive: [{
                            breakpoint: 480,
                            options: {
                                chart: { height: 310 },
                                legend: { position: 'bottom' },
                                title: {
                                    floating: true,
                                    align: 'center',
                                    style: { fontSize: '13px', color: '#444' }
                                }
                            }
                        }]
                    };

                    new ApexCharts(document.querySelector("#company-registration"), options).render();
                } catch (e) {
                    // console.error("Error parsing response", e);
                }
            },
            error: function (xhr, status, error) {
                // console.error("AJAX error:", status, error);
            }
        });
    });

    $(window).on("load", function () {
        let durationId = $('#duration_request_type').val();
        $.ajax({
            url: "controller/companyDashboardController.php",
            cache: false,
            type: "POST",
            data: {
                getCompanyDashboardRequestData: "getCompanyDashboardRequestData",
                duration_type: durationId,
                csrf: csrf,
            },
            success: function (response) {
                try {
                    var data = JSON.parse(response);
                    if (!data || !data.monthNameRequestList || !data.monthWiseRegistrationRequestCount) {
                        // console.error("Invalid response data", data);
                        return;
                    }
                    var options = {
                        series: [{
                            name: 'Registrations',
                            data: data.monthWiseRegistrationRequestCount
                        }],
                        chart: {
                            foreColor: "#2f648e",
                            height: 250,
                            type: "line",
                            zoom: { enabled: false },
                            toolbar: { show: false },
                            dropShadow: {
                                enabled: true,
                                top: 3,
                                left: 14,
                                blur: 4,
                                opacity: 0.1
                            }
                        },
                        stroke: {
                            width: 3,
                            curve: "smooth"
                        },
                        xaxis: {
                            categories: data.monthNameRequestList
                        },
                        markers: {
                            size: 5,
                            colors: ["#0d6efd"],
                            strokeColors: "#fff",
                            strokeWidth: 2,
                            hover: { size: 7 }
                        },
                        dataLabels: { enabled: false },
                        colors: ["#2f648e"],
                        responsive: [{
                            breakpoint: 480,
                            options: {
                                chart: { height: 310 },
                                legend: { position: 'bottom' },
                                title: {
                                    floating: true,
                                    align: 'center',
                                    style: { fontSize: '13px', color: '#444' }
                                }
                            }
                        }]
                    };

                    new ApexCharts(document.querySelector("#company-request"), options).render();
                } catch (e) {
                    // console.error("Error parsing response", e);
                }
            },
            error: function (xhr, status, error) {
                // console.error("AJAX error:", status, error);
            }
        });
    });
</script>