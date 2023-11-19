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

		<div class="page-content">
			<!--banner-->
			<div class="dz-bnr-inr style-1" style="background-image:url(images/background/bg-shape.jpg);">
				<div class="container">
					<div class="dz-bnr-inr-entry">
						<h1>Shop Kumpe3D</h1>
						<nav aria-label="breadcrumb" class="breadcrumb-row">
							<ul class="breadcrumb">
								<li class="breadcrumb-item"><a href="index.html"> Home</a></li>
							</ul>
						</nav>
					</div>
				</div>
			</div>

			<section class="content-inner pt-3 z-index-unset">
				<div class="container-fluid">
					<div class="row ">
						<div class="col-xl-12">
							<div class="filter-wrapper">
								<div class="filter-left-area">
									<!-- <ul class="filter-tag">
									<li>
										<a href="javascript:void(0);" class="tag-btn">Bottle 
											<i class="icon feather icon-x tag-close"></i>
										</a>
									</li>
									<li>
										<a href="javascript:void(0);" class="tag-btn">Wooden CUP
											<i class="icon feather icon-x tag-close"></i>
										</a>
									</li>
									<li>
										<a href="javascript:void(0);" class="tag-btn">Begs 
											<i class="icon feather icon-x tag-close"></i>
										</a>
									</li>
								</ul> -->
									<span>Showing <span id="resultsCountTop"></span> Results</span>
								</div>
								<div class="filter-right-area">
									<!-- <div class="form-group">
										<a href="javascript:void(0);" class="filter-top-btn" id="filterTopBtn">
											<svg class="me-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 25 25"
												width="20" height="20">
												<g id="Layer_29" data-name="Layer 29">
													<path
														d="M2.54,5H15v.5A1.5,1.5,0,0,0,16.5,7h2A1.5,1.5,0,0,0,20,5.5V5h2.33a.5.5,0,0,0,0-1H20V3.5A1.5,1.5,0,0,0,18.5,2h-2A1.5,1.5,0,0,0,15,3.5V4H2.54a.5.5,0,0,0,0,1ZM16,3.5a.5.5,0,0,1,.5-.5h2a.5.5,0,0,1,.5.5v2a.5.5,0,0,1-.5.5h-2a.5.5,0,0,1-.5-.5Z">
													</path>
													<path
														d="M22.4,20H18v-.5A1.5,1.5,0,0,0,16.5,18h-2A1.5,1.5,0,0,0,13,19.5V20H2.55a.5.5,0,0,0,0,1H13v.5A1.5,1.5,0,0,0,14.5,23h2A1.5,1.5,0,0,0,18,21.5V21h4.4a.5.5,0,0,0,0-1ZM17,21.5a.5.5,0,0,1-.5.5h-2a.5.5,0,0,1-.5-.5v-2a.5.5,0,0,1,.5-.5h2a.5.5,0,0,1,.5.5Z">
													</path>
													<path
														d="M8.5,15h2A1.5,1.5,0,0,0,12,13.5V13H22.45a.5.5,0,1,0,0-1H12v-.5A1.5,1.5,0,0,0,10.5,10h-2A1.5,1.5,0,0,0,7,11.5V12H2.6a.5.5,0,1,0,0,1H7v.5A1.5,1.5,0,0,0,8.5,15ZM8,11.5a.5.5,0,0,1,.5-.5h2a.5.5,0,0,1,.5.5v2a.5.5,0,0,1-.5.5h-2a.5.5,0,0,1-.5-.5Z">
													</path>
												</g>
											</svg>
											Filter
										</a>
									</div> -->
									<!-- <div class="form-group">
										<select class="default-select">
											<option>Default sorting</option>
											<option>1 Day</option>
											<option>1 Week</option>
											<option>3 Weeks</option>
											<option>1 Month</option>
											<option>3 Months</option>
										</select>
									</div> -->
									<div class="form-group Category">
										<select id='categorySelect' class="default-select">
											<option value='%'>All Categories</option>
										</select>
									</div>
									<div class="shop-tab">
										<ul class="nav" role="tablist" id="dz-shop-tab">
											<!-- <li class="nav-item" role="presentation">
											<a href="#tab-list-list" class="nav-link active" id="tab-list-list-btn" data-bs-toggle="pill" data-bs-target="#tab-list-list" role="tab" aria-controls="tab-list-list" aria-selected="true">
												<i class="flaticon flaticon-list"></i>
											</a>
										</li> -->
											<li class="nav-item" role="presentation">
												<a href="#tab-list-column" class="nav-link" id="tab-list-column-btn"
													data-bs-toggle="pill" data-bs-target="#tab-list-column" role="tab"
													aria-controls="tab-list-column" aria-selected="false">
													<i class="flaticon flaticon-blocks"></i>
												</a>
											</li>
											<li class="nav-item" role="presentation">
												<a href="#tab-list-grid" class="nav-link" id="tab-list-grid-btn"
													data-bs-toggle="pill" data-bs-target="#tab-list-grid" role="tab"
													aria-controls="tab-list-grid" aria-selected="false">
													<i class="flaticon flaticon-menu"></i>
												</a>
											</li>
											<!-- <li class="nav-item" role="presentation">
											<a href="#tab-list-collage" class="nav-link" id="tab-list-collage-btn" data-bs-toggle="pill" data-bs-target="#tab-list-collage" role="tab" aria-controls="tab-list-collage" aria-selected="false">
												<i class="flaticon flaticon-sections"></i>
											</a>
										</li> -->
										</ul>
									</div>
								</div>
							</div>
							<!-- <div class="col-xl-12 shop-top-filter">
							<a href="javascript:void(0);" class="panel-close-btn">																	
								<svg width="35" height="35" viewBox="0 0 51 50" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M37.748 12.5L12.748 37.5" stroke="white" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
									<path d="M12.748 12.5L37.748 37.5" stroke="white" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
							</a>
							<div class="shop-filter mt-xl-2 mt-0 " id="shopFilter">
								<aside>
									<div class="d-flex d-xl-none align-items-center justify-content-between m-b30">
										<h6 class="title mb-0 fw-normal">
											<svg class="me-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 25 25" width="20" height="20"><g id="Layer_28" data-name="Layer 28"><path d="M2.54,5H15v.5A1.5,1.5,0,0,0,16.5,7h2A1.5,1.5,0,0,0,20,5.5V5h2.33a.5.5,0,0,0,0-1H20V3.5A1.5,1.5,0,0,0,18.5,2h-2A1.5,1.5,0,0,0,15,3.5V4H2.54a.5.5,0,0,0,0,1ZM16,3.5a.5.5,0,0,1,.5-.5h2a.5.5,0,0,1,.5.5v2a.5.5,0,0,1-.5.5h-2a.5.5,0,0,1-.5-.5Z"></path><path d="M22.4,20H18v-.5A1.5,1.5,0,0,0,16.5,18h-2A1.5,1.5,0,0,0,13,19.5V20H2.55a.5.5,0,0,0,0,1H13v.5A1.5,1.5,0,0,0,14.5,23h2A1.5,1.5,0,0,0,18,21.5V21h4.4a.5.5,0,0,0,0-1ZM17,21.5a.5.5,0,0,1-.5.5h-2a.5.5,0,0,1-.5-.5v-2a.5.5,0,0,1,.5-.5h2a.5.5,0,0,1,.5.5Z"></path><path d="M8.5,15h2A1.5,1.5,0,0,0,12,13.5V13H22.45a.5.5,0,1,0,0-1H12v-.5A1.5,1.5,0,0,0,10.5,10h-2A1.5,1.5,0,0,0,7,11.5V12H2.6a.5.5,0,1,0,0,1H7v.5A1.5,1.5,0,0,0,8.5,15ZM8,11.5a.5.5,0,0,1,.5-.5h2a.5.5,0,0,1,.5.5v2a.5.5,0,0,1-.5.5h-2a.5.5,0,0,1-.5-.5Z"></path></g></svg>
											Filter
										</h6>
									</div>
									<div class="widget widget_search">
										<div class="form-group">
											<div class="input-group">
												<input name="dzSearch" required="required" type="search" class="form-control" placeholder="Search Product">
												<div class="input-group-addon">
													<button name="submit" value="Submit" type="submit" class="btn">
														<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
															<path d="M9.16667 15.8333C12.8486 15.8333 15.8333 12.8486 15.8333 9.16667C15.8333 5.48477 12.8486 2.5 9.16667 2.5C5.48477 2.5 2.5 5.48477 2.5 9.16667C2.5 12.8486 5.48477 15.8333 9.16667 15.8333Z" stroke="#0D775E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
															<path d="M17.5 17.5L13.875 13.875" stroke="#0D775E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
														</svg>
													</button>
												</div>
											</div>
										</div>
									</div>
									<div class="widget">
										<h6 class="widget-title">Price</h6>
										<div class="price-slide range-slider">
											<div class="price">
												<div class="range-slider style-1">
													<div id="slider-tooltips" class="mb-3"></div>
													<span class="example-val" id="slider-margin-value-min"></span>
													<span class="example-val" id="slider-margin-value-max"></span>
												</div>
											</div>
										</div>
									</div>
									<div class="widget">
										<h6 class="widget-title">Color</h6>
										<div class="d-flex align-items-center flex-wrap color-filter ps-2">
											<div class="form-check">
												<input class="form-check-input" type="radio" name="radioNoLabel" id="radioNoLabel1" value="#24262B" aria-label="..." checked>
												<span></span>
											</div>
											<div class="form-check">
												<input class="form-check-input" type="radio" name="radioNoLabel" id="radioNoLabel2" value="#8CB2D1" aria-label="...">
												<span></span>
											</div>
											<div class="form-check">
												<input class="form-check-input" type="radio" name="radioNoLabel" id="radioNoLabel3" value="#0D775E" aria-label="...">
												<span></span>
											</div>
											<div class="form-check">
												<input class="form-check-input" type="radio" name="radioNoLabel" id="radioNoLabel4" value="#D7D7D7" aria-label="...">
												<span></span>
											</div>
											<div class="form-check">
												<input class="form-check-input" type="radio" name="radioNoLabel" id="radioNoLabel5" value="#D1998C" aria-label="...">
												<span></span>
											</div>
											<div class="form-check">
												<input class="form-check-input" type="radio" name="radioNoLabel" id="radioNoLabel6" value="#84BBAE" aria-label="...">
												<span></span>
											</div>
											<div class="form-check">
												<input class="form-check-input" type="radio" name="radioNoLabel" id="radioNoLabel7" value="#9072AD" aria-label="...">
												<span></span>
											</div>
											<div class="form-check">
												<input class="form-check-input" type="radio" name="radioNoLabel" id="radioNoLabel8" value="#C895A1" aria-label="...">
												<span></span>
											</div>
											<div class="form-check">
												<input class="form-check-input" type="radio" name="radioNoLabel" id="radioNoLabel9" value="#8FA4EF" aria-label="...">
												<span></span>
											</div>
											<div class="form-check">
												<input class="form-check-input" type="radio" name="radioNoLabel" id="radioNoLabe20" value="#ADCFA7" aria-label="...">
												<span></span>
											</div>
										</div>
									</div>
									
									<div class="widget">
										<h6 class="widget-title">Size</h6>
										<div class="btn-group product-size">
											<input type="radio" class="btn-check" name="btnradio1" id="btnradio11" checked="">
											<label class="btn" for="btnradio11">4</label>

											<input type="radio" class="btn-check" name="btnradio1" id="btnradio21">
											<label class="btn" for="btnradio21">6</label>

											<input type="radio" class="btn-check" name="btnradio1" id="btnradio31">
											<label class="btn" for="btnradio31">8</label>
										  
											<input type="radio" class="btn-check" name="btnradio1" id="btnradio41">
											<label class="btn" for="btnradio41">10</label>
											
											<input type="radio" class="btn-check" name="btnradio1" id="btnradio51">
											<label class="btn" for="btnradio51">12</label>
											
											<input type="radio" class="btn-check" name="btnradio1" id="btnradio61">
											<label class="btn" for="btnradio61">14</label>
											
											<input type="radio" class="btn-check" name="btnradio1" id="btnradio71">
											<label class="btn" for="btnradio71">16</label>
											
											<input type="radio" class="btn-check" name="btnradio1" id="btnradio81">
											<label class="btn" for="btnradio81">18</label>
											
											<input type="radio" class="btn-check" name="btnradio1" id="btnradio91">
											<label class="btn" for="btnradio91">20</label>
										</div>
									</div>
									
									<div class="widget widget_categories">
										<h6 class="widget-title">Category</h6>
										<ul>
											<li class="cat-item cat-item-26"><a href="javascript:void(0);">Yoga Mats</a> (15)</li>
											<li class="cat-item cat-item-36"><a href="javascript:void(0);">Yoga Accessories</a> (22)</li>
											<li class="cat-item cat-item-43"><a href="javascript:void(0);">Reusable Bags</a> (10)</li>
											<li class="cat-item cat-item-27"><a href="javascript:void(0);">Water Bottles</a> (3)</li>
										</ul>
									</div>
									
									<div class="widget widget_tag_cloud">
										<h6 class="widget-title">Tags</h6>
										<div class="tagcloud"> 
											<a href="javascript:void(0);">Mats </a>
											<a href="javascript:void(0);">Accessories</a>
											<a href="javascript:void(0);">Bottles</a>
											<a href="javascript:void(0);">Bottles</a>
											<a href="javascript:void(0);">Trackers</a>
											<a href="javascript:void(0);">Bags</a>
											<a href="javascript:void(0);">Cup</a>
											<a href="javascript:void(0);">Toothbrushes</a>
										</div>
									</div>
									<a href="javascript:void(0);" class="btn btn-sm font-14 btn-primary btn-sharp reset-btn">RESET</a>
								</aside>
							</div>
						</div> -->

							<div class="row">
								<div class="col-12 tab-content shop-" id="pills-tabContent">
									<div class="tab-pane fade" id="tab-list-column" role="tabpanel"
										aria-labelledby="tab-list-column-btn">
										<div id="productsColumn" class="row gx-xl-4 g-3 mb-xl-0 mb-md-0 mb-3">
											<div
												class="col-6 col-xl-4 col-lg-6 col-md-6 col-sm-6 m-md-b15 m-sm-b0 m-b30">
												<!-- <div class="shop-card">
													<div class="dz-media">
														<img src="images/shop/product/1.png" alt="image">
													</div>
													<div class="dz-content">
														<h5 class="title"><a href="shop-list.html">Wooden Water
																Bottles</a></h5>
														<h6 class="price">
															<del>$45.00</del>
															$40.00
														</h6>
													</div>
													<div class="product-tag">
														<span class="badge badge-secondary">Sale</span>
													</div>
												</div> -->
											</div>
											<div
												class="col-6 col-xl-4 col-lg-6 col-md-6 col-sm-6 m-md-b15 m-sm-b0 m-b30">
												<!-- <div class="shop-card">
													<div class="dz-media">
														<img src="images/shop/product/2.png" alt="image">
													</div>
													<div class="dz-content">
														<h5 class="title"><a href="shop-list.html">Wooden Cup</a></h5>
														<h6 class="price">
															<del>$25.00</del>
															$10.00
														</h6>
													</div>
													<div class="product-tag">
														<span class="badge badge-secondary">-10%</span>
														<span class="badge badge-primary">Featured</span>
													</div>
												</div> -->
											</div>
										</div>
									</div>
									<div class="tab-pane fade active show" id="tab-list-grid" role="tabpanel"
										aria-labelledby="tab-list-grid-btn">
										<div id="productsGrid" class="row gx-xl-4 g-3 mb-xl-0 mb-md-0 mb-3">
											<div
												class="col-6 col-xl-3 col-lg-3 col-md-4 col-sm-6 m-md-b15 m-b30 grid-5">
												<!-- <div class="shop-card">
													<div class="dz-media">
														<img src="images/shop/product/1.png" alt="image">
													</div>
													<div class="dz-content">
														<h5 class="title"><a href="shop-list.html">Wooden Water
																Bottles</a></h5>
														<h6 class="price">
															<del>$40.00</del>
															$20.00
														</h6>
													</div>
													<div class="product-tag">
														<span class="badge badge-secondary">Sale</span>
													</div> -->
												</div>
											</div>
											<div
												class="col-6 col-xl-3 col-lg-3 col-md-4 col-sm-6 m-md-b15 m-b30 grid-5">
												<!-- <div class="shop-card">
													<div class="dz-media">
														<img src="images/shop/product/2.png" alt="image">
													</div>
													<div class="dz-content">
														<h5 class="title"><a href="shop-list.html">Wooden Cup</a></h5>
														<h6 class="price">
															<del>$52.00</del>
															$42.00
														</h6>
													</div>
													<div class="product-tag">
														<span class="badge badge-secondary">-12%</span>
														<span class="badge badge-primary">Featured</span>
													</div>
												</div> -->
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="row page mt-0">
								<div class="col-md-6">
									<p class="page-text">Showing <span id="resultsCountBottom"></span> Results</p>
								</div>
								<!-- <div class="col-md-6">
								<nav aria-label="Blog Pagination">
									<ul class="pagination style-1">
										<li class="page-item"><a class="page-link prev" href="javascript:void(0);">Prev</a></li>
										<li class="page-item"><a class="page-link active" href="javascript:void(0);">1</a></li>
										<li class="page-item"><a class="page-link" href="javascript:void(0);">2</a></li>
										<li class="page-item"><a class="page-link" href="javascript:void(0);">3</a></li>
										<li class="page-item"><a class="page-link next" href="javascript:void(0);">Next</a></li>
									</ul>
								</nav>
							</div> -->
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
	<script nonce="<?php echo $nonce; ?>" src="vendor-js/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
	<!-- BOOTSTRAP MIN JS -->
	<script nonce="<?php echo $nonce; ?>" src="vendor-js/bootstrap-select/dist/js/bootstrap-select.min.js"></script>
	<!-- BOOTSTRAP SELECT MIN JS -->
	<script nonce="<?php echo $nonce; ?>" src="vendor-js/bootstrap-touchspin/bootstrap-touchspin.js"></script>
	<!-- BOOTSTRAP TOUCHSPIN JS -->

	<script nonce="<?php echo $nonce; ?>" src="vendor-js/counter/waypoints-min.js"></script><!-- WAYPOINTS JS -->
	<script nonce="<?php echo $nonce; ?>" src="vendor-js/counter/counterup.min.js"></script><!-- COUNTERUP JS -->
	<script nonce="<?php echo $nonce; ?>" src="vendor-js/swiper/swiper-bundle.min.js"></script><!-- SWIPER JS -->
	<script nonce="<?php echo $nonce; ?>" src="vendor-js/imagesloaded/imagesloaded.js"></script><!-- IMAGESLOADED-->
	<script nonce="<?php echo $nonce; ?>" src="vendor-js/masonry/masonry-4.2.2.js"></script><!-- MASONRY -->
	<script nonce="<?php echo $nonce; ?>" src="vendor-js/masonry/isotope.pkgd.min.js"></script><!-- ISOTOPE -->
	<script nonce="<?php echo $nonce; ?>" src="vendor-js/countdown/jquery.countdown.js"></script>
	<!-- COUNTDOWN FUCTIONS  -->
	<script nonce="<?php echo $nonce; ?>" src="vendor-js/wnumb/wNumb.js"></script><!-- WNUMB -->
	<script nonce="<?php echo $nonce; ?>" src="vendor-js/nouislider/nouislider.min.js"></script><!-- NOUSLIDER MIN JS-->
	<script nonce="<?php echo $nonce; ?>" src="js/dz.carousel.js"></script><!-- DZ CAROUSEL JS -->
	<script nonce="<?php echo $nonce; ?>" src="vendor-js/lightgallery/dist/lightgallery.min.js"></script>
	<script nonce="<?php echo $nonce; ?>"
		src="vendor-js/lightgallery/dist/plugins/thumbnail/lg-thumbnail.min.js"></script>
	<script nonce="<?php echo $nonce; ?>" src="vendor-js/lightgallery/dist/plugins/zoom/lg-zoom.min.js"></script>
	<script nonce="<?php echo $nonce; ?>" src="js/dz.ajax.js"></script><!-- AJAX -->
	<script nonce="<?php echo $nonce; ?>" src="js/custom.js"></script><!-- CUSTOM JS -->
	<script nonce="<?php echo $nonce; ?>" src="js/shop.js"></script>

</body>

</html>

<?php
mysqli_close($conn);
?>