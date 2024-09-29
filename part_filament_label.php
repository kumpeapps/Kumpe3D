<?php
$swatch_id = 'P01';
require_once 'includes/site_params.php';
$swatch_id = $_GET['swatch_id'];
$conn = mysqli_connect(
    "sql.kumpedns.us",
    $_ENV['mysql_user'],
    $_ENV['mysql_pass'],
    'Web_3dprints'
) or die("Couldn't connect to server.");

// Search Product by full SKU
$product_sql = "
    SELECT 
        products.sku,
        filament.color_name
    FROM
        Web_3dprints.products products
    left join filament on filament.swatch_id = right(products.sku, 3)
    WHERE 1=1
        AND products.sku like '%FIL-PRT-$swatch_id';
";

$product_result = mysqli_query($conn, $product_sql);
$product_num_results = mysqli_num_rows($product_result);
$product_data = mysqli_fetch_assoc($product_result);

$filament_color = $product_data['color_name'];
$sku = $product_data['sku'];
$barcode = 'https://api.qrserver.com/v1/create-qr-code/?size=50x50&data=' . $sku;
?>
<html>

<head>
    <style>
        @page {
            margin: 0;
        }

        body {
            margin: 0in 0in 0in 0in;
            width: 40mm;
            height: 28mm;
        }

        .body {
            width: 40mm;
            height: 28mm;
        }

        .sku {
            text-align: center;
            text-wrap: break-word;
            font-size: smaller;
            max-width: 39.7mm;
            /* padding-right: 12mm; */
            line-height: 1em;
            /* a */
            max-height: 1em;
            /* a x number of line to show (ex : 2 line)  */
        }

        .color-name {
            text-align: center;
            text-wrap: break-word;
            font-size: xx-small;
            max-width: 39.7mm;
            /* padding-right: 12mm; */
            line-height: 1em;
            /* a */
            max-height: 1em;
            /* a x number of line to show (ex : 2 line)  */
        }

        .barcode-block {
            text-align: center;
            text-wrap: break-word;
            font-size: x-small;
            padding-top: 1mm;
            max-width: 39.7mm;
            /* padding-right: 12mm; */
            line-height: 1em;
            /* a */
            max-height: 1em;
            /* a x number of line to show (ex : 2 line)  */
        }

        .qr {
            height: 17mm;
        }

        /* * {
            font-family: arial;
            font-size: 12px;
        } */
    </style>
</head>

<body>
    <div class="sku">
        <b>
            <?php echo $product_data['sku']; ?>
        </b>
    </div>
    <div class="color-name">
        <b>
            <?php echo $filament_color; ?>
        </b>
    </div>
    <div class="barcode-block">
        <img class="qr" src="<?php echo $barcode; ?>">
    </div>

</body>

</html>