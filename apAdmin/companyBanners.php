<?php
extract(array_map("test_input" , $_REQUEST));
$society = $d->selectArray("society_master","society_id='$id'");
error_reporting(0);
?>

<link rel="stylesheet" href="assets/plugins/summernote/dist/summernote-bs4.css"/>
<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-8">
        <h4 class="page-title">Banners - <?php echo $society['society_name'] ?> </h4>
        
      </div>
      <div class="col-sm-4">
        <div class="btn-group float-right">
          <a href="javascript:void(0)" data-toggle="modal" data-target="#getSliderListModal" onclick="getSliders()" class="btn btn-sm btn-primary">Assign</a>
          <a href="javascript:void(0)" onclick="DeleteAll('deleteCommonSliderSettings');" class="btn btn-danger btn-sm waves-effect waves-light float-right"><i class="fa fa-trash-o fa-lg"></i> Delete </a>
        </div>
      </div>
    </div>
    <!-- End Breadcrumb-->
  </div>
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table id="checkAllwithAllDelete" class="table table-bordered">
              <thead>
                <tr>
                  <th class="text-center">
                    <input type="checkbox" id="checkUncheckAllDelete" value="CheckAll"  />
                  </th>
                  <th>#</th>
                  <th>Banner</th>
                  <th>Youtube URL</th>
                  <th>Page URL</th>
                  <th>Mobile</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                  $i=1;
                  $bannerData = array();
                  $q=$d->select("app_slider_master,app_common_slider_master,society_master","society_master.society_id=app_common_slider_master.society_id AND app_slider_master.app_slider_id=app_common_slider_master.app_slider_id AND app_common_slider_master.society_id='$id' $countryAppendQuerySociety","");
                  while ($data=mysqli_fetch_array($q)) {
                    $bannerData[] = $data;
                  }
                  for ($j=0; $j <count($bannerData) ; $j++) { 
                ?>
                <tr>
                  <td class='text-center'>
                    <input type="checkbox" class="sp_society_delete_select"  value="<?php echo $bannerData[$j]['app_common_slider_id']; ?>" id="banner_<?php echo $bannerData[$j]['app_common_slider_id']; ?>">
                  </td>
                  <td><?php echo $i++; ?></td>
                  <td><img src="../img/sliders/<?php echo $bannerData[$j]['slider_image_name']; ?>" class="lightbox-thumb img-thumbnail" style="width:250px;height:150px;"></td>
                  <td class="tableWidth"><?php echo $bannerData[$j]['youtube_url'];?></td>
                  <td class="tableWidth"><?php echo $bannerData[$j]['page_url']; ?></td>
                  <td><?php echo $bannerData[$j]['page_mobile']; ?></td>
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
<?php
for ($md=0; $md < count($bannerData) ; $md++) { ?>
  <input style="display: none;" class="multiDelteCheckbox" value="<?php echo $bannerData[$md]['app_common_slider_id']; ?>" type="checkbox" id="banner_<?php echo $bannerData[$md]['app_common_slider_id']; ?>_hidden" name="media_select[]">
<?php } ?>

<script type="text/javascript">
 
  $(".sp_society_delete_select").click(function(){ 
    var elmId = $(this). attr("id");
    if(this.checked) {
      $('#'+elmId+'_hidden').prop('checked', true);
    } else {
      $('#'+elmId+'_hidden').prop('checked', false);
    }
  });
</script>

<script type="text/javascript">
  function getSliders() {
    $.ajax({
      url: "getSliderList.php",
      cache: false,
      type: "POST",
      data: {society_id:'<?php echo $id; ?>',slider_type:'slider_type'},
      success: function(response){
        $('#sliderListDiv').html(response);
      }
    });
  }
</script>


<div class="modal fade" id="getSliderListModal">
  <div class="modal-dialog">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white">Banners</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="sliderListDiv">
        
      </div>
      
    </div>
  </div>
</div>