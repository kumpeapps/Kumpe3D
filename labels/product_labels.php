<?php
require_once 'dompdf/autoload.inc.php';
use Dompdf\Dompdf;

$sku = 'test';

$html = file_get_contents('https://www.preprod.kumpe3d.com/product_labels.php?sku=' . $sku);
$dompdf = new Dompdf();
$dompdf->loadHtml($html);

$dompdf->render();
$dompdf->stream();
?>