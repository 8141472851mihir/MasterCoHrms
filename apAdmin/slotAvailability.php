<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-9">
        <h4 class="page-title">Slot Availability</h4>
      </div>
      <div class="col-sm-3">
        <div class="btn-group float-sm-right">
          <?php
          // Get return URL from query parameter or default to manageTrainingSlots
          $returnUrl = isset($_GET['return']) ? $_GET['return'] : 'manageTrainingSlots';
          ?>
          <a href="<?php echo htmlspecialchars($returnUrl); ?>" class="btn btn-secondary btn-sm">
            <i class="fa fa-arrow-left mr-1"></i>Back
          </a>
        </div>
      </div>
    </div>
    <!-- End Breadcrumb-->
    
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div class="mb-3">
              <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-primary active" id="viewBySlotsBtn">
                  <i class="fa fa-list"></i> List by Slots
                </button>
                <button type="button" class="btn btn-outline-primary" id="viewByBatchBtn">
                  <i class="fa fa-th-large"></i> List by Batch
                </button>
              </div>
            </div>

            <div id="slotsView">
              <h6 class="mb-3">Upcoming & Today's Slots</h6>
              <div class="table-responsive">
                <table class="table table-bordered table-sm">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Slot Name</th>
                      <th>Batch Name</th>
                      <th>Date</th>
                      <th>Time</th>
                      <th>Trainer</th>
                      <th>City</th>
                      <th>Companies</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody id="slotsTableBody">
                    <?php
                    $today = date('Y-m-d');
                    $slotsQuery = $d->selectRow(
                      "bsm.slot_id, bsm.slot_name, bsm.date, bsm.from_time, bsm.to_time, bsm.city, bsm.company_id, bsm.reference_company_ids, bsm.batch_id,
                       bam.admin_name AS trainer_name, tbm.batch_name",
                      "batch_slot_master bsm
                       LEFT JOIN bms_admin_master bam ON bam.admin_id = bsm.trainer_id
                       LEFT JOIN training_batch_master tbm ON tbm.batch_id = bsm.batch_id",
                      "bsm.date >= '$today' AND bsm.year = YEAR(CURDATE())",
                      "ORDER BY bsm.date ASC, bsm.from_time ASC"
                    );
                    $slotIndex = 1;
                    while ($slotRow = mysqli_fetch_assoc($slotsQuery)) {
                      $slotDate = $slotRow['date'];
                      $slotCompanies = !empty($slotRow['company_id']) ? explode(',', $slotRow['company_id']) : [];
                      $slotReferenceIds = !empty($slotRow['reference_company_ids']) ? explode(',', $slotRow['reference_company_ids']) : [];
                      $slotCompanies = array_filter($slotCompanies);
                      $slotReferenceIds = array_filter($slotReferenceIds);
                      $totalSlotCompanies = count($slotCompanies) + count($slotReferenceIds);
                      $hasCompanies = $totalSlotCompanies > 0;
                      $dateClass = ($slotDate == $today) ? 'text-warning font-weight-bold' : '';
                      
                      // Format slot name: remove meeting number (M1, M2, etc.) and show count
                      $slotNameFormatted = $slotRow['slot_name'];
                      // Remove meeting number pattern like -M1, -M2, etc.
                      $slotNameFormatted = preg_replace('/-M\d+(-?\d*)$/', '$1', $slotNameFormatted);
                      // If there's a count suffix like -2, format it as " - 2"
                      if (preg_match('/^(.+)-(\d+)$/', $slotNameFormatted, $matches)) {
                        $slotNameFormatted = $matches[1] . ' - ' . $matches[2];
                      }
                      $slotNameDisplay = htmlspecialchars($slotNameFormatted);
                      $slotNameForJS = htmlspecialchars($slotNameFormatted, ENT_QUOTES);
                    ?>
                      <tr class="<?php echo $hasCompanies ? '' : 'table-secondary'; ?>">
                        <td><?php echo $slotIndex++; ?></td>
                        <td><?php echo $slotNameDisplay; ?></td>
                        <td><?php echo htmlspecialchars($slotRow['batch_name'] ?? 'N/A'); ?></td>
                        <td class="<?php echo $dateClass; ?>">
                          <?php 
                          echo date('d M Y', strtotime($slotDate));
                          if ($slotDate == $today) echo ' <span class="badge badge-warning">Today</span>';
                          ?>
                        </td>
                        <td><?php echo date('h:i A', strtotime($slotRow['from_time'])) . ' - ' . date('h:i A', strtotime($slotRow['to_time'])); ?></td>
                        <td><?php echo htmlspecialchars($slotRow['trainer_name'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($slotRow['city'] ?? '-'); ?></td>
                        <td>
                          <?php if ($hasCompanies): ?>
                            <span class="badge badge-success"><?php echo $totalSlotCompanies; ?> Company(s)</span>
                          <?php else: ?>
                            <span class="badge badge-secondary">No Companies</span>
                          <?php endif; ?>
                        </td>
                        <td>
                          <button type="button" class="btn btn-sm btn-info" onclick="showSlotCompanies(<?php echo $slotRow['slot_id']; ?>, '<?php echo $slotNameForJS; ?>')">
                            View Details
                          </button>
                        </td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>

            <div id="batchView" style="display: none;">
              <h6 class="mb-3">Upcoming & Today's Slots (Grouped by Slot Pattern)</h6>
              <div class="table-responsive">
                <table class="table table-bordered table-sm">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Slot Pattern</th>
                      <th>Date Range</th>
                      <th>Total Slots</th>
                      <th>Slots with Companies</th>
                      <th>Total Unique Companies</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody id="batchTableBody">
                    <?php
                    // Get all upcoming slots
                    $allSlotsQuery = $d->selectRow(
                      "bsm.slot_id, bsm.slot_name, bsm.date, bsm.company_id, bsm.reference_company_ids, bsm.batch_id,
                       tbm.batch_name",
                      "batch_slot_master bsm
                       LEFT JOIN training_batch_master tbm ON tbm.batch_id = bsm.batch_id",
                      "bsm.date >= '$today' AND bsm.year = YEAR(CURDATE())",
                      "ORDER BY bsm.slot_name ASC, bsm.date ASC"
                    );
                    
                    // Group slots by base pattern (remove meeting number -M1, -M2, etc.)
                    $slotGroups = [];
                    while ($slotRow = mysqli_fetch_assoc($allSlotsQuery)) {
                      // Extract base slot pattern: remove -M{number} part
                      $slotName = $slotRow['slot_name'];
                      // Remove meeting number pattern like -M1, -M2, etc., but keep count if present
                      $basePattern = preg_replace('/-M\d+(-?\d*)$/', '$1', $slotName);
                      
                      // Initialize group if not exists
                      if (!isset($slotGroups[$basePattern])) {
                        $slotGroups[$basePattern] = [
                          'pattern' => $basePattern,
                          'slots' => [],
                          'earliest_date' => $slotRow['date'],
                          'latest_date' => $slotRow['date'],
                          'total_slots' => 0,
                          'slots_with_companies' => 0,
                          'all_company_ids' => []
                        ];
                      }
                      
                      // Add slot to group
                      $slotGroups[$basePattern]['slots'][] = $slotRow;
                      $slotGroups[$basePattern]['total_slots']++;
                      
                      // Update date range
                      if ($slotRow['date'] < $slotGroups[$basePattern]['earliest_date']) {
                        $slotGroups[$basePattern]['earliest_date'] = $slotRow['date'];
                      }
                      if ($slotRow['date'] > $slotGroups[$basePattern]['latest_date']) {
                        $slotGroups[$basePattern]['latest_date'] = $slotRow['date'];
                      }
                      
                      // Check if slot has companies
                      $slotCompanyIds = !empty($slotRow['company_id']) ? explode(',', $slotRow['company_id']) : [];
                      $slotReferenceIds = !empty($slotRow['reference_company_ids']) ? explode(',', $slotRow['reference_company_ids']) : [];
                      $slotCompanyIds = array_filter($slotCompanyIds);
                      $slotReferenceIds = array_filter($slotReferenceIds);
                      
                      if (!empty($slotCompanyIds) || !empty($slotReferenceIds)) {
                        $slotGroups[$basePattern]['slots_with_companies']++;
                        $slotGroups[$basePattern]['all_company_ids'] = array_merge(
                          $slotGroups[$basePattern]['all_company_ids'],
                          $slotCompanyIds,
                          $slotReferenceIds
                        );
                      }
                    }
                    
                    // Sort groups by earliest date
                    usort($slotGroups, function($a, $b) {
                      return strcmp($a['earliest_date'], $b['earliest_date']);
                    });
                    
                    $groupIndex = 1;
                    foreach ($slotGroups as $group) {
                      $allCompanyIds = array_unique(array_filter($group['all_company_ids']));
                      $totalCompanies = count($allCompanyIds);
                      
                      $dateRange = '';
                      if ($group['earliest_date'] == $group['latest_date']) {
                        $dateRange = date('d M Y', strtotime($group['earliest_date']));
                        if ($group['earliest_date'] == $today) $dateRange .= ' <span class="badge badge-warning">Today</span>';
                      } else {
                        $dateRange = date('d M Y', strtotime($group['earliest_date'])) . ' to ' . date('d M Y', strtotime($group['latest_date']));
                        if ($group['earliest_date'] == $today || $group['latest_date'] == $today) $dateRange .= ' <span class="badge badge-warning">Includes Today</span>';
                      }
                      
                      $patternDisplay = htmlspecialchars($group['pattern']);
                      $patternForJS = htmlspecialchars($group['pattern'], ENT_QUOTES);
                    ?>
                      <tr class="<?php echo $totalCompanies > 0 ? '' : 'table-secondary'; ?>">
                        <td><?php echo $groupIndex++; ?></td>
                        <td><strong><?php echo $patternDisplay; ?></strong></td>
                        <td><?php echo $dateRange; ?></td>
                        <td><span class="badge badge-primary"><?php echo $group['total_slots']; ?></span></td>
                        <td>
                          <?php if ($group['slots_with_companies'] > 0): ?>
                            <span class="badge badge-success"><?php echo $group['slots_with_companies']; ?> / <?php echo $group['total_slots']; ?></span>
                          <?php else: ?>
                            <span class="badge badge-secondary">0 / <?php echo $group['total_slots']; ?></span>
                          <?php endif; ?>
                        </td>
                        <td>
                          <?php if ($totalCompanies > 0): ?>
                            <span class="badge badge-info"><?php echo $totalCompanies; ?> Company(s)</span>
                          <?php else: ?>
                            <span class="badge badge-secondary">No Companies</span>
                          <?php endif; ?>
                        </td>
                        <td>
                          <button type="button" class="btn btn-sm btn-info" onclick="showSlotGroupDetails('<?php echo $patternForJS; ?>')">
                            <i class="fa fa-list"></i> View All Slots
                          </button>
                        </td>
                      </tr>
                    <?php } ?>
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

<!-- Companies List Modal -->
<div class="modal fade" id="companiesListModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white" id="companiesModalTitle">Companies List</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="companiesModalBody">
        <!-- Content will be loaded via AJAX -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<script src="assets/js/jquery.min.js"></script>
<script>
  // Toggle between Slots and Batch view
  function toggleViewType(viewType) {
    if (viewType === 'slots') {
      $('#slotsView').show();
      $('#batchView').hide();
      $('#viewBySlotsBtn').addClass('active');
      $('#viewByBatchBtn').removeClass('active');
    } else if (viewType === 'batch') {
      $('#slotsView').hide();
      $('#batchView').show();
      $('#viewBySlotsBtn').removeClass('active');
      $('#viewByBatchBtn').addClass('active');
    }
  }

  $(document).ready(function() {
    // Handle button clicks for view toggle
    $('#viewBySlotsBtn').on('click', function() {
      toggleViewType('slots');
    });
    
    $('#viewByBatchBtn').on('click', function() {
      toggleViewType('batch');
    });
    
    // Initialize view
    toggleViewType('slots');
  });

  function showSlotCompanies(slotId, slotName) {
    $('#companiesModalTitle').text('Companies for: ' + slotName);
    $('#companiesModalBody').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i> Loading...</div>');
    $('#companiesListModal').modal('show');
    
    $.ajax({
      url: 'ajaxGetSlotCompanies.php',
      type: 'POST',
      data: { slot_id: slotId },
      success: function(response) {
        $('#companiesModalBody').html(response);
      },
      error: function() {
        $('#companiesModalBody').html('<div class="alert alert-danger">Error loading companies.</div>');
      }
    });
  }

  function showSlotGroupDetails(slotPattern) {
    $('#companiesModalTitle').text('Slot Group Details: ' + slotPattern);
    $('#companiesModalBody').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i> Loading...</div>');
    $('#companiesListModal').modal('show');
    
    $.ajax({
      url: 'ajaxGetSlotGroupDetails.php',
      type: 'POST',
      data: { slot_pattern: slotPattern },
      success: function(response) {
        $('#companiesModalBody').html(response);
      },
      error: function() {
        $('#companiesModalBody').html('<div class="alert alert-danger">Error loading slot group details.</div>');
      }
    });
  }
</script>

