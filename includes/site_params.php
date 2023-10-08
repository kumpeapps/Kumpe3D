<?php
	include_once 'vendor/autoload.php';
	$dotenv = Dotenv\Dotenv::createImmutable(__DIR__, '../.env');
	$dotenv->load();
	$env = $_ENV['env'];
    $base_url = $_SERVER['SERVER_NAME'];
	$params_conn = mysqli_connect(
		$_ENV['mysql_host'],
		$_ENV['mysql_user'],
		$_ENV['mysql_pass'],
		'Web_3dprints'
	) or die ("Couldn't connect to server.");
	$params_sql = "
        SELECT 
            parameter,
            value,
            type
        FROM
            Web_3dprints.site_parameters;
    ";

    function snakeToCamelCase($string, $capitalizeFirstCharacter = false) {
        $str = str_replace('_', '', ucwords($string, '_'));
        if (!$capitalizeFirstCharacter) {
            $str = lcfirst($str);
        }
        return $str;
    }

    $result = mysqli_query($params_conn, $params_sql);
    $site_params = [];
    $site_params_js = "let siteParams = {};";

    if ($result) {
      while($row = mysqli_fetch_array($result)) {
        if ($env == "dev" && $row['parameter'] == "store_paypal_clientid_sandbox") {
            $paypal_clientid = $row['value'];
        } elseif ($env == "prod" && $row['parameter'] == "store_paypal_clientid_prod") {
            $paypal_clientid = $row['value'];
        }
        $site_params[$row['parameter']] = $row['value'];
        switch ($row['type']) {
            case "json":
                $value = json_encode($row['value']);
                $param = snakeToCamelCase($row['parameter']);
                $jsparam = 'siteParams["'.$param.'"] = JSON.parse('.$value.');';
                $site_params_js = $site_params_js.$jsparam;
                break;
            case "int":
                $value = $row['value'];
                $param = snakeToCamelCase($row['parameter']);
                $jsparam = 'siteParams["'.$param.'"] = '.$value.';';
                $site_params_js = $site_params_js.$jsparam;
                break;
            case "string":
                $value = $row['value'];
                $param = snakeToCamelCase($row['parameter']);
                $jsparam = 'siteParams["'.$param.'"] = "'.$value.'";';
                $site_params_js = $site_params_js.$jsparam;
                break;
            default:
                break;
          }
      }

    }
    else {
      echo mysqli_error($params_conn);
    }
    mysqli_close($params_conn);
?>