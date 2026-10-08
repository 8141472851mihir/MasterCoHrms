<?php 
session_start(); 
include_once "./lib/dao.php";
$d=new dao();
$turnstile = $d->turnstile_site();
$_SESSION["token"] = md5(uniqid(mt_rand(), true));
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
  <meta name="description" content=""/>
  <meta name="author" content=""/>
  <title>Login | <?php echo $d->app_name(); ?></title>
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
  
</head>

<body class="bg-dark">
 <!-- Start wrapper-->
 <div id="wrapper" class="container-fluid">
	<div class="card card-authentication1 mx-auto my-5">
		<div class="card-body">
		 <div class="card-content p-2">
		 	<div class="text-center">
		 		<img src="../img/logo.png" alt="<?php echo $d->app_name(); ?>  Logo" width="130">
		 	</div>
		    <form id="signupForm" action="controller/loginController.php" method="post">
		    	<input type="hidden" name="csrf" value="<?php echo $_SESSION["token"]; ?>" />
			<div class="form-group row">
	          <label for="exampleInputUsername"  class="col-sm-12 col-form-label">Country</label>
	          <div class="position-relative has-icon-right col-sm-12">
	            <select name="country_code" class="form-control single-select" id="country_code" required="">
	                <?php include 'country_code_option_list.php'; ?>
	                </select>
	          </div>
	        </div>
			  <div class="form-group row">
			  <label for="exampleInputEmailAddress col-sm-12" class="">Email Address/Mobile</label>
			   <div class="position-relative has-icon-right col-sm-12">
				  <input type="text" name="forgot_email" required="" id="exampleInputEmailAddress" class="form-control input-shadow" placeholder="Email Address/Mobile">
				  <div class="form-control-position">
					  <i class="icon-envelope-open"></i>
				  </div>
			   </div>
			  </div>
			 <?php include 'common/turnstile_widget.php'; ?>
			 <div class="form-group row">
			  <div class="col-sm-12">
			  <button type="submit" class="btn btn-primary shadow-primary btn-block waves-effect waves-light mt-3">Reset Password</button>
			  <div >
			  </div>
			 </div>
	          
	        </div>
			 </form>
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
	
  <!-- Bootstrap core JavaScript-->
  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/popper.min.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>
  <script src="assets/plugins/jquery-validation/js/jquery.validate.min.js"></script>
  <?php include 'common/turnstile_scripts.php'; ?>
    <!--Sweet Alerts -->
  <script src="assets/plugins/alerts-boxes/js/sweetalert.min.js"></script>
  <script src="assets/plugins/alerts-boxes/js/sweet-alert-script.js"></script>
    <?php include 'common/alert.php'; ?>
  <script>
    $().ready(function() {
    $("#personal-info").validate();
   // validate signup form on keyup and submit
    $("#signupForm").validate({
	        rules: {
	            forgot_email: {
	                required: true,
	                minlength: 2
	            },
	        },
	        messages: {
	            forgot_email: "Please enter your Email Or Mobile",
	        },
          submitHandler: function(form) {
            if (!requireTurnstile()) {
              return false;
            }
            form.submit();
          }
    	});

	});

    </script>
	
</body>

</html>
