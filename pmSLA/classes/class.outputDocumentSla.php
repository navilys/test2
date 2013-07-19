<?php
require_once 'classes/model/OutputDocument.php';

class OutputDocumentSla extends OutputDocument
{
    public function generatePdf($sContent, $sPath, $sFilename, $aProperties)
    {
        $nrt     = array("\n",    "\r",    "\t");
        $nrthtml = array("(n /)", "(r /)", "(t /)");
        $sLandscape = false;

        $sContent = G::unhtmlentities($sContent);

        $strContentAux = str_replace($nrt, $nrthtml, $sContent);

        $iOcurrences = preg_match_all('/\@(?:([\>])([a-zA-Z\_]\w*)|([a-zA-Z\_][\w\-\>\:]*)\(((?:[^\\\\\)]*(?:[\\\\][\w\W])?)*)\))((?:\s*\[[\'"]?\w+[\'"]?\])+)?/',
            $strContentAux,
            $arrayMatch1,
            PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE);

        if ($iOcurrences) {
            $arrayGrid = array();

            for ($i = 0; $i <= $iOcurrences - 1; $i++) {
                $arrayGrid[] = $arrayMatch1[2][$i][0];
            }

            $arrayGrid = array_unique($arrayGrid);

            foreach ($arrayGrid as $index => $value) {
                $grdName = $value;

                $strContentAux1 = $strContentAux;
                $strContentAux  = null;

                $ereg = "/^(.*)@>" . $grdName . "(.*)@<" . $grdName . "(.*)$/";

                while (preg_match($ereg, $strContentAux1, $arrayMatch2)) {
                    $strData = null;

                    if (isset($aFields[$grdName]) && is_array($aFields[$grdName])) {
                        foreach ($aFields[$grdName] as $aRow) {
                            foreach ($aRow as $sKey => $vValue) {
                                if (!is_array($vValue)) {
                                    $aRow[$sKey] = nl2br($aRow[$sKey]);
                                }
                            }

                            $strData = $strData . G::replaceDataField($arrayMatch2[2], $aRow);
                        }
                    }

                    $strContentAux1 = $arrayMatch2[1];
                    $strContentAux  = $strData . $arrayMatch2[3] . $strContentAux;
                }

                $strContentAux = $strContentAux1 . $strContentAux;
            }
        }

        $strContentAux = str_replace($nrthtml, $nrt, $strContentAux);
        $sContent = $strContentAux;

        G::verifyPath($sPath, true);

        /* Start - Create .pdf */
        $oFile = fopen($sPath . $sFilename . '.html', 'wb');
        fwrite($oFile, $sContent);
        fclose($oFile);

        //define("MAX_FREE_FRACTION", 1);
        define('PATH_OUTPUT_FILE_DIRECTORY', PATH_HTML . 'files/outdocs/');
        G::verifyPath(PATH_OUTPUT_FILE_DIRECTORY, true);
        require_once (PATH_THIRDPARTY . 'html2ps_pdf/config.inc.php');
        require_once (PATH_THIRDPARTY . 'html2ps_pdf/pipeline.factory.class.php');

        parse_config_file(PATH_THIRDPARTY . 'html2ps_pdf/html2ps.config');

        $GLOBALS['g_config'] = array(
            'cssmedia'                => 'screen',
            'media'                   => 'Letter',
            'scalepoints'             => false,
            'renderimages'            => true,
            'renderfields'            => true,
            'renderforms'             => false,
            'pslevel'                 => 3,
            'renderlinks'             => true,
            'pagewidth'               => 800,
            'landscape'               => $sLandscape,
            'method'                  => 'fpdf',
            'margins'                 => array('left' => 15, 'right' => 15, 'top' => 15, 'bottom' => 15,),
            'encoding'                => '',
            'ps2pdf'                  => false,
            'compress'                => false,
            'output'                  => 2,
            'pdfversion'              => '1.3',
            'transparency_workaround' => false,
            'imagequality_workaround' => false,
            'draw_page_border'        => isset($_REQUEST['pageborder']),
            'debugbox'                => false,
            'html2xhtml'              => true,
            'mode'                    => 'html',
            'smartpagebreak'          => true
        );

        $GLOBALS['g_config']= array_merge($GLOBALS['g_config'],$aProperties);
        $g_media = Media::predefined($GLOBALS['g_config']['media']);
        $g_media->set_landscape($GLOBALS['g_config']['landscape']);
        $g_media->set_margins($GLOBALS['g_config']['margins']);
        $g_media->set_pixels($GLOBALS['g_config']['pagewidth']);


        if (isset($GLOBALS['g_config']['pdfSecurity'])) {
            if (isset($GLOBALS['g_config']['pdfSecurity']['openPassword']) &&
                $GLOBALS['g_config']['pdfSecurity']['openPassword'] != ""
            ) {
                $GLOBALS['g_config']['pdfSecurity']['openPassword'] = G::decrypt(
                    $GLOBALS['g_config']['pdfSecurity']['openPassword'],
                    $sUID
                );
            }

            if (isset($GLOBALS['g_config']['pdfSecurity']['ownerPassword']) &&
                $GLOBALS['g_config']['pdfSecurity']['ownerPassword'] != ""
            ) {
                $GLOBALS['g_config']['pdfSecurity']['ownerPassword'] = G::decrypt(
                    $GLOBALS['g_config']['pdfSecurity']['ownerPassword'],
                    $sUID
                );
            }

            $g_media->set_security($GLOBALS['g_config']['pdfSecurity']);

            require_once (HTML2PS_DIR . 'pdf.fpdf.encryption.php');
        }

        $pipeline = new Pipeline();

        if (extension_loaded('curl')) {
            require_once (HTML2PS_DIR . 'fetcher.url.curl.class.php');

            $pipeline->fetchers = array(new FetcherURLCurl());

            if (isset($proxy)) {
                if ($proxy != '') {
                    $pipeline->fetchers[0]->set_proxy($proxy);
                }
            }
        } else {
            require_once (HTML2PS_DIR . 'fetcher.url.class.php');
            $pipeline->fetchers[] = new FetcherURL();
        }

        $pipeline->data_filters[] = new DataFilterDoctype();
        $pipeline->data_filters[] = new DataFilterUTF8($GLOBALS['g_config']['encoding']);

        if ($GLOBALS['g_config']['html2xhtml']) {
            $pipeline->data_filters[] = new DataFilterHTML2XHTML();
        } else {
            $pipeline->data_filters[] = new DataFilterXHTML2XHTML();
        }

        $pipeline->parser = new ParserXHTML();
        $pipeline->pre_tree_filters = array();
        $header_html = '';
        $footer_html = '';
        $filter      = new PreTreeFilterHeaderFooter($header_html, $footer_html);
        $pipeline->pre_tree_filters[] = $filter;

        if ($GLOBALS['g_config']['renderfields']) {
            $pipeline->pre_tree_filters[] = new PreTreeFilterHTML2PSFields();
        }

        if ($GLOBALS['g_config']['method'] === 'ps') {
            $pipeline->layout_engine = new LayoutEnginePS();
        } else {
            $pipeline->layout_engine = new LayoutEngineDefault();
        }

        $pipeline->post_tree_filters = array();

        if ($GLOBALS['g_config']['pslevel'] == 3) {
            $image_encoder = new PSL3ImageEncoderStream();
        } else {
            $image_encoder = new PSL2ImageEncoderStream();
        }

        switch ($GLOBALS['g_config']['method']) {
            case 'fastps':
                if ($GLOBALS['g_config']['pslevel'] == 3) {
                    $pipeline->output_driver = new OutputDriverFastPS($image_encoder);
                } else {
                    $pipeline->output_driver = new OutputDriverFastPSLevel2($image_encoder);
                }
                break;
            case 'pdflib':
                $pipeline->output_driver = new OutputDriverPDFLIB16($GLOBALS['g_config']['pdfversion']);
                break;
            case 'fpdf':
                  $pipeline->output_driver = new OutputDriverFPDF();
                break;
            case 'png':
                  $pipeline->output_driver = new OutputDriverPNG();
                break;
            case 'pcl':
                  $pipeline->output_driver = new OutputDriverPCL();
                break;
            default:
                  die('Unknown output method');
        }

        if (isset($GLOBALS['g_config']['watermarkhtml'])) {
            $watermark_text = $GLOBALS['g_config']['watermarkhtml'];
        } else {
            $watermark_text = '';
        }

        $pipeline->output_driver->set_watermark($watermark_text);

        if ($watermark_text != '') {
            $dispatcher =& $pipeline->getDispatcher();
        }

        if ($GLOBALS['g_config']['debugbox']) {
            $pipeline->output_driver->set_debug_boxes(true);
        }

        if ($GLOBALS['g_config']['draw_page_border']) {
            $pipeline->output_driver->set_show_page_border(true);
        }

        if ($GLOBALS['g_config']['ps2pdf']) {
            $pipeline->output_filters[] = new OutputFilterPS2PDF($GLOBALS['g_config']['pdfversion']);
        }

        if ($GLOBALS['g_config']['compress'] && $GLOBALS['g_config']['method'] == 'fastps') {
            $pipeline->output_filters[] = new OutputFilterGZip();
        }

        if (!isset($GLOBALS['g_config']['process_mode'])) {
            $GLOBALS['g_config']['process_mode'] = '';
        }

        if ($GLOBALS['g_config']['process_mode'] == 'batch') {
            $filename = 'batch';
        } else {
            $filename = $sFilename;
        }

        switch ($GLOBALS['g_config']['output']) {
            case 0:
                $pipeline->destination = new DestinationBrowser($filename);
                break;
            case 1:
                $pipeline->destination = new DestinationDownload($filename);
                break;
            case 2:
                $pipeline->destination = new DestinationFile($filename);
                break;
        }

        copy($sPath . $sFilename . '.html', PATH_OUTPUT_FILE_DIRECTORY . $sFilename . '.html');
        $status = $pipeline->process(((isset($_SERVER['HTTPS']))&&($_SERVER['HTTPS']=='on') ? 'https://' : 'http://') .
            $_SERVER['HTTP_HOST'] . '/files/outdocs/' . $sFilename . '.html', $g_media);

        copy(PATH_OUTPUT_FILE_DIRECTORY . $sFilename . '.pdf', $sPath . $sFilename . '.pdf');
        unlink(PATH_OUTPUT_FILE_DIRECTORY . $sFilename . '.pdf');
        unlink(PATH_OUTPUT_FILE_DIRECTORY . $sFilename . '.html');
    }
}

