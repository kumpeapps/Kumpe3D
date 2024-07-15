<?php
$nonce = bin2hex(openssl_random_pseudo_bytes(32));
include_once 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__, '../.env');
$dotenv->load();
$env = $_ENV['env'];
$base_url = $_SERVER['SERVER_NAME'];
$session_sql = "
    INSERT INTO `Web_3dprints`.`sessions`
        (`session_id`, `app`)
    VALUES
        ('" . session_id() . "', 'kumpe3d.com')
    on DUPLICATE KEY 
        UPDATE timestamp = now(), app= 'kumpe3d.com';
";
$params_conn = mysqli_connect(
    $_ENV['mysql_host'],
    $_ENV['mysql_user'],
    $_ENV['mysql_pass'],
    'Web_3dprints'
) or die("Couldn't connect to server.");
mysqli_query($params_conn, $session_sql);
$params_sql = "
        SELECT 
            parameter,
            value,
            type
        FROM
            Web_3dprints.site_parameters;
    ";

function snakeToCamelCase($string, $capitalizeFirstCharacter = false)
{
    $str = str_replace('_', '', ucwords($string, '_'));
    if (!$capitalizeFirstCharacter) {
        $str = lcfirst($str);
    }
    return $str;
}

$result = mysqli_query($params_conn, $params_sql);
$site_params = [];

if ($result) {
    while ($row = mysqli_fetch_array($result)) {
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
                $jsparam = 'siteParams["' . $param . '"] = JSON.parse(' . $value . ');';
                break;
            case "int":
                $value = $row['value'];
                $param = snakeToCamelCase($row['parameter']);
                $jsparam = 'siteParams["' . $param . '"] = ' . $value . ';';
                break;
            case "bool":
                $value = $row['value'];
                $param = snakeToCamelCase($row['parameter']);
                $jsparam = 'siteParams["' . $param . '"] = ' . $value . ';';
                break;
            case "string":
                $value = $row['value'];
                $param = snakeToCamelCase($row['parameter']);
                $jsparam = 'siteParams["' . $param . '"] = "' . $value . '";';
                break;
            default:
                break;
        }
    }

} else {
    echo mysqli_error($params_conn);
}
mysqli_close($params_conn);
if ($site_params['store_maitenance_mode'] && !isset($_COOKIE['maitenance_mode_override'])) {
    http_response_code(503);
    include ('./under-construction.php');
    die();
}
