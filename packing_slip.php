<?php
require_once 'includes/site_params.php';
$order_id = $_GET['order_id'];
$conn = mysqli_connect(
	$_ENV['mysql_host'],
	$_ENV['mysql_user'],
	$_ENV['mysql_pass'],
	'Web_3dprints'
) or die("Couldn't connect to server.");

$order_sql = "
  SELECT 
    idorders,
    first_name,
    last_name,
    company_name,
    email,
    street_address,
    street_address_2,
    city,
    state,
    zip,
    country,
    order_date,
    payment_method,
    notes
  FROM
    Web_3dprints.orders
  WHERE 1=1
    AND idorders = $order_id;
";
$order_result = mysqli_query($conn, $order_sql);
$order = mysqli_fetch_array($order_result, MYSQLI_ASSOC);

$items_sql = "
  SELECT 
      *
  FROM
      Web_3dprints.orders__items
  WHERE 1=1
    AND idorders = $order_id;
";
$items_result = mysqli_query($conn, $items_sql);
$items = "";
while($item = mysqli_fetch_array($items_result)) {
  $title = $item["title"];
  $sku = $item["sku"];
  $qty = $item["qty"];
  $item_row = "
    <tr>
      <td class='sku'>$title</td>
      <td class='sku'>$sku</td>
      <td align='center'>$qty</td>
    </tr>
  ";
  $items = $items.$item_row;
// mysqli_close($conn);
}

?>
<html>

<head>
  <style>
    header {
      width: 4in;
      display: block;
      margin-left: auto;
      margin-right: auto;
      height: 1in;
    }

    body {
      margin: 0in 0in 0in 0in;
      width: 4in;
    }

    * {
      font-family: arial;
      font-size: 12px;
    }

    th {
      background-color: gray;
      color: white;
      font-weight: bold;
    }

    td {
      vertical-align: top;
    }

    .store-info div {
      font-size: 1.2em;
    }

    .store-info div.company-name {
      font-size: 1.5em;
      font-weight: bold;
    }

    table.order-info td {

      padding: 2px 4px 2px 4px;
    }

    table.order-info tr td.label {
      font-weight: bold;
      text-align: right;
      border-right: solid 1px #c0c0c0;

    }

    table.order-info tr td.label.first {}

    table.order-info tr td.label.last {}

    table.line-items {
      margin-top: 0.1in;
      padding: 0.1in 0in 0.1in 0in;
    }

    table.line-items th {
      padding: 2px;
    }

    table.footer {
      border-top: solid 1px #707070;
    }

    table.footer td.label {
      font-weight: bold;
      text-align: right;
    }

    td.notes {
      padding: 0.1in;
      font-style: italic;
    }

    .barcode {
      font-family: "Free 3 of 9 Extended";
      font-size: 48pt;
    }
  </style>
</head>

<body>
  <!-- Order Header - THIS SECTION CAN BE MODIFIED AS NEEDED -->
  <table cellspacing=0 cellpadding="2" border=0 style="width:4in">
    <thead>
      <tr>
        <th colspan="3">
          Packing Slip
        </th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td colspan="2" style="width:3.5in" class="store-info">
          <div class="company-name">Kumpe3D by KumpeApps LLC</div>
          <div>4021 West Walnut Street #1109<br/>Rogers, AR  72756</div>
        </td>
        <td style="width:3.5in;" align="right" valign="top">

        </td>
      </tr>
      <tr>
        <td style="height:0.15in"></td>
      </tr>
      <tr>
        <td align="right" style="width:1in">
          <b>Ship To:</b>
        </td>
        <td style="width:3.5in; font-size:14px">
          <div><?php echo $order['first_name']." ".$order['last_name']; ?></div>
          <div><?php echo $order['company_name']; ?></div>
          <div><?php echo $order['street_address']; ?></div>
          <div><?php echo $order['street_address_2']; ?></div>
          <div><?php echo $order['city'].', '.$order['state'].'  '.$order['zip']; ?></div>
        </td>
        <td style="width:2.5in">
          <table cellspacing="0" border="0" class="order-info">
            <tr>
              <td align="right" class="label first">Order #</td>
              <td><?php echo $order['idorders']; ?></td>
            </tr>
            <tr>
              <td align="right" class="label">Date</td>
              <td><?php echo $order['order_date']; ?></td>
            </tr>
          </table>
        </td>
      </tr>
    </tbody>
  </table>

  <!-- END Order Header -->

  <table cellspacing=0 cellpadding="2" border="0" style="width:100%" class="line-items">
    <thead>

      <!-- Order Items Header - THIS SECTION CAN BE MODIFIED AS NEEDED -->

      <tr>
        <th align="left" style="width:1.5in" class="sku">
          Item
        </th>
        <th align="left" style="width:1.5in" class="sku">
          Item SKU
        </th>
        <th align="center" style="width:0.75in">
          Qty
        </th>
      </tr>

      <!-- END Order Items Header -->

    </thead>
    <tbody>

      <!-- Order Items - THIS SECTION CAN BE MODIFIED AS NEEDED -->
      <?php echo $items; ?>
      <!-- END Order Items -->

    </tbody>
  </table>

  <!-- Order Footer - THIS SECTION CAN BE MODIFIED AS NEEDED -->

  <table cellspacing=0 cellpadding="2" border="0" style="width:100%" class="footer">
    <p>
    <?php echo $order['notes']; ?>
    </p>
  </table>

  <!-- END Order Footer -->

  </div>

</body>

</html>