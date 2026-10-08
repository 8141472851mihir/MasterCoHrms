<?php
extract($_GET);
$autoGenerateLanguageString = '';
if (!empty($_SESSION['new_language_key_string'])) {
  $autoGenerateLanguageString = $_SESSION['new_language_key_string'];
}
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
    <div class="row pt-2 pb-2">
      <div class="col-sm-4">
        <h4 class="page-title">Language Key Value List</h4>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="welcome">Home</a></li>
          <li class="breadcrumb-item active" aria-current="page">Language Key Value List</li>
        </ol>
      </div>
      <div class="col-sm-5"></div>
      <div class="col-sm-3">
        <div class="btn-group float-sm-right">
          <a href="keyValue" class="btn btn-sm btn-primary waves-effect waves-light"><i class="fa fa-plus mr-1"></i>Add New Language Key Value</a>
        </div>
      </div>
    </div>
    <form id="personal-info" method="get" id="form" action="">
      <input type="hidden" name="language_id" value="<?php echo $language_id; ?>">
      <div class="row pt-2 pb-2">
        <div class="col-sm-6">
          <input type="text" required="" value="<?php echo isset($key_name) ? htmlspecialchars($key_name, ENT_QUOTES, 'UTF-8') : ''; ?>" name="key_name" placeholder="Enter Key Name" class="form-control">
        </div>
        <div class="col-sm-6">
          <input class="btn btn-primary" type="submit" name="" value="Search">
        </div>
      </div>
    </form>
    <?php if (isset($key_name) && $key_name != "") {
      $qCC = $d->select("language_key_master", "key_name='$key_name'", "");
      if (mysqli_num_rows($qCC) > 0) {
        $keyData = mysqli_fetch_array($qCC);
        $language_key_id = $keyData['language_key_id'];
        $languages = [];
        $q = $d->select("language_master", "", "");
        while ($row = mysqli_fetch_array($q)) {
          $languages[$row['language_id']] = $row;
        }
        $valuesByLanguage = [];
        $qValues = $d->select("language_key_value_master", "language_key_id='$language_key_id'", "ORDER BY language_id, key_value_id");
        while ($valueRow = mysqli_fetch_array($qValues)) {
          $valuesByLanguage[$valueRow['language_id']][] = $valueRow;
        }
    ?>
        <div class="row mb-3">
          <div class="col-sm-8">
            <input type="text" id="generateText" placeholder="Enter text to populate all fields" class="form-control">
          </div>
          <div class="col-sm-4">
            <button type="button" id="generateBtn" class="btn btn-success">Generate All Values</button>
          </div>
        </div>

        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <form id="editLanguageValueKeyFrm" action="controller/languageKeyValueController.php" method="post">
                <table class="table table-bordered data_table">
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
                    $i4 = 1;
                    foreach ($languages as $langId => $language) {
                      $values = $valuesByLanguage[$langId] ?? [];
                    ?>
                      <tr>
                        <td><?php echo $i4++; ?></td>
                        <td><?php echo $keyData['key_name']; ?></td>
                        <td><?php echo $language['language_name']; ?>(<?php echo $language['language_name_1']; ?>)</td>
                        <td>
                          <?php if ($keyData['key_type'] == 0) { ?>
                            <div class="value-container">
                              <input type="hidden" name="language_id[]" value="<?php echo $langId; ?>">
                              <input type="hidden" name="language_key_id[]" value="<?php echo $language_key_id; ?>">
                              <input type="hidden" name="key_value_id[]" value="<?php echo $values[0]['key_value_id'] ?? ''; ?>">
                              <div class="sortable-item" data-id="<?php echo '0'; ?>">
                                <input required class="form-control value-input d-inline-block" type="text"
                                  name="value_name[]" value="<?php echo htmlspecialchars($values[0]['value_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" style="width: calc(100% - 40px);">
                              </div>
                            </div>
                          <?php } else { ?>
                            <div class="value-container">
                              <?php
                              $valueCount = max(count($values), $keyData['no_of_key']);
                              $valueId = 0;
                              for ($ic = 0; $ic < $valueCount; $ic++) {
                              ?>
                                <input type="hidden" name="language_id[]" value="<?php echo $langId; ?>">
                                <input type="hidden" name="language_key_id[]" value="<?php echo $language_key_id; ?>">
                                <input type="hidden" name="key_value_id[]" value="<?php echo $values[$ic]['key_value_id'] ?? ''; ?>">
                                <div class="sortable-item" data-id="<?php echo $valueId++; ?>">

                                  <input required class="form-control value-input d-inline-block" type="text"
                                    name="value_name[]" value="<?php echo htmlspecialchars($values[$ic]['value_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" style="width: calc(100% - 40px);">
                                  <button type="button" class="drag-handle btn btn-sm btn-light ml-2"><i class="fa fa-bars  "></i></button>
                                </div>
                              <?php } ?>
                            </div>
                          <?php } ?>
                        </td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
                <div class="text-center">
                  <input type="hidden" name="key_name" value="<?php echo htmlspecialchars($key_name, ENT_QUOTES, 'UTF-8'); ?>">
                  <input type="hidden" name="language_id_selected" value="<?php echo $language_id; ?>">
                  <input type="hidden" name="AddLanguageKeyValueAll" value="AddLanguageKeyValueAll">
                  <input type="hidden" name="backMenu" value="lngValueFind">
                  <button type="submit" class="btn btn-primary" name="submit" value="submit">
                    <i class="fa fa-edit">SUBMIT</i>
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
    <?php }
    } ?>
  </div>
</div>

<script type="text/javascript">
  window.onpageshow = function(event) {
    if (event.persisted) {
      location.reload();
    }
  };
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const containers = Array.from(document.querySelectorAll('.value-container'));

    containers.forEach((container) => {
      new Sortable(container, {
        animation: 150,
        handle: '.drag-handle',
        onEnd: function(evt) {
          const items = container.querySelectorAll('.sortable-item');
          const order = Array.from(items).map(item => item.dataset.id);
        }
      });
    });

    const generateBtn = document.getElementById('generateBtn');
    const generateTextInput = document.getElementById('generateText');
    const languageCount = <?php echo (isset($languages) && is_array($languages)) ? count($languages) : 0; ?>;
    const languageNames = [
      <?php
      if (isset($languages) && is_array($languages)) {
        foreach ($languages as $lang) {
          echo "'" . addslashes($lang['language_name']) . "',";
        }
      }
      ?>
    ];
    const autoGenerateLanguageString = <?php echo json_encode($autoGenerateLanguageString); ?>;

    function clearAutoLanguageStringSession() {
      if (!autoGenerateLanguageString) {
        return;
      }
      fetch('controller/languagekeyController.php?clear_new_language_key_string=1', { method: 'GET' }).catch(() => {});
    }

    function runGenerateAllValues() {
      const generateText = generateTextInput ? generateTextInput.value : '';
      if (!generateText) {
        Lobibox.notify('warning', {
          pauseDelayOnHover: true,
          continueDelayOnInactiveTab: false,
          position: 'top right',
          icon: 'fa fa-exclamation-triangle',
          msg: "Please enter text to populate all fields."
        });
        return;
      }

      const characterCount = generateText.length;

      fetch('save_character_count.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: `text=${encodeURIComponent(generateText)}&character_count=${characterCount}&language_count=${languageCount}&total_characters=${characterCount * languageCount}&key_name=${encodeURIComponent('<?php echo isset($key_name) ? $key_name : ""; ?>')}&language_names=${encodeURIComponent(JSON.stringify(languageNames))}&timestamp=${new Date().toISOString()}`
        })
        .then(response => {
          if (!response.ok) {
            throw new Error('Network response was not ok');
          }
          return response.json();
        })
        .then(data => {
          const normalizedTranslations = {};
          Object.entries(data.translations || {}).forEach(([key, value]) => {
            normalizedTranslations[String(key).trim()] = value;
          });

          const languageRows = document.querySelectorAll('tbody tr');
          languageRows.forEach((row) => {
            const languageName = row.querySelector('td:nth-child(3)').textContent.split('(')[0].trim();
            const translation = normalizedTranslations[languageName] || '';
            const inputs = row.querySelectorAll('.value-input');

            inputs.forEach(input => {
              input.value = translation;
            });
          });

          Lobibox.notify('success', {
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            position: 'top right',
            icon: 'fa fa-check-circle',
            msg: "Processed translations for " + languageCount + " languages"
          });

          if (autoGenerateLanguageString) {
            clearAutoLanguageStringSession();
          }
        })
        .catch(error => {
          console.error('Error:', error);
          if (autoGenerateLanguageString) {
            clearAutoLanguageStringSession();
          }
          Lobibox.notify('error', {
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            position: 'top right',
            icon: 'fa fa-exclamation-circle',
            msg: "Error processing translations. Some fields may be empty."
          });
        });
    }

    if (generateBtn) {
      generateBtn.addEventListener('click', runGenerateAllValues);
    }

    if (autoGenerateLanguageString && generateTextInput && languageCount > 0) {
      generateTextInput.value = autoGenerateLanguageString;
      runGenerateAllValues();
    }
  });
</script>
<style>
  .sortable-item {
    margin-bottom: 8px;
    display: flex;
    align-items: center;
  }

  .drag-handle {
    cursor: move;
  }

  .value-input {
    flex-grow: 1;
  }
</style>