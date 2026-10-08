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
              <form method="get">
    <div class="row pt-2 pb-2">
      <div class="col-sm-6">
        <h4 class="page-title">City Language </h4>
        
      </div>
      <div class="col-sm-6">
                <select class="form-control single-select" name="lId" onchange="this.form.submit()">
                  <option value="">  Select Language </option>
                  <?php  for ($i1=0; $i1 <count($server_output['language']) ; $i1++) {  ?>
                  <option <?php if(isset($_GET['lId']) && $server_output['language'][$i1]['language_id']==$_GET['lId']) { echo "selected";} ?> value="<?php echo $server_output['language'][$i1]['language_id']; ?>"><?php echo $server_output['language'][$i1]['language_name']; ?>-<?php echo $server_output['language'][$i1]['language_name_1']; ?></option>
                  <?php } ?>
                </select>
              </form>

      </div>
      <div class="col-sm-6">
                <select class="form-control single-select" name="sId" onchange="this.form.submit()">
                  <option value="">  Select State </option>
                  <?php 
                     $sq=$d->select("states","flag=1","");
                   while ($stateData=mysqli_fetch_array($sq)) {  ?>
                  <option <?php if(isset($_GET['sId']) && $stateData['state_id']==$_GET['sId']) { echo "selected";} ?> value="<?php echo $stateData['state_id']; ?>"><?php echo $stateData['name']; ?></option>
                  <?php } ?>
                </select>

      </div>
    </div>
              </form>
    <!-- End Breadcrumb-->


    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <?php if(isset($_GET['lId']) && $_GET['lId']!='') { ?>
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
                        <input type="hidden" name="setCityLanguage" value="<?php echo 'setStateLanguage';?>">
                        <input type="hidden" name="lId" value="<?php echo $lId;?>">
                        <input type="hidden" name="sId" value="<?php echo $_GET['sId'];?>">
                      <?php 
                        $i=1;
                        $sIdFilter = isset($_GET['sId']) ? $d->sanitizeReportFilterIdAsInt($_GET['sId']) : 0;
                        $q=$d->select("states,cities","states.state_id=cities.state_id AND states.flag=1 AND cities.flag=1 AND states.state_id='$sIdFilter'","");
                        $citiesRows = [];
                        $cityIds = [];
                        while ($data = mysqli_fetch_array($q)) {
                          $citiesRows[] = $data;
                          $cityIds[] = (int)$data['city_id'];
                        }

                        // Prefetch language names for all cities (avoid N+1)
                        $lngByCity = [];
                        if (!empty($cityIds) && !empty($lId)) {
                          $lIdEsc = (int)$lId;
                          $cityIdsIn = implode(',', array_map('intval', $cityIds));
                          $qt = $d->select(
                            "country_state_city_language",
                            "common_id_csc IN ($cityIdsIn) AND language_id='$lIdEsc' AND cat_type=2"
                          );
                          while ($lngRow = mysqli_fetch_array($qt)) {
                            $lngByCity[(int)$lngRow['common_id_csc']] = $lngRow;
                          }
                        }

                        foreach ($citiesRows as $data) {
                        extract($data);
                        $lngData = $lngByCity[(int)$city_id] ?? [];

                      ?>
                        <tr>
                          
                          <td><?php echo $i++; ?></td>
                            <td ><?php echo $name; ?></td>
                           
                          <td>
                              <input value="<?php echo $city_id; ?>" type="hidden" name="city_id[]">

                              <input class="form-control" value="<?php echo $lngData['language_value_name'] ?? ''; ?>" type="text" name="language_value_name[]">
                          </td>
                          
                       
                         
                        </tr>

                      <?php } if (count($citiesRows)>0) {  ?>
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
              echo "Please Select State & Language";
             } ?>
          </div>
        </div>
      </div>
    </div><!-- End Row-->

  </div>
</div>

