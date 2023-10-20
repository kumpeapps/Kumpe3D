<?php
$color_id = 'P01';
require_once 'includes/site_params.php';
$sku = $_GET['color_id'];
$conn = mysqli_connect(
    $_ENV['mysql_host'],
    $_ENV['mysql_user'],
    $_ENV['mysql_pass'],
    'Web_3dprints'
) or die("Couldn't connect to server.");


$color_id = substr($sku, -3);
$color_sql = "
    SELECT 
        swatch_id,
        `name`,
        `type`,
        substring(color_name,4) as color_name,
        brand
    FROM
        Web_3dprints.filament
    WHERE 1=1
        AND (swatch_id = '$color_id' OR manufacture_barcode = '$color_id')
    LIMIT 1;
    ";
$color_result = mysqli_query($conn, $color_sql);
$color_num_results = mysqli_num_rows($color_result);
$color_data = mysqli_fetch_assoc($color_result);

$brand = $color_data['brand'];
$filament_type = $color_data['type'];
$filament_color = $color_data['color_name'];

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

        * {
            font-family: arial;
            font-size: 12px;
        }

        .top-container {
            width: 80mm;
            height: 17mm;
        }
    </style>
</head>

<body>
        <b>Filament Type:</b> <?php echo $filament_type ?><br><br>
        <b>Filament Name:</b> <?php echo $filament_color ?><br><br>
        <b>Manufacture:</b> <?php echo $brand ?><br><br>
        <b>Your Name:</b> Kumpe3D
</body>

</html>