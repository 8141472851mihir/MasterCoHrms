<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
?>
<div class="content-wrapper">
  <div class="container-fluid">
    <div class="row ">
      <div class="col-12 col-lg-3 col-xl-3">
        <div class="card border border-2 border-dark rounded-0">
          <a href="viewCompanies">
            <div class="p-2">
              <div class="media align-items-center">
                <div class="media-body">
                  <p class="text-dark">Companies</p>
                  <h4 class="text-dark line-height-5"> <?php if ($role_id == 1) {
                                                          echo $d->count_data_direct("society_id", "society_master", "is_demo_society=0 $countryAppendQuerySocietySingle");
                                                        } ?> </h4>
                </div>
                <div class="w-circle-icon rounded-circle border-white">
                  <img class="myIcon" src="<?php echo $bucket_url; ?>icons/ic_new_ui_company_info.png">
                </div>
              </div>
            </div>
          </a>
        </div>
      </div>
      <div class="col-12 col-lg-3 col-xl-3">
        <div class="card border border-2 border-dark rounded-0">
          <a href="crmReport">
            <div class="p-2">
              <div class="media align-items-center">
                <div class="media-body">
                  <p class="text-dark">CRM</p>
                  <h4 class="text-dark line-height-5"> <?php if ($role_id == 1) {
                                                          echo $d->count_data_direct("society_id", "society_master", "crm_created=1 $countryAppendQuerySocietySingle");
                                                        } ?> </h4>
                </div>
                <div class="w-circle-icon rounded-circle border-white">
                  <img class="myIcon" src="<?php echo $bucket_url; ?>icons/ic_new_ui_crm.png">
                </div>
              </div>
            </div>
          </a>
        </div>
      </div>
      <div class="col-12 col-lg-3 col-xl-3">
        <div class="card border border-2 border-dark rounded-0">
          <a href="manageCompanyRequests">
            <div class="p-2">
              <div class="media align-items-center">
                <div class="media-body">
                  <p class="text-dark">New Company Requests</p>
                  <h4 class="text-dark line-height-5"> <?php echo $newRequests = $d->count_data_direct("request_society_id", "society_master_requests", "request_society_create_status=0 $countryAppendQuerySocietySingleReq"); ?> </h4>
                </div>
                <div class="w-circle-icon rounded-circle border-white">
                  <img class="myIcon" src="<?php echo $bucket_url; ?>icons/ic_new_ui_lms.png">
                </div>
              </div>
            </div>
          </a>
        </div>
      </div>
      <div class="col-12 col-lg-3 col-xl-3">
        <div class="card border border-2 border-dark rounded-0">
          <a href="crmRequestsList">
            <div class="p-2">
              <div class="media align-items-center">
                <div class="media-body">
                  <p class="text-dark">New CRM Requests</p>
                  <h4 class="text-dark line-height-5"> <?php echo $rejectedRequests = $d->count_data_direct("crm_request_id", "crm_request_master", "request_status=0"); ?> </h4>
                </div>
                <div class="w-circle-icon rounded-circle border-white">
                  <img class="myIcon" src="<?php echo $bucket_url; ?>icons/ic_new_ui_leave.png">
                </div>
              </div>
            </div>
          </a>
        </div>
      </div>
      <div class="col-12 col-lg-3 col-xl-3">
        <div class="card border border-2 border-dark rounded-0">
          <a href="feedback">
            <div class="p-2">
              <div class="media align-items-center">
                <div class="media-body">
                  <p class="text-dark">Tickets with Support Team</p>
                  <h4 class="text-dark line-height-5"> <?php echo $DeveloperFeedback = $d->count_data_direct("feedback_id", "feedback_master LEFT JOIN society_master ON society_master.society_id=feedback_master.society_id", "feedback_master.feedback_status != '2' AND feedback_master.inquiry_type='0' AND feedback_master.with_developer = 0"); ?>
                  </h4>
                </div>
                <div class="w-circle-icon rounded-circle border-white">
                  <img class="myIcon" src="<?php echo $bucket_url; ?>icons/ic_new_ui_support.png">
                </div>
              </div>
            </div>
          </a>
        </div>
      </div>
      <div class="col-12 col-lg-3 col-xl-3">
        <div class="card border border-2 border-dark rounded-0">
          <a href="feedback?tab=3">
            <div class="p-2">
              <div class="media align-items-center">
                <div class="media-body">
                  <p class="text-dark">Tickets Requiring More Information</p>
                  <h4 class="text-dark line-height-5"> <?php echo $DeveloperFeedback = $d->count_data_direct("feedback_id", "feedback_master LEFT JOIN society_master ON society_master.society_id=feedback_master.society_id", "feedback_master.feedback_status='7' AND feedback_master.inquiry_type='0' AND feedback_master.with_developer=1"); ?> </h4>
                </div>
                <div class="w-circle-icon rounded-circle border-white">
                  <img class="myIcon" src="<?php echo $bucket_url; ?>icons/tickets_requiring_more_information.png">
                </div>
              </div>
            </div>
          </a>
        </div>
      </div>
      <div class="col-12 col-lg-3 col-xl-3">
        <div class="card border border-2 border-dark rounded-0">
          <a href="feedback?tab=4">
            <div class="p-2">
              <div class="media align-items-center">
                <div class="media-body">
                  <p class="text-dark">Tickets Resolved in Next Update</p>
                  <h4 class="text-dark line-height-5"> <?php echo $DeveloperFeedback = $d->count_data_direct("feedback_id", "feedback_master LEFT JOIN society_master ON society_master.society_id=feedback_master.society_id", "feedback_master.feedback_status='8' AND feedback_master.inquiry_type='0' AND feedback_master.with_developer=1"); ?> </h4>
                </div>
                <div class="w-circle-icon rounded-circle border-white">
                  <img class="myIcon" src="<?php echo $bucket_url; ?>icons/tickets_resolved_in_next_update.png">
                </div>
              </div>
            </div>
          </a>
        </div>
      </div>
      <div class="col-12 col-lg-3 col-xl-3">
        <div class="card border border-2 border-dark rounded-0">
          <a href="feedback?tab=1">
            <div class="p-2">
              <div class="media align-items-center">
                <div class="media-body">
                  <p class="text-dark">Tickets with Developer</p>
                  <h4 class="text-dark line-height-5"> <?php echo $DeveloperFeedback = $d->count_data_direct("feedback_id", "feedback_master LEFT JOIN society_master ON society_master.society_id=feedback_master.society_id", "feedback_master.feedback_status != '2' AND feedback_master.feedback_status != '7' AND feedback_master.feedback_status != '8' AND feedback_master.inquiry_type='0' AND feedback_master.with_developer = 1"); ?> </h4>
                </div>
                <div class="w-circle-icon rounded-circle border-white">
                  <img class="myIcon" src="<?php echo $bucket_url; ?>icons/ic_new_ui_complaints.png">
                </div>
              </div>
            </div>
          </a>
        </div>
      </div>
      <div class="col-12 col-lg-3 col-xl-3">
        <div class="card border border-2 border-dark rounded-0">
          <a href="manageUsers">
            <div class="p-2">
              <div class="media align-items-center">
                <div class="media-body">
                  <p class="text-dark">Active Admin Count</p>
                  <h4 class="text-dark line-height-5"> <?php echo $d->count_data_direct("admin_id", "bms_admin_master", "active_status=0"); ?> </h4>
                </div>
                <div class="w-circle-icon rounded-circle border-white">
                  <img class="myIcon" src="<?php echo $bucket_url; ?>icons/ic_new_ui_employee_profile.png">
                </div>
              </div>
            </div>
          </a>
        </div>
      </div>
      <div class="col-12 col-lg-3 col-xl-3">
        <div class="card border border-2 border-dark rounded-0">
          <a href="sliderImages">
            <div class="p-2">
              <div class="media align-items-center">
                <div class="media-body">
                  <p class="text-dark"> Banners</p>
                  <h4 class="text-dark line-height-5"> <?php echo $d->count_data_direct("app_slider_id", "app_slider_master", "slider_status=0"); ?> </h4>
                </div>
                <div class="w-circle-icon rounded-circle border-white">
                  <img class="myIcon" src="<?php echo $bucket_url; ?>icons/ic_new_ui_gallery.png">
                </div>
              </div>
            </div>
          </a>
        </div>
      </div>

      <div class="col-12 col-lg-3 col-xl-3">
        <div class="card border border-2 border-dark rounded-0">
          <a href="manageFestivals">
            <div class="p-2">
              <div class="media align-items-center">
                <div class="media-body">
                  <p class="text-dark">Festival Banner Current Year</p>
                  <h4 class="text-dark line-height-5"> <?php echo $d->count_data_direct("festival_id", "festival_master", "festival_active_status=0 AND is_festival=0 AND YEAR(festival_date) = 2024"); ?> </h4>
                </div>
                <div class="w-circle-icon rounded-circle border-white">
                  <img class="myIcon" src="<?php echo $bucket_url; ?>icons/ic_new_ui_holiday.png" style="width:60px; height: 60px;">
                </div>
              </div>
            </div>
          </a>
        </div>
      </div>
      <?php
      if ($role_id == '1') {
        $issueTypesNew = [
          'closed_rejected' => ['id' => 0, 'label' => 'Rejected', 'emoji' => '⛔'],
          'closed_bug' => ['id' => 1, 'label' => 'Bug', 'emoji' => '🐞'],
          'closed_configuration_issue' => ['id' => 2, 'label' => 'Configuration Issue', 'emoji' => '⚙️'],
          'closed_training_issue' => ['id' => 3, 'label' => 'Training Issue', 'emoji' => '🧑‍🏫'],
          'closed_issue_not_found' => ['id' => 4, 'label' => 'Issue Not Found', 'emoji' => '❌'],
          'closed_client_change_request' => ['id' => 5, 'label' => 'Change Request by Client', 'emoji' => '🔁'],
          'closed_device_issue' => ['id' => 6, 'label' => 'Device Specific Issue', 'emoji' => '📱'],
          'closed_data_delete_request' => ['id' => 7, 'label' => 'Data Delete Request', 'emoji' => '🗑️'],
          'closed_not_an_issue' => ['id' => 8, 'label' => 'Not an issue', 'emoji' => '🚫'],
          'closed_internet_connectivity_issue' => ['id' => 9, 'label' => 'Internet Connectivity Issue', 'emoji' => '🌐']
        ];

        $moduleTypes = [
          'closed_module_my_visits' => ['id' => 1, 'label' => 'My Visits', 'emoji' => '📍'], // location
          'closed_module_sales_order' => ['id' => 2, 'label' => 'Sales & Order', 'emoji' => '🛒'], // shopping
          'closed_module_work_report' => ['id' => 3, 'label' => 'Work Report', 'emoji' => '📑'], // report
          'closed_module_leave' => ['id' => 4, 'label' => 'Leave', 'emoji' => '🌴'], // leave/relax
          'closed_module_payroll' => ['id' => 5, 'label' => 'Payroll', 'emoji' => '💰'], // salary
          'closed_module_attendance' => ['id' => 6, 'label' => 'Attendance', 'emoji' => '⏱️'], // time tracking
          'closed_module_tasks' => ['id' => 7, 'label' => 'Tasks', 'emoji' => '✅'], // completed tasks
          'closed_module_tax_exemption' => ['id' => 8, 'label' => 'Tax Exemption', 'emoji' => '🧾'], // tax
          'closed_module_performance_matrix' => ['id' => 9, 'label' => 'Performance Matrix', 'emoji' => '📊'], // analytics
          'closed_module_crm' => ['id' => 10, 'label' => 'CRM', 'emoji' => '🤝'], // relationships
          'closed_module_tracking' => ['id' => 11, 'label' => 'Tracking', 'emoji' => '🛰️'], // tracking system
          'closed_biometric_attendance' => ['id' => 12, 'label' => 'Biometric Attendance', 'emoji' => '🆔'], // identity
          'closed_face_app' => ['id' => 13, 'label' => 'Face App', 'emoji' => '🧑‍💻'], // face recognition / user
          'closed_loan' => ['id' => 14, 'label' => 'Loan', 'emoji' => '🏦'], // bank/loan
          'closed_expense' => ['id' => 15, 'label' => 'Expense', 'emoji' => '💸'], // money out
          'closed_advance_expense' => ['id' => 16, 'label' => 'Advance Expense', 'emoji' => '📤'], // advance paid
          'closed_advance_salary' => ['id' => 17, 'label' => 'Advance Salary', 'emoji' => '💵'], // salary advance
          'closed_holiday' => ['id' => 18, 'label' => 'Holiday', 'emoji' => '✈️'], // vacation
          'closed_employee_document_letter' => ['id' => 19, 'label' => 'Employee Document & Letter', 'emoji' => '📂'], // documents
          'closed_app_not_opening' => ['id' => 20, 'label' => 'App Not Opening', 'emoji' => '📵'],
          'closed_log_issue' => ['id' => 21, 'label' => 'Log Issue', 'emoji' => '📋'],
          'closed_management' => ['id' => 22, 'label' => 'Management', 'emoji' => '👔'],
          'closed_employee' => ['id' => 23, 'label' => 'Employee', 'emoji' => '👤'],
          'closed_server_down' => ['id' => 24, 'label' => 'Server Down', 'emoji' => '🔌'],
          'closed_server_timeout' => ['id' => 25, 'label' => 'Server Timeout', 'emoji' => '⏳'],
          'closed_third_party_integration' => ['id' => 26, 'label' => 'Third Party Integration', 'emoji' => '🔗'],
          'closed_chat' => ['id' => 27, 'label' => 'Chat', 'emoji' => '💬'],
          'closed_timeline' => ['id' => 28, 'label' => 'Timeline', 'emoji' => '🕒'],
          'closed_assets' => ['id' => 29, 'label' => 'Assets', 'emoji' => '🧰'],
          'closed_site_management' => ['id' => 30, 'label' => 'Site Management', 'emoji' => '🏗️'],
          'closed_penalty' => ['id' => 31, 'label' => 'Penalty', 'emoji' => '⚖️'],
          'closed_module_other' => ['id' => 0, 'label' => 'Other', 'emoji' => '📦'] // misc
        ];

        $current_date = date('Y-m-d');
        $selectFields = [];
        $selectFields[] = "SUM(CASE WHEN (feedback_status IN ('0','1','3') AND isTicket != 0 AND with_developer = 1) THEN 1 ELSE 0 END) AS today_open_with_developer";
        $selectFields[] = "SUM(CASE WHEN DATE(develeoper_assign_time) = '$current_date' THEN 1 ELSE 0 END) AS today_generated_ticket";
        $selectFields[] = "SUM(CASE WHEN DATE(developer_solve_time) = '$current_date'  THEN 1 ELSE 0 END) AS today_closed_ticket";
        // Additional open-with-developer breakdowns (not date-limited)
        $selectFields[] = "SUM(CASE WHEN (feedback_status = '7' AND isTicket != 0 AND with_developer = 1) THEN 1 ELSE 0 END) AS open_need_more_spec";
        $selectFields[] = "SUM(CASE WHEN (feedback_status = '8' AND isTicket != 0 AND with_developer = 1) THEN 1 ELSE 0 END) AS open_resolved_next";
        foreach ($issueTypesNew as $field => $data) {
          $selectFields[] = "SUM(CASE WHEN DATE(developer_solve_time) = '$current_date'  AND issueType = {$data['id']} THEN 1 ELSE 0 END) AS $field";
        }
        foreach ($moduleTypes as $field => $data) {
          $selectFields[] = "SUM(CASE WHEN DATE(developer_solve_time) = '$current_date' AND issueType = 1 AND module_type = {$data['id']} THEN 1 ELSE 0 END) AS $field";
        }
        $sqlFields = implode(",\n", $selectFields);
        // MYCO (platform != 4)
        $todaysCountQryNonCrm = $d->selectRow(
          $sqlFields,
          "feedback_master",
          "feedback_master.society_id != 0 AND feedback_master.platform != '4'"
        );
        $todaysCountDataNonCrm = mysqli_fetch_array($todaysCountQryNonCrm);
        $todayOpenNonCrm = $todaysCountDataNonCrm['today_open_with_developer'] ?? 0;
        $todayAssignedNonCrm = $todaysCountDataNonCrm['today_generated_ticket'] ?? 0;
        $todayClosedNonCrm = $todaysCountDataNonCrm['today_closed_ticket'] ?? 0;
        $openNeedMoreSpecNonCrm = $todaysCountDataNonCrm['open_need_more_spec'] ?? 0;
        $openResolvedNextNonCrm = $todaysCountDataNonCrm['open_resolved_next'] ?? 0;

        $copyTextNonCrm = "🚦 *Ticket Summary (" . date('d-M-Y') . ") – MYCO*\n";
        $copyTextNonCrm .= " 🟠 Open with Developer: $todayOpenNonCrm\n";
        $copyTextNonCrm .= " 🧾 Need More Specification: $openNeedMoreSpecNonCrm\n";
        $copyTextNonCrm .= " 🧩 Resolved in Next Update: $openResolvedNextNonCrm\n";
        $copyTextNonCrm .= " 🆕 Assigned Today: $todayAssignedNonCrm\n";
        $copyTextNonCrm .= " ✅ Closed Today: $todayClosedNonCrm\n\n";
        $copyTextNonCrm .= " 📂 *Closed Ticket Breakdown:*\n";
        foreach ($issueTypesNew as $field => $data) {
          $count = $todaysCountDataNonCrm[$field] ?? 0;
          $copyTextNonCrm .= " {$data['emoji']} {$data['label']}: $count\n";
        }
        $copyTextNonCrm .= "\n🔍 *Bug Report – Module Wise:*\n";
        foreach ($moduleTypes as $field => $data) {
          $count = $todaysCountDataNonCrm[$field] ?? 0;
          $copyTextNonCrm .= " {$data['emoji']} {$data['label']}: $count\n";
        }

        // CRM (platform = 4)
        $todaysCountQryCrm = $d->selectRow(
          $sqlFields,
          "feedback_master",
          "feedback_master.society_id != 0 AND feedback_master.platform = '4'"
        );
        $todaysCountDataCrm = mysqli_fetch_array($todaysCountQryCrm);
        $todayOpenCrm = $todaysCountDataCrm['today_open_with_developer'] ?? 0;
        $todayAssignedCrm = $todaysCountDataCrm['today_generated_ticket'] ?? 0;
        $todayClosedCrm = $todaysCountDataCrm['today_closed_ticket'] ?? 0;
        $openNeedMoreSpecCrm = $todaysCountDataCrm['open_need_more_spec'] ?? 0;
        $openResolvedNextCrm = $todaysCountDataCrm['open_resolved_next'] ?? 0;

        $copyTextCrm = "🚦 *Ticket Summary (" . date('d-M-Y') . ") – CRM*\n";
        $copyTextCrm .= " 🟠 Open with Developer: $todayOpenCrm\n";
        $copyTextCrm .= " 🧾 Need More Specification: $openNeedMoreSpecCrm\n";
        $copyTextCrm .= " 🧩 Resolved in Next Update: $openResolvedNextCrm\n";
        $copyTextCrm .= " 🆕 Assigned Today: $todayAssignedCrm\n";
        $copyTextCrm .= " ✅ Closed Today: $todayClosedCrm\n\n";
        $copyTextCrm .= " 📂 *Closed Ticket Breakdown:*\n";
        foreach ($issueTypesNew as $field => $data) {
          $count = $todaysCountDataCrm[$field] ?? 0;
          $copyTextCrm .= " {$data['emoji']} {$data['label']}: $count\n";
        }
      ?>
        <div class="col-12 col-lg-3 col-xl-3">
          <div class="card border border-2 border-dark rounded-0" id="copySummaryCard">
            <div class="p-2">
              <div class="media align-items-center">
                <div class="media-body">
                  <p class="text-dark">Copy Today Ticket Status</p>

                  <div class="d-flex justify-content-start align-items-center gap-2">
                    <button id="copyNonCrmBtn" class="btn btn-sm rounded-0 my-0 w-auto">
                      <i class="fa fa-copy"></i> MYCO
                    </button>

                    <button id="copyCrmBtn" class="btn btn-sm rounded-0 my-0 w-auto">
                      <i class="fa fa-copy"></i> CRM
                    </button>
                  </div>

                  <textarea id="summaryTextNonCrm" style="display:none;"><?php echo trim($copyTextNonCrm); ?></textarea>
                  <textarea id="summaryTextCrm" style="display:none;"><?php echo trim($copyTextCrm); ?></textarea>
                </div>

                <div class="w-circle-icon rounded-circle border-white">
                  <img class="myIcon" src="<?php echo $bucket_url; ?>icons/copy_today_ticket_status.png">
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php } ?>
    </div>
  </div>
</div>
<script src="assets/js/jquery.min.js"></script>
<script src="assets/plugins/Chart.js/Chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script type="text/javascript">
  $(function() {
    "use strict";
    var canvasElement = document.getElementById("unitStatus");
    if (canvasElement) {
      var ctx = canvasElement.getContext('2d');
      var myChart = new Chart(ctx, {
        type: 'pie',
        data: {
          labels: ["Open", "With Developer"],
          datasets: [{
            backgroundColor: [
              "#2FBBA4",
              "#FFC026"
            ],
            data: [
              <?php echo isset($openFeedback) ? $openFeedback : 0; ?>,
              <?php echo isset($DeveloperFeedback) ? $DeveloperFeedback : 0; ?>
            ]
          }]
        },
        options: {
          legend: {
            position: 'bottom',
            display: true,
            labels: {
              boxWidth: 40
            }
          }
        }
      });
    }
  });
</script>
<?php if ($role_id == '1') { ?>
  <script>
    document.getElementById('copyNonCrmBtn').addEventListener('click', function() {
      const textArea = document.getElementById('summaryTextNonCrm');
      textArea.style.display = 'block';
      textArea.select();
      document.execCommand('copy');
      textArea.style.display = 'none';
      Lobibox.notify('success', {
        pauseDelayOnHover: true,
        continueDelayOnInactiveTab: false,
        position: 'top right',
        icon: 'fa fa-check-circle',
        msg: "MYCO summary has been copied to clipboard."
      });
    });
    document.getElementById('copyCrmBtn').addEventListener('click', function() {
      const textArea = document.getElementById('summaryTextCrm');
      textArea.style.display = 'block';
      textArea.select();
      document.execCommand('copy');
      textArea.style.display = 'none';
      Lobibox.notify('success', {
        pauseDelayOnHover: true,
        continueDelayOnInactiveTab: false,
        position: 'top right',
        icon: 'fa fa-check-circle',
        msg: "CRM summary has been copied to clipboard."
      });
    });
  </script>
<?php } ?>