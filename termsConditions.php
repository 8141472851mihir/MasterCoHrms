<?php 
include_once "apAdmin/lib/dao.php";
$d=new dao();
if(isset($_REQUEST['app']) && $_REQUEST['app']!="") {
	$appName = $_REQUEST['app'];
} else {
	$appName = $d->app_name()."";
}
?>
<!DOCTYPE html>
<html class="no-js" lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />

	<title>Welcome to <?php echo $appName;?></title>

	<link rel="icon" type="image/png" sizes="32x32" href="img/fav.png">

	<link rel="manifest" href="assets/img/favicon/manifest.json">

	<link rel="shortcut icon" type="image/png" href="img/fav.png" />
	<link rel="icon" type="image/png" href="img/fav.png" />
	<link href="assets/css/custom.css" type="text/css" rel="stylesheet"/> 
	<link rel="stylesheet" type="text/css" href="assets/css/animate.css">




<!-- BOOTSTRAP CSS -->
<link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css" />
<!-- to add bootstrap from cdn pls uncomment the bellow line -->
<!--link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous"-->

<!-- FONT AWESOME CSS -->
<link rel="stylesheet" href="assets/vendor/fontawesome/css/font-awesome.min.css" />

<!-- GOOGLE FONT -->
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,300,800%7CExo:300,400,500,600,700,900" rel="stylesheet" />

<!--owl carousel css -->
<link rel="stylesheet" href="assets/vendor/owl-carousel/assets/owl.carousel.min.css" media="all" />
<link rel="stylesheet" href="assets/vendor/owl-carousel/assets/owl.carousel1.min.css" media="all" />
<link rel="stylesheet" href="assets/vendor/owl-carousel/assets/owl.theme.default.min.css" media="all" />

<!-- lity css -->
<link rel="stylesheet" href="assets/vendor/lity/lity.min.css" media="all" />



<!-- CUSTOM  CSS  -->
<link id="cbx-style" data-layout="1" rel="stylesheet" href="assets/css/style1-default.min.css" media="all" />
<!-- <link id="cbx-style" data-layout="1" rel="stylesheet" href="assets/css/style2-default.min.css" media="all" /> -->

<style type="text/css">
	.features-tracker p{
		text-align: left;
	}
	.cbx-inner{
		padding-top: 0px;
	}
	.p span{
		text-align: justify !important;
	}
</style>
</head>
<body id="hometop">


	<section class="features-tracker">
		<div id="cbx-faq" class="cbx-section cbx-faq">
			<div class="cbx-inner">
				<div class="container">
					<div class="cbx-content">
						<div class="row">
							<div class="col-md-12 animated wow zoomIn slower go">
								<div class="text-justify ">

									<h2 class="uppercase"><?php echo $appName;?> Terms & Condition</h2>

									<p style="margin-bottom: 0.17cm; line-height: 100%; background: #ffffff;"><span style="font-family: Times New Roman, serif;"><span style="font-size: medium;"><strong><span style="color: #3d3c4c;"><span style="font-family: Calibri, serif;"><span style="font-size: small;">a.</span></span></span></strong><span style="color: #3d3c4c;"><span style="font-family: Calibri, serif;"><span style="font-size: small;">&nbsp;These Terms set forth a legally binding contract between You and Us. By using the Platforms, You agree to be bound by these Terms together with the Privacy Policy.</span></span></span></span></span></p>
									<p style="margin-bottom: 0.17cm; line-height: 100%; background: #ffffff;"><span style="font-family: Times New Roman, serif;"><span style="font-size: medium;"><strong><span style="color: #3d3c4c;"><span style="font-family: Calibri, serif;"><span style="font-size: small;">b.</span></span></span></strong><span style="color: #3d3c4c;"><span style="font-family: Calibri, serif;"><span style="font-size: small;">&nbsp;To accept these Terms, You must be at least 18 (eighteen) years of age. In the event that You are below 18 (eighteen) years of age or such other older legal age, Your guardian must read and understand and accept the provisions on Your behalf.</span></span></span></span></span></p>
									<p style="margin-bottom: 0.17cm; line-height: 100%; background: #ffffff;"><span style="font-family: Times New Roman, serif;"><span style="font-size: medium;"><strong><span style="color: #3d3c4c;"><span style="font-family: Calibri, serif;"><span style="font-size: small;">c.</span></span></span></strong><span style="color: #3d3c4c;"><span style="font-family: Calibri, serif;"><span style="font-size: small;">&nbsp;If You are executing/accepting these terms on behalf of any registered Company (&ldquo;Direct Customer&rdquo;), You confirm that You are a major and are competent, to accept/execute the Terms of Use.</span></span></span></span></span></p>
									<p style="margin-bottom: 0.17cm; line-height: 100%; background: #ffffff;"><span style="font-family: Times New Roman, serif;"><span style="font-size: medium;"><strong><span style="color: #3d3c4c;"><span style="font-family: Calibri, serif;"><span style="font-size: small;">d.</span></span></span></strong><span style="color: #3d3c4c;"><span style="font-family: Calibri, serif;"><span style="font-size: small;">&nbsp;You also agree to provide true, accurate and complete information about Yourself and update them upon any change.</span></span></span></span></span></p>
									<p style="margin-bottom: 0.17cm; line-height: 100%; background: #ffffff;"><span style="font-family: Times New Roman, serif;"><span style="font-size: medium;"><strong><span style="color: #3d3c4c;"><span style="font-family: Calibri, serif;"><span style="font-size: small;">e.</span></span></span></strong><span style="color: #3d3c4c;"><span style="font-family: Calibri, serif;"><span style="font-size: small;">&nbsp;To avail any services offered by the Company, You will have to register on the Platforms to create Your account. To register You will be required to open an account by completing the registration process, by providing Us with current, complete and accurate information as prompted by the registration form. You may then update the identifiable information relating to You.</span></span></span></span></span></p>
									<p style="margin-bottom: 0.17cm; line-height: 100%; background: #ffffff;"><span style="font-family: Times New Roman, serif;"><span style="font-size: medium;"><strong><span style="color: #3d3c4c;"><span style="font-family: Calibri, serif;"><span style="font-size: small;">f.</span></span></span></strong><span style="color: #3d3c4c;"><span style="font-family: Calibri, serif;"><span style="font-size: small;">&nbsp;To access Your account on the Platforms, You will be asked to log in with Your user ID and password, to establish a unique identity for You. You are responsible for maintaining the confidentiality of Your password, verification code, and account information. If there is any compromise on Your password You can change Your password using the &lsquo;Forgot Password?&rsquo; functionality.</span></span></span></span></span></p>
									<p style="margin-bottom: 0.17cm; line-height: 100%; background: #ffffff;"><span style="font-family: Times New Roman, serif;"><span style="font-size: medium;"><strong><span style="color: #3d3c4c;"><span style="font-family: Calibri, serif;"><span style="font-size: small;">g.</span></span></span></strong><span style="color: #3d3c4c;"><span style="font-family: Calibri, serif;"><span style="font-size: small;">&nbsp;At the time of registration, if You provide any information that is untrue, inaccurate, not current or incomplete, or We have reasonable grounds to suspect that such information is untrue, inaccurate, not current or incomplete, We reserve the right to suspend or terminate Your account and refuse any and all current or future use of the Platforms (or any portion thereof) at any time.</span></span></span></span></span></p>
									<p style="margin-bottom: 0.17cm; line-height: 100%; background: #ffffff;"><span style="font-family: Times New Roman, serif;"><span style="font-size: medium;"><strong><span style="color: #3d3c4c;"><span style="font-family: Calibri, serif;"><span style="font-size: small;">h.</span></span></span></strong><span style="color: #3d3c4c;"><span style="font-family: Calibri, serif;"><span style="font-size: small;">&nbsp;You may access the Platforms as available for Your personal use only.</span></span></span></span></span></p>
									<p style="margin-bottom: 0.17cm; line-height: 100%; background: #ffffff;"><span style="font-family: Times New Roman, serif;"><span style="font-size: medium;"><strong><span style="color: #3d3c4c;"><span style="font-family: Calibri, serif;"><span style="font-size: small;">i.</span></span></span></strong><span style="color: #3d3c4c;"><span style="font-family: Calibri, serif;"><span style="font-size: small;">&nbsp;You will be required to enter a valid phone number while registering on the Platforms. By registering Your phone number with us, You consent to be contacted by Us via phone calls, SMS notifications, and/or e-mails, in case of any subscription/service updates.</span></span></span></span></span></p>
									<p style="margin-bottom: 0.17cm; line-height: 100%; background: #ffffff;"><span style="font-family: Times New Roman, serif;"><span style="font-size: medium;"><strong><span style="color: #3d3c4c;"><span style="font-family: Calibri, serif;"><span style="font-size: small;">j.</span></span></span></strong><span style="color: #3d3c4c;"><span style="font-family: Calibri, serif;"><span style="font-size: small;">&nbsp;You agree that it shall not be the responsibility of the Company to perform background checks or provide any advice relating to the hiring/firing of the security personnel of the apartment complex /gated complex and/or any third-party service providers providing services to the residents of such apartment complex /gated complex. The hiring/firing of such security personnel and/or any third-party service providers shall be at the sole risk of the Company of the apartment complex /gated complex or the residents of such apartment complex /gated complex, as the case maybe. We are merely a technology platform.</span></span></span></span></span></p>
									<p style="margin-bottom: 0.17cm; line-height: 100%; background: #ffffff;"><br /> </p>
									<p style="margin-bottom: 0.17cm; line-height: 100%; background: #ffffff;"><span style="font-family: Times New Roman, serif;"><span style="font-size: medium;"><strong><span style="color: #3d3c4c;"><span style="font-family: Calibri, serif;"><span style="font-size: small;">k.</span></span></span></strong><span style="color: #3d3c4c;"><span style="font-family: Calibri, serif;"><span style="font-size: small;">&nbsp;The Company offers You a technology platform which ensures to the best extent possible, the safety and security of apartment complexes/gated enclaves by deploying its proprietary software (&ldquo;Service&rdquo;).</span></span></span></span></span></p>
									<p style="margin-bottom: 0.17cm; line-height: 100%; background: #ffffff;"><span style="font-family: Times New Roman, serif;"><span style="font-size: medium;"><strong><span style="color: #3d3c4c;"><span style="font-family: Calibri, serif;"><span style="font-size: small;">l.</span></span></span></strong><span style="color: #3d3c4c;"><span style="font-family: Calibri, serif;"><span style="font-size: small;">&nbsp;You acknowledge and agree that the Company of each such apartment complex/gated enclave, being our Direct Customers, shall share all Users / residents information with the Company, including Your information, and the same shall also be subject to the terms of our Privacy Policy. It is clarified that such information is in addition to the information provided by You on the Platform.</span></span></span></span></span></p>
									<p style="margin-bottom: 0.17cm; line-height: 100%; background: #ffffff;"><span style="font-family: Times New Roman, serif;"><span style="font-size: medium;"><strong><span style="color: #3d3c4c;"><span style="font-family: Calibri, serif;"><span style="font-size: small;">m.</span></span></span></strong><span style="color: #3d3c4c;"><span style="font-family: Calibri, serif;"><span style="font-size: small;">&nbsp;You agree that the Service may enable You to control the access of entry into the apartment complex/gated enclave through inviting or letting non-residents, such as service providers who provide daily services (maids, drivers, cooks, etc.), as well as non-regular visitors including, without limitation, family, friends, and delivery personnel. You agree that every entry to service providers, guests and visitors in the Premises is either resident initiated and is authorized by You or authorized by personnel of appointed Security Agency or any other agency or vendor or individual as appointed by the Company or  at the Premises and the Company shall have no liability in this regard. Therefore, the Company shall not be liable for any entry in to the apartment complex/gated enclave, as the entry process is completely controlled by You.</span></span></span></span></span></p>
									<p style="margin-bottom: 0.17cm; line-height: 100%; background: #ffffff;"><span style="font-family: Times New Roman, serif;"><span style="font-size: medium;"><strong><span style="color: #3d3c4c;"><span style="font-family: Calibri, serif;"><span style="font-size: small;">n.</span></span></span></strong><span style="color: #3d3c4c;"><span style="font-family: Calibri, serif;"><span style="font-size: small;">&nbsp;You agree that the Company offers various others additional services through its Platform, which You may avail at Your own risk. The Company shall not be liable for any services that You chose to avail on/from the Platform. These additional services may be governed by specific policies.</span></span></span></span></span></p>
									<p style="margin-bottom: 0.17cm; line-height: 100%; background: #ffffff;"><span style="font-family: Times New Roman, serif;"><span style="font-size: medium;"><strong><span style="color: #3d3c4c;"><span style="font-family: Calibri, serif;"><span style="font-size: small;">o.</span></span></span></strong><span style="color: #3d3c4c;"><span style="font-family: Calibri, serif;"><span style="font-size: small;">&nbsp;The Company does not guarantee the consistency or the stability of the Platforms. Further, any services provided by the Company may be subject to change at the discretion of the Company, and the Company does not require Your permission for the same. While we want to insure the best user experience, We do not represent or warrant that the services or our Platform will be error free or uninterrupted.</span></span></span></span></span></p>
									<p style="margin-bottom: 0.17cm; line-height: 100%; background: #ffffff;"><span style="font-family: Times New Roman, serif;"><span style="font-size: medium;"><strong><span style="color: #3d3c4c;"><span style="font-family: Calibri, serif;"><span style="font-size: small;">p.</span></span></span></strong><span style="color: #3d3c4c;"><span style="font-family: Calibri, serif;"><span style="font-size: small;">&nbsp;The Company does not control the information provided by third parties (such as value-added services or other promotional / marketing links on our Platform) nor does the Company control any information or data provided by the other residents registered Company or s, being our Direct Customers, as the case may be. Your reliance on the same shall be at your own risk.</span></span></span></span></span></p>
									<p style="margin-bottom: 0.17cm; line-height: 100%; background: #ffffff;"><span style="font-family: Times New Roman, serif;"><span style="font-size: medium;"><strong><span style="color: #3d3c4c;"><span style="font-family: Calibri, serif;"><span style="font-size: small;">q.</span></span></span></strong><span style="color: #3d3c4c;"><span style="font-family: Calibri, serif;"><span style="font-size: small;">&nbsp;Reliance on any information provided by such third parties, their employees, or others appearing on the Platform is solely at your own risk. Furthermore, the information provided by the Company on the Platform is on as &ldquo;AS IS&rdquo; basis and any reliance on the same shall be at Your sole risk. You understand the Company is merely a technology platform and that the Company shall not be responsible for the correctness of the information provided by You or any third party on the Company&rsquo;s Platform. You acknowledge and agree that the Company is an intermediary and the use and/or publication of any information provided by the users shall be at the User&rsquo;s sole risk except so far as provided under these Terms.</span></span></span></span></span></p>

									<p style="margin-bottom: 0.17cm; line-height: 100%; background: #ffffff;"><span style="font-family: Times New Roman, serif;"><span style="font-size: medium;"><strong><span style="color: #3d3c4c;"><span style="font-family: Calibri, serif;"><span style="font-size: small;">R.</span></span></span></strong><span style="color: #3d3c4c;"><span style="font-family: Calibri, serif;"><span style="font-size: small;">&nbsp;In Face App <?php echo $appName;?> accessibility setting is used to enhance the user experience .<br>
										This setting is required for the control the device screen on/off at employee shift wise scheduled time.
										The device screen automaticaly on when shift time start & automaticaly off when when shift time end.
										This accessibility setting is used for device battry & screen health life increase.
										doesn't collect any personal &  sensitive data in  Face App <?php echo $appName;?> . </span></span></span></span></span></p>


								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section><!-- *************  //FREQUENTLY ASKED QUESTION END ************* -->
</body>
</html>
