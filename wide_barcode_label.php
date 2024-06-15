<?php
$sku = 'test';
$title = 'Carbon Fiber PETG Bear Wine Bottle Holder';
$filamnet_color = 'Orange';
require_once 'includes/site_params.php';
$sku = $_GET['sku'];
$conn = mysqli_connect(
    "sql.kumpedns.us",
    $_ENV['mysql_user'],
    $_ENV['mysql_pass'],
    'Web_3dprints'
) or die("Couldn't connect to server.");

if (isset($_GET['distributor'])) {
    $distributor = $_GET['distributor'];
} else {
    $distributor = 0;
}

// Search Product by full SKU
$product_sql = "
    SELECT 
        upc.upc,
        upc.ean,
        upc.sku,
        IFNULL(dist.dist_sku, upc.sku) AS dist_sku
    FROM
        Web_3dprints.upc_codes upc
    LEFT JOIN
        distributor_skus dist ON dist.sku = upc.sku
        AND dist.iddistributors = $distributor
    WHERE 1 = 1
        AND (upc.sku = '$sku' OR upc.upc = '$sku');
";

$product_result = mysqli_query($conn, $product_sql);
$product_num_results = mysqli_num_rows($product_result);
$product_data = mysqli_fetch_assoc($product_result);

if (true) {
    $color_id = substr($product_data['sku'], -3);
    $color_sql = "
        SELECT 
            swatch_id,
            `name`,
            `type`,
            color_name
        FROM
            Web_3dprints.filament
        WHERE 1=1
            AND (swatch_id = '$color_id');
    ";
    $color_result = mysqli_query($conn, $color_sql);
    $color_num_results = mysqli_num_rows($color_result);
    $color_data = mysqli_fetch_assoc($color_result);
}

$filament_color = $color_data['color_name'];
$upc = $product_data['upc'];
$barcode = "https://barcodeapi.org/api/$upc";
?>
<html>

<head>
    <style>
        @page {
            margin: 0;
        }

        body {
            margin: 0in 0in 0in 0in;
            width: 50mm;
            height: 30mm;
        }

        .body {
            width: 50mm;
            height: 30mm;
        }

        .sku {
            text-align: left;
            text-wrap: ;
            font-size: 50%;
            /* padding-right: 12mm; */
            line-height: 1em;
            /* a */
            max-height: 1em;
            /* a x number of line to show (ex : 2 line)  */
        }

        .color-name {
            text-align: left;
            text-wrap: break-word;
            font-size: xx-small;
            /* padding-right: 12mm; */
            line-height: 1em;
            /* a */
            max-height: 1em;
            /* a x number of line to show (ex : 2 line)  */
        }

        .right-block {
            float: right;
            max-width: 40mm;
        }

        .qr {
            width: 29mm;
            transform: rotate(-90deg);
            float: left;
        }

        /* * {
            font-family: arial;
            font-size: 12px;
        } */
    </style>
</head>

<body>
    <div class="right-block">
        <div class="color-name">
            <b>
                Color: <?php echo $filament_color; ?>
            </b>
        </div>
        <div class="sku">
            <b>
                K3D sku: <?php echo $product_data['sku']; ?>
            </b>
        </div>
    </div>
    <img class="qr" src="<?php echo $barcode; ?>">

</body>

</html>