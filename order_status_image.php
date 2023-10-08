<?php
session_start();
require_once 'includes/site_params.php';
$sqlhost = $_ENV['mysql_host'];
$conn = new mysqli(
    $sqlhost,
    $_ENV['mysql_user'],
    $_ENV['mysql_pass'],
    'Web_3dprints',
    "3306"
);
$cancelled_image = "./pcloud/images/order_cancelled.png";
$delivered_image = "./pcloud/images/order_status_delivered.png";
$intransit_image = "./pcloud/images/order_status_intransit.png";
$ordered_image = "./pcloud/images/order_status_ordered.png";
$packed_image = "./pcloud/images/order_status_packed.png";
$order_id = $_GET['order_id'];
$sql = "
    SELECT 
        status_id
    FROM
        Web_3dprints.orders
    WHERE idorders = $order_id;
";
$result = $conn->query($sql);
$result = $result->fetch_row();
$status_id = $result[0];

if ($status_id >= 3 && $status_id <= 12) {
    $file = $ordered_image;
} else if ($status_id == 13) {
    $file = $packed_image;
} else if ($status_id == 14) {
    $file = $intransit_image;
} else if ($status_id == 15) {
    $file = $delivered_image;
} else if ($status_id > 15) {
    $file = $cancelled_image;
} else {
    $file = $ordered_image;
}

$type = 'image/png';
mysqli_close($conn);
header('Content-Type:' . $type);
header('Content-Length: ' . filesize($file));
readfile($file);
?>