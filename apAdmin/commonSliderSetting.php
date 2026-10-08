<?php
extract(array_map("test_input" , $_REQUEST));
error_reporting(0);
$id = $d->sanitizeReportFilterIdAsInt($_GET['id'] ?? ($id ?? 0));
if ($id <= 0) {
  $_SESSION['msg1']="Please Select Slider";
  echo ("<script LANGUAGE='JavaScript'>
      window.location.href='sliderImages';
      </script>");
  exit();
}
  $q=$d->select("app_slider_master","app_slider_id=$id");
  $data=mysqli_fetch_array($q);
  $sliderName = $data['slider_image_name'];

?>

<div class="content-wrapper">
    <div class="container-fluid">
      <!-- Breadcrumb-->
     <div class="row pt-2 pb-2">
        <div class="col-sm-9">
        <h4 class="page-title">App Banner</h4>
        
      </div>
     </div>
    <!-- End Breadcrumb-->
    
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col-md-12 p-3">
                <div class="row ">
                  <div class="col-md-2">
                    <h5>Description : </h5>
                  </div>
                  <div class="col-md-10">
                    <p><?=($data['about_offer']!='') ? $data['about_offer'] : 'NA' ?></p>
                  </div>
                  <div class="col-md-2">
                    <h5>Contact No. : </h5>
                  </div>
                  <div class="col-md-10">
                    <p><?=($data['page_mobile']!='' AND $data['page_mobile']!='0') ? $data['page_mobile'] : '' ?></p>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-2">
                    <h5>Youtube Video : </h5>
                  </div>
                  <div class="col-md-10">
                    <p><?= ($data['youtube_url']!='') ? '<a href="https://www.youtube.com/watch?v='.$data['youtube_url'].'" target="_blank">https://www.youtube.com/watch?v='.$data['youtube_url'].'</a>' : 'NA' ?></p>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-2">
                    <h5>Website : </h5>
                  </div>
                  <div class="col-md-10">
                    <p><?= ($data['page_url']!='') ? '<a href="'.$data['page_url'].'" target="_blank">'.$data['page_url'].'</a>' : 'NA' ?></p>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-2">
                    <h5>Image : </h5>
                  </div>
                  <div class="col-md-10">
                    <?php $slider=$base_url.'img/sliders/'.$data["slider_image_name"]; ?>
                    <p><a  data-fancybox="images" data-caption="Photo Name : <?php echo $data["slider_image_name"]; ?>" href="<?=$slider; ?>" target="_blank"><img width="200" src="<?=$slider; ?>"></a></p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            
            <form id="CommonSliderFrm" action="controller/sliderController.php" method="post" enctype="multipart/form-data">
            
             <?php 
              $edit_id =  $id;
                 $q=$d->select("app_common_slider_master","app_slider_id='$id'");
                 $selected_society_ids = array();
                while ( $common_service_providers_data=mysqli_fetch_array($q)) {
                  $selected_society_ids[] = $common_service_providers_data['society_id'];
                }
               ?>
                 <input type="hidden" name="app_slider_id" value="<?php echo $edit_id;?>">
             <div class="form-group row">
                <label for="input-10" class="col-sm-6 col-form-label">Add New Company For This Banner</label>
                <!-- <label for="input-10" class="col-sm-6 col-form-label">Add New City For This Service Provider</label> -->
                </div>
               
              <div class="form-group row">
               
                <div class="col-sm-4">
                 <?php 
                 $ids = join("','",$selected_society_ids);   
                    $qss=$d->select("society_master","society_id NOT IN ('$ids') AND society_status = 0 $countryAppendQuerySocietySingle");
                    ?>
                  <select multiple="multiple" name="society_id[]" id="society_id" class="form-control multiple-select" required="" >
                    <option value="">-- Select <?php echo $xml->string->society; ?> --</option>
                   <?php
                    while ($sData=mysqli_fetch_array($qss)) {
                   
                     ?>
                     <option value="<?php echo $sData['society_id']; ?>"><?php echo $sData['society_name'].' ('.$sData['city_name'].')'; ?></option>
                     <?php } ?>
                  </select>
                </div>
                <div class="col-sm-2">
                  <button type="submit" id="CommonSliderBtn" class="btn btn-success" name="CommonSliderBtn"><i class="fa fa-check-square-o"></i> Add</button>
                </div>
                
              </div>
            </form>


            <hr>

            <form id="CommonSliderFrm1" action="controller/sliderController.php" method="post" enctype="multipart/form-data">
            
             <?php 
              $edit_id =  $id;
               ?>
                 <input type="hidden" name="app_slider_id" value="<?php echo $edit_id;?>">
             <div class="form-group row">
                <label for="input-10" class="col-sm-6 col-form-label">Add New Company By Country For This Banner</label>
                </div>
               
              <div class="form-group row">
               
                <div class="col-sm-4">
                 <?php 
                 $qcountries=$d->selectSpArray("getCountry");
                 $cIdsArray = explode(",", str_replace("'", "", $countryids));
                    ?>
                  <select multiple="multiple" name="country_id[]" id="country" class="form-control multiple-select" >
                    <option value="">-- Select Country --</option>
                    <?php
                     for ($ic=0; $ic <count($qcountries) ; $ic++) { 
                      if(in_array($qcountries[$ic]['country_id'], $cIdsArray)){
                     ?>
                     <option value="<?php echo $qcountries[$ic]['country_id']; ?>"><?php echo $qcountries[$ic]['name']; ?></option>
                   <?php } } ?>
                  </select>
                </div>
                <div class="col-sm-2">
                  <button type="submit" id="CommonSliderCountryBtn" class="btn btn-success" name="CommonSliderCountryBtn"><i class="fa fa-check-square-o"></i> Add</button>
                </div>
                
              </div>
            </form>


            <hr>

            <form id="CommonSliderFrm2" action="controller/sliderController.php" method="post" enctype="multipart/form-data">
            
             <?php 
              $edit_id =  $id;
               ?>
                 <input type="hidden" name="app_slider_id" value="<?php echo $edit_id;?>">
             <div class="form-group row">
                <label for="input-10" class="col-sm-6 col-form-label">Add New Company By State For This Banner</label>
                </div>
               
              <div class="form-group row">
                <div class="col-sm-4">
                  <select type="text" required="" id="country_id" onchange="getStates();" class="form-control single-select" name="country_id">
                    <option value="">-- Select --</option>
                    <?php 
                     for ($ic=0; $ic <count($qcountries) ; $ic++) { 
                         if(in_array($qcountries[$ic]['country_id'], $cIdsArray)){
                     ?>
                      <option value="<?php echo $qcountries[$ic]['country_id'];?>"><?php echo $qcountries[$ic]['name'];?></option>
                      <?php } }?>
                    </select>

                </div>
                <div class="col-sm-4">
                 
                  <select multiple="multiple" name="state_id[]" id="state_id" class="form-control multiple-select" >
                    <option value="">-- Select State --</option>
                   
                  </select>
                </div>
                <div class="col-sm-2">
                  <button type="submit" id="CommonSliderStateBtn" class="btn btn-success" name="CommonSliderStateBtn"><i class="fa fa-check-square-o"></i> Add</button>
                </div>
                
              </div>
            </form>


            <hr>

            <form id="CommonSliderFrm3" action="controller/sliderController.php" method="post" enctype="multipart/form-data">
            
             <?php 
              $edit_id =  $id;
               ?>
                 <input type="hidden" name="app_slider_id" value="<?php echo $edit_id;?>">
             <div class="form-group row">
                <label for="input-10" class="col-sm-6 col-form-label">Add New Comapny By City For This Banner</label>
                </div>
               
              <div class="form-group row">
                 <div class="col-sm-3">
                    <select type="text" required="" id="countryId" onchange="getStatesNew();" class="form-control single-select" name="country_id">
                      <option value="">-- Select --</option>
                      <?php 
                       for ($ic=0; $ic <count($qcountries) ; $ic++) { 
                         if(in_array($qcountries[$ic]['country_id'], $cIdsArray)){
                       ?>
                        <option value="<?php echo $qcountries[$ic]['country_id'];?>"><?php echo $qcountries[$ic]['name'];?></option>
                        <?php } }?>
                      </select>

                </div>
                <div class="col-md-3">
                   <select name="stateId" id="stateId" onchange="getCityNew();" class="form-control multiple-select" >
                    <option value="">-- Select City --</option>
                   </select>
                </div>
                <div class="col-sm-3">
               
                  <select multiple="multiple" name="city_id[]" id="cityId" class="form-control multiple-select" >
                    <option value="">-- Select City --</option>
                  
                  </select>
                </div>
                <div class="col-sm-2">
                  <button type="submit" id="CommonSliderProviderCityBtn" class="btn btn-success" name="CommonSliderCityBtn"><i class="fa fa-check-square-o"></i> Add</button>
                </div>
                
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-header">
            <a href="javascript:void(0)" onclick="DeleteAll('deleteCommonSliderSettings');" class="btn btn-danger btn-sm waves-effect waves-light float-right"><i class="fa fa-trash-o fa-lg"></i> Delete </a>
          </div>
          <div class="card-body">
            <div class="table-responsive">
            <table id="checkAllwithAllDelete" class="table table-bordered">
              <thead>
                  <tr>
                    <th class="text-center">
                      <input type="checkbox" id="checkUncheckAllDelete" value="CheckAll"  />
                    </th>
                    <th>#</th>
                    <th>Id</th>
                    <th>Company</th>
                    <th>City</th>
                    <th>Status</th>
                    <th>Delete</th>
                  </tr>
              </thead>
              <tbody>
                 <?php 
                    $sliderData = array();
                    $i=1;
                    $q = $d->select("society_master,app_common_slider_master" ,"app_common_slider_master.society_id=society_master.society_id AND  app_common_slider_master.app_slider_id='$id' $countryAppendQuerySociety","order by society_master.society_id  DESC");
                    while ($data=mysqli_fetch_array($q)) {
                      $sliderData[] = $data;
                    }
                    for ($j=0; $j < count($sliderData) ; $j++) { 
                     ?>
                  <tr>
                    <td class='text-center'>
                      <input type="checkbox" class="common_slider_delete"  value="<?php echo $sliderData[$j]['app_common_slider_id']; ?>" id="common_<?php echo $sliderData[$j]['app_common_slider_id']; ?>">
                    </td>
                    <td><?php echo $i++; ?></td>
                    <td><?php echo ''.$d->short_app_name().'_'.$sliderData[$j]['society_id']; ?></td>
                    <td><?php echo $sliderData[$j]['society_name']; ?></td>
                    <td><?php echo $sliderData[$j]['city_name']; ?></td>
                   
                    <td>
                        <!-- jainish start--- -->
                        <?php
                        $buttonClass = ($sliderData[$j]['status'] == "0") ? 'btn-success-new' : 'btn-danger';
                        $buttonCondition = ($sliderData[$j]['status'] == "0") ? 'Active' : 'Deactive';
                        $status = ($sliderData[$j]['status'] == "0") ? 'SliderSocietyDeactive' : 'SliderSocietyActive';
                        $newStatus = ($sliderData[$j]['status'] == "0") ? 'SliderSocietyActive' : 'SliderSocietyDeactive';
                        $newStatusVal = ($sliderData[$j]['status'] == "0") ? '1' : '0';
                        $statusValue = ($sliderData[$j]['status'] == "0") ? '0' : '1';
                        ?>
                        <input type="button" class="btn btn-sm pl-1 pr-1 w-50 <?php echo $buttonClass ?>" id="<?php echo 'app_common_slider_id_' . $sliderData[$j]['app_common_slider_id']; ?>" onclick="changeStatusNew('<?php echo $sliderData[$j]['app_common_slider_id']; ?>','<?php echo $status; ?>','<?php echo $newStatus; ?>','<?php echo $statusValue; ?>','<?php echo $newStatusVal; ?>','<?php echo 'app_common_slider_id_' . $sliderData[$j]['app_common_slider_id']; ?>');" data-size="small" value="<?php echo $buttonCondition ?>" />
                        <!-- jainish end--- -->
                    </td>
                    
                    <td>
                      <form action="controller/sliderController.php" method="post" >
                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                        <input type="hidden" name="society_id_delete_from_slider" value="<?php echo $sliderData[$j]['society_id']; ?>">
                        <button type="submit" class="form-btn btn btn-sm btn-danger waves-effect waves-light m-1" title="Delete"> <i class="fa fa-trash-o"></i> </button>
                        
                      </form>
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
</div> 

<?php
for ($md=0; $md < count($sliderData) ; $md++) { ?>
  <input style="display: none" class="multiDelteCheckbox" value="<?php echo $sliderData[$md]['app_common_slider_id']; ?>" type="checkbox" id="common_<?php echo $sliderData[$md]['app_common_slider_id']; ?>_hidden" name="media_select[]">
<?php } ?>

<script src="assets/js/jquery.min.js"></script>
<script type="text/javascript">
 
  $(".common_slider_delete").click(function(){ 
    var elmId = $(this). attr("id");
    if(this.checked) {
      $('#'+elmId+'_hidden').prop('checked', true);
    } else {
      $('#'+elmId+'_hidden').prop('checked', false);
    }
  });
</script>
