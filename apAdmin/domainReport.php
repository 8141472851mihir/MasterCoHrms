<?php
error_reporting(0);
$sId = (isset($_GET['sId']) && $_GET['sId'] != 'all') ? $d->sanitizeReportFilterIdAsInt($_GET['sId']) : 0;
$sId = $sId > 0 ? $sId : '';
$maintenance = (isset($_GET['maintenance']) && $_GET['maintenance'] != 'all') ? $d->sanitizeReportFilterIdAsInt($_GET['maintenance']) : 0;
$maintenance = $maintenance > 0 ? $maintenance : '';
?>
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row pb-2">
            <div class="col-sm-2">
                <h4 class="page-title">Domain Report</h4>
            </div>
            <div class="col-sm-3">
                <form action="" method="get" accept-charset="utf-8">
                    <input type="hidden" name="maintenance" value="<?php echo $_GET['maintenance']; ?>">
                    <select type="text" required="" id="sId" onchange="this.form.submit()"
                        class="form-control single-select" name="sId">
                        <option value="all">-- All --</option>
                        <?php
                        $qc = $d->select("server_master", "");
                        while ($cData = mysqli_fetch_array($qc)) {
                            ?>
                            <option <?php if (isset($sId) && $cData['server_id'] == $sId) {
                                echo "selected";
                            } ?> value="<?php echo $cData['server_id']; ?>"><?php echo $cData['server_name']; ?>
                                (<?php echo $cData['server_ip']; ?>)</option>
                        <?php } ?>
                    </select>
                </form>
            </div>
            <div class="col-sm-3">
                <form action="" method="get" accept-charset="utf-8">
                    <input type="hidden" name="sId" value="<?php echo $_GET['sId']; ?>">
                    <select type="text" required="" id="maintenance" onchange="this.form.submit()"
                        class="form-control single-select" name="maintenance">
                        <option value="all">-- All Domains --</option>
                        <option <?php if (isset($_GET['maintenance']) && $_GET['maintenance'] == '1') {
                            echo "selected";
                        } ?> value="1">Domain under maintenance</option>
                        <option <?php if (isset($_GET['maintenance']) && $_GET['maintenance'] == '2') {
                            echo "selected";
                        } ?> value="2">Domain not in maintenance</option>
                    </select>
                </form>
            </div>
        </div>
        <?php
        $companyArray = array();
        $companyArrayExpire = array();
        $qcompany = $d->selectRow(
            "domain_id, CASE WHEN STR_TO_DATE(TRIM(plan_expire_date), '%Y-%m-%d') < CURDATE() THEN 'YES' ELSE 'NO' END AS is_expired",
            "society_master",
            "domain_id!=0"
        );
        while ($companyCountData = mysqli_fetch_assoc($qcompany)) {
            $domain_id = $companyCountData['domain_id'];
            if (!isset($companyArray[$domain_id])) {
                $companyArray[$domain_id] = 0;
                $companyArrayExpire[$domain_id] = 0;
            }
            $companyArray[$domain_id]++;
            if ($companyCountData['is_expired'] == 'YES') {
                $companyArrayExpire[$domain_id]++;
            }
        }
        $maintenance_date_time = $d->selectRow("festival_time", "festival_master", "is_festival='1'");
        if (mysqli_num_rows($maintenance_date_time)) {
            $maintenance_data = mysqli_fetch_array($maintenance_date_time);
            $maintenance_time = $maintenance_data['festival_time'];
            if ($maintenance_time != '' && $maintenance_time != '0000-00-00 00:00:00') {
                $maintenance_datetime = new DateTime($maintenance_time);
                $current_datetime = new DateTime();
                if ($maintenance_datetime < $current_datetime) {
                    $message = "Ongoing Maintenance";
                } else {
                    $message = "Upcoming Maintenance";
                }
                $show_note = "";
            } else {
                $show_note = "d-none";
            }
        }
        ?>
        <div class="col-sm-12 text-danger float-right <?php echo $show_note; ?>">
            <p>Note: <?php echo $message; ?> :- <?php echo $maintenance_time; ?> </p>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="reportTable" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Domain Name</th>
                                        <th>Server Name</th>
                                        <th>Company Count</th>
                                        <th>Expire Company</th>
                                        <th>Remote Db</th>
                                        <th>Maintainance</th>
                                        <th>Remote Ip</th>
                                        <th>Domain Remark</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th class="no-search-box"></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    <?php
                                    $appendSeverQuery = '';
                                    if (isset($sId) && $sId > 0 && $sId != 'all') {
                                        $appendSeverQuery .= " AND dm.server_id='$sId'";
                                    }
                                    if (isset($maintenance) && $maintenance > 0 && $maintenance != 'all') {
                                        $maintenance_value = ($maintenance == '1') ? '1' : '0';
                                        $appendSeverQuery .= " AND dm.maintainance_active_status='$maintenance_value'";
                                    }
                                    $i = 1;
                                    $q = $d->selectRow("dm.*,sm.server_name,sm.server_ip", "domain_master as dm,server_master as sm", "sm.server_id=dm.server_id  $appendSeverQuery");
                                    while ($data = mysqli_fetch_array($q)) {
                                        extract($data);

                                        ?>
                                        <tr>
                                            <td><?php echo $i++; ?></td>
                                            <td><?php echo $domain_name; ?></td>
                                            <!-- <td><?php echo "UPDATE `society_master` SET `domain_id`='$domain_id' WHERE `sub_domain` LIKE '%$domain_name%';"; ?></td> -->
                                            <td class="tableWidth"><?php echo $server_name; ?> (<?php echo $server_ip; ?>)
                                            </td>
                                            <td class="tableWidth"><?php if (array_key_exists($domain_id, $companyArray)) {
                                                echo $companyArray[$domain_id];
                                            } ?></td>
                                            <td><?php if (array_key_exists($domain_id, $companyArrayExpire)) {
                                                echo $companyArrayExpire[$domain_id];
                                            } ?></td>

                                            <td class="tableWidth">
                                                <?php echo ($is_remote_db == 1) ? "YES" : "NO"; ?>
                                            </td>
                                            <td class="tableWidth">
                                                <?php echo ($maintainance_active_status == 1) ? "YES" : "NO"; ?>
                                            </td>
                                            <td class="tableWidth"><?php echo $remote_ip; ?></td>
                                            <td class="tableWidth"><?php echo $domain_remark; ?></td>
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