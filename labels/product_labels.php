<?php
$sku = 'test';

$url = 'https://www.preprod.kumpe3d.com/product_labels.php?sku=' . $sku;
passthru("wkhtmltopdf $url $sku.pdf");
?>