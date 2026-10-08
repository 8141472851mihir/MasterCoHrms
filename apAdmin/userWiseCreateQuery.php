<?php
extract($_GET);
?>
<div class="content-wrapper">
  <div class="container-fluid">
    <div class="row pt-2 pb-2">
      <div class="col-sm-3">
        <h4 class="page-title">User Wise Create Query</h4>
      </div>
      <div class="col-sm-6">
        <form action="" method="get" accept-charset="utf-8">
          <div class="row">
            <div class="col-sm-6">
              <select type="text" required="" id="months" onchange="this.form.submit()" class="form-control single-select" name="months">
                <?php
                for($i=0;$i<=11;$i++){
                  $month = date("F", strtotime( date( 'Y-m-01' )." +$i months"));
                  ?>
                  <option value="<?=$month?>" <?php if(isset($months) && $months==$month) {echo "selected";} ?> ><?=$month?></option>
                  <?php
                }
                ?>
              </select>
            </div>
            <div class="col-sm-6">
              <select type="text" required="" id="years" onchange="this.form.submit()" class="form-control single-select" name="years">
                <?php
                for($i=0;$i<2;$i++)
                {
                  $year = date('Y', strtotime('-'.$i.' years'));
                  ?>
                  <option value="<?=$year?>" <?php if(isset($years) && $years==$year) {echo "selected";} ?> ><?=$year?></option>
                  <?php
                }
                ?>
              </select>
            </div>
          </div>
        </form>
      </div>
    </div>
      <?php
      if(isset($months) && !empty($months)){
        $nmonth = date('m',strtotime($months));
        $searchMonth =  $nmonth;
      }else{
        $searchMonth =  date('m');
      }
      if(isset($years) && !empty($years)){
        $searchyear =  $years;
      }else{
        $searchyear =  date('Y');
      }
      if(isset($months) && isset($years)){
        $date = $searchMonth."-".$searchyear;
        $whare = "AND feedback_master.feedback_date_time LIKE '%$date%'";
      }else{
        $date = date('m-Y');
        $whare = "AND feedback_master.feedback_date_time LIKE '%$date%'";
      }
      ?>
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <div class="table-responsive">
                <table id="reportTable" class="table table-bordered">
                <!-- <table class="table align-middle mb-0"> -->
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Name</th>
                      <th>Count</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    if(isset($months) && !empty($months)){
                      $nmonth = date('m',strtotime($months));
                      $searchMonth =  $nmonth;
                    }else{
                      $searchMonth =  date('m');
                    }
                    if(isset($years) && !empty($years)){
                      $searchyear =  $years;
                    }else{
                      $searchyear =  date('Y');
                    }
                    if(isset($months) && isset($years)){
                      $date = $searchMonth."-".$searchyear;
                      $whare = "feedback_master.feedback_date_time LIKE '%$date%'";
                    }else{
                      $date = date('m-Y');
                      $whare = "feedback_master.feedback_date_time LIKE '%$date%'";
                    }
                    $i=1;
                    $q=$d->selectRow("b.admin_id,b.admin_name,f.total","(SELECT feedback_master.feedback_id, feedback_master.created_by, count(feedback_master.feedback_id) as total FROM feedback_master WHERE $whare GROUP BY created_by) AS f RIGHT JOIN bms_admin_master AS b ON b.admin_id=f.created_by");
                    while ($data=mysqli_fetch_array($q)) {
                      extract($data);
                      ?>
                      <tr>
                        <td><?=$i++?></td>
                        <td class="tableWidth"><?php echo $admin_name; ?></td>
                        <td class="tableWidth"><?php if(!empty($total)){echo $total;}else{echo "0";}?></td>
                      </tr>
                      <?php
                    } ?> 
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
  </div>
</div>