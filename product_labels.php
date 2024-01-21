<?php
$sku = 'test';
$title = 'Carbon Fiber PETG Bear Wine Bottle Holder';
$filament_type = 'OV PLA+';
$filamnet_color = 'Orange';
// require_once 'includes/site_params.php';
// $sku = $_GET['sku'];
// $conn = mysqli_connect(
// 	$_ENV['mysql_host'],
// 	$_ENV['mysql_user'],
// 	$_ENV['mysql_pass'],
// 	'Web_3dprints'
// ) or die("Couldn't connect to server.");

// $product = "
//   SELECT 
//     idorders,
//     first_name,
//     last_name,
//     company_name,
//     email,
//     street_address,
//     street_address_2,
//     city,
//     state,
//     zip,
//     country,
//     order_date,
//     payment_method,
//     notes
//   FROM
//     Web_3dprints.orders
//   WHERE 1=1
//     AND idorders = $order_id;
// ";
// $order_result = mysqli_query($conn, $order_sql);
// $order = mysqli_fetch_array($order_result, MYSQLI_ASSOC);

// $items_sql = "
//   SELECT 
//       *
//   FROM
//       Web_3dprints.orders__items
//   WHERE 1=1
//     AND idorders = $order_id;
// ";
// $items_result = mysqli_query($conn, $items_sql);
// $items = "";
// while($item = mysqli_fetch_array($items_result)) {
//   $title = $item["title"];
//   $sku = $item["sku"];
//   $qty = $item["qty"];
//   $item_row = "
//     <tr>
//       <td class='sku'>$title</td>
//       <td class='sku'>$sku</td>
//       <td align='center'>$qty</td>
//     </tr>
//   ";
//   $items = $items.$item_row;
// mysqli_close($conn);
// }

$qr_url = 'https://api.qrserver.com/v1/create-qr-code/?size=50x50&data=' . $sku;
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
            position: absolute;
            top: 0px;
            left: 0px;
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
            /* padding-right: 12mm; */
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
        <img class="logo" src="images/logo.png">

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
            <?php echo $filament_type . ' ' . $filamnet_color; ?>
        </b>
    </div>
</body>

</html>