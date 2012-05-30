<?php


    // get the HTML
    ob_start();
    //include(dirname(__FILE__).'');
    require_once 'arquivos/contrato01.php';
    $content = ob_get_clean();

    // convert in PDF
    require_once('../libs/html2pdf/html2pdf.class.php');
    try
    {
        $html2pdf = new HTML2PDF('P', 'A4', 'pt',true, 'ISO-8859-1');
        //$html2pdf = new HTML2PDF('P','A4','pt', true, 'ISO-8859-1', array(0, 0, 0, 0));
        //$html2pdf->setModeDebug();
        $html2pdf->setDefaultFont('Arial');
        $html2pdf->writeHTML($content, isset($_GET['vuehtml']));
        $html2pdf->Output('ContratoCliente.pdf');
    }
    catch(HTML2PDF_exception $e) {
        echo $e;
        exit;
    }
