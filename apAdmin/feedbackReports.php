<?php
error_reporting(0);
extract($_GET);
// if (!isset($_GET['filter'])) {
//   $filter = 1;
// }
?>
<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-4">
        <h4 class="page-title">Feedback </h4>
        
      </div>
      <div class="col-sm-8">
      </div>
    </div>
    <!-- End Breadcrumb-->
  </div>
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <!-- <div class="card-header">
          <form method="GET">
            <select class="form-control single-select" onchange="this.form.submit()" name="filter">
              <option value=""></option>
              <option <?php if($filter==1){echo "selected";} ?> value="1">Category</option>
              <option <?php if($filter==2){echo "selected";} ?> value="2">Zipcode</option>
              <option <?php if($filter==3){echo "selected";} ?> value="3">City</option>
              <option <?php if($filter==4){echo "selected";} ?> value="4">State</option>
              <option <?php if($filter==5){echo "selected";} ?> value="5">Rating</option>
              <option <?php if($filter==6){echo "selected";} ?> value="6">Complains</option>
            </select>
          </form>
        </div> -->
        <div class="card-body">
          <div class="card-body" >
            <canvas id="serviceProviderCharts"></canvas>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <!-- <table id="reportTable" class="table table-bordered"> -->
            <table id="exampleReport" class="table table-bordered align-middle mb-0 ">
              <thead>
                <tr>
                  <th>Company</th>
                  <th>Count</th>
                </tr>
              </thead>
              
              <tbody>
                <?php 
                  $i1=1;
                  $feedbackArray = array();
                  $feedbackCountArray = array();
                  $date = date('Y-m-d');
                  
                  $q=$d->selectRow("feedback_master.society_id,COUNT(feedback_master.feedback_id) as count,society_master.society_name","feedback_master,society_master","feedback_master.society_id = society_master.society_id $countryAppendQuerySociety","GROUP BY feedback_master.society_id");
                  while ($data=mysqli_fetch_array($q)) {
                    extract($data);
                ?>
                <tr>
                  <td>
                    <?php
                     echo $society_name;
                      array_push($feedbackArray,$society_name);
                      array_push($feedbackCountArray,$count);
                    ?>
                    
                  </td>
                  <td>
                    <?php echo $count; ?>
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
<script src="assets/js/jquery.min.js"></script>
<script src="assets/plugins/Chart.js/Chart.min.js"></script>
<?php 
  $feedbackList = "'" . implode("','", $feedbackArray). "'";
  $feedbackCountList = implode(",",$feedbackCountArray);
?>
  <script type="text/javascript">
    if ($('#serviceProviderCharts').length) {
      var ctx = document.getElementById('serviceProviderCharts').getContext('2d');

      var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: [<?php echo $feedbackList; ?>],
          datasets: [{
            label: 'Category',
            data: [<?php echo $feedbackCountList; ?>],
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
