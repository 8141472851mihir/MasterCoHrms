<?php 
extract($_REQUEST);
if (!isset($limit)) {
  $limit=(int)0;
} else if($limit!='') {
   $limit=(int)$limit;
}
   ?>
 
<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-9">
        <h4 class="page-title"> Add Language Key Value  </h4>
      </div> 
      <div class="col-sm-3">
      </div>
    </div>
  </div>
  <!-- End Breadcrumb-->
      <div class="row">
      <div class="container-fluid">
        <div class="card">
          <div class="card-body"> 
            <div class="table-responsive">
              <form method="get" id="form" action="">
              <div class="row">
                 <div class="col-md-1">
                 </div>
                <div class="col-md-5">
                  <select onchange="this.form.submit()" class="form-control single-select" name="language_id" id="select">
                    <option value=""> Select language</option>
                    <?php
                    $data=$d->select("language_master","","");
                    while ($row=mysqli_fetch_array($data)) { ?>
                      <option <?php if(isset($_GET['language_id']) && $_GET['language_id'] == $row['language_id']){echo "selected";} ?> value="<?php echo $row['language_id']; ?>"><?php echo $row['language_name']; ?> (<?php echo $row['language_name_1']; ?>)</option>
                    <?php } ?>
                  </select>
                </div>
                <div class="col-md-5">
                  <select onchange="this.form.submit()" name="limit" class="form-control">
                      <option <?php if(isset($_GET['limit']) && $_GET['limit'] == 100){echo "selected";} ?> value="0">From 1</option>
                      <option <?php if(isset($_GET['limit']) && $_GET['limit'] == 100){echo "selected";} ?>  value="100">From 100</option>
                      <option <?php if(isset($_GET['limit']) && $_GET['limit'] == 200){echo "selected";} ?>  value="200">From 200</option>
                      <option <?php if(isset($_GET['limit']) && $_GET['limit'] == 300){echo "selected";} ?>  value="300">From 300</option>
                      <option <?php if(isset($_GET['limit']) && $_GET['limit'] == 400){echo "selected";} ?>  value="400">From 400</option>
                      <option <?php if(isset($_GET['limit']) && $_GET['limit'] == 500){echo "selected";} ?>  value="500">From 500</option>
                      <option <?php if(isset($_GET['limit']) && $_GET['limit'] == 600){echo "selected";} ?>  value="600">From 600</option>
                      <option <?php if(isset($_GET['limit']) && $_GET['limit'] == 700){echo "selected";} ?>  value="700">From 700</option>
                      <option <?php if(isset($_GET['limit']) && $_GET['limit'] == 800){echo "selected";} ?>  value="800">From 800</option>
                      <option <?php if(isset($_GET['limit']) && $_GET['limit'] == 900){echo "selected";} ?>  value="900">From 900</option>
                      <option <?php if(isset($_GET['limit']) && $_GET['limit'] == 1000){echo "selected";} ?>  value="1000">From 1000</option>
                      <option <?php if(isset($_GET['limit']) && $_GET['limit'] == 1100){echo "selected";} ?>  value="1100">From 1100</option>
                      <option <?php if(isset($_GET['limit']) && $_GET['limit'] == 1200){echo "selected";} ?>  value="1200">From 1200</option>
                      <option <?php if(isset($_GET['limit']) && $_GET['limit'] == 1300){echo "selected";} ?> value="1300">From 1300</option>
                      <option <?php if(isset($_GET['limit']) && $_GET['limit'] == 1400){echo "selected";} ?>  value="1400">From 1400</option>
                      <option <?php if(isset($_GET['limit']) && $_GET['limit'] == 1500){echo "selected";} ?>  value="1500">From 1500</option>
                  </select>
                </div>
                 <div class="col-md-1">
                 </div>
              </div>
              </form>
              <br>
              <?php if (isset($_GET['language_id']) && $language_id!=0) {  ?>
              <form id="editLanguageValueKeyFrm" action="controller/languageKeyValueControllerGuard.php" method="post">
                <input type="hidden" name="limit" value="<?php echo $limit;?>">
                <table id="" class="table table-bordered data_table">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>key_name</th>
                      <th>EDIT</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $i=1;
                    $q=$d->select("language_key_master_gatekeeper","","LIMIT $limit,100");
                    $keyRows = [];
                    $keyIds = [];
                    while($row=mysqli_fetch_array($q)) {
                      $keyRows[] = $row;
                      $keyIds[] = (int)$row['language_key_id'];
                    }

                    // Prefetch English (language_id=1) values for all keys on this page
                    $valuesByKeyId = [];
                    if (!empty($keyIds)) {
                      $keyIdsIn = implode(',', array_map('intval', $keyIds));
                      $qc = $d->select(
                        "language_key_value_master_gatekeeper",
                        "language_id='1' AND language_key_id IN ($keyIdsIn)"
                      );
                      while ($oldData = mysqli_fetch_array($qc)) {
                        $kid = (int)$oldData['language_key_id'];
                        if (!isset($valuesByKeyId[$kid])) {
                          $valuesByKeyId[$kid] = ['values' => [], 'ids' => []];
                        }
                        $valuesByKeyId[$kid]['values'][] = $oldData['value_name'];
                        $valuesByKeyId[$kid]['ids'][] = $oldData['key_value_id'];
                      }
                    }

                    foreach ($keyRows as $row) {
                    $kid = (int)$row['language_key_id'];
                    $valuArray = $valuesByKeyId[$kid]['values'] ?? [];
                    $key_value_idArray = $valuesByKeyId[$kid]['ids'] ?? [];
                      ?>
                      
                      <tr>
                        <td><?php echo $i++;?></td>
                        <td><?php echo $row['key_name'];?></td>
                        <td>
                          <?php if($row['key_type']==1) {
                            for ($i1=0; $i1 <$row['no_of_key'] ; $i1++) { 
                           ?>
                          <input type="hidden" name="key_value_id[]" value="">
                          <input type="hidden" name="language_key_id[]" value="<?php echo $row['language_key_id']; ?>">
                          <input placeholder="Value <?php echo $i1+1;?>" required="" class="form-control" type="text" class="value_name" id="value_name" value="<?php if(count($valuArray)>0)  { echo $valuArray[$i1]; } ?>" name="value_name[]">
                        <?php } } else {  ?>
                          <input type="hidden" name="key_value_id[]" value="">
                          <input type="hidden" name="language_key_id[]" value="<?php echo $row['language_key_id']; ?>">
                          <input required="" class="form-control" type="text" class="value_name" id="value_name" value="<?php if(count($valuArray)>0)  { echo $valuArray[0]; } ?>" name="value_name[]">
                        <?php } ?>
                        </td>
                      </tr>         
                    <?php } ?>  
                  </tbody>
                </table>
                <div class="text-center">
                  <input type="hidden" name="AddLanguageKeyValue" value="AddLanguageKeyValue">
                  <input type="hidden" name="language_id" value="<?=$_GET['language_id']?>">
                  <button type="submit" class="btn btn-primary" name="submit"  value="submit"><i class="fa fa-edit">SUBMIT</i>
                  </button>
                </div>
              </form>
            <?php }  ?>
            </div>
          </div>
        </div>     
      </div>   
    </div>