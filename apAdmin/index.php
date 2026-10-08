<?php 
session_start();
error_reporting(0);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
include 'common/object.php';
$_SESSION["token"] = md5(uniqid(mt_rand(), true));
$server_output=json_decode($server_output,true);
if (isset($server_output['language'])) {
    $LangarrayCount= count($server_output['language']);
}

// already loged in check 
if(isset($_COOKIE['master_token'])){ 
  $token=$_COOKIE['master_token'] ?? null;
  $key = $d->get_encrypt_key(); 
  try {
    $decoded = JWT::decode($token, $key, ['HS256']);
    if (time() < $decoded->exp) {
      header("location:./welcome");
      exit();
    }
  } catch (Exception $e) {
    
  }
}

$countryCode = $_COOKIE['country_code'];
$turnstile = $d->turnstile_site();
 ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
  <meta name="description" content=""/>
  <meta name="author" content=""/>
  <title>Login | <?php echo $d->app_name();?> </title>
  <!--favicon-->
  <link rel="icon" href="../img/fav.png" type="image/png">
  <!-- Bootstrap core CSS-->
  <?php include 'common/colours.php'; ?>
  <link href="assets/css/bootstrap.min.css" rel="stylesheet"/>
  <!-- animate CSS-->
  <link href="assets/css/animate.css" rel="stylesheet" type="text/css"/>
  <!-- Icons CSS-->
  <link href="assets/css/icons.css" rel="stylesheet" type="text/css"/>
  <!-- Custom Style-->
  <link href="assets/css/app-style.css" rel="stylesheet"/>
   <!--Select Plugins-->
  <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet"/>
   <!--Select Plugins-->
  <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet"/>
  
</head>

<body class="bg-dark" style="overflow-x:hidden;">
<div class="ajax-loader">
  <img src="../img/ajax-loader.gif" class="img-responsive" />
</div>
 <!-- Start wrapper-->
 <div id="wrapper" class="container-fluid">
	<div class="card card-authentication1 mx-auto my-5">
		<div class="card-body">
		 <div class="card-content p-2">
		 	<div class="text-center">
		 		<img src="../img/logo.png" alt="<?php echo $d->app_name();?>  Logo" width="70">
		 	</div>
		 	<?php //IS_576  signupForm to loginFrm?> 
		    <form id="loginFrm" action="controller/loginController.php" method="post">
          <input type="hidden" name="csrf" value="<?php echo $_SESSION["token"]; ?>" />
        
        <div class="row">
        <label for="exampleInputUsername"  class="col-sm-12 col-form-label">Mobile/Email</label>
         <div class="position-relative has-icon-right col-sm-4 col-3">
          <select  name="country_code" class="form-control single-select" id="country_code" required="">  
                <?php include 'country_code_option_list.php'; ?>
          </select>
          <input type="hidden" name="country_code" id="verify_country_code">
        </div>
         <div class="position-relative has-icon-right col-sm-8 col-9">
            <input required="" autocomplete="off" type="text" id="mobile" class="form-control  loginNumber" name="mobile" placeholder="Enter Mobile Number or Email" value="">
          </div>
        </div>
        <div class="row passwordDiv" >
        <label for="exampleInputPassword"  class="col-sm-12 col-form-label">Password</label>
         <div class="position-relative has-icon-right col-sm-12">
          <input required="" autocomplete="off" type="password" id="password" class="form-control " name="inputPass" placeholder="Enter Your Password">
         </div>
        </div>
        <div class="row otpDiv" >
          <label for="exampleInputPassword"  class="col-sm-12 col-form-label">Enter OTP </label>
          <div class="position-relative has-icon-right col-sm-12">
            <input required="" autocomplete="off" type="text" id="otp_web" class="form-control input-shadow onlyNumber" inputmode="numeric" name="otp_web" placeholder="Enter Six Digit OTP" maxlength="6" onkeyup="loginMatchOTP();" >
          </div>
        </div>
        
      <div class="row">
       
       <div class="form-group col-12 text-right">
        <span class="otpDiv">
          <a class="loginOTP" href="#">Resend OTP ?</a>
        </span>
        <span class="passwordDiv">
          <a href="forgot.php">Forgot Password ?</a>
        </span>
       </div>
      </div>
      <?php include 'common/turnstile_widget.php'; ?>
      <div class="row mb-2 passwordDiv" >
        <div class="col-sm-6 col-6">
          <button type="button" class="loginOTP btn btn-warning shadow-primary btn-block waves-effect waves-light">Get OTP</button>
        </div>
        <div class="col-sm-6 col-6">
          <button type="submit" class="btn btn-primary shadow-primary btn-block waves-effect waves-light">Sign In</button>
        </div>
       
      </div>
      
      <div class="row mb-2 otpDiv" <?php if($_SESSION['otpDiv']!=1) { echo "display:none";}?> >
       <button type="submit" class="btn btn-primary shadow-primary btn-block waves-effect waves-light">Verify</button>
      </div>
      <div class="row otpDiv"  <?php if($_SESSION['otpDiv']!=1) { echo "display:none";}?> >
        <p class="text-muted mb-0">Return to the <a  id="loginPassword" href="#"> Sign In With Password ?</a></p>
      </div>
      
       </form>
		   </div>
		  </div>
		  
	     </div>
    
     <!--Start Back To Top Button-->
    <a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>
    <!--End Back To Top Button-->
	</div><!--wrapper-->
	
  <!-- Bootstrap core JavaScript-->
  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/popper.min.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>
  <script src="assets/plugins/jquery-validation/js/jquery.validate.min.js"></script>
  <?php include 'common/turnstile_scripts.php'; ?>

  <!--Sweet Alerts -->
  <script src="assets/plugins/alerts-boxes/js/sweetalert.min.js"></script>
  <script src="assets/plugins/alerts-boxes/js/sweet-alert-script.js"></script>
   <!--Select Plugins Js-->
  <script src="assets/plugins/select2/js/select2.min.js"></script>
   <!--Multi Select Js-->
    <script src="assets/plugins/jquery-multi-select/jquery.multi-select.js"></script>
    <script src="assets/plugins/jquery-multi-select/jquery.quicksearch.js"></script>
    <script type="text/javascript" src="assets/js/select.js"></script>
<?php include 'common/alert.php';
  if (isset($_SESSION['otpDiv']) && $_SESSION['otpDiv']==true) {  ?>
    <script>
    $('.otpDiv').show();
    $('.passwordDiv').hide();
    </script>
 <?php  } else {  ?>
    <script>
    $('.otpDiv').hide();
    $('.passwordDiv').show();
    </script>
 <?php  }?>

 <script>
function loginMatchOTP(){
  var otp_web = $('#otp_web').val();
  otp_web = parseInt(otp_web);
  var mobile = $('#mobile').val();
  var csrf =$('input[name="csrf"]').val();
  // console.log(mobile);
  if(otp_web.toString().length ==6){
  $.ajax({
    url: "controller/loginController.php",
    cache: false,
    type: "POST",
    data: {
      mobile: mobile,
      otp_web: otp_web,
      checkLoginOTP: 'checkLoginOTP',
      csrf: csrf
    },
    success: function (response) {
      // console.log(response);
      if (response == 1) {
        swal("OTP Not Match.", {
         icon: "error",
        });
        $('#otp_web').val(""); 
       } }
  });
}
 }
</script>
  <script>
     var countryCode  = '<?php echo $countryCode; ?>';
      // document.getElementById('country_code').value = countryCode;
      jQuery(document).ready(function($){
      
      // var countryCode = $("#country_code").val();
      // $("#selected_country_code").val(countryCode);
        <?php if(isset($_SESSION['mobile'])){ ?>
    $('#success_info').text('OTP Sent to '+'<?php echo $_SESSION['mobile'];?>'+', Please Provide Correct OTP.');
          $('#verifyOTPFrm :input[name="verify_mobile"]').val('<?php echo $_SESSION['mobile'];?>');
          $('#verifyOTP').click();
    <?php } ?>
    
    $("#personal-info").validate();
   // validate signup form on keyup and submit

   // IS_576
   $('input').change(function(){
        this.value = $.trim(this.value);
    });
    $("#loginFrm").validate({
      errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) { 
            error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {     
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {                                      
                error.insertAfter(element);               // default
            }
        },
	        rules: {
	            mobile: {
	                required: true,
	                minlength: 3
	            },
	            inputPass: {
	                required: true,
	                minlength: 3
	            },
                otp_web: {
                    required: true,
                    minlength: 6,
                    maxlength: 6,
                },
	        },
	        messages: {
	            mobile: "Please enter your Mobile or Email",
	            inputPass: "Please enter your Password",
                otp_web: "Please enter 6 digit OTP"
	        },
           submitHandler: function(form) {
                if (!requireTurnstile()) {
                  return false;
                }
                $(':input[type="submit"]').prop('disabled', true);
                $(".ajax-loader").show();
                form.submit(); 
          }
    	});

	});

$(document).ready(function(){
  var country_code= $('#country_code').val();
  $('#verify_country_code').val(country_code);
});
 $('#country_code').on('change', function(){
  var country_code= $(this).val();
  $('#verify_country_code').val(country_code);
 });
 $('.loginOTP').on('click',function(e){

    e.preventDefault();

 var adminMobile= $('#mobile').val();
 var country_code= $('#country_code').val();

 if($.trim(adminMobile) ==''){
   swal("Error! Please enter your mobile number or email", {icon: "error",});
 } else {
   if (!requireTurnstile()) {
     return;
   }
   var turnstileResponse = $('input[name="cf-turnstile-response"]').val() || '';
   $(".ajax-loader").show();
  var csrf =$('input[name="csrf"]').val();
    $.ajax({
    url: 'controller/loginController.php',
    cache: false,
    data: {country_code:country_code,adminMobile : adminMobile,SendOPT:'yes',csrf:csrf,'cf-turnstile-response':turnstileResponse},
    type: "post",
    success: function(data) {
       $(".ajax-loader").hide();
      if(data==0){
        swal("Error! Please Enter Registered Mobile/Email!", { icon: "error", });
      }else if(data==3){
        swal("Security verification failed. Please try again.", { icon: "error" });
        resetTurnstileWidget();
      }else if(data==5){
        swal("Error! Your account is deactivated !", { icon: "error", });
      }  else if(data==2){
        swal("Error! Something Went Wrong, Please Try Again!", { icon: "error",  });
      } else {

         
          // $('#success_info').text('OTP Sent to '+adminMobile+', Please Provide in below textbox.');
          
          swal('OTP sent successfully to '+data, {icon: "success",timer: 2000,timerProgressBar: true,});

          $('#verifyOTPFrm :input[name="verify_mobile"]').val(adminMobile);
          $('.passwordDiv').hide();
          $('.otpDiv').show();
          $('#verify_country_code').val(country_code);
          $('#country_code').prop('disabled', true);
          $('#mobile').prop('readonly', true);
          resetTurnstileWidget();
      }
        
    },
    error: function(data) {
       swal("Error! Something Went Wrong, Please Try Again!", { icon: "error",  });
    }
  });
 }

 });

 $('#loginPassword').on('click',function(e){
    $('.passwordDiv').show();
    $('.otpDiv').hide();
  });

$("#verifyOTPFrm").validate({
          rules: {
              otp_web: {
                  required: true,
                  minlength: 4
              } 
          },
          messages: {
              mobile: "Please enter OTP" 
          },
           submitHandler: function(form) {
                $(':input[type="submit"]').prop('disabled', true);
                $(".ajax-loader").show();
                form.submit(); 
          }
      });



 $(".onlyNumber").keydown(function (e) {
    // Allow: backspace, delete, tab, escape, enter and .
    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
         // Allow: Ctrl+A, Command+A
        (e.keyCode == 65 && ( e.ctrlKey === true || e.metaKey === true ) ) || 
         // Allow: home, end, left, right, down, up
        (e.keyCode >= 35 && e.keyCode <= 40)) {
             // let it happen, don't do anything
             return;
    }
    // Ensure that it is a number and stop the keypress
    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
        e.preventDefault();
    }
});	 

	 //IS_576

    </script>
  <script src="https://www.gstatic.com/firebasejs/8.3.1/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.3.1/firebase-messaging.js"></script>
<!-- <script src="/__/firebase/init.js"></script> -->
  
</body>

</html>
