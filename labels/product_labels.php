<?php
use mikehaertl\wkhtmlto\Pdf;
include '../vendor/autoload.php';
$sku = 'ALO-POO-KSN-K17';
// header("Content-Type: application/octet-stream");
// header("Content-Disposition: attachment; filename=\"label.pdf\"");
$url = 'https://www.preprod.kumpe3d.com/product_labels.php?sku=' . $sku;
$pdf = new Pdf(array(
    'margin-top'    => 1,
    'margin-right'  => 1,
    'margin-bottom' => 0,
    'margin-left'   => 1,
    'page-height'   => 31.5,
    'page-width'    => 50,
    'print-media-type',
    'no-outline',
    'disable-smart-shrinking',
));

// Add a HTML file, a HTML string or a page from a URL
$pdf->addPage($url);

// ... or send to client for inline display
$pdf->send("label.pdf");

// ... or send to client as file download
// $pdf->send('test.pdf');
?>