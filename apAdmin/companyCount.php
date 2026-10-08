<?php
error_reporting(0);
extract(array_map("test_input" , $_REQUEST));
error_reporting(0);

$callLocationApi = function ($postData) use ($d, $base_url, $keydb) {
  $encrypted_data = $d->manage_encryption('1', $postData);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, rtrim($base_url, '/') . '/mainApiEnc/locationController.php');
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $encrypted_data);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'key: ' . $keydb,
    'Content-Type: text/plain',
  ));
  $server_output = curl_exec($ch);
  curl_close($ch);
  $decoded = json_decode($d->manage_decryption('1', $server_output), true);
  return is_array($decoded) ? $decoded : array();
};

$server_output = $callLocationApi(array(
  'getCountriesMaster' => 'getCountriesMaster',
  'language_id' => 1,
  'countryids' => $countryids,
));
?>

<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-4">
        <h4 class="page-title">Company Count</h4>
        
      </div>
      <div class="col-sm-8">
      </div>
    </div>
    <!-- End Breadcrumb-->
  </div>
  
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-header">
        </div>
        <div class="card-body">
          <canvas id="companyCount"></canvas>
        </div>
      </div>
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <!-- <table id="reportTable" class="table table-bordered"> -->
            <table class="table align-middle mb-0">
              <thead>
                <tr>
                  <th>#</th>
                  <?php if ($filter==1) { ?>
                    <th>Category</th>
                  <?php } elseif ($filter==2) { ?>
                    <th>Zipcode</th>
                  <?php } elseif ($filter==3) { ?>
                    <th>City</th>
                  <?php } elseif ($filter==4) { ?>
                    <th>State</th>
                  <?php } elseif ($filter==5) { ?>
                    <th>Service Providers</th>
                  <?php } elseif ($filter==6) { ?>
                    <th>Complains</th>
                  <?php } else { ?>
                    <th>Category</th>
                  <?php } ?>
                  <th><?php if ($filter==5) {
                    echo "Rating";
                  } else { echo "Count";} ?></th>
                </tr>
              </thead>
              <tfoot>
                <tr>
                  <th class="no-search-box"></th>
                  <th></th>
                  <th></th>
                </tr>
              </tfoot>
              <tbody>
                <?php 
                  $k=1;
                  $cityArray = array();
                  $companyCountArray = array();
                  $date = date('Y-m-d');
                  $server_output = $callLocationApi(array(
                    'getCityAllMaster' => 'getCityAllMaster',
                    'language_id' => 1,
                    'countryids' => $countryids,
                  ));
                  $cityIdArray = array();
                  for ($i=0; $i <count($server_output['cities']) ; $i++) {
                    array_push($cityIdArray,$server_output['cities'][$i]['city_id']);
                  }
                  $cityIdArray = array_unique($cityIdArray);
                  $cityIdArray = array_values($cityIdArray);

                  for ($j=0; $j <count($cityIdArray) ; $j++) {
                    $count = $d->count_data_direct("society_id","society_master","city_id=$cityIdArray[$j] ");
                    $selectSociety = $d->selectArray("society_master","city_id='$cityIdArray[$j]'","GROUP BY city_id");
                    array_push($cityArray,$selectSociety['city_name']);
                    array_push($companyCountArray,$count);
                ?>
                <tr>
                  <td><?php echo $k++; ?></td>
                  <td>
                    <a href="societyList?city_id=<?php echo $cityIdArray[$j] ?>"><?php echo $selectSociety['city_name'] ?></a>
                  </td>
                  <td><?php echo $count; ?></td>
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
<script src="assets/js/jquery.min.js"></script>
<script src="assets/plugins/Chart.js/Chart.min.js"></script>
<?php 
  $cityList = "'" . implode("','", $cityArray). "'";
  $companyCountList = implode(",",$companyCountArray);
?>
  <script type="text/javascript">
    if ($('#companyCount').length) {
      var ctx = document.getElementById('companyCount').getContext('2d');

      var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: [<?php echo $cityList; ?>],
          datasets: [{
            data: [<?php echo $companyCountList; ?>],
            backgroundColor: '#5E72E4',
            borderColor: '#1a4089',
            borderWidth: 3
          }]
        },
        options: {
          legend: {
            display: false,
          },
          scales: {
            xAxes: [{
              ticks: {
                fontSize: 100,
                display: false
              },
              gridLines: {
                display: false
              }
            }],
            yAxes: [{
              ticks: {
                beginAtZero: true
              }
            }],
          }
        },
      });
    }
  </script>
