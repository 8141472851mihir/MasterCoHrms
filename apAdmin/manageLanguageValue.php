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

         <a href="keyValue" class="btn btn-sm btn-primary waves-effect waves-light"><i class="fa fa-plus mr-1"></i>Add New Language Key Value</a>

       </div>
     </div>
   </div>
   <!-- End Breadcrumb-->
   <form id="personal-info" method="get" id="form" action="">
    <input type="hidden" name="language_id" value="<?php echo $language_id; ?>">
    <div class="row pt-2 pb-2">
      <div class="col-sm-6">
        <input type="text" required="" value="<?php if (isset($key_name)) { echo htmlspecialchars($key_name, ENT_QUOTES, 'UTF-8');} ?>" name="key_name" placeholder="Enter Key Name" class="form-control">
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
         <form id="editLanguageValueKeyFrm" action="controller/languageKeyValueController.php" method="post">
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
              $qCC=$d->select("language_key_master","key_name='$key_name'","");
              if (mysqli_num_rows($qCC)>0) {
                $keyData=mysqli_fetch_array($qCC);
                $language_key_id = (int)$keyData['language_key_id'];
                
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
                    "language_key_value_master",
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
                    <td><?php echo $keyData['key_name'];?> </td>
                    <td><?php echo $row['language_name'];?>(<?php echo $row['language_name_1']; ?>)</td>
                    <td>
                      <?php if ($keyData['key_type']==0) {
                       ?>
                       <input type="hidden" name="language_id[]" value="<?php if(isset($language_id)) {  echo $language_id; } ?>">
                       <input type="hidden" name="language_key_id[]" value="<?php echo $language_key_id; ?>">
                       <input type="hidden" name="key_value_id[]" value="<?php if(isset($valueData['key_value_id'])) {  echo $valueData['key_value_id']; } ?>">
                       <input required="" class="form-control" type="text" class="value_name" id="value_name" value="<?php echo htmlspecialchars($valueData['value_name'], ENT_QUOTES, 'UTF-8'); ?>" name="value_name[]">
                     <?php } else { 
                      for ($ic=0; $ic < $keyData['no_of_key'] ; $ic++) {  ?>
                        <input type="hidden" name="language_id[]" value="<?php if(isset($language_id)) {  echo $language_id; } ?>">
                        <input type="hidden" name="language_key_id[]" value="<?php echo $language_key_id; ?>">
                        <input type="hidden" name="key_value_id[]" value="<?php if(isset($editArayKeyId[$ic])) {  echo $editArayKeyId[$ic]; } ?>">
                        <input required="" class="form-control" type="text" class="value_name" id="value_name" value="<?php echo htmlspecialchars($editArayValue[$ic] ?? '', ENT_QUOTES, 'UTF-8'); ?>" name="value_name[]">

                      <?php } } ?>
                    </td>
                  </tr>         
                <?php } } ?>  
              </tbody>
            </table>
            <div class="text-center">
              <input type="hidden" name="key_name" value="<?php echo htmlspecialchars($key_name, ENT_QUOTES, 'UTF-8'); ?>">
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
                <table id="table_language_values" class="table table-bordered">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th >Language Name</th>
                      <th>Language Key</th>
                      <th>Language Key Value</th>
                      <th>updated by</th>
                      <th>updated date</th>
                      <th>Action</th>
                    </tr>
                  </thead>
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

<script src="assets/js/jquery.min.js"></script>
<script type="text/javascript">
  window.onpageshow = function(event) {
    if (event.persisted) {
      location.reload();
    }
  };
  $(document).ready(function() {
    $.fn.dataTable.ext.errMode = 'none';
    var table_language_values = $('#table_language_values').DataTable({
      lengthChange: false,
      responsive: true,
      autoWidth: false,
      searching: true,
      ordering: true,
      processing: true,
      destroy: true,
      stateSave:true,
      language: {
        lengthMenu: "_MENU_",
        processing: "",
        emptyTable: "NO DATA FOUND !"
      },
      lengthMenu: [[10, 20, 30, 50], [10, 20, 30, 50]],
      pageLength: 10,
      'ajax': {
        url:'./ajaxLanguage.php',
        type: "POST",
        data: function (d) {
          d.csrf = "<?php echo $_SESSION["token"]; ?>";
          d.languageValues = "languageValues";
          d.language_id_temp = "<?php echo $language_id_temp; ?>";
        }
        ,
        'error': function(xhr, error, thrown) {
              // console.log('Error:', error, thrown);
          $('#table_language_keys').DataTable().clear().draw();
          $('#table_language_keys tbody').html('<tr><td colspan="12" class="text-center">No data found</td></tr>');
        }
      },
      columns: [
        { "data": "id", "width": "50px" },
        { "data": "language_name", "width": "120px" },
        { "data": "key_name" },
        { "data": "value_name" },
        { "data": "updated_by", "width": "80px" },
        { "data": "updated_date", "width": "80px" },
        { "data": "action", "width": "80px" }
      ],
      createdRow: function(row, data, dataIndex) {
        $('td:eq(0),td:eq(3),td:eq(4)', row).addClass('tableWidth');
      },
      'error': function(settings, helpPage, message) {
          // console.log("DataTables error:", message);
      }
    });
  });
</script>
<script type="text/javascript">
 function clickable(id) {
  var inputField = document.getElementById("copyText_"+id);
    // Create a temporary textarea element
  var tempInput = document.createElement('textarea');
  tempInput.value = inputField.value;
  document.body.appendChild(tempInput);
    // Select the value of the textarea
  tempInput.select();
    // Copy the selected value to the clipboard
  document.execCommand('copy');
    // Remove the temporary textarea
  document.body.removeChild(tempInput);


  Lobibox.notify('success', {
    pauseDelayOnHover: true,
    continueDelayOnInactiveTab: false,
    position: 'top right',
    icon: 'fa fa-check-circle',
    msg: "Copied the text successfully "
  });

};


//dharti 9-1-2025
document.addEventListener('click', function (e) {
  if (e.target.closest('.deleteButton')) {
    const button = e.target.closest('.deleteButton');
    const feedbackId = button.getAttribute('data_key_value_id');
    deleteFeedback(feedbackId);
  }
});

function deleteFeedback(key_value_id) {
  swal({
    title: "Are you sure?",
    text: "This action will permanently delete the feedback.",
    icon: "warning",
    buttons: ['Cancel', 'Yes, delete it!'],
    dangerMode: true,
  }).then((willDelete) => {
    if (willDelete) {
      document.getElementById(`delete-form-${key_value_id}`).submit();
    }
  });
}

//dharti 9-1-2025 end

</script>