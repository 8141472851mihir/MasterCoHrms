<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Breadcrumb-->
        <div class="row pt-2 pb-2">
            <div class="col-sm-9">
                <?php if (isset($_POST['edit_white_lable'])) { ?>
                    <h4 class="page-title">Edit White Label</h4>
                <?php } else { ?>
                    <h4 class="page-title">Add White Label</h4>
                <?php } ?>
            </div>
        </div>
        <!-- End Breadcrumb-->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form id="addmycoWhiteLable" action="controller/mycoWhitelabelController.php" method="post"
                            enctype="multipart/form-data">
                            <?php
                            if (isset($_POST['edit_white_lable'])) {
                                extract(array_map("test_input", $_POST));
                                $q = $d->select("society_master_white_label", "society_id='$society_id'");
                                $data = mysqli_fetch_array($q);
                            }
                            ?>
                            <?php if (isset($_POST['edit_white_lable'])) { ?>
                                <h4 class="form-header text-uppercase"><i class="fa fa-building"></i>Edit White Label
                                </h4>
                            <?php } else { ?>
                                <h4 class="form-header text-uppercase"><i class="fa fa-building"></i> Add White Label
                                </h4>
                            <?php } ?>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Company ID <span
                                        class="required">*</span></label>
                                <div class="col-sm-4">
                                    <input type="text" autocomplete="off" maxlength="40" class="form-control onlyNumber"
                                        name="master_company_id" value="<?php echo $data['master_company_id']; ?>">
                                </div>
                                <label class="col-sm-2 col-form-label">Company Name <span
                                        class="required">*</span></label>
                                <div class="col-sm-4">
                                    <input type="text" value="<?php echo $data['society_name']; ?>" autocomplete="off"
                                         maxlength="50" class="form-control" id="input-10" name="society_name">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="country_id" class="col-sm-2 col-form-label"> Country </label>
                                <div class="col-sm-4">

                                    <select type="text"  id="country_id" onchange="getStates();"
                                        class="form-control single-select" name="country_id">
                                        <option value="">-- Select --</option>
                                        <?php
                                        $qc = $d->select("countries", "flag=1");
                                        while ($cData = mysqli_fetch_array($qc)) {
                                            ?>
                                            <option <?php if ($data['country_id'] == $cData['country_id']) {
                                                echo "selected";
                                            } ?> value="<?php echo $cData['country_id']; ?>">
                                                <?php echo $cData['name']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <label for="state_id" class="col-sm-2 col-form-label"> State </label>
                                <div class="col-sm-4">
                                    <?php if (isset($_POST['edit_white_lable'])) {

                                        ?>
                                        <select type="text" onchange="getCity();" 
                                            class="form-control single-select" id="state_id" name="state_id">
                                            <?php
                                            $qs = $d->select("states", "country_id=$data[country_id]");
                                            while ($sData = mysqli_fetch_array($qs)) {
                                                ?>
                                                <option <?php if (isset($data['state_id']) && $data['state_id'] == $sData['state_id']) {
                                                    echo "selected";
                                                } ?>
                                                    value="<?php echo $sData['state_id']; ?>"><?php echo $sData['name']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    <?php } else { ?>
                                        <select type="text" onchange="getCity();" 
                                            class="form-control single-select" id="state_id" name="state_id">
                                            <option value="">-- Select --</option>
                                        </select>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="input-101" class="col-sm-2 col-form-label"> City </label>
                                <div class="col-sm-4">
                                    <?php if (isset($_POST['edit_white_lable'])) {

                                        ?>
                                        <select type="text" class="form-control single-select" id="city_id"
                                            name="city_id">
                                            <?php
                                            $qcity = $d->select("cities", "state_id=$data[state_id]");
                                            while ($cityData = mysqli_fetch_array($qcity)) {
                                                ?>
                                                <option <?php if (isset($data['city_id']) && $data['city_id'] == $cityData['city_id']) {
                                                    echo "selected";
                                                } ?>
                                                    value="<?php echo $cityData['city_id']; ?>"><?php echo $cityData['name']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    <?php } else { ?>
                                        <select type="text" class="form-control single-select" name="city_id"
                                            id="city_id">
                                            <option value="">-- Select --</option>

                                        </select>
                                    <?php } ?>
                                </div>
                                <label for="input-101" class="col-sm-2 col-form-label"> Address </label>
                                <div class="col-sm-4">
                                    <textarea class="form-control" id="input-101"
                                        name="society_address"><?php echo $data['society_address']; ?></textarea>
                                </div>
                            </div>
                            <!-- start -->
                            <div class="form-group row">
                                <label for="input-101" class="col-sm-2 col-form-label"> Project Type <span
                                        class="required">*</span></label>
                                <div class="col-sm-4">
                                    <?php if (isset($_POST['edit_white_lable'])) {

                                    ?>
                                    <select type="text" required="" class="form-control single-select" id="project_type"
                                        name="project_type">
                                        <option <?php echo ($data['project_type']=='0')?"selected":""; ?> value='0'>Myco</option>
                                        <option <?php echo ($data['project_type']=='1')?"selected":""; ?> value='1'>Smart society</option>
                                        <option <?php echo ($data['project_type']=='2')?"selected":""; ?> value='2'>My Association</option>
                                    </select>
                                    <?php } else { ?>
                                    <select type="text" required="" class="form-control single-select" name="project_type"
                                        id="project_type">
                                        <option value="">-- Select --</option>
                                        <option value="0">MyCo</option>
                                        <option value="1">Smart Society</option>
                                        <option value="2">My Association</option>
                                    </select>
                                    <?php } ?>
                                </div>
                                <!-- url -->
                                 <label for="sub_domain" class="col-sm-2 col-form-label">Company Base URL <span
                                        class="required">*</span></label>
                                <div class="col-sm-4">
                                    <input type="text" id="sub_domain" autocomplete="off" maxlength="120"
                                        value="<?php echo $data['sub_domain']; ?>" required="" class="form-control"
                                        name="sub_domain">
                                </div>
                            </div>

                            <!-- Optional Logo -->
                            <div class="form-group row">
                                <label for="powered_by_logo" class="col-sm-2 col-form-label">Company Logo</label>
                                <div class="col-sm-4">
                                    <input type="file" accept=".jpg,.jpeg,.png,.webp,.svg" class="form-control" id="powered_by_logo" name="powered_by_logo">
                                    <?php if (isset($_POST['edit_white_lable'])) { ?>
                                        <input type="hidden" name="powered_by_logo_old" value="<?php echo isset($data['powered_by_logo']) ? htmlspecialchars($data['powered_by_logo']) : ''; ?>">
                                    <?php } ?>
                                    <small class="text-muted">Optional. JPG/PNG/WebP/SVG</small>
                                </div>
                                <label class="col-sm-2 col-form-label">Logo Preview</label>
                                <div class="col-sm-4">
                                    <?php
                                        $pbLogoVal = isset($data['powered_by_logo']) ? trim($data['powered_by_logo']) : '';
                                        $logoSrc = '';
                                        if ($pbLogoVal !== '') {
                                            $isAbsolute = (bool)preg_match('/^(https?:\\/\\/|\\/)/i', $pbLogoVal);
                                            $hasImgPath = (strpos($pbLogoVal, 'img/') !== false);
                                            $logoSrc = $isAbsolute || $hasImgPath ? $pbLogoVal : ('../img/whitelabel/' . $pbLogoVal);
                                        }
                                    ?>
                                    <img id="powered_by_logo_preview" src="<?php echo htmlspecialchars($logoSrc); ?>" alt="Logo Preview" onerror="this.style.display='none'" style="max-height:80px; <?php echo empty($logoSrc) ? 'display:none;' : ''; ?>">
                                </div>
                            </div>

                            <script>
                            (function(){
                                var input = document.getElementById('powered_by_logo');
                                if(input){
                                    input.addEventListener('change', function(e){
                                        var file = e.target.files && e.target.files[0];
                                        var img = document.getElementById('powered_by_logo_preview');
                                        if(file && img){
                                            var reader = new FileReader();
                                            reader.onload = function(ev){
                                                img.src = ev.target.result;
                                                img.style.display = 'inline-block';
                                            };
                                            reader.readAsDataURL(file);
                                        }
                                    });
                                }
                            })();
                            </script>

                            <div class="form-footer text-center">
                                <?php if (isset($_POST['edit_white_lable'])) { ?>
                                    <input name="editWhitelabel" type="hidden">
                                    <input name="society_id" type="hidden"
                                       id="society_id" value="<?php echo $data['society_id']; ?>">
                                    <button type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i>
                                        UPDATE</button>
                                <?php } else { ?>
                                    <button name="addWhitelabel" type="submit" class="btn btn-success">
                                        <i class="fa fa-check-square-o"></i> ADD</button>
                                <?php } ?>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>