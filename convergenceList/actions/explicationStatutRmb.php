<?php 
G::loadClass ( 'pmFunctions' );
G::LoadClass("case");


if (isset($_GET['APP_UID']) && $_GET['APP_UID'] != '') {

    $oCase = new Cases ();
    $Fields = $oCase->loadCase($_GET['APP_UID']);
    $libelStatut = 'SELECT TITLE FROM PMT_STATUT WHERE UID='.intval($Fields['APP_DATA']['STATUT']);
    $libelRes = executeQuery($libelStatut);
    
    switch ($Fields['APP_DATA']['STATUT']) {

        case '4' :
            $msgErr = convergence_getMsgErreurRmb($_GET['APP_UID']);
            $messageInfo = 'Le dossier est <b>'.strtolower($libelRes[1]['TITLE']).'</b> pour les raisons suivantes : <br/><br/>&nbsp;-&nbsp;';
            $messageInfo .= implode('<br/>&nbsp;-&nbsp;',$msgErr);
            break;

        case '3' :
            $msgIncomplet = convergence_getIncompletErreurRmb($_GET['APP_UID']);
            $messageInfo = 'Le dossier est <b>'.strtolower($libelRes[1]['TITLE']).'</b> car les éléments suivants sont manquants : <br/><br/>&nbsp;-&nbsp;';
            $messageInfo .= implode('<br/>&nbsp;-&nbsp;',$msgIncomplet);
            break;

        default :
            $messageInfo = 'Le dossier est en statut <b>'.strtolower($libelRes[1]['TITLE']).'</b>';
            break;
        
    }
    
    G::loadClass('configuration');	
    global $G_PUBLISH;
    $oHeadPublisher =& headPublisher::getSingleton();     
    $conf = new Configurations;
    $oHeadPublisher->assign('MESSAGEINFO', $messageInfo);
    $oHeadPublisher->assign('ADAPTIVEHEIGHT', $_GET['ADAPTIVEHEIGHT']);
    $oHeadPublisher->addExtJsScript('convergenceList/explicationStatut', true );    //adding a javascript file .js
    $oHeadPublisher->addContent('convergenceList/explicationStatut');
    G::RenderPage('publish', 'extJs');
    exit(0);
    
}
else {
    header ( "Content-Type: text/plain" );
    
    $array=array();
    $array = $_REQUEST['array'];
    $items = json_decode($array,true);

    $messageInfo = '';

    switch (strtolower($items[0]['STATUT'])) {

        case '4' :
            $msgErr = convergence_getMsgErreurRmb($items[0]['APP_UID']);
            $messageInfo = 'Le dossier est '.strtolower($items[0]['TITLE']).' pour les raisons suivantes : <br/><br/>&nbsp;-&nbsp;';
            $messageInfo .= implode('<br/>&nbsp;-&nbsp;',$msgErr);
            break;

        case '3' :
            $msgIncomplet = convergence_getIncompletErreurRmb($items[0]['APP_UID']);
            $messageInfo = 'Le dossier est '.strtolower($items[0]['TITLE']).' car les éléments suivants sont manquants : <br/><br/>&nbsp;-&nbsp;';
            $messageInfo .= implode('<br/>&nbsp;-&nbsp;',$msgIncomplet);
            break;
        default :
              $messageInfo = 'Le statut du dossier est "<b>'.$items[0]['TITLE'].'"</b>';
            break;

    }
    
    $paging = array ('success' => true, 'messageinfo' => $messageInfo, 'num_dossier' => $items[0]['NUM_DOSSIER']);
    echo G::json_encode ( $paging );
}


?>