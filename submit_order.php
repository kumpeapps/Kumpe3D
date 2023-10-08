<?php
	session_start();
	include 'vendor/autoload.php';
	$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
	$dotenv->load();
	$env = $_ENV['env'];
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
	$submit_session_id = $data['session_id'];
	$data = $data['checkout_data'];
    $order_id = "unavailable";
    $sql = "
        INSERT INTO `Web_3dprints`.`orders`
            (`idcustomers`,
            `first_name`,
            `last_name`,
            `company_name`,
            `email`,
            `street_address`,
            `street_address_2`,
            `city`,
            `state`,
            `zip`,
            `country`,
            `subtotal`,
            `taxes`,
            `shipping_cost`,
            `discount`,
            `total`,
            `order_date`,
            `status_id`,
            `payment_method`,
            `paypal_transaction_id`,
            `notes`)
        VALUES
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, now(), ?, ?, ?, ?);
    ";
    $db = new mysqli(
        $_ENV['mysql_host'],
        $_ENV['mysql_user'],
        $_ENV['mysql_pass'],
        'Web_3dprints',
        "3306"
    );
    if ($submit_session_id == session_id()) {
        $stmt = $db->prepare($sql);
        $stmt->bind_param(
            "issssssssssdddddisss",
            $data['customerID'],
            $data['firstName'],
            $data['lastName'],
            $data['companyName'],
            $data['emailAddress'],
            $data['address'],
            $data['address2'],
            $data['city'],
            $data['state'],
            $data['zip'],
            $data['country'],
            $data['subtotal'],
            $data['taxes'],
            $data['shippingCost'],
            $data['discount'],
            $data['total'],
            $data['statusID'],
            $data['paymentMethod'],
            $data['ppTransactionID'],
            $data['orderNotes']
        );
        $stmt->execute();
        $order_id = $db->insert_id;
        $cart = $data['cart'];

        $items_sql = "
            INSERT INTO `Web_3dprints`.`orders__items`
                (`idorders`,
                `sku`,
                `title`,
                `price`,
                `qty`,
                `customization`)
            VALUES
                (?, ?, ?, ?, ?, NULL);
        ";

        foreach ($cart as $item) {
            $stmt = $db->prepare($items_sql);
            $stmt->bind_param(
                "issdi",
                $order_id,
                $item['sku'],
                $item['name'],
                $item['price'],
                $item['quantity']
            );
            $stmt->execute();
          }
    }
    mysqli_close($db);
    $response = [];
    $response['id'] = $order_id;
    print(json_encode($response));
?>