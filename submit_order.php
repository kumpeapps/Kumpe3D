<?php
	session_start();
	include 'vendor/autoload.php';
	$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
	$dotenv->load();
	$env = $_ENV['env'];
	$submit_session_id = $_POST['session_id'];
    if ($submit_session_id == session_id()) {
        $conn = mysqli_connect(
            $_ENV['mysql_host'],
            $_ENV['mysql_user'],
            $_ENV['mysql_pass'],
            'Web_3dprints'
            ) or die ("Couldn't connect to server.");
        $checkout = $_POST['checkout_data'];
        $cart = $checkout['cart'];

        $sql = "INSERT INTO MyGuests (firstname, lastname, email)
        VALUES ('John', 'Doe', 'john@example.com')";

        if (mysqli_query($conn, $sql)) {
            $order_id = mysqli_insert_id($conn);
            echo "New record created successfully. Last inserted ID is: " . $last_id;
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    }
    mysqli_close($conn);
?>