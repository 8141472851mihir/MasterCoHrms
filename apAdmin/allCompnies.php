<?php
extract($_REQUEST);
$sId = isset($_GET['sId']) ? $d->sanitizeReportFilterIdAsInt($_GET['sId']) : 0;
$sId = $sId > 0 ? $sId : '';
$ongoing_patch = $d->count_data_direct("festival_id", "festival_master", "is_festival='1' AND ongoing_patch=1");
?>

<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-6">
        <h4 class="page-title">Company</h4>
      </div>
      <div class="col-sm-6 text-right">
        <?php if ($bms_admin_id == 1) { ?>
          <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#myModalScript">Script Command</button>
          <?php if ($ongoing_patch > 0) { ?>
            <button type="button" class="btn btn-sm btn-success" id="copyDomainsToClipboard" <?php echo $sId === '' ? 'disabled' : ''; ?>>Copy Domains</button>
          <?php } ?>
        <?php } ?>
      </div>
    </div>
    <!-- End Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-lg-12">
        <form action="" method="get" accept-charset="utf-8">
          <select type="text" required="" id="sId" onchange="this.form.submit()" class="form-control single-select"
            name="sId">
            <option value="">-- Select --</option>
            <?php
            $qc = $d->select("server_master", "");
            while ($cData = mysqli_fetch_array($qc)) {
            ?>
              <option <?php if (isset($sId) && $cData['server_id'] == $sId) {
                        echo "selected";
                      } ?>
                value="<?php echo $cData['server_id']; ?>"><?php echo $cData['server_name']; ?>
                (<?php echo $cData['server_ip']; ?>)</option>
            <?php } ?>
          </select>
        </form>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <table id="reportTable" class="table table-bordered">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Id</th>
                    <th>Company</th>
                    <th>City</th>
                    <th>Plan</th>
                    <th>Plan Expire</th>
                    <th>Server</th>
                    <th>Domain</th>
                    <th>Command</th>
                    <th>Domain Command</th>
                    <th>Folder Path</th>

                  </tr>
                </thead>
                <tfoot>
                  <tr>
                    <th class="no-search-box"></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                  </tr>
                </tfoot>
                <tbody>
                  <?php
                  $i = 1;

                  if (isset($sId) && $sId > 0) {
                    $appendSeverQuery = " AND domain_master.server_id='$sId'";
                  }


                  $q = $d->selectRow("society_master.*, CASE WHEN plan_expire_date < CURDATE() THEN 'YES' ELSE 'NO' END AS is_expired,server_master.server_name,server_master.server_ip,domain_master.domain_name", "society_master LEFT JOIN domain_master ON society_master.domain_id=domain_master.domain_id LEFT JOIN server_master ON server_master.server_id=domain_master.server_id", "society_master.society_status='0' $appendSeverQuery", "order by society_master.society_id  DESC");
                  while ($data = mysqli_fetch_array($q)) {
                    extract($data);
                  ?>
                    <tr>
                      <td><?php echo $i++; ?></td>
                      <td><span
                          style="display: none;"><?php echo $society_id; ?></span><?php echo '' . $d->short_app_name() . '_' . $society_id; ?>
                      </td>
                      <td><a href="<?php echo $sub_domain; ?>apAdmin/" target="_blank"><?php echo $society_name; ?></a>
                      </td>
                      <td><?php echo $city_name; ?></td>
                      <td><?php echo $is_expired; ?></td>
                      <td><?php
                          if ($default_time_zone != "Asia/Kolkata") {
                            echo $d->change_timezone($plan_expire_date, $default_time_zone, 'Y-m-d');
                          } else {
                            echo $plan_expire_date;
                          } ?>

                      </td>

                      <td><a target="_blank"
                          href="http://<?php echo $server_ip; ?>/phpmyadmin"><?php echo $server_ip; ?></a></td>
                      <td><?php echo $domain_name; ?></td>
                      <td><?php

                          $charge = explode('/', $sub_domain);
                          echo 'chmod -R 777 ' . $charge = $charge[3] . '/img/'; //assuming that the url starts with http:// or https://
                          // $domainTemp = "https://".$charge;
                          ?></td>
                      <td><?php

                          $charge = explode('/', $sub_domain);
                          echo 'chmod -R 777 ' . $charge[2] . '/' . $charge[3] . '/img/'; //assuming that the url starts with http:// or https://
                          // $domainTemp = "https://".$charge;
                          ?></td>
                      <td><?php

                          $charge = explode('/', $sub_domain);
                          echo '"' . $charge[2] . '/' . $charge[3] . '"'; //assuming that the url starts with http:// or https://
                          // $domainTemp = "https://".$charge;
                          ?></td>

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
<?php $pq = $d->select("patch_file_master", "");
$patchData = mysqli_fetch_assoc($pq);
$folder_name = $patchData['folder_name'];
$sql_file_name = $patchData['sql_file_name'];
$patch_file_master_id = $patchData['patch_file_master_id'];

?>

<div class="modal" id="myModalScript">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Script Command</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>


      <!-- Modal body -->
      <div class="modal-body">
        <form id="patchForm" class="p-2">
          <div class="row g-2 align-items-end">
            <div class="col-md-6">
              <label for="folder_name" class="form-label mb-1">Folder Name</label>
              <input type="text" class="form-control form-control-sm" id="folder_name" maxlength="100"
                placeholder="Folder Name" value="<?php echo $folder_name; ?>" autocomplete="off">
            </div>
            <div class="col-md-6">
              <label for="sql_file_name" class="form-label mb-1">SQL File Name</label>
              <input type="text" class="form-control form-control-sm" value="<?php echo $sql_file_name; ?>" id="sql_file_name" maxlength="100"
                placeholder="SQL File Name" autocomplete="off">
            </div>
          </div>
          <div class="mt-3 text-center">
            <button type="submit" class="btn btn-primary btn-sm">Save</button>
          </div>
        </form>
        <p class="text-warning">Sync Custom Languages Before Patch <a href="companyAnalyticsGetData?getDataType=13">Click Here</a></p>
        <h6>For SQL</h6>
        <p>1. vim apply_sql_patch.sh <i class="copy-icon fa fa-copy" data-copy="vim apply_sql_patch.sh"></i></p>
        <p>2. <button class="btn btn-sm btn-danger" id="copySqlScript">Copy SQL Script</button> </p>
        <p>3. chmod +x apply_sql_patch.sh <i class="copy-icon fa fa-copy" data-copy="chmod +x apply_sql_patch.sh"></i>
        </p>
        <p>4. ./apply_sql_patch.sh <i class="copy-icon fa fa-copy" data-copy="./apply_sql_patch.sh"></i></p>
        <p>5. rm apply_sql_patch.sh <i class="copy-icon fa fa-copy" data-copy="rm apply_sql_patch.sh"></i></p>
        <p>6. rm <?php echo $sql_file_name; ?> <i class="copy-icon fa fa-copy"
            data-copy="rm <?php echo $sql_file_name; ?>"></i></p>

        <h6>For Code</h6>
        <p>1. chown -R myco:chpl <?php echo $folder_name; ?>/ <i class="copy-icon fa fa-copy"
            data-copy="chown -R myco:chpl <?php echo $folder_name; ?>/"></i></p>
        <p>2. chmod -R 777 <?php echo $folder_name; ?>/img/ <i class="copy-icon fa fa-copy"
            data-copy="chmod -R 777 <?php echo $folder_name; ?>/img/"></i></p>
        <p>3. vim patch.sh <i class="copy-icon fa fa-copy" data-copy="vim patch.sh"></i></p>
        <p>4. <button class="btn btn-sm btn-warning" id="copyShToClipboard">Copy Patch File</button> </p>
        <p>5. chmod +x patch.sh <i class="copy-icon fa fa-copy" data-copy="chmod +x patch.sh"></i></p>
        <p>6. ./patch.sh <i class="copy-icon fa fa-copy" data-copy="./patch.sh"></i></p>
        <p>7. rm patch.sh <i class="copy-icon fa fa-copy" data-copy="rm patch.sh"></i></p>
        <p>8. rm -rf <?php echo $folder_name; ?> <i class="copy-icon fa fa-copy"
            data-copy="rm -rf <?php echo $folder_name; ?>"></i></p>
      </div>



    </div>
  </div>
</div>
<script>
  const copyDomainsButton = document.getElementById("copyDomainsToClipboard");
  if (copyDomainsButton) {
    copyDomainsButton.addEventListener("click", function() {
      const domains = [...document.querySelectorAll("#reportTable tbody tr")]
        .map(row => row.querySelectorAll("td")[7]?.textContent.trim()
          .replace(/^https?:\/\//i, '')
          .replace(/\/+$/, ''))
        .filter(Boolean);
      const domainPriority = domain => {
        if (domain.endsWith('.my-company.app')) return 0;
        if (domain.endsWith('.my-co.app')) return 1;
        return 2;
      };
      const uniqueDomains = [...new Set(domains)].sort((firstDomain, secondDomain) => {
        const priorityDifference = domainPriority(firstDomain) - domainPriority(secondDomain);
        return priorityDifference || firstDomain.localeCompare(secondDomain);
      });

      if (!uniqueDomains.length) {
        swal("No domains found for the selected server.", { icon: "warning" });
        return;
      }

      navigator.clipboard.writeText(uniqueDomains.join("\n")).then(() => {
        swal("Domains copied to clipboard!", {
          icon: "success",
          timer: 1000
        });
      }).catch(err => {
        alert("Failed to copy domains: " + err);
      });
    });
  }

  document.getElementById("copyShToClipboard").addEventListener("click", function() {
    const folder_name = document.getElementById("folder_name").value.trim();
    const directories = [];

    // Get all table rows in the tbody
    const rows = document.querySelectorAll("#reportTable tbody tr");

    rows.forEach(row => {
      const tds = row.querySelectorAll("td");
      if (tds.length > 0) {
        const folderPath = tds[tds.length - 1].textContent.trim();
        if (folderPath !== '') {
          directories.push(folderPath);
        }
      }
    });

    // Build the full shell script
    const script = `#!/bin/bash

# Define the folder you want to copy or replace
source_folder="${folder_name}"

# Define the directories where you want to copy the folder
directories=(
${directories.map(dir => `${dir}`).join("\n")}
)

# Loop through each target directory
for dir in "\${directories[@]}"; do
    if [ -d "$dir" ]; then  # Check if the directory exists
        echo "Copying subdirectories from $source_folder to $dir"

        # Loop through each subdirectory in the source folder
        for subdir in "$source_folder"/*/; do
            if [ -d "$subdir" ]; then  # Ensure it's a directory
                echo "Copying $subdir to $dir"
                cp -rfp "$subdir" "$dir"
                # Optionally, use rsync for more advanced syncing:
                # rsync -av "$subdir" "$dir"
            fi
        done

        echo "Copying root files from $source_folder to $dir"

        # Copy root-level files
        for file in "$source_folder"/*; do
            if [ -f "$file" ]; then
                echo "Copying file $file to $dir"
                cp -rfp "$file" "$dir/"
            fi
        done
        
    else
        echo "Directory $dir does not exist, skipping."
    fi
done

echo "Subdirectories copied to all directories."
`;

    navigator.clipboard.writeText(script).then(() => {
      swal("Copied to clipboard!", {
        icon: "success",
        timer: 1000
      });
    }).catch(err => {
      alert("Failed to copy script: " + err);
    });
  });

  document.getElementById("copySqlScript").addEventListener("click", function() {
    const sql_file_name = document.getElementById("sql_file_name").value.trim();

    const sqlScript = `#!/bin/bash

# SQL file path
sql_file="${sql_file_name}"

# Database credentials


# Get the list of databases that start with 'Company_'
databases=$(mysql -e "SHOW DATABASES LIKE 'Company_%';" -s --skip-column-names)

# Loop through each database and apply the SQL patch
for db in $databases
do
    echo "Applying SQL patch to $db..."
    
    # Execute SQL patch on the current database
    mysql $db < $sql_file

    # Check if the execution was successful
    if [ $? -eq 0 ]; then
        echo "SQL patch applied successfully to $db."
    else
        echo "Error applying SQL patch to $db."
    fi
done
`;

    navigator.clipboard.writeText(sqlScript).then(() => {
      swal("Copied to clipboard!", {
        icon: "success",
        timer: 1000
      });
    }).catch(err => {
      alert("Failed to copy SQL script: " + err);
    });
  });
  document.addEventListener('click', function(e) {
    if (e.target && e.target.classList.contains('copy-icon')) {
      const text = e.target.getAttribute('data-copy')?.trim();
      if (text) {
        navigator.clipboard.writeText(text).then(() => {
          swal("Copied to clipboard!", {
            icon: "success",
            timer: 1000
          });
        }).catch(err => {
          alert("Copy failed: " + err);
        });
      }
    }
  });
</script>
