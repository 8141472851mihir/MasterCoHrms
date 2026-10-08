<?php 
extract($_GET);
if (!isset($language_id)) {
  $language_id = 1;
  $language_id_temp = 1;
} else {
   $language_id = (int) $language_id;
   $language_id_temp = (int) $language_id;
} 

?>
<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-4">
        <h4 class="page-title">Language Key Value List</h4>
         <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="welcome">Home</a></li>
          <li class="breadcrumb-item active" aria-current="page">Language Key Value List</li>
        </ol>
      </div>
      <div class="col-sm-5">
          <form method="get" id="form" action="">
             
                  <select onchange="this.form.submit()" class="form-control single-select" name="language_id" id="select">
                    <option value=""> Select language</option>
                    <?php
                    $data=$d->select("language_master","","");
                    while ($row=mysqli_fetch_array($data)) { ?>
                      <option <?php if(isset($language_id) && $language_id == $row['language_id']){echo "selected";} ?> value="<?php echo $row['language_id']; ?>"><?php echo $row['language_name']; ?> (<?php echo $row['language_name_1']; ?>)</option>
                    <?php } ?>
                  </select>
               
              </form>
      </div>
      <div class="col-sm-3"> 
        <div class="btn-group float-sm-right">

         <a href="keyValueGuard" class="btn btn-sm btn-primary waves-effect waves-light"><i class="fa fa-plus mr-1"></i>Add New Language Key Value</a>

        </div>
      </div>
    </div>
    <!-- End Breadcrumb-->
     <form id="personal-info" method="get" id="form" action="">
      <input type="hidden" name="language_id" value="<?php echo $language_id; ?>">
    <div class="row pt-2 pb-2">
      <div class="col-sm-6">
        <input type="text" required="" value="<?php if (isset($key_name)) { echo $key_name;} ?>" name="key_name" placeholder="Enter Key Name" class="form-control">
      </div>
      <div class="col-sm-6">
        <input class="btn btn-primary" type="submit" name="" value="Search">
      </div>
    </div>
    </form>
    <?php if (isset($key_name) && $key_name!="") { ?>
    <div class="card">
      <div class="card-body"> 
        <div class="table-responsive">
       <form id="editLanguageValueKeyFrm" action="controller/languageKeyValueControllerGuard.php" method="post">
          <table id="" class="table table-bordered data_table">
            <thead>
              <tr>
                <th>ID</th>
                <th>key_name</th>
                <th>Language</th>
                <th>EDIT</th>
              </tr>
            </thead>
            <tbody>
              <?php
               $qCC=$d->select("language_key_master_gatekeeper","key_name='$key_name'","");
              if (mysqli_num_rows($qCC)>0) {
              $keyData=mysqli_fetch_array($qCC);
              $language_key_id = (int)$keyData['language_key_id'];
              // $q=$d->select("language_key_master_gatekeeper,language_key_value_master_gatekeeper,language_master","language_master.language_id=language_key_value_master_gatekeeper.language_id AND language_key_master_gatekeeper.language_key_id=language_key_value_master_gatekeeper.language_key_id  AND language_key_master_gatekeeper.key_name='$key_name'","");
              $q=$d->select("language_master","","");
              $langRows = [];
              $langIds = [];
              while($row=mysqli_fetch_array($q)) {
                $langRows[] = $row;
                $langIds[] = (int)$row['language_id'];
              }

              // Prefetch all language values for this key (avoid N+1)
              $valuesByLangId = [];
              if (!empty($langIds)) {
                $langIdsIn = implode(',', array_map('intval', $langIds));
                $qValueAll = $d->select(
                  "language_key_value_master_gatekeeper",
                  "language_key_id='$language_key_id' AND language_id IN ($langIdsIn)",
                  ""
                );
                while ($valueData = mysqli_fetch_array($qValueAll)) {
                  $lid = (int)$valueData['language_id'];
                  if (!isset($valuesByLangId[$lid])) {
                    $valuesByLangId[$lid] = ['values' => [], 'ids' => []];
                  }
                  $valuesByLangId[$lid]['values'][] = $valueData['value_name'];
                  $valuesByLangId[$lid]['ids'][] = $valueData['key_value_id'];
                }
              }

              $i4=1;
              foreach ($langRows as $row) {
                $language_id = $row['language_id'];
                $lid = (int)$language_id;
                $editArayValue = $valuesByLangId[$lid]['values'] ?? [];
                $editArayKeyId = $valuesByLangId[$lid]['ids'] ?? [];
                $valueData = [
                  'value_name' => $editArayValue[0] ?? '',
                  'key_value_id' => $editArayKeyId[0] ?? null,
                ];

                ?>
                
                <tr>
                  <td><?php echo $i4++;?></td>
                  <td><?php echo $keyData['key_name'];?></td>
                  <td><?php echo $row['language_name'];?> (<?php echo $row['language_name_1']; ?>)</td>
                  <td>
                     <?php if ($keyData['key_type']==0) { ?>
                    <input type="hidden" name="language_id[]" value="<?php if(isset($language_id)) {  echo $language_id; } ?>">
                    <input type="hidden" name="language_key_id[]" value="<?php echo $language_key_id; ?>">
                    <input type="hidden" name="key_value_id[]" value="<?php if(isset($valueData['key_value_id'])) {  echo $valueData['key_value_id']; } ?>">
                    <input required="" class="form-control" type="text" class="value_name" id="value_name" value="<?php  echo $valueData['value_name'];?>" name="value_name[]">
                    <?php } else { 
                    for ($ic=0; $ic < $keyData['no_of_key'] ; $ic++) {  ?>
                      <input type="hidden" name="language_id[]" value="<?php if(isset($language_id)) {  echo $language_id; } ?>">
                      <input type="hidden" name="language_key_id[]" value="<?php echo $language_key_id; ?>">
                      <input type="hidden" name="key_value_id[]" value="<?php if(isset($editArayKeyId[$ic])) {  echo $editArayKeyId[$ic]; } ?>">
                      <input required="" class="form-control" type="text" class="value_name" id="value_name" value="<?php  echo $editArayValue[$ic] ?? '';?>" name="value_name[]">
                    <?php } }?>
                  </td>
                </tr>         
              <?php } } ?>  
            </tbody>
          </table>
          <div class="text-center">
            <input type="hidden" name="key_name" value="<?php echo $key_name; ?>">
             <input type="hidden" name="language_id_selected" value="<?php echo $language_id; ?>">
            <input type="hidden" name="AddLanguageKeyValueAll" value="AddLanguageKeyValueAll">
            <button type="submit" class="btn btn-primary" name="submit"  value="submit"><i class="fa fa-edit">SUBMIT</i>
            </button>
          </div>
        </form>
      </div></div></div>
    <?php } ?>
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <!-- <div class="card-header"><i class="fa fa-table"></i> Data Exporting</div> -->
          <div class="card-body">
            <div class="table-responsive">
              <table id="example" class="table table-bordered">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Language Name</th>
                    <th>Language Key</th>
                    <th>Language Key Value</th>
                    <th>Action</th>
                  </tr>
                  </thead>
                   
                  <tbody>
                    <?php
                    $i = 1 ;
                    $language_id= $language_id_temp;
                    
                     $languageKey=$d->select("language_key_master_gatekeeper","","");
                     $languageKey_array = array();
                       while ($languageKey_data=mysqli_fetch_array($languageKey)) {
                        $languageKey_array[$languageKey_data['language_key_id']] = $languageKey_data['key_name'];
                        }

                       $language_master=$d->select("language_master","","");
                       $language_master_data_array = array();
                       while ($language_master_data=mysqli_fetch_array($language_master)) {
                        $language_master_data_array[$language_master_data['language_id']] = $language_master_data['language_name'].'-'.$language_master_data['language_name_1'];
                        }


                     $q=$d->select("language_key_value_master_gatekeeper","language_id='$language_id'","");
                    while ($row=mysqli_fetch_array($q)) {

                      $language_key_id = $row['language_key_id'];
                      $key_name= $languageKey_array[$language_key_id];


  $language_id = $row['language_id'];
  $language_name = $language_master_data_array[$language_id];

                      ?>
                      <tr>
                        <td><?php echo $i; $i++;?></td>
                         <td><?php echo $language_name;?></td> 
                        <td><?php echo $key_name;?></td> 
                        <td><?php echo $row['value_name'];?></td> 

                     
                      <td>
                        <div style="display: inline-block;">

                          <form action="edit_language_key_value_guard" method="post" >
                            <input type="hidden" name="key_value_id" value="<?=$row['key_value_id']?>">
                             

                            <button type="submit" class="btn btn-sm btn-primary  " name="edit"  value="edit"><i class="fa fa-edit"></i></button>
                          </form>
                        </div> 
                        <div style="display: inline-block;" >
                          <form action="controller/languageKeyValueControllerGuard.php" method="post">
                            <input type="hidden" name="delete_key_value_id" value="<?=$row['key_value_id']?>">
                            <button type="submit" class="btn btn-sm btn-danger form-btn" name="delete"  value="delete"><i class="fa fa-trash"></i></button>
                          </form>

                        </div>

                      
                    </td>
                  </tr>
                <?php } ?>

                <!-------------------end select query-------------------------------->
              </tbody>
            </table>

          </div>
        </div>
      </div>
    </div>
  </div><!-- End Row-->
</div>
<!-- End container-fluid-->

</div>

</div><!--End wrapper-->


