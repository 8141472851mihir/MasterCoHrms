<?php $videoguide=TRUE ?>
<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-9">
        <h4 class="page-title">Security App Videos</h4>
        
      </div>
       <div class="col-sm-3">
       <div class="btn-group float-sm-right">
        <a href="#" class="btn btn-primary waves-effect btn-sm waves-light" data-toggle="modal" data-target="#add-video" data-backdrop="static" data-keyboard="false"><i class="fa fa-plus mr-1"></i> Add New</a>
       
      </div>
     </div>
    </div>
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <table id="example" class="table table-bordered">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Video Title</th>
                    <th>Description</th>
                    <th>Language</th>
                    <th>Thumbnail</th>
                    <th>Video</th>
                    <th>Added Date</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                    $i=1;
                    $q = $d->select("video_guide","","order by  video_id  DESC");
                    while ($data=mysqli_fetch_array($q)) {
                      extract($data);
                  ?>
                    <tr>
                      <td><?php echo $i++; ?></td>
                        <td><?php echo $video_title; ?></td>
                        <td><?php echo $video_description; ?></td>
                        <?php 
                        if($language_id=='E'){ $language = 'English'; }else if($language_id=='H'){ $language = 'Hindi'; }else{ $language = 'Gujarati'; }
                        ?>
                        <td><?php echo $language; ?></td>
                        <td><?php echo '<img src="img/videos/thumbnails/'.$video_thumbnail.'" width="70">'; ?></td>
                        <td><?php echo '<video width="200" controls ><source src="img/videos/'.$video_file.'" type="video/mp4"></video>'; ?></td>
                        <td><?php echo date('d-M-Y h:i A', strtotime($uploaded_date)); ?></td>
                        <td><a href="javascript:void();" onclick="deleteVideo(<?=$video_id?>);" class="btn btn-sm btn-danger shadow-primary">Delete</a><a href="javascript:void();" class="btn btn-sm btn-primary shadow-primary ml-3" data-toggle="modal" data-target="#edit-video" data-backdrop="static" data-keyboard="false" onClick="editVideo('<?=$video_id?>','<?=$language_id?>','<?=$video_title?>','<?=$video_description?>','<?=$video_thumbnail?>','<?=$video_file?>')">Edit</a></td>
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
<div class="modal fade" id="add-video">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Video</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="videoAddForm" method="post" action="controller/videoController.php" enctype="multipart/form-data" novalidate="novalidate">
          <div class="form-group row">
            <label for="input-12" class="col-sm-2 col-form-label">Language <span class="required">*</span> </label>
            <div class="col-sm-10">
                  <select  type="text" required="" id="primary_language_id" class="form-control single-select" name="language_id" >
                    <option value="">-- Select--</option>
                    <?php     
                    $ql=$d->select("language_master","active_status=0 ","");
                     while($ldata=mysqli_fetch_array($ql)) { ?>
                      <option value="<?php echo $ldata['language_id'];?>"><?php echo $ldata['language_name'].'-'.$ldata['language_name_1'];?></option>
                    <?php } ?>

                  </select>
            </div>
          </div>
          <div class="form-group row">
            <label for="input-12" class="col-sm-2 col-form-label">Video Title <span class="required">*</span> </label>
            <div class="col-sm-10">
              <input type="text" class="form-control" name="title1" id="title1" required="">
              <div class="error title1"></div>
            </div>
          </div>
          <div class="form-group row">
            <label for="input-12" class="col-sm-2 col-form-label">Video Description <span class="required">*</span> </label>
            <div class="col-sm-10">
              <textarea class="form-control" rows="5" name="about" id="about" required=""></textarea>
              <div class="error about"></div>
            </div>
          </div>
          <div class="form-group row">
            <label for="input-12" class="col-sm-2 col-form-label">Video Thumbnail <span class="required">*</span></label>
            <div class="col-sm-10">
              <input class="form-control-file border" required="" type="file" name="thumbnail" id="thumbnail" accept="image/*">
              <div class="error thumbnail"></div>
            </div>
          </div>
          <div class="form-group row">
            <label for="input-12" class="col-sm-2 col-form-label">Video File <span class="required">*</span></label>
            <div class="col-sm-10">
              <input class="form-control-file border" required="" type="file" name="file1" id="file1" accept="video/mp4,video/x-m4v,video/*">
              <div class="error photo"></div>
            </div>
          </div>
          <div class="form-footer text-center">
            <button type="submit" class="btn btn-success" name="videoUpload" id="videoUpload"><i class="fa fa-check-square-o"></i> Upload</button>
           
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="edit-video">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Video</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="videoEditForm" method="post" action="controller/videoController.php" enctype="multipart/form-data" novalidate="novalidate">
          <div class="form-group row">
            <label for="input-12" class="col-sm-2 col-form-label">Language <span class="required">*</span> </label>
            <div class="col-sm-10">
              <select  type="text" required="" id="primary_language_id" class="form-control single-select" name="language_id" >
                <option value="">-- Select--</option>
                <?php     
                $ql=$d->select("language_master","active_status=0 ","");
                 while($ldata=mysqli_fetch_array($ql)) { ?>
                  <option value="<?php echo $ldata['language_id'];?>"><?php echo $ldata['language_name'].'-'.$ldata['language_name_1'];?></option>
                <?php } ?>

              </select>
            </div>
          </div>
          <div class="form-group row">
            <label for="input-12" class="col-sm-2 col-form-label">Video Title <span class="required">*</span> </label>
            <div class="col-sm-10">
              <input type="hidden" name="videoid" id="videoid">
              <input type="text" class="form-control" name="title2" id="title2" required="">
            </div>
          </div>
          <div class="form-group row">
            <label for="input-12" class="col-sm-2 col-form-label">Video Description <span class="required">*</span> </label>
            <div class="col-sm-10">
              <textarea class="form-control" rows="5" name="about1" id="about1" required=""></textarea>
            </div>
          </div>
          <div class="form-group row">
            <label for="input-12" class="col-sm-2 col-form-label">Video Thumbnail</label>
            <div class="col-sm-6">
              <input class="form-control-file border" type="file" name="thumbnail1" id="thumbnail1" accept="image/*">
            </div>
            <div class="col-sm-4">
              <img src="" width="50" id="thumbfile">
            </div>
          </div>
          <div class="form-group row">
            <label for="input-12" class="col-sm-2 col-form-label">Video File</label>
            <div class="col-sm-6">
              <input class="form-control-file border" type="file" name="file2" id="file2" accept="video/mp4,video/x-m4v,video/*">
              <div class="error photo"></div>
            </div>
            <div class="col-sm-4">
              <video width="200" controls ><source id="vidfile" src="" type="video/mp4"></video>
            </div>
          </div>
          <div class="form-footer text-center">
            <button type="submit" class="btn btn-success" name="videoEditUpload" id="videoEditUpload"><i class="fa fa-check-square-o"></i> Update</button>
           
          </div>
        </form>
      </div>
    </div>
  </div>
</div>