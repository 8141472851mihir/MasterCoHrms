<div class="content-wrapper">
    <div class="container-fluid">

      <!--Start Dashboard Content-->
   

    

    <div class="row">
      <div class="col-12 col-lg-12">
        <div class="card">
          <div class="card-header">
            Menu Wise Usages
            <div class="card-action">
              <?php
              $colors_array= array();
              $q=$d->selectRow("ram.app_menu_id,ram.menu_title,f.total","(SELECT app_menu_master.menuId,count(app_menu_master.menuId) as total FROM app_menu_master GROUP BY menuId) AS f RIGHT JOIN resident_app_menu AS ram ON ram.app_menu_id=f.menuId");
              while ($data=mysqli_fetch_array($q)) {
                  $analytic_color = $d->bm_random_hex();
                  $checkcolorexistanalytics = in_array($analytic_color, $colors_array);
                  if(!$checkcolorexistanalytics){
                    array_push($colors_array,$analytic_color);
                  }
                array_push($colors_array,$d->bm_random_hex());
              }
              $color_names= '"'.implode('", "', $colors_array).'"';
              ?>
            </div>
          </div>
          <div class="card-body">
            <canvas id="unitStatus1" height="150"></canvas>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
        <div class="col-12 col-lg-12">
          <div class="card">
            <div class="card-header">
                Query Generate (<?php echo date("F-Y"); ?>)
                </div>
                <div class="card-body">
                  <div id="chart1" height="150"></div>
                </div>
          </div>
        </div>
      </div>

<script src="assets/js/jquery.min.js"></script>
<script src="assets/plugins/Chart.js/Chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script type="text/javascript">
  $(function() {
    "use strict";
    <?php
    $labels = [];
    $data = [];
    $sd = $d->count_data_direct("menuId","app_menu_master","menuId='-1' AND societyId NOT IN (1,2,597,598,625)");
    $sd1 = $d->count_data_direct("menuId","app_menu_master","menuId='-2' AND societyId NOT IN (1,2,597,598,625)");
    $q1=$d->selectRow("ram.app_menu_id,ram.menu_title,f.total","(SELECT app_menu_master.menuId,count(app_menu_master.menuId) as total FROM app_menu_master WHERE app_menu_master.societyId NOT IN (1,2,597,598,625) GROUP BY menuId) AS f RIGHT JOIN resident_app_menu AS ram ON ram.app_menu_id=f.menuId","total!=''","ORDER BY total DESC");
    while ($row = mysqli_fetch_array($q1)) {
      $labels[] = $row['menu_title'];
      $data[] = $row['total'];
    }
    if ($sd > 0) {
      $labels[] = "Timeline";
      $data[] = $sd;
    }
    if ($sd1 > 0) {
      $labels[] = "Chat";
      $data[] = $sd1;
    }
    $combined = array_map(null, $labels, $data);
    usort($combined, function ($a, $b) {
        return $b[1] <=> $a[1];
    });
    $sortedLabels = array_column($combined, 0);
    $sortedData = array_column($combined, 1);
    ?>

    var ctx = document.getElementById("unitStatus1").getContext('2d');
    var myChart = new Chart(ctx, {
      type: 'pie',
      data: {
         labels: [
            <?php
              if (!empty($sortedLabels)) {
                  echo '"' . implode('","', $sortedLabels) . '"';
              }
            ?>
        ],
          datasets: [{
            backgroundColor: [<?=$color_names?>,],
            data: [<?= implode(',', $sortedData) ?>]
        }]
      },
          options: {
              legend: {
          position: 'bottom',
                display: true,
          labels: {
                  boxWidth:40
                }
              }
          }
    });

     //IS_1134
 
 //DY 12-10-23 Start

    var options = {
      series: [{
         name: 'Query',
         data: [<?php
          for($i=1; $i<=31; $i++)
          {
            $time=mktime(12, 0, 0, date('m'), $i, date('Y'));
            if(date('m', $time)==date('m')){
              $list=date('d-m-Y', $time);
              $where = "society_id!='0' AND inquiry_type='0' AND feedback_type='0' AND feedback_date_time LIKE '$list%' ORDER BY feedback_id DESC";
              $q = $d->count_data_direct("feedback_id","feedback_master","$where");
              print_r($q.',');
            }
          }
          ?>]
      }],
      chart: {
         type: 'bar',
         height: 350
      },
      plotOptions: {
         bar: {
            horizontal: false,
            columnWidth: '55%',
            endingShape: 'rounded'
         },
      },
      dataLabels: {
         enabled: false
      },
      stroke: {
         show: true,
         width: 2,
         // colors: ['transparent']
      },
      xaxis: {
         categories: [<?php
          for($i=1; $i<=31; $i++)
          {
            $time=mktime(12, 0, 0, date('m'), $i, date('Y'));
            if(date('m', $time)==date('m')){
              $list=date('d-D', $time);
              print_r("'".$list."'".',');
            }
          }
          ?>],
      },
      fill: {
         opacity: 1, 
      },
      colors: ['#17a00e'],
      tooltip: {
         y: {
            formatter: function (val) {
               return val
            }
         }
      }
   };
   var chart = new ApexCharts(document.querySelector("#chart1"), options);
   chart.render();
 //DY 12-10-23 End
  });
</script>
