<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

include 'vendor/autoload.php';
$base_url = $_SERVER['SERVER_NAME'];
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$env = $_ENV['env'];
$json = file_get_contents('php://input');
$data = json_decode($json, true);
$submit_session_id = $data['session_id'];
$data = $data['checkout_data'];
if ($data['paymentMethod'] == 'venmo') {
    $data['paymentMethod'] = 'Venmo';
} else if ($data['paymentMethod'] == 'card') {
    $data['paymentMethod'] == "Credit/Debit Card";
} else if ($data['paymentMethod'] == 'applepay') {
    $data['paymentMethod'] == 'ApplePay';
} else if ($data['paymentMethod'] == 'googlepay') {
    $data['paymentMethod'] = 'Google Pay';
} else {
    $data['paymentMethod'] = 'PayPal';
}
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
    $email_products = "";
    $email_name = $data['firstName'];
    $email_shippingname = $data['firstName'] . " " . $email_shippingname = $data['lastName'];
    if ($data['companyName'] != '') {
        $email_shippingname = $email_shippingname . "<br>" . $email_shippingname = $data['companyName'];
    }
    $email_address = $data['address'];
    $email_address2 = $data['address2'];
    if ($email_address != '') {
        $email_address = $email_address . "<br>" . $email_address2;
    }
    $email_city = $data['city'];
    $email_state = $data['state'];
    $email_zip = $data['zip'];
    $email_country = $data['country'];
    $email_subtotal = "$" . $data['subtotal'];
    $email_taxes = "$" . $data['taxes'];
    $email_shipping = "$" . $data['shippingCost'];
    $email_discount = "$" . $data['discount'];
    $email_total = "$" . $data['total'];
    $email_paymentmethod = $data['paymentMethod'];
    $email_shippingmethod = "Flat Rate";
    $email_notes = $data['orderNotes'];
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
    $email_orderid = $order_id;
    $email_date = '';
    $cart = $data['cart'];

    $response = [];
    $response['id'] = $order_id;
    print(json_encode($response));
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
        $product_img = $item['image_url'];
        $product_name = $item['name'];
        $product_sku = $item['sku'];
        $product_quantity = $item['quantity'];
        $product_price = "$" . $item['price'];
        $stmt->execute();
        $html_email_items = "
            <tr>
                <td align=\"left\" style=\"padding:0;Margin:0;padding-left:20px;padding-right:20px;padding-bottom:40px\">
                    <!--[if mso]><table style=\"width:560px\" cellpadding=\"0\" cellspacing=\"0\"><tr><td style=\"width:195px\" valign=\"top\"><![endif]-->
                    <table cellpadding=\"0\" cellspacing=\"0\" class=\"es-left\" align=\"left\"
                        style=\"mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left\">
                        <tr>
                            <td align=\"left\" class=\"es-m-p20b\" style=\"padding:0;Margin:0;width:195px\">
                                <table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" role=\"presentation\"
                                    style=\"mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px\">
                                    <tr>
                                        <td align=\"center\" style=\"padding:0;Margin:0;font-size:0px\"><a target=\"_blank\"
                                                href=\"\"
                                                style=\"-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:underline;color:#6A994E;font-size:16px\"><img
                                                    class=\"adapt-img p_image\"
                                                    src=\"$product_img\"
                                                    alt
                                                    style=\"display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic;border-radius:10px\"
                                                    width=\"195\"></a></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <!--[if mso]></td><td style=\"width:20px\"></td><td style=\"width:345px\" valign=\"top\"><![endif]-->
                    <table cellpadding=\"0\" cellspacing=\"0\" class=\"es-right\" align=\"right\"
                        style=\"mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:right\">
                        <tr>
                            <td align=\"left\" style=\"padding:0;Margin:0;width:345px\">
                                <table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\"
                                    style=\"mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:separate;border-spacing:0px;border-left:1px solid #386641;border-right:1px solid #386641;border-top:1px solid #386641;border-bottom:1px solid #386641;border-radius:10px\"
                                    role=\"presentation\">
                                    <tr>
                                        <td align=\"left\" class=\"es-m-txt-c\"
                                            style=\"Margin:0;padding-left:20px;padding-right:20px;padding-top:25px;padding-bottom:25px\">
                                            <h3 class=\"p_name\"
                                                style=\"Margin:0;line-height:36px;mso-line-height-rule:exactly;font-family:Raleway, Arial, sans-serif;font-size:24px;font-style:normal;font-weight:normal;color:#386641\">
                                                $product_name</h3>
                                            <p class=\"p_description\"
                                                style=\"Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:tahoma, verdana, segoe, sans-serif;line-height:24px;color:#4D4D4D;font-size:16px\">
                                                SKU: $product_sku</p>
                                            <p
                                                style=\"Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:tahoma, verdana, segoe, sans-serif;line-height:24px;color:#4D4D4D;font-size:16px\">
                                                QTY:&nbsp;$product_quantity</p>
                                            <h3 style=\"Margin:0;line-height:36px;mso-line-height-rule:exactly;font-family:Raleway, Arial, sans-serif;font-size:24px;font-style:normal;font-weight:normal;color:#386641\"
                                                class=\"p_price\">$product_price each</h3>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table><!--[if mso]></td></tr></table><![endif]-->
                </td>
            </tr>
        ";
        $email_products = $email_products . $html_email_items;
    }
    $history_sql = "
        INSERT INTO `Web_3dprints`.`orders__history`
            (`idorders`,
            `status_id`,
            `notes`,
            `updated_by`)
        VALUES
            (?, ?, 'Order Paid', 'checkout');
    ";
    $stmt = $db->prepare($history_sql);
    $stmt->bind_param(
        "ii",
        $order_id,
        $data['statusID']
    );
    $stmt->execute();
    require_once 'order_confirm_email.php';
    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER; //Enable verbose debug output
        $mail->isSMTP(); //Send using SMTP
        $mail->Host = 'mail.kumpeapps.com'; //Set the SMTP server to send through
        $mail->SMTPAuth = true; //Enable SMTP authentication
        $mail->Username = $_ENV['email_user']; //SMTP username
        $mail->Password = $_ENV['email_pass']; //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; //Enable implicit TLS encryption
        $mail->Port = 587; //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom($_ENV['email_user'], 'Kumpe3D');
        $mail->addAddress($data['emailAddress'], $data['firstName']." ".$data['lastName']); //Add a recipient
        $mail->addReplyTo('sales@kumpeapps.com', 'Kumpe3D');
        $mail->addBCC('sales@kumpeapps.com');

        //Attachments
        // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
        $subject = 'Kumpe3D Order Number ' . $email_orderid;
        if ($env == 'dev') {
            $subject = '[PreProd] '.$subject;
        }
        //Content
        $mail->isHTML(true); //Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body = $html_email;

        $mail->send();
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
    }
}
mysqli_close($db);
?>