<?php
$sku = 'test';
$title = 'Carbon Fiber PETG Bear Wine Bottle Holder';
$filament_type = 'OV PLA+';
$filamnet_color = 'Orange';
require_once 'includes/site_params.php';
$sku = $_GET['sku'];

if (isset($_GET['qr_data'])) {
    $qr_data = $_GET['qr_data'];
} else {
    $qr_data = $sku;
}

$conn = mysqli_connect(
    $_ENV['mysql_host'],
    $_ENV['mysql_user'],
    $_ENV['mysql_pass'],
    'Web_3dprints'
) or die("Couldn't connect to server.");

if (strlen($sku) == 15 || (strlen($sku) == 3)) {
    $get_color = true;
} else {
    $get_color = false;
}
$base_sku = substr($sku, 0, 11);
$dyncolor_sku = $base_sku . '-000';

// Search Product by full SKU
$product_sql = "
        SELECT 
        sku,
        title
    FROM
        Web_3dprints.products
    WHERE 1=1
        AND sku = '$sku';
";
// Search Product by Dynamic Color
$product_dyncolor_sql = "
    SELECT 
        sku,
        title
    FROM
        Web_3dprints.products
    WHERE 1=1
        AND sku = '$dyncolor_sku';
";
$product_result = mysqli_query($conn, $product_sql);
$product_num_results = mysqli_num_rows($product_result);
$product_data = mysqli_fetch_assoc($product_result);

if ($product_num_results == 0) {
    $dyncolor_product_result = mysqli_query($conn, $product_dyncolor_sql);
    $product_data = mysqli_fetch_assoc($dyncolor_product_result);
}
if ($get_color) {
    $color_id = substr($sku, -3);
    $color_sql = "
        SELECT 
            swatch_id,
            `name`,
            `type`,
            color_name
        FROM
            Web_3dprints.filament
        WHERE 1=1
            AND (swatch_id = '$color_id' OR manufacture_barcode = '$sku');
    ";
    $color_result = mysqli_query($conn, $color_sql);
    $color_num_results = mysqli_num_rows($color_result);
    $color_data = mysqli_fetch_assoc($color_result);
}
$title = $product_data['title'];
$filament_type = $color_data['type'];
$filament_color = $color_data['color_name'];

$qr_url = 'https://api.qrserver.com/v1/create-qr-code/?size=50x50&data=' . $qr_data;
?>
<html>

<head>
    <style>
        @page {
            margin: 0;
        }

        body {
            margin: 0in 0in 0in 0in;
            width: 80mm;
            height: 49.7mm;
        }

        .body {
            width: 80mm;
            height: 49.7mm;
        }

        .logo {
            width: 13.5mm;
            float: left;
            padding-left: 0.02mm;
            padding-top: 0.02mm;
        }

        .sku {
            text-align: center;
            text-wrap: wrap;
            line-height: 17mm;
            font-size: medium;
            padding-left: 13.6mm;
        }

        .title {
            text-align: center;
            text-wrap: break-word;
            padding-left: 0.1mm;
            padding-right: 0.1mm;
            padding-top: 2mm;
            font-size: larger;
            height: 14mm;
            max-width: 79.2mm;
            /* padding-right: 12mm; */

        }

        .color-name {
            text-align: center;
            text-wrap: break-word;
            padding-left: 0.1mm;
            /* padding-right: 0.1mm; */
            padding-bottom: 1mm;
            font-size: small;
            height: 2mm;
            line-height: 1em;
            max-height: 1em;
            max-width: 79.2mm;
            /* padding-right: 12mm; */
        }

        .qr {
            width: 16mm;
            float: right;
            padding-top: 1mm;
            padding-right: 1.5mm;
        }

        /* * {
            font-family: arial;
            font-size: 12px;
        } */

        .top-container {
            width: 80mm;
            height: 17mm;
        }
    </style>
</head>

<body>
    <div class="top-container">
        <img class="logo" src="images/logo_black_transparent.png">

        <img class="qr" src="<?php echo $qr_url; ?>">
        <div class="sku">
            <b>
                <?php echo $sku; ?>
            </b>
        </div>
    </div>
    <div class="title">
        <b>
            <?php echo $title; ?>
        </b>
    </div>
    <div class="color-name">
        <b>
            <?php echo $filament_type . ' ' . $filament_color; ?>
        </b>
    </div>
</body>

</html>