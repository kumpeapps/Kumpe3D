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
        upc.short_sku,
        upc.psd_sku
    FROM
        Web_3dprints.upc_codes upc
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
            margin: 0;
            /* margin-left: -5mm; */
            margin-right: 1mm;
        }

        .body {
            margin: 0;
            /* margin-left: -5mm; */
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
            text-align: center;
            text-wrap: break-word;
            font-size: 0.5em;
            /* padding-right: 12mm; */
            line-height: 1em;
            /* a */
            max-height: 1em;
            /* a x number of line to show (ex : 2 line)  */
        }

        .right-block {
            -ms-transform: rotate(-90deg);
            -moz-transform: rotate(-90deg);
            -webkit-transform: rotate(-90deg);
            -o-transform: rotate(-90deg);
            float: right;
            max-width: 40mm;
            margin-top: 10mm;
            margin-right: -5mm;
        }

        .barcode-block {
            margin: 0;
            margin-left: 1mm;
            /* padding-top: 8mm;
            padding-right: 1mm; */
        }

        .barcode {
            margin: 0;
            width: 35mm;
            float: left;
            display: block;
            white-space: nowrap;
            /* padding-top: 10mm; */
        }

        /* * {
            font-family: arial;
            font-size: 12px;
        } */
    </style>
</head>

<body>
    <div class="right-block">
        <div class="sku">
            <b>
                Short sku: <?php echo $product_data['short_sku']; ?><br>
                PSD sku: <?php echo $product_data['psd_sku']; ?><br>
                Manufacture: Kumpe3D<br>
                UPC: <?php echo $product_data['upc']; ?><br>
                EAN: <?php echo $product_data['ean']; ?><br>
            </b>
        </div>
    </div>
    <div class="barcode-block">
        <div class="color-name">
            <b>
                Color: <?php echo $filament_color; ?><br>
                K3D sku: <?php echo $product_data['sku']; ?>
            </b>
        </div>
        <img class="barcode" src="<?php echo $barcode; ?>">
    </div>

</body>

</html>