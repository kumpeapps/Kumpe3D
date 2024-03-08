<?php
$sku = 'test';
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"label.pdf\"");
$url = 'https://www.preprod.kumpe3d.com/product_labels.php?sku=' . $sku;
passthru("wkhtmltopdf $url $sku.pdf",$result);
?>