<?php session_start();
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/
include_once 'lib/dao.php';
include 'lib/model.php';
$d = new dao();
$m = new model();
$turnstile = $d->turnstile_site();
error_reporting(0);
$_SESSION["token"] = md5(uniqid(mt_rand(), true));
$url=$_SESSION['url'] = $_SERVER['REQUEST_URI']; 
 ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
  <meta name="description" content=""/>
  <meta name="author" content=""/>
  <title>Set New Password | <?php echo $d->app_name();?> </title>
  <link rel="icon" href="../img/fav.png" type="image/x-icon">
  <!-- Bootstrap core CSS-->
  <link href="assets/css/bootstrap.min.css" rel="stylesheet"/>
  <!-- animate CSS-->
  <?php include 'common/colours.php'; ?>
  <link href="assets/css/animate.css" rel="stylesheet" type="text/css"/>
  <!-- Icons CSS-->
  <link href="assets/css/icons.css" rel="stylesheet" type="text/css"/>
  <!-- Custom Style-->
  <link href="assets/css/app-style.css" rel="stylesheet"/>
  
</head>

<body class="bg-dark">
 <!-- Start wrapper-->
 <div id="wrapper">
	<div class="card card-authentication1 mx-auto my-5">
		<div class="card-body">
		 <div class="card-content p-2">
		 	<div class="text-center">
		 		<img src="../img/logo.png" alt="<?php echo $d->app_name();?> Logo" width="150">
		 	</div>
		  <div class="card-title text-uppercase text-center pb-2 py-3">Set New Password</div>
		  		<?php
                    extract(array_map("test_input" , $_GET));
                    $forgotTime=date("Y/m/d");
                    $q=$d->select("bms_admin_master","forgot_token='$t' AND admin_id = '$f' AND token_date='$forgotTime' ");
                    $data=mysqli_fetch_array($q);
                    if ($data>0) {
                    $_SESSION['forgot_admin_id']=$f;
                ?>
		    <p class="pb-2">Please enter your New Password</p>
		    <?php //IS_591  id="forgotPwdFrm" ?>
		    <form id="forgotPwdFrm" action="controller/loginController.php" method="post">
          <input type="hidden" name="csrf" value="<?php echo $_SESSION["token"]; ?>" />
			  <div class="form-group">
			  <label for="exampleInputEmailAddress" class="">New Password</label>
			   <div class="position-relative has-icon-right">
			   	<?php //IS_591  id="exampleInputEmailAddress" to id="passwordNew"?>
				  <input type="password" name="passwordNew" required="" id="passwordNew" class="form-control input-shadow" placeholder="Password">
				  <div class="form-control-position">
					  <i class="fa fa-lock"></i>
				  </div>
			   </div>
			  </div>
			  <div class="form-group">
			  <label for="exampleInputEmailAddress" class="">Confirm New Password</label>
			   <div class="position-relative has-icon-right">
			   	<?php //IS_591  id="exampleInputEmailAddress" to id="password2"?>
				  <input type="password" name="password2" required="" id="password2" class="form-control input-shadow" placeholder="Confirm Password">
				  <div class="form-control-position">
					  <i class="fa fa-lock"></i>
				  </div>
			   </div>
			  </div>
			 <?php include 'common/turnstile_widget.php'; ?>
			  <button type="submit" class="btn btn-primary shadow-primary btn-block waves-effect waves-light mt-3">Set Password</button>
			  <div >
			 	<br>
	            <?php include 'common/alert.php'; ?>
	        </div>
			 </form>
			<?php } 
                else{  ?>
                	<div class="alert alert-danger alert-dismissible" role="alert">
					   <button type="button" class="close" data-dismiss="alert">×</button>
						<div class="alert-icon">
						 <i class="fa fa-times"></i>
						</div>
						<div class="alert-message">
						  <span><strong>Error!</strong> Invalid URL or Token Expired</span>
						</div>
					 </div>
                    
            <?php    }
            ?>
		   </div>
		  </div>
		   <div class="card-footer text-center py-3">
		    <p class="text-muted mb-0">Return to the <a href="index.php"> Sign In</a></p>
		  </div>
	     </div>
    
     <!--Start Back To Top Button-->
    <a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>
    <!--End Back To Top Button-->
	</div><!--wrapper-->
	
 <script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/popper.min.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>
  <script src="assets/plugins/jquery-validation/js/jquery.validate.min.js"></script>
  <?php include 'common/turnstile_scripts.php'; ?>
  <script>
    $().ready(function() {
       $.validator.addMethod("pwcheck", function(value) {
          return /^[A-Za-z0-9\d=!\-@._*]*$/.test(value) // consists of only these
             && /[A-Z]/.test(value) // has a lowercase letter
             && /\d/.test(value) // has a digit
      });

    	//IS_591 divya
 $("#forgotPwdFrm").validate({
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
              passwordNew: {
                required: true,
                 pwcheck: true,
                minlength: 5
                },
                password2: {
                required: true,
                 pwcheck: true,
                minlength: 5,
                equalTo: "#passwordNew"
                },
        },
        messages: {
            passwordNew: {
                required: "Please provide a new password",
                pwcheck: "Please enter atleasst 1 Uppercase letter & 1 number value",
                minlength: "Your password must be at least 5 characters long"
                },
                password2: {
                required: "Please provide a confirm password",
                minlength: "Your password must be at least 5 characters long",
                pwcheck: "Please enter atleasst 1 Uppercase letter & 1 number value",
                equalTo: "Please enter the same password"
                }
           
        },
        submitHandler: function(form) {
          if (!requireTurnstile()) {
            return false;
          }
          form.submit();
        }
    });
//IS_591 divya

    $("#personal-info").validate();
   // validate signup form on keyup and submit
    $("#signupForm").validate({
	        rules: {
	            mobile: {
	                required: true,
	                minlength: 2
	            },
	            inputPass: {
	                required: true,
	                minlength: 3
	            },
	        },
	        messages: {
	            mobile: "Please enter your Email Or Mobile",
	            inputPass: "Please enter your Password",
	        }
    	});

	});

    </script>
  
  
</body>

</html>
