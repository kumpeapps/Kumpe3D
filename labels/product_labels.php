<?php
require_once 'dompdf/autoload.inc.php';
use Dompdf\Dompdf;
$sku = 'test';
$title = 'Carbon Fiber PETG Bear Wine Bottle Holder';
$filament_type = 'OV PLA+';
$filamnet_color = 'Orange';
require_once 'includes/site_params.php';
$sku = $_GET['sku'];
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

$html = file_get_contents('../product_labels.php?sku='.$sku);
$dompdf = new Dompdf();
$dompdf->loadHtml($html);

$dompdf->render();
$dompdf->stream();
?>