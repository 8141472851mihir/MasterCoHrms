<?php
error_reporting(0);

$roleMap = array();
$roleQ = $d->select("role_master", "", "ORDER BY role_name ASC");
if ($roleQ) {
  while ($roleRow = mysqli_fetch_assoc($roleQ)) {
    $roleMap[(int) $roleRow['role_id']] = $roleRow['role_name'];
  }
}

$userMap = array();
$userQ = $d->select("bms_admin_master", "active_status='0'", "ORDER BY admin_name ASC");
if ($userQ) {
  while ($userRow = mysqli_fetch_assoc($userQ)) {
    $userMap[(int) $userRow['admin_id']] = $userRow['admin_name'];
  }
}

$formatAccessLabels = function ($csv, $map) {
  $csv = trim((string) $csv);
  if ($csv === '') {
    return 'All';
  }
  $ids = array_filter(array_map('intval', explode(',', $csv)));
  if (empty($ids)) {
    return 'All';
  }
  $labels = array();
  foreach ($ids as $id) {
    $labels[] = isset($map[$id]) ? $map[$id] : ('#' . $id);
  }
  return implode(', ', $labels);
};
?>
<div class="content-wrapper">
  <div class="container-fluid">
    <div class="row pt-2 pb-2">
      <div class="col-sm-8">
        <h4 class="page-title">Company Actions Access</h4>
      </div>
      <div class="col-sm-4">
        <div class="float-sm-right">
          <button type="button" onclick="openAddModal()" class="btn btn-sm btn-primary waves-effect waves-light m-1">
            <i class="fa fa-plus"></i> Add Action
          </button>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <table id="example" class="table table-bordered">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Manage</th>
                    <th>Action</th>
                    <th>Value</th>
                    <th>Access By Role</th>
                    <th>Access By User</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $i = 1;
                  $q = $d->select("company_action_master", "", "ORDER BY action_id ASC");
                  if ($q && mysqli_num_rows($q) > 0) {
                    while ($data = mysqli_fetch_assoc($q)) {
                      $actionId = (int) $data['action_id'];
                      $actionName = (string) $data['action'];
                      $actionValue = (string) $data['value'];
                      $accessByRole = (string) ($data['access_by_role'] ?? '');
                      $accessByUser = (string) ($data['access_by_user'] ?? '');
                      ?>
                      <tr>
                        <td><?php echo $i++; ?></td>
                        <td>
                          <div class="d-flex flex-wrap">
                            <button
                              type="button"
                              class="btn btn-sm btn-primary waves-effect waves-light m-1"
                              data-action-id="<?php echo $actionId; ?>"
                              data-action="<?php echo htmlspecialchars($actionName, ENT_QUOTES); ?>"
                              data-value="<?php echo htmlspecialchars($actionValue, ENT_QUOTES); ?>"
                              data-roles="<?php echo htmlspecialchars($accessByRole, ENT_QUOTES); ?>"
                              data-users="<?php echo htmlspecialchars($accessByUser, ENT_QUOTES); ?>"
                              onclick="openEditModal(this)">
                              <i class="fa fa-pencil"></i>
                            </button>
                            <form action="controller/companyActionMasterController.php" method="post" class="m-1">
                              <input type="hidden" name="action_id" value="<?php echo $actionId; ?>">
                              <input type="hidden" name="action" value="<?php echo htmlspecialchars($actionName, ENT_QUOTES); ?>">
                              <input type="hidden" name="deleteCompanyAction" value="deleteCompanyAction">
                              <button type="submit" class="form-btn btn btn-sm btn-danger waves-effect waves-light" title="Delete">
                                <i class="fa fa-trash-o"></i>
                              </button>
                            </form>
                          </div>
                        </td>
                        <td><?php echo htmlspecialchars($actionName); ?></td>
                        <td><?php echo htmlspecialchars($actionValue); ?></td>
                        <td><?php echo htmlspecialchars($formatAccessLabels($accessByRole, $roleMap)); ?></td>
                        <td><?php echo htmlspecialchars($formatAccessLabels($accessByUser, $userMap)); ?></td>
                      </tr>
                      <?php
                    }
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

<div class="modal fade" id="companyActionModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white" id="companyActionModalTitle">Add Company Action</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="companyActionForm" action="controller/companyActionMasterController.php" method="post">
          <div class="form-group row">
            <label class="col-sm-3 col-form-label">Action <span class="required">*</span></label>
            <div class="col-sm-9">
              <input type="text" class="form-control" name="action" id="cam_action" required maxlength="255" autocomplete="off">
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-3 col-form-label">Value <span class="required">*</span></label>
            <div class="col-sm-9">
              <input type="text" class="form-control" name="value" id="cam_value" required maxlength="100" autocomplete="off" placeholder="getDataType value">
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-3 col-form-label">Access By Role</label>
            <div class="col-sm-9">
              <select class="form-control multiple-select" name="access_by_role[]" id="cam_access_by_role" multiple="multiple">
                <?php foreach ($roleMap as $roleId => $roleName) { ?>
                  <option value="<?php echo (int) $roleId; ?>"><?php echo htmlspecialchars($roleName); ?></option>
                <?php } ?>
              </select>
              <small class="text-muted">Leave empty for all roles</small>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-3 col-form-label">Access By User</label>
            <div class="col-sm-9">
              <select class="form-control multiple-select" name="access_by_user[]" id="cam_access_by_user" multiple="multiple">
                <?php foreach ($userMap as $userId => $userName) { ?>
                  <option value="<?php echo (int) $userId; ?>"><?php echo htmlspecialchars($userName); ?></option>
                <?php } ?>
              </select>
              <small class="text-muted">Leave empty for all users</small>
            </div>
          </div>
          <div class="form-footer text-center">
            <input type="hidden" name="action_id" id="cam_action_id" value="">
            <input type="hidden" id="camActionType" name="" value="">
            <button type="submit" class="btn btn-primary" id="camFormSubmitBtn">
              <i class="fa fa-check-square-o"></i> Save
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  function parseCsvIds(csv) {
    if (!csv) {
      return [];
    }
    return String(csv).split(',').map(function (v) {
      return v.trim();
    }).filter(function (v) {
      return v !== '';
    });
  }

  function openAddModal() {
    $('#companyActionForm')[0].reset();
    $('#cam_action_id').val('');
    $('#camActionType').attr('name', 'addCompanyAction').val('addCompanyAction');
    $('#companyActionModalTitle').text('Add Company Action');
    $('#camFormSubmitBtn').html('<i class="fa fa-check-square-o"></i> Save');
    $('#cam_access_by_role').val(null).trigger('change');
    $('#cam_access_by_user').val(null).trigger('change');
    $('#companyActionModal').modal('show');
  }

  function openEditModal(btn) {
    var $btn = $(btn);
    $('#cam_action_id').val($btn.attr('data-action-id'));
    $('#cam_action').val($btn.attr('data-action'));
    $('#cam_value').val($btn.attr('data-value'));
    $('#cam_access_by_role').val(parseCsvIds($btn.attr('data-roles'))).trigger('change');
    $('#cam_access_by_user').val(parseCsvIds($btn.attr('data-users'))).trigger('change');
    $('#camActionType').attr('name', 'editCompanyAction').val('editCompanyAction');
    $('#companyActionModalTitle').text('Update Company Action');
    $('#camFormSubmitBtn').html('<i class="fa fa-check-square-o"></i> Update');
    $('#companyActionModal').modal('show');
  }
</script>
