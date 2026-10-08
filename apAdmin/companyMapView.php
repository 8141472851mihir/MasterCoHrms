
<div class="content-wrapper">
  <div class="container-fluid">
    <div class="row pt-2 pb-2">
      <div class="col-sm-4">
        <h4 class="page-title">Company Locations Map</h4>
      </div>
      <div class="col-sm-8 text-right">
        <button type="button" class="btn btn-primary" onclick="refreshMap()">
          <i class="fa fa-refresh"></i> Refresh Map
        </button>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div class="row mb-3">
              <div class="col-md-4">
                <div class="form-group">
                  <label for="searchCompany">Search Company:</label>
                  <input type="text" class="form-control" id="searchCompany" placeholder="Enter company name to search...">
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group">
                  <label for="filterCountry">Filter by Country:</label>
                  <select class="form-control" id="filterCountry">
                    <option value="">All Countries</option>
                    <?php
                    $qc = $d->select("countries", "flag=1");
                    while ($cData = mysqli_fetch_array($qc)) {
                      echo "<option value='" . $cData['country_id'] . "'>" . $cData['name'] . "</option>";
                    }
                    ?>
                  </select>
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group">
                  <label for="filterStatus">Filter by Status:</label>
                  <select class="form-control" id="filterStatus">
                    <option value="">All Status</option>
                    <option value="0">Active</option>
                    <option value="1">Inactive</option>
                  </select>
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group">
                  <label for="clusterToggle">Map View:</label>
                  <select class="form-control" id="clusterToggle">
                    <option value="clustered">Clustered View</option>
                    <option value="individual">Individual Markers</option>
                  </select>
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group">
                  <label for="zoomLevel">Zoom Level:</label>
                  <select class="form-control" id="zoomLevel">
                    <option value="auto">Auto</option>
                    <option value="5">Country Level</option>
                    <option value="8">State Level</option>
                    <option value="12">City Level</option>
                    <option value="15">Street Level</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-8">
                <div id="map" style="width: 100%; height: 600px; border: 1px solid #ddd;"></div>
              </div>
              <div class="col-md-4">
                <div class="card">
                  <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex">
                      <input type="text" class="form-control form-control-sm mr-2" id="listSearch" placeholder="Search in list..." style="width: 150px;">
                      <select class="form-control form-control-sm" id="listSort" style="width: 120px;">
                        <option value="name">Sort by Name</option>
                        <option value="status">Sort by Status</option>
                        <option value="location">Sort by Location</option>
                        <option value="country">Sort by Country</option>
                      </select>
                    </div>
                  </div>
                  <div class="card-body" style="max-height: 550px; overflow-y: auto;">
                    <div class="mb-2">
                      <small class="text-muted">
                        <span id="listCount">0</span> companies shown
                        <span id="listWithLocation" class="text-success"></span>
                      </small>
                    </div>
                    <div id="companyList">
                      <!-- Company list will be populated here -->
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="row mt-3">
              <div class="col-12">
                <div class="company-stats">
                  <div class="row">
                    <div class="col-md-3">
                      <div class="stat-item">
                        <div class="stat-number" id="totalCompanies">0</div>
                        <div class="stat-label">Total Companies</div>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="stat-item">
                        <div class="stat-number" id="activeCompanies">0</div>
                        <div class="stat-label">Active Companies</div>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="stat-item">
                        <div class="stat-number" id="companiesWithLocation">0</div>
                        <div class="stat-label">With Location</div>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="stat-item">
                        <div class="stat-number" id="companiesWithoutLocation">0</div>
                        <div class="stat-label">Without Location</div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.company-item {
    transition: all 0.3s ease;
    border-left: 3px solid transparent;
}

.company-item:hover {
    border-left-color: #4285F4;
    background-color: #f8f9fa;
}

.company-item.active {
    border-left-color: #28a745;
    background-color: #d4edda;
}

.cluster-marker {
    background-color: #4285F4;
    border-radius: 50%;
    color: white;
    text-align: center;
    font-weight: bold;
    border: 2px solid white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

.form-control-sm {
    height: 32px;
    font-size: 12px;
}

#listSearch, #listSort {
    border: 1px solid #ddd;
    border-radius: 4px;
}

#listSearch:focus, #listSort:focus {
    border-color: #4285F4;
    box-shadow: 0 0 0 0.2rem rgba(66, 133, 244, 0.25);
}

.map-controls {
    background: white;
    border-radius: 4px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    padding: 10px;
    margin-bottom: 15px;
}

.company-stats {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 20px;
}

.company-stats .stat-item {
    text-align: center;
}

.company-stats .stat-number {
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 5px;
}

.company-stats .stat-label {
    font-size: 12px;
    opacity: 0.9;
}
</style>

<script src="assets/js/jquery.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $d->map_key(); ?>&libraries=places"></script>
<script src="https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js"></script>
<script>
let map;
let markers = [];
let infoWindows = [];
let companies = [];
let clusterManager;
let isClustered = true;

$(document).ready(function() {
    initializeMap();
    loadCompanies();
    
    // Search functionality
    $('#searchCompany').on('input', function() {
        filterCompanies();
    });
    
    // Filter functionality
    $('#filterCountry, #filterStatus').on('change', function() {
        filterCompanies();
    });
    
    // Cluster toggle functionality
    $('#clusterToggle').on('change', function() {
        isClustered = $(this).val() === 'clustered';
        displayCompanies();
    });
    
    // Zoom level functionality
    $('#zoomLevel').on('change', function() {
        const zoomLevel = $(this).val();
        if (zoomLevel !== 'auto') {
            map.setZoom(parseInt(zoomLevel));
        }
    });
    
    // List search functionality
    $('#listSearch').on('input', function() {
        filterCompanyList();
    });
    
    // List sort functionality
    $('#listSort').on('change', function() {
        sortCompanyList();
    });
});

function initializeMap() {
    // Default center (India)
    const defaultCenter = { lat: 23.5937, lng: 78.9629 };
    
    map = new google.maps.Map(document.getElementById('map'), {
        center: defaultCenter,
        zoom: 5,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        mapTypeControl: true,
        streetViewControl: true,
        fullscreenControl: true
    });
}

function loadCompanies() {
    $.ajax({
        url: 'controller/companyMapController.php',
        type: 'POST',
        data: {
            action: 'getCompanies',
            csrf: '<?php echo $_SESSION["token"]; ?>'
        },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                companies = response.data;
                displayCompanies();
                updateStatistics();
            } else {
                alert('Error loading companies: ' + response.message);
            }
        },
        error: function() {
            alert('Error connecting to server');
        }
    });
}

function displayCompanies() {
    // Clear existing markers
    clearMarkers();
    
    // Clear company list
    $('#companyList').empty();
    
    let bounds = new google.maps.LatLngBounds();
    let hasValidLocations = false;
    let validMarkers = [];
    
    companies.forEach(function(company, index) {
        if (company.society_latitude && company.society_longitude && 
            company.society_latitude != '0' && company.society_longitude != '0') {
            
            const position = {
                lat: parseFloat(company.society_latitude),
                lng: parseFloat(company.society_longitude)
            };
            
            // Create marker
            const marker = new google.maps.Marker({
                position: position,
                title: company.society_name,
                icon: {
                    url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent('<svg width="32" height="32" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><defs><filter id="shadow" x="-50%" y="-50%" width="200%" height="200%"><feDropShadow dx="0" dy="2" stdDeviation="2" flood-color="#000000" flood-opacity="0.3"/></filter></defs><g filter="url(%23shadow)"><circle cx="16" cy="16" r="12" fill="#4285F4" stroke="#FFFFFF" stroke-width="2"/><circle cx="16" cy="16" r="6" fill="#FFFFFF"/></g></svg>'),
                    scaledSize: new google.maps.Size(32, 32)
                }
            });
            
            // Create info window
            const infoWindow = new google.maps.InfoWindow({
                content: createInfoWindowContent(company)
            });
            
            // Add click listener
            marker.addListener('click', function() {
                infoWindows.forEach(iw => iw.close());
                infoWindow.open(map, marker);
            });
            
            markers.push(marker);
            infoWindows.push(infoWindow);
            validMarkers.push(marker);
            
            bounds.extend(position);
            hasValidLocations = true;
        }
        
        // Add to company list
        addCompanyToList(company, index);
    });
    
    // Apply clustering or individual markers
    if (isClustered && validMarkers.length > 0) {
        clusterManager = new markerClusterer.MarkerClusterer({
            map,
            markers: validMarkers,
            algorithm: new markerClusterer.SuperClusterAlgorithm({
                radius: 100,
                maxZoom: 15
            }),
            renderer: {
                render: ({ count, position }) => {
                    const svg = `
                        <svg width="40" height="40" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="20" cy="20" r="18" fill="#4285F4" stroke="#FFFFFF" stroke-width="2"/>
                            <text x="20" y="25" text-anchor="middle" fill="white" font-size="14" font-weight="bold">${count}</text>
                        </svg>
                    `;
                    return new google.maps.Marker({
                        position,
                        icon: {
                            url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(svg),
                            scaledSize: new google.maps.Size(40, 40)
                        },
                        label: '',
                        title: `${count} companies`
                    });
                }
            }
        });
    } else {
        // Show individual markers
        validMarkers.forEach(marker => {
            marker.setMap(map);
        });
    }
    
    // Fit map to bounds if there are valid locations
    if (hasValidLocations) {
        map.fitBounds(bounds);
        if (markers.length === 1) {
            map.setZoom(12);
        }
    }
    
    // Update list count
    updateListCount();
}

function createInfoWindowContent(company) {
    return `
        <div style="min-width: 200px;">
            <h6 style="margin: 0 0 10px 0; color: #333;">${company.society_name}</h6>
            <p style="margin: 5px 0; font-size: 12px;"><strong>Address:</strong> ${company.society_address || 'N/A'}</p>
            <p style="margin: 5px 0; font-size: 12px;"><strong>City:</strong> ${company.city_name || 'N/A'}</p>
            <p style="margin: 5px 0; font-size: 12px;"><strong>Country:</strong> ${company.country_name || 'N/A'}</p>
            <p style="margin: 5px 0; font-size: 12px;"><strong>Status:</strong> 
                <span style="color: ${company.society_status == 0 ? 'green' : 'red'};">
                    ${company.society_status == 0 ? 'Active' : 'Inactive'}
                </span>
            </p>
            <p style="margin: 5px 0; font-size: 12px;"><strong>Admin:</strong> ${company.secretary_name || 'N/A'}</p>
            <p style="margin: 5px 0; font-size: 12px;"><strong>Mobile:</strong> ${company.secretary_mobile || 'N/A'}</p>
        </div>
    `;
}

function addCompanyToList(company, index) {
    const statusClass = company.society_status == 0 ? 'text-success' : 'text-danger';
    const statusText = company.society_status == 0 ? 'Active' : 'Inactive';
    const hasLocation = company.society_latitude && company.society_longitude && 
                       company.society_latitude != '0' && company.society_longitude != '0';
    
    const companyHtml = `
        <div class="company-item mb-2 p-2 border rounded" data-index="${index}" style="cursor: pointer; ${hasLocation ? '' : 'opacity: 0.6;'}">
            <div class="d-flex justify-content-between align-items-start">
                <div class="flex-grow-1">
                    <h6 class="mb-1" style="font-size: 14px;">${company.society_name}</h6>
                    <p class="mb-1" style="font-size: 12px; color: #666;">${company.city_name || 'N/A'}, ${company.country_name || 'N/A'}</p>
                    <small class="${statusClass}">${statusText}</small>
                </div>
                ${hasLocation ? '<i class="fa fa-map-marker text-primary"></i>' : '<i class="fa fa-map-marker text-muted"></i>'}
            </div>
        </div>
    `;
    
    $('#companyList').append(companyHtml);
    
    // Add click listener to company item
    $(`[data-index="${index}"]`).on('click', function() {
        if (hasLocation) {
            const position = {
                lat: parseFloat(company.society_latitude),
                lng: parseFloat(company.society_longitude)
            };
            map.setCenter(position);
            map.setZoom(15);
            
            // Open info window
            infoWindows.forEach(iw => iw.close());
            if (markers[index]) {
                infoWindows[index].open(map, markers[index]);
            }
        }
    });
}

function filterCompanies() {
    const searchTerm = $('#searchCompany').val().toLowerCase();
    const countryFilter = $('#filterCountry').val();
    const statusFilter = $('#filterStatus').val();
    
    const filteredCompanies = companies.filter(function(company) {
        const matchesSearch = company.society_name.toLowerCase().includes(searchTerm);
        const matchesCountry = !countryFilter || company.country_id == countryFilter;
        const matchesStatus = statusFilter === '' || company.society_status == statusFilter;
        
        return matchesSearch && matchesCountry && matchesStatus;
    });
    
    displayFilteredCompanies(filteredCompanies);
}

function displayFilteredCompanies(filteredCompanies) {
    // Clear existing markers
    clearMarkers();
    
    // Clear company list
    $('#companyList').empty();
    
    let bounds = new google.maps.LatLngBounds();
    let hasValidLocations = false;
    let validMarkers = [];
    
    filteredCompanies.forEach(function(company, index) {
        if (company.society_latitude && company.society_longitude && 
            company.society_latitude != '0' && company.society_longitude != '0') {
            
            const position = {
                lat: parseFloat(company.society_latitude),
                lng: parseFloat(company.society_longitude)
            };
            
            // Create marker
            const marker = new google.maps.Marker({
                position: position,
                title: company.society_name,
                icon: {
                    url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent('<svg width="32" height="32" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><defs><filter id="shadow" x="-50%" y="-50%" width="200%" height="200%"><feDropShadow dx="0" dy="2" stdDeviation="2" flood-color="#000000" flood-opacity="0.3"/></filter></defs><g filter="url(%23shadow)"><circle cx="16" cy="16" r="12" fill="#4285F4" stroke="#FFFFFF" stroke-width="2"/><circle cx="16" cy="16" r="6" fill="#FFFFFF"/></g></svg>'),
                    scaledSize: new google.maps.Size(32, 32)
                }
            });
            
            // Create info window
            const infoWindow = new google.maps.InfoWindow({
                content: createInfoWindowContent(company)
            });
            
            // Add click listener
            marker.addListener('click', function() {
                infoWindows.forEach(iw => iw.close());
                infoWindow.open(map, marker);
            });
            
            markers.push(marker);
            infoWindows.push(infoWindow);
            validMarkers.push(marker);
            
            bounds.extend(position);
            hasValidLocations = true;
        }
        
        // Add to company list
        addCompanyToList(company, index);
    });
    
    // Apply clustering or individual markers
    if (isClustered && validMarkers.length > 0) {
        clusterManager = new markerClusterer.MarkerClusterer({
            map,
            markers: validMarkers,
            algorithm: new markerClusterer.SuperClusterAlgorithm({
                radius: 100,
                maxZoom: 15
            }),
            renderer: {
                render: ({ count, position }) => {
                    const svg = `
                        <svg width="40" height="40" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="20" cy="20" r="18" fill="#4285F4" stroke="#FFFFFF" stroke-width="2"/>
                            <text x="20" y="25" text-anchor="middle" fill="white" font-size="14" font-weight="bold">${count}</text>
                        </svg>
                    `;
                    return new google.maps.Marker({
                        position,
                        icon: {
                            url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(svg),
                            scaledSize: new google.maps.Size(40, 40)
                        },
                        label: '',
                        title: `${count} companies`
                    });
                }
            }
        });
    } else {
        // Show individual markers
        validMarkers.forEach(marker => {
            marker.setMap(map);
        });
    }
    
    // Fit map to bounds if there are valid locations
    if (hasValidLocations && filteredCompanies.length > 0) {
        map.fitBounds(bounds);
        if (markers.length === 1) {
            map.setZoom(12);
        }
    }
    
    // Update list count
    updateListCount();
}

function clearMarkers() {
    if (clusterManager) {
        clusterManager.clearMarkers();
    }
    markers.forEach(marker => marker.setMap(null));
    markers = [];
    infoWindows = [];
}

function updateStatistics() {
    const total = companies.length;
    const active = companies.filter(c => c.society_status == 0).length;
    const withLocation = companies.filter(c => 
        c.society_latitude && c.society_longitude && 
        c.society_latitude != '0' && c.society_longitude != '0'
    ).length;
    const withoutLocation = total - withLocation;
    
    $('#totalCompanies').text(total);
    $('#activeCompanies').text(active);
    $('#companiesWithLocation').text(withLocation);
    $('#companiesWithoutLocation').text(withoutLocation);
}

function updateListCount() {
    const visibleCompanies = $('.company-item:visible').length;
    const withLocation = $('.company-item:visible .fa-map-marker.text-primary').length;
    $('#listCount').text(visibleCompanies);
    $('#listWithLocation').text(`(${withLocation} with location)`);
}

function filterCompanyList() {
    const searchTerm = $('#listSearch').val().toLowerCase();
    
    $('.company-item').each(function() {
        const companyName = $(this).find('h6').text().toLowerCase();
        const companyLocation = $(this).find('p').text().toLowerCase();
        const companyStatus = $(this).find('small').text().toLowerCase();
        
        const matches = companyName.includes(searchTerm) || 
                       companyLocation.includes(searchTerm) || 
                       companyStatus.includes(searchTerm);
        
        $(this).toggle(matches);
    });
    
    updateListCount();
}

function sortCompanyList() {
    const sortBy = $('#listSort').val();
    const $companyList = $('#companyList');
    const $companies = $companyList.find('.company-item').get();
    
    $companies.sort(function(a, b) {
        const $a = $(a);
        const $b = $(b);
        
        switch(sortBy) {
            case 'name':
                return $a.find('h6').text().localeCompare($b.find('h6').text());
            case 'status':
                const statusA = $a.find('small').text();
                const statusB = $b.find('small').text();
                return statusA.localeCompare(statusB);
            case 'location':
                const hasLocationA = $a.find('.fa-map-marker.text-primary').length > 0;
                const hasLocationB = $b.find('.fa-map-marker.text-primary').length > 0;
                return hasLocationB - hasLocationA; // Show companies with location first
            case 'country':
                return $a.find('p').text().localeCompare($b.find('p').text());
            default:
                return 0;
        }
    });
    
    $companyList.empty().append($companies);
}

function refreshMap() {
    loadCompanies();
}
</script>
