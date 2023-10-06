<?php
	include 'vendor/autoload.php';
	$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
	$dotenv->load();
	$env = $_ENV['env'];
	$conn = mysqli_connect(
		$_ENV['mysql_host'],
		$_ENV['mysql_user'],
		$_ENV['mysql_pass'],
		'Web_3dprints'
		) or die ("Couldn't connect to server.");
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
		include('./404.php');
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
	$sku['base_sku'] = $sku['design']."-".$sku['product']."-".$sku['options'];
	$base_sku = $sku['base_sku'];

	if (mysqli_connect_errno()) {
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
		exit();
	}

	$sql = "CALL get_products('$base_sku', '%', '%')";
	$product = mysqli_query($conn, $sql);
	$product = mysqli_fetch_array($product);
	mysqli_close($conn);

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
	$photo_sql = "SELECT * FROM Web_3dprints.product_photos WHERE sku = '$base_sku';";
	$filaments_sql = "CALL get_filament_options('$base_sku', '$filament_filter');";
	
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<!-- Meta -->
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

	<!-- PAGE TITLE HERE -->
	<title><?php echo $product['title']; ?></title>

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
	<link rel="stylesheet" type="text/css" href="vendor-js/lightgallery/dist/css/lightgallery.css" >
    <link rel="stylesheet" type="text/css" href="vendor-js/lightgallery/dist/css/lg-thumbnail.css">
    <link rel="stylesheet" type="text/css" href="vendor-js/lightgallery/dist/css/lg-zoom.css">
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<script src="https://unpkg.com/cart-localstorage@1.1.4/dist/cart-localstorage.min.js" type="text/javascript"></script>

	<!-- GOOGLE FONTS-->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">

</head>

<body onload="updateShoppingCartModal();">
<div class="page-wraper">
	<div id="loading-area" class="preloader-wrapper-1">
		<div>
			<span class="loader-2"></span>
			<img src="images/logo.png" alt="/">
			<span class="loader"></span>
		</div>
	</div>
	<?php
		include('./includes/header.php');
	?>

	<div class="page-content">

		<div class="d-sm-flex justify-content-between container-fluid py-3">
			<nav aria-label="breadcrumb" class="breadcrumb-row">
				<ul class="breadcrumb mb-0">
					<li class="breadcrumb-item"><a href="index.php"> Home</a></li>
					<li class="breadcrumb-item">Products</li>
					<li class="breadcrumb-item"><?php echo $product['title']; ?></li>
				</ul>
			</nav>
		</div>

		<section class="content-inner py-0">
			<div class="container-fluid">
				<div class="row">
					<div class="col-xl-4 col-md-4">
						<div class="dz-product-detail sticky-top">
							<div class="swiper-btn-center-lr">
								<div class="swiper product-gallery-swiper2" >
									<div class="swiper-wrapper" id="lightgallery">
										<?php
											if ($photo_query = mysqli_query($conn, $photo_sql)) {
												// Loop through each row in the result set
												$photo_images = '';
												$photo_thumbnails = '';
												while($photo = mysqli_fetch_array($photo_query)) {
													$photo_images = $photo_images. 
													'<div class="swiper-slide">
														<div class="dz-media DZoomImage">
															<a class="mfp-link lg-item" data-src="'.$photo['file_path'].'">
																<i class="feather icon-maximize dz-maximize top-left"></i>
															</a>
															<img src="'.$photo['file_path'].'" alt="image">
														</div>
													</div>';
													$photo_thumbnails = $photo_thumbnails.'
													<div class="swiper-slide">
														<img src="'.$photo['file_path'].'" alt="image">
													</div>';
												}
												echo $photo_images;
											}
										?>
									</div>
								</div>
								<div class="swiper product-gallery-swiper thumb-swiper-lg">
									<div class="swiper-wrapper">
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
												<h4 class="title mb-1"><?php echo $product['title']; ?></h4>
											</div>
										</div>
										<p class="para-text">
										<?php echo $product['description']; ?>
										</p>
										<div class="meta-content m-b20 d-flex align-items-end">
											<div class="me-3">
												<span class="price-name">Price</span>
												<span id="priceLabel" class="price-num">$<?php echo $product['price']; ?></span>
											</div>
										</div>
										<div class="product-num">
											<!-- <div class="btn-quantity light d-xl-block d-sm-none d-none">
												<label class="form-label">Quantity</label>
												<input min="1" id="qty" type="number" value="1" name="qty" onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57">
											</div> -->
											<!-- <div class="d-block">
												<label class="form-label">Size</label>
												<div class="btn-group product-size mb-0">
													<input type="radio" class="btn-check" name="btnradio1" id="btnradio11" checked="">
													<label class="btn btn-light" for="btnradio11">S</label>

													<input type="radio" class="btn-check" name="btnradio1" id="btnradio21">
													<label class="btn btn-light" for="btnradio21">M</label>

													<input type="radio" class="btn-check" name="btnradio1" id="btnradio31">
													<label class="btn btn-light" for="btnradio31">L</label>
												</div>
											</div> -->
											<div class="meta-content">
												<label class="form-label">Color</label>
												<div class="d-flex align-items-center block-row">
													<?php
														if ($filaments_query = mysqli_query($conn, $filaments_sql)) {
															// Loop through each row in the result set
															while($filament = mysqli_fetch_array($filaments_query)) {
																echo '
																<div class="radio-value image-radio">
																	<input onchange="changedColor()" class="form-check-input radio-value" type="radio" name="radioColor" id="radioColor" value="'.$filament['swatch_id'].'" aria-label="...">
																	<br>'.$filament['type'].' '.$filament['color_name'].'
																	<br>'.$filament['status'].'
																	<img src="https://images.kumpeapps.com/filament_swatch?swatch='.$filament['swatch_id'].'_'.$base_sku.'">
																</div>';
															}
														}
													?>
												</div>
											</div>
										</div>
										<div class="dz-info">
											<ul>
												<li>
													<strong>SKU:</strong>
													<span id="skuLabel"><?php echo $product['sku']; ?></span>
												</li>
												<li>
													<strong>Category:</strong>
													<?php echo $product['categories']; ?>
												</li>
												<li>
													<strong>Tags:</strong>
													<?php echo $product['tags']; ?>
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
												<div class="btn-quantity light d-xl-block d-sm-none d-none">
													<label class="form-label">Quantity</label>
													<input min="1" id="qty" type="number" value="1" name="qty" onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57">
												</div>
											</tr>
											<tr class="total">
												<td>
													<h6 class="mb-0">Total</h6>
												</td>
												<td id="totalPriceLabel" class="price">
													<?php echo '$'.$product['price']; ?>
												</td>
											</tr>
										</tbody>
									</table>
									<a id='addToCartButton' class="btn btn-secondary w-100">ADD TO CART</a>
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
<script src="vendor-js/lightgallery/dist/lightgallery.min.js"></script>
<script src="vendor-js/lightgallery/dist/plugins/thumbnail/lg-thumbnail.min.js"></script>
<script src="vendor-js/lightgallery/dist/plugins/zoom/lg-zoom.min.js"></script>
<script src="js/dz.ajax.js"></script><!-- AJAX -->
<script src="js/custom.js"></script><!-- CUSTOM JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script><!-- SweetAlerts -->
<script src="product.js"></script><!-- product scripts -->

<script>

	$(".image-radio img").click(function(){
		$(this).prev().attr('checked',true);
	});
	<?php
		echo 'const price = '.$product['price'].';';
		echo 'const wholesale_price = '.$product['wholesale_price'].';';
	?>

	function changedColor() {
		const color_id = getColorValue();
		const base_sku = '<?php echo $base_sku; ?>';
		const qty = document.getElementById('qty').value;
		let sku = base_sku + '-' + color_id;
		skuLabel.innerHTML = sku;
	};

	function addToCart() {
		const sku = skuLabel.innerHTML;
		const base_sku = '<?php echo $base_sku; ?>';
		const color_id = getColorValue();
		const image_url_base = 'https://images.kumpeapps.com/filament_swatch?sku=';
		const image_url = image_url_base + base_sku + '-' + color_id;
		const qty = document.getElementById('qty').value;
		let itemPrice = price;

		if (qty > 9) {
			itemPrice = wholesale_price;
		}
		if (!isColorSet()) {
			Swal.fire(
				'Error!',
				'Please select a color',
				'error'
			);
		} else {
			cartLS.add(
				{
					id: sku,
					sku: sku,
					name: "<?php echo $product['title']; ?> (" + color_id + ")",
					price: itemPrice,
					image_url: image_url,
					original_price: price,
					wholesale_price: wholesale_price
				}, parseInt(qty)
			);
			document.getElementById("cartButton").click()
		}
		updateShoppingCartModal();
	};

</script>

</body>
</html>

<?php
	mysqli_close($conn);
?>
