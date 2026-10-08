<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-6">
        <h4 class="page-title">Language Key List  </h4>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="welcome">Home</a></li>
          <li class="breadcrumb-item active" aria-current="page">Language Key List</li>
        </ol>
      </div>
      <div class="col-sm-6 text-sm-right"> 
        <div class="btn-group mb-2">
         <a href="languageKey" class="btn btn-sm btn-primary waves-effect waves-light"><i class="fa fa-plus mr-1"></i>Add New Language Key</a>
       </div>
        <div class="btn-group mb-2">
         <button type="button" class="btn btn-sm btn-warning waves-effect waves-light" data-toggle="modal" data-target="#bulkUploadLanguageKeysModal"><i class="fa fa-upload mr-1"></i>Bulk Upload Keys</button>
       </div>
        <div class="btn-group mb-2">
         <a href="companyAnalyticsGetData?getDataType=13" class="btn btn-sm btn-success waves-effect waves-light"><i class="fa fa-sync mr-1"></i>Sync Custom Languages</a>
       </div>
     </div>
   </div>
   <!-- End Breadcrumb-->
   <?php
   $bulkLanguageKeyResults = $_SESSION['bulk_language_key_results'] ?? null;
   $bulkFailedLanguageKeysJson = $_SESSION['bulk_language_key_failed_json'] ?? [];
   if (!empty($bulkLanguageKeyResults) && !empty($bulkLanguageKeyResults['results'])) {
     $bulkSuccessCount = (int) ($bulkLanguageKeyResults['success_count'] ?? 0);
     $bulkFailCount = (int) ($bulkLanguageKeyResults['fail_count'] ?? 0);
     $bulkTotalCount = (int) ($bulkLanguageKeyResults['total_count'] ?? count($bulkLanguageKeyResults['results']));
     $bulkFailedJsonText = json_encode($bulkFailedLanguageKeysJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
   ?>
   <div class="row" id="bulkUploadResults">
     <div class="col-lg-12">
       <div class="card border-<?php echo $bulkFailCount > 0 ? 'warning' : 'success'; ?>">
         <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
           <div>
             <h5 class="mb-1">Bulk Upload Review</h5>
             <small class="text-muted">
               <?php echo htmlspecialchars($bulkLanguageKeyResults['summary'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
               <?php if (!empty($bulkLanguageKeyResults['generated_at'])) { ?>
                 · <?php echo htmlspecialchars($bulkLanguageKeyResults['generated_at'], ENT_QUOTES, 'UTF-8'); ?>
               <?php } ?>
             </small>
           </div>
           <div class="mt-2 mt-md-0">
             <span class="badge badge-success mr-1">Success: <?php echo $bulkSuccessCount; ?></span>
             <span class="badge badge-danger mr-1">Failed: <?php echo $bulkFailCount; ?></span>
             <span class="badge badge-secondary">Total: <?php echo $bulkTotalCount; ?></span>
           </div>
         </div>
         <div class="card-body">
           <?php if ($bulkFailCount > 0) { ?>
           <div class="mb-3 d-flex flex-wrap">
             <a href="controller/languagekeyController.php?download_failed_language_keys=1" class="btn btn-sm btn-danger mr-2 mb-2">
               <i class="fa fa-download mr-1"></i>Download Failed Keys JSON
             </a>
             <button type="button" class="btn btn-sm btn-outline-secondary mr-2 mb-2" id="copyFailedLanguageKeysBtn">
               <i class="fa fa-copy mr-1"></i>Copy Failed Keys JSON
             </button>
             <button type="button" class="btn btn-sm btn-outline-primary mb-2" data-toggle="collapse" data-target="#failedLanguageKeysJsonPreview">
               <i class="fa fa-eye mr-1"></i>View Failed Keys JSON
             </button>
           </div>
           <div class="collapse mb-3" id="failedLanguageKeysJsonPreview">
             <textarea id="failedLanguageKeysJsonText" class="form-control" rows="8" readonly><?php echo htmlspecialchars($bulkFailedJsonText, ENT_QUOTES, 'UTF-8'); ?></textarea>
           </div>
           <?php } ?>

           <div class="table-responsive">
             <table class="table table-bordered table-sm mb-0" id="bulkUploadResultsTable">
               <thead>
                 <tr>
                   <th style="width:40px;">#</th>
                   <th>Key Name</th>
                   <th>English Value</th>
                   <th style="width:90px;">Status</th>
                   <th>Response / Reason</th>
                   <th>Translations</th>
                 </tr>
               </thead>
               <tbody>
                 <?php
                 $bulkRowIndex = 1;
                 foreach ($bulkLanguageKeyResults['results'] as $bulkResultRow) {
                   $isSuccess = (($bulkResultRow['status'] ?? '') === 'success');
                   $translations = $bulkResultRow['translations'] ?? [];
                   $translationBits = [];
                   if (is_array($translations)) {
                     foreach ($translations as $langName => $langValue) {
                       $translationBits[] = htmlspecialchars((string) $langName, ENT_QUOTES, 'UTF-8') . ': '
                         . htmlspecialchars((string) $langValue, ENT_QUOTES, 'UTF-8');
                     }
                   }
                 ?>
                 <tr class="<?php echo $isSuccess ? 'table-success' : 'table-danger'; ?>">
                   <td><?php echo $bulkRowIndex++; ?></td>
                   <td><code><?php echo htmlspecialchars($bulkResultRow['key_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></code></td>
                   <td><?php echo htmlspecialchars($bulkResultRow['english'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                   <td>
                     <?php if ($isSuccess) { ?>
                       <span class="badge badge-success">Success</span>
                     <?php } else { ?>
                       <span class="badge badge-danger">Failed</span>
                     <?php } ?>
                   </td>
                   <td><?php echo htmlspecialchars($bulkResultRow['reason'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                   <td style="max-width:320px; white-space:normal;">
                     <?php
                     if (!empty($bulkResultRow['failed_languages'])) {
                       echo '<div class="text-danger mb-1"><strong>Failed languages:</strong> '
                         . htmlspecialchars(implode(', ', $bulkResultRow['failed_languages']), ENT_QUOTES, 'UTF-8')
                         . '</div>';
                     }
                     echo !empty($translationBits) ? implode('<br>', $translationBits) : '<span class="text-muted">—</span>';
                     ?>
                   </td>
                 </tr>
                 <?php } ?>
               </tbody>
             </table>
           </div>
         </div>
         <div class="card-footer text-right">
           <button type="button" class="btn btn-sm btn-secondary" id="dismissBulkUploadResultsBtn">
             Dismiss Review
           </button>
         </div>
       </div>
     </div>
   </div>
   <?php } ?>
   <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <!-- <div class="card-header"><i class="fa fa-table"></i> Data Exporting</div> -->
          <div class="card-body">
            <div class="alert alert-info px-2 py-0 mb-3" role="alert"><i class="fa fa-info-circle mr-1"></i> <strong>Note:</strong> Pasted values in Key Name will automatically convert to lower snake_case format (e.g. <code>hello_world</code>).</div>
            <form <?php if(isset($language_key_id)){ ?> id="EditlangKeyFrm" <?php  } else {?> id="langKeyFrm" <?php } ?>  method="POST" class="form-horizontal" action="controller/languagekeyController.php">
              <?php if (!isset($language_key_id)) { ?>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label" for="language_string">Language String</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" id="language_string" name="language_string" placeholder="e.g. Sandwich Leave Report" autocomplete="off" />
                  <small class="form-text text-muted">Enter display text to auto-generate key name in snake_case format.</small>
                </div>
              </div>
              <?php } ?>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label" for="key_name">Key Name <span class="required">*</span></label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" id="key_name" name="key_name" placeholder="Key Name" required="" value="<?php if(isset($language_key_id)){ echo $key_name;} ?>" />
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label" for="key_name">Type  <span class="required">*</span></label>
                <div class="col-sm-10">
                  <div class="form-check-inline">
                    <label class="form-check-label">
                      <?php if(isset($language_key_id)) { ?>
                      <input type="radio" <?php if(isset($language_key_id) && $key_type=='0'){echo "checked";} ?>  class="form-check-input" value="0" name="key_type"> String  
                    <?php } else { ?>
                      <input type="radio" checked class="form-check-input" value="0" name="key_type"> String  
                    <?php } ?>
                    </label>
                  </div>
                  <div class="form-check-inline">
                    <label class="form-check-label">
                      <?php if(isset($language_key_id)) { ?>
                      <input type="radio"  <?php if(isset($language_key_id) && $key_type=='1'){echo "checked";} ?>  class="form-check-input" value="1" name="key_type"> Array
                      <?php } else { ?>
                      <input type="radio"  class="form-check-input" value="1" name="key_type"> Array  
                    <?php } ?>
                    </label>
                  </div>
                </div>
              </div>
              <div id="no_of_keyDiv"  <?php if(isset($language_key_id) && $key_type=='1') { echo "style='display:block'"; } else { echo "style='display:none'"; } ?>>
               <div class="form-group row" >
                <label class="col-sm-2 col-form-label" for="no_of_key">Number of Values <span class="required">*</span></label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" id="no_of_key" name="no_of_key" placeholder="Number of Values" required="" value="<?php if(isset($language_key_id)){ echo $no_of_key;} ?>" />
                </div>
              </div>
             </div>
              <div class="form-footer text-center">
                <?php if(isset($language_key_id)){ ?>
                <input type="hidden" name="language_key_id" value="<?php echo $language_key_id;?>">
                <button type="submit" class="btn btn-success" name="UpdateLanguageKey"  value="Update">Submit</button>
                <?php } else {?>
                <button type="submit" class="btn btn-success" name="AddLanguageKey"  value="Save">Submit</button>
                <?php } ?>
                
              </div>
            </form>
            
          </div>
          
        </div>
      </form>
    </div>
  </div>

   <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <!-- <div class="card-header"><i class="fa fa-table"></i> Data Exporting</div> -->
        <div class="card-body">
          <div class="table-responsive">
            <form method="POST" id="form" action="">

            </form>

            <table id="table_language_keys" class="table table-bordered">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Key Name</th>
                  <th>Type</th>
                  <th>created by</th>
                  <th>created date</th>
                  <th>Action</th>

                </thead>
                <!-------------select query-------------------->
                
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

<!-- Bulk Upload Language Keys Modal -->
<div class="modal fade" id="bulkUploadLanguageKeysModal" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white">Bulk Upload Language Keys</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="bulkUploadLanguageKeysFrm" action="controller/languagekeyController.php" method="post" enctype="multipart/form-data">
        <div class="modal-body">
          <div class="alert alert-info mb-3 px-2" role="alert">
            Paste a JSON object where each <strong>key</strong> is the language key (snake_case) and each <strong>value</strong> is the English text.
            Keys are created and auto-translated into all languages.
          </div>
          <div class="form-group">
            <label for="bulk_json_text">JSON Key / English Value Pairs</label>
            <textarea class="form-control" id="bulk_json_text" name="bulk_json_text" rows="12" placeholder='{
  "welcome_back": "Welcome Back",
  "sandwich_leave_report": "Sandwich Leave Report"
}'></textarea>
            <small class="form-text text-muted">
              Example:
              <code>{"welcome_back":"Welcome Back","sandwich_leave_report":"Sandwich Leave Report"}</code>
            </small>
          </div>
          <div class="form-group mb-0">
            <label for="bulk_json_file">Or upload a .json file</label>
            <input type="file" class="form-control-file" id="bulk_json_file" name="bulk_json_file" accept=".json,application/json">
          </div>
          <input type="hidden" name="BulkUploadLanguageKeys" value="BulkUploadLanguageKeys">
          <input type="hidden" name="csrf" value="<?php echo isset($_SESSION['token']) ? $_SESSION['token'] : ''; ?>">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-success btn-sm" id="bulkUploadLanguageKeysBtn">
            <i class="fa fa-upload mr-1"></i>Upload &amp; Auto Translate
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php if (!isset($language_key_id)) { ?>
<script type="text/javascript">
  function toSnakeCase(text) {
    return String(text || '')
      .trim()
      .replace(/\s+/g, '_')
      .replace(/([a-z])([A-Z])/g, '$1_$2')
      .replace(/[^a-zA-Z0-9_]+/g, '_')
      .replace(/_+/g, '_')
      .replace(/^_|_$/g, '')
      .toLowerCase();
  }

  document.addEventListener('DOMContentLoaded', function() {
    const languageStringInput = document.getElementById('language_string');
    const keyNameInput = document.getElementById('key_name');

    if (!languageStringInput || !keyNameInput) {
      return;
    }

    languageStringInput.addEventListener('input', function() {
      keyNameInput.value = toSnakeCase(this.value);
    });

    languageStringInput.addEventListener('paste', function(e) {
      e.preventDefault();
      const pastedText = (e.clipboardData || window.clipboardData).getData('text');
      this.value = pastedText;
      keyNameInput.value = toSnakeCase(pastedText);
    });
  });
</script>
<?php } ?>
<script src="assets/js/jquery.min.js"></script>
<script type="text/javascript">
  window.onpageshow = function(event) {
    if (event.persisted) {
        location.reload();
    }
};
</script>
<script type="text/javascript">
  $(document).ready(function() {
    $.fn.dataTable.ext.errMode = 'none';
    var table_language_keys = $('#table_language_keys').DataTable({
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
              d.languageKeys = "languageKeys";
          }
          ,
          'error': function(xhr, error, thrown) {
              console.log('Error:', error, thrown);
              $('#table_language_keys').DataTable().clear().draw();
              $('#table_language_keys tbody').html('<tr><td colspan="12" class="text-center">No data found</td></tr>');
          }
      },
      columns: [
        { "data": "id" , "width": "50px" },
        { "data": "key_name" },
        { "data": "type" , "width": "50px" },
        { "data": "created_by", "width": "50px" },
        { "data": "created_date", "width": "50px" },
        { "data": "action", "width": "80px"  }
       ],createdRow: function(row, data, dataIndex) {
        $('td:eq(0),td:eq(2),td:eq(3)', row).addClass('tableWidth');
      },
      'error': function(settings, helpPage, message) {
          console.log("DataTables error:", message);
      }
    });
  });


//dharti 10-1-2024
document.addEventListener('click', function (e) {
    if (e.target.closest('.deleteButton')) {
        const button = e.target.closest('.deleteButton');
        const languageId = button.getAttribute('data-language_key_id');
        deleteFeedback(languageId);
    }
});

function deleteFeedback(language_key_id) {
    swal({
        title: "Are you sure?",
        text: "This action will permanently delete the feedback.",
        icon: "warning",
        buttons: ['Cancel', 'Yes, delete it!'],
        dangerMode: true,
    }).then((willDelete) => {
        if (willDelete) {
            document.getElementById(`delete-form-${language_key_id}`).submit();
        }
    });
}
//dharti 10-1-2024 end

$('#bulkUploadLanguageKeysFrm').on('submit', function(e) {
  var jsonText = $.trim($('#bulk_json_text').val() || '');
  var fileInput = document.getElementById('bulk_json_file');
  var hasFile = fileInput && fileInput.files && fileInput.files.length > 0;

  if (!jsonText && !hasFile) {
    e.preventDefault();
    Lobibox.notify('warning', {
      pauseDelayOnHover: true,
      continueDelayOnInactiveTab: false,
      position: 'top right',
      icon: 'fa fa-exclamation-triangle',
      msg: 'Please paste JSON or upload a .json file.'
    });
    return false;
  }

  if (jsonText) {
    try {
      var parsed = JSON.parse(jsonText);
      if (!parsed || typeof parsed !== 'object' || Array.isArray(parsed) && parsed.length === 0) {
        throw new Error('empty');
      }
    } catch (err) {
      e.preventDefault();
      Lobibox.notify('error', {
        pauseDelayOnHover: true,
        continueDelayOnInactiveTab: false,
        position: 'top right',
        icon: 'fa fa-times-circle',
        msg: 'Invalid JSON. Expected {"key_name":"English value", ...}'
      });
      return false;
    }
  }

  $('#bulkUploadLanguageKeysBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i>Uploading...');
});

$('#copyFailedLanguageKeysBtn').on('click', function() {
  var textArea = document.getElementById('failedLanguageKeysJsonText');
  var jsonText = textArea ? textArea.value : '';
  if (!jsonText) {
    Lobibox.notify('warning', {
      pauseDelayOnHover: true,
      continueDelayOnInactiveTab: false,
      position: 'top right',
      icon: 'fa fa-exclamation-triangle',
      msg: 'No failed keys JSON available.'
    });
    return;
  }

  if (navigator.clipboard && navigator.clipboard.writeText) {
    navigator.clipboard.writeText(jsonText).then(function() {
      Lobibox.notify('success', {
        pauseDelayOnHover: true,
        continueDelayOnInactiveTab: false,
        position: 'top right',
        icon: 'fa fa-check-circle',
        msg: 'Failed keys JSON copied.'
      });
    }).catch(function() {
      textArea.select();
      document.execCommand('copy');
    });
  } else {
    textArea.style.display = 'block';
    textArea.select();
    document.execCommand('copy');
    Lobibox.notify('success', {
      pauseDelayOnHover: true,
      continueDelayOnInactiveTab: false,
      position: 'top right',
      icon: 'fa fa-check-circle',
      msg: 'Failed keys JSON copied.'
    });
  }
});

$('#dismissBulkUploadResultsBtn').on('click', function() {
  fetch('controller/languagekeyController.php?clear_bulk_language_key_results=1', { method: 'GET' })
    .finally(function() {
      $('#bulkUploadResults').slideUp(200, function() {
        $(this).remove();
      });
    });
});

if (window.location.hash === '#bulkUploadResults' && document.getElementById('bulkUploadResults')) {
  document.getElementById('bulkUploadResults').scrollIntoView({ behavior: 'smooth', block: 'start' });
}
</script>