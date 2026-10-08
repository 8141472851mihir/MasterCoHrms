<a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>
<!--End Back To Top Button-->
<div class="modal fade" id="notification">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white">Send Notification</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="notificationValidation" action="controller/sendNotificationController.php" method="post" enctype="multipart/form-data">
          <div class="form-group row">
            <label for="input-10" class="col-sm-4 col-form-label">Company <span class="text-danger">*</span></label>
            <div class="col-sm-8">
              <?php
              // $notisocities = $d->select('society_master', "society_status='0'", 'ORDER BY society_name ASC');
              ?>

              <select name="noti_society_id" id="noti_society_id" required="" class="form-control single-select">
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label for="input-10" class="col-sm-4 col-form-label">Title <span class="text-danger">*</span></label>
            <div class="col-sm-8">
              <input required="" maxlength="200" id="noti_title" type="text" name="noti_title" class="form-control">
            </div>
          </div>
          <div class="form-group row">
            <label for="input-10" class="col-sm-4 col-form-label">Description <span class="text-danger">*</span></label>
            <div class="col-sm-8">
              <textarea maxlength="500" required="" name="noti_description" class="form-control"></textarea>
            </div>
          </div>

          <div class="form-group row">
            <label for="input-10" class="col-sm-4 col-form-label">Image </label>
            <div class="col-sm-8">
              <input type="file" accept="image/*" maxlength="500" name="noti_notiUrl" class="form-control"></textarea>
            </div>
          </div>
          <div class="form-group row">
            <label for="input-10" class="col-sm-4 col-form-label">Send to <span class="text-danger">*</span></label>
            <div class="col-sm-8">
              <select name="noti_send_to" required="" class="form-control">
                <option value="Users">Employees</option>
                <option value="Admin">Admins</option>
              </select>
            </div>
          </div>

          <div class="form-footer text-center">
            <button type="submit" name="sendNoti" value="sendNoti" class="btn  btn-success"><i class="fa fa-check-square-o"></i> Send</button>
          </div>

        </form>
      </div>

    </div>
  </div>
</div>
</div>

<div class="modal fade" id="webNotification">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white">Send Notification</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="notificationWebValidation" action="controller/sendWebNotiController.php" method="post" enctype="multipart/form-data">
          <div class="form-group row">
            <label for="input-10" class="col-sm-4 col-form-label">Title <span class="text-danger">*</span></label>
            <div class="col-sm-8">
              <input required="" maxlength="200" autocomplete="off" id="web_noti_title" type="text" name="noti_title" class="form-control">
            </div>
          </div>
          <div class="form-group row">
            <label for="input-10" class="col-sm-4 col-form-label">Description <span class="text-danger">*</span></label>
            <div class="col-sm-8">
              <textarea maxlength="500" required="" name="noti_description" class="form-control"></textarea>
            </div>
          </div>

          <div class="form-group row">
            <label for="input-10" class="col-sm-4 col-form-label">Image </label>
            <div class="col-sm-8">
              <input type="file" accept="image/*" maxlength="500" name="noti_notiUrl" class="form-control-file border">
            </div>
          </div>

          <div class="form-footer text-center">
            <input type="hidden" name="sendWebNoti">
            <button type="submit" class="btn  btn-success"><i class="fa fa-check-square-o"></i> Send</button>
          </div>

        </form>
      </div>

    </div>
  </div>
</div>

<!-- <style>
  #popup {
      display: none;
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: white;
      padding: 20px;
      box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.5);
      z-index: 1000;
  }
  #overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      z-index: 999;
  }
</style>
<div id="overlay"></div>
<div id="popup">
    <p>This is your 2-hour reminder popup!</p>
    <button id="closePopup">Close</button>
</div> -->

<?php include_once 'webNotifications.php'; ?>
<script>
  var csrf = "<?php echo $_SESSION["token"]; ?>";
</script>
<!-- Bootstrap core JavaScript-->
<script src="assets/js/jquery.min.js"></script>
<!-- <script src="assets/js/timeout.js"></script> -->
<script src="assets/js/popper.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/jquery-ui.js"></script>

<!-- simplebar js -->
<script src="assets/plugins/simplebar/js/simplebar.js"></script>
<!-- waves effect js -->
<script src="assets/js/waves.js"></script>
<!-- sidebar-menu js -->
<script src="assets/js/sidebar-menu.js"></script>
<!-- Custom scripts -->
<script src="assets/js/app-script.js"></script>

<!--Data Tables js-->
<script src="assets/plugins/bootstrap-datatable/js/jquery.dataTables.min.js"></script>
<script src="assets/plugins/bootstrap-datatable/js/dataTables.bootstrap4.min.js"></script>
<script src="assets/plugins/bootstrap-datatable/js/dataTables.buttons.min.js"></script>
<script src="assets/plugins/bootstrap-datatable/js/buttons.bootstrap4.min.js"></script>
<script src="assets/plugins/bootstrap-datatable/js/jszip.min.js"></script>
<script src="assets/plugins/bootstrap-datatable/js/pdfmake.min.js"></script>
<script src="assets/plugins/bootstrap-datatable/js/vfs_fonts.js"></script>
<script src="assets/plugins/bootstrap-datatable/js/buttons.html5.min.js"></script>
<script src="assets/plugins/bootstrap-datatable/js/buttons.print.min.js"></script>
<script src="assets/plugins/bootstrap-datatable/js/buttons.colVis.min.js"></script>
<!-- ColReorder JS -->
<script src="assets/plugins/bootstrap-datatable/js/dataTables.colReorder.min.js"></script>

<!--notification js -->
<script src="assets/plugins/notifications/js/lobibox.min.js"></script>
<script src="assets/plugins/notifications/js/notifications.min.js"></script>
<script src="assets/plugins/notifications/js/notification-custom-script.js"></script>


<!--Sweet Alerts -->
<script src="assets/plugins/alerts-boxes/js/sweetalert.min.js"></script>
<script src="assets/plugins/alerts-boxes/js/sweet-alert-script.js"></script>

<!-- Chart js -->
<script src="assets/plugins/Chart.js/Chart.min.js"></script>
<!--Peity Chart -->
<script src="assets/plugins/peity/jquery.peity.min.js"></script>
<!-- Index js -->
<!-- <script src="assets/js/index.js"></script> -->
<!--Lightbox-->
<script src="assets/plugins/fancybox/js/jquery.fancybox.min.js"></script>

<script src="assets/js/custom.js"></script>


<script src="assets/plugins/summernote/dist/summernote-bs4.min.js"></script>

<?php include_once 'common/alert.php'; ?>

<!--Form Validatin Script-->
<script src="assets/plugins/jquery-validation/js/jquery.validate.min.js"></script>
<script type="text/javascript" src="assets/js/validate.js"></script>

<!--Select Plugins Js-->
<script src="assets/plugins/select2/js/select2.min.js"></script>
<!--Inputtags Js-->
<script src="assets/plugins/inputtags/js/bootstrap-tagsinput.js"></script>

<!--Bootstrap Datepicker Js-->
<script src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>


<!--Multi Select Js-->
<script src="assets/plugins/jquery-multi-select/jquery.multi-select.js"></script>
<script src="assets/plugins/jquery-multi-select/jquery.quicksearch.js"></script>
<script type="text/javascript" src="assets/js/select.js"></script>

<!--material date picker js-->
<script src="assets/plugins/material-datepicker/js/moment.min.js"></script>
<script src="assets/plugins/material-datepicker/js/bootstrap-material-datetimepicker.min.js"></script>
<script src="assets/plugins/material-datepicker/js/ja.js"></script>

<script src="assets/js/datepicker.js"></script>
<script type="text/javascript" src="assets/js/daterangepicker.min.js"></script>

<script type="text/javascript" src="assets/js/lazyload.js"></script>
<script type="text/javascript">
  window.addEventListener("load", function(event) {
    lazyload();
  });
</script>



<?php if (isset($videoguide)) { ?>
  <script>
    (function($) {
      $.fn.checkFileType = function(options) {
        var defaults = {
          allowedExtensions: [],
          success: function() {},
          error: function() {}
        };
        options = $.extend(defaults, options);
        return this.each(function() {
          $(this).on('change', function() {
            var value = $(this).val(),
              file = value.toLowerCase(),
              extension = file.substring(file.lastIndexOf('.') + 1);
            if ($.inArray(extension, options.allowedExtensions) == -1) {
              options.error();
              $(this).focus();
            } else {
              options.success();
            }
          });
        });
      };

    })(jQuery);
    $(document).ready(function() {
      $('#file1,#file2').checkFileType({
        allowedExtensions: ['mp4'],
        success: function() {},
        error: function() {
          $('#file1,#file2').val('');
          swal({
            title: "The File You Selected is Not a Valid Video File",
            icon: "error",
            text: "Please Upload mp4 Extension Video",
            timer: 4000
          });
        }
      });
      $('#thumbnail,#thumbnail1').checkFileType({
        allowedExtensions: ['jpg', 'jpeg', 'png', 'JPG', 'JPEG'],
        success: function() {},
        error: function() {
          $('#thumbnail,#thumbnail1').val('');
          swal({
            title: "Invalid File Extension",
            icon: "error",
            text: "Please Upload jpg | png | jpeg | JPG | JPEG Extension File",
            timer: 4000
          });
        }
      });
      $('#file1,#file2').bind('change', function() {
        if (this.files[0].size > '500000000') {
          alert('Please Select File Smallet Than 500 MB in Size.');
          $('#file1,#file2').val('');
        }
      });
    });
    $('#videoAddForm').submit(function() {
      var title1 = $.trim($('#title1').val());
      var about = $.trim($('#about').val());
      var thumbnail = $('#thumbnail').val();
      var file1 = $('#file1').val();
      if (title1 == '' || about == '' || thumbnail == '' || file1 == '') {
        swal({
          title: "Please Complete All Mandotory Fields",
          icon: "error",
          text: "* are the mandotory fields",
          timer: 4000
        });
        return false;
      } else {
        $('#videoUpload').attr('disabled', 'disabled');
        $('#videoUpload').text('Uploading...');
        return true;
      }
      return false;
    });

    function deleteVideo(id) {
      swal({
          title: "Are you sure to Delete this Video?",
          text: "Once deleted, you will not be able to recover this data!",
          icon: "warning",
          buttons: true,
          dangerMode: true,
        })
        .then((willDelete) => {
          if (willDelete) {
            $.ajax({
              url: "controller/videoController.php",
              cache: false,
              type: "POST",
              data: {
                video_id: id,
                csrf: csrf
              },
              success: function(response) {
                if (response == 1) {
                  document.location.reload(true);
                }
              }
            });
          }
        });
    }

    function editVideo(id, language, title, description, thumbnail, video) {
      $('#title2').val(title);
      $('#about1').val(description);
      $('#videoid').val(id);
      $('#language1').val(language);
      $('#thumbfile').attr('src', 'img/videos/thumbnails/' + thumbnail);
      $('#vidfile').attr('src', 'img/videos/' + video);
    }
    $('#videoEditForm').submit(function() {
      var title2 = $.trim($('#title2').val());
      var about1 = $.trim($('#about1').val());
      var thumbnail1 = $('#thumbnail1').val();
      var file2 = $('#file2').val();
      if (title2 == '' || about1 == '') {
        swal({
          title: "Please Complete All Mandotory Fields",
          icon: "error",
          text: "* are the mandotory fields",
          timer: 4000
        });
        return false;
      } else {
        $('#videoEditUpload').attr('disabled', 'disabled');
        $('#videoEditUpload').text('Updating...');
        return true;
      }
      return false;
    });
  </script>
<?php } ?>

<script type="text/javascript">
  $('form').append('<input type="hidden" name="csrf" value="<?php echo $_SESSION["token"]; ?>" />');
</script>
<script type="text/javascript">
  function readNotification(link, id) {
    $.ajax({
      url: "controller/notificationController.php",
      cache: false,
      type: "POST",
      data: {
        id: id,
        readNoti: "readNoti"
      },
      success: function(response) {
        window.location = link;
      }
    });
  }

  $(window).load(function() {
    $("#spinner").fadeOut("slow");
  });

  window.onpageshow = function(event) {
    if (event.persisted) {
      location.reload();
    }
  };
</script>
<script>
  $(document).ready(function() {
    $('#notification').on('shown.bs.modal', function() {
      $.ajax({
        url: './companyListAjax.php',
        type: 'POST',
        data: {
          getCompanyList: "getCompanyList",
          csrf: csrf,
        },
        dataType: 'json',
        success: function(response) {
          if (response.success == true) {
            let societyDropdown = $('#noti_society_id');
            societyDropdown.empty();
            societyDropdown.append(`<option value="" selected disabled>Select</option>`);
            response.data.societies.forEach(function(society) {
              societyDropdown.append(
                `<option value="${society.society_id}">${society.society_name} (${society.city_name})</option>`
              );
            });
          } else {}
        },
        error: function(xhr, status, error) {},
      });
    });
  });
</script>


<script>
  // $(document).ready(function() {
  //   function showPopup() {
  //     $("#overlay, #popup").fadeIn();
  //     localStorage.setItem("lastPopupTime", Date.now());
  //   }

  //   function checkPopup() {
  //     let lastPopupTime = localStorage.getItem("lastPopupTime");
  //     let currentTime = Date.now();
  //     let twoHours = 2 * 60 * 60 * 1000;
  //     if (!lastPopupTime || (currentTime - lastPopupTime) > twoHours) {
  //       showPopup();
  //     }
  //   }
  //   checkPopup();
  //   $("#closePopup").click(function() {
  //     $("#overlay, #popup").fadeOut();
  //   });
  //   setInterval(checkPopup, 60 * 1000);
  // });
</script>
</body>

</html>