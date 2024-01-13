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
	<!-- Google tag (gtag.js) -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=G-05F2DWKXWF"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag() { dataLayer.push(arguments); }
		gtag('js', new Date());

		gtag('config', 'G-05F2DWKXWF');
	</script>
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

</head>

<body>
	<div id="fb-root"></div>
	<script async defer crossorigin="anonymous"
		src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v18.0&appId=313430524972692"
		nonce="4q6wcyoK"></script>
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

		<div class="page-content bg-white">

			<!--Swiper Banner Start -->
			<div class="main-slider style-2">
				<div class="main-swiper">
					<!-- <div class="swiper-wrapper">
						<div class="swiper-slide bg-light">
							<div class="container">
								<div class="banner-content">
									<div class="row">
										<div class="col-xl-6 col-md-6 col-sm-7 align-self-center">
											<div class="swiper-content">
												<div class="content-info">
													<h1 class="offer-title mb-0" data-swiper-parallax="-20">SALE 50%
													</h1>
													<h2 class="title mb-2" data-swiper-parallax="-20">For Meditation,
														yoga, Asana.</h2>
													<p class="sub-title mb-0" data-swiper-parallax="-40">No code need.
														Plus free shippng on $99+ orders!</p>
												</div>
												<div class="content-btn" data-swiper-parallax="-60">
													<a class="btn btn-secondary  me-3" href="shop-cart.html">ADD TO
														CART</a>
													<a class="btn btn-outline-secondary "
														href="product-default.html">VIEW DETAILS</a>
												</div>
											</div>
										</div>
										<div class="col-xl-6 col-md-6 col-sm-5">
											<div class="banner-media">
												<div class="img-preview" data-swiper-parallax="-100">
													<img src="images/main-slider/slider2/pic1.jpg" alt="banner-media">
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="swiper-slide bg-light">
							<div class="container">
								<div class="banner-content">
									<div class="row">
										<div class="col-xl-6 col-md-6 col-sm-7 align-self-center">
											<div class="swiper-content">
												<div class="content-info">
													<h1 class="offer-title mb-0" data-swiper-parallax="-20">SALE 40%
													</h1>
													<h2 class="title mb-2" data-swiper-parallax="-20">For Gym, yoga,
														running.</h2>
													<p class="sub-title mb-0" data-swiper-parallax="-40">No code need.
														Plus free shippng on $99+ orders!</p>
												</div>
												<div class="content-btn" data-swiper-parallax="-60">
													<a class="btn btn-secondary  me-3" href="shop-cart.html">ADD TO
														CART</a>
													<a class="btn btn-outline-secondary "
														href="product-default.html">VIEW DETAILS</a>
												</div>
											</div>
										</div>
										<div class="col-xl-6 col-md-6 col-sm-5">
											<div class="banner-media">
												<div class="img-preview" data-swiper-parallax="-100">
													<img src="images/main-slider/slider2/pic2.jpg" alt="banner-media">
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div> -->
					<div class="banner-social-media style-2 left">
						<ul>
							<li>
								<a href="https://www.facebook.com/kumpe3d" target="_blank">Facebook</a>
							</li>
							<li>
								<a href="https://www.instagram.com/kumpe3d" target="_blank">Instagram</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
			<!--Swiper Banner End-->

			<!-- Categories Start-->
			<section class="content-inner-1 py-0 overlay-white-middle">
				<div class="container-fluid p-3">
					<div class="swiper swiper-product">
						<div id="categoriesSwiper" class="swiper-wrapper product-style2">
							<!-- Start Category -->
							<div class="swiper-slide">
								<div class="product-box style-2 wow fadeInUp" data-wow-delay="0.4s"
									style="background-image: url('');">
									<div class="product-content">
										<div class="main-content">
											<h2 class="product-name">All Products</h2>
										</div>
										<a href="shop" class="btn btn-dark">Shop Now</a>
									</div>
								</div>
							</div>
							<!-- End Category -->
						</div>
					</div>
				</div>
			</section>
			<!-- Categories End-->

			<!-- Trending Start-->
			<!-- <section class="content-inner-1 overlay-white-middle overflow-hidden">
				<div class="container">
					<div class="section-head style-2 wow fadeInUp" data-wow-delay="0.1s">
						<div class="left-content">
							<h2 class="title">What's trending now</h2>
							<p>Discover the most trending products in Mooncart.</p>
						</div>
						<a href="shop" class="text-secondary font-14 d-flex align-items-center gap-1">See all
							products
							<i class="icon feather icon-chevron-right font-18"></i>
						</a>
					</div>
					<div class="swiper-btn-center-lr">
						<div class="swiper swiper-four">
							<div class="swiper-wrapper">
								<div class="swiper-slide">
									<div class="shop-card wow fadeInUp" data-wow-delay="0.2s">
										<div class="dz-media">
											<img src="images/shop/product/9.png" alt="image">
											<div class="shop-meta">
												<a href="javascript:void(0);" class="btn btn-secondary btn-icon"
													data-bs-toggle="modal" data-bs-target="#exampleModal">
													<i class="fa-solid fa-eye d-md-none d-block"></i>
													<span class="d-md-block d-none">Quick View</span>
												</a>
												<div class="btn btn-primary meta-icon dz-wishicon">
													<svg class="dz-heart-fill" width="14" height="12"
														viewBox="0 0 14 12" fill="none"
														xmlns="http://www.w3.org/2000/svg">
														<path
															d="M13.6412 5.80113C13.0778 6.9649 12.0762 8.02624 11.1657 8.8827C10.5113 9.49731 9.19953 10.7322 7.77683 11.62C7.30164 11.9159 6.69842 11.9159 6.22323 11.62C4.80338 10.7322 3.4888 9.49731 2.83435 8.8827C1.92382 8.02624 0.92224 6.96205 0.358849 5.80113C-0.551681 3.91747 0.344622 1.44196 2.21121 0.557041C3.98674 -0.282354 5.54034 0.292418 7.00003 1.44765C8.45972 0.292418 10.0133 -0.282354 11.786 0.557041C13.6554 1.44196 14.5517 3.91747 13.6412 5.80113Z"
															fill="white" />
													</svg>
													<svg class="dz-heart feather feather-heart"
														xmlns="http://www.w3.org/2000/svg" width="14" height="14"
														viewBox="0 0 24 24" fill="none" stroke="currentColor"
														stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
														<path
															d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z">
														</path>
													</svg>
												</div>
												<div class="btn btn-primary meta-icon dz-carticon">
													<svg class="dz-cart-check" width="15" height="15"
														viewBox="0 0 15 15" fill="none"
														xmlns="http://www.w3.org/2000/svg">
														<path d="M11.9144 3.73438L5.49772 10.151L2.58105 7.23438"
															stroke="white" stroke-width="2" stroke-linecap="round"
															stroke-linejoin="round" />
													</svg>
													<svg class="dz-cart-out" width="14" height="14" viewBox="0 0 14 14"
														fill="none" xmlns="http://www.w3.org/2000/svg">
														<path
															d="M10.6033 10.4092C9.70413 10.4083 8.97452 11.1365 8.97363 12.0357C8.97274 12.9348 9.70097 13.6644 10.6001 13.6653C11.4993 13.6662 12.2289 12.938 12.2298 12.0388C12.2298 12.0383 12.2298 12.0378 12.2298 12.0373C12.2289 11.1391 11.5014 10.4109 10.6033 10.4092Z"
															fill="white" />
														<path
															d="M13.4912 2.6132C13.4523 2.60565 13.4127 2.60182 13.373 2.60176H3.46022L3.30322 1.55144C3.20541 0.853911 2.60876 0.334931 1.90439 0.334717H0.627988C0.281154 0.334717 0 0.61587 0 0.962705C0 1.30954 0.281154 1.59069 0.627988 1.59069H1.90595C1.9858 1.59011 2.05338 1.64957 2.06295 1.72886L3.03004 8.35727C3.16263 9.19953 3.88712 9.8209 4.73975 9.82363H11.2724C12.0933 9.8247 12.8015 9.24777 12.9664 8.44362L13.9884 3.34906C14.0543 3.00854 13.8317 2.67909 13.4912 2.6132Z"
															fill="white" />
														<path
															d="M6.61539 11.9676C6.57716 11.0948 5.85687 10.4077 4.98324 10.4108C4.08483 10.4471 3.38595 11.2048 3.42225 12.1032C3.45708 12.9653 4.15833 13.6505 5.02092 13.6653H5.06017C5.95846 13.626 6.65474 12.8658 6.61539 11.9676Z"
															fill="white" />
													</svg>
												</div>
											</div>
										</div>
										<div class="dz-content">
											<h5 class="title"><a href="shop-list.html">Protein Supplements</a></h5>
											<ul class="star-rating">
												<li>
													<svg width="14" height="14" viewBox="0 0 14 14" fill="none"
														xmlns="http://www.w3.org/2000/svg">
														<path
															d="M7.24805 0.734375L9.22301 5.01608L13.9054 5.57126L10.4436 8.77267L11.3625 13.3975L7.24805 11.0944L3.13355 13.3975L4.0525 8.77267L0.590651 5.57126L5.27309 5.01608L7.24805 0.734375Z"
															fill="#FF8A00"></path>
													</svg>
												</li>
												<li>
													<svg width="14" height="14" viewBox="0 0 14 14" fill="none"
														xmlns="http://www.w3.org/2000/svg">
														<path
															d="M7.24805 0.734375L9.22301 5.01608L13.9054 5.57126L10.4436 8.77267L11.3625 13.3975L7.24805 11.0944L3.13355 13.3975L4.0525 8.77267L0.590651 5.57126L5.27309 5.01608L7.24805 0.734375Z"
															fill="#FF8A00"></path>
													</svg>
												</li>
												<li>
													<svg width="14" height="14" viewBox="0 0 14 14" fill="none"
														xmlns="http://www.w3.org/2000/svg">
														<path
															d="M7.24805 0.734375L9.22301 5.01608L13.9054 5.57126L10.4436 8.77267L11.3625 13.3975L7.24805 11.0944L3.13355 13.3975L4.0525 8.77267L0.590651 5.57126L5.27309 5.01608L7.24805 0.734375Z"
															fill="#FF8A00"></path>
													</svg>
												</li>
												<li>
													<svg width="14" height="14" viewBox="0 0 14 14" fill="none"
														xmlns="http://www.w3.org/2000/svg">
														<path
															d="M7.24805 0.734375L9.22301 5.01608L13.9054 5.57126L10.4436 8.77267L11.3625 13.3975L7.24805 11.0944L3.13355 13.3975L4.0525 8.77267L0.590651 5.57126L5.27309 5.01608L7.24805 0.734375Z"
															fill="#E4E5E8"></path>
													</svg>
												</li>
												<li>
													<svg width="14" height="14" viewBox="0 0 14 14" fill="none"
														xmlns="http://www.w3.org/2000/svg">
														<path
															d="M7.24805 0.734375L9.22301 5.01608L13.9054 5.57126L10.4436 8.77267L11.3625 13.3975L7.24805 11.0944L3.13355 13.3975L4.0525 8.77267L0.590651 5.57126L5.27309 5.01608L7.24805 0.734375Z"
															fill="#E4E5E8"></path>
													</svg>
												</li>
											</ul>
											<h6 class="price">
												<del>$85.00</del>
												$40.00
											</h6>
										</div>
										<div class="product-tag">
											<span class="badge badge-secondary">-31%</span>
										</div>
									</div>
								</div>
								<div class="swiper-slide">
									<div class="shop-card wow fadeInUp" data-wow-delay="0.3s">
										<div class="dz-media">
											<img src="images/shop/product/10.png" alt="image">
											<div class="shop-meta">
												<a href="javascript:void(0);" class="btn btn-secondary btn-icon"
													data-bs-toggle="modal" data-bs-target="#exampleModal">
													<i class="fa-solid fa-eye d-md-none d-block"></i>
													<span class="d-md-block d-none">Quick View</span>
												</a>
												<div class="btn btn-primary meta-icon dz-wishicon">
													<svg class="dz-heart-fill" width="14" height="12"
														viewBox="0 0 14 12" fill="none"
														xmlns="http://www.w3.org/2000/svg">
														<path
															d="M13.6412 5.80113C13.0778 6.9649 12.0762 8.02624 11.1657 8.8827C10.5113 9.49731 9.19953 10.7322 7.77683 11.62C7.30164 11.9159 6.69842 11.9159 6.22323 11.62C4.80338 10.7322 3.4888 9.49731 2.83435 8.8827C1.92382 8.02624 0.92224 6.96205 0.358849 5.80113C-0.551681 3.91747 0.344622 1.44196 2.21121 0.557041C3.98674 -0.282354 5.54034 0.292418 7.00003 1.44765C8.45972 0.292418 10.0133 -0.282354 11.786 0.557041C13.6554 1.44196 14.5517 3.91747 13.6412 5.80113Z"
															fill="white" />
													</svg>
													<svg class="dz-heart feather feather-heart"
														xmlns="http://www.w3.org/2000/svg" width="14" height="14"
														viewBox="0 0 24 24" fill="none" stroke="currentColor"
														stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
														<path
															d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z">
														</path>
													</svg>
												</div>
												<div class="btn btn-primary meta-icon dz-carticon">
													<svg class="dz-cart-check" width="15" height="15"
														viewBox="0 0 15 15" fill="none"
														xmlns="http://www.w3.org/2000/svg">
														<path d="M11.9144 3.73438L5.49772 10.151L2.58105 7.23438"
															stroke="white" stroke-width="2" stroke-linecap="round"
															stroke-linejoin="round" />
													</svg>
													<svg class="dz-cart-out" width="14" height="14" viewBox="0 0 14 14"
														fill="none" xmlns="http://www.w3.org/2000/svg">
														<path
															d="M10.6033 10.4092C9.70413 10.4083 8.97452 11.1365 8.97363 12.0357C8.97274 12.9348 9.70097 13.6644 10.6001 13.6653C11.4993 13.6662 12.2289 12.938 12.2298 12.0388C12.2298 12.0383 12.2298 12.0378 12.2298 12.0373C12.2289 11.1391 11.5014 10.4109 10.6033 10.4092Z"
															fill="white" />
														<path
															d="M13.4912 2.6132C13.4523 2.60565 13.4127 2.60182 13.373 2.60176H3.46022L3.30322 1.55144C3.20541 0.853911 2.60876 0.334931 1.90439 0.334717H0.627988C0.281154 0.334717 0 0.61587 0 0.962705C0 1.30954 0.281154 1.59069 0.627988 1.59069H1.90595C1.9858 1.59011 2.05338 1.64957 2.06295 1.72886L3.03004 8.35727C3.16263 9.19953 3.88712 9.8209 4.73975 9.82363H11.2724C12.0933 9.8247 12.8015 9.24777 12.9664 8.44362L13.9884 3.34906C14.0543 3.00854 13.8317 2.67909 13.4912 2.6132Z"
															fill="white" />
														<path
															d="M6.61539 11.9676C6.57716 11.0948 5.85687 10.4077 4.98324 10.4108C4.08483 10.4471 3.38595 11.2048 3.42225 12.1032C3.45708 12.9653 4.15833 13.6505 5.02092 13.6653H5.06017C5.95846 13.626 6.65474 12.8658 6.61539 11.9676Z"
															fill="white" />
													</svg>
												</div>
											</div>
										</div>
										<div class="dz-content">
											<h5 class="title"><a href="shop-list.html">Yoga mats and accessories</a>
											</h5>
											<ul class="star-rating">
												<li>
													<svg width="14" height="14" viewBox="0 0 14 14" fill="none"
														xmlns="http://www.w3.org/2000/svg">
														<path
															d="M7.24805 0.734375L9.22301 5.01608L13.9054 5.57126L10.4436 8.77267L11.3625 13.3975L7.24805 11.0944L3.13355 13.3975L4.0525 8.77267L0.590651 5.57126L5.27309 5.01608L7.24805 0.734375Z"
															fill="#FF8A00"></path>
													</svg>
												</li>
												<li>
													<svg width="14" height="14" viewBox="0 0 14 14" fill="none"
														xmlns="http://www.w3.org/2000/svg">
														<path
															d="M7.24805 0.734375L9.22301 5.01608L13.9054 5.57126L10.4436 8.77267L11.3625 13.3975L7.24805 11.0944L3.13355 13.3975L4.0525 8.77267L0.590651 5.57126L5.27309 5.01608L7.24805 0.734375Z"
															fill="#FF8A00"></path>
													</svg>
												</li>
												<li>
													<svg width="14" height="14" viewBox="0 0 14 14" fill="none"
														xmlns="http://www.w3.org/2000/svg">
														<path
															d="M7.24805 0.734375L9.22301 5.01608L13.9054 5.57126L10.4436 8.77267L11.3625 13.3975L7.24805 11.0944L3.13355 13.3975L4.0525 8.77267L0.590651 5.57126L5.27309 5.01608L7.24805 0.734375Z"
															fill="#FF8A00"></path>
													</svg>
												</li>
												<li>
													<svg width="14" height="14" viewBox="0 0 14 14" fill="none"
														xmlns="http://www.w3.org/2000/svg">
														<path
															d="M7.24805 0.734375L9.22301 5.01608L13.9054 5.57126L10.4436 8.77267L11.3625 13.3975L7.24805 11.0944L3.13355 13.3975L4.0525 8.77267L0.590651 5.57126L5.27309 5.01608L7.24805 0.734375Z"
															fill="#E4E5E8"></path>
													</svg>
												</li>
												<li>
													<svg width="14" height="14" viewBox="0 0 14 14" fill="none"
														xmlns="http://www.w3.org/2000/svg">
														<path
															d="M7.24805 0.734375L9.22301 5.01608L13.9054 5.57126L10.4436 8.77267L11.3625 13.3975L7.24805 11.0944L3.13355 13.3975L4.0525 8.77267L0.590651 5.57126L5.27309 5.01608L7.24805 0.734375Z"
															fill="#E4E5E8"></path>
													</svg>
												</li>
											</ul>
											<h6 class="price">
												<del>$55.00</del>
												$10.00
											</h6>
										</div>
										<div class="product-tag">
											<span class="badge badge-secondary">-20%</span>
											<span class="badge badge-primary">Featured</span>
										</div>
									</div>
								</div>
								<div class="swiper-slide">
									<div class="shop-card wow fadeInUp" data-wow-delay="0.4s">
										<div class="dz-media">
											<img src="images/shop/product/5.png" alt="image">
											<div class="shop-meta">
												<a href="javascript:void(0);" class="btn btn-secondary btn-icon"
													data-bs-toggle="modal" data-bs-target="#exampleModal">
													<i class="fa-solid fa-eye d-md-none d-block"></i>
													<span class="d-md-block d-none">Quick View</span>
												</a>
												<div class="btn btn-primary meta-icon dz-wishicon">
													<svg class="dz-heart-fill" width="14" height="12"
														viewBox="0 0 14 12" fill="none"
														xmlns="http://www.w3.org/2000/svg">
														<path
															d="M13.6412 5.80113C13.0778 6.9649 12.0762 8.02624 11.1657 8.8827C10.5113 9.49731 9.19953 10.7322 7.77683 11.62C7.30164 11.9159 6.69842 11.9159 6.22323 11.62C4.80338 10.7322 3.4888 9.49731 2.83435 8.8827C1.92382 8.02624 0.92224 6.96205 0.358849 5.80113C-0.551681 3.91747 0.344622 1.44196 2.21121 0.557041C3.98674 -0.282354 5.54034 0.292418 7.00003 1.44765C8.45972 0.292418 10.0133 -0.282354 11.786 0.557041C13.6554 1.44196 14.5517 3.91747 13.6412 5.80113Z"
															fill="white" />
													</svg>
													<svg class="dz-heart feather feather-heart"
														xmlns="http://www.w3.org/2000/svg" width="14" height="14"
														viewBox="0 0 24 24" fill="none" stroke="currentColor"
														stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
														<path
															d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z">
														</path>
													</svg>
												</div>
												<div class="btn btn-primary meta-icon dz-carticon">
													<svg class="dz-cart-check" width="15" height="15"
														viewBox="0 0 15 15" fill="none"
														xmlns="http://www.w3.org/2000/svg">
														<path d="M11.9144 3.73438L5.49772 10.151L2.58105 7.23438"
															stroke="white" stroke-width="2" stroke-linecap="round"
															stroke-linejoin="round" />
													</svg>
													<svg class="dz-cart-out" width="14" height="14" viewBox="0 0 14 14"
														fill="none" xmlns="http://www.w3.org/2000/svg">
														<path
															d="M10.6033 10.4092C9.70413 10.4083 8.97452 11.1365 8.97363 12.0357C8.97274 12.9348 9.70097 13.6644 10.6001 13.6653C11.4993 13.6662 12.2289 12.938 12.2298 12.0388C12.2298 12.0383 12.2298 12.0378 12.2298 12.0373C12.2289 11.1391 11.5014 10.4109 10.6033 10.4092Z"
															fill="white" />
														<path
															d="M13.4912 2.6132C13.4523 2.60565 13.4127 2.60182 13.373 2.60176H3.46022L3.30322 1.55144C3.20541 0.853911 2.60876 0.334931 1.90439 0.334717H0.627988C0.281154 0.334717 0 0.61587 0 0.962705C0 1.30954 0.281154 1.59069 0.627988 1.59069H1.90595C1.9858 1.59011 2.05338 1.64957 2.06295 1.72886L3.03004 8.35727C3.16263 9.19953 3.88712 9.8209 4.73975 9.82363H11.2724C12.0933 9.8247 12.8015 9.24777 12.9664 8.44362L13.9884 3.34906C14.0543 3.00854 13.8317 2.67909 13.4912 2.6132Z"
															fill="white" />
														<path
															d="M6.61539 11.9676C6.57716 11.0948 5.85687 10.4077 4.98324 10.4108C4.08483 10.4471 3.38595 11.2048 3.42225 12.1032C3.45708 12.9653 4.15833 13.6505 5.02092 13.6653H5.06017C5.95846 13.626 6.65474 12.8658 6.61539 11.9676Z"
															fill="white" />
													</svg>
												</div>
											</div>
										</div>
										<div class="dz-content">
											<h5 class="title"><a href="shop-list.html">Bamboo toothbrushes</a></h5>
											<ul class="star-rating">
												<li>
													<svg width="14" height="14" viewBox="0 0 14 14" fill="none"
														xmlns="http://www.w3.org/2000/svg">
														<path
															d="M7.24805 0.734375L9.22301 5.01608L13.9054 5.57126L10.4436 8.77267L11.3625 13.3975L7.24805 11.0944L3.13355 13.3975L4.0525 8.77267L0.590651 5.57126L5.27309 5.01608L7.24805 0.734375Z"
															fill="#FF8A00"></path>
													</svg>
												</li>
												<li>
													<svg width="14" height="14" viewBox="0 0 14 14" fill="none"
														xmlns="http://www.w3.org/2000/svg">
														<path
															d="M7.24805 0.734375L9.22301 5.01608L13.9054 5.57126L10.4436 8.77267L11.3625 13.3975L7.24805 11.0944L3.13355 13.3975L4.0525 8.77267L0.590651 5.57126L5.27309 5.01608L7.24805 0.734375Z"
															fill="#FF8A00"></path>
													</svg>
												</li>
												<li>
													<svg width="14" height="14" viewBox="0 0 14 14" fill="none"
														xmlns="http://www.w3.org/2000/svg">
														<path
															d="M7.24805 0.734375L9.22301 5.01608L13.9054 5.57126L10.4436 8.77267L11.3625 13.3975L7.24805 11.0944L3.13355 13.3975L4.0525 8.77267L0.590651 5.57126L5.27309 5.01608L7.24805 0.734375Z"
															fill="#FF8A00"></path>
													</svg>
												</li>
												<li>
													<svg width="14" height="14" viewBox="0 0 14 14" fill="none"
														xmlns="http://www.w3.org/2000/svg">
														<path
															d="M7.24805 0.734375L9.22301 5.01608L13.9054 5.57126L10.4436 8.77267L11.3625 13.3975L7.24805 11.0944L3.13355 13.3975L4.0525 8.77267L0.590651 5.57126L5.27309 5.01608L7.24805 0.734375Z"
															fill="#E4E5E8"></path>
													</svg>
												</li>
												<li>
													<svg width="14" height="14" viewBox="0 0 14 14" fill="none"
														xmlns="http://www.w3.org/2000/svg">
														<path
															d="M7.24805 0.734375L9.22301 5.01608L13.9054 5.57126L10.4436 8.77267L11.3625 13.3975L7.24805 11.0944L3.13355 13.3975L4.0525 8.77267L0.590651 5.57126L5.27309 5.01608L7.24805 0.734375Z"
															fill="#E4E5E8"></path>
													</svg>
												</li>
											</ul>
											<h6 class="price">
												<del>$25.00</del>
												$10.00
											</h6>
										</div>
										<div class="product-tag">
											<span class="badge badge-secondary">Sale</span>
										</div>
									</div>
								</div>
								<div class="swiper-slide">
									<div class="shop-card wow fadeInUp" data-wow-delay="0.5s">
										<div class="dz-media">
											<img src="images/shop/product/11.png" alt="image">
											<div class="shop-meta">
												<a href="javascript:void(0);" class="btn btn-secondary btn-icon"
													data-bs-toggle="modal" data-bs-target="#exampleModal">
													<i class="fa-solid fa-eye d-md-none d-block"></i>
													<span class="d-md-block d-none">Quick View</span>
												</a>
												<div class="btn btn-primary meta-icon dz-wishicon">
													<svg class="dz-heart-fill" width="14" height="12"
														viewBox="0 0 14 12" fill="none"
														xmlns="http://www.w3.org/2000/svg">
														<path
															d="M13.6412 5.80113C13.0778 6.9649 12.0762 8.02624 11.1657 8.8827C10.5113 9.49731 9.19953 10.7322 7.77683 11.62C7.30164 11.9159 6.69842 11.9159 6.22323 11.62C4.80338 10.7322 3.4888 9.49731 2.83435 8.8827C1.92382 8.02624 0.92224 6.96205 0.358849 5.80113C-0.551681 3.91747 0.344622 1.44196 2.21121 0.557041C3.98674 -0.282354 5.54034 0.292418 7.00003 1.44765C8.45972 0.292418 10.0133 -0.282354 11.786 0.557041C13.6554 1.44196 14.5517 3.91747 13.6412 5.80113Z"
															fill="white" />
													</svg>
													<svg class="dz-heart feather feather-heart"
														xmlns="http://www.w3.org/2000/svg" width="14" height="14"
														viewBox="0 0 24 24" fill="none" stroke="currentColor"
														stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
														<path
															d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z">
														</path>
													</svg>
												</div>
												<div class="btn btn-primary meta-icon dz-carticon">
													<svg class="dz-cart-check" width="15" height="15"
														viewBox="0 0 15 15" fill="none"
														xmlns="http://www.w3.org/2000/svg">
														<path d="M11.9144 3.73438L5.49772 10.151L2.58105 7.23438"
															stroke="white" stroke-width="2" stroke-linecap="round"
															stroke-linejoin="round" />
													</svg>
													<svg class="dz-cart-out" width="14" height="14" viewBox="0 0 14 14"
														fill="none" xmlns="http://www.w3.org/2000/svg">
														<path
															d="M10.6033 10.4092C9.70413 10.4083 8.97452 11.1365 8.97363 12.0357C8.97274 12.9348 9.70097 13.6644 10.6001 13.6653C11.4993 13.6662 12.2289 12.938 12.2298 12.0388C12.2298 12.0383 12.2298 12.0378 12.2298 12.0373C12.2289 11.1391 11.5014 10.4109 10.6033 10.4092Z"
															fill="white" />
														<path
															d="M13.4912 2.6132C13.4523 2.60565 13.4127 2.60182 13.373 2.60176H3.46022L3.30322 1.55144C3.20541 0.853911 2.60876 0.334931 1.90439 0.334717H0.627988C0.281154 0.334717 0 0.61587 0 0.962705C0 1.30954 0.281154 1.59069 0.627988 1.59069H1.90595C1.9858 1.59011 2.05338 1.64957 2.06295 1.72886L3.03004 8.35727C3.16263 9.19953 3.88712 9.8209 4.73975 9.82363H11.2724C12.0933 9.8247 12.8015 9.24777 12.9664 8.44362L13.9884 3.34906C14.0543 3.00854 13.8317 2.67909 13.4912 2.6132Z"
															fill="white" />
														<path
															d="M6.61539 11.9676C6.57716 11.0948 5.85687 10.4077 4.98324 10.4108C4.08483 10.4471 3.38595 11.2048 3.42225 12.1032C3.45708 12.9653 4.15833 13.6505 5.02092 13.6653H5.06017C5.95846 13.626 6.65474 12.8658 6.61539 11.9676Z"
															fill="white" />
													</svg>
												</div>
											</div>
										</div>
										<div class="dz-content">
											<h5 class="title"><a href="shop-list.html">Fitness trackers</a></h5>
											<ul class="star-rating">
												<li>
													<svg width="14" height="14" viewBox="0 0 14 14" fill="none"
														xmlns="http://www.w3.org/2000/svg">
														<path
															d="M7.24805 0.734375L9.22301 5.01608L13.9054 5.57126L10.4436 8.77267L11.3625 13.3975L7.24805 11.0944L3.13355 13.3975L4.0525 8.77267L0.590651 5.57126L5.27309 5.01608L7.24805 0.734375Z"
															fill="#FF8A00"></path>
													</svg>
												</li>
												<li>
													<svg width="14" height="14" viewBox="0 0 14 14" fill="none"
														xmlns="http://www.w3.org/2000/svg">
														<path
															d="M7.24805 0.734375L9.22301 5.01608L13.9054 5.57126L10.4436 8.77267L11.3625 13.3975L7.24805 11.0944L3.13355 13.3975L4.0525 8.77267L0.590651 5.57126L5.27309 5.01608L7.24805 0.734375Z"
															fill="#FF8A00"></path>
													</svg>
												</li>
												<li>
													<svg width="14" height="14" viewBox="0 0 14 14" fill="none"
														xmlns="http://www.w3.org/2000/svg">
														<path
															d="M7.24805 0.734375L9.22301 5.01608L13.9054 5.57126L10.4436 8.77267L11.3625 13.3975L7.24805 11.0944L3.13355 13.3975L4.0525 8.77267L0.590651 5.57126L5.27309 5.01608L7.24805 0.734375Z"
															fill="#FF8A00"></path>
													</svg>
												</li>
												<li>
													<svg width="14" height="14" viewBox="0 0 14 14" fill="none"
														xmlns="http://www.w3.org/2000/svg">
														<path
															d="M7.24805 0.734375L9.22301 5.01608L13.9054 5.57126L10.4436 8.77267L11.3625 13.3975L7.24805 11.0944L3.13355 13.3975L4.0525 8.77267L0.590651 5.57126L5.27309 5.01608L7.24805 0.734375Z"
															fill="#E4E5E8"></path>
													</svg>
												</li>
												<li>
													<svg width="14" height="14" viewBox="0 0 14 14" fill="none"
														xmlns="http://www.w3.org/2000/svg">
														<path
															d="M7.24805 0.734375L9.22301 5.01608L13.9054 5.57126L10.4436 8.77267L11.3625 13.3975L7.24805 11.0944L3.13355 13.3975L4.0525 8.77267L0.590651 5.57126L5.27309 5.01608L7.24805 0.734375Z"
															fill="#E4E5E8"></path>
													</svg>
												</li>
											</ul>
											<h6 class="price">
												<del>$65.00</del>
												$20.00
											</h6>
										</div>
										<div class="product-tag">
											<span class="badge badge-secondary">-31%</span>
											<span class="badge badge-primary">Featured</span>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="pagination-align">
							<div class="tranding-button-prev btn-prev">
								<i class="flaticon flaticon-left-chevron"></i>
							</div>
							<div class="tranding-button-next btn-next">
								<i class="flaticon flaticon-chevron"></i>
							</div>
						</div>
					</div>
				</div>
			</section> -->
			<!-- Trending Stop-->
			<!-- Facebook Feed Start -->
			<script src="https://static.elfsight.com/platform/platform.js" data-use-service-core></script>
			<div class="elfsight-app-40f378db-d0ba-4120-a15a-546ae07fd963" data-elfsight-app-lazy></div>
			<!-- Facebook Feed End -->
			<!-- Facebook Reviews Start -->
			<script src="https://static.elfsight.com/platform/platform.js" data-use-service-core></script>
			<div id="reviews" class="elfsight-app-4678166b-13ea-45e7-b03d-7e659c19efbc" data-elfsight-app-lazy></div>
			<div id="reviews-end"></div>
			<!-- Facebook Reviews End -->

		</div>

		<?php
		include("./includes/footer.php");
		?>

		<button class="scroltop" type="button"><i class="fas fa-arrow-up"></i></button>

	</div>

	<button class="scroltop" type="button"><i class="fas fa-arrow-up"></i></button>

	</div>
	<!-- JAVASCRIPT FILES ========================================= -->
	<script src="js/jquery.min.js"></script><!-- JQUERY MIN JS -->
	<script src="vendor-js/wow/wow.min.js"></script><!-- WOW JS -->
	<script src="vendor-js/bootstrap/dist/js/bootstrap.bundle.min.js"></script><!-- BOOTSTRAP MIN JS -->
	<script src="vendor-js/bootstrap-select/dist/js/bootstrap-select.min.js"></script><!-- BOOTSTRAP SELECT MIN JS -->
	<script src="vendor-js/bootstrap-touchspin/bootstrap-touchspin.js"></script><!-- BOOTSTRAP TOUCHSPIN JS -->

	<script src="vendor-js/magnific-popup/magnific-popup.js"></script><!-- MAGNIFIC POPUP JS -->
	<script src="vendor-js/counter/waypoints-min.js"></script><!-- WAYPOINTS JS -->
	<script src="vendor-js/counter/counterup.min.js"></script><!-- COUNTERUP JS -->
	<script src="vendor-js/swiper/swiper-bundle.min.js"></script><!-- SWIPER JS -->
	<script src="vendor-js/imagesloaded/imagesloaded.js"></script><!-- IMAGESLOADED-->
	<script src="vendor-js/masonry/masonry-4.2.2.js"></script><!-- MASONRY -->
	<script src="vendor-js/masonry/isotope.pkgd.min.js"></script><!-- ISOTOPE -->
	<script src="vendor-js/countdown/jquery.countdown.js"></script><!-- COUNTDOWN FUCTIONS  -->
	<script src="js/dz.carousel.js"></script><!-- DZ CAROUSEL JS -->
	<script src="vendor-js/lightgallery/dist/lightgallery.min.js"></script>
	<script src="vendor-js/lightgallery/dist/plugins/thumbnail/lg-thumbnail.min.js"></script>
	<script src="vendor-js/lightgallery/dist/plugins/zoom/lg-zoom.min.js"></script>
	<script src="js/dz.ajax.js"></script><!-- AJAX -->
	<script src="js/custom.js"></script><!-- CUSTOM JS -->
	<script src="js/home.js?version=202311241652"></script>
</body>

</html>

<?php
mysqli_close($conn);
?>