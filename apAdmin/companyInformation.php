<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
?>
<div class="content-wrapper">
  <div class="container-fluid">
    <!-- General Company Stats -->
    <div class="row">
      <h4 class="page-title">Company Overview</h4>
    </div>
    <div class="row">
      <!-- Total Companies -->
      <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 mb-4">
        <div class="card border-left-primary h-100  mb-0" data-toggle="tooltip" title="Displays the total number of companies">
          <div class="card-body text-center pb-0">
            <div class="d-flex flex-column align-items-center">
              <div class="icon-circle bg-primary mb-2">
                <i class="fa fa-building-o text-white" aria-hidden="true"></i>
              </div>
              <h6 class="font-weight-bold text-primary mb-1">Total Companies</h6>
              <h3 class="mb-1" id="totalcompanies">0</h3>
              <div class="py-2">
                <span class="text-primary"><i class="fa fa-calendar"></i> Curr Year: <span id="thisyearcompanies">0</span></span><br>
                <span class="text-info"><i class="fa fa-calendar-check-o"></i> Curr Month: <span id="thismonthcompanies">0</span></span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Implementation Done -->
      <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 mb-4">
        <div class="card border-left-success h-100 mb-0" data-toggle="tooltip" title="Shows the total number of companies that have completed their product training(implementation)">
          <div class="card-body text-center pb-0">
            <div class="d-flex flex-column align-items-center">
              <div class="icon-circle bg-success mb-2">
                <i class="fa fa-check-circle text-white"></i>
              </div>
              <h6 class="font-weight-bold text-success mb-1">Imp Done Companies</h6>
              <h3 class="mb-1" id="totalimpdone">0</h3>
              <div class="py-2">
                <span class="text-primary"><i class="fa fa-calendar"></i> Curr Year: <span id="thisyearimpdone">0</span></span><br>
                <span class="text-info"><i class="fa fa-calendar-check-o"></i> Curr Month: <span id="thismonthimpdone">0</span></span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Pending Implementation -->
      <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 mb-4">
        <div class="card border-left-warning h-100 mb-0" data-toggle="tooltip" title="Indicates the number of companies that have not yet started their product training(implementation)">
          <div class="card-body text-center pb-0">
            <div class="d-flex flex-column align-items-center">
              <div class="icon-circle bg-warning mb-2">
                <i class="fa fa-clock-o text-white"></i>
              </div>
              <h6 class="font-weight-bold text-warning mb-1">Pending Imp Companies</h6>
              <h3 class="mb-1" id="totalimpending">0</h3>
              <div class="py-2">
                <span class="text-primary"><i class="fa fa-calendar"></i> Curr Year: <span id="thisyearimpending">0</span></span><br>
                <span class="text-info"><i class="fa fa-calendar-check-o"></i> Curr Month: <span id="thismonthimpending">0</span></span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Key Accounts -->
      <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 mb-4">
        <div class="card border-left-info h-100 mb-0" data-toggle="tooltip" title="Displays the total number of key accounts">
          <div class="card-body text-center pb-0">
            <div class="d-flex flex-column align-items-center">
              <div class="icon-circle bg-info mb-2">
                <i class="fa fa-key text-white"></i>
              </div>
              <h6 class="font-weight-bold text-info mb-1">Total Key Accounts</h6>
              <h3 class="mb-1" id="keyaccount">0</h3>
              <div class="py-2">
                <span class="text-primary"><i class="fa fa-calendar"></i> Curr Year: <span id="thisyearkey">0</span></span><br>
                <span class="text-info"><i class="fa fa-calendar-check-o"></i> Curr Month: <span id="thismonthkey">0</span></span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Key Accounts Done -->
      <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 mb-4">
        <div class="card border-left-success h-100 mb-0" data-toggle="tooltip" title="Shows the total number of key accounts that have completed their product training(implementation)">
          <div class="card-body text-center pb-0">
            <div class="d-flex flex-column align-items-center">
              <div class="icon-circle bg-success mb-2">
                <i class="fa fa-check text-white"></i>
              </div>
              <h6 class="font-weight-bold text-success mb-1">Key Accounts Imp Done</h6>
              <h3 class="mb-1" id="completionkey">0</h3>
              <div class="py-2">
                <span class="text-primary"><i class="fa fa-calendar"></i> Curr Year: <span id="thisyearkeydone">0</span></span><br>
                <span class="text-info"><i class="fa fa-calendar-check-o"></i> Curr Month: <span id="thismonthkeydone">0</span></span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Pending Key Accounts -->
      <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 mb-4">
        <div class="card border-left-danger h-100 mb-0" data-toggle="tooltip" title="Indicates the number of key accounts whose product training(implementation) is still pending or incomplete">
          <div class="card-body text-center pb-0">
            <div class="d-flex flex-column align-items-center">
              <div class="icon-circle bg-danger mb-2">
                <i class="fa fa-exclamation-circle text-white"></i>
              </div>
              <h6 class="font-weight-bold text-danger mb-1">Imp Pending Key Accounts</h6>
              <h3 class="mb-1" id="pendingkey">0</h3>
              <div class="py-2">
                <span class="text-primary"><i class="fa fa-calendar"></i> Curr Year: <span id="thisyearkeypending">0</span></span><br>
                <span class="text-info"><i class="fa fa-calendar-check-o"></i> Curr Month: <span id="thismonthkeypending">0</span></span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Trial Company Stats -->
    <div class="row mt-3">
      <h4 class="page-title">Trial Company Statistics</h4>
    </div>
    <div class="row">
      <!-- Total Trial Companies -->
      <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 mb-4">
        <div class="card border-left-primary h-100 mb-0" data-toggle="tooltip" title="Displays the total number of trial companies">
          <div class="card-body text-center pb-0">
            <div class="d-flex flex-column align-items-center">
              <div class="icon-circle bg-primary mb-2">
                <i class="fa fa-flask text-white"></i>
              </div>
              <h6 class="font-weight-bold text-primary mb-1">Trial Companies</h6>
              <h3 class="mb-1" id="trial_totaltrial">0</h3>
              <div class="py-2">
                <span class="text-primary"><i class="fa fa-calendar"></i> Curr Year: <span id="thisyeartrial">0</span></span><br>
                <span class="text-info"><i class="fa fa-calendar-check-o"></i> Curr Month: <span id="thismonthtrial">0</span></span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Trial Implementation Done -->
      <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 mb-4">
        <div class="card border-left-success h-100 mb-0" data-toggle="tooltip" title="Shows the total number of trial companies that have completed their product training(implementation)">
          <div class="card-body text-center pb-0">
            <div class="d-flex flex-column align-items-center">
              <div class="icon-circle bg-success mb-2">
                <i class="fa fa-check-circle text-white"></i>
              </div>
              <h6 class="font-weight-bold text-success mb-1">Trial Imp Done</h6>
              <h3 class="mb-1" id="trial_totalimplementation">0</h3>
              <div class="py-2">
                <span class="text-primary"><i class="fa fa-calendar"></i> Curr Year: <span id="thisyeartrialdone">0</span></span><br>
                <span class="text-info"><i class="fa fa-calendar-check-o"></i> Curr Month: <span id="thismonthtrialdone">0</span></span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Pending Trial Implementation -->
      <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 mb-4">
        <div class="card border-left-warning h-100 mb-0" data-toggle="tooltip" title="Indicates the number of trial companies whose product training(implementation) is still pending or incomplete">
          <div class="card-body text-center pb-0">
            <div class="d-flex flex-column align-items-center">
              <div class="icon-circle bg-warning mb-2">
                <i class="fa fa-hourglass-half text-white"></i>
              </div>
              <h6 class="font-weight-bold text-warning mb-1">Trial Imp Pending</h6>
              <h3 class="mb-1" id="trial_totalpending">0</h3>
              <div class="py-2">
                <span class="text-primary"><i class="fa fa-calendar"></i> Curr Year: <span id="thisyeartrialpending">0</span></span><br>
                <span class="text-info"><i class="fa fa-calendar-check-o"></i> Curr Month: <span id="thismonthtrialpending">0</span></span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Trial Key Accounts -->
      <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 mb-4">
        <div class="card border-left-info h-100 mb-0" data-toggle="tooltip" title="Displays the total number of trial key accounts">
          <div class="card-body text-center pb-0">
            <div class="d-flex flex-column align-items-center">
              <div class="icon-circle bg-info mb-2">
                <i class="fa fa-key text-white"></i>
              </div>
              <h6 class="font-weight-bold text-info mb-1">Trial Key Accounts</h6>
              <h3 class="mb-1" id="trial_keyaccount">0</h3>
              <div class="py-2">
                <span class="text-primary"><i class="fa fa-calendar"></i> Curr Year: <span id="trial_thisyearkey">0</span></span><br>
                <span class="text-info"><i class="fa fa-calendar-check-o"></i> Curr Month: <span id="trial_thismonthkey">0</span></span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Trial Key Accounts Done -->
      <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 mb-4">
        <div class="card border-left-success h-100 mb-0" data-toggle="tooltip" title="Shows the total number of trial companies that have completed their product training(implementation)">
          <div class="card-body text-center pb-0">
            <div class="d-flex flex-column align-items-center">
              <div class="icon-circle bg-success mb-2">
                <i class="fa fa-check text-white"></i>
              </div>
              <h6 class="font-weight-bold text-success mb-1">Trial Key Done</h6>
              <h3 class="mb-1" id="trial_completionkey">0</h3>
              <div class="py-2">
                <span class="text-primary"><i class="fa fa-calendar"></i> Curr Year: <span id="trial_thisyearkeydone">0</span></span><br>
                <span class="text-info"><i class="fa fa-calendar-check-o"></i> Curr Month: <span id="trial_thismonthkeydone">0</span></span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 mb-4">
        <div class="card border-left-danger h-100 mb-0" data-toggle="tooltip" title="Indicates the number of trial companies whose product training(implementation) is still pending or incomplete">
          <div class="card-body text-center pb-0">
            <div class="d-flex flex-column align-items-center">
              <div class="icon-circle bg-danger mb-2">
                <i class="fa fa-exclamation-triangle text-white"></i>
              </div>
              <h6 class="font-weight-bold text-danger mb-1">Trial Expired Companies</h6>
              <h3 class="mb-1" id="trial_totalexpired">0</h3>
              <div class="py-2">
                <span class="text-primary"><i class="fa fa-calendar"></i> Curr Year: <span id="trial_thisyearexpired">0</span></span><br>
                <span class="text-info"><i class="fa fa-calendar-check-o"></i> Curr Month: <span id="trial_thismonthexpired">0</span></span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- <div class="row mt-3">
      <h4 class="page-title">Company Statistics Dashboard</h4>
    </div>

    <div class="row">
      <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 mb-4">
        <div class="card border-left-primary h-100  mb-0">
          <div class="card-body text-center pb-0">
            <div class="d-flex flex-column align-items-center">
              <div class="icon-circle bg-primary mb-2">
                <i class="fa fa-building-o text-white" aria-hidden="true"></i>
              </div>
              <h6 class="font-weight-bold text-primary mb-1" data-toggle="tooltip" title="This shows the total number of companies from last year">Last Year Total Companies</h6>
              <h3 class="mb-1" id="lastyeartotalcompanies">0</h3>
              <div class="py-2">
                <span class="text-primary"><i class="fa fa-calendar"></i> Last Year: <span id="lastyearcompanies">0</span></span><br>
                <span class="text-info"><i class="fa fa-calendar-check-o"></i> Last Month: <span id="lastmonthcompanies">0</span></span>
              </div>
            </div>
          </div>
        </div>
      </div>


      <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 mb-4">
        <div class="card border-left-success h-100 mb-0">
          <div class="card-body text-center pb-0">
            <div class="d-flex flex-column align-items-center">
              <div class="icon-circle bg-success mb-2">
                <i class="fa fa-credit-card text-white"></i>
              </div>
              <h6 class="font-weight-bold text-success mb-1" data-toggle="tooltip" title="Number of companies renewed with payment">Renewed with Payment
              </h6>
              <h3 class="mb-1" id="renewedcompany">0</h3>
              <div class="py-2">
                <span class="text-primary"><i class="fa fa-calendar"></i> Curr Year: <span id="thisyearrenewedcompanies">0</span></span><br>
                <span class="text-info"><i class="fa fa-calendar-check-o"></i> Curr Month: <span id="thismonthrenewdcompanies">0</span></span>
              </div>
            </div>
          </div>
        </div>
      </div>


      <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 mb-4">
        <div class="card border-left-warning h-100 mb-0">
          <div class="card-body text-center pb-0">
            <div class="d-flex flex-column align-items-center">
              <div class="icon-circle bg-warning mb-2">
                <i class="fa fa-exclamation-circle text-white"></i>
              </div>
              <h6 class="font-weight-bold text-warning mb-1" data-toggle="tooltip" title="Number of companies lost (not renewed)">Lost Companies
              </h6>
              <h3 class="mb-1" id="lostcompanies">0</h3>
              <div class="py-2">
                <span class="text-primary"><i class="fa fa-calendar"></i> Curr Year: <span id="thisyearlostcompanies">0</span></span><br>
                <span class="text-info"><i class="fa fa-calendar-check-o"></i> Curr Month: <span id="thismonthlostcompanies">0</span></span>
              </div>
            </div>
          </div>
        </div>
      </div>


      <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 mb-4">
        <div class="card border-left-info h-100 mb-0">
          <div class="card-body text-center pb-0">
            <div class="d-flex flex-column align-items-center">
              <div class="icon-circle bg-info mb-2">
                <i class="fa fa-undo text-white"></i>
              </div>
              <h6 class="font-weight-bold text-info mb-1">Refunds Companies
              </h6>
              <h3 class="mb-1" id="refundscompanies">0</h3>
              <div class="py-2">
                <span class="text-primary"><i class="fa fa-calendar"></i> Curr Year: <span id="thisyearrefundscompanies">0</span></span><br>
                <span class="text-info"><i class="fa fa-calendar-check-o"></i> Curr Month: <span id="thismonthrefundscompanies">0</span></span>
              </div>
            </div>
          </div>
        </div>
      </div>


      <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 mb-4">
        <div class="card border-left-info h-100 mb-0">
          <div class="card-body text-center pb-0">
            <div class="d-flex flex-column align-items-center">
              <div class="icon-circle bg-info mb-2">
                <i class="fa fa-check text-white"></i>
              </div>
              <h6 class="font-weight-bold text-info mb-1" data-toggle="tooltip"
                title="Companies that converted from trial to paid, including both successful and unsuccessful conversions.">
                Trial to Paid Companies(Success & Loss) </h6>
              <h3 class="mb-1" id="trialtopaidcompanies">0</h3>
              <div class="py-2">
                <span class="text-primary"><i class="fa fa-calendar"></i> Curr Year: <span id="thisyear_trail_to_paid_companies">0</span></span><br>
                <span class="text-info"><i class="fa fa-calendar-check-o"></i> Curr Month: <span id="thismonth_trail_to_paid_companies">0</span></span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 mb-4">
        <div class="card border-left-success h-100 mb-0">
          <div class="card-body text-center pb-0">
            <div class="d-flex flex-column align-items-center">
              <div class="icon-circle bg-success mb-2">
                <i class="fa fa-exclamation-triangle text-white"></i>
              </div>
              <h6 class="font-weight-bold text-success mb-1" data-toggle="tooltip"
                title="Companies whose subscriptions or services are nearing expiration.">Expiring Companies
              </h6>
              <h3 class="mb-1" id="expiredcompanies">0</h3>
              <div class="py-2">
                <span class="text-primary"><i class="fa fa-calendar"></i> Curr Year: <span id="thisyearexpiredcompanies">0</span></span><br>
                <span class="text-info"><i class="fa fa-calendar-check-o"></i> Curr Month: <span id="thismonthexpairedcompanies">0</span></span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 mb-4">
        <div class="card border-left-danger h-100  mb-0">
          <div class="card-body text-center pb-0">
            <div class="d-flex flex-column align-items-center">
              <div class="icon-circle bg-danger mb-2">
                <i class="fa fa-money text-white" aria-hidden="true"></i>
              </div>
              <h6 class="font-weight-bold text-danger mb-1" data-toggle="tooltip"
                title="Companies currently using payroll services">Payroll using Companies
              </h6>
              <h3 class="mb-1" id="payrollcompanies">0</h3>
              <div class="py-2">
                <span class="text-primary"><i class="fa fa-calendar"></i> Curr Year: <span id="thisyearpayrollcompanies">0</span></span><br>
                <span class="text-info"><i class="fa fa-calendar-check-o"></i> Curr Month: <span id="thismonthpayrollcompanies">0</span></span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div> -->

    <!-- Add this CSS in your header or style tag -->
    <style>
      .icon-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
      }

      .card {
        transition: all 0.3s ease;
      }

      .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      }


      .card.no-hover-transform:hover {
        transform: none !important;
      }



      .border-left-primary {
        border-left: 4px solid #4e73df;
      }

      .border-left-success {
        border-left: 4px solid #1cc88a;
      }

      .border-left-info {
        border-left: 4px solid #36b9cc;
      }

      .border-left-warning {
        border-left: 4px solid #f6c23e;
      }

      .border-left-danger {
        border-left: 4px solid #e74a3b;
      }
    </style>

    <!-- Rest of your existing code (form, tables, etc.) -->
    <!-- ... -->
    <div class="card no-hover-transform">
      <div class="card-body">
        <form action="" method="get">
          <div class="row mb-3">
            <div class="col-md-4">
              <label for="accountType">Account Type</label>
              <select id="accountType" class="form-control single-select" name="accountType" onchange="this.form.submit()">
                <option value="2" <?php echo (!isset($_GET['accountType']) or $_GET['accountType'] == '') ? "selected" : ""; ?>>All</option>
                <option value="0" <?php echo (isset($_GET['accountType']) && $_GET['accountType'] == '0') ? "selected" : ""; ?>>Normal Account</option>
                <option value="1" <?php echo (isset($_GET['accountType']) && $_GET['accountType'] == '1') ? "selected" : ""; ?>>Key Account</option>
              </select>
            </div>
            <div class="col-md-4">
              <label for="planType">Plan</label>
              <select id="planType" class="form-control single-select" name="planType" onchange="this.form.submit()">
                <option value="2" <?php echo (!isset($_GET['planType']) or $_GET['planType'] == '2') ? "selected" : ""; ?>>All
                </option>
                <option value="0" <?php echo (isset($_GET['planType']) && $_GET['planType'] == '0') ? "selected" : ""; ?>>
                  Trial</option>
                <option value="1" <?php echo (isset($_GET['planType']) && $_GET['planType'] == '1') ? "selected" : ""; ?>>Full
                  Paid</option>
              </select>
            </div>
            <div class="col-md-4">
              <label for="duration">Duration</label>
              <select id="duration" class="form-control single-select" name="duration" onchange="this.form.submit()">
                <option value="2" <?php echo (!isset($_GET['duration']) or $_GET['duration'] == '2') ? "selected" : ""; ?>>All
                </option>
                <option value="0" <?php echo (isset($_GET['duration']) && $_GET['duration'] == '0') ? "selected" : ""; ?>>This
                  Month</option>
                <option value="1" <?php echo (isset($_GET['duration']) && $_GET['duration'] == '1') ? "selected" : ""; ?>>This
                  Year</option>
              </select>
            </div>
            <input type="hidden" name="tab" value="<?php echo isset($_GET['tab']) ? $_GET['tab'] : 'company'; ?>">
          </div>
        </form>
        <?php
        $accountFilter = '';
        $planFilter = '';
        $durationFilter = '';
        if (isset($_GET['accountType']) && $_GET['accountType'] != '2') {
          $type = $_GET['accountType'];
          $accountFilter = " AND sm.account_type = '$type'";
        }
        if (isset($_GET['planType']) && $_GET['planType'] != '2') {
          $plan = $_GET['planType'];
          if ($plan == '0') {
            $planFilter = " AND sm.package_id = '0'";
          } else if ($plan == '1') {
            $planFilter = " AND sm.package_id != '0'";
          }
        } else {
          $planFilter = "";
        }
        if (isset($_GET['duration']) && $_GET['duration'] != '2') {
          if ($_GET['duration'] == 0) {
            $currentMonth = date('m');
            $currentYear = date('Y');
            $durationFilter = " AND MONTH(sm.created_date) = '$currentMonth' AND YEAR(sm.created_date) = '$currentYear'";
          } else if ($_GET['duration'] == 1) {
            $currentYear = date('Y');
            $durationFilter = " AND YEAR(sm.created_date) = '$currentYear'";
          }
        }
        ?>
        <ul class="nav nav-tabs nav-tabs-info nav-justified" id="companyTabs" role="tablist">
          <li class="nav-item">
            <a class="nav-link <?php echo (!isset($_GET['tab']) || $_GET['tab'] == 'company') ? 'active' : ''; ?>"
              id="company-tab" href="?tab=company" role="tab" aria-controls="company" aria-selected="true">Company</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'trial-company') ? 'active' : ''; ?>"
              id="trial-company-tab" href="?tab=trial-company" role="tab" aria-controls="trial-company"
              aria-selected="false">Trial Company</a>
          </li>
        </ul>
        <!-- Comapny  Tab -->
        <div class="tab-content" id="companyTabsContent">
          <div
            class="tab-pane fade <?php echo (!isset($_GET['tab']) || $_GET['tab'] == 'company') ? 'show active' : ''; ?>"
            id="company" role="tabpanel" aria-labelledby="company-tab">
            <div class="card-body">
              <div class="table-responsive">
                <table id="example" class="table table-bordered">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Company Id</th>
                      <th>Company Name</th>
                      <th>City</th>
                      <th>Account Type</th>
                      <th>Plan</th>
                      <th>Start Date</th>
                      <th>End Date</th>
                      <th>Total Trial Days</th>
                      <th>Employee Registration Limit</th>
                      <th>Trial Receive Amount</th>
                      <th>Expected Amount</th>
                      <th>Expected Team Size</th>
                      <th>Implementation Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $q = $d->selectRow(
                      "sm.*, c.name as city_name",
                      "society_master sm LEFT JOIN cities c ON sm.city_id = c.city_id",
                      "sm.created_on_society_server = 1 AND sm.society_id !='' $accountFilter $planFilter $durationFilter",
                      "order by society_id  DESC",
                      "1"
                    );
                    $i = 1;
                    while ($data = mysqli_fetch_array($q)) {
                    ?>
                      <tr>
                        <td><?php echo $i++; ?></td>
                        <td>
                          <span style="display: none;"><?php echo $data['society_id']; ?></span>
                          <?php echo $d->short_app_name() . '_' . $data['society_id']; ?>
                        </td>
                        <td><?php echo $data['society_name']; ?></td>
                        <td><?php echo $data['city_name']; ?></td>
                        <td>
                          <?php
                          echo ($data['account_type'] == 0) ? 'Normal Account' : 'Key Account';
                          ?>
                        </td>
                        <td>
                          <?php
                          echo ($data['package_id'] == 0) ? 'Trial' : 'Full Paid';
                          ?>
                        </td>
                        <td><?php
                            $createdDate = $data['created_date'];
                            echo (empty($createdDate) || $createdDate == '0000-00-00') ? '' : date("d-M-Y", strtotime($createdDate));
                            ?></td>
                        <td><?php
                            $expireDate = $data['plan_expire_date'];
                            echo (empty($expireDate) || $expireDate == '0000-00-00') ? '' : date("d-M-Y", strtotime($expireDate));
                            ?></td>
                        <td><?php echo $data['trial_days']; ?></td>
                        <td><?php echo $data['employee_registration_limit']; ?></td>
                        <td><?php echo $data['received_ticket_size']; ?></td>
                        <td><?php echo $data['yearly_ticket_size']; ?></td>
                        <td><?php echo $data['expected_team_size']; ?></td>
                        <td>
                          <?php
                          echo ($data['training_status'] == 1) ? 'Completed' : 'Pending';
                          ?>
                        </td>
                      </tr>
                    <?php
                    }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <!-- Trial Company -->
          <div
            class="tab-pane fade <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'trial-company') ? 'show active' : ''; ?>"
            id="trial-company" role="tabpanel" aria-labelledby="trial-company-tab">
            <div class="card-body">
              <div class="table-responsive">
                <table id="example" class="table table-bordered">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Company Id</th>
                      <th>Company Name</th>
                      <th>City</th>
                      <th>Account Type</th>
                      <th>Plan</th>
                      <th>Start Date</th>
                      <th>End Date</th>
                      <th>Total Trial Days</th>
                      <th>Employee Registration Limit</th>
                      <th>Trial Receive Amount</th>
                      <th>Expected Amount</th>
                      <th>Expected Team Size</th>
                      <th>Implementation Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $i = 1;
                    $q = $d->selectRow(
                      "sm.*, c.name as city_name",
                      "society_master sm LEFT JOIN cities c ON sm.city_id = c.city_id",
                      "sm.created_on_society_server = 1 AND sm.plan_expire_date IS NOT NULL AND (sm.last_renew_date IS NULL OR sm.last_renew_date = '') AND sm.package_id = 0 $accountFilter $planFilter $durationFilter",
                      "order by society_id  DESC",
                      "1"
                    );
                    while ($data = mysqli_fetch_array($q)) {
                    ?>
                      <tr>
                        <td><?php echo $i++; ?></td>
                        <td>
                          <span style="display: none;"><?php echo $data['society_id']; ?></span>
                          <?php echo $d->short_app_name() . '_' . $data['society_id']; ?>
                        </td>
                        <td><?php echo $data['society_name']; ?></td>
                        <td><?php echo $data['city_name']; ?></td>
                        <td><?php echo ($data['account_type'] == 0) ? 'Normal Account' : 'Key Account'; ?></td>
                        <td><?php echo ($data['package_id'] == 0) ? 'Trial' : 'Full Paid'; ?></td>
                        <td><?php
                            $createdDate = $data['created_date'];
                            echo (empty($createdDate) || $createdDate == '0000-00-00') ? '' : date("d-M-Y", strtotime($createdDate));
                            ?></td>
                        <td><?php
                            $expireDate = $data['plan_expire_date'];
                            echo (empty($expireDate) || $expireDate == '0000-00-00') ? '' : date("d-M-Y", strtotime($expireDate));
                            ?></td>
                        <td><?php echo $data['trial_days']; ?></td>
                        <td><?php echo $data['employee_registration_limit']; ?></td>
                        <td><?php echo $data['received_ticket_size']; ?></td>
                        <td><?php echo $data['yearly_ticket_size']; ?></td>
                        <td><?php echo $data['expected_team_size']; ?></td>
                        <td><?php echo ($data['training_status'] == 1) ? 'Completed' : 'Pending'; ?></td>
                      </tr>
                    <?php
                    }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $(document).ready(function() {
    function fetchCounts() {
      $.ajax({
        url: 'get_company_information_dashboard_counts.php',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
          // General counts
          $('#totalcompanies').text(data.totalcompanies);
          $('#thisyearcompanies').text(data.thisyearcompanies);
          $('#thismonthcompanies').text(data.thismonthcompanies);

          $('#totalimpdone').text(data.totalimpdone);
          $('#thisyearimpdone').text(data.thisyearimpdone);
          $('#thismonthimpdone').text(data.thismonthimpdone);

          $('#totalimpending').text(data.totalimpending);
          $('#thisyearimpending').text(data.thisyearimpending);
          $('#thismonthimpending').text(data.thismonthimpending);

          $('#keyaccount').text(data.keyaccount);
          $('#thisyearkey').text(data.thisyearkey);
          $('#thismonthkey').text(data.thismonthkey);

          $('#completionkey').text(data.completionkey);
          $('#thisyearkeydone').text(data.thisyearkeydone);
          $('#thismonthkeydone').text(data.thismonthkeydone);

          $('#pendingkey').text(data.pendingkey);
          $('#thisyearkeypending').text(data.thisyearkeypending);
          $('#thismonthkeypending').text(data.thismonthkeypending);

          // $('#currentmonth').text(data.currentmonth);
          // $('#completionmonth').text(data.completionmonth);

          // Trial counts
          $('#trial_totaltrial').text(data.trial_totaltrial);
          $('#thisyeartrial').text(data.thisyeartrial);
          $('#thismonthtrial').text(data.thismonthtrial);

          $('#trial_totalimplementation').text(data.trial_totalimplementation);
          $('#thisyeartrialdone').text(data.thisyeartrialdone);
          $('#thismonthtrialdone').text(data.thismonthtrialdone);

          $('#trial_totalpending').text(data.trial_totalpending);
          $('#thisyeartrialpending').text(data.thisyeartrialpending);
          $('#thismonthtrialpending').text(data.thismonthtrialpending);

          $('#trial_planexpired_month').text(data.trial_planexpired_month);
          $('#trial_completionmonth').text(data.trial_completionmonth);

          $('#trial_keyaccount').text(data.trial_keyaccount);
          $('#trial_thisyearkey').text(data.trial_thisyearkey);
          $('#trial_thismonthkey').text(data.trial_thismonthkey);

          $('#trial_completionkey').text(data.trial_completionkey);
          $('#trial_thisyearkeydone').text(data.trial_thisyearkeydone);
          $('#trial_thismonthkeydone').text(data.trial_thismonthkeydone);

          $('#trial_pendingkey').text(data.trial_pendingkey);
          $('#trial_thisyearkeypending').text(data.trial_thisyearkeypending);
          $('#trial_thismonthkeypending').text(data.trial_thismonthkeypending);
          $('#trial_totalexpired').text(data.trial_totalexpired);
          $('#trial_thisyearexpired').text(data.trial_thisyearexpired);
          $('#trial_thismonthexpired').text(data.trial_thismonthexpired);

          //company dashboard
          $('#lastyeartotalcompanies').text(data.lastyeartotalcompanies);
          $('#lastyearcompanies').text(data.lastyearcompanies);
          $('#lastmonthcompanies').text(data.lastmonthcompanies);

          $('#renewedcompany').text(data.renewedcompany);
          $('#thisyearrenewedcompanies').text(data.thisyearrenewedcompanies);
          $('#thismonthrenewdcompanies').text(data.thismonthrenewdcompanies);

          $('#lostcompanies').text(data.lostcompanies);
          $('#thisyearlostcompanies').text(data.thisyearlostcompanies);
          $('#thismonthlostcompanies').text(data.thismonthlostcompanies);

          $('#refundscompanies').text(data.refundscompanies);
          $('#thisyearrefundscompanies').text(data.thisyearrefundscompanies);
          $('#thismonthrefundscompanies').text(data.thismonthrefundscompanies);

          $('#trialtopaidcompanies').text(data.trialtopaidcompanies);
          $('#thisyear_trail_to_paid_companies').text(data.thisyear_trail_to_paid_companies);
          $('#thismonth_trail_to_paid_companies').text(data.thismonth_trail_to_paid_companies);

          $('#expiredcompanies').text(data.expiredcompanies);
          $('#thisyearexpiredcompanies').text(data.thisyearexpiredcompanies);
          $('#thismonthexpairedcompanies').text(data.thismonthexpairedcompanies);

          $('#payrollcompanies').text(data.payrollcompanies);
          $('#thisyearpayrollcompanies').text(data.thisyearpayrollcompanies);
          $('#thismonthpayrollcompanies').text(data.thismonthpayrollcompanies);
        },
      });
    }
    fetchCounts();
    // Optional: Refresh counts every 5 minutes
    setInterval(fetchCounts, 300000);
  });
</script>
<!-- <script>
  document.addEventListener('DOMContentLoaded', function () {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function (tooltipTriggerEl) {
      new bootstrap.Tooltip(tooltipTriggerEl);
    });
  });
</script> -->