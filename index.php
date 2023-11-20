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
					<div class="swiper-wrapper">
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
					</div>
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

			<!-- Product Start-->
			<section class="content-inner-1 py-0 overlay-white-middle">
				<div class="container-fluid p-3">
					<div class="swiper swiper-product">
						<div class="swiper-wrapper product-style2">
							<div class="swiper-slide">
								<div class="product-box style-2 wow fadeInUp" data-wow-delay="0.1s"
									style="background-image: url('images/gym/1.png');">
									<div class="product-content">
										<div class="main-content">
											<h2 class="product-name">Home fitness equipment</h2>
											<span class="offer">UP TO 60% OFF</span>
										</div>
										<a href="shop-list.html" class="btn btn-outline-secondary">Shop Now</a>
									</div>
								</div>
							</div>
							<div class="swiper-slide">
								<div class="product-box style-2 wow fadeInUp" data-wow-delay="0.2s"
									style="background-image: url('images/gym/2.png');">
									<div class="product-content">
										<div class="main-content">
											<h2 class="product-name">Yoga mats and accessories</h2>
											<span class="offer">UP TO 60% OFF</span>
										</div>
										<a href="shop-list.html" class="btn btn-outline-secondary">Shop Now</a>
									</div>
								</div>
							</div>
							<div class="swiper-slide">
								<div class="product-box style-2 wow fadeInUp" data-wow-delay="0.3s"
									style="background-image: url('images/gym/3.png');">
									<div class="product-content">
										<div class="main-content">
											<h2 class="product-name">Plant-based protein supplements</h2>
											<span class="offer">UP TO 60% OFF</span>
										</div>
										<a href="shop-list.html" class="btn btn-outline-secondary">Shop Now</a>
									</div>
								</div>
							</div>
							<div class="swiper-slide">
								<div class="product-box style-2 wow fadeInUp" data-wow-delay="0.4s"
									style="background-image: url('images/gym/4.png');">
									<div class="product-content">
										<div class="main-content">
											<h2 class="product-name">Nike Sports shoes</h2>
											<span class="offer">UP TO 60% OFF</span>
										</div>
										<a href="shop-list.html" class="btn btn-outline-secondary">Shop Now</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
			<!-- Product End-->

			<!--Featured Section Start-->
			<section class="content-inner overlay-white-middle">
				<div class="container">
					<div class="section-head style-2 wow fadeInUp" data-wow-delay="0.1s">
						<div class="left-content">
							<h2 class="title">Featured Categories</h2>
							<p>Discover the most trending products in Mooncart.</p>
						</div>
						<a href="shop-list.html" class="text-secondary font-14 d-flex align-items-center gap-1">See all
							products
							<i class="icon feather icon-chevron-right font-18"></i>
						</a>
					</div>
					<div class="row gx-xl-4 g-3">
						<div class="col-xl-4 col-lg-4 col-md-4 col-6 wow fadeInUp" data-wow-delay="0.2s">
							<div class="category-product left">
								<a href="javascript:void(0);">
									<img src="images/category/pic1.jpg" alt="">
									<div class="category-badge">Exercise Ball</div>
								</a>
							</div>
						</div>
						<div class="col-xl-3 col-lg-4 col-md-4 col-6 wow fadeInUp" data-wow-delay="0.3s">
							<div class="category-product left">
								<a href="javascript:void(0);">
									<img src="images/category/pic2.jpg" alt="">
									<div class="category-badge">Fitness trackers</div>
								</a>
							</div>
						</div>
						<div class="col-xl-5 col-lg-4 col-md-4 col-6 wow fadeInUp" data-wow-delay="0.4s">
							<div class="category-product left">
								<a href="javascript:void(0);">
									<img src="images/category/pic3.jpg" alt="">
									<div class="category-badge">Yoga Ring</div>
								</a>
							</div>
						</div>
						<div class="col-xl-4 col-lg-4 col-md-4 col-6 wow fadeInUp" data-wow-delay="0.5s">
							<div class="category-product right">
								<a href="javascript:void(0);">
									<img src="images/category/pic4.jpg" alt="">
									<div class="category-badge">Dumbbells</div>
								</a>
							</div>
						</div>
						<div class="col-xl-5 col-lg-4 col-md-4 col-6 wow fadeInUp" data-wow-delay="0.6s">
							<div class="category-product right">
								<a href="javascript:void(0);">
									<img src="images/category/pic5.jpg" alt="">
									<div class="category-badge">Yoga mats</div>
								</a>
							</div>
						</div>
						<div class="col-xl-3 col-lg-4 col-md-4 col-6 wow fadeInUp" data-wow-delay="0.7s">
							<div class="category-product right">
								<a href="javascript:void(0);">
									<img src="images/category/pic6.jpg" alt="">
									<div class="category-badge">Protein</div>
								</a>
							</div>
						</div>
					</div>
				</div>
			</section>
			<!--Featured Section End-->

			<!-- icon-box1 -->
			<section class="content-inner-3 overlay-white-dark"
				style="background-image: url('images/background/bg2.jpg'); background-repeat: no-repeat; background-size: cover;">
				<div class="container">
					<div class="row justify-content-center gx-sm-1">
						<div class="col-lg-4 col-md-4 col-sm-4 p-b30 wow fadeInUp" data-wow-delay="0.1s">
							<div class="icon-bx-wraper style-1 text-center">
								<div class="icon-bx">
									<i class="flaticon flaticon-fast-delivery"></i>
								</div>
								<div class="icon-content">
									<h3 class="dz-title m-b0">FREE SHIPPING</h3>
									<div class="square"></div>
									<p class="font-20">Enjoy free shipping on all orders - no minimum purchase required.
									</p>
								</div>
							</div>
						</div>
						<div class="col-lg-4 col-md-4 col-sm-4 p-b30 wow fadeInUp" data-wow-delay="0.2s">
							<div class="icon-bx-wraper style-1 text-center">
								<div class="icon-bx">
									<i class="flaticon flaticon-message"></i>
								</div>
								<div class="icon-content">
									<h3 class="dz-title m-b0">24/7 SUPPORT</h3>
									<div class="square"></div>
									<p class="font-20">Our team is available 24/7 to help with any questions or
										concerns.</p>
								</div>
							</div>
						</div>
						<div class="col-lg-4 col-md-4 col-sm-4 p-b30 wow fadeInUp" data-wow-delay="0.3s">
							<div class="icon-bx-wraper style-1 text-center">
								<div class="icon-bx">
									<i class="flaticon flaticon-money-back-guarantee"></i>
								</div>
								<div class="icon-content">
									<h3 class="dz-title m-b0">MONEY BACK</h3>
									<div class="square"></div>
									<p class="font-20">We offer a 100% money-back guarantee for your satisfaction.</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
			<!-- icon-box1 End-->

			<!-- Tranding Start-->
			<section class="content-inner-1 overlay-white-middle overflow-hidden">
				<div class="container">
					<div class="section-head style-2 wow fadeInUp" data-wow-delay="0.1s">
						<div class="left-content">
							<h2 class="title">What's trending now</h2>
							<p>Discover the most trending products in Mooncart.</p>
						</div>
						<a href="shop-list.html" class="text-secondary font-14 d-flex align-items-center gap-1">See all
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
			</section>
			<!-- Tranding Stop-->

			<!-- Video Bx Start-->
			<section class="content-inner overlay-black-light"
				style="background-image: url('images/background/bg3.jpg'); background-repeat: no-repeat; background-size: cover;">
				<div class="container">
					<div class="row align-items-center dz-content-bx style-1">
						<div class="col-xl-7 col-lg-7 m-b30 wow fadeInLeft" data-wow-delay="0.1s">
							<div class="dz-media video-bx1 dz-img-overlay1">
								<img src="images/video-img.png" alt="image">
								<a href="https://www.youtube.com/watch?v=IT94xC35u6k" class="popup-youtube video-btn">
									<svg width="26" height="34" viewBox="0 0 26 34" fill="none"
										xmlns="http://www.w3.org/2000/svg">
										<path
											d="M0.709318 2.07991C0.703817 0.454314 2.53706 -0.498418 3.86423 0.440304L24.3738 14.947C25.4952 15.7401 25.5017 17.4016 24.3866 18.2035L3.97574 32.8813C2.65517 33.8309 0.813587 32.8908 0.808083 31.2643L0.709318 2.07991Z"
											fill="white" />
									</svg>
								</a>
							</div>
						</div>
						<div class="col-xl-5 col-lg-5 m-b30 wow fadeInRight" data-wow-delay="0.2s">
							<div class="inner-content">
								<div class="section-head">
									<h2 class="title">Enjoy the best quality and features made by MoonCart.</h2>
									<p class="max-w500">It is a long established fact that a reader will be distracted
										by the readable content of a page when looking at its layout. The point of
										using.</p>
								</div>
								<a href="shop-list.html" class="btn text-uppercase btn-outline-light me-3">Shop Now</a>
								<a href="product-default.html" class="btn text-uppercase btn-outline-light">View
									Details</a>
							</div>
						</div>
					</div>
				</div>
			</section>
			<!-- Video Bx End-->

			<!--Recommend Section Start-->
			<section class="content-inner-1">
				<div class="container">
					<div class="row">
						<div class="col-xl-3">
							<div class="row">
								<div class="col-xl-12 col-md-6 col-sm-6 wow fadeInLeft" data-wow-delay="0.1s">
									<div class="offer-card text-center">
										<div class="dz-media">
											<img src="images/offer/pic1.jpg" alt="">
										</div>
										<div class="offer-content">
											<h3 class="title">Yoga mats and accessories</h3>
											<span class="offer">UP TO 60% OFF</span>
											<a href="shop-list.html" class="btn btn-outline-secondary">Shop Now</a>
										</div>
									</div>
								</div>
								<div class="col-xl-12 col-md-6 col-sm-6 wow fadeInLeft" data-wow-delay="0.2s">
									<div class="offer-card text-center">
										<div class="dz-media">
											<img src="images/offer/pic2.jpg" alt="">
										</div>
										<div class="offer-content">
											<h3 class="title">Plant-based protein supplements</h3>
											<span class="offer">UP TO 60% OFF</span>
											<a href="shop-list.html" class="btn btn-outline-secondary">Shop Now</a>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-xl-9">
							<div class="wow fadeInUp" data-wow-delay="0.3s">
								<h3 class="title">Most popular products</h3>
								<div class="site-filters clearfix d-flex align-items-center">
									<ul class="filters" data-bs-toggle="buttons">
										<li class="btn active">
											<input type="radio">
											<a href="javascript:void(0);">All Products <span>(20)</span></a>
										</li>
										<li data-filter=".Bottle" class="btn">
											<input type="radio">
											<a href="javascript:void(0);">Bottle <span>(10)</span></a>
										</li>
										<li data-filter=".Begs" class="btn">
											<input type="radio">
											<a href="javascript:void(0);">Begs <span>(02)</span></a>
										</li>
										<li data-filter=".Toothbrushes" class="btn">
											<input type="radio">
											<a href="javascript:void(0);">Toothbrushes <span>(08)</span></a>
										</li>
									</ul>
									<a href="shop-list.html"
										class="product-link text-secondary font-14 d-flex align-items-center gap-1 text-nowrap">See
										all products
										<i class="icon feather icon-chevron-right font-18"></i>
									</a>
								</div>
							</div>
							<div class="clearfix">
								<ul id="masonry" class="row g-xl-4 g-3">
									<li class="card-container col-6 col-xl-4 col-lg-4 col-md-4 col-sm-6 Begs">
										<div class="shop-card">
											<div class="dz-media">
												<img src="images/shop/product/12.png" alt="image">
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
															stroke-width="2" stroke-linecap="round"
															stroke-linejoin="round">
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
														<svg class="dz-cart-out" width="14" height="14"
															viewBox="0 0 14 14" fill="none"
															xmlns="http://www.w3.org/2000/svg">
															<path
																d="M10.6033 10.4092C9.70413 10.4083 8.97452 11.1365 8.97363 12.0357C8.97274 12.9348 9.70097 13.6644 10.6001 13.6653C11.4993 13.6662 12.2289 12.938 12.2298 12.0388C12.2298 12.0383 12.2298 12.0378 12.2298 12.0373C12.2289 11.1391 11.5014 10.4109 10.6033 10.4092Z"
																fill="white" />
															<path
																d="M13.4912 2.6132C13.4523 2.60565 13.4127 2.60182 13.373 2.60176H3.46022L3.30322 1.55144C3.20541 0.853911 2.60876 0.334931 1.90439 0.334717H0.627988C0.281154 0.334717 0 0.61587 0 0.962705C0 1.30954 0.281154 1.59069 0.627988 1.59069H1.90595C1.9858 1.59011 2.05338 1.64957 2.06295 1.72886L3.03004 8.35727C3.16263 9.19953 3.88712 9.8209 4.73975 9.82363H11.2724C12.0933 9.8247 12.8015 9.24777 12.9664 8.44362L13.9884 3.34906C14.0543 3.00854 13.8317 2.67909 13.4912 2.6132Z"
																fill="white" />
															<path
																d="M6.61539 11.9676C6.57716 11.0948 5.85687 10.4077 4.98324 10.4108C4.08483 10.4471 3.38595 11.2048 3.42225 12.1032C3.45708 12.9653 4.15833 13.6505 5.02092 13.6653H5.06017C5.95846 13.626 6.65474 12.8658 6.61539 11.9676Z"
																fill="white" />
															<clipPath id="clip0_5_3906">
																<rect width="14" height="14" fill="white" />
															</clipPath>
														</svg>
													</div>
												</div>
											</div>
											<div class="dz-content">
												<h5 class="title"><a href="shop-list.html">Protein supplements</a></h5>
												<ul class="star-rating">
													<li>
														<svg width="14" height="14" viewBox="0 0 14 14" fill="none"
															xmlns="http://www.w3.org/2000/svg">
															<path
																d="M7.24805 0.734375L9.22301 5.01608L13.9054 5.57126L10.4436 8.77267L11.3625 13.3975L7.24805 11.0944L3.13355 13.3975L4.0525 8.77267L0.590651 5.57126L5.27309 5.01608L7.24805 0.734375Z"
																fill="#FF8A00" />
														</svg>
													</li>
													<li>
														<svg width="14" height="14" viewBox="0 0 14 14" fill="none"
															xmlns="http://www.w3.org/2000/svg">
															<path
																d="M7.24805 0.734375L9.22301 5.01608L13.9054 5.57126L10.4436 8.77267L11.3625 13.3975L7.24805 11.0944L3.13355 13.3975L4.0525 8.77267L0.590651 5.57126L5.27309 5.01608L7.24805 0.734375Z"
																fill="#FF8A00" />
														</svg>
													</li>
													<li>
														<svg width="14" height="14" viewBox="0 0 14 14" fill="none"
															xmlns="http://www.w3.org/2000/svg">
															<path
																d="M7.24805 0.734375L9.22301 5.01608L13.9054 5.57126L10.4436 8.77267L11.3625 13.3975L7.24805 11.0944L3.13355 13.3975L4.0525 8.77267L0.590651 5.57126L5.27309 5.01608L7.24805 0.734375Z"
																fill="#FF8A00" />
														</svg>
													</li>
													<li>
														<svg width="14" height="14" viewBox="0 0 14 14" fill="none"
															xmlns="http://www.w3.org/2000/svg">
															<path
																d="M7.24805 0.734375L9.22301 5.01608L13.9054 5.57126L10.4436 8.77267L11.3625 13.3975L7.24805 11.0944L3.13355 13.3975L4.0525 8.77267L0.590651 5.57126L5.27309 5.01608L7.24805 0.734375Z"
																fill="#E4E5E8" />
														</svg>
													</li>
													<li>
														<svg width="14" height="14" viewBox="0 0 14 14" fill="none"
															xmlns="http://www.w3.org/2000/svg">
															<path
																d="M7.24805 0.734375L9.22301 5.01608L13.9054 5.57126L10.4436 8.77267L11.3625 13.3975L7.24805 11.0944L3.13355 13.3975L4.0525 8.77267L0.590651 5.57126L5.27309 5.01608L7.24805 0.734375Z"
																fill="#E4E5E8" />
														</svg>
													</li>
												</ul>
												<h6 class="price">
													<del>$45.00</del>
													$40.00
												</h6>
											</div>
											<div class="product-tag">
												<span class="badge badge-secondary">Sale</span>
											</div>
										</div>
									</li>
									<li class="card-container col-6 col-xl-4 col-lg-4 col-md-4 col-sm-6 Bottle">
										<div class="shop-card">
											<div class="dz-media">
												<img src="images/shop/product/13.png" alt="image">
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
															stroke-width="2" stroke-linecap="round"
															stroke-linejoin="round">
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
														<svg class="dz-cart-out" width="14" height="14"
															viewBox="0 0 14 14" fill="none"
															xmlns="http://www.w3.org/2000/svg">
															<path
																d="M10.6033 10.4092C9.70413 10.4083 8.97452 11.1365 8.97363 12.0357C8.97274 12.9348 9.70097 13.6644 10.6001 13.6653C11.4993 13.6662 12.2289 12.938 12.2298 12.0388C12.2298 12.0383 12.2298 12.0378 12.2298 12.0373C12.2289 11.1391 11.5014 10.4109 10.6033 10.4092Z"
																fill="white" />
															<path
																d="M13.4912 2.6132C13.4523 2.60565 13.4127 2.60182 13.373 2.60176H3.46022L3.30322 1.55144C3.20541 0.853911 2.60876 0.334931 1.90439 0.334717H0.627988C0.281154 0.334717 0 0.61587 0 0.962705C0 1.30954 0.281154 1.59069 0.627988 1.59069H1.90595C1.9858 1.59011 2.05338 1.64957 2.06295 1.72886L3.03004 8.35727C3.16263 9.19953 3.88712 9.8209 4.73975 9.82363H11.2724C12.0933 9.8247 12.8015 9.24777 12.9664 8.44362L13.9884 3.34906C14.0543 3.00854 13.8317 2.67909 13.4912 2.6132Z"
																fill="white" />
															<path
																d="M6.61539 11.9676C6.57716 11.0948 5.85687 10.4077 4.98324 10.4108C4.08483 10.4471 3.38595 11.2048 3.42225 12.1032C3.45708 12.9653 4.15833 13.6505 5.02092 13.6653H5.06017C5.95846 13.626 6.65474 12.8658 6.61539 11.9676Z"
																fill="white" />
															<clipPath id="clip_502_3906">
																<rect width="14" height="14" fill="white" />
															</clipPath>
														</svg>
													</div>
												</div>
											</div>
											<div class="dz-content">
												<h5 class="title"><a href="shop-list.html">Home fitness equipment</a>
												</h5>
												<ul class="star-rating">
													<li>
														<svg width="14" height="14" viewBox="0 0 14 14" fill="none"
															xmlns="http://www.w3.org/2000/svg">
															<path
																d="M7.24805 0.734375L9.22301 5.01608L13.9054 5.57126L10.4436 8.77267L11.3625 13.3975L7.24805 11.0944L3.13355 13.3975L4.0525 8.77267L0.590651 5.57126L5.27309 5.01608L7.24805 0.734375Z"
																fill="#FF8A00" />
														</svg>
													</li>
													<li>
														<svg width="14" height="14" viewBox="0 0 14 14" fill="none"
															xmlns="http://www.w3.org/2000/svg">
															<path
																d="M7.24805 0.734375L9.22301 5.01608L13.9054 5.57126L10.4436 8.77267L11.3625 13.3975L7.24805 11.0944L3.13355 13.3975L4.0525 8.77267L0.590651 5.57126L5.27309 5.01608L7.24805 0.734375Z"
																fill="#FF8A00" />
														</svg>
													</li>
													<li>
														<svg width="14" height="14" viewBox="0 0 14 14" fill="none"
															xmlns="http://www.w3.org/2000/svg">
															<path
																d="M7.24805 0.734375L9.22301 5.01608L13.9054 5.57126L10.4436 8.77267L11.3625 13.3975L7.24805 11.0944L3.13355 13.3975L4.0525 8.77267L0.590651 5.57126L5.27309 5.01608L7.24805 0.734375Z"
																fill="#FF8A00" />
														</svg>
													</li>
													<li>
														<svg width="14" height="14" viewBox="0 0 14 14" fill="none"
															xmlns="http://www.w3.org/2000/svg">
															<path
																d="M7.24805 0.734375L9.22301 5.01608L13.9054 5.57126L10.4436 8.77267L11.3625 13.3975L7.24805 11.0944L3.13355 13.3975L4.0525 8.77267L0.590651 5.57126L5.27309 5.01608L7.24805 0.734375Z"
																fill="#E4E5E8" />
														</svg>
													</li>
													<li>
														<svg width="14" height="14" viewBox="0 0 14 14" fill="none"
															xmlns="http://www.w3.org/2000/svg">
															<path
																d="M7.24805 0.734375L9.22301 5.01608L13.9054 5.57126L10.4436 8.77267L11.3625 13.3975L7.24805 11.0944L3.13355 13.3975L4.0525 8.77267L0.590651 5.57126L5.27309 5.01608L7.24805 0.734375Z"
																fill="#E4E5E8" />
														</svg>
													</li>
												</ul>
												<h6 class="price">
													<del>$45.00</del>
													$40.00
												</h6>
											</div>
											<div class="product-tag">
												<span class="badge badge-secondary">Sale</span>
												<span class="badge badge-primary">Featured</span>
											</div>
										</div>
									</li>
									<li class="card-container col-6 col-xl-4 col-lg-4 col-md-4 col-sm-6 Bottle">
										<div class="shop-card">
											<div class="dz-media">
												<img src="images/shop/product/14.png" alt="image">
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
															stroke-width="2" stroke-linecap="round"
															stroke-linejoin="round">
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
														<svg class="dz-cart-out" width="14" height="14"
															viewBox="0 0 14 14" fill="none"
															xmlns="http://www.w3.org/2000/svg">
															<path
																d="M10.6033 10.4092C9.70413 10.4083 8.97452 11.1365 8.97363 12.0357C8.97274 12.9348 9.70097 13.6644 10.6001 13.6653C11.4993 13.6662 12.2289 12.938 12.2298 12.0388C12.2298 12.0383 12.2298 12.0378 12.2298 12.0373C12.2289 11.1391 11.5014 10.4109 10.6033 10.4092Z"
																fill="white" />
															<path
																d="M13.4912 2.6132C13.4523 2.60565 13.4127 2.60182 13.373 2.60176H3.46022L3.30322 1.55144C3.20541 0.853911 2.60876 0.334931 1.90439 0.334717H0.627988C0.281154 0.334717 0 0.61587 0 0.962705C0 1.30954 0.281154 1.59069 0.627988 1.59069H1.90595C1.9858 1.59011 2.05338 1.64957 2.06295 1.72886L3.03004 8.35727C3.16263 9.19953 3.88712 9.8209 4.73975 9.82363H11.2724C12.0933 9.8247 12.8015 9.24777 12.9664 8.44362L13.9884 3.34906C14.0543 3.00854 13.8317 2.67909 13.4912 2.6132Z"
																fill="white" />
															<path
																d="M6.61539 11.9676C6.57716 11.0948 5.85687 10.4077 4.98324 10.4108C4.08483 10.4471 3.38595 11.2048 3.42225 12.1032C3.45708 12.9653 4.15833 13.6505 5.02092 13.6653H5.06017C5.95846 13.626 6.65474 12.8658 6.61539 11.9676Z"
																fill="white" />
															<clipPath id="clip0_502_6">
																<rect width="14" height="14" fill="white" />
															</clipPath>
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
																fill="#FF8A00" />
														</svg>
													</li>
													<li>
														<svg width="14" height="14" viewBox="0 0 14 14" fill="none"
															xmlns="http://www.w3.org/2000/svg">
															<path
																d="M7.24805 0.734375L9.22301 5.01608L13.9054 5.57126L10.4436 8.77267L11.3625 13.3975L7.24805 11.0944L3.13355 13.3975L4.0525 8.77267L0.590651 5.57126L5.27309 5.01608L7.24805 0.734375Z"
																fill="#FF8A00" />
														</svg>
													</li>
													<li>
														<svg width="14" height="14" viewBox="0 0 14 14" fill="none"
															xmlns="http://www.w3.org/2000/svg">
															<path
																d="M7.24805 0.734375L9.22301 5.01608L13.9054 5.57126L10.4436 8.77267L11.3625 13.3975L7.24805 11.0944L3.13355 13.3975L4.0525 8.77267L0.590651 5.57126L5.27309 5.01608L7.24805 0.734375Z"
																fill="#FF8A00" />
														</svg>
													</li>
													<li>
														<svg width="14" height="14" viewBox="0 0 14 14" fill="none"
															xmlns="http://www.w3.org/2000/svg">
															<path
																d="M7.24805 0.734375L9.22301 5.01608L13.9054 5.57126L10.4436 8.77267L11.3625 13.3975L7.24805 11.0944L3.13355 13.3975L4.0525 8.77267L0.590651 5.57126L5.27309 5.01608L7.24805 0.734375Z"
																fill="#E4E5E8" />
														</svg>
													</li>
													<li>
														<svg width="14" height="14" viewBox="0 0 14 14" fill="none"
															xmlns="http://www.w3.org/2000/svg">
															<path
																d="M7.24805 0.734375L9.22301 5.01608L13.9054 5.57126L10.4436 8.77267L11.3625 13.3975L7.24805 11.0944L3.13355 13.3975L4.0525 8.77267L0.590651 5.57126L5.27309 5.01608L7.24805 0.734375Z"
																fill="#E4E5E8" />
														</svg>
													</li>
												</ul>
												<h6 class="price">
													<del>$49.00</del>
													$20.00
												</h6>
											</div>
											<div class="product-tag">
												<span class="badge badge-secondary">-10%</span>
											</div>
										</div>
									</li>
									<li class="card-container col-6 col-xl-4 col-lg-4 col-md-4 col-sm-6 Begs">
										<div class="shop-card">
											<div class="dz-media">
												<img src="images/shop/product/15.png" alt="image">
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
															stroke-width="2" stroke-linecap="round"
															stroke-linejoin="round">
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
														<svg class="dz-cart-out" width="14" height="14"
															viewBox="0 0 14 14" fill="none"
															xmlns="http://www.w3.org/2000/svg">
															<path
																d="M10.6033 10.4092C9.70413 10.4083 8.97452 11.1365 8.97363 12.0357C8.97274 12.9348 9.70097 13.6644 10.6001 13.6653C11.4993 13.6662 12.2289 12.938 12.2298 12.0388C12.2298 12.0383 12.2298 12.0378 12.2298 12.0373C12.2289 11.1391 11.5014 10.4109 10.6033 10.4092Z"
																fill="white" />
															<path
																d="M13.4912 2.6132C13.4523 2.60565 13.4127 2.60182 13.373 2.60176H3.46022L3.30322 1.55144C3.20541 0.853911 2.60876 0.334931 1.90439 0.334717H0.627988C0.281154 0.334717 0 0.61587 0 0.962705C0 1.30954 0.281154 1.59069 0.627988 1.59069H1.90595C1.9858 1.59011 2.05338 1.64957 2.06295 1.72886L3.03004 8.35727C3.16263 9.19953 3.88712 9.8209 4.73975 9.82363H11.2724C12.0933 9.8247 12.8015 9.24777 12.9664 8.44362L13.9884 3.34906C14.0543 3.00854 13.8317 2.67909 13.4912 2.6132Z"
																fill="white" />
															<path
																d="M6.61539 11.9676C6.57716 11.0948 5.85687 10.4077 4.98324 10.4108C4.08483 10.4471 3.38595 11.2048 3.42225 12.1032C3.45708 12.9653 4.15833 13.6505 5.02092 13.6653H5.06017C5.95846 13.626 6.65474 12.8658 6.61539 11.9676Z"
																fill="white" />
															<clipPath id="clip0_5012_3906">
																<rect width="14" height="14" fill="white" />
															</clipPath>
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
																fill="#FF8A00" />
														</svg>
													</li>
													<li>
														<svg width="14" height="14" viewBox="0 0 14 14" fill="none"
															xmlns="http://www.w3.org/2000/svg">
															<path
																d="M7.24805 0.734375L9.22301 5.01608L13.9054 5.57126L10.4436 8.77267L11.3625 13.3975L7.24805 11.0944L3.13355 13.3975L4.0525 8.77267L0.590651 5.57126L5.27309 5.01608L7.24805 0.734375Z"
																fill="#FF8A00" />
														</svg>
													</li>
													<li>
														<svg width="14" height="14" viewBox="0 0 14 14" fill="none"
															xmlns="http://www.w3.org/2000/svg">
															<path
																d="M7.24805 0.734375L9.22301 5.01608L13.9054 5.57126L10.4436 8.77267L11.3625 13.3975L7.24805 11.0944L3.13355 13.3975L4.0525 8.77267L0.590651 5.57126L5.27309 5.01608L7.24805 0.734375Z"
																fill="#FF8A00" />
														</svg>
													</li>
													<li>
														<svg width="14" height="14" viewBox="0 0 14 14" fill="none"
															xmlns="http://www.w3.org/2000/svg">
															<path
																d="M7.24805 0.734375L9.22301 5.01608L13.9054 5.57126L10.4436 8.77267L11.3625 13.3975L7.24805 11.0944L3.13355 13.3975L4.0525 8.77267L0.590651 5.57126L5.27309 5.01608L7.24805 0.734375Z"
																fill="#E4E5E8" />
														</svg>
													</li>
													<li>
														<svg width="14" height="14" viewBox="0 0 14 14" fill="none"
															xmlns="http://www.w3.org/2000/svg">
															<path
																d="M7.24805 0.734375L9.22301 5.01608L13.9054 5.57126L10.4436 8.77267L11.3625 13.3975L7.24805 11.0944L3.13355 13.3975L4.0525 8.77267L0.590651 5.57126L5.27309 5.01608L7.24805 0.734375Z"
																fill="#E4E5E8" />
														</svg>
													</li>
												</ul>
												<h6 class="price">
													<del>$65.00</del>
													$30.00
												</h6>
											</div>
											<div class="product-tag">
												<span class="badge badge-secondary">-50%</span>
											</div>
										</div>
									</li>
									<li class="card-container col-6 col-xl-4 col-lg-4 col-md-4 col-sm-6 Bottle">
										<div class="shop-card">
											<div class="dz-media">
												<img src="images/shop/product/7.png" alt="image">
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
															stroke-width="2" stroke-linecap="round"
															stroke-linejoin="round">
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
														<svg class="dz-cart-out" width="14" height="14"
															viewBox="0 0 14 14" fill="none"
															xmlns="http://www.w3.org/2000/svg">
															<path
																d="M10.6033 10.4092C9.70413 10.4083 8.97452 11.1365 8.97363 12.0357C8.97274 12.9348 9.70097 13.6644 10.6001 13.6653C11.4993 13.6662 12.2289 12.938 12.2298 12.0388C12.2298 12.0383 12.2298 12.0378 12.2298 12.0373C12.2289 11.1391 11.5014 10.4109 10.6033 10.4092Z"
																fill="white" />
															<path
																d="M13.4912 2.6132C13.4523 2.60565 13.4127 2.60182 13.373 2.60176H3.46022L3.30322 1.55144C3.20541 0.853911 2.60876 0.334931 1.90439 0.334717H0.627988C0.281154 0.334717 0 0.61587 0 0.962705C0 1.30954 0.281154 1.59069 0.627988 1.59069H1.90595C1.9858 1.59011 2.05338 1.64957 2.06295 1.72886L3.03004 8.35727C3.16263 9.19953 3.88712 9.8209 4.73975 9.82363H11.2724C12.0933 9.8247 12.8015 9.24777 12.9664 8.44362L13.9884 3.34906C14.0543 3.00854 13.8317 2.67909 13.4912 2.6132Z"
																fill="white" />
															<path
																d="M6.61539 11.9676C6.57716 11.0948 5.85687 10.4077 4.98324 10.4108C4.08483 10.4471 3.38595 11.2048 3.42225 12.1032C3.45708 12.9653 4.15833 13.6505 5.02092 13.6653H5.06017C5.95846 13.626 6.65474 12.8658 6.61539 11.9676Z"
																fill="white" />
															<clipPath id="clip0_502_39062">
																<rect width="14" height="14" fill="white" />
															</clipPath>
														</svg>
													</div>
												</div>
											</div>
											<div class="dz-content">
												<h5 class="title"><a href="shop-list.html">Wooden Water Bottles</a></h5>
												<ul class="star-rating">
													<li>
														<svg width="14" height="14" viewBox="0 0 14 14" fill="none"
															xmlns="http://www.w3.org/2000/svg">
															<path
																d="M7.24805 0.734375L9.22301 5.01608L13.9054 5.57126L10.4436 8.77267L11.3625 13.3975L7.24805 11.0944L3.13355 13.3975L4.0525 8.77267L0.590651 5.57126L5.27309 5.01608L7.24805 0.734375Z"
																fill="#FF8A00" />
														</svg>
													</li>
													<li>
														<svg width="14" height="14" viewBox="0 0 14 14" fill="none"
															xmlns="http://www.w3.org/2000/svg">
															<path
																d="M7.24805 0.734375L9.22301 5.01608L13.9054 5.57126L10.4436 8.77267L11.3625 13.3975L7.24805 11.0944L3.13355 13.3975L4.0525 8.77267L0.590651 5.57126L5.27309 5.01608L7.24805 0.734375Z"
																fill="#FF8A00" />
														</svg>
													</li>
													<li>
														<svg width="14" height="14" viewBox="0 0 14 14" fill="none"
															xmlns="http://www.w3.org/2000/svg">
															<path
																d="M7.24805 0.734375L9.22301 5.01608L13.9054 5.57126L10.4436 8.77267L11.3625 13.3975L7.24805 11.0944L3.13355 13.3975L4.0525 8.77267L0.590651 5.57126L5.27309 5.01608L7.24805 0.734375Z"
																fill="#FF8A00" />
														</svg>
													</li>
													<li>
														<svg width="14" height="14" viewBox="0 0 14 14" fill="none"
															xmlns="http://www.w3.org/2000/svg">
															<path
																d="M7.24805 0.734375L9.22301 5.01608L13.9054 5.57126L10.4436 8.77267L11.3625 13.3975L7.24805 11.0944L3.13355 13.3975L4.0525 8.77267L0.590651 5.57126L5.27309 5.01608L7.24805 0.734375Z"
																fill="#E4E5E8" />
														</svg>
													</li>
													<li>
														<svg width="14" height="14" viewBox="0 0 14 14" fill="none"
															xmlns="http://www.w3.org/2000/svg">
															<path
																d="M7.24805 0.734375L9.22301 5.01608L13.9054 5.57126L10.4436 8.77267L11.3625 13.3975L7.24805 11.0944L3.13355 13.3975L4.0525 8.77267L0.590651 5.57126L5.27309 5.01608L7.24805 0.734375Z"
																fill="#E4E5E8" />
														</svg>
													</li>
												</ul>
												<h6 class="price">
													<del>$75.00</del>
													$30.00
												</h6>
											</div>
											<div class="product-tag">
												<span class="badge badge-secondary">-20%</span>
												<span class="badge badge-primary">Featured</span>
											</div>
										</div>
									</li>
									<li class="card-container col-6 col-xl-4 col-lg-4 col-md-4 col-sm-6 Toothbrushes">
										<div class="shop-card">
											<div class="dz-media">
												<img src="images/shop/product/16.png" alt="image">
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
															stroke-width="2" stroke-linecap="round"
															stroke-linejoin="round">
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
														<svg class="dz-cart-out" width="14" height="14"
															viewBox="0 0 14 14" fill="none"
															xmlns="http://www.w3.org/2000/svg">
															<path
																d="M10.6033 10.4092C9.70413 10.4083 8.97452 11.1365 8.97363 12.0357C8.97274 12.9348 9.70097 13.6644 10.6001 13.6653C11.4993 13.6662 12.2289 12.938 12.2298 12.0388C12.2298 12.0383 12.2298 12.0378 12.2298 12.0373C12.2289 11.1391 11.5014 10.4109 10.6033 10.4092Z"
																fill="white" />
															<path
																d="M13.4912 2.6132C13.4523 2.60565 13.4127 2.60182 13.373 2.60176H3.46022L3.30322 1.55144C3.20541 0.853911 2.60876 0.334931 1.90439 0.334717H0.627988C0.281154 0.334717 0 0.61587 0 0.962705C0 1.30954 0.281154 1.59069 0.627988 1.59069H1.90595C1.9858 1.59011 2.05338 1.64957 2.06295 1.72886L3.03004 8.35727C3.16263 9.19953 3.88712 9.8209 4.73975 9.82363H11.2724C12.0933 9.8247 12.8015 9.24777 12.9664 8.44362L13.9884 3.34906C14.0543 3.00854 13.8317 2.67909 13.4912 2.6132Z"
																fill="white" />
															<path
																d="M6.61539 11.9676C6.57716 11.0948 5.85687 10.4077 4.98324 10.4108C4.08483 10.4471 3.38595 11.2048 3.42225 12.1032C3.45708 12.9653 4.15833 13.6505 5.02092 13.6653H5.06017C5.95846 13.626 6.65474 12.8658 6.61539 11.9676Z"
																fill="white" />
															<clipPath id="clip01_502_3906">
																<rect width="14" height="14" fill="white" />
															</clipPath>
														</svg>
													</div>
												</div>
											</div>
											<div class="dz-content">
												<h5 class="title"><a href="shop-wishlist.html">Fitness trackers</a></h5>
												<ul class="star-rating">
													<li>
														<svg width="14" height="14" viewBox="0 0 14 14" fill="none"
															xmlns="http://www.w3.org/2000/svg">
															<path
																d="M7.24805 0.734375L9.22301 5.01608L13.9054 5.57126L10.4436 8.77267L11.3625 13.3975L7.24805 11.0944L3.13355 13.3975L4.0525 8.77267L0.590651 5.57126L5.27309 5.01608L7.24805 0.734375Z"
																fill="#FF8A00" />
														</svg>
													</li>
													<li>
														<svg width="14" height="14" viewBox="0 0 14 14" fill="none"
															xmlns="http://www.w3.org/2000/svg">
															<path
																d="M7.24805 0.734375L9.22301 5.01608L13.9054 5.57126L10.4436 8.77267L11.3625 13.3975L7.24805 11.0944L3.13355 13.3975L4.0525 8.77267L0.590651 5.57126L5.27309 5.01608L7.24805 0.734375Z"
																fill="#FF8A00" />
														</svg>
													</li>
													<li>
														<svg width="14" height="14" viewBox="0 0 14 14" fill="none"
															xmlns="http://www.w3.org/2000/svg">
															<path
																d="M7.24805 0.734375L9.22301 5.01608L13.9054 5.57126L10.4436 8.77267L11.3625 13.3975L7.24805 11.0944L3.13355 13.3975L4.0525 8.77267L0.590651 5.57126L5.27309 5.01608L7.24805 0.734375Z"
																fill="#FF8A00" />
														</svg>
													</li>
													<li>
														<svg width="14" height="14" viewBox="0 0 14 14" fill="none"
															xmlns="http://www.w3.org/2000/svg">
															<path
																d="M7.24805 0.734375L9.22301 5.01608L13.9054 5.57126L10.4436 8.77267L11.3625 13.3975L7.24805 11.0944L3.13355 13.3975L4.0525 8.77267L0.590651 5.57126L5.27309 5.01608L7.24805 0.734375Z"
																fill="#E4E5E8" />
														</svg>
													</li>
													<li>
														<svg width="14" height="14" viewBox="0 0 14 14" fill="none"
															xmlns="http://www.w3.org/2000/svg">
															<path
																d="M7.24805 0.734375L9.22301 5.01608L13.9054 5.57126L10.4436 8.77267L11.3625 13.3975L7.24805 11.0944L3.13355 13.3975L4.0525 8.77267L0.590651 5.57126L5.27309 5.01608L7.24805 0.734375Z"
																fill="#E4E5E8" />
														</svg>
													</li>
												</ul>
												<h6 class="price">
													<del>$45.00</del>
													$40.00
												</h6>
											</div>
											<div class="product-tag">
												<span class="badge badge-secondary">Sale</span>
											</div>
										</div>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</section>
			<!--Recommend Section End-->

			<!-- Newsletter -->
			<section class="overlay-black-light newsletter-wrapper style-2"
				style="background-image: url('images/background/bg4.jpg'); background-repeat: no-repeat; background-size: cover;">
				<div class="container">
					<div class="subscride-inner wow fadeInUp" data-wow-delay="0.2s">
						<div class="row justify-content-center text-center">
							<div class="col-xl-12 col-lg-12">
								<div class="section-head">
									<h3 class="title">SUBSCRIBE TO OUR NEWSLETTER</h3>
									<p class="sub-title">Get latest news, offers and discounts.</p>
								</div>
								<form class="dzSubscribe" action="script/mailchamp.php" method="post">
									<div class="dzSubscribeMsg"></div>
									<div class="form-group">
										<div class="input-group mb-0">
											<input name="dzEmail" required="required" type="email" class="form-control"
												placeholder="Your Email Address">
											<div class="input-group-addon">
												<button name="submit" value="Submit" type="submit" class="btn">
													<svg width="21" height="21" viewBox="0 0 21 21" fill="none">
														<path d="M4.20972 10.7344H15.8717" stroke="#0D775E"
															stroke-width="2" stroke-linecap="round"
															stroke-linejoin="round" />
														<path d="M10.0408 4.90112L15.8718 10.7345L10.0408 16.5678"
															stroke="#0D775E" stroke-width="2" stroke-linecap="round"
															stroke-linejoin="round" />
													</svg>
												</button>
											</div>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</section>
			<!-- Newsletter End -->

			<!-- Blog Start -->
			<section class="content-inner overlay-white-middle">
				<div class="container">
					<div class="section-head style-2 wow fadeInUp" data-wow-delay="0.1s">
						<div class="left-content">
							<h2 class="title">Blog Post</h2>
							<p>Discover the most trending post.</p>
						</div>
						<a href="post-standard.html"
							class="text-secondary font-14 d-flex align-items-center gap-1 text-nowrap">See all Post
							<i class="icon feather icon-chevron-right font-18"></i>
						</a>
					</div>
					<div class="swiper swiper-blog-post">
						<div class="swiper-wrapper">
							<div class="swiper-slide">
								<div class="dz-card style-2 wow fadeInUp" data-wow-delay="0.2s">
									<div class="dz-media">
										<img src="images/blog/grid/1.jpg" alt="">
										<div class="post-date">17 May 2023</div>
									</div>
									<div class="dz-info">
										<div class="dz-meta">
											<ul>
												<li><a href="javascript:void(0);">lifestyle</a></li>
												<li><a href="javascript:void(0);">Wooden</a></li>
											</ul>
										</div>
										<h4 class="dz-title mb-0">
											<a href="blog-light-3-column.html">Uncovering the Beauty Benefits of
												Plant-Based Skincare</a>
										</h4>
									</div>
								</div>
							</div>
							<div class="swiper-slide">
								<div class="dz-card style-2 wow fadeInUp" data-wow-delay="0.3s">
									<div class="dz-media">
										<img src="images/blog/grid/1.jpg" alt="">
										<div class="post-date">20 Apr 2023</div>
									</div>
									<div class="dz-info">
										<div class="dz-meta">
											<ul>
												<li><a href="javascript:void(0);">lifestyle</a></li>
												<li><a href="javascript:void(0);">Wooden</a></li>
											</ul>
										</div>
										<h4 class="dz-title mb-0">
											<a href="blog-light-3-column.html">Start Your Day With Yoga And Refreshing
												Air</a>
										</h4>
									</div>
								</div>
							</div>
							<div class="swiper-slide">
								<div class="dz-card style-2 wow fadeInUp" data-wow-delay="0.4s">
									<div class="dz-media">
										<img src="images/blog/grid/3.jpg" alt="">
										<div class="post-date">15 Dec 2023</div>
									</div>
									<div class="dz-info">
										<div class="dz-meta">
											<ul>
												<li><a href="javascript:void(0);">lifestyle</a></li>
												<li><a href="javascript:void(0);">Wooden</a></li>
											</ul>
										</div>

										<h4 class="dz-title mb-0">
											<a href="blog-light-3-column.html">Explore Something New With Yoga in Your
												Daily LifeStyle</a>
										</h4>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
			<!-- Blog End -->

			<!-- Feature Box -->
			<div class="content-inner py-0 overlay-white-middle">
				<div class="container-fluid px-0">
					<div class="row gx-0">
						<div class="col-xl-2 col-lg-4 col-md-4 col-sm-4 col-4 wow fadeIn" data-wow-delay="0.1s">
							<div class="insta-post dz-media dz-img-effect rotate">
								<a href="javascript:void(0);">
									<img src="images/gym/feature/1.png" alt="">
								</a>
							</div>
						</div>
						<div class="col-xl-2 col-lg-4 col-md-4 col-sm-4 col-4 wow fadeIn" data-wow-delay="0.2s">
							<div class="insta-post dz-media dz-img-effect rotate">
								<a href="javascript:void(0);">
									<img src="images/gym/feature/2.png" alt="">
								</a>
							</div>
						</div>
						<div class="col-xl-2 col-lg-4 col-md-4 col-sm-4 col-4 wow fadeIn" data-wow-delay="0.3s">
							<div class="insta-post dz-media dz-img-effect rotate">
								<a href="javascript:void(0);">
									<img src="images/gym/feature/3.png" alt="">
								</a>
							</div>
						</div>
						<div class="col-xl-2 col-lg-4 col-md-4 col-sm-4 col-4 wow fadeIn" data-wow-delay="0.4s">
							<div class="insta-post dz-media dz-img-effect rotate">
								<a href="javascript:void(0);">
									<img src="images/gym/feature/4.png" alt="">
								</a>
							</div>
						</div>
						<div class="col-xl-2 col-lg-4 col-md-4 col-sm-4 col-4 wow fadeIn" data-wow-delay="0.5s">
							<div class="insta-post dz-media dz-img-effect rotate">
								<a href="javascript:void(0);">
									<img src="images/gym/feature/5.png" alt="">
								</a>
							</div>
						</div>
						<div class="col-xl-2 col-lg-4 col-md-4 col-sm-4 col-4 wow fadeIn" data-wow-delay="0.6s">
							<div class="insta-post dz-media dz-img-effect rotate">
								<a href="javascript:void(0);">
									<img src="images/gym/feature/6.png" alt="">
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- Feature Box End -->

			<!-- Icon Box Start -->
			<section class="content-inner py-0">
				<div class="container-fluid px-0">
					<div class="row gx-0">
						<div class="col-xl-3 col-lg-3 col-sm-6">
							<div class="icon-bx-wraper style-2 bg-light wow fadeInUp" data-wow-delay="0.1s">
								<div class="icon-bx">
									<img src="images/svg/icon-bx/password-check.svg" alt="">
								</div>
								<div class="icon-content">
									<h5 class="dz-title">Filter & Discover</h5>
									<p>Lorem Ipsum is simply dummy text of the printing and typesetting</p>
								</div>
								<div class="data-text">01</div>
							</div>
						</div>
						<div class="col-xl-3 col-lg-3 col-sm-6">
							<div class="icon-bx-wraper style-2 wow fadeInUp" data-wow-delay="0.2s">
								<div class="icon-bx">
									<img src="images/svg/icon-bx/cart.svg" alt="">
								</div>
								<div class="icon-content">
									<h5 class="dz-title">Add to cart</h5>
									<p>Lorem Ipsum is simply dummy text of the printing and typesetting</p>
								</div>
								<div class="data-text">02</div>
							</div>
						</div>
						<div class="col-xl-3 col-lg-3 col-sm-6">
							<div class="icon-bx-wraper style-2 bg-light wow fadeInUp" data-wow-delay="0.3s">
								<div class="icon-bx">
									<img src="images/svg/icon-bx/discovery.svg" alt="">
								</div>
								<div class="icon-content">
									<h5 class="dz-title">Fast Shipping</h5>
									<p>Lorem Ipsum is simply dummy text of the printing and typesetting</p>
								</div>
								<div class="data-text">03</div>
							</div>
						</div>
						<div class="col-xl-3 col-lg-3 col-sm-6">
							<div class="icon-bx-wraper style-2 wow fadeInUp" data-wow-delay="0.4s">
								<div class="icon-bx">
									<img src="images/svg/icon-bx/box-tick.svg" alt="">
								</div>
								<div class="icon-content">
									<h5 class="dz-title">Enjoy The Product</h5>
									<p>Lorem Ipsum is simply dummy text of the printing and typesetting</p>
								</div>
								<div class="data-text">04</div>
							</div>
						</div>
					</div>
				</div>
			</section>
			<!-- Icon Box End -->
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
</body>

</html>

<?php
mysqli_close($conn);
?>