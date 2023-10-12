<?php
require_once 'includes/site_params.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<!-- Meta -->
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="keywords" content="">
	<meta name="author" content="HugeBinary">
	<meta name="robots" content="">
	<meta name="description" content="<?php echo $site_params['store_name']; ?>">
	<meta property="og:title" content="<?php echo $site_params['store_name']; ?>">
	<meta property="og:description" content="<?php echo $site_params['store_name']; ?>">
	<meta name="format-detection" content="telephone=no">
	<style>
		p {
			text-align: center;
			font-size: 60px;
			margin-top: 0px;
		}
	</style>
	<!-- FAVICONS ICON -->
	<link rel="icon" type="image/x-icon" href="images/favicon.png">

	<!-- PAGE TITLE HERE -->
	<title>
		<?php echo $site_params['store_name']; ?>
	</title>

	<!-- MOBILE SPECIFIC -->
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- STYLESHEETS -->
	<link rel="stylesheet" type="text/css" href="vendor-js/bootstrap-select/dist/css/bootstrap-select.min.css">
	<link rel="stylesheet" type="text/css" href="icons/fontawesome/css/all.min.css">
	<link rel="stylesheet" type="text/css" href="icons/themify/themify-icons.css">
	<link rel="stylesheet" type="text/css" href="icons/flaticon/flaticon_mooncart.css">
	<link rel="stylesheet" type="text/css" href="vendor-js/swiper/swiper-bundle.min.css">
	<link rel="stylesheet" type="text/css" href="vendor-js/nouislider/nouislider.min.css">
	<link rel="stylesheet" type="text/css" href="vendor-js/animate/animate.css">
	<link rel="stylesheet" type="text/css" href="vendor-js/lightgallery/dist/css/lightgallery.css">
	<link rel="stylesheet" type="text/css" href="vendor-js/lightgallery/dist/css/lg-thumbnail.css">
	<link rel="stylesheet" type="text/css" href="vendor-js/lightgallery/dist/css/lg-zoom.css">
	<link rel="stylesheet" type="text/css" href="css/style.css">

	<!-- GOOGLE FONTS-->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link
		href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=Roboto:wght@100;300;400;500;700;900&display=swap"
		rel="stylesheet">

</head>

<body>
	<div class="page-wraper">
		<div id="loading-area" class="preloader-wrapper-1">
			<div>
				<span class="loader-2"></span>
				<img src="<?php echo $site_params['store_loading_image_url']; ?>" alt="/">
				<span class="loader"></span>
			</div>
		</div>
		<section class="px-3 overflow-hidden">
			<div class="row under-construct">
				<div class="col-xxl-6 col-xl-5 col-lg-6 construct-box-1 single-page">
					<img src="images/circle-lines.png" class="bg-img" alt="">
					<div class="logo">
						<a href="index.php"><img src="<?php echo $site_params['store_logo_url']; ?>" alt=""></a>
					</div>
					<div class="dz-content">
						<div class="dz-media-title"><img src="images/vlc.png" alt="">Oops!</div>
						<h2 class="dz-title">Our website in Under Construction</h2>
						<h3>
							<?php echo $site_params['store_maitenance_text']; ?>
						</h3>
						<p id="countdown"></p>
					</div>
				</div>
			</div>
		</section>

	</div>

	<!-- JAVASCRIPT FILES ========================================= -->
	<script src="js/jquery.min.js"></script><!-- JQUERY MIN JS -->
	<script src="vendor-js/bootstrap/dist/js/bootstrap.bundle.min.js"></script><!-- BOOTSTRAP MIN JS -->
	<script src="vendor-js/bootstrap-select/dist/js/bootstrap-select.min.js"></script><!-- BOOTSTRAP SELECT MIN JS -->
	<script src="vendor-js/bootstrap-touchspin/bootstrap-touchspin.js"></script><!-- BOOTSTRAP TOUCHSPIN JS -->
	<script src="js/dz.ajax.js"></script><!-- AJAX -->
	<script src="js/custom.js"></script><!-- CUSTOM JS -->
	<script>
		// Set the date we're counting down to
		var countDownDate = new Date("<?php  echo $site_params['store_countdown_date']; ?>").getTime();

		// Update the count down every 1 second
		var x = setInterval(function () {

			// Get today's date and time
			var now = new Date().getTime();

			// Find the distance between now and the count down date
			var distance = countDownDate - now;

			// Time calculations for days, hours, minutes and seconds
			var days = Math.floor(distance / (1000 * 60 * 60 * 24));
			var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
			var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
			var seconds = Math.floor((distance % (1000 * 60)) / 1000);

			// Display the result in the element with id="demo"
			document.getElementById("countdown").innerHTML = days + "d " + hours + "h "
				+ minutes + "m " + seconds + "s ";

			// If the count down is finished, write some text 
			if (distance < 0) {
				clearInterval(x);
				document.getElementById("countdown").innerHTML = "";
			}
		}, 1000);
	</script>

</body>

</html>