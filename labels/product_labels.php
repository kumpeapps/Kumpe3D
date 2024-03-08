<?php
use mikehaertl\wkhtmlto\Pdf;
include '../vendor/autoload.php';
$sku = 'test';
// header("Content-Type: application/octet-stream");
// header("Content-Disposition: attachment; filename=\"label.pdf\"");
$url = 'https://www.preprod.kumpe3d.com/product_labels.php?sku=' . $sku;
$pdf = new Pdf(array(
    'margin-top'    => 0,
    'margin-right'  => 0,
    'margin-bottom' => 0,
    'margin-left'   => 0,
    'page-height'   => "1.97in",
    'page-width'    => "3.15in",
    'print-media-type',
    'no-outline',
    'disable-smart-shrinking',
    'dpi'           => 400,
));

// Add a HTML file, a HTML string or a page from a URL
$pdf->addPage($url);

// ... or send to client for inline display
$pdf->send("label.pdf");

// ... or send to client as file download
// $pdf->send('test.pdf');
?>