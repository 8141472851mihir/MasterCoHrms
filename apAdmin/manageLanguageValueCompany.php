<?php
extract($_GET);
if (!isset($cId)) {
  $cId = 1;
} else {
  $cId = (int) $cId;
}

?>
<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-4">
        <h4 class="page-title">Language Key Value List Company Wise</h4>
      </div>
      <div class="col-sm-5">
        <form method="get" id="form" action="">
          <select onchange="this.form.submit()" class="form-control single-select" name="cId" id="select">
            <option value=""> Select Company</option>
            <?php
            $data = $d->selectRow("society_name,society_id,city_name", "society_master", "", "");
            while ($row = mysqli_fetch_array($data)) { ?>
              <option <?php if (isset($cId) && $cId == $row['society_id']) {
                        echo "selected";
                      } ?> value="<?php echo $row['society_id']; ?>"><?php echo $row['society_name']; ?> (<?php echo $row['city_name']; ?>)</option>
            <?php } ?>
          </select>

        </form>
      </div>
      <div class="col-sm-3">
        <div class="btn-group float-sm-right">
          <a href="companyAnalyticsGetData?getDataType=13" class="btn btn-sm btn-success waves-effect waves-light">
            <i class="fa fa-sync mr-1"></i>Sync Custom Languages
          </a>
        </div>
      </div>
    </div>
    <!-- End Breadcrumb-->
    <form id="personal-info" method="get" id="form" action="">
      <input type="hidden" name="cId" value="<?php echo $cId; ?>">
      <input type="hidden" name="language_id" value="1">
      <div class="row pt-2 pb-2">
        <div class="col-sm-6">
          <input type="text" required="" value="<?php if (isset($key_name)) {
                                                  echo htmlspecialchars($key_name, ENT_QUOTES, 'UTF-8');
                                                } ?>" name="key_name" placeholder="Enter Key Name" class="form-control">
        </div>
        <div class="col-sm-6">
          <input class="btn btn-primary" type="submit" name="" value="Search">
        </div>
      </div>
    </form>
    <?php if (isset($key_name) && $key_name != "") { ?>
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <form id="editLanguageValueKeyFrm" action="controller/languageKeyValueController.php" method="post">
              <input type="hidden" name="cId" value="<?= $cId ?>">
              <input type="hidden" name="key_name" value="<?= htmlspecialchars($key_name, ENT_QUOTES, 'UTF-8') ?>">

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
                  $qCC = $d->select("language_key_master", "key_name='$key_name'", "");
                  if (mysqli_num_rows($qCC) > 0) {
                    $keyData = mysqli_fetch_array($qCC);
                    $language_key_id = (int)$keyData['language_key_id'];

                    $q = $d->select("language_master", "", "");
                    $langRows = [];
                    $langIds = [];
                    while ($row = mysqli_fetch_array($q)) {
                      $langRows[] = $row;
                      $langIds[] = (int)$row['language_id'];
                    }

                    // Prefetch society values for this key across all languages (avoid N+1)
                    $valuesByLangId = [];
                    if (!empty($langIds)) {
                      $langIdsIn = implode(',', array_map('intval', $langIds));
                      $cIdEsc = (int)$cId;
                      $qValueAll = $d->select(
                        "language_key_value_master_society",
                        "key_name='$key_name' AND society_id='$cIdEsc' AND language_id IN ($langIdsIn)",
                        ""
                      );
                      while ($valueData = mysqli_fetch_array($qValueAll)) {
                        $lid = (int)$valueData['language_id'];
                        if (!isset($valuesByLangId[$lid])) {
                          $valuesByLangId[$lid] = ['values' => [], 'ids' => [], 'society_values' => []];
                        }
                        $valuesByLangId[$lid]['society_values'][] = $valueData['value_name_society'] ?? '';
                        $valuesByLangId[$lid]['values'][] = $valueData['value_name'] ?? '';
                        $valuesByLangId[$lid]['ids'][] = $valueData['key_value_id'] ?? null;
                      }
                    }

                    $i4 = 1;
                    foreach ($langRows as $row) {
                      $language_id = $row['language_id'];
                      $lid = (int)$language_id;
                      $editArayValue = $valuesByLangId[$lid]['values'] ?? [];
                      $editArayKeyId = $valuesByLangId[$lid]['ids'] ?? [];
                      $societyValues = $valuesByLangId[$lid]['society_values'] ?? [];
                      $valueData = [
                        'value_name_society' => $societyValues[0] ?? '',
                      ];


                  ?>

                      <tr>
                        <td><?php echo $i4++; ?></td>
                        <td><?php echo $keyData['key_name']; ?> </td>
                        <td><?php echo $row['language_name']; ?>(<?php echo $row['language_name_1']; ?>)</td>
                        <td>
                          <?php if ($keyData['key_type'] == 0) {
                          ?>
                            <input type="hidden" name="language_id[]" value="<?php if (isset($language_id)) {
                                                                                echo $language_id;
                                                                              } ?>">
                            <input type="hidden" name="key_name[]" value="<?php if (isset($key_name[$ic])) {
                                                                            echo $key_name;
                                                                          } ?>">

                            <input required="" class="form-control" type="text" class="value_name_society" id="value_name_society" value="<?php echo htmlspecialchars($valueData['value_name_society'], ENT_QUOTES, 'UTF-8'); ?>" name="value_name_society[]">
                            <?php } else {
                            for ($ic = 0; $ic < $keyData['no_of_key']; $ic++) {  ?>
                              <input type="hidden" name="language_id[]" value="<?php if (isset($language_id)) {
                                                                                  echo $language_id;
                                                                                } ?>">
                              <input type="hidden" name="language_key_id[]" value="<?php echo $language_key_id; ?>">
                              <input type="hidden" name="key_name[]" value="<?php if (isset($key_name[$ic])) {
                                                                              echo $key_name;
                                                                            } ?>">
                              <input required="" class="form-control" type="text" class="value_name_society" id="value_name_society" value="<?php echo htmlspecialchars($editArayValue[$ic] ?? '', ENT_QUOTES, 'UTF-8'); ?>" name="value_name_society[]">

                          <?php }
                          } ?>
                        </td>
                      </tr>
                  <?php }
                  } ?>
                </tbody>
              </table>
              <div class="text-center">
                <input type="hidden" name="key_name" value="<?php echo $key_name; ?>">
                <input type="hidden" name="language_id_selected" value="<?php echo $language_id; ?>">
                <input type="hidden" name="addLanguageCustomeCompany" value="addLanguageCustomeCompany">
                <button type="submit" class="btn btn-primary" name="submit" value="submit"><i class="fa fa-edit">SUBMIT</i>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    <?php } ?>
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <!-- <div class="card-header"><i class="fa fa-table"></i> Data Exporting</div> -->
          <div class="card-body">
            <div class="table-responsive">
              <table id="exampleReport" class="table table-bordered">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Language Key</th>
                    <th>Language Key Value (Eng)</th>
                    <th>Action</th>
                  </tr>
                </thead>

                <tbody>
                  <?php
                  $i = 1;

                  $q = $d->select("language_key_value_master_society,language_key_master", "language_key_master.key_name=language_key_value_master_society.key_name AND language_id='1' AND language_key_value_master_society.society_id='$cId'", "");
                  while ($row = mysqli_fetch_array($q)) {

                    $language_key_id = $row['language_key_id'];
                    $key_name = $languageKey_array[$language_key_id];


                    $language_id = $row['language_id'];
                    $language_name = $language_master_data_array[$language_id];

                  ?>
                    <tr>
                      <td><?php echo $i;
                          $i++; ?></td>
                      <td><?php echo $row['key_name']; ?></td>
                      <td><?php echo $row['value_name_society']; ?></td>


                      <td>
                        <div style="display: inline-block;">

                          <form action="manageLanguageValueCompany" method="get">
                            <input type="hidden" name="key_name" value="<?= $row['key_name'] ?>">
                            <input type="hidden" name="cId" value="<?= $row['society_id'] ?>">


                            <button type="submit" class="btn btn-sm btn-primary  " name="edit" value="edit"><i class="fa fa-edit"></i></button>
                          </form>
                        </div>
                        <div style="display: inline-block;">
                          <form action="controller/languageKeyValueController.php" method="post">
                            <input type="hidden" name="delete_key_name_company" value="<?= $row['key_name'] ?>">
                            <input type="hidden" name="cId" value="<?= $row['society_id'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger form-btn" name="delete" value="delete"><i class="fa fa-trash"></i></button>
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