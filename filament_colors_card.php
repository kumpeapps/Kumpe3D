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
        AND swatch_id = '$color_id';
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
            padding-top: 5mm;
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
            padding-right: 1mm;
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
        Filament Type: <?php echo $filament_type ?><br>
        Filament Name: <?php echo $filament_color ?><br>
        Manufacture: <?php echo $brand ?><br>
        Your Name: Kumpe3D
</body>

</html>