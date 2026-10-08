<?php 
  extract($_GET);
  if(isset($_GET['lId'])) { 
    $lId = (int)$lId;
  }

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,"https://master.fincasys.com/commonApi/language_controller.php");
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,
              "getLanguageAll=getLanguageAll&society_id=$society_id ");

  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'key: ' . EnvLoader::get('API_KEY')
  ));

  $server_output = curl_exec($ch);

  curl_close ($ch);
  $server_output=json_decode($server_output,true);


?>
<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-6">
        <h4 class="page-title">State Language </h4>
        
      </div>
      <div class="col-sm-6">
              <form method="get">
                <select class="form-control single-select" name="lId" onchange="this.form.submit()">
                  <option value="">  Select Language </option>
                  <?php  for ($i1=0; $i1 <count($server_output['language']) ; $i1++) {  ?>
                  <option <?php if(isset($_GET['lId']) && $server_output['language'][$i1]['language_id']==$_GET['lId']) { echo "selected";} ?> value="<?php echo $server_output['language'][$i1]['language_id']; ?>"><?php echo $server_output['language'][$i1]['language_name']; ?>-<?php echo $server_output['language'][$i1]['language_name_1']; ?></option>
                  <?php } ?>
                </select>
              </form>

      </div>
    </div>
    <!-- End Breadcrumb-->


    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <?php if(isset($_GET['lId'])) { ?>
                <div class="table-responsive">
                  <table  class="table table-bordered">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Language Name</th>
                      </tr>
                    </thead>
                    <tbody>
                       <form action="controller/cscController.php" id="adMultiUnit" method="post" >
                        <input type="hidden" name="setStateLanguage" value="<?php echo 'setStateLanguage';?>">
                        <input type="hidden" name="lId" value="<?php echo $lId;?>">
                      <?php 
                        $i=1;
                        $q=$d->select("states","flag=1","");
                        $statesRows = [];
                        $stateIds = [];
                        while ($data = mysqli_fetch_array($q)) {
                          $statesRows[] = $data;
                          $stateIds[] = (int)$data['state_id'];
                        }

                        // Prefetch language names for all states (avoid N+1)
                        $lngByState = [];
                        if (!empty($stateIds) && !empty($lId)) {
                          $lIdEsc = (int)$lId;
                          $stateIdsIn = implode(',', array_map('intval', $stateIds));
                          $qt = $d->select(
                            "country_state_city_language",
                            "common_id_csc IN ($stateIdsIn) AND language_id='$lIdEsc' AND cat_type=1"
                          );
                          while ($lngRow = mysqli_fetch_array($qt)) {
                            $lngByState[(int)$lngRow['common_id_csc']] = $lngRow;
                          }
                        }

                        foreach ($statesRows as $data) {
                        extract($data);
                        $lngData = $lngByState[(int)$state_id] ?? [];

                      ?>
                        <tr>
                          
                          <td><?php echo $i++; ?></td>
                            <td ><?php echo $name; ?></td>
                           
                          <td>
                              <input value="<?php echo $state_id; ?>" type="hidden" name="state_id[]">

                              <input class="form-control" value="<?php echo $lngData['language_value_name'] ?? ''; ?>" type="text" name="language_value_name[]">
                          </td>
                          
                       
                         
                        </tr>

                      <?php } if (count($statesRows)>0) {  ?>
                        <tr>
                          <td colspan="7" class="text-center">
                            <button type="submit"  class="btn ml-3 btn-primary btn-sm">Update Name</button>
                          </td>

                        </tr>
                       <?php } ?>
                       </form>
                    </tbody>

                  </table>
                </div>
             <?php } else {
              echo "Please Select Society";
             } ?>
          </div>
        </div>
      </div>
    </div><!-- End Row-->

  </div>
</div>

