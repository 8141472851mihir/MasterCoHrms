<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-3">
        <h4 class="page-title">Company </h4>
      </div>
      <div class="col-sm-9">
        <div class="float-sm-right">
          <?php
          $ongoing_patch = $d->count_data_direct("festival_id", "festival_master", "is_festival='1' AND ongoing_patch=1");
          $show_note = ($ongoing_patch > 0) ? "" : "d-none";
          ?>
          <div class="col-sm-12 text-danger float-right <?php echo $show_note; ?>">
            <p>Note: Ongoing patch, Please Wait. </p>
          </div>

        </div>
      </div>
    </div>
    <!-- End Breadcrumb-->

    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <table id="example" class="table table-bordered">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Company Id</th>
                    <th>Company</th>
                    <th>City</th>
                    <th>Service Providers</th>
                    <th>Banners</th>
                    <th>App Menu</th>
                    <th>Server</th>
                    <th>Company Server</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $i = 1;
                  $shortAppName = $d->short_app_name();
                  $q = $d->select("society_master", "created_on_society_server=0 $countryAppendQuerySocietySingle", "order by society_id  DESC");
                  $pendingRows = [];
                  $societyIds = [];
                  while ($data = mysqli_fetch_array($q)) {
                    $pendingRows[] = $data;
                    $societyIds[] = (int)$data['society_id'];
                  }

                  // Prefetch domains+servers once; match subdomain in PHP (avoids LIKE per row)
                  $allDomains = [];
                  $qdomainAll = $d->selectRow(
                    "domain_master.domain_id,server_master.server_name,server_master.server_ip,domain_master.domain_name",
                    "domain_master LEFT JOIN server_master ON server_master.server_id=domain_master.server_id",
                    "1=1"
                  );
                  while ($dom = mysqli_fetch_array($qdomainAll)) {
                    $allDomains[] = $dom;
                  }

                  // Prefetch app menu counts for all pending societies
                  $menuCountBySociety = [];
                  if (!empty($societyIds)) {
                    $societyIdsIn = implode(',', array_map('intval', $societyIds));
                    $menuCountQ = $d->selectRow(
                      "resident_app_menu_society.society_id, COUNT(*) AS menu_count",
                      "resident_app_menu,resident_app_menu_society",
                      "resident_app_menu.menu_status=0
                        AND resident_app_menu_society.app_menu_id=resident_app_menu.app_menu_id
                        AND resident_app_menu_society.society_id IN ($societyIdsIn)
                        AND resident_app_menu_society.menu_status=0",
                      "GROUP BY resident_app_menu_society.society_id"
                    );
                    while ($mc = mysqli_fetch_assoc($menuCountQ)) {
                      $menuCountBySociety[(int)$mc['society_id']] = (int)$mc['menu_count'];
                    }
                  }

                  foreach ($pendingRows as $data) {
                    extract($data);
                    $charge = explode('/', $data['sub_domain']);
                    $charge = $charge[2] ?? '';
                    $domainTemp = "https://" . $charge;

                    $domainData = ['domain_id' => null, 'server_name' => '', 'server_ip' => '', 'domain_name' => ''];
                    if ($domainTemp !== 'https://') {
                      foreach ($allDomains as $dom) {
                        if (strpos((string)$dom['domain_name'], $domainTemp) !== false) {
                          $domainData = $dom;
                          break;
                        }
                      }
                    }
                    $domain_id = $domainData['domain_id'];
                    $menuCount = $menuCountBySociety[(int)$society_id] ?? 0;
                  ?>
                    <tr>
                      <td><?php echo $i++; ?></td>
                      <td><span style="display: none;"><?php echo $society_id; ?></span><?php echo '' . $shortAppName . '_' . $society_id; ?></td>
                      <td><a href="javascript:void" data-toggle="modal" data-target="#buildingModal" onclick="getAllBuildingData(<?php echo $society_id; ?>)"><?php echo $society_name; ?></a></td>
                      <!-- <td><?php echo $society_name; ?></td> -->
                      <td><?php echo $city_name; ?></td>
                      <td class="tableWidth">
                        <form action="societyServiceProviders" method="GET" class="d-inline-block float-right">
                          <input type="hidden" name="id" value="<?php echo $society_id ?>">
                          <button class="btn btn-primary btn-sm"><i class="fa fa-eye" data-toggle="tooltip" title="Company Service Providers"></i></button>
                        </form>
                      </td>
                      <td>
                        <form action="companyBanners" method="GET" class="d-inline-block float-right">
                          <input type="hidden" name="id" value="<?php echo $society_id ?>">
                          <button class="btn btn-primary btn-sm" data-toggle="tooltip" title="Company Banners"><i class="fa fa-eye"></i></button>
                        </form>
                      </td>
                      <td><?php echo $menuCount; ?>

                        <a href="companyAppMenu?sId=<?php echo $society_id; ?>" class="btn btn-primary btn-sm" title="Company App Menu"><i class="fa fa-eye"></i></button>
                      </td>
                      <td>
                        <?php echo $domainData['server_name'] . '-' . $domainData['server_ip'];
                        ?>
                      </td>
                      <td>
                        <?php if ($created_on_society_server == 1) {
                          echo "Created";
                        } else if (($role_id == 1 || $role_id == 6  || $role_id == 22) && $ongoing_patch == 0) { ?>
                          <form action="controller/createSocietyAutoController.php" method="post" class="d-inline-block float-right">
                            <input type="hidden" name="society_id" value="<?php echo $society_id ?>">
                            <input type="hidden" name="createSoceitySubdomain" value="<?php echo "createSoceitySubdomain" ?>">
                            <button class="btn btn-danger form-btn btn-form btn-sm" data-toggle="tooltip" title="Create Company"> Create</button>
                          </form>
                        <?php } ?>
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
<script src="assets/js/jquery.min.js"></script>
<script type="text/javascript">
  function getAllBuildingData(request_society_id) {
    $.ajax({
      url: "getBuildingDetails.php",
      cache: false,
      type: "POST",
      data: {
        request_society_id: request_society_id,
        csrf: csrf,
        is_pending: true
      },
      success: function(response) {
        $('#buildingData').html(response);
      }
    });
  }

  // Auto-submit forms when ?auto=true is in URL
  $(document).ready(function() {
    // Check if auto=true parameter exists in URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('auto') === 'true') {
      // Find all forms that submit to createSocietyAutoController.php
      const createForms = $('form[action="controller/createSocietyAutoController.php"]');

      if (createForms.length > 0) {
        // Collect forms with their society_id for sorting (oldest first)
        const formsData = [];
        createForms.each(function() {
          const societyId = $(this).find('input[name="society_id"]').val();
          formsData.push({
            form: $(this),
            societyId: parseInt(societyId)
          });
        });

        // Sort by society_id ascending (oldest first)
        formsData.sort(function(a, b) {
          return a.societyId - b.societyId;
        });

        // Submit the first form (oldest one) and reload after each submission
        function submitFirstForm() {
          if (formsData.length === 0) {
            console.log('All forms submitted successfully');
            // Refresh the page after all submissions are complete
            // Remove ?auto=true parameter to prevent auto-submission on refresh
            setTimeout(function() {
              const url = window.location.pathname;
              window.location.href = url;
            }, 2000); // Wait 2 seconds before refresh
            return;
          }

          // Always submit the first form (oldest one) after sorting
          const formData = formsData[0];
          const originalForm = formData.form[0]; // Get DOM element
          const societyId = formData.societyId;

          console.log('Submitting form for society_id: ' + societyId + ' (oldest remaining: ' + formsData.length + ' forms left)');

          // Create a unique iframe name for this submission
          const iframeName = 'hiddenFormSubmit_' + Date.now();

          // Create a hidden iframe for this form submission
          const hiddenIframe = $('<iframe>', {
            name: iframeName,
            id: iframeName,
            style: 'display: none; width: 0; height: 0;'
          });
          $('body').append(hiddenIframe);

          // Clone the form to avoid issues with multiple submissions
          const clonedForm = originalForm.cloneNode(true);
          clonedForm.setAttribute('target', iframeName);
          clonedForm.style.display = 'none';
          $('body').append(clonedForm);

          // Listen for iframe load event to know when form submission is complete
          const iframe = document.getElementById(iframeName);
          let formSubmitted = false;

          const onIframeLoad = function() {
            if (!formSubmitted) {
              formSubmitted = true;
              console.log('Successfully submitted society_id: ' + societyId);
              // Clean up
              $(clonedForm).remove();
              $(hiddenIframe).remove();

              // Reload the page after successful submission to continue with next form
              // Keep ?auto=true to continue auto-submission
              setTimeout(function() {
                window.location.href = window.location.pathname + '?auto=true';
              }, 120000); // Wait 120000 seconds before reload
            }
          };

          // Also use timeout as fallback in case iframe load doesn't fire
          const timeoutId = setTimeout(function() {
            if (!formSubmitted) {
              formSubmitted = true;
              console.log('Form submission timeout for society_id: ' + societyId + ', reloading...');
              $(clonedForm).remove();
              $(hiddenIframe).remove();
              // Reload the page even on timeout to continue
              setTimeout(function() {
                window.location.href = window.location.pathname + '?auto=true';
              }, 120000); // Wait 120000 seconds before reload
            }
          }, 120000); // 120000 second timeout

          iframe.addEventListener('load', function() {
            clearTimeout(timeoutId);
            onIframeLoad();
          });

          // Submit the cloned form (hard form submission, not AJAX)
          clonedForm.submit();
        }

        // Start submitting the first form after a short delay
        setTimeout(submitFirstForm, 500);
      }
    }
  });
</script>


<div class="modal fade" id="buildingModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white">Details</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="buildingData">

      </div>

    </div>
  </div>
</div>