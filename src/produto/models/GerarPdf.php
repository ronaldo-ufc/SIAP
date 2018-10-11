<?php
    use Dompdf\Dompdf;
    
    // include autoloader
    require_once 'dompdf/autoload.inc.php';
    
    $dompdf = new DOMPDF();
    $dompdf->load_html('<h1 style="text-align: center;">SIAP - Sistema de Almoxarifado e Patrimônio</h1>');
    
    $dompdf->render();
    $dompdf->stream("Relatório.pdf", array("Attachment" => FALSE));
