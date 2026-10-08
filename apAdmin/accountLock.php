<?php 
session_start();
include 'common/object.php';
if(isset($_COOKIE['master_token'])){ 
	$token=$_COOKIE['master_token'] ?? null;
	$key = $d->get_encrypt_key(); 
	try {
		$decoded = JWT::decode($token, $key, ['HS256']);
		if (time() < $decoded->exp) {
			echo "<script>window.location.href = './welcome';</script>";
      // header("location:./welcome");
			exit();
		}
	} catch (Exception $e) {

	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8"/>
	<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
	<meta name="description" content=""/>
	<meta name="author" content=""/>
	<title>Account Deactivated | <?php echo $d->app_name(); ?> </title>
	<!--favicon-->
	<link rel="icon" href="../img/fav.png" type="image/png">
	<!-- Bootstrap core CSS-->
	<link href="assets/css/bootstrap.min.css" rel="stylesheet"/>
	<!-- animate CSS-->
	<link href="assets/css/animate.css" rel="stylesheet" type="text/css"/>
	<!-- Icons CSS-->
	<link href="assets/css/icons.css" rel="stylesheet" type="text/css"/>
	<!-- Custom Style-->
	<link href="assets/css/app-style9.css" rel="stylesheet"/>
</head>
<body class="bg-dark">
	<div id="wrapper">
		<div class="card card-authentication1 mx-auto my-5">
			<div class="card-body">
				<div class="card-content p-2">
					<div class="text-center">
						<img src="../img/logo.png" alt="<?php echo $d->app_name(); ?>  Logo" width="150">
					</div>
				</div>
				<div >
					<br>
					<div class="alert alert-danger alert-dismissible fade show" role="alert">
					  <strong>Error!</strong> Your Account was Deactivate by Admin.
					  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
					    <span aria-hidden="true">&times;</span>
					  </button>
					</div>
					<div class="form-group col-12 text-right">
						<a href="index.php">Login ?</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div><!--wrapper-->

<!-- Bootstrap core JavaScript-->
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/popper.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>

</body>

</html>
