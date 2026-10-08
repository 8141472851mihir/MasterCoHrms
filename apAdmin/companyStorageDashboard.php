<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<?php $selected_society_id = isset($_GET['society_id']) ? $d->sanitizeReportFilterIdAsInt($_GET['society_id']) : 0; ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<style>
  .card {
    margin-bottom: 20px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s;
  }

  .storage-summary {
    background-color: #f8f9fa;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 30px;
  }

  .folder-structure {
    max-height: 500px;
    overflow-y: auto;
    border: 1px solid #eee;
    border-radius: 5px;
    padding: 15px;
  }

  .society-card {
    cursor: pointer;
  }

  .active-society {
    border: 2px solid #007bff;
  }

  .folder-icon {
    color: #ffc107;
    margin-right: 5px;
  }

  .file-icon {
    color: #6c757d;
    margin-right: 5px;
  }

  .nav-tabs .nav-link.active {
    font-weight: bold;
  }

  .tree,
  .tree ul {
    list-style: none;
    margin: 0;
    padding: 0;
  }

  .tree ul {
    margin-left: 20px;
    position: relative;
  }

  .tree ul::before {
    content: '';
    position: absolute;
    top: 0;
    left: -10px;
    border-left: 1px solid #ccc;
    height: 100%;
  }

  .tree li {
    margin: 5px 0;
    padding-left: 20px;
    position: relative;
  }

  .tree li::before {
    content: '';
    position: absolute;
    top: 10px;
    left: 0;
    border-top: 1px solid #ccc;
    width: 10px;
    height: 0;
  }

  .tree .folder {
    font-weight: bold;
    cursor: pointer;
    color: #007bff;
  }

  .tree .folder .fa {
    margin-right: 5px;
  }

  .tree .file {
    color: #6c757d;
  }

  .tree li.collapsed>ul {
    display: none;
  }

  #filesTable_wrapper .dataTables_filter input {
    border-radius: 5px;
    border: 1px solid #ced4da;
    padding: 6px 10px;
  }

  #filesTable_wrapper .dataTables_length select {
    border-radius: 5px;
    padding: 6px;
  }

  .dataTables_paginate .paginate_button {
    padding: 5px 10px;
    margin: 2px;
    border-radius: 4px;
    background-color: #f1f1f1;
    border: 1px solid #ddd;
  }

  .dataTables_paginate .paginate_button.current {
    background-color: #007bff;
    color: white !important;
    border-color: #007bff;
  }

  .toggle-icon {
    cursor: pointer;
    color: #007bff;
    margin-right: 5px;
  }

  .nested {
    margin-left: 20px;
  }

  .custom-control-description {
    margin-left: 10px;
  }

  .folder-checkbox-container ul {
    max-height: 1000px;
    overflow-y: auto;
    padding-left: 0;
  }

  .list-group-item {
    border: none;
    margin-bottom: 2px;
    border-radius: 4px;
  }

  .toggle-icon {
    color: #222222;
    font-weight: bolder;
    transition: color 0.2s ease;
  }

  .toggle-folder-btn:hover .toggle-icon {
    color: #000000;
  }
</style>
<div class="content-wrapper">
  <div class="container-fluid">
    <div class="row pt-2 pb-2">
      <div class="col-sm-9 col-5">
        <h4 class="page-title">Storage Dashboard</h4>
      </div>
    </div>
    <form>
      <div class="row mb-3">
        <div class="col-md-6">
          <div class="input-group mb-3">
            <input type="text" id="society-search" class="form-control" placeholder="Search Companies...">
          </div>
        </div>
        <div class="col-md-6">
          <select class="form-control single-select" id="server_search">
            <option value="all">All</option>
            <?php
            $qc = $d->select("server_master", "");
            while ($cData = mysqli_fetch_array($qc)) {
              ?>
              <option value="<?php echo $cData['server_id']; ?>"><?php echo $cData['server_name']; ?>
                (<?php echo $cData['server_ip']; ?>)</option>
            <?php } ?>
          </select>
        </div>
        <div class="col-md-3">
          <select class="form-control single-select" id="size_filter">
            <option value="all">All Sizes</option>
            <option value="0-50">0-50 MB</option>
            <option value="50-100">50-100 MB</option>
            <option value="100-500">100-500 MB</option>
            <option value="500+">500+ MB</option>
          </select>
        </div>
        <div class="col-md-3">
          <select class="form-control single-select" id="file-count-filter">
            <option value="all">All File Counts</option>
            <option value="0-100">0-100 files</option>
            <option value="100-500">100-500 files</option>
            <option value="500-1000">500-1000 files</option>
            <option value="1000+">1000+ files</option>
          </select>
        </div>
        <div class="col-md-3">
          <select class="form-control single-select" id="file_mb">
            <option value="" selected>-- Order Type --</option>
            <option value="file_counts">Order By File Counts</option>
            <option value="size">Order By Size</option>
          </select>
        </div>
        <div class="col-md-2">
          <button class="btn btn-primary btn-block" id="apply-filters">Apply Filters</button>
        </div>
        <!-- <div class="col-md-2">
          <button type="button" class="btn btn-danger btn-block" data-toggle="modal" data-target="#folderModal">
            <i class="fas fa-folder-minus"></i> Manage Folders
          </button>
        </div> -->
      </div>
    </form>
    <div id="" class="society-card-total py-2 mx-0 pb-0 bg-light d-flex">
      <p class="ml-2 mr-5 pb-0 my-0"><strong>Total Size:</strong> <span id="total-size">0 MB</span></p>
      <p class="mx-5 pb-0 my-0"><strong>Total Files:</strong> <span id="total-files">0</span></p>
    </div>
    <div class="row">
      <div class="col-md-4">
        <div class="card">
          <div class="card-header bg-primary text-white">
            <h5 class="mb-0" style="color: white;">Company</h5>
          </div>
          <div class="card-body" style="max-height: 750px; overflow-y: auto;">
            <div class="list-group" id="society-list">
              <?php
              $societyQuery = $d->selectRow("society_master.*,domain_master.server_id", "society_master LEFT JOIN domain_master ON domain_master.domain_id=society_master.domain_id", "");
              $societyRows = [];
              $societyIds = [];
              if (mysqli_num_rows($societyQuery) > 0) {
                while ($society = mysqli_fetch_array($societyQuery)) {
                  $societyRows[] = $society;
                  $societyIds[] = (int)$society['society_id'];
                }
              }

              $storageBySociety = [];
              if (!empty($societyIds)) {
                $societyIdsIn = implode(',', array_map('intval', $societyIds));
                $analyticsQuery = $d->selectRow("society_id,storage_data", "society_analytics_master", "society_id IN ($societyIdsIn)");
                while ($analytics = mysqli_fetch_array($analyticsQuery)) {
                  $storageBySociety[(int)$analytics['society_id']] = $analytics['storage_data'];
                }
              }

              if (!empty($societyRows)) {
                foreach ($societyRows as $society) {
                  $server_id = $society['server_id'];
                  $id = htmlspecialchars($society['society_id']);
                  $name = htmlspecialchars($society['society_name']);
                  $size = "0 MB";
                  $files = "0";
                  if (isset($storageBySociety[(int)$society['society_id']])) {
                    $storageDataJson = $storageBySociety[(int)$society['society_id']];
                    $mainFolder = json_decode($storageDataJson, true);
                    if (is_array($mainFolder)) {
                      $size = isset($mainFolder['size']) ? htmlspecialchars($mainFolder['size']) : "0 MB";
                      $files = isset($mainFolder['file_count']) ? htmlspecialchars($mainFolder['file_count']) : "0";
                    }
                  }
                  $activeClass = ($selected_society_id > 0 && $selected_society_id == $id) ? 'active-society' : '';
                  echo "<div class='list-group-item society-card $activeClass' data-server-id='$server_id'  data-society='$id'> 
                    <div class='d-flex w-100 justify-content-between'>
                      <h6 class='mb-1'>$name</h6>
                      <small>{$size}</small>
                    </div>
                    <small>{$files} files</small>
                  </div>";
                }
              } else {
                echo "<div class='list-group-item'>No societies available</div>";
              }
              ?>
            </div>
            <div id="no-results-message" style="display: none; text-align: center; margin-top: 15px; color: black;">
              No company available
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-8">
        <div class="card">
          <div class="card-header bg-primary text-white">
            <h5 class="mb-0" style="color: white;">Storage Details: <span id="society-name"></span></h5>
          </div>
          <div class="card-body" style="max-height: 750px; overflow-y: auto;" id="company-details">
            <div class="main-folder-summary text-center">
              <div class="row">
                <div class="col-md-4">
                  <h3 id="main-folder-size">0 MB</h3>
                  <p class="text-muted">Size</p>
                </div>
                <div class="col-md-4">
                  <h3 id="main-folder-files">0</h3>
                  <p class="text-muted">Files</p>
                </div>
                <div class="col-md-4">
                  <h3 id="main-folder-subfolders">0</h3>
                  <p class="text-muted">Subfolders</p>
                </div>
              </div>
            </div>
            <div id="server-summary-table-section" style="display:none;">
              <div class="table-responsive">
                <table class="table table-bordered" id="serverSummaryTable">
                  <thead>
                    <tr>
                      <th>Server Name</th>
                      <th>Total Size</th>
                      <th>Total Files</th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
              </div>
            </div>
            <div id="company-details-section">

              <h5 class="mt-4 mb-3">Folder Structure</h5>
              <div class="folder-structure">
                <ul id="folder-structure-list" class="folder-tree"></ul>
              </div>

              <ul class="nav nav-tabs mb-3" id="chartTabs" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" id="size-tab" data-toggle="tab" href="#size-chart" role="tab">Size
                    Distribution</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="files-tab" data-toggle="tab" href="#files-chart" role="tab">File Count</a>
                </li>
              </ul>
              <div id="chart-section">
                <div id="select-company-message"
                  style="text-align:center; padding: 50px; font-size: 18px; color: #888;">
                  Please select any company to view storage details.
                </div>
                <div class="tab-content" id="chartTabsContent">
                  <div class="tab-pane fade show active" id="size-chart" role="tabpanel">
                    <canvas id="sizeChart" height="300" style="display:none;"></canvas>
                  </div>
                  <div class="tab-pane fade" id="files-chart" role="tabpanel">
                    <canvas id="filesChart" height="300" style="display:none;"></canvas>
                  </div>
                </div>
              </div>

              <h5 class="mt-4 mb-3">Folder List</h5>
              <div id="folder-path" class="mb-3 text-black fw-semibold"></div>
              <div class="table-responsive">
                <table class="table table-bordered" id="filesTable">
                  <thead>
                    <tr>
                      <th>File Name</th>
                      <th>Size (MB)</th>
                      <th>Type</th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
<script>
  let sizeChartInstance = null;
  let filesChartInstance = null;
  let currentSocietyId = null;
  let currentFolderPath = [];
  let chartHistory = [];
  let currentVisibleFolderData = [];
  let selectedFolderPath = [];

  function loadStorageCharts(societyId, folderPath = []) {
    document.getElementById('server-summary-table-section').style.display = 'none';
    document.getElementById('company-details-section').style.display = 'block';

    restoreMainFolderSummaryHtml();
    currentSocietyId = societyId;
    currentFolderPath = folderPath;
    chartHistory = [];

    document.getElementById('select-company-message').style.display = 'none';
    document.getElementById('sizeChart').style.display = 'block';
    document.getElementById('filesChart').style.display = 'block';

    fetch(`ajaxStorageDashboard.php?society_id=${societyId}&folder_path=${JSON.stringify(folderPath)}`)
      .then(res => res.json())
      .then(data => {
        const mainFolder = data.main_folder || {};
        const files = data.files || [];

        document.getElementById("main-folder-size").textContent = mainFolder.size || "0 MB";
        document.getElementById("main-folder-files").textContent = mainFolder.file_count || "0";
        document.getElementById("main-folder-subfolders").textContent = mainFolder.subfolder_count || "0";

        updateChartsAndTable(files);
        renderFolderStructure(files);
      });
  }

  function updateChartsAndTable(files) {
    const sortedFiles = [...files].sort((a, b) => {
      const sizeA = parseFloat(a.size) || 0;
      const sizeB = parseFloat(b.size) || 0;
      return sizeB - sizeA;
    });

    currentVisibleFolderData = sortedFiles;

    const labels = sortedFiles.map(item => item.name);
    const sizes = sortedFiles.map(item => parseFloat(item.size));
    const counts = sortedFiles.map(item => item.file_count);

    if (sizeChartInstance) sizeChartInstance.destroy();
    if (filesChartInstance) filesChartInstance.destroy();

    sizeChartInstance = new Chart(document.getElementById('sizeChart'), {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Size (MB)',
          data: sizes,
          backgroundColor: 'rgba(75, 192, 192, 0.6)'
        }]
      }
    });

    filesChartInstance = new Chart(document.getElementById('filesChart'), {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'File Count',
          data: counts,
          backgroundColor: 'rgba(255, 159, 64, 0.6)'
        }]
      }
    });

    if ($.fn.DataTable.isDataTable('#filesTable')) {
      $('#filesTable').DataTable().destroy();
      $('#filesTable tbody').empty();
    }

    $('#filesTable').DataTable({
      data: sortedFiles,
      columns: [{
        title: "Name",
        data: 'name'
      },
      {
        title: "Size (MB)",
        data: 'size'
      },
      {
        title: "File Count",
        data: 'file_count'
      }
      ],
      order: [
        [1, 'desc']
      ]
    });

  }


  function getCurrentChartData() {
    return currentVisibleFolderData;
  }

  function updateFolderPathDisplay() {
    const pathContainer = document.getElementById("folder-path");
    if (selectedFolderPath.length === 0) {
      pathContainer.textContent = 'img';
    } else {
      pathContainer.innerHTML = ' ' + selectedFolderPath.join(' / ');
    }
  }

  function renderFolderStructure(files, parentElement = null, isRoot = true, currentPath = []) {
    const container = parentElement || document.getElementById('folder-structure-list');
    if (!parentElement) container.innerHTML = '';

    const sortBySize = (a, b) => {
      const sizeA = a.size || 0;
      const sizeB = b.size || 0;
      return sizeB - sizeA;
    };

    if (isRoot) {
      const rootLi = document.createElement('li');
      const rootSpan = document.createElement('span');
      rootSpan.classList.add('folder');
      rootSpan.innerHTML = `<i class="fa fa-minus toggle-icon"></i> <i class="fa fa-folder folder-icon"></i> img`;

      const rootUl = document.createElement('ul');
      rootUl.classList.add('nested');
      rootUl.style.display = 'block';

      rootSpan.style.cursor = 'pointer';
      rootSpan.addEventListener('click', () => {
        const icon = rootSpan.querySelector('.toggle-icon');
        const isVisible = rootUl.style.display === 'block';

        if (isVisible) {
          rootUl.style.display = 'none';
          icon.classList.remove('fa-minus');
          icon.classList.add('fa-plus');
        } else {
          rootUl.style.display = 'block';
          icon.classList.remove('fa-plus');
          icon.classList.add('fa-minus');
          selectedFolderPath = [];
          chartHistory = [];
          updateFolderPathDisplay();
          updateChartsAndTable(files);
        }
      });

      rootLi.appendChild(rootSpan);
      rootLi.appendChild(rootUl);
      container.appendChild(rootLi);

      const sortedFiles = [...files].sort(sortBySize);
      renderFolderStructure(sortedFiles, rootUl, false, ['img']);
      return;
    }

    const sortedItems = [...files].sort((a, b) => {
      if (a.type === 'folder' && b.type === 'folder') {
        return sortBySize(a, b);
      } else if (a.type === 'folder') {
        return -1;
      } else if (b.type === 'folder') {
        return 1;
      } else {
        return 0;
      }
    });

    sortedItems.forEach(item => {
      const li = document.createElement('li');

      if (item.type === 'folder') {
        const span = document.createElement('span');
        span.classList.add('folder');
        const hasSubfolders = item.subfolders && item.subfolders.some(sub => sub.type === 'folder');

        span.innerHTML = `
        ${hasSubfolders ? '<i class="fa fa-plus toggle-icon"></i>' : '<i style="margin-left: 14px;"></i>'}
        <i class="fa fa-folder folder-icon"></i> ${item.name} (${item.size} MB, ${item.file_count} files)
      `;

        const subUl = document.createElement('ul');
        subUl.classList.add('nested');
        subUl.style.display = 'none';

        const fullPath = [...currentPath, item.name];

        span.style.cursor = 'pointer';
        span.addEventListener('click', () => {
          const icon = span.querySelector('.toggle-icon');
          const isVisible = subUl.style.display === 'block';

          const parentUl = span.parentElement.parentElement;
          Array.from(parentUl.children).forEach(siblingLi => {
            const siblingSpan = siblingLi.querySelector('span.folder');
            const siblingUl = siblingLi.querySelector('ul.nested');
            const siblingIcon = siblingLi.querySelector('.toggle-icon');

            if (siblingSpan !== span && siblingUl && siblingUl.style.display === 'block') {
              collapseAllSubfolders(siblingUl);
              siblingUl.style.display = 'none';
              if (siblingIcon) {
                siblingIcon.classList.remove('fa-minus');
                siblingIcon.classList.add('fa-plus');
              }
            }
          });

          if (isVisible) {
            collapseAllSubfolders(subUl);
            subUl.style.display = 'none';
            if (icon) {
              icon.classList.remove('fa-minus');
              icon.classList.add('fa-plus');
            }

            selectedFolderPath = currentPath;
            updateFolderPathDisplay();
            const parentSubfolders = files.filter(sub => sub.type === 'folder');
            chartHistory.pop();
            updateChartsAndTable(parentSubfolders);
          } else {
            subUl.style.display = 'block';
            if (icon) {
              icon.classList.remove('fa-plus');
              icon.classList.add('fa-minus');
            }

            selectedFolderPath = [...fullPath];
            updateFolderPathDisplay();

            const directSubfolders = (item.subfolders || []).filter(sub => sub.type === 'folder');
            chartHistory.push(getCurrentChartData());
            updateChartsAndTable(directSubfolders);
          }
        });

        li.appendChild(span);

        if (item.subfolders && item.subfolders.length > 0) {
          const sortedSubfolders = [...item.subfolders].sort(sortBySize);
          renderFolderStructure(sortedSubfolders, subUl, false, fullPath);

          if (isPathMatch(selectedFolderPath, fullPath)) {
            subUl.style.display = 'block';
            const icon = span.querySelector('.toggle-icon');
            if (icon) {
              icon.classList.remove('fa-plus');
              icon.classList.add('fa-minus');
            }
          }
          li.appendChild(subUl);
        }

      } else if (item.type === 'file') {
        li.innerHTML = `<span class="file"><i class="fa fa-file file-icon"></i> ${item.name} (${item.size} MB)</span>`;
      }

      container.appendChild(li);
    });

    function collapseAllSubfolders(ulElement) {
      const subUls = ulElement.querySelectorAll('ul.nested');
      subUls.forEach(sub => {
        sub.style.display = 'none';
        const icon = sub.previousElementSibling?.querySelector('.toggle-icon');
        if (icon) {
          icon.classList.remove('fa-minus');
          icon.classList.add('fa-plus');
        }
      });
    }

    function isPathMatch(path1, path2) {
      return path2.every((val, index) => path1[index] === val);
    }
  }

  document.querySelectorAll('.society-card').forEach(item => {
    item.addEventListener('click', function () {
      const societyId = this.dataset.society;
      loadStorageCharts(societyId, []);
      document.querySelectorAll('.society-card').forEach(el => el.classList.remove('active-society'));
      this.classList.add('active-society');
      document.getElementById('society-name').innerText = this.querySelector('h6').innerText;
    });
  });

  document.getElementById('society-search').addEventListener('input', function () {
    document.getElementById('apply-filters').click();
  });
  const allSocietyCards = Array.from(document.querySelectorAll('.society-card'));
  document.getElementById('apply-filters').addEventListener('click', function (e) {
    e.preventDefault();

    const sizeFilter = document.getElementById('size_filter').value;
    const fileCountFilter = document.getElementById('file-count-filter').value;
    const searchQuery = document.getElementById('society-search').value.toLowerCase();
    const chartType = document.getElementById('file_mb').value;
    const sortBy = (chartType === 'file_counts') ? 'file_counts' : (chartType === 'size' ? 'size' : '');
    const selectedServer = document.getElementById('server_search').value;
    const societyCards = allSocietyCards;
    // const societyCards = Array.from(document.querySelectorAll('.society-card'));
    const listContainer = document.getElementById('society-list');
    const noResultsMessage = document.getElementById('no-results-message');

    const filteredCards = societyCards.filter(card => {
      const name = card.querySelector('h6').textContent.toLowerCase();
      const sizeText = card.querySelector('small').textContent;
      const filesText = card.querySelectorAll('small')[1].textContent;
      const cardServerId = card.dataset.serverId;

      function convertToMB(sizeStr) {
        sizeStr = sizeStr.trim().toUpperCase();
        if (sizeStr.endsWith('GB')) {
          return parseFloat(sizeStr) * 1000;
        } else if (sizeStr.endsWith('MB')) {
          return parseFloat(sizeStr);
        } else if (sizeStr.endsWith('KB')) {
          return parseFloat(sizeStr) / 1000;
        } else if (sizeStr.endsWith('B')) {
          return parseFloat(sizeStr) / 1000 / 1000;
        } else if (sizeStr === '0' || sizeStr === '0 MB') {
          return 0;
        } else {
          return parseFloat(sizeStr) || 0;
        }
      }
      const sizeMB = convertToMB(sizeText);
      const fileCount = parseInt(filesText.replace("files", "").trim()) || 0;
      card.style.display = 'block';
      let show = true;
      if (selectedServer !== 'all' && selectedServer !== cardServerId) show = false;
      if (searchQuery && !name.includes(searchQuery)) show = false;
      if (sizeFilter) {
        if (sizeFilter === "500+" && sizeMB < 500) show = false;
        else if (sizeFilter !== "500+") {
          const [min, max] = sizeFilter.split('-');
          if (sizeMB < parseFloat(min) || sizeMB > parseFloat(max)) show = false;
        }
      }

      if (fileCountFilter) {
        if (fileCountFilter === "1000+" && fileCount < 1000) show = false;
        else if (fileCountFilter !== "1000+") {
          const [min, max] = fileCountFilter.split('-');
          if (fileCount < parseInt(min) || fileCount > parseInt(max)) show = false;
        }
      }

      card.dataset.sizeMb = sizeMB;
      card.dataset.fileCount = fileCount;

      card.style.display = show ? 'block' : 'none';
      return show;
    });
    console.log(filteredCards.length);
    if (filteredCards.length === 0) {
      noResultsMessage.style.display = 'block';
    } else {
      noResultsMessage.style.display = 'none';
    }
    console.log(filteredCards.length);
    if (sortBy && filteredCards.length > 0) {
      filteredCards.sort((a, b) => {
        if (sortBy === 'size') {
          return parseFloat(b.dataset.sizeMb) - parseFloat(a.dataset.sizeMb);
        } else if (sortBy === 'file_counts') {
          return parseInt(b.dataset.fileCount) - parseInt(a.dataset.fileCount);
        }
        return 0;
      });

      listContainer.innerHTML = '';
      filteredCards.forEach(card => listContainer.appendChild(card));
    }
    let totalSizeMB = 0;
    let totalFiles = 0;

    filteredCards.forEach(card => {
      totalSizeMB += parseFloat(card.dataset.sizeMb || 0);
      totalFiles += parseInt(card.dataset.fileCount || 0);
    });

    // Format total size
    let formattedSize = totalSizeMB.toFixed(2) + ' MB';
    if (totalSizeMB > 1000) {
      formattedSize = (totalSizeMB / 1000).toFixed(2) + ' GB';
    }

    // Update totals in DOM
    document.getElementById('total-size').textContent = formattedSize;
    document.getElementById('total-files').textContent = totalFiles;
    if (chartType === 'file_counts') {
      document.querySelector('#files-tab').click();
    } else if (chartType === 'size') {
      document.querySelector('#size-tab').click();
    }
  });

  document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('apply-filters').click();
    loadServerSummaryOnPageLoad();
  });

  function loadServerSummaryOnPageLoad() {
    fetch('ajaxStorageDashboard.php?allServersSummary=1')
      .then(res => res.json())
      .then(data => {
        let html = '';
        document.querySelector('.main-folder-summary').innerHTML = html;
        document.getElementById('society-name').textContent = 'All Servers';
        document.getElementById('company-details-section').style.display = 'none';
        const tableSection = document.getElementById('server-summary-table-section');
        const tableBody = document.querySelector('#serverSummaryTable tbody');
        tableBody.innerHTML = '';
        data.forEach(server => {
          tableBody.innerHTML += `
          <tr>
            <td>${server.server_name}</td>
            <td>${server.total_size}</td>
            <td>${server.total_files}</td>
          </tr>
        `;
        });
        tableSection.style.display = 'block';
        if ($.fn.DataTable.isDataTable('#serverSummaryTable')) {
          $('#serverSummaryTable').DataTable().destroy();
        }
        $('#serverSummaryTable').DataTable();
      });
  }
  function restoreMainFolderSummaryHtml() {
    document.querySelector('.main-folder-summary').innerHTML = `
    <div class="row">
      <div class="col-md-4">
        <h3 id="main-folder-size">0 MB</h3>
        <p class="text-muted">Size</p>
      </div>
      <div class="col-md-4">
        <h3 id="main-folder-files">0</h3>
        <p class="text-muted">Files</p>
      </div>
      <div class="col-md-4">
        <h3 id="main-folder-subfolders">0</h3>
        <p class="text-muted">Subfolders</p>
      </div>
    </div>
  `;
  }
  loadServerSummaryOnPageLoad();
</script>