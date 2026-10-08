<?php
error_reporting(0);
extract($_REQUEST);
$edit_seasonal_greet_id = $_POST['edit_seasonal_greet_id'];
$copy_seasonal_greet_id = $_POST['copy_seasonal_greet_id'];
if (isset($edit_seasonal_greet_id)) {
  $seasonal_greet_master = $d->select("seasonal_greet_master", "  seasonal_greet_id = '$edit_seasonal_greet_id' ", "");
  $seasonal_greet_master_data = mysqli_fetch_array($seasonal_greet_master);
  extract($seasonal_greet_master_data);
  $countrykAry = array();
  $duCheck = $d->select("seasonal_greet_countries", "seasonal_greet_id='$edit_seasonal_greet_id' ");
  while ($oldBlock = mysqli_fetch_array($duCheck)) {
    array_push($countrykAry, $oldBlock['country_id']);
  }
}
if (isset($copy_seasonal_greet_id)) {
  $seasonal_greet_master = $d->select("seasonal_greet_master", "  seasonal_greet_id = '$copy_seasonal_greet_id' ", "");
  $seasonal_greet_master_data = mysqli_fetch_array($seasonal_greet_master);
  extract($seasonal_greet_master_data);
  $countrykAry = array();
  $duCheck = $d->select("seasonal_greet_countries", "seasonal_greet_id='$copy_seasonal_greet_id' ");
  while ($oldBlock = mysqli_fetch_array($duCheck)) {
    array_push($countrykAry, $oldBlock['country_id']);
  }
}
$qcountries = $d->selectSpArray("getCountry");
$cIdsArray = explode(",", $countryids);
?>
<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-9">
        <?php if (!empty($edit_seasonal_greet_id)) { ?>
          <h4 class="page-title">Edit Seasonal Greetings</h4>
        <?php  } else if (!empty($copy_seasonal_greet_id)) { ?>
          <h4 class="page-title">Copy Seasonal Greetings</h4>
        <?php } else { ?>
          <h4 class="page-title">Add Seasonal Greetings</h4>
        <?php } ?>
      </div>
    </div>
    <!-- End Breadcrumb-->
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <form id="seasonalGreetFrm" action="controller/seasonalGreetController.php" method="post" enctype="multipart/form-data" autocomplete="off">
              <?php if (isset($edit_seasonal_greet_id)) { ?>
                <input type="hidden" name="promotionEditBtn" value="promotionEditBtn">
              <?php } else if (isset($copy_seasonal_greet_id)) { ?>
                <input type="hidden" name="promotionAddBtn" value="promotionAddBtn">
              <?php } else { ?>
                <input type="hidden" name="promotionAddBtn" value="promotionAddBtn">
              <?php }
              if (isset($edit_seasonal_greet_id)) { ?>
                <input type="hidden" id="isEdit" value="yes">
              <?php } else if (isset($copy_seasonal_greet_id)) { ?>
                <input type="hidden" id="isEdit" value="no">
              <?php } else { ?>
                <input type="hidden" id="isEdit" value="no">
              <?php } ?>
              <div class="form-group row">
                <label class="col-lg-2 col-form-label form-control-label">Title<span class="required">*</span></label>
                <div class="col-lg-10">
                  <input required="" type="text" class="form-control" name="title" id="title" value="<?php if (isset($edit_seasonal_greet_id)) {
                    echo $title;
                  } else if (isset($copy_seasonal_greet_id)) {
                    echo $title;
                  }  ?>" placeholder="Title" minlength="3" maxlength="100">
                </div>
              </div>
              <?php if (isset($copy_seasonal_greet_id)) { ?>
                <div class="form-group row" style="display: none;">
                  <label for="is_expiry" class="col-sm-2 col-form-label">Is Expiry?</label>
                  <div class="col-sm-4">
                    <select id="is_expiry" class="form-control single-select" name="is_expiry" type="text" onchange="isExpire();" required="">
                      <option <?php if (isset($copy_seasonal_greet_id) && $is_expiry == "Yes") {
                        echo "selected";
                      } ?> value="Yes">Yes</option>
                      <option <?php if (isset($copy_seasonal_greet_id) && $is_expiry == "No") {
                        echo "selected";
                      } ?> value="No">No</option>
                      <option <?php if (isset($copy_seasonal_greet_id) && $is_expiry == "Common") {
                        echo "selected";
                      } ?> value="Common">Common</option>
                    </select>
                  </div>
                  <label for="status" class="col-sm-2 col-form-label">Status</label>
                  <div class="col-sm-4">
                    <select id="status" class="form-control single-select" name="status" type="text" required="">
                      <option <?php if (isset($copy_seasonal_greet_id) && $status == "Active") {
                        echo "selected";
                      } ?> value="Active">Active</option>
                      <option <?php if (isset($copy_seasonal_greet_id) && $status == "Inactive") {
                        echo "selected";
                      } ?> value="Inactive">InActive</option>
                    </select>
                  </div>
                </div>
              <?php } else { ?>
                <div class="form-group row">
                  <label for="is_expiry" class="col-sm-2 col-form-label">Is Expiry?</label>
                  <div class="col-sm-4">
                    <select id="is_expiry" class="form-control single-select" name="is_expiry" type="text" onchange="isExpire();" required="">
                      <option <?php if (isset($edit_seasonal_greet_id) && $is_expiry == "Yes") {
                        echo "selected";
                      } ?> value="Yes">Yes</option>
                      <option <?php if (isset($edit_seasonal_greet_id) && $is_expiry == "No") {
                        echo "selected";
                      } ?> value="No">No</option>
                      <option <?php if (isset($edit_seasonal_greet_id) && $is_expiry == "Common") {
                        echo "selected";
                      } ?> value="Common">Common</option>
                    </select>
                  </div>
                  <label for="status" class="col-sm-2 col-form-label">Status</label>
                  <div class="col-sm-4">
                    <select id="status" class="form-control single-select" name="status" type="text" required="">
                      <option <?php if (isset($edit_seasonal_greet_id) && $status == "Active") {
                        echo "selected";
                      } ?> value="Active">Active</option>
                      <option <?php if (isset($edit_seasonal_greet_id) && $status == "Inactive") {
                        echo "selected";
                      } ?> value="Inactive">InActive</option>
                    </select>
                  </div>
                </div>
              <?php  } ?>
              <span id="date_div" <?php if ($is_expiry == "No") { ?> style="display: none;" <?php } ?>>
                <div class="form-group row">
                  <label class="col-lg-2 col-form-label form-control-label">Start Date <span class="required">*</span></label>
                  <div class="col-lg-4">
                    <input id="start-date" required="" type="text" readonly="" class="form-control" value="<?php if (isset($edit_seasonal_greet_id) && ($start_date != "1970-01-01" && $start_date != "0000-00-00")) {
                      echo date("d-m-Y", strtotime($start_date));
                    } ?>" name="start_date" autocomplete="off">
                  </div>
                  <label class="col-lg-2 col-form-label form-control-label">End Date <span class="required">*</span></label>
                  <div class="col-lg-4">
                    <input id="end-date" required="" type="text" readonly="" class="form-control" value="<?php if (isset($edit_seasonal_greet_id) && ($end_date != "1970-01-01" && $end_date != "0000-00-00")) {
                      echo date("d-m-Y", strtotime($end_date));
                    } ?>" name="end_date">
                  </div>
                </div>
                <div class="form-group row">
                  <label class="col-lg-2 col-form-label form-control-label">Event Date
                    (for sorting) <span class="required">*</span></label>
                    <div class="col-lg-4">
                      <input required="" type="text" class="form-control" name="order_date" id="autoclose-datepicker-evt" value="<?php if (isset($edit_seasonal_greet_id) && ($order_date != "0000-00-00" && $order_date != "")) { 
                        echo date("d-m-Y", strtotime($order_date)); 
                      } ?>">
                    </div>
                  </div>
                </span>
                <?php if (isset($copy_seasonal_greet_id)) { ?>
                  <div class="form-group row" style="display: none;">
                    <label class="col-sm-2 col-form-label">Countries Access <span class="required">*</span></label>
                    <div class="col-sm-10">
                      <select type="text" required="" class="form-control multiple-select" name="country_id[]" multiple="multiple">
                        <option value="">-- Select--</option>
                        <?php
                        $country = $d->select("countries", "flag=1");
                        while ($row = mysqli_fetch_array($country)) {
                          ?>
                          <option <?php if (isset($seasonal_greet_id)) {
                            if (in_array($row['country_id'], $countrykAry)) {
                              echo 'selected';
                            }
                          } ?> value="<?php echo $row['country_id']; ?>"><?php echo $row['name']; ?> </option>
                          <?php
                        }
                        ?>
                      </select>
                    </div>
                  </div>
                <?php } else if (isset($edit_seasonal_greet_id)) { ?>
                  <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Countries Access <span class="required">*</span></label>
                    <div class="col-sm-10">
                      <select type="text" required="" class="form-control multiple-select" name="country_id[]" multiple="multiple">
                        <option value="">-- Select--</option>
                        <?php
                        $country = $d->select("countries", "flag=1");
                        while ($row = mysqli_fetch_array($country)) {
                          ?>
                          <option <?php if (isset($edit_seasonal_greet_id)) {
                            if (in_array($row['country_id'], $countrykAry)) {
                              echo 'selected';
                            }
                          } ?> value="<?php echo $row['country_id']; ?>"><?php echo $row['name']; ?> </option>
                          <?php
                        }
                        ?>
                      </select>
                    </div>
                  </div>
                <?php } else { ?>
                  <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Countries Access <span class="required">*</span></label>
                    <div class="col-sm-10">
                      <select type="text" required="" class="form-control multiple-select" name="country_id[]" multiple="multiple">
                        <option value="">-- Select--</option>
                        <?php
                        $country = $d->select("countries", "flag=1");
                        while ($row = mysqli_fetch_array($country)) {
                          ?>
                          <option value="<?php echo $row['country_id']; ?>"><?php echo $row['name']; ?> </option>
                          <?php
                        }
                        ?>
                      </select>
                    </div>
                  </div>
                <?php } ?>

                <!-- new code -->
                <?php if (empty($edit_seasonal_greet_id) && empty($copy_seasonal_greet_id)) { ?>
                  <div id="dynamicFieldsContainer">
                    <!-- Dynamic Block Start -->
                    <div class="dynamic-block m-5 border border-1 p-2">
                      <fieldset class="scheduler-border">
                        <legend class="scheduler-border">Image Details</legend>
                        <div class="form-group row">
                          <label for="background_image" class="col-sm-2 col-form-label">Image <span class="required">*</span></label>
                          <div class="col-sm-10">
                            <input class="form-control-file border" id="background_image_0" accept="image/*" type="file" name="background_image[0]">
                          </div>
                        </div>
                      </fieldset>

                      <fieldset class="scheduler-border">
                        <legend class="scheduler-border">Name Details</legend>
                        <div class="form-group row">
                          <label class="col-sm-2 col-form-label">Show To Name?</label>
                          <div class="col-sm-4">
                            <select class="form-control" name="show_to_name[0]" required>
                              <option value="No">No</option>
                              <option value="Yes">Yes</option>
                            </select>
                          </div>
                          <label class="col-sm-2 col-form-label">Show From Name?</label>
                          <div class="col-sm-4">
                            <select class="form-control" name="show_from_name[0]" required>
                              <option value="Yes">Yes</option>
                              <option value="No">No</option>
                            </select>
                          </div>
                        </div>
                      </fieldset>

                      <fieldset class="scheduler-border">
                        <legend class="scheduler-border">Other Details</legend>
                        <div class="form-group row">
                          <label class="col-lg-2 col-form-label form-control-label">Status</label>
                          <div class="col-lg-4">
                            <select class="form-control" name="other_status[0]">
                              <option value="Active">Active</option>
                              <option value="InActive">InActive</option>
                            </select>
                          </div>
                        </div>
                      </fieldset>
                      <button type="button" class="btn btn-danger remove-block mt-2">
                        <i class="bi bi-trash"></i> Remove
                      </button>
                    </div>
                    <!-- Dynamic Block End -->
                  </div>

                  <button id="rowAdder" type="button" class="btn btn-dark mt-3">
                    <i class="bi bi-plus-square-dotted"></i> ADD More Image
                  </button>
                  <input type="hidden" id="counter" name="counter" value="0">
                <?php } ?>
                <div class="form-footer text-center">
                  <?php if (isset($edit_seasonal_greet_id)) { ?>
                    <input type="hidden" name="seasonal_greet_id" value="<?php echo $seasonal_greet_id; ?>">
                    <input type="hidden" name="updateSeasonalGreet" value="updateSeasonalGreet">
                    <button type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i> UPDATE</button>
                  <?php  } else if (isset($copy_seasonal_greet_id)) { ?>
                    <input type="hidden" name="copy_seasonal_greet_id" value="<?php echo $seasonal_greet_id; ?>">
                    <input type="hidden" name="copySeasonalGreet" value="copySeasonalGreet">
                    <button type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i> SAVE</button>
                  <?php } else { ?>
                    <input type="hidden" name="addSeasonalGreet" value="addSeasonalGreet">
                    <button type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i> SAVE</button>
                  <?php } ?>
                  <a href="seasonalGreetList" class="btn btn-danger">Cancel</a>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div><!--End Row-->
    </div>
    <!-- End container-fluid-->
  </div>
  <!-- End content-wrapper-->