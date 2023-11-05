<?php
// **PREVENTING SESSION FIXATION**
// Session ID cannot be passed through URLs
ini_set('session.use_only_cookies', 1);

// Uses a secure connection (HTTPS) if possible
ini_set('session.cookie_secure', 1);
session_start();
require_once 'includes/site_params.php';
$conn = mysqli_connect(
	$_ENV['mysql_host'],
	$_ENV['mysql_user'],
	$_ENV['mysql_pass'],
	'Web_3dprints'
) or die("Couldn't connect to server.");

?>
<!DOCTYPE html>
<html lang="en">

<head>
	<script nonce="<?php echo $nonce; ?>" type="text/javascript" src="https://app.termly.io/embed.min.js"
		data-auto-block="off" data-website-uuid="f0526f09-9728-4a75-853d-72961022b400"></script>
	<!-- Meta -->
	<meta http-equiv="Content-Security-Policy-Report-Only" content="
		default-src 'self';
		script-src 'self' 'nonce-<?php echo $nonce; ?>';
		style-src * data: blob: 'unsafe-inline';
		object-src 'none';
		base-uri 'self';
		connect-src 'self' https://api.preprod.kumpe3d.com https://api.kumpe3d.com;
		font-src 'self' data: https://fonts.gstatic.com;
		frame-src 'self';
		img-src *;
		manifest-src 'self';
		media-src 'self';
		worker-src 'none';">
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="keywords" content="3d,poodles,ornaments,grooming,creative,kumpe,kumpe3d,kumpeapps,angela kumpe">
	<meta name="author" content="KumpeApps LLC">
	<meta name="robots" content="">
	<meta name="description" content="3d Printed objects for sale">
	<meta property="og:title" content="Kumpe3D">
	<meta property="og:description" content="3d Printed objects for sale">
	<meta name="format-detection" content="telephone=no">

	<!-- FAVICONS ICON -->
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<script nonce="<?php echo $nonce; ?>" src="js/loadingOverlay.js"></script>
	<script nonce="<?php echo $nonce; ?>" src="js/http-methods.js"></script>
	<script nonce="<?php echo $nonce; ?>" src="js/cookies.js"></script>
	<script nonce="<?php echo $nonce; ?>" src="env.js"></script>
	<script nonce="<?php echo $nonce; ?>" src="js/default.js"></script>

	<!-- PAGE TITLE HERE -->
	<title>
		Kumpe3D
	</title>
	
	<!-- MOBILE SPECIFIC -->
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<!-- STYLESHEETS -->
	<link nonce="<?php echo $nonce; ?>" rel="stylesheet" type="text/css" href="vendor-js/bootstrap-select/dist/css/bootstrap-select.min.css">
	<link nonce="<?php echo $nonce; ?>" rel="stylesheet" type="text/css" href="icons/fontawesome/css/all.min.css">
	<link nonce="<?php echo $nonce; ?>" rel="stylesheet" type="text/css" href="icons/themify/themify-icons.css">
	<link nonce="<?php echo $nonce; ?>" rel="stylesheet" type="text/css" href="icons/flaticon/flaticon_mooncart.css">
	<link nonce="<?php echo $nonce; ?>" rel="stylesheet" type="text/css" href="vendor-js/swiper/swiper-bundle.min.css">
	<link nonce="<?php echo $nonce; ?>" rel="stylesheet" type="text/css" href="vendor-js/nouislider/nouislider.min.css">
	<link nonce="<?php echo $nonce; ?>" rel="stylesheet" type="text/css" href="vendor-js/animate/animate.css">
	<link nonce="<?php echo $nonce; ?>" rel="stylesheet" type="text/css" href="vendor-js/lightgallery/dist/css/lightgallery.css" >
    <link nonce="<?php echo $nonce; ?>" rel="stylesheet" type="text/css" href="vendor-js/lightgallery/dist/css/lg-thumbnail.css">
    <link nonce="<?php echo $nonce; ?>" rel="stylesheet" type="text/css" href="vendor-js/lightgallery/dist/css/lg-zoom.css">
	<link nonce="<?php echo $nonce; ?>" rel="stylesheet" type="text/css" href="css/style.css">
	
	<!-- GOOGLE FONTS-->
	<link nonce="<?php echo $nonce; ?>" rel="preconnect" href="https://fonts.googleapis.com">
	<link nonce="<?php echo $nonce; ?>" rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link nonce="<?php echo $nonce; ?>" href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">

</head>

<body>
	<div class="page-wraper">
		<div id="loading-area" class="preloader-wrapper-1">
			<div>
				<span class="loader-2"></span>
				<img src="<?php echo $site_params['store_loading_image_url'] ?>" alt="/">
				<span class="loader"></span>
			</div>
		</div>
		<?php
		include('./includes/header.php');
		?>
	</div>
	
	<div class="page-content">
		<section class="content-inner-1">
			<div class="container">
				<div class="row justify-content-center">
					<div class="col-xl-8 col-lg-10 col-md-12">
						<div class="error-page style-1">
							<div class="dz-error-media">
								<img src="images/pic-404.png" alt="">
							</div>
							<div class="error-inner">
								<h1 class="dz_error">404</h1>
								<p class="error-head">Oh, no! This page does not exist.</p>
								<a href="index.php" class="btn btn-secondary  text-uppercase">Go to Main Page</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
	</div>
	
	<?php
		include("./includes/footer.php");
	?>

	<button class="scroltop" type="button"><i class="fas fa-arrow-up"></i></button>

	
</div>

<!-- JAVASCRIPT FILES ========================================= -->
<script nonce="<?php echo $nonce; ?>" src="js/jquery.min.js"></script><!-- JQUERY MIN JS -->
<script nonce="<?php echo $nonce; ?>" src="vendor-js/wow/wow.min.js"></script><!-- WOW JS -->
<script nonce="<?php echo $nonce; ?>" src="vendor-js/bootstrap/dist/js/bootstrap.bundle.min.js"></script><!-- BOOTSTRAP MIN JS -->
<script nonce="<?php echo $nonce; ?>" src="vendor-js/bootstrap-select/dist/js/bootstrap-select.min.js"></script><!-- BOOTSTRAP SELECT MIN JS -->
<script nonce="<?php echo $nonce; ?>" src="vendor-js/bootstrap-touchspin/bootstrap-touchspin.js"></script><!-- BOOTSTRAP TOUCHSPIN JS -->

<script nonce="<?php echo $nonce; ?>" src="vendor-js/counter/waypoints-min.js"></script><!-- WAYPOINTS JS -->
<script nonce="<?php echo $nonce; ?>" src="vendor-js/counter/counterup.min.js"></script><!-- COUNTERUP JS -->
<script nonce="<?php echo $nonce; ?>" src="vendor-js/swiper/swiper-bundle.min.js"></script><!-- SWIPER JS -->
<script nonce="<?php echo $nonce; ?>" src="vendor-js/imagesloaded/imagesloaded.js"></script><!-- IMAGESLOADED-->
<script nonce="<?php echo $nonce; ?>" src="vendor-js/masonry/masonry-4.2.2.js"></script><!-- MASONRY -->
<script nonce="<?php echo $nonce; ?>" src="vendor-js/masonry/isotope.pkgd.min.js"></script><!-- ISOTOPE -->
<script nonce="<?php echo $nonce; ?>" src="vendor-js/countdown/jquery.countdown.js"></script><!-- COUNTDOWN FUCTIONS  -->
<script nonce="<?php echo $nonce; ?>" src="vendor-js/wnumb/wNumb.js"></script><!-- WNUMB -->
<script nonce="<?php echo $nonce; ?>" src="vendor-js/nouislider/nouislider.min.js"></script><!-- NOUSLIDER MIN JS-->
<script nonce="<?php echo $nonce; ?>" src="js/dz.carousel.js"></script><!-- DZ CAROUSEL JS -->
<script nonce="<?php echo $nonce; ?>" src="js/dz.ajax.js"></script><!-- AJAX -->
<script nonce="<?php echo $nonce; ?>" src="js/custom.js"></script><!-- CUSTOM JS -->

</body>
</html>