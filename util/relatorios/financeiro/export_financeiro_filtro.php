<?php
/**
 * HTML2PDF Librairy - example
 *
 * HTML => PDF convertor
 * distributed under the LGPL License
 *
 * @author      Laurent MINGUET <webmaster@html2pdf.fr>
 *
 * isset($_GET['vuehtml']) is not mandatory
 * it allow to display the result in the HTML format
 */

    // get the HTML
    ob_start();
    include(dirname(__FILE__).'/arquivos/export_financeiro_filtro_arq.php');
    $content = ob_get_clean();

    // convert in PDF
    require_once('../../libs/html2pdf/html2pdf.class.php');
    try
    {
        $html2pdf = new HTML2PDF('P', 'A4', 'pt', false, 'ISO-8859-15');
//      $html2pdf->setModeDebug();
		
        $html2pdf->setDefaultFont('Arial');
        $html2pdf->writeHTML($content, isset($_GET['vuehtml']));
        $html2pdf->Output('Export Financeiro Filtro.pdf');
    }
    catch(HTML2PDF_exception $e) {
        echo $e;
        exit;
    }
