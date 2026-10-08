<?php
extract($_REQUEST);
$currentYear = date('Y');
$countryId = (isset($_GET['countryId']) && $_GET['countryId'] > 0) ? $d->sanitizeReportFilterIdAsInt($_GET['countryId'], 101) : 101;
$filterYear = isset($year) && $year !== '' && $year !== 'all' ? $d->sanitizeReportFilterYear($year, null) : null;

// Build year options from available holidays + current year
$yearsResult = $d->select("holidays_master", "", "ORDER BY holiday_date DESC");
$availableYears = [$currentYear];
while ($row = mysqli_fetch_array($yearsResult)) {
  $y = date('Y', strtotime($row['holiday_date']));
  if (!in_array($y, $availableYears)) {
    $availableYears[] = $y;
  }
}
rsort($availableYears);
?>

<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-2">
        <h4 class="page-title">Manage Holidays</h4>
      </div>
      <div class="col-lg-4">
        <form method="get" action="holidays" id="countryFilterForm">
          <div class="form-group mb-0">
            <select name="countryId" id="holidayCountryFilter" class="form-control single-select" onchange="this.form.submit()">
              <?php
              $countryq = $d->selectRow("country_id,name", "countries", "flag='1'", "ORDER BY name ASC");
              while ($countrydata = mysqli_fetch_array($countryq)) {
                $selected = ($countryId == $countrydata['country_id']) ? 'selected' : '';
                echo '<option value="' . $countrydata['country_id'] . '" ' . $selected . '>' . htmlspecialchars($countrydata['name']) . '</option>';
              }
              ?>
            </select>
            <?php if ($filterYear !== null) { ?>
              <input type="hidden" name="year" value="<?= $filterYear ?>">
            <?php } ?>
          </div>
        </form>
      </div>
      <div class="col-sm-3 col-md-6 col-6">
        <div class="float-sm-right d-flex align-items-center justify-content-end">
          <form method="get" action="holidays" id="yearFilterForm" class="mr-2">
            <label class="mb-0 mr-1">Year:</label>
            <input type="hidden" name="countryId" value="<?= $countryId ?>">
            <select name="year" id="holidayYearFilter" class="form-control form-control-sm d-inline-block" style="width: auto;">
              <option value="all" <?= $filterYear === null ? 'selected' : '' ?>>All Years</option>
              <?php foreach ($availableYears as $y) { ?>
                <option value="<?= $y ?>" <?= $filterYear === (int)$y ? 'selected' : '' ?>><?= $y ?></option>
              <?php } ?>
            </select>
          </form>
          <button onclick="openAddHolidayByYearModal()" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Copy Holidays</button>
          <a href="syncSlabs?sync=Holiday" class="btn mr-1 btn-sm btn-danger waves-effect waves-light"><i class="fa fa-plus mr-1"></i>Sync Holidays</a>
          <button onclick="openAddHolidayModal()" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Add</button>
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
                    <th>Country</th>
                    <th>Holiday Name</th>
                    <th>Holiday Date</th>
                    <th>Holiday Description</th>
                    <th>Holiday Image</th>

                    <th>Status</th>
                    <th>Delete</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $i = 1;
                  $whereClause = "holidays_master.country_id=countries.country_id AND holidays_master.country_id='$countryId'";
                  if ($filterYear !== null) {
                    $whereClause .= " AND YEAR(holiday_date) = '$filterYear'";
                  }
                  $q = $d->selectRow("holidays_master.*, countries.name AS country_name", "holidays_master,countries", $whereClause, "order by holiday_date ASC");
                  while ($data = mysqli_fetch_array($q)) {
                    extract($data);
                    ?>
                    <tr>
                      <td><?php echo $i++; ?></td>
                      <td><?= htmlspecialchars($country_name) ?></td>
                      <td><?= $festival_name ?></td>
                      <td><?= $holiday_date ?></td>
                      <td><?= $holiday_desc ?></td>
                      <td>
                        <a href="../img/master/holiday/<?php echo $data['festival_image']; ?>" data-fancybox="images"
                          data-caption="Photo Name : <?php echo $data['festival_image']; ?>">
                          <img id="blah" onerror="this.src='../img/dummy-image.jpg'"
                            src="../img/master/holiday/<?php echo $data['festival_image']; ?>" width="40" height="40"
                            alt="your image" class="profile" />
                        </a>

                      </td>
                      <td>
                        <?php if ($holiday_status == 1) { ?>
                          <form action="controller/statusController.php" method="post">
                            <input type="hidden" name="holiday_id" value="<?= $holiday_id ?>">
                            <input type="hidden" name="holiday_status" value="0">
                            <input type="hidden" name="festival_name" value="<?= $festival_name ?>">
                            <input type="hidden" name="Status" value="holidayStatus">
                            <button type="submit" class="form-btn btn btn-sm btn-danger waves-effect waves-light m-1"
                              title="Update Status">Deactive</button>
                          </form>
                        <?php } elseif ($holiday_status == 0) { ?>
                          <form action="controller/statusController.php" method="post">
                            <input type="hidden" name="holiday_id" value="<?= $holiday_id ?>">
                            <input type="hidden" name="holiday_status" value="1">
                            <input type="hidden" name="festival_name" value="<?= $festival_name ?>">
                            <input type="hidden" name="Status" value="holidayStatus">
                            <button style="background-color: green;" type="submit"
                              class="form-btn btn btn-sm btn-info waves-effect waves-light m-1"
                              title="Update Status">Active</button>
                          </form>
                        <?php } ?>
                      </td>
                      <td>
                        <div class="row ml-1">
                          <button type="button"
                            onclick="openEditHolidayModal('<?= $holiday_id ?>','<?= htmlspecialchars($festival_name, ENT_QUOTES) ?>','<?= $holiday_date ?>','<?= htmlspecialchars($holiday_desc, ENT_QUOTES) ?>','<?= $festival_image ?>','<?= $country_id ?>')"
                            class="btn btn-sm btn-primary m-1"><i class="fa fa-pencil"></i></button>

                          <form action="controller/holidayController.php" method="post">
                            <input type="hidden" name="holiday_id" value="<?= $holiday_id ?>">
                            <input type="hidden" name="country_id" value="<?= $country_id ?>">
                            <input type="hidden" name="deleteHoliday" id="deleteHoliday" value="deleteHoliday">
                            <button type="submit" class="form-btn btn btn-sm btn-danger waves-effect waves-light m-1"
                              title="Delete"> <i class="fa fa-trash-o"></i> </button>
                          </form>
                        </div>
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


<div class="modal fade" id="holidayModal">
  <div class="modal-dialog">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white" id="modalTitle">Add Holiday</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="holidayForm" action="controller/holidayController.php" method="post" enctype="multipart/form-data">
          <div class="form-group row">
            <label class="col-sm-4 col-form-label">Country <span class="required">*</span></label>
            <div class="col-sm-8">
              <?php
              $countryq = $d->selectRow("country_id,name", "countries", "flag='1'", "ORDER BY name ASC");
              ?>
              <select name="country_id" id="country_id" class="form-control single-select" required>
                <option value="">Select Country</option>
                <?php while ($countrydata = mysqli_fetch_array($countryq)) {
                  echo '<option value="' . $countrydata['country_id'] . '">' . htmlspecialchars($countrydata['name']) . '</option>';
                } ?>
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-4 col-form-label">Holiday Name <span class="required">*</span></label>
            <div class="col-sm-8">
              <input type="text" autocomplete="off" class="form-control" id="festival_name" name="festival_name"
                required>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-4 col-form-label">Holiday Date <span class="required">*</span></label>
            <div class="col-sm-8">
              <input type="text" autocomplete="off" id="holiday_date" class="form-control autoclose-datepicker"
                name="holiday_date" required>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-4 col-form-label">Holiday Description</label>
            <div class="col-sm-8">
              <textarea class="form-control" name="holiday_desc" id="holiday_desc" maxlength="300"></textarea>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-4 col-form-label">Holiday Image</label>
            <div class="col-sm-8">
              <input type="file" class="form-control-file border photoOnly" accept="image/*" name="festival_image"
                id="festival_image">
              <img id="festival_image_preview" src="" width="100" height="100" class="mt-2 d-none" alt="Preview Image">
            </div>
          </div>

          <div class="form-footer text-center">
            <input type="hidden" id="holiday_type_action" name="" value="">
            <input type="hidden" name="holiday_id" id="holiday_id">
            <input type="hidden" name="old_image" id="old_image">
            <button type="submit" id="formSubmitBtn" class="btn btn-primary"><i class="fa fa-check-square-o"></i>
              Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="holidayYearModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white">Copy Holidays</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row align-items-center mb-3">
          <div class="col-md-4">
            <select class="form-control single-select mb-2" id="copy_country_id" name="country_id">
              <?php
              $countryq = $d->selectRow("country_id,name", "countries", "flag='1'", "ORDER BY name ASC");
              while ($countrydata = mysqli_fetch_array($countryq)) {
                $selected = ($countryId == $countrydata['country_id']) ? 'selected' : '';
                echo '<option value="' . $countrydata['country_id'] . '" ' . $selected . '>' . htmlspecialchars($countrydata['name']) . '</option>';
              }
              ?>
            </select>
            <select class="form-control single-select" id="yearFilter" name="yearFilter">
              <?php
              $currentYear = date('Y');
              $selectedYear = $_POST['year'] ?? ($currentYear - 1);
              for ($i = -4; $i < 0; $i++) {
                $year = $currentYear + $i;
                $selected = ($year == $selectedYear) ? 'selected' : '';
                echo "<option value='$year' $selected>$year</option>";
              }
              ?>
            </select>
          </div>
          <div class="col-md-8 text-right d-flex justify-content-end align-items-end">
            <div>
              <label class="invisible">Submit</label>
              <button type="submit" id="insertSelectedHolidaysBtn" class="btn btn-success" form="holidayYearForm">Insert
                Selected Holidays</button>
            </div>
          </div>
        </div>


        <form id="holidayYearForm" action="controller/holidayYearController.php" method="POST"
          enctype="multipart/form-data">
          <input type="hidden" name="copyMultipleHolidays" value="copyMultipleHolidays">
          <input type="hidden" name="country_id" id="copy_holiday_country_id" value="<?= $countryId ?>">
          <div class="table-responsive mb-4">
            <table class="table table-bordered table-hover" id="holidaysTable">
              <thead class="thead-light">
                <tr>
                  <th width="5%"><input type="checkbox" id="selectAll"></th>
                  <th width="20%">Date</th>
                  <th width="30%">Holiday Name</th>
                  <th width="35%">Description</th>
                  <th width="10%">Festival Image</th>
                </tr>
              </thead>
              <tbody id="holidaysTableBody"></tbody>
            </table>

          </div>
        </form>
      </div>
    </div>
  </div>
</div>


<script src="assets/js/jquery.min.js"></script>
<script type="text/javascript"></script>

<script>
  function openAddHolidayModal() {
    $('#holidayForm')[0].reset();
    $('#modalTitle').text('Add Holiday');
    $('#holiday_type_action').attr('name', 'addHolidayData').val('addHolidayData');
    $('#holiday_id').val('');
    $('#country_id').val('<?= $countryId ?>').trigger('change');
    $('#old_image').val('');
    $('#festival_image_preview').attr('src', '').addClass('d-none');
    $('#holidayModal').modal('show');
  }

  function openEditHolidayModal(id, name, date, desc, image, country_id) {
    $('#modalTitle').text('Update Holiday');
    $('#holiday_type_action').attr('name', 'editHolidayData').val('editHolidayData');
    $('#holiday_id').val(id);
    $('#country_id').val(country_id).trigger('change');
    $('#festival_name').val(name);
    $('#holiday_date').val(date);
    $('#holiday_desc').val(desc);
    $('#old_image').val(image);

    if (image) {
      $('#festival_image_preview').attr('src', '../img/master/holiday/' + image).removeClass('d-none');
    } else {
      $('#festival_image_preview').attr('src', '../img/dummy-image.jpg').removeClass('d-none');
    }

    $('#holidayModal').modal('show');
  }

  function filterHolidays() {
    const selectedYear = $('#yearFilter').val();
    const currentYear = new Date().getFullYear();
    $.ajax({
      url: 'getHolidays.php',
      type: 'POST',
      data: { year: selectedYear, country_id: $('#copy_country_id').val() },
      dataType: 'json',
      success: function (holidays) {
        let html = '';
        holidays.forEach((holiday, index) => {
          const backendDate = holiday.holiday_date;
          const [oldYear, month, day] = backendDate.split("-");
          const displayDate = `${currentYear}-${month}-${day}`;
          const imageSrc = holiday.imageSrc || '../img/dummy-image.jpg';

          html += `
  <tr>
    <td>
      <input type="checkbox" name="selected_rows[]" value="${index}" class="row-checkbox" />
      <input type="hidden" name="holiday_id[${index}]" value="${holiday.holiday_id}">
    </td>

    <td>
      <input type="text" class="form-control holiday-date-input" name="holiday_date[${index}]" value="${displayDate}" autocomplete="off" />
    </td>

    <td>
      <input type="text" class="form-control" name="festival_name[${index}]" value="${holiday.festival_name}" autocomplete="off"/>
    </td>

    <td>
      <input type="text" class="form-control" name="holiday_desc[${index}]" value="${holiday.holiday_desc}" autocomplete="off"/>
    </td>

    <td>
    <input type="hidden" name="festival_image[${index}]" value="${holiday.festival_image || ''}" accept="image/*">
      <a href="${holiday.imagePath || '#'}" data-fancybox="images">
        <img onerror="this.src='../img/dummy-image.jpg'" 
             src="${holiday.imageSrc || '../img/dummy-image.jpg'}" 
             width="40" height="40" alt="Image" class="profile" />
      </a>
    </td>
  </tr>`;
        });

        if ($.fn.DataTable.isDataTable('#holidaysTable')) {
          $('#holidaysTable').DataTable().clear().destroy();
        }

        $('#holidaysTableBody').html(html);

        $('#holidaysTableBody').find('input[name^="festival_name"]').each(function () {
          const $festivalInput = $(this);
          $(this).rules("add", {
            required: true,
            minlength: 2,
            maxlength: 40,
            noSpace: true,
            remote: {
              url: "controller/holidayYearController.php",
              type: "POST",
              data: {
                festivalYearNameCheck: 'festivalYearNameCheck',
                csrf: csrf,
                country_id: function () {
                  return $('#copy_country_id').val();
                },
                festival_year_name_check: function () {
                  return $festivalInput.val();
                },
                holiday_year_date_check: function () {
                  return $festivalInput.closest('tr').find('input[name^="holiday_date"]').val();
                }
              },
              dataFilter: function (response) {
                try {
                  var result = JSON.parse(response);
                  return result.valid ? "true" : "false";
                } catch (e) {
                  return "false";
                }
              }
            },
            messages: {
              required: "Please enter the holiday name",
              minlength: "Minimum 2 characters required",
              maxlength: "Maximum 40 characters allowed",
              remote: "This holiday name already exists in this year."
            }
          });
        });

        $('#holidaysTableBody').find('input[name^="holiday_date"]').each(function () {
          const $dateInput = $(this);
          $(this).rules("add", {
            required: true,
            remote: {
              url: "./controller/holidayYearController.php",
              type: "POST",
              data: {
                festivalYearDateCheck: 'festivalYearDateCheck',
                csrf: csrf,
                country_id: function () {
                  return $('#copy_country_id').val();
                },
                holiday_year_date_check: function () {
                  return $dateInput.val();
                },
                festival_year_name_check: function () {
                  return $dateInput.closest('tr').find('input[name^="festival_name"]').val();
                }
              },
              dataFilter: function (response) {
                var result = JSON.parse(response);
                return result.valid ? "true" : "false";
              }
            },
            messages: {
              required: "Please enter a holiday date",
              remote: "This holiday date already exist in this year."
            }
          });
        });

        $('#holidaysTable').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        responsive: true,
        autoWidth: false,
        columnDefs: [
          { targets: 0, orderable: false }
        ],
        drawCallback: function(settings) {
          $('.holiday-date-input').datepicker('destroy').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: 'yyyy-mm-dd',
            orientation: 'bottom auto',
            zIndexOffset: 9999,
            startDate: new Date(currentYear, 0, 1),
            endDate: new Date(currentYear, 11, 31)
          });
        }
      });


        $('.holiday-date-input').datepicker({
          autoclose: true,
          todayHighlight: true,
          format: 'yyyy-mm-dd',
          orientation: 'bottom auto',
          zIndexOffset: 9999,
          startDate: new Date(currentYear, 0, 1),
          endDate: new Date(currentYear, 11, 31)
        });
        $('#yearFilter').val(selectedYear);
        if ($('#yearFilter').hasClass('select2-hidden-accessible')) {
          $('#yearFilter').trigger('change.select2');
        }
      },
      error: function () {
        $('#holidaysTableBody').html('<tr><td colspan="5" class="text-center text-danger">Failed to load holidays</td></tr>');

        if ($.fn.DataTable.isDataTable('#holidaysTable')) {
          $('#holidaysTable').DataTable().clear().destroy();
        }
      }
    });
  }


  $(document).on('change', '#selectAll', function () {
    $('.row-checkbox').prop('checked', this.checked);
  });


  function toggleAllCheckboxes(source) {
    const checkboxes = document.querySelectorAll('input[name="holiday_ids[]"]');
    checkboxes.forEach(cb => cb.checked = source.checked);
  }

  function openAddHolidayByYearModal() {
    $('#holiday_id').val('');
    $('#old_image').val('');
    $('#festival_image_preview').attr('src', '').addClass('d-none');
    $('#holidayYearModal').modal('show');
    setTimeout(filterHolidays, 300);
  }
  $(document).ready(function () {
    const currentYear = new Date().getFullYear();
    $('#holidaysTable').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        responsive: true,
        autoWidth: false,
        columnDefs: [
          { targets: 0, orderable: false }
        ],
        drawCallback: function(settings) {
          $('.holiday-date-input').datepicker('destroy').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: 'yyyy-mm-dd',
            orientation: 'bottom auto',
            zIndexOffset: 9999,
            startDate: new Date(currentYear, 0, 1),
            endDate: new Date(currentYear, 11, 31)
          });
        }
      });

    if ($.fn.validate) {
      $('#holidayYearForm').validate({
        ignore: '#yearFilter',
        rules: {
        },
        errorPlacement: function (error, element) {
        }
      });
    }

    $('#yearFilter').on('change', function (e) {
      e.stopImmediatePropagation();
      filterHolidays();
      return false;
    });
    $('#copy_country_id').on('change', function (e) {
      e.stopImmediatePropagation();
      $('#copy_holiday_country_id').val($(this).val());
      filterHolidays();
      return false;
    });
    if ($.fn.select2) {
      $('#yearFilter').select2();
    }

    if ($('#holidayYearModal').is(':visible')) {
      filterHolidays();
    }

    $('#holidayYearFilter').on('change', function () {
      $('#yearFilterForm').submit();
    });
  });
</script>