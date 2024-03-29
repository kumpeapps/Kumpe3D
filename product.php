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
$sku = [];
if (isset($_GET['sku'])) {
	$sku['sku'] = $_GET['sku'];
} else {
	if ($env == 'dev') {
		$sku['sku'] = 'ALO-POO-LSN-000';
	} else {
		http_response_code(404);
		include('./404.php');
		die();
	}
}
$sku_array = explode("-", $sku['sku']);
if (isset($sku_array[0]) && isset($sku_array[1]) && isset($sku_array[2])) {
	$sku['design'] = $sku_array[0];
	$sku['product'] = $sku_array[1];
	$sku['options'] = $sku_array[2];
} else {
	http_response_code(404);
	include('./ErrorPages/HTTP404.html');
	die();
}
if (isset($sku_array[3])) {
	$sku['color'] = $sku_array[3];
} else {
	$sku['color'] = '000';
}
$sku['main_cat'] = substr($sku['design'], 0, 1);
$sku['design_type'] = substr($sku['design'], 1, 1);
$sku['sub_cat'] = substr($sku['design'], 2, 1);
$sku['filament_type'] = substr($sku['options'], 0, 1);
$sku['layer_quality'] = substr($sku['options'], 1, 1);
$sku['size'] = substr($sku['options'], 2, 1);
$sku['base_sku'] = $sku['design'] . "-" . $sku['product'] . "-" . $sku['options'];
$base_sku = $sku['base_sku'];

if (mysqli_connect_errno()) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
	exit();
}

$sql = "CALL get_products('" . $_GET['sku'] . "', '%', '%', '%')";
$product = mysqli_query($conn, $sql);
$product = mysqli_fetch_array($product);

if (empty($product)) {
	http_response_code(404);
	include('./404.php');
	die();
}

if (mysqli_connect_errno()) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
	exit();
}

$filament_filter = $product['filament_filter'];
$photo_sql = "SELECT * FROM Web_3dprints.product_photos WHERE sku = '" . $_GET['sku'] . "';";
$filaments_sql = "CALL get_filament_options('$base_sku', '$filament_filter');";

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
	<meta name="keywords" content="<?php echo $product['tags']; ?>">
	<meta name="author" content="KumpeApps LLC">
	<meta name="robots" content="">
	<meta name="description" content="<?php echo $product['description']; ?>">
	<meta property="og:title" content="<?php echo $product['title']; ?>">
	<meta property="og:description" content="<?php echo $product['description']; ?>">
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
		<?php echo $product['title']; ?>
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
	<!-- SweetAlerts -->

	<!-- GOOGLE FONTS-->
	<link nonce="<?php echo $nonce; ?>" rel="preconnect" href="https://fonts.googleapis.com">
	<link nonce="<?php echo $nonce; ?>" rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link nonce="<?php echo $nonce; ?>"
		href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=Roboto:wght@100;300;400;500;700;900&display=swap"
		rel="stylesheet">

</head>

<body>
	<div class="page-wraper">
		<!-- <div id="loading-area" class="preloader-wrapper-1">
			<div>
				<span class="loader-2"></span>
				<img src="<?php echo $site_params['store_loading_image_url'] ?>" alt="/">
				<span class="loader"></span>
			</div>
		</div> -->
		<?php
		include('./includes/header.php');
		?>

		<div class="page-content">

			<div class="d-sm-flex justify-content-between container-fluid py-3">
				<nav aria-label="breadcrumb" class="breadcrumb-row">
					<ul class="breadcrumb mb-0">
						<li class="breadcrumb-item"><a href="/"> Home</a></li>
						<li class="breadcrumb-item"><a href="shop"> Products</a></li>
						<li id="titleCrumb" class="breadcrumb-item"></li>
					</ul>
				</nav>
			</div>

			<section class="content-inner py-0">
				<div class="container-fluid">
					<div class="row">
						<div class="col-xl-4 col-md-4">
							<div class="dz-product-detail sticky-top">
								<div id="productImageGallery" class="swiper-btn-center-lr">
									<div class="swiper product-gallery-swiper2">
										<!-- Tried building this in JS but can not get it to work. Need to circle back around to it. -->
										<div id="imageGallery" class="swiper-wrapper" id="lightgallery">
											<?php
											if ($photo_query = mysqli_query($conn, $photo_sql)) {
												// Loop through each row in the result set
												$photo_images = '';
												$photo_thumbnails = '';
												while ($photo = mysqli_fetch_array($photo_query)) {
													$photo_images = $photo_images .
														'<div class="swiper-slide">
															<div class="dz-media DZoomImage">
																<a class="mfp-link lg-item" data-src="' . $photo['file_path'] . '">
																	<i class="feather icon-maximize dz-maximize top-left"></i>
																</a>
																<img src="' . $photo['file_path'] . '" alt="image">
															</div>
														</div>';
													$photo_thumbnails = $photo_thumbnails . '
													<div class="swiper-slide">
														<img src="' . $photo['file_path'] . '" alt="image">
													</div>';
												}
												echo $photo_images;
											}
											?>
										</div>
									</div>
									<div class="swiper product-gallery-swiper thumb-swiper-lg">
										<div id="photoThumbnails" class="swiper-wrapper">
											<?php
											echo $photo_thumbnails;
											?>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-xl-8 col-md-8">
							<div class="row">
								<div class="col-xl-7">
									<div class="dz-product-detail style-2 p-t20 ps-0">
										<div class="dz-content">
											<div class="dz-content-footer">
												<div class="dz-content-start">
													<span id="isOnSaleBadge" class="badge badge-pill bg-warning mb-2"
														hidden>On
														Sale</span>
													<h4 id="titleLabel" class="title mb-1"></h4>
												</div>
											</div>
											<p id="descriptionLabel" class="para-text"></p>
											<div class="meta-content m-b20 d-flex align-items-end">
												<div class="me-3">
													<span class="price-name">Price</span>
													<span id="priceLabel" class="price-num"></span>
												</div>
											</div>
											<!-- Start Size -->
											<div id="sizeOptionsBlock" class="product-num" hidden>
												<div class="d-block">
													<label class="form-label">Size</label>
													<div id="sizeOptions" class="btn-group product-size mb-0">
														<input type="radio" class="btn-check" name="btnradio1"
															id="sizeRadioN" checked="" value="N">
														<label class="btn btn-light" for="sizeRadioN">N/A</label>
													</div>
												</div>
											</div>
											<!-- End Size -->
											<!-- TODO: Layer Quality Block -->
											<!-- Start Layer Quality -->
											<!-- <div id="layerQualityBlock" class="product-num" hidden>
												<div class="d-block">
													<label class="form-label">Layer Quality</label>
													<div id="layerQualityOptions" class="btn-group product-size mb-0">
														<input type="radio" class="btn-check" name="btnradio1"
															id="layerQualityN" checked="" value="N">
														<label class="btn btn-light" for="layerQualityN">N/A</label>
														<input type="radio" class="btn-check" name="btnradio1"
															id="layerQualityS" checked="" value="S">
														<label class="btn btn-light" for="layerQualityS">Standard (0.2)</label>
														<input type="radio" class="btn-check" name="btnradio1"
															id="layerQualityZ" checked="" value="N">
														<label class="btn btn-light" for="layerQualityZ">Standard (0.2)<br>Fuzzy Skin</label>
													</div>
												</div>
											</div> -->
											<!-- End Layer Quality -->
											<!-- TODO: Customization Field -->
											<div id="customizationInputContainer" class="product-num" hidden>
												<div class="meta-content">
													<label class="form-label">Customization</label>
													<input id="customizationInput" name="dzName" class="form-control">
												</div>
											</div>
											<div id="color-block" class="product-num">
												<div class="meta-content">
													<label class="form-label">Color</label>
													<form class="d-flex align-items-center block-row" id="colorOptions"
														name="colorOptions">
														<?php
														// if ($filaments_query = mysqli_query($conn, $filaments_sql)) {
														// 	// Loop through each row in the result set
														// 	while ($filament = mysqli_fetch_array($filaments_query)) {
														// 		echo '
														// 		<div class="radio-value image-radio">
														// 			<input class="form-check-input radio-value" type="radio" name="radioColor" id="radioColor" value="' . $filament['swatch_id'] . '" aria-label="...">
														// 			<br>' . $filament['type'] . ' ' . $filament['color_name'] . '
														// 			<br>' . $filament['status'] . '
														// 			<img src="https://images.kumpeapps.com/filament?swatch=' . $filament['swatch_id'] . '_' . $base_sku . '">
														// 		</div>';
														// 	}
														// }
														?>
													</form>
												</div>
											</div>
											<div class="dz-info">
												<ul>
													<li>
														<strong>SKU:</strong>
														<span id="skuLabel"></span>
													</li>
													<li>
														<strong>Category:</strong>
														<span id="categoryLabel"></span>
													</li>
													<li>
														<strong>Tags:</strong>
														<span id="tagsLabel"></span>
													</li>
												</ul>
											</div>
										</div>
									</div>
								</div>
								<div class="col-xl-5">
									<div class="cart-detail sticky-top">
										<table>
											<tbody>
												<tr>
													<div class="btn-quantity light d-xl-block">
														<label class="form-label">Quantity</label>
														<input min="1" id="productQuantity" type="number" value="1"
															name="qty">
													</div>
												</tr>
												<tr class="total">
													<td>
														<h6 class="mb-0">Total</h6>
													</td>
													<td id="totalPriceLabel" class="price"></td>
												</tr>
											</tbody>
										</table>
										<div id="addToCartContainer"><a id='addToCartButton1'
												class="btn btn-secondary w-100">ADD TO CART</a></div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>

			<?php
			include("./includes/footer.php");
			?>

			<button class="scroltop" type="button"><i class="fas fa-arrow-up"></i></button>

		</div>

		<!-- JAVASCRIPT FILES ========================================= -->
		<script nonce="<?php echo $nonce; ?>" src="js/jquery.min.js"></script>
		<!-- JQUERY MIN JS -->
		<script nonce="<?php echo $nonce; ?>" src="vendor-js/wow/wow.min.js"></script>
		<!-- WOW JS -->
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
		<script nonce="<?php echo $nonce; ?>" src="vendor-js/wnumb/wNumb.js"></script>
		<!-- WNUMB -->
		<script nonce="<?php echo $nonce; ?>" src="vendor-js/nouislider/nouislider.min.js"></script>
		<!-- NOUSLIDER MIN JS-->
		<script nonce="<?php echo $nonce; ?>" src="js/dz.carousel.js"></script>
		<!-- DZ CAROUSEL JS -->
		<script nonce="<?php echo $nonce; ?>" src="vendor-js/lightgallery/dist/lightgallery.min.js"></script>
		<script nonce="<?php echo $nonce; ?>"
			src="vendor-js/lightgallery/dist/plugins/thumbnail/lg-thumbnail.min.js"></script>
		<script nonce="<?php echo $nonce; ?>" src="vendor-js/lightgallery/dist/plugins/zoom/lg-zoom.min.js"></script>
		<script nonce="<?php echo $nonce; ?>" src="js/dz.ajax.js"></script>
		<!-- AJAX -->
		<script nonce="<?php echo $nonce; ?>" src="js/custom.js"></script>
		<!-- CUSTOM JS -->
		<script nonce="<?php echo $nonce; ?>" src="js/product.js?version=202311171701"></script>


</body>

</html>

<?php
mysqli_close($conn);
?>