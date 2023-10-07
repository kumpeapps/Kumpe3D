<?php
	include_once './vendor/autoload.php';
	$dotenv = Dotenv\Dotenv::createImmutable(__DIR__, '../.env');
	$dotenv->load();
	$env = $_ENV['env'];
	$conn = mysqli_connect(
		$_ENV['mysql_host'],
		$_ENV['mysql_user'],
		$_ENV['mysql_pass'],
		'Web_3dprints'
	) or die ("Couldn't connect to server.");
	$params_sql = "
        SELECT 
            `parameter`,
            `value`
        FROM
            Web_3dprints.site_parameters;
    ";
?>