<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

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
    $email_products = "";
    $email_name = $data['firstName']."<br>".$data['companyName'];
    $email_address = $data['address'];
    $email_address2 = $data['address2'];
    $email_city = $data['city'];
    $email_state = $data['state'];
    $email_zip = $data['zip'];
    $email_country = $data['country'];
    $email_subtotal = $data['subtotal'];
    $email_taxes = $data['taxes'];
    $email_shipping = $data['shippingCost'];
    $email_discount = $data['discount'];
    $email_total = $data['total'];
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
        $product_price = $item['price'];
        $stmt->execute();
        require_once 'items_html.php';
        $email_products = $email_products.$html_email_items;
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
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'mail.kumpeapps.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = $_ENV['email_user'];                     //SMTP username
    $mail->Password   = $_ENV['email_pass'];                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom($_ENV['email_user'], 'Mailer');
    $mail->addAddress($data['emailAddress'], $email_name);     //Add a recipient
    $mail->addReplyTo('sales@kumpeapps.com', 'Kumpe3D');
    $mail->addBCC('sales@kumpeapps.com');

    //Attachments
    // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments

    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = 'Kumpe3D Order Number '.$email_orderid;
    $mail->Body    = $html_email;

    $mail->send();
} catch (Exception $e) {
    error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
}
}
mysqli_close($db);
$response = [];
$response['id'] = $order_id;
print(json_encode($response));
?>