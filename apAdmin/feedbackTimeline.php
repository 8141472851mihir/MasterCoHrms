<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
extract(array_map("test_input", $_REQUEST));
$id = isset($_GET['id']) ? $d->sanitizeReportFilterIdAsInt($_GET['id']) : 0;
$bms_admin_id = $bms_admin_id;
$feedback_id = $id;
$readByList = $d->selectRow("read_by_id", "read_by", "read_user_id='$bms_admin_id' AND feedback_id='$feedback_id'", "ORDER BY read_by_id DESC");
$readBy = mysqli_fetch_array($readByList);
if (mysqli_num_rows($readByList) == 0) {
  if ($feedback_id > 0) {
    $a12['read_user_id'] = $bms_admin_id;
    $a12['feedback_id'] = $feedback_id;
    $a12['date_time'] = date('Y-m-d H:i:s');
    $q = $d->insert("read_by", $a12);
  }
} else {
  $read_by_id = $readBy['read_by_id'];
  $a11['updated_date'] = date('Y-m-d H:i:s');
  $qq = $d->update("read_by", $a11, "read_by_id='$read_by_id'");
}

$q = $d->selectRow(
  "feedback_master.*,society_master.*,
   feedback_master.society_id as ticket_society_id,
   feedback_master.is_whitelabel as ticket_is_whitelabel,
   feedback_master.whitelabel_type as ticket_whitelabel_type,
   society_master_white_label.society_name as wl_society_name,
   COALESCE(wl_city.name, society_master_white_label.city_name) as wl_city_name,
   society_master_white_label.sub_domain as wl_sub_domain,
   server_master.server_ip",
  "feedback_master
LEFT JOIN society_master ON society_master.society_id = feedback_master.society_id AND COALESCE(feedback_master.is_whitelabel, 0) = 0
LEFT JOIN society_master_white_label ON society_master_white_label.society_id = feedback_master.society_id AND society_master_white_label.project_type = feedback_master.whitelabel_type AND COALESCE(feedback_master.is_whitelabel, 0) = 1
LEFT JOIN cities wl_city ON wl_city.city_id = society_master_white_label.city_id
LEFT JOIN domain_master ON society_master.domain_id = domain_master.domain_id
LEFT JOIN server_master ON server_master.server_id = domain_master.server_id",
  "feedback_master.feedback_id='$id'",
  ""
);
$up['read_status'] = 1;
$d->update("admin_notification", $up, "admin_click_action='feedbackTimeline?id=" . $id . "' AND admin_id='$bms_admin_id'");
if (mysqli_num_rows($q) == 0) {
  $_SESSION['msg1'] = "Invalid feedback";
  echo "<script>window.location.href = './feedback'; </script>";
  exit;
}
$data = mysqli_fetch_array($q);
extract($data);
$society_id = $ticket_society_id ?? $data['ticket_society_id'] ?? $society_id;
$is_whitelabel = $ticket_is_whitelabel ?? $data['ticket_is_whitelabel'] ?? $is_whitelabel ?? 0;
$whitelabel_type = $ticket_whitelabel_type ?? $data['ticket_whitelabel_type'] ?? $whitelabel_type ?? 0;

// For whitelabel tickets, `society_id` stores society_master_white_label.society_id.
// Override display + subdomain fields so openLogin/heading are correct.
if ((int)($data['is_whitelabel'] ?? 0) === 1) {
  $society_name = $wl_society_name ?? $society_name;
  $city_name = $wl_city_name ?? $city_name;
  $sub_domain = $wl_sub_domain ?? $sub_domain;
}
if ($read_by == 0) {
  $read_by = $bms_admin_id;
  $m->set_data('read_by', $read_by);
  $m->set_data('read_time', date('Y-m-d H:i:s'));
  $a1 = array(
    'read_by' => $m->get_data('read_by'),
    'read_time' => $m->get_data('read_time'),
  );
  $q = $d->update('feedback_master', $a1, "feedback_id='$feedback_id'");
}

?>
<link href="assets/plugins/vertical-timeline/css/vertical-timeline1.css" rel="stylesheet" />
<div class="content-wrapper">
  <div class="container-fluid">
    <div class="row pt-2 pb-2">
      <div class="col-sm-6">
        <h4 class="page-title">Feedback Details (#TKT<?php echo $data['feedback_id']; ?>) </h4>
      </div>
      <div class="col-sm-6 text-right">
        <?php if ($role_id == 1) {
          if ($server_ip != "") {
        ?>
            <a target="_blank" class="btn ml-1 btn-sm btn-info pull-right" href="http://<?php echo $server_ip; ?>/phpmyadmin/"><i class="fa fa-database"></i> </a>
          <?php } ?>

          <form class="d-inline-block" action="readUserList" method="GET">
            <input type="hidden" name="id" value="<?php echo $feedback_id; ?>">
            <button type="submit" title="Read User List" name="" class="btn  btn-sm btn-danger pull-right"><i class="fa fa-bars fa-lg"></i> Read User List</button>
          </form>
          <form class="d-inline-block" action="companyQueryList" method="GET">
            <input type="hidden" name="society_id" value="<?php echo (int)$society_id; ?>">
            <input type="hidden" name="is_whitelabel" value="<?php echo (int)$is_whitelabel; ?>">
            <input type="hidden" name="whitelabel_type" value="<?php echo (int)$whitelabel_type; ?>">
            <button type="submit" title="Company Query List" name="" class="btn  btn-sm btn-primary pull-right"><i class="fa fa-bars fa-lg"></i>Company Query List</button>
          </form>
          <a href="feedback<?php echo $d->feedbackListQuerySuffix(); ?>" title="Back to list" name="" class="btn ml-1  btn-sm btn-warning pull-right"><i class="fa fa-arrow-left"></i> Back</a>
        <?php
        }
        ?>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <?php
                $companyHeading = $society_name . '-' . $city_name . ' (' . $d->short_app_name() . '_' . $society_id . ')';
                if ((int)($is_whitelabel ?? 0) === 1) {
                  $whitelabelTypeLabels = [
                    0 => 'MyCo',
                    1 => 'Smart Society',
                    2 => 'My Association',
                  ];
                  $typeLabel = $whitelabelTypeLabels[(int)($whitelabel_type ?? 0)] ?? 'Whitelabel';
                  $companyHeading = $society_name . '-' . $city_name . ' (' . $typeLabel . ' Whitelabel)';
                }
                ?>
                <h6 ondblclick="openLogin('<?php echo $sub_domain . 'apAdmin'; ?>');">Company : <?php echo $companyHeading; ?> </h6>
                <?php if ($bms_admin_data['is_developer'] == 1 && !empty($server_ip)) { ?>
                  <h6>Company IP : <a target="_blank" href="http://<?php echo $server_ip; ?>/phpmyadmin/"><?php echo $server_ip; ?></a></h6>
                <?php } ?>
                <h6>Subject : <?php echo $subject; ?></h6>
                <?php
                $platform_name = "";
                if ($platform == 0) {
                  $platform_name = "Other";
                } else if ($platform == 1) {
                  $platform_name = "Android";
                } else if ($platform == 2) {
                  $platform_name = "iOS";
                } else if ($platform == 3) {
                  $platform_name = "Web";
                } else if ($platform == 4) {
                  $platform_name = "CRM";
                }

                ?>
                <h6>Platform : <?php echo $platform_name; ?> <button data-toggle="modal" data-target="#editPlatform" title="Edit Platform" class="btn btn-primary btn-sm <?php echo ($feedback_status == 2) ? 'd-none' : ''; ?>"><i class="fa fa-pencil"></i></button></h6>
                <?php
                $moduleTypes = [
                  0 => "Other",
                  1 => "My Visits",
                  2 => "Sales & Order",
                  3 => "Work Report",
                  4 => "Leave",
                  5 => "Payroll",
                  6 => "Attendance",
                  7 => "Tasks",
                  8 => "Tax Exemption",
                  9 => "Performance Matrix",
                  10 => "CRM",
                  11 => "Tracking",
                  12 => "Biometric Attendance",
                  13 => "Face App",
                  14 => "Loan",
                  15 => "Expense",
                  16 => "Advance Expense",
                  17 => "Advance Salary",
                  18 => "Holiday",
                  19 => "Employee Document & Letter",
                  20 => "App Not Opening",
                  21 => "Log Issue",
                  22 => "Management",
                  23 => "Employee",
                  24 => "Server Down",
                  25 => "Server Timeout",
                  26 => "Third Party Integration",
                  27 => "Chat",
                  28 => "Timeline",
                  29 => "Assets",
                  30 => "Site Management",
                  31 => "Penalty",
                ];
                $module_name = isset($moduleTypes[$data['module_type']]) ? $moduleTypes[$data['module_type']] : 'Not specified';
                ?>
                <h6>Module Type : <?php echo $module_name; ?> <button class="btn btn-primary btn-sm " data-toggle="modal" data-target="#editPlatform" title="Edit Module Type"> <i class="fa fa-pencil"></i> </button> </h6>
                <p><b>Client Name :</b> <?php echo $name; ?></p>
                <p><b>Client Mobile :</b> <?php echo $mobile; ?></p>
                <?php if ($email != '') { ?><p><b>Client Email :</b> <?php echo $email; ?></p><?php } ?>
                <?php if ($app_version_code != '') { ?><p><b>App Version :</b> <?php echo $app_version_code; ?></p> <?php } ?>
                <?php if ($device != '') { ?><p><b>Device :</b> <?php echo $device; ?></p> <?php } ?>
                <p><b>Message :</b> <?php echo $feedback_msg; ?></p>
                <?php
                $adminDataCreated = $d->selectArray("bms_admin_master", "admin_id='$created_by'");
                ?>
              </div>
              <div class="col-md-6">
                <p><b>Created By :</b> <?= ($adminDataCreated) ? $adminDataCreated['admin_name'] . " (" . $adminDataCreated['country_code'] . " " . $d->encryptDecrypt("decrypt", $adminDataCreated['admin_mobile']) . ")" : 'Added By Client'; ?></p>
                <p><b>Created By Email:</b> <?php if ($created_by > 0) {
                                              echo $d->encryptDecrypt("decrypt", $adminDataCreated['admin_email']);
                                            } ?></p>
                <?php $admin_email = $d->encryptDecrypt("decrypt", $adminDataCreated['admin_email']); ?>
                <p><b>Created Date : </b>
                  <?php
                  if ($default_time_zone != "Asia/Kolkata") {
                    if (empty($default_time_zone) || !in_array($default_time_zone, timezone_identifiers_list())) {
                      $default_time_zone = 'Asia/Kolkata';
                    }
                    echo $d->change_timezone($feedback_date_time, $default_time_zone, 'd M Y h:i A');
                  } else {
                    echo date("d M Y h:i A", strtotime($feedback_date_time));
                  }
                  ?>
                </p>
                <?php if ($attachment != '') { ?>
                  <p><b>Attachment 1 : </b> <a href="../img/fin_support/<?php echo $attachment; ?>" data-fancybox="images" data-caption="">View</a></p>
                <?php } ?>
                <?php if ($attachment_2 != '') { ?>
                  <p><b>Attachment 2 : </b> <a href="../img/fin_support/<?php echo $attachment_2; ?>" data-fancybox="images" data-caption="">View </a></p>
                <?php } ?>
                <?php if ($video != '') { ?>
                  <p><b>Video :</b> <a target="_blank" href="../img/fin_support/<?php echo $video; ?>">View </a></p>
                <?php } ?>
                <?php if ($document != '') { ?>
                  <p><b>Document :</b> <a target="_blank" href="../img/fin_support/<?php echo $document; ?>">View </a></p>
                <?php } ?>
                <?php if ($read_by != 0) { ?>
                  <p><b>Read by :</b> <?php $adminData = $d->selectArray("bms_admin_master", "admin_id='$read_by'");
                                      echo $adminData['admin_name']; ?></p>
                  <p><b>Read at :</b> <?php if ($read_time != '') {
                                        if ($default_time_zone != "Asia/Kolkata") {
                                          if (empty($default_time_zone) || !in_array($default_time_zone, timezone_identifiers_list())) {
                                            $default_time_zone = 'Asia/Kolkata';
                                          }
                                          echo $d->change_timezone($read_time, $default_time_zone, 'd M Y h:i A');
                                        } else {
                                          echo date('d M Y H:i A', strtotime($read_time));
                                        }
                                      } ?></p>
                <?php }
                $dev_tat = "";
                if ($develeoper_assign_time != '' && $developer_solve_time != '') {
                  $time1 = new DateTime($develeoper_assign_time);
                  $time2 = new DateTime($developer_solve_time);
                  $dev_tat = $time1->diff($time2);
                }
                if ($dev_tat != "") {
                ?>
                  <p><b>Developer TAT :</b> <?= $dev_tat->format('%m months %d days %h hours %i minutes') ?></p>
                <?php } ?>
                <?php
                $feed_tat = "";
                if ($feedback_date_time != '' && $feedback_solve_time != '') {
                  $time1 = new DateTime($feedback_date_time);
                  $time2 = new DateTime($feedback_solve_time);
                  $feed_tat = $time1->diff($time2);
                }
                if ($feed_tat != "") {
                ?>
                  <p><b>Feedback TAT :</b> <?= $feed_tat->format('%m months %d days %h hours %i minutes') ?></p>
                <?php }

                $statusBadges = [
                  0 => "<span class='badge badge-warning'>Pending</span>",
                  1 => "<span class='badge badge-default'>In Progress</span>",
                  2 => "<span class='badge badge-success'>Solved</span>",
                  3 => "<span class='badge badge-warning'>On Hold</span>",
                  4 => "<span class='badge badge-danger'>Rejected</span>",
                  5 => "<span class='badge badge-success'>Closed by Developer</span>",
                  6 => "<span class='badge badge-danger'>Rejected by Developer</span>",
                  7 => "<span class='badge badge-danger'>Need More Specification</span>",
                  8 => "<span class='badge badge-danger'>Resolved in next update</span>"
                ];
                // $data['feedback_status']."asda";
                echo $statusBadges[$data['feedback_status']] ?? "<span class='badge badge-secondary'></span>";
                ?>
              </div>
              <div class="col-md-12">
                <?php
                if ((($data['isTicket'] == 1 && $data['feedback_status'] == 5) || $data['isTicket'] == 0) && $data['with_developer'] == 0 && $inquiry_type == 0 && $feedback_status != '2') {
                  echo "<button data-toggle='modal' data-target='#closeRemarksModal' title='Query Solved?' class='btn btn-primary btn-sm mx-1 d-inline-block' onclick='remarkFeedback({$data['feedback_id']}, \"{$data['email']}\")'><i class='fa fa-check'></i> Close Query</button>";
                }
                ?>
                <?php if ($isTicket == 2 && ($role_id == 1 || $bms_admin_data['is_developer']==1)) { ?>
                  <form class="d-inline-block" action="controller/feedbackController.php" method="post">
                    <input type="hidden" name="feedback_id" value="<?php echo $feedback_id; ?>">
                    <input type="hidden" name="society_id" value="<?php echo $society_id; ?>">
                    <input type="hidden" name="isTicket">
                    <button type="submit" title="Generate Ticket - By <?php echo $forwardBy['admin_name'] ?>" name="" class="btn btn-warning btn-sm form-btn"><i class="fa fa-ticket fa-lg"> </i> Generate Ticket ?</button>
                  </form>
                  <!-- <form class="d-inline-block" action="controller/feedbackController.php" method="post">
                    <input type="hidden" name="feedback_id" value="<?php echo $feedback_id; ?>">    
                    <input type="hidden" name="society_id" value="<?php echo $society_id; ?>">    
                    <input type="hidden" name="rejectbyDeveloper">                 
                    <button type="submit" title="Reject Query - By <?php echo $forwardBy['admin_name'] ?>" name="" class="btn btn-danger btn-sm form-btn"><i class="fa fa-times fa-lg"> </i> Reject Ticket ?</button>
                  </form> -->
                  <button data-toggle="modal" data-target="#rejectbyDeveloper" title="Reject by Developer?" class="btn btn-danger btn-sm" onclick="RejectByDeveloper('<?php echo $feedback_id; ?>','<?php echo $admin_email; ?>');"><i class="fa fa-times fa-lg"></i>Reject Ticket ?</button>
                <?php } ?>
                <?php if ($data['feedback_status'] != 5 && $data['feedback_status'] != 6  && $data['feedback_status'] != 2 && $data['isTicket'] == 1) { ?>

                  <button data-toggle="modal" data-target="#closebyDeveloper" title="Closed by Developer?" class="btn btn-primary btn-sm d-inline-block" onclick="DeveloperReply('<?php echo $feedback_id; ?>','<?php echo $email; ?>','<?= $society_id ?>','<?= $created_by ?>');"><i class="fa fa-check fa-lg"></i> Close Ticket ?</button>
                <?php } ?>
              </div>
            </div>

            <div class="row pt-2 pb-2">
              <?php if ($data['feedback_status'] != 2 && $data['isTicket'] == 1) { ?>
                <div class="col-sm-12 text-center">
                  <button data-toggle="modal" data-target="#replyModal" class="btn btn-primary btn-sm d-inline-block" onclick="replyFeedback(<?php echo $data['feedback_id']; ?>,'<?php echo $data['email']; ?>');"><i class="fa fa-reply"></i> Reply</button>
                </div>
              <?php } ?>
            </div>
            <section class="cd-timeline js-cd-timeline">
              <div class="cd-timeline__container">
                <?php
                $timelineQuery = $d->select("feedback_log_master", "feedback_id='$id'", "ORDER BY feedback_log_id DESC");
                $timelineRows = [];
                $adminIds = [];
                while ($timelineData = mysqli_fetch_array($timelineQuery)) {
                  $timelineRows[] = $timelineData;
                  $adminIds[] = (int)$timelineData['feedback_added_by'];
                }

                $adminById = [];
                $adminIds = array_unique(array_filter(array_map('intval', $adminIds)));
                if (!empty($adminIds)) {
                  $adminIdsIn = implode(',', $adminIds);
                  $qad = $d->select("bms_admin_master", "admin_id IN ($adminIdsIn)");
                  while ($adm = mysqli_fetch_array($qad)) {
                    $adminById[(int)$adm['admin_id']] = $adm;
                  }
                }

                foreach ($timelineRows as $timelineData) {
                  $prent_feedback_log_id = $timelineData['feedback_log_id'];
                ?>
                  <div class="cd-timeline__block js-cd-block <?php if ($timelineData['feedback_added_by'] != 0) {
                                                                echo "floatRight";
                                                              } ?>">
                    <div class="cd-timeline__img cd-timeline__img--picture js-cd-img text-center ">
                      <img src="../img/fav.png">
                    </div>

                    <div class="cd-timeline__content js-cd-content" style="border: 1px solid gray;">
                      <p>
                        <?php
                        $adminData = $adminById[(int)$timelineData['feedback_added_by']] ?? [];
                        $feedback_log_attachment = $timelineData['feedback_log_attachment'];
                        ?>
                        <img class="rounded-circle" id="blah" onerror="this.src='img/user.png'" src="../img/profile/<?php echo $adminData['admin_profile'] ?? ''; ?>" width="30" height="30" src="#" alt="your image" class='profile' />
                        <?php if ($timelineData['log_added_type'] == 1) {
                          echo $timelineData['client_name'];
                        } else {
                          echo $adminData['admin_name'] ?? '';
                        } ?>
                        <?php if ($timelineData['feedback_msg_status'] != 1) { ?> <img width="10" src="../img/check1.png" class="float-right mx-2"> <?php } ?>
                        <?php
                        $feedbackTime = strtotime($timelineData['feedback_log_date']);
                        $currentTime = time();
                        $timeDiff = $currentTime - $feedbackTime;
                        if ($timelineData['feedback_log_date'] && $timelineData['feedback_added_by'] != 0 && $timeDiff <= 600) { ?>
                      <form method="POST" action="./controller/feedbackController.php" class="d-inline-block float-right">
                        <input type="hidden" name="feedback_log_id" value="<?php echo $prent_feedback_log_id; ?>">
                        <input type="hidden" name="feedback_id" value="<?php echo $data['feedback_id']; ?>">
                        <input type="hidden" name="deleteFeedbackReply" value="deleteFeedbackReply">
                        <input type="hidden" name="csrf" value="<?php echo $_SESSION["token"]; ?>">
                        <?php echo $d->feedbackListHiddenInputs(); ?>
                        <button type="submit" class="btn btn-sm btn-danger deleteButton form-btn ">
                          <i class="fa fa-trash-o"></i>
                        </button>
                      </form>
                    <?php } ?>
                    </p>
                    <h6 style="word-wrap: break-word;"><?php echo $timelineData['feedback_log']; ?></h6>
                    <div style="word-wrap: break-word;white-space: pre-line;"><?php echo $timelineData['feedback_log_msg']; ?></div><br>
                    <?php
                    $IssueType = '';
                    if ($timelineData['issue_type'] == 0) {
                      $IssueType = '';
                    } elseif ($timelineData['issue_type'] == 1) {
                      $IssueType = 'Bug';
                    } elseif ($timelineData['issue_type'] == 2) {
                      $IssueType = 'Configuration Issue';
                    } elseif ($timelineData['issue_type'] == 3) {
                      $IssueType = 'Training Issue';
                    } elseif ($timelineData['issue_type'] == 4) {
                      $IssueType = 'Issue Not Found';
                    } elseif ($timelineData['issue_type'] == 5) {
                      $IssueType = 'Change Request by Client';
                    } else if ($timelineData['issue_type'] == 6) {
                      $IssueType = "Device Specific Issue";
                    } else if ($timelineData['issue_type'] == 7) {
                      $IssueType = "Data Delete Request";
                    } else if ($timelineData['issue_type'] == 8) {
                      $IssueType = "Not an issue";
                    } else if ($timelineData['issue_type'] == 9) {
                      $IssueType = "Internet Connectivity Issue";
                    }
                    ?>
                    <div style="word-wrap: break-word;white-space: pre-line;"><?= $IssueType ?></div><br>
                    <?php
                    if (!empty($feedback_log_attachment)) {
                      $ext = pathinfo($feedback_log_attachment, PATHINFO_EXTENSION);
                      $videoFormats = ["mp4" => "video/mp4", "webm" => "video/webm", "avi" => "video/x-msvideo", "wmv" => "video/x-ms-wmv"];
                      if (array_key_exists($ext, $videoFormats)) { ?>
                        <a data-fancybox="images" data-caption="Photo Name: <?php echo $feedback_log_attachment; ?>" href="../img/fin_support/<?php echo $feedback_log_attachment; ?>" target="_blank">
                          <video width="50%" controls>
                            <source src="../img/fin_support/<?php echo $feedback_log_attachment; ?>" type="<?php echo $videoFormats[$ext]; ?>">Your browser does not support the video tag.
                          </video>
                        </a>
                      <?php
                      } elseif ($ext == "csv") {
                      ?>
                        <a download="<?php echo $feedback_log_attachment; ?>" href="../img/fin_support/<?php echo $feedback_log_attachment; ?>" target="_blank"><img class="lazyload" src='../img/ajax-loader.gif' width="100" data-src="../img/fin_support/csvShow.svg"></a>
                      <?php
                      } else {
                      ?>
                        <a data-fancybox="images" data-caption="Photo Name : <?php echo $feedback_log_attachment; ?>" href="../img/fin_support/<?php echo $feedback_log_attachment; ?>" target="_blank"><img class="lazyload" src='../img/ajax-loader.gif' width="100" data-src="../img/fin_support/<?php echo $feedback_log_attachment; ?>"></a>
                      <?php
                      }
                      ?>
                    <?php } ?>

                    <span class="cd-timeline__date"><?php echo date('M d H:i A', strtotime($timelineData['feedback_log_date'])); ?></span>
                    </div>
                  </div>
                <?php } ?>
              </div>
            </section>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  function replyFeedback(feedback_id, email) {
    $('#feedback_id').val(feedback_id);
    $('#feedback_email').val(email);
  }
</script>
<div class="modal fade" id="rejectbyDeveloper">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white">Developer Reject Feedback</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="RejectByDeveloperFeedbackFrm" action="controller/feedbackController.php" method="POST" enctype="multipart/form-data">
          <input type="hidden" id="society_id" name="society_id" value="<?php echo $society_id; ?>">
          <input type="hidden" id="developer_feedback_id" name="feedback_id">
          <input type="hidden" id="developer_feedback_email" name="feedback_email">
          <div class="form-group row">
            <label for="input-10" class="col-sm-4 col-form-label">Reason <span class="required">*</span></label>
            <div class="col-sm-8">
              <textarea placeholder="Please mention how to reject this query" maxlength="2500" style="resize: vertical;" class="form-control" required="" name="resion"></textarea>
            </div>
          </div>
          <div class="form-group row">
            <label for="input-10" class="col-sm-4 col-form-label">Attachment </label>
            <div class="col-sm-8">
              <input type="file" accept="image/*,.pdf" id="reject_attachment" name="reject_attachment" class="form-control">
            </div>
          </div>
          <div class="form-footer text-center">
            <input type="hidden" name="rejectbyDeveloper" value="rejectbyDeveloper">
            <button type="submit" class="btn btn-primary"><i class="fa fa-check-square-o"></i> Submit</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="replyModal">
  <div class="modal-dialog modal-xl">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white">Reply Feedback</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="setReplyForm">
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="closebyDeveloper">
  <div class="modal-dialog modal-xl">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white">Developer Reply Feedback</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="developerSetReplyForm">
        <form id="developerReplyFeedbackFrm" action="controller/feedbackController.php" method="post" enctype="multipart/form-data">
          <input type="hidden" name="closebyDeveloper">
          <input type="hidden" id="developerSociety_id" name="society_id">
          <input type="hidden" id="developerFeedback_id" name="feedback_id">
          <input type="hidden" id="developerFeedback_created_by" name="feedback_created_by">
          <input type="hidden" id="csrf" name="csrf" value="<?php echo $csrf; ?>">
          <div class="form-group row">
            <label for="input-10" class="col-sm-2 col-form-label">Reply</label>
            <div class="col-sm-10">
              <textarea maxlength="2500" cols="10" rows="6" style="resize: vertical;" class="form-control" name="reply" id="dev_reply"></textarea>
            </div>
          </div>
          <div class="form-group row">
            <label for="input-10" class="col-sm-2 col-form-label">Attachment File</label>
            <div class="col-sm-10">
              <input class="form-control-file border" type="file" name="attachment">
            </div>
          </div>
          <div class="form-group row">
            <label for="input-10" class="col-sm-2 col-form-label">Send Mail</label>
            <div class="col-sm-10">
              <select class="form-control single-select" name="send_mail">
                <option value="0">Client and Support Executive</option>
                <option value="1">Support Executive</option>
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label for="input-10" class="col-sm-2 col-form-label">Issue <span class="required">*</span></label>
            <div class="col-sm-10">
              <select class="form-control single-select" name="issue_type" required>
                <option value="">---Select---</option>
                <option value="1">Bug</option>
                <option value="2">Configuration Issue</option>
                <option value="3">Training Issue</option>
                <option value="4">Issue Not Found</option>
                <option value="5">Change Request by Client</option>
                <option value="6">Device Specific Issue</option>
                <option value="7">Data Delete Request</option>
                <option value="8">Not an issue</option>
                <option value="9">Internet Connectivity Issue</option>
              </select>
            </div>
          </div>
          <div class="form-footer text-center">
            <input type="hidden" name="previousURL" value="feedbackTimeline">
            <?php echo $d->feedbackListHiddenInputs(); ?>
            <button type="submit" class="btn btn-primary"><i class="fa fa-check-square-o"></i>Submit</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="editPlatform">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white">Reply Feedback</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="replyFeedbackFrm" action="controller/feedbackController.php" method="post" enctype="multipart/form-data">
          <input type="hidden" id="society_id" name="society_id" value="<?php echo $data['society_id']; ?>">
          <input type="hidden" id="feedback_id" name="feedback_id" value="<?php echo $data['feedback_id'] ?>">
          <input type="hidden" id="feedback_added_by" name="feedback_added_by" value="<?= $data['created_by'] ?>">
          <input type="hidden" name="previousURL" value="feedbackTimeline?id=<?php echo $data['feedback_id'] ?>">
          <?php echo $d->feedbackListHiddenInputs(); ?>
          <div class="form-group row">
            <label for="input-10" class="col-sm-4 col-form-label">Platform <span class="text-danger">*</span></label>
            <div class="col-sm-8">
              <select name="platform" class="form-control" required="">
                <option value="">--Platform--</option>
                <option <?php echo ($platform == '0') ? "selected" : ""; ?> value="0">Other</option>
                <option <?php echo ($platform == '1') ? "selected" : ""; ?> value="1">Android</option>
                <option <?php echo ($platform == '2') ? "selected" : ""; ?> value="2">iOS</option>
                <option <?php echo ($platform == '3') ? "selected" : ""; ?> value="3">Web</option>
                <option <?php echo ($platform == '4') ? "selected" : ""; ?> value="4">CRM</option>
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label for="input-10" class="col-sm-4 col-form-label">Module type <span class="text-danger">*</span></label>
            <div class="col-sm-8">
              <select name="module_type" class="form-control single-select" required="">
                <option value="">--Module type--</option>
                <?php foreach ($moduleTypes as $value => $label): ?>
                  <option <?php echo ($value == $module_type) ? "selected" : ""; ?> value="<?php echo $value; ?>"><?php echo $label; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="form-footer text-center">
            <input type="hidden" name="editFeedbackPlatform" value="editFeedbackPlatform">
            <button type="submit" class="btn btn-primary"><i class="fa fa-check-square-o"></i> Edit</button>
          </div>

        </form>
      </div>

    </div>
  </div>
</div>
<div class="modal fade" id="closeRemarksModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white">Closing Remarks</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="remarkFeedbackFrm" action="controller/feedbackController.php" method="POST">
          <input type="hidden" id="society_id" name="society_id" value="<?php echo $society_id; ?>">
          <input type="hidden" id="feedback_remark_id" name="feedback_id">
          <input type="hidden" id="feedback_remark_email" name="feedback_email">
          <div class="form-group row">
            <label for="input-10" class="col-sm-4 col-form-label">Close By <span class="required">*</span></label>
            <div class="col-sm-8">
              <select class="form-control single-select" required="" name="closing_by">
                <option value="">-- Select --</option>
                <option value="By Whatsapp">By Whatsapp</option>
                <option value="By Call">By Call</option>
                <option value="By Text Message">By Text Message</option>
                <option value="By Mail">By Mail</option>
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label for="input-10" class="col-sm-4 col-form-label">Enter Last Message <span class="required">*</span></label>
            <div class="col-sm-8">
              <textarea placeholder="Please mention how to this query resolved" class="form-control" rows="6" cols="30" maxlength="2500" style="resize: vertical;" required="" name="closing_remarks" id="closing_remarks"></textarea>
            </div>
          </div>
          <div class="form-footer text-center">
            <?php echo $d->feedbackListHiddenInputs(); ?>
            <input type="hidden" name="remarkFeedback" value="remarkFeedback">
            <button type="submit" class="btn btn-primary"><i class="fa fa-check-square-o"></i> Submit</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script src="assets/js/jquery.min.js"></script>
<script type="text/javascript">
  function openLogin(url) {
    window.open(url);
  }

  function DeveloperReply(feedback_id, email, society_id, created_by) {
    $("#developerSociety_id").val(society_id);
    $("#developerFeedback_id").val(feedback_id);
    $("#developerFeedback_created_by").val(created_by);
    $("#developerEmail").val(email);
  }

  function RejectByDeveloper(feedback_id, email) {
    $('#developer_feedback_id').val(feedback_id);
    $('#developer_feedback_email').val(email);
  }
</script>
<script>
  $("#attachmentFile").change(function() {
    var val = $(this).val();
    switch (val.substring(val.lastIndexOf('.') + 1).toLowerCase()) {
      case 'gif':
      case 'jpg':
      case 'jpeg':
      case 'png':
      case 'mp4':
      case 'webm':
      case 'avi':
      case 'wmv':
      case 'csv':
      case 'docx':
        break;
      default:
        $(this).val('');
        swal({
          icon: "error",
          text: "Only formats are allowed : gif, jpeg, jpg, png, mp4, webm, avi, wmv, csv, docx",
          timer: 4000
        });
        break;
    }
  });
</script>
<script>
  let ticketClosingReasons = [];
  let ticketReplySuggestions = [];

  fetch('ajaxGetSuggestions.php', {
      method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
      if (data.status) {
        ticketClosingReasons = data.data.ticketClosingReasons;
        ticketReplySuggestions = data.data.ticketReplySuggestions;

        initializeAutocomplete();
      } else {
        console.error("Failed to fetch suggestions:", data.message);
      }
    })
    .catch(error => {
      console.error("Error loading suggestions:", error);
    });

  function initializeAutocomplete() {
    $(function() {
      $('#reply').autocomplete({
        source: ticketReplySuggestions,
        minLength: 0,
        autoFocus: true
      });

      $('#dev_reply').autocomplete({
        source: ticketClosingReasons,
        minLength: 0,
        autoFocus: true
      });
    });
  }

  function replyFeedback(feedback_id, email) {
    let previousURL = "feedbackTimeline?id=" + feedback_id;
    $.ajax({
        url: 'ajaxGetReplyForm.php',
        type: 'POST',
        data: {
          feedback_id: feedback_id,
          email: email,
          previousURL: previousURL,
          csrf: csrf
        }
      })
      .done(function(response) {
        $('#setReplyForm').html(response);

        $('#reply').autocomplete({
          source: ticketReplySuggestions,
          minLength: 0,
          autoFocus: true
        });
      });
  }

  function remarkFeedback(feedback_id, email, name) {
    $('#feedback_remark_id').val(feedback_id);
    $('#feedback_remark_email').val(email);
  }
</script>