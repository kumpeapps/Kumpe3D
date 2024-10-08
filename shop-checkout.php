<?php
// **PREVENTING SESSION FIXATION**
// Session ID cannot be passed through URLs
ini_set('session.use_only_cookies', 1);

// Uses a secure connection (HTTPS) if possible
ini_set('session.cookie_secure', 1);

session_start();
require_once 'includes/site_params.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<script nonce="<?php echo $nonce; ?>" type="text/javascript" src="https://app.termly.io/embed.min.js"
		data-auto-block="off" data-website-uuid="f0526f09-9728-4a75-853d-72961022b400"></script>
	<!-- Google tag (gtag.js) -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=G-05F2DWKXWF"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag() { dataLayer.push(arguments); }
		gtag('js', new Date());

		gtag('config', 'G-05F2DWKXWF');
	</script>
	<!-- Meta -->
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="keywords" content="Checkout">
	<meta name="author" content="KumpeApps LLC">
	<meta name="robots" content="">
	<meta name="description" content="<?php echo $site_params['store_name'] ?> checkout">
	<meta property="og:title" content="Checkout">
	<meta property="og:description" content="<?php echo $site_params['store_name'] ?> checkout">
	<meta name="format-detection" content="telephone=no">

	<!-- FAVICONS ICON -->
	<link nonce="<?php echo $nonce; ?>" rel="icon" type="image/x-icon" href="images/favicon.png">
	<script nonce="<?php echo $nonce; ?>" src="js/loadingOverlay.js"></script>
	<script nonce="<?php echo $nonce; ?>" src="js/http-methods.js"></script>
	<script nonce="<?php echo $nonce; ?>" src="js/cookies.js"></script>
	<script nonce="<?php echo $nonce; ?>" src="env.js"></script>
	<script nonce="<?php echo $nonce; ?>" src="js/default.js"></script>

	<!-- PAGE TITLE HERE -->
	<title>
		<?php echo $site_params['store_name']; ?> Checkout
	</title>

	<!-- MOBILE SPECIFIC -->
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- STYLESHEETS -->
	<link nonce="<?php echo $nonce; ?>" rel="stylesheet" type="text/css"
		href="vendor-js/bootstrap-select/dist/css/bootstrap-select.min.css">
	<link nonce="<?php echo $nonce; ?>" rel="stylesheet" type="text/css" href="icons/fontawesome/css/all.min.css">
	<link nonce="<?php echo $nonce; ?>" rel="stylesheet" type="text/css" href="icons/themify/themify-icons.css">
	<link nonce="<?php echo $nonce; ?>" rel="stylesheet" type="text/css" href="icons/flaticon/flaticon_mooncart.css">
	<link nonce="<?php echo $nonce; ?>" rel="stylesheet" type="text/css" href="vendor-js/swiper/swiper-bundle.min.css">
	<link nonce="<?php echo $nonce; ?>" rel="stylesheet" type="text/css" href="vendor-js/nouislider/nouislider.min.css">
	<link nonce="<?php echo $nonce; ?>" rel="stylesheet" type="text/css" href="vendor-js/animate/animate.css">
	<link nonce="<?php echo $nonce; ?>" rel="stylesheet" type="text/css"
		href="vendor-js/lightgallery/dist/css/lightgallery.css">
	<link nonce="<?php echo $nonce; ?>" rel="stylesheet" type="text/css"
		href="vendor-js/lightgallery/dist/css/lg-thumbnail.css">
	<link nonce="<?php echo $nonce; ?>" rel="stylesheet" type="text/css"
		href="vendor-js/lightgallery/dist/css/lg-zoom.css">
	<link nonce="<?php echo $nonce; ?>" rel="stylesheet" type="text/css" href="css/style.css">
	<script nonce="<?php echo $nonce; ?>" src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

	<!-- GOOGLE FONTS-->
	<link nonce="<?php echo $nonce; ?>" rel="preconnect" href="https://fonts.googleapis.com">
	<link nonce="<?php echo $nonce; ?>" rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link nonce="<?php echo $nonce; ?>"
		href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=Roboto:wght@100;300;400;500;700;900&display=swap"
		rel="stylesheet">

	<!-- TODO: Move to js file -->
	<script>
		const allTrue = arr => arr.every(e => e);
		let isValid = {
			firstNameInput: false,
			lastNameInput: false,
			streetAddressInput: false,
			zipCodeInput: false,
			phoneInput: false,
			emailInput: false
		};
	</script>
</head>

<body>
	<div class="page-wraper">
		<!-- <div id="loading-area" class="preloader-wrapper-1">
			<div>
				<span class="loader-2"></span>
				<img src="<?php echo $site_params['store_loading_image_url']; ?>" alt="/">
				<span class="loader"></span>
			</div>
		</div> -->

		<?php
		include('./includes/header.php');
		?>

		<div class="page-content">
			<!--banner-->
			<div class="dz-bnr-inr" style="background-image:url(images/background/bg-shape.jpg);">
				<div class="container">
					<div class="dz-bnr-inr-entry">
						<h1>Checkout</h1>
						<nav aria-label="breadcrumb" class="breadcrumb-row">
							<ul class="breadcrumb">
								<li class="breadcrumb-item"><a href="/"> Home</a></li>
								<li class="breadcrumb-item"><a href="shop"> Shop</a></li>
								<li class="breadcrumb-item">Checkout</li>
							</ul>
						</nav>
					</div>
				</div>
			</div>
			<!-- inner page banner End-->
			<div class="content-inner-1">
				<div class="container">
					<div class="row shop-checkout">
						<div class="col-xl-8">
							<h4 class="title m-b15">Shipping Details</h4>
							<div class="accordion dz-accordion accordion-sm" id="accordionFaq">
								<!-- TODO: Customer Login -->
								<!-- <div class="accordion-item">
								<h2 class="accordion-header" id="headingOne">
									<a href="#" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
										Returning customer? &nbsp; <span class="text-primary">Click here to login</span>
										<span class="toggle-close"></span>
									</a>
								</h2>
								<div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionFaq">
									<div class="accordion-body">
										<p class="m-b0">If your order has not yet shipped, you can contact us to change your shipping address</p>
									</div>
								</div>
							</div> -->
								<!-- TODO: Coupons -->
								<!-- <div class="accordion-item">
								<h2 class="accordion-header" id="headingTwo">
									<a href="#" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="true" aria-controls="collapseOne">
										Have a coupon? &nbsp; <span class="text-primary">Click here to enter your code</span>
										<span class="toggle-close"></span>
									</a>
								</h2>
								<div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionFaq">
									<div class="accordion-body">
										<p class="m-b0">If your order has not yet shipped, you can contact us to change your shipping address</p>
									</div>
								</div>
							</div> -->
							</div>
							<form class="row">
								<div class="col-md-6">
									<div class="form-group m-b25">
										<label class="label-title">First Name</label>
										<input id="firstNameInput" name="dzName" required=""
											class="form-control is-invalid">
										<div class="valid-feedback">Looks Good!</div>
										<div class="invalid-feedback">Please Enter First Name</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group m-b25">
										<label class="label-title">Last Name</label>
										<input id="lastNameInput" name="dzName" required=""
											class="form-control is-invalid">
										<div class="valid-feedback">Looks Good!</div>
										<div class="invalid-feedback">Please Enter Last Name</div>
									</div>
								</div>
								<div class="col-md-12">
									<div class="form-group m-b25">
										<label class="label-title">Company name (optional)</label>
										<input id="companyName" name="dzName" class="form-control">
									</div>
								</div>
								<div class="col-md-12">
									<div class="form-group m-b25">
										<label class="label-title">Phone *</label>
										<input id="phoneInput" name="dzName" required=""
											class="form-control is-invalid">
										<div class="valid-feedback">Looks Good!</div>
										<div class="invalid-feedback">Please Enter Phone Number</div>
									</div>
								</div>
								<div class="col-md-12">
									<div class="form-group m-b25">
										<label class="label-title">Email address *</label>
										<input id="emailInput" name="dzName" required=""
											class="form-control is-invalid">
										<div class="valid-feedback">Looks Good!</div>
										<div class="invalid-feedback">Please Enter a Valid Email Address</div>
									</div>
								</div>
								<div class="col-md-12">
									<div class="m-b25">
										<label class="label-title">Country / Region *</label>
										<div class="form-select">
											<select id="countrySelect" class="default-select w-100">
												<option value="US" selected>🇺🇸 United States of America</option>
												<option value="GB">🇬🇧 United Kingdom</option>
												<option value="CA">🇨🇦 Canada</option>
											</select>
										</div>
									</div>
								</div>
								<div class="col-md-12">
									<div class="form-group m-b25">
										<label class="label-title">Street address *</label>
										<input id="streetAddressInput" name="dzName" required=""
											class="form-control m-b15 is-invalid"
											placeholder="House number and street name">
										<div class="valid-feedback">Looks Good!</div>
										<div class="invalid-feedback">Please Enter Street Address</div>
										<input id="streetAddress2Input" name="dzName" required="" class="form-control"
											placeholder="Apartment, suite, unit, etc. (optional)">
									</div>
								</div>
								<div class="col-md-12" id="cityContainer" hidden>
									<div class="m-b25">
										<label id="cityTitle" class="label-title">Town / City *</label>
										<input id="cityInput" name="dzName" required="" class="form-control m-b15"
											placeholder="City">
									</div>
								</div>
								<div class="col-md-12" id="stateContainer" hidden>
									<div class="m-b25">
										<label id="stateTitle" class="label-title">State/Locality/Province *</label>
										<input id="stateInput" name="dzName" required="" class="form-control m-b15"
											placeholder="State">
									</div>
								</div>
								<div class="col-md-12">
									<div class="form-group m-b25">
										<label id="zipTitle" class="label-title">ZIP/Postal Code *</label>
										<input id="zipCodeInput" name="dzName" required=""
											class="form-control is-invalid">
										<div class="valid-feedback">Looks Good!</div>
										<div class="invalid-feedback">Please Enter Zip/Postal Code</div>
									</div>
								</div>
								<div class="col-md-12 m-b25">
									<!-- TODO: Add Customer Accounts -->
									<!-- <div class="form-group m-b5">
								   <div class="custom-control custom-checkbox">
										<input type="checkbox" class="form-check-input" id="basic_checkbox_1">
										<label class="form-check-label" for="basic_checkbox_1">Create an account? </label>
									</div>
								</div> -->
									<!-- <div class="form-group">
								   <div class="custom-control custom-checkbox">
										<input type="checkbox" class="form-check-input" id="basic_checkbox_2">
										<label class="form-check-label" for="basic_checkbox_2">Ship to a different address?</label>
									</div>
								</div> -->
								</div>
								<div class="col-md-12 m-b25">
									<div class="form-group">
										<label class="label-title">Order notes (optional)</label>
										<textarea id="orderNotes"
											placeholder="Notes about your order, e.g. special notes for delivery."
											class="form-control" name="comment" cols="90" rows="5"
											required="required"></textarea>
									</div>
								</div>
							</form>
						</div>
						<div class="col-xl-4 side-bar">
							<h4 class="title m-b15">Your Order</h4>
							<div class="order-detail sticky-top">
								<div id="checkout_items">
									<!-- Start Item -->
									<!-- End Item -->
								</div>
								<table>
									<tbody>
										<tr class="subtotal">
											<td>Subtotal</td>
											<td id="cart_subtotal" class="price">$0</td>
										</tr>
										<tr class="title">
											<td>
												<h6 class="title font-weight-500">Shipping</h6>
											</td>
											<td></td>
										</tr>
										<tr class="shipping">
											<td>
												<!-- Start Shipping Option -->
												<div class="custom-control custom-checkbox">
													<input id="shippingCost" value="10.00"
														class="form-check-input radio" type="radio"
														name="flexRadioDefault" id="flexRadioDefault2" checked>
													<label id="shippingLabel" class="form-check-label"
														for="flexRadioDefault2">
														Flat Rate: $10
													</label>
												</div>
												<!-- End Shipping Option -->
											</td>
											<td id="shippingCostLabel" class="price">$10.00</td>
										</tr>
										<tr class="title">
											<td>
												<h6 class="title font-weight-500">Tax</h6>
											</td>
											<td></td>
										</tr>
										<tr class="taxes">
											<!-- Start Taxes -->
											<td id="taxes">
											</td>
											<td id="totalTax" class="price">
												Total Tax $0
											</td>
										</tr>
										<tr class="total">
											<td>Total</td>
											<td id="cart_total" class="price">$0</td>
										</tr>
									</tbody>
								</table>

								<div class="accordion dz-accordion accordion-sm" id="accordionFaq1">
									<div id="paylater_message" data-pp-message="0" data-pp-style-layout="text"
										data-pp-style-layout="black" data-pp-style-logo-type="primary"
										data-pp-style-logo-position="left">
									</div>
									<div id="paypal-button-container" style="max-width:1000px;" hidden=True></div>
									<div id="paymentBlockedNotice">Please fill out order form for payment options to
										appear.</div>
								</div>
								<!-- <p class="text">Your personal data will be used to process your order, support your experience throughout this website, and for other purposes described in our <a href="javascript:void(0);">privacy policy.</a></p>
							<div class="form-group">
								<div class="custom-control custom-checkbox d-flex m-b15">
									<input type="checkbox" class="form-check-input" id="basic_checkbox_3">
									<label class="form-check-label" for="basic_checkbox_3">I have read and agree to the website terms and conditions </label>
								</div>
							</div>
							<a href="shop-checkout.html" class="btn btn-secondary w-100">PLACE ORDER</a> -->
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<?php
		include("./includes/footer.php");
		?>

		<button class="scroltop" type="button"><i class="fas fa-arrow-up"></i></button>


	</div>
	<a href="https://www.kumpe3d.com/dapper.php"><!-- vital --></a>

	<!-- JAVASCRIPT FILES ========================================= -->
	<script src="js/jquery.min.js"></script><!-- JQUERY MIN JS -->
	<script src="vendor-js/wow/wow.min.js"></script><!-- WOW JS -->
	<script src="vendor-js/bootstrap/dist/js/bootstrap.bundle.min.js"></script><!-- BOOTSTRAP MIN JS -->
	<script src="vendor-js/bootstrap-select/dist/js/bootstrap-select.min.js"></script><!-- BOOTSTRAP SELECT MIN JS -->
	<script src="vendor-js/bootstrap-touchspin/bootstrap-touchspin.js"></script><!-- BOOTSTRAP TOUCHSPIN JS -->

	<script src="vendor-js/counter/waypoints-min.js"></script><!-- WAYPOINTS JS -->
	<script src="vendor-js/counter/counterup.min.js"></script><!-- COUNTERUP JS -->
	<script src="vendor-js/swiper/swiper-bundle.min.js"></script><!-- SWIPER JS -->
	<script src="vendor-js/imagesloaded/imagesloaded.js"></script><!-- IMAGESLOADED-->
	<script src="vendor-js/masonry/masonry-4.2.2.js"></script><!-- MASONRY -->
	<script src="vendor-js/masonry/isotope.pkgd.min.js"></script><!-- ISOTOPE -->
	<script src="vendor-js/countdown/jquery.countdown.js"></script><!-- COUNTDOWN FUCTIONS  -->
	<script src="vendor-js/wnumb/wNumb.js"></script><!-- WNUMB -->
	<script src="vendor-js/nouislider/nouislider.min.js"></script><!-- NOUSLIDER MIN JS-->
	<script src="js/dz.carousel.js"></script><!-- DZ CAROUSEL JS -->
	<script src="js/dz.ajax.js"></script><!-- AJAX -->
	<script src="js/custom.js"></script><!-- CUSTOM JS -->
	<!-- Checkout JS -->
	<script src="https://unpkg.com/validator@latest/validator.min.js"></script>
	<?php
	echo "<script src='https://www.paypal.com/sdk/js?&client-id=$paypal_clientid&currency=USD&components=" . $site_params['store_paypal_components'] . "&disable-funding=" . $site_params['store_paypal_disablefunding'] . "&enable-funding=" . $site_params['store_paypal_enablefunding'] . "&integration-date=2023-10-01' data-page-type=\"checkout\"></script>";
	?>
	<script nonce="<?php echo $nonce; ?>" src="js/checkout.js"></script>

</body>

</html>

<?php
mysqli_close($conn);
?>