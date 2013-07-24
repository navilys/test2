<?php
/**
 * class.obladyConvergence.pmFunctions.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.
 * *
 */

////////////////////////////////////////////////////
// obladyConvergence PM Functions
//
// Copyright (C) 2007 COLOSA
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////


//GLOBAL : recupere le role pour l'affichage des champs des formulaires
function convergence_getUserRole($usr_uid){
    $query = "SELECT USR_ROLE FROM USERS WHERE USR_UID='".$usr_uid."'";
    $result = executeQuery($query);
    $role='';
    if(isset($result))
        $role = $result[1]['USR_ROLE'];
    return $role;
   
}



//GLOBAL : mise a jour du statut et du coup ajout d'une ligne dans les logs.
function convergence_changeStatut($app_uid, $statut, $labelLog = '') {
    
    try {
        $oCase = new Cases ();
        $Fields = $oCase->loadCase ($app_uid);
        $oldStatut = $Fields['APP_DATA']['STATUT'];
        $Fields['APP_DATA']['STATUT'] = $statut;
        
        $oCase->updateCase($app_uid, $Fields);
        if ($labelLog == '') {
            $libelStatut = 'SELECT TITLE FROM PMT_STATUT WHERE UID='.intval($statut);
            $libelRes = executeQuery($libelStatut);
            insertHistoryLogPlugin($app_uid,$_SESSION['USER_LOGGED'],date('Y-m-d H:i:s'),'0','',$libelRes[1]['TITLE'],$statut); 
        }
        else
            insertHistoryLogPlugin($app_uid,$_SESSION['USER_LOGGED'],date('Y-m-d H:i:s'),'0','',$labelLog,$statut); 
            

    }
    catch (Exception $e) {
        var_dump($e);
        die();
    }
    
}

//GLOBAL : mise a jour du statut sans ajout d'une ligne dans les logs.
function convergence_changeStatutWithoutHistory($app_uid, $statut) {
    
    try {
        $oCase = new Cases ();
        $Fields = $oCase->loadCase ($app_uid);
        $Fields['APP_DATA']['STATUT'] = $statut;
        
        $test_result = PMFSendVariables($app_uid, $Fields['APP_DATA']);
        $oCase->updateCase($app_uid, $Fields);

       
    }
    catch (Exception $e) {
        var_dump($e);
        die();
    }
    
}

//GLOBAL : ajouter une ligne dans le compteur des PND  // appeler quand on classe PND
function convergence_insertCompteurPND($dossier) {
    
    $insert = "INSERT INTO PMT_CPTPND (DOSSIER) VALUES ('$dossier')";                
    $resultInsData=executeQuery($insert);
    
}

//GLOBAL : supprimer une ligne dans le compteur des PND  // appeler quand on enleve des PND
function convergence_deleteCompteurPND($dossier) {
    
    $delete = "DELETE FROM PMT_CPTPND WHERE DOSSIER = '$dossier'";                
    $resultInsData=executeQuery($delete);
    
}


//GLOBAL : mise a jour de donnee de demande
function convergence_updateDemande($app_uid, $data) {
    
    $set = '';
    try {
        if (is_array($data) && count($data)>0) {
            $oCase = new Cases ();
            $Fields = $oCase->loadCase ($app_uid);
            $Fields['APP_DATA'] = array_merge($Fields['APP_DATA'],$data);
            
            PMFSendVariables($app_uid, $Fields['APP_DATA']);
            $oCase->updateCase($app_uid, $Fields);
        }
    }
    catch (Exception $e) {
        var_dump($e);
        die();
    }
}

//GLOBAL : Insertio ndes données dans l'historique

function  insertHistoryLogPlugin($APP_UID,$USR_UID,$CURRENTDATETIME,$VERSION,$NEWAPP_UID,$ACTION,$STATUT=""){
    
    $selectVersion = "SELECT MAX(HLOG_VERSION) AS VERSION FROM PMT_HISTORY_LOG WHERE HLOG_APP_UID = '". $APP_UID ."'";
    $qSelectVersion = executeQuery($selectVersion);
    $versionHistory = 0;
    if(sizeof($qSelectVersion))
        $versionHistory = $qSelectVersion[1]['VERSION'];
    
    $versionHistory = $versionHistory + 1;
        
    $ACTION = addslashes($ACTION);
    
    $Insertdata="INSERT INTO PMT_HISTORY_LOG (
                          HLOG_UID ,
                          HLOG_APP_UID ,
                          HLOG_USER_UID ,
                          HLOG_DATECREATED ,
                          HLOG_VERSION ,                          
                          HLOG_CHILD_APP_UID,
                          HLOG_ACTION,
                          HLOG_STATUS
                        )
                        VALUES (
                        NULL , '$APP_UID', '$USR_UID', '$CURRENTDATETIME', '$versionHistory','$NEWAPP_UID','$ACTION','$STATUT'
                        ); 
              ";                
    $resultInsData=executeQuery($Insertdata);
    
    if ($NEWAPP_UID != '' || $NEWAPP_UID != 0) {
        $data = array();
        $data['STATUT'] = '0';
        convergence_updateDemande($APP_UID,$data);
    }
}

//GLOBAL : update history apres edition pour avoir le nouveau statut
function updateHistoryLogStatus($app_uid,$statut) {
    
    $query = 'UPDATE PMT_HISTORY_LOG SET HLOG_STATUS = "'.$statut.'" WHERE HLOG_CHILD_APP_UID="'.$app_uid.'"';
    $result = executeQuery($query);
    if(sizeof($result)){
        return 0;
    }
    else
        return 1;
}

function FredirectTypo3($APP_UID) {
    
    $caseInstance = new Cases();
    $caseFields = $caseInstance->loadCase( $APP_UID );
    $DATA = $caseFields['APP_DATA'];

    if(isset($DATA['FLAGTYPO3']) && $DATA['FLAGTYPO3'] == 'On' && !$DATA['FLAG_ACTION']) {
        if (!$DATA['FLAG_REDIRECT_PAGE']) $page='http://172.17.20.29:8083/index.php?id=76'; else $page = $DATA['FLAG_REDIRECT_PAGE'];
        $hostPort = 'http://'.$_SERVER['HTTP_HOST'].':8081';
        echo "<script language='javascript'> parent.parent.location.href = '".$page."';</script>";
        die();
    }
    else {
        if(isset($DATA['FLAG_ACTION'])){
            
            if($DATA['FLAG_ACTION'] == 'actionCreateCase'){

                $query = "SELECT ID_INBOX FROM PMT_STATUT WHERE UID = '".$DATA['STATUT']."'";
                $result = executeQuery($query);
                if(count($result))
                    $inbox = isset($result[1]['ID_INBOX'])?$result[1]['ID_INBOX']:"";
                
                if($inbox)
                {
                    $node = 'NEW_OPTION_'.$inbox;
                    echo "<html><head>
                    <script language='javascript'> 
                    parent.location.href = 'http://".$_SERVER['HTTP_HOST']."/sys".$DATA['SYS_SYS']."/".$DATA['SYS_LANG']."/".$DATA['SYS_SKIN']."/convergenceList/inboxDinamic.php?idInbox=".$inbox."';
                    var treepanel = parent.parent.Ext.getCmp('tree-panel');
                    var node = treepanel.getNodeById('".$node."');
                    node.select();              
                    </script></head></html>";
                    die(); 
                }else{
                    echo "<script language='javascript'>
                    parent.Ext.getCmp('gridNewTab').store.reload();
                    parent.Ext.getCmp('win2').hide();
                    </script>";     
                    die();
                }  
            }
            if($DATA['FLAG_ACTION'] == 'editForms'){
                $DYN_UID = $DATA['DYN_UID'];
                $CURRENTDATETIME = $DATA['CURRENTDATETIME'];
                $APP_UID = $DATA['APPLICATION'];
                $PRO_UID = $DATA['PROCESS'];
                $_SESSION['USER_LOGGED'] = $DATA['FLG_INITUSERUID'];
                $_SESSION['USR_USERNAME'] = $DATA['FLG_INITUSERNAME'];
                $url = '../convergenceList/casesHistoryDynaformPage_Ajax.php?ACTIONTYPE=edit&actionAjax=historyDynaformGridPreview&DYN_UID='.$DYN_UID.'&APP_UID='.$APP_UID.'&PRO_UID='.$PRO_UID.'&CURRENTDATETIME='.$CURRENTDATETIME;
                echo "<script language='javascript'> location.href = '".$url."';</script>";
                die();
            }
            if($DATA['FLAG_ACTION'] == 'actionAjaxRestartCases'){
                $_SESSION['USER_LOGGED'] = $DATA['FLG_INITUSERUID'];
                $_SESSION['USR_USERNAME'] = $DATA['FLG_INITUSERNAME'];                
                header ( "Content-Type: text/plain" );
                $paging = array ('success' => true, 'messageinfo' => 'Operation Completed');
                echo G::json_encode ( $paging );
                die();                
            }
            if($DATA['FLAG_ACTION'] == 'actionAjax'){
                if(isset($DATA['FLG_INITUSERUID_DOUBLON']) && isset($DATA['FLG_INITUSERNAME_DOUBLON'])){
                                    $_SESSION['USER_LOGGED'] = $DATA['FLG_INITUSERUID_DOUBLON'];
                                    $_SESSION['USR_USERNAME'] = $DATA['FLG_INITUSERNAME_DOUBLON'];                  
                                }                 
                header ( "Content-Type: text/plain" );
                $paging = array ('success' => true, 'messageinfo' => 'Operation Completed');
                echo G::json_encode ( $paging );
                die();                
            }         
        }
        else {
            header("Location:http://".$_SERVER['HTTP_HOST']."/sys".$DATA['SYS_SYS']."/".$DATA['SYS_LANG']."/".$DATA['SYS_SKIN']."/cases/casesListExtJsRedirector.php");
            die();      
        }   
    }
}
### unsetSessionVars ($words, $var) -req
function unsetSessionVars($words='FLAG'){
    //$words = 'FLAG|FLAG_ACTION';
    $aVarSession = array();
    foreach ($_SESSION as $key => $value) {
        $aVarSession [] = $key;
    }
    $list_words = explode( '|', $words );
    $aDeleteVarSession = array();
    foreach ( $aVarSession as $key => $text ){
        foreach ( $list_words as $sCad ){
            $patron = '#^'.$sCad.'.*#s';
            if(preg_match($patron, trim($text)))
                $aDeleteVarSession[]= $aVarSession[$key];
        }
    }
    $aDeleteVarSession=array_unique($aDeleteVarSession);
    foreach ($aDeleteVarSession as $key => $value) {
        unset($_SESSION[$value]);
    }
}
function unsetCasesFlag($words='FLAG', $APP_DATA){
    //$words = 'FLAG|FLAG_ACTION';
    $aVarSession = array();
    foreach ($APP_DATA as $key => $value) {
        $aVarSession [] = $key;
    }
    $list_words = explode( '|', $words );
    $aDeleteVarSession = array();
    foreach ( $aVarSession as $key => $text ){
        foreach ( $list_words as $sCad ){
            $patron = '#^'.$sCad.'.*#s';
            if(preg_match($patron, trim($text)))
                $aDeleteVarSession[]= $aVarSession[$key];
        }
    }
    $aDeleteVarSession=array_unique($aDeleteVarSession);
    foreach ($aDeleteVarSession as $key => $value) {
        if($value != 'FLAG_NON_DOUBLON')
            unset($APP_DATA[$value]);
    }
    return  $APP_DATA;
}

//GLOBAL : fonction pour creer un fe_user Typo3 a la confirmation de creation de compte dans PM
function  userSettingsPlugin($groupId,$urlTypo3 = 'http://172.17.20.29:8081/'){
    $res = "";
    if (isset($_GET['ER_REQ_UID'])) {

    //set_include_path(PATH_PLUGINS . 'externalRegistration' . PATH_SEPARATOR . get_include_path());
    require_once PATH_PLUGINS.'externalRegistration/classes/model/ErConfiguration.php';
    require_once PATH_PLUGINS.'externalRegistration/classes/model/ErRequests.php';
    require_once PATH_PLUGINS.'externalRegistration/classes/class.ExternalRegistrationUtils.php';

    $erReqUid = G::decrypt($_GET['ER_REQ_UID'], URL_KEY);
    // Load request
     $erRequestsInstance = new ErRequests();
     $request = $erRequestsInstance->load($erReqUid);
      
    $data = $request['ER_REQ_DATA'];  
    ini_set("soap.wsdl_cache_enabled", "0");
    $hostTypo3 = $urlTypo3.'typo3conf/ext/pm_webservices/serveur.php?wsdl';
    $pfServer = new SoapClient($hostTypo3);
    $key = rand();
    $ret = $pfServer->createAccount(array(
        'username' => $data['__USR_USERNAME__'],
        'password' => md5($data['__PASSWORD__']),
        'email' => $data['__USR_EMAIL__'],
        'lastname' => $data['__USR_LASTNAME__'],
        'firstname' => $data['__USR_FIRSTNAME__'],
        'key' => $key,
        'pmid' => $data['USR_UID'],
        'usergroup' => $groupId,
        'cHash' => md5($data['__USR_USERNAME__'].'*'.$data['__USR_LASTNAME__'].'*'.$data['__USR_FIRSTNAME__'].'*'.$key)));

    // Get the group name
    $query = "SELECT CON_VALUE FROM CONTENT WHERE CON_ID = '$groupId' AND CON_CATEGORY='GRP_TITLE' ";
    $result = executeQuery($query);
    $roleName='';
    if(isset($result))
        $roleName = $result[1]['CON_VALUE'];
    // End Get the group name
    
    // Change the role    
    if($roleName != ''){
        $updateRole = "UPDATE USERS SET USR_ROLE ='$roleName' WHERE USR_UID='".$data['USR_UID']."'";
        $updateRQuery = executeQuery($updateRole);    
    }    
    // End Change the role

    }
    
    return $res;
}

//GLOBAL : Recupère toutes les données des chanmps dans le case passé en paramètre
function convergence_getAllAppData($app_id,$upper = 0) {
    
    G::LoadClass('case');
    $oCase = new Cases();
    $fields = $oCase->loadCase( $app_id );
    $fields['APP_DATA']['APP_NUMBER'] = $fields['APP_NUMBER'];
    
    if ($upper == 1) {
        foreach ($fields['APP_DATA'] as $k => $v) {
            $fields['APP_DATA'][strtoupper($k)] = $v;
        }
    }
    
    return $fields['APP_DATA'];
    
}

//Global permet de récupérer le CODE_OPER en fonction du code du chéquier
function convergence_getCodeOperation($code) {
  $query ='SELECT NUM_OPER FROM PMT_LISTE_OPER, PMT_TYPE_CHEQUIER WHERE PMT_LISTE_OPER.CODE_OPER = PMT_TYPE_CHEQUIER.CODE_OPER AND PMT_TYPE_CHEQUIER.CODE_CD = '.$code;
  $result = executeQuery($query);
    if(isset($result)) {
        return $result[1]['NUM_OPER'];
    }
    else
        return 0;
}

/*
 * GLOBALS
 * Fonction qui renvoi la possibilité que le cas soit un doublon avec un autre.
 * 
 */
function make_dedoublonage($process,$app_id,$debug = 0) {
    
    //recuperation des variable du formulaire
    $fields = convergence_getAllAppData($app_id);

    $doublon = 0;
    
    if($fields['FLAG_NON_DOUBLON'] == 1)
         return $doublon;
    
    $where = $whereLev = $whereSound = 'STATUT !=0 AND STATUT !=999 AND NUM_DOSSIER !="'.$fields['NUM_DOSSIER'].'"';
    
    $getTableName = 'SELECT * FROM PMT_CONFIG_DEDOUBLONAGE WHERE CD_PROCESS_UID="'.$process.'"';
    $table = executeQuery($getTableName);
    if(isset($table) && count($table) > 0) {
        $uid_config = $table[1]['CD_UID'];
        $table = $table[1]['CD_TABLENAME'];
    }

    $getFieldsQuery = 'SELECT * FROM PMT_COLUMN_DEDOUBLONAGE WHERE CD_INCLUDE_OPTION = 1 AND CD_UID_CONFIG_AS='.$uid_config;
    $config = executeQuery($getFieldsQuery);
    if(isset($config) && count($config) > 0 && $table != '') {
        
        foreach ($config as $data) {
        
            $select_debug .= ',"'.$fields[$data['CD_FIELDNAME']].'" AS reference,'.strtoupper($data['CD_FIELDNAME']).',levenshtein_ratio("'.metaphone($fields[$data['CD_FIELDNAME']]).'",dm('.strtoupper($data['CD_FIELDNAME']).')),levenshtein_ratio("'.$fields[$data['CD_FIELDNAME']].'",'.strtoupper($data['CD_FIELDNAME']).')';
            $where .= ' AND ('.strtoupper($data['CD_FIELDNAME']).' = "'.$fields[$data['CD_FIELDNAME']].'" OR levenshtein_ratio("'.metaphone($fields[$data['CD_FIELDNAME']]).'",dm('.strtoupper($data['CD_FIELDNAME']).')) >= '.$data['CD_RATIO'].' OR levenshtein_ratio("'.$fields[$data['CD_FIELDNAME']].'",'.strtoupper($data['CD_FIELDNAME']).') >= '.$data['CD_RATIO'].')';
            
           // $whereLev .= ' AND levenshtein_ratio("'.metaphone($fields[$data['CD_FIELDNAME']]).'",dm('.strtoupper($data['CD_FIELDNAME']).')) >= '.$data['CD_RATIO'];
            
            //SOUNDEX censé etre moi perfofmant que le metaphone du dessus.
            //$whereSound .= ' AND SOUNDEX("'.$fields[$data['CD_FIELDNAME']].'") = SOUNDEX('.strtoupper($data['CD_FIELDNAME']).')';
            
        }
        
        $requete = 'SELECT count(*) as NB FROM '.$table.' WHERE '.$where;
        $result = executeQuery($requete);

        //$requeteLev = 'SELECT count(*) as NB FROM '.$table.' WHERE '.$whereLev;
        //$resultLev = executeQuery($requeteLev);
    
        //$requeteSound = 'SELECT * FROM '.$table.' WHERE '.$whereSound;
        //$resultSound = executeQuery($requeteSound);
        
        if ($debug != 0) {
            
            $requeteDebug = 'SELECT * FROM '.$table.' WHERE '.$where;
            $resultDebug = executeQuery($requeteDebug);

            //$requeteLevDebug = 'SELECT * FROM '.$table.' WHERE '.$whereLev;
           // $resultLevDebug = executeQuery($requeteLevDebug);
            
            G::pr($select_debug);
            G::pr($requeteDebug);
            G::pr($resultDebug);

            /*G::pr('AVEC LEVENSHTEIN');
            G::pr($requeteLevDebug);
            G::pr($resultLevDebug);*/
        }
        
        if(isset($result) && $result[1]['NB'] > 0 || isset($resultLev) && $resultLev[1]['NB'] > 0) {
                $doublon = 1;
        }
        
    }
    
    if ($debug != 0) {
        G::pr('Doublon : '.$doublon);
    }
    return $doublon;
}

//GLobaL : return all the app_uid for the doublon
function getAllDoublon($process,$app_id) {
    
    //recuperation des variable du formulaire
    $fields = convergence_getAllAppData($app_id);

    $where = 'STATUT !=0 AND STATUT !=999 AND NUM_DOSSIER !="'.$fields['NUM_DOSSIER'].'" AND ( FLAG_NON_DOUBLON IS NULL OR ( FLAG_NON_DOUBLON =1 AND "'.$fields['NUM_DOSSIER'].'" < APP_NUMBER))';
    
    
    $getTableName = 'SELECT * FROM PMT_CONFIG_DEDOUBLONAGE WHERE CD_PROCESS_UID="'.$process.'"';
    $table = executeQuery($getTableName);
    if(isset($table) && count($table) > 0) {
        $uid_config = $table[1]['CD_UID'];
        $table = $table[1]['CD_TABLENAME'];
    }

    $getFieldsQuery = 'SELECT * FROM PMT_COLUMN_DEDOUBLONAGE WHERE CD_INCLUDE_OPTION = 1 AND CD_UID_CONFIG_AS='.$uid_config;
    $config = executeQuery($getFieldsQuery);
    if(isset($config) && count($config) > 0 && $table != '') {
        
        foreach ($config as $data) {
        
           $where .= ' AND ('.strtoupper($data['CD_FIELDNAME']).' = "'.$fields[$data['CD_FIELDNAME']].'" OR levenshtein_ratio("'.metaphone($fields[$data['CD_FIELDNAME']]).'",dm('.strtoupper($data['CD_FIELDNAME']).')) >= '.$data['CD_RATIO'].' OR levenshtein_ratio("'.$fields[$data['CD_FIELDNAME']].'",'.strtoupper($data['CD_FIELDNAME']).') >= '.$data['CD_RATIO'].')';
            
        }
        
        $requete = 'SELECT * FROM '.$table.' WHERE '.$where;
        $result = executeQuery($requete);

        return $result;

    }
   
    return '';
}

//GLOBAL
function convergence_getFrenchDate()
{
    return date('d/m/Y');
}
//GLOBAL
function convergence_getAS400Date()
{
    return G::CurDate('d.m.Y');
}

//GLOBAL
function convergence_getOutputDocument($app_id,$doc_id) {
    
    $aAttachFiles = array();
    
    $outDocQuery = 'SELECT AD.APP_DOC_UID, AD.DOC_VERSION, C.CON_VALUE AS FILENAME 
    FROM APP_DOCUMENT AD, CONTENT C 
    WHERE AD.APP_UID="'.$app_id.'" AND AD.DOC_UID="'.$doc_id.'" AND 
    AD.APP_DOC_STATUS="ACTIVE" AND AD.DOC_VERSION = (
    SELECT MAX(DOC_VERSION) FROM APP_DOCUMENT WHERE APP_UID="'.$app_id.'" AND 
    DOC_UID="'.$doc_id.'" AND APP_DOC_STATUS="ACTIVE")
    AND AD.APP_DOC_UID = C.CON_ID AND C.CON_CATEGORY = "APP_DOC_FILENAME"';
    
    $outDoc = executeQuery($outDocQuery);
    if (is_array($outDoc) and count($outDoc) > 0) {
        $path = PATH_DOCUMENT . $app_id . PATH_SEP . 'outdocs' . PATH_SEP . 
                $outDoc[1]['APP_DOC_UID'] . '_' . $outDoc[1]['DOC_VERSION'];
        $filename = $outDoc[1]['FILENAME'];
        $aAttachFiles[$filename . '.pdf'] = $path . '.pdf';
        $aAttachFiles[$filename . '.doc'] = $path . '.doc';
    }
    
    return $aAttachFiles;
    
}


//GLOBAL
function convergence_getNameUser($userID) {
    
    $user = userInfo($userID);
    return $user['firstname'].' '.$user['lastname'];   
}

//GLOBAL
function convergence_getNamePresta($prestaID) {
    
    $query = 'SELECT RAISONSOCIALE FROM PMT_PRESTATAIRE WHERE SIRET='.$prestaID;
    $result = executeQuery($query);
    if (is_array($result)) {
        return $result[1]['RAISONSOCIALE'];
    }
    else
        return '';
}

//GLOBAL
function convergence_getCPVille($villeID) {
  if($villeID != ''){  
    $query = 'SELECT ZIP,NAME FROM PMT_VILLE WHERE UID='.$villeID;
    $result = executeQuery($query);
    if (is_array($result)) {
        return $result[1]['ZIP'].' '.$result[1]['NAME'];
    }
    else
        return '';   
  }else
      return '';
}
//GLOBAL
function convergence_annuleCheque($chequeID) {
    
    $query = 'UPDATE PMT_CHEQUES SET ANNULE=1 WHERE UID='.$chequeID;
    $result = executeQuery($query);
    if (is_array($result)) {
        return '';
    }
    else
        return '';    
}
//GLOBAL
function convergence_annuleChequier($commandeID) {
    
    $query = 'UPDATE PMT_CHEQUES SET ANNULE=1 WHERE ID_DEMANDE='.$commandeID;
    $result = executeQuery($query);
    if (is_array($result)) {
        return '';
    }
    else
        return '';    
    
}    

//GLOBAL
function convergence_concatFiles($files) {
    
    //si plusieurs fichiers on les concatenent 
    if (is_array($files) && count($files)>1) {
       
        $i=0;
        $query = 'SELECT * FROM APP_DOCUMENT, CONTENT WHERE APP_UID IN ('.implode(',',$files).') AND APP_DOC_TYPE="OUTPUT" AND APP_DOC_STATUS="ACTIVE" AND APP_DOC_UID = CON_ID AND CON_CATEGORY = "APP_DOC_FILENAME" AND CON_LANG="fr"';    
        
        $result = executeQuery($query);
        
        foreach($result as $f) {
            $path = PATH_DOCUMENT . $f['APP_UID'] . PATH_SEP . 'outdocs' . PATH_SEP . $f['APP_DOC_UID'] . '_' . $f['DOC_VERSION'];
            $concatFile[$i++] = $path.'.pdf';
        }
         
    }
    //sinon on concatene tous les docs de ce dispositif
    else {        
        $i=0;
        $query = 'SELECT * FROM APP_DOCUMENT, CONTENT WHERE APP_UID IN ('.implode(',',$files).') AND APP_DOC_TYPE="OUTPUT" AND APP_DOC_STATUS="ACTIVE" AND APP_DOC_UID = CON_ID AND CON_CATEGORY = "APP_DOC_FILENAME" AND CON_LANG="fr"';    
        $result = executeQuery($query);
        
        foreach($result as $f) {
            $path = PATH_DOCUMENT . $f['APP_UID'] . PATH_SEP . 'outdocs' . PATH_SEP . $f['APP_DOC_UID'] . '_' . $f['DOC_VERSION'];
            $concatFile[$i++] = $path.'.pdf';
        }
 
    }
    
    $resultFile = '/tmp/temp_concat_'.time().'.pdf';
    exec('gs -q -dBATCH -dNOPAUSE -dSAFER -sDEVICE=pdfwrite -sOutputFile='.$resultFile.' -dBATCH '.implode(' ',$concatFile));

    $return = file_get_contents($resultFile);
    unlink($resultFile);

    return $return;
        
    
}

//GLOBAL
function convergence_exportToAS400($process_id, $file_base, $code, $liste = null, $makeRetourProdTxtForRecette = 0, $onlyThisCodeOper = 0) {
    if(!isset($process_id)){
        return 'Le process_uid n\'est pas renseigné pour l\'export de fichier';
    }
    $query_config = 'SELECT * FROM PMT_AS400_CONFIG WHERE PROCESS_UID ='."'$process_id'";
    $res_config = executeQuery($query_config);
    if(isset($res_config) && count($res_config) > 0){
        $config = $res_config[1];
        ($config['TOKEN_CSV'] == '') ? $token = ' ' : $token = $config['TOKEN_CSV'];
    }else{
        return 'Aucun process uid correcpondant dans la table AS400 Config !';
    }
    $query_fields = 'SELECT * FROM PMT_COLUMN_AS400 WHERE ID_CONFIG_AS = '. $config['ID'].' ORDER BY ORDER_FIELD';
    $select = executeQuery($query_fields);
 // a confirmer avec fred
    if(!is_null($liste)){
        // Modifier par Nico, voir avec fred si pas d'effet de bord dans ses process
        //$config['CONFIG_WHERE'] = ' AND NUM_DOSSIER IN ('.$liste.')';
        $value = "'".implode("','", $liste)."'";
        $config['CONFIG_WHERE'] = ' AND APP_UID IN('.$value.')';
    }    
    /*     * *****  récupération des different code opération pour le dispositif et génération d'un fichier de production par code ** */
    $whereOper = ' WHERE 1';
    // Si l'on ne souhaite lancer la production que sur un seul code opération
    if (intval($onlyThisCodeOper) != 0)
    {
        $whereOper .= ' AND NUM_OPER = ' . intval($onlyThisCodeOper);
    }
    $sqlOper = 'SELECT DISTINCT(NUM_OPER) FROM PMT_LISTE_OPER' . $whereOper;
    $resOper = executeQuery($sqlOper);
    $listOper = array();
    if(isset($resOper) && count($resOper) > 0){
         foreach($resOper as $operation){
             $listOper[] = intval($operation['NUM_OPER']);
         }        
     }else{
         G::pr('Aucun dispositif défini dans la table PMT_LISTE_OPER');die;
     }
    //G::pr($resOper);die;
    /********/    
    $app_uid = array();
    $nb_result = 0; 
    foreach($listOper as $codeOper){
        if (count($listOper) > 1 || $onlyThisCodeOper != 0)
            $whereCodeOper = ' AND CODE_OPERATION ='.$codeOper;            
        else
            $whereCodeOper = '';
        $dateFile = date("YmdHis");
        $file = $file_base.$codeOper.'_'.$dateFile.'.txt';
        
        $query = 'SELECT * FROM '.$config['TABLENAME'].' '.$config['JOIN_CONFIG'].' WHERE 1 '.$config['CONFIG_WHERE'].$whereCodeOper;   
        $result = executeQuery($query);
        
        $mode = 'w'; // enregistrer le fichier sous un format _date et le sauvegarder dans une table historique        
        
        $data = array();        
        if(isset($result) && count($result) > 0){
        $ftic = fopen($file, $mode);
            foreach ($result as $row) {
                $line = '';
                foreach ($select as $field) {
                    //if($field['FIELD_DESCRIPTION'] != '') $field['FIELD_NAME'] = $field['FIELD_DESCRIPTION'];
                    switch ($field['AS400_TYPE']) {
                        case 'Integer':
                        case 'Entier':
                            $line .= substr(str_pad($row[$field['FIELD_NAME']], $field['LENGTH'], 0, STR_PAD_LEFT), 0, $field['LENGTH']);
                            break;
                        case 'Date':
                            $char = array('-','/');
                            $line .= substr(str_pad(str_replace($char, '.', $row[$field['FIELD_NAME']]), $field['LENGTH'], $token), 0, $field['LENGTH']);
                            break;
                        case 'Decimal'://0000000.00
                            $char = array('.',',');
                            $count = count(explode('.', $row[$field['FIELD_NAME']]));
                            ($count > 1) ? $dec = $row[$field['FIELD_NAME']] : $dec = $row[$field['FIELD_NAME']].'00' ;
                            $line .= substr(str_pad(str_replace($char, '', $dec), $field['LENGTH'], 0, STR_PAD_LEFT), 0, $field['LENGTH']);
                            break;
                        case 'Telephone':
                            $char = array('-','.',' ');
                            $line .= substr(str_pad(str_replace($char, '', $row[$field['FIELD_NAME']]), $field['LENGTH'], $token), 0, $field['LENGTH']);
                            break;
                        case 'Yesno':
                            $yes = array('oui','yes', 'o', 1);
                            (in_array(strtolower($row[$field['FIELD_NAME']], $yes))) ?  $yesno ='O' : $yesno = 'N';
                            $line .= $yesno;
                            break;
                        case 'NCommande':
                            // numéro de commande à récupérer en amont et passer en paramètre
                            $line .= substr(str_pad($code, $field['LENGTH'], 0, STR_PAD_LEFT), 0, $field['LENGTH']);
                            break;
                        case 'codeOper':
                            // code opération
                            $line .= substr(str_pad($codeOper, $field['LENGTH'], 0, STR_PAD_LEFT), 0, $field['LENGTH']);
                            break;
                        // ajouter les autres cas possible, par defaut les champs sont comblés avec des espaces à droite
                        default:
                            $line .= substr(str_pad($row[$field['FIELD_NAME']], $field['LENGTH'], $token), 0, $field['LENGTH']);
                            break;
                    }
                }
                fwrite($ftic, $line."\n"); // voir si l'as400 supporte la dernière ligne vide
                $nb_result++;
                if(isset($row['APP_UID'])){
                    $app_uid[]= $row['APP_UID'];
                }
            }
            fclose($ftic);
        }  
    }    
     /***** autogenrate a sample file of return from as400 for test on debug mode******/
        if($makeRetourProdTxtForRecette == 1){
            $nameFile = '/var/tmp/autogenerateForRetourProd_'.$code.'.txt';
            $fret = fopen($nameFile,$mode);
            $in = "'".implode("','", $app_uid)."'";            
            $q= 'SELECT CODE_OPERATION, CODE_CHEQUIER, NUM_DOSSIER FROM PMT_DEMANDES WHERE APP_UID IN('.$in.')';            
            $r = executeQuery($q);
            $q1 = 'SELECT MAX(BCONSTANTE)+1 as TITRE FROM PMT_CHEQUES WHERE 1';
            $r1 = executeQuery($q1);
            $numChq = $r1[1]['TITRE'];
            foreach($r as $k => $v){
               // $chqSql = 'SELECT SUM(NB) AS S FROM PMT_CHEQUIER_MM_VN WHERE LOCAL_ID='.$v['CODE_CHEQUIER'];
                $chqSql = 'SELECT *, PMT_LISTE_TYPES.LABEL as LBTYPE FROM PMT_CHEQUIER_MM_VN LEFT JOIN PMT_LISTE_VN ON(PMT_CHEQUIER_MM_VN.FOREIGN_ID = PMT_LISTE_VN.CODE) LEFT JOIN PMT_LISTE_TYPES on(PMT_LISTE_VN.TYPE_TITRE = PMT_LISTE_TYPES.CODE) WHERE LOCAL_ID='.$v['CODE_CHEQUIER'];
                $chqRes = executeQuery($chqSql);                
                $numChqier = $numChq = str_pad($numChq,9, 0, STR_PAD_LEFT);
                
                foreach ($chqRes as $cheque) {                  
                    $nbInsert = 0;                
                    while ($nbInsert < $cheque['NB']){

                        $type = str_pad($cheque['LBTYPE'],3, 0, STR_PAD_LEFT); 
                        
                        $char = array('.',',');
                        $count = count(explode('.', $cheque['VALEUR_NOMINALE']));
                        ($count > 1) ? $dec = $cheque['VALEUR_NOMINALE'] : $dec = $cheque['VALEUR_NOMINALE'].'00' ;
                        $vn = substr(str_pad(str_replace($char, '', $dec), 7, 0, STR_PAD_LEFT), 0, 7);
                                                                                                                      
                        $numChq = str_pad($numChq,9, 0, STR_PAD_LEFT);
                        $codeoper = str_pad($v['CODE_OPERATION'],5, 0, STR_PAD_LEFT);
                        $numD = str_pad($v['NUM_DOSSIER'],12, 0, STR_PAD_LEFT);
                        $numP = str_pad($code,12, 0, STR_PAD_LEFT);
                        $line = $codeoper.$numD.$numP.$numChqier.$numChq.$type.$vn.'04.09.201204.12.2014100';
                        fwrite($fret, $line."\n");
                        $numChq++;
                        $nbInsert++;
                    }
                }
            }
            fclose($fret);
        }    
    /************************* end debug mode function **********************************/
    if(count($app_uid) > 0){             
        return $app_uid;
    }else{
        return $nb_result;
    }
}
/****** //GLOBAL
*  Met le statut En cours de production sur les demandes pour les export de production de chèques
*
*
*/
function convergence_updateAllStatutDemandes($app_uid,$statutTo) {
  if(is_array($app_uid)){
    foreach($app_uid as $uid){
    convergence_changeStatut($uid, $statutTo);
    }
  }
}
/****** //GLOBAL
* Supprime le falg à reproduire après le lancement en production de la demande
*
*
*/
function convergence_updateAllReproductionDemandes($app_uid,$flagTo) {
  if(is_array($app_uid)){
    $data['REPRODUCTION_CHQ'] = $flagTo;
    foreach($app_uid as $uid){
    convergence_updateDemande($uid,$data);
    }
  }
}
/****** //GLOBAL
*  Met le statut En cours de remboursement sur les demandes pour les export de remboursement
*
*
*/
function convergence_updateAllStatutRemboursement($app_uid,$statutTo) {
  if(is_array($app_uid)){
    foreach($app_uid as $uid){
    convergence_changeStatut($uid, $statutTo);
    }
  }
}

function convergence_getDossiers($res, $table, $export = true){  
    if($export == true){
        $in = "'".implode("','", $res)."'";
        $query ='SELECT NUM_DOSSIER FROM '.$table.' WHERE APP_UID IN('.$in.')';        
        $res = executeQuery($query);
    }
    $array = array();
    foreach($res as $value){
        $array[] = $value['NUM_DOSSIER'];
    }
    $unique = array_unique($array);
    $liste = implode(',', $unique);    
    return $liste;
}
function convergence_checkReproduction($line_import){
    $repro = 0;
   // G::pr('-------line_import in the function --------');G::pr($line_import);
    if(count($line_import)>0){
        try{
            $query = 'SELECT UID, REPRODUCTION FROM PMT_CHEQUES WHERE NUM_TITRE ="'.$line_import['NUM_TITRE'].'" AND (ANNULE <> 1 OR ANNULE IS NULL)';
            //G::pr('------debut pm-------');G::pr($line_import);
            //G::pr($query);//G::pr('------fin pm-------');
            $result = executeQuery($query);
            //G::pr($result);
            if (isset($result) && count($result) > 0) {
                if(is_null($result[1]['REPRODUCTION'])) $result[1]['REPRODUCTION'] = 0;
                $repro = $result[1]['REPRODUCTION'] + 1;
                //$update = 'UPDATE PMT_CHEQUES SET ANNULE = 1 WHERE UID = '.$result[1]['UID'];
                //$resUpdate = executeQuery($update);
                return $repro;
            }
            
        }
        catch (Exception $e) {
        var_dump($e);
        die();
    }    
    }
    return 0;
}


//GLOBAL
function convergence_importFromAS400($process_uid, $app_id = '', $childProc = 0) {     
    if ($app_id != '') {
    try{
        $query = 'SELECT C.CON_ID, C.CON_VALUE, AD.DOC_VERSION FROM APP_DOCUMENT AD, CONTENT C
            WHERE AD.APP_UID="' . $app_id . '" AND AD.APP_DOC_TYPE="INPUT" AND AD.APP_DOC_STATUS="ACTIVE"
           AND AD.APP_DOC_UID=C.CON_ID AND C.CON_CATEGORY="APP_DOC_FILENAME" AND C.CON_VALUE<>""';                
                $result = executeQuery($query);
        if (is_array($result) and count($result) > 0) {
            $filePath = PATH_DOCUMENT . $app_id . '/' . $result[1]['CON_ID'] . '_' . $result[1]['DOC_VERSION'] . '.' . pathinfo($result[1]['CON_VALUE'], PATHINFO_EXTENSION);
        }
        
    }
    catch (Exception $e) {
        G::pr('Erreur lors de la récupération du document');
        G::pr($e);
        die();
    }    

        if (!isset($process_uid)) {
             G::pr( 'Le process_uid n\'est pas renseigné pour configurer l\'import du fichier');die;
        }
        $query_config = 'SELECT * FROM PMT_AS400_CONFIG WHERE PROCESS_UID ="'.$process_uid.'"';

        $res_config = executeQuery($query_config);
        if (isset($res_config) && count($res_config) > 0) {
            $config = $res_config[1];
            ($config['TOKEN_CSV'] == '') ? $token = ' ' : $token = $config['TOKEN_CSV'];
            if ($config['TASK_UID'] == '' || is_null($config['TASK_UID'])) {
                G::pr('Le uid_process n\'est pas renseigné pour traiter les données importées');die();
            }
        } else {
             G::pr( 'Aucun process uid correcpondant dans la table AS400 Config !');die;
        }
        $query_fields = 'SELECT * FROM PMT_COLUMN_AS400 WHERE ID_CONFIG_AS = ' . $config['ID'] . ' ORDER BY ORDER_FIELD';

        $select = executeQuery($query_fields);
        $mode = 'r';
        $ftic = fopen($filePath, $mode);
        $data = array();

        while (($current_line = fgets($ftic)) !== false) {
            $importLine = array();
            foreach ($select as $field) {
                switch ($field['AS400_TYPE']) {
                    case 'Integer':
                    case 'Entier':
                        $importLine[$field['FIELD_NAME']] = intval(trim(substr($current_line, 0, $field['LENGTH']), $token));
                        break;
                    case 'Ignore':
                        //$forget = trim(substr($current_line, 0, $field['LENGTH']), $token);
                        break;
                    case 'Decimal':
                        $importLine[$field['FIELD_NAME']]  = floatval(substr_replace(substr($current_line, 0, $field['LENGTH']),'.',-2,0));                       
                        break;
                    case 'Telephone':
                        $stringTel = trim(substr($current_line, 0, $field['LENGTH']), $token);
                        $importLine[$field['FIELD_NAME']] = wordwrap($stringTel, 2, "-", 1);
                        break;
                    case 'Date':
                         $importLine[$field['FIELD_NAME']] = str_replace('.', '-', trim(substr($current_line, 0, $field['LENGTH']), $token));
                        break;
                    default:
                        $importLine[$field['FIELD_NAME']] = trim(substr($current_line, 0, $field['LENGTH']), $token);
                        break;
                }
                $current_line = substr($current_line, $field['LENGTH']);
            }
            $data[] = $importLine;

            //lancer le process ici $start_process_uid 
            // /!\ lancer un ou plusieur process enfant depuis un process parent doit toujours ce faire en dernier dans le workflow du process parent
            // $importLine contien un array assoc FIELD => VALUE
            //$data contien tous les importLine
            if($childProc == 1)
                new_case_for_import($importLine, $config);
        }
        // Added By Gary Meyer
        /*if($childProc == 1)
            $resInfo = PMFDerivateCase($app_id, 1,false, $_SESSION['USER_LOGGED']);*/
        return $data;
    } else {
        return;
        ////autre méthode
    }
}
/* * ***
 * Execution d'un nouveau process suite à un import de l'AS400
 *
 * $task    @string    uid de la tache à exécuter
 * $line    @array     tableau contenant les champs => valeur à traiter
 * $config  @array     tableau contenant le nom de la table
 */
//GLOBAL
function new_case_for_import($line, $config) {
    
    G::LoadClass("case");
// Execute events
    //require_once 'classes/model/Event.php';

    $caseInstance = new Cases ();
    //$eventInstance = new Event();
    $data = $caseInstance->startCase($config['TASK_UID'], $_SESSION['USER_LOGGED']);
    $_SESSION['APPLICATION'] = $data['APPLICATION'];
    $_SESSION['INDEX'] = $data['INDEX'];
    $_SESSION['PROCESS'] = $data['PROCESS'];
    $_SESSION['STEP_POSITION'] = 0;

    $newFields = $caseInstance->loadCase($data['APPLICATION']);
    /*$newFields['APP_DATA']['FLAG_ACTION'] = 'actionCreateCase';
    $newFields['APP_DATA']['FLAGTYPO3'] = 'Off';*/
    $newFields['APP_DATA']['LINE_IMPORT'] = $line;
    $newFields['APP_DATA']['CONFIG_IMPORT'] = $config;

    
    PMFSendVariables($data['APPLICATION'], $newFields['APP_DATA']);
    $caseInstance->updateCase($data['APPLICATION'], $newFields);
    $resInfo = PMFDerivateCase($data['APPLICATION'], 1,true, $_SESSION['USER_LOGGED']);

    //$eventInstance->createAppEvents($_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['TASK']);

// Redirect to cases steps
//$nextStep = $caseInstance->getNextStep($_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['STEP_POSITION']);
//G::header('Location: ../../cases/' . $nextStep['PAGE']);
//G::header('Location: ../../cases/open?APP_UID=' . $_SESSION['APPLICATION'].'&DEL_INDEX='.$_SESSION['INDEX']);
}

/***
 * Mise a jour de DATE_PROD dans PMT_LISTE_PROD et des dossiers produits
 * 
 */
 //GLOBAL
function convergence_updateDateProd($num_prod, $update){
    try{
        $query = 'SELECT APP_UID FROM PMT_LISTE_PROD WHERE NUM_DOSSIER ='.intval($num_prod);
        $result = executeQuery($query);
    convergence_updateDemande($result[1]['APP_UID'],$update);
    }
    catch (Exception $e) {
        var_dump($e);
        die();
    }  
}
//GLOBAL
function convergence_updateListeProd($app_id, $res){
    $field = convergence_getAllAppData($app_id);
    try{// à changer et recup de la table dispositif  CODE_OPER et CODE_CHEQUIER dans demandes
        $queryCode = 'SELECT CODE_OPERATION FROM PMT_DEMANDES WHERE APP_UID = \''.$res[0].'\'';
        $resCode = executeQuery($queryCode);
        $code = $resCode[1]['CODE_OPERATION'];
       /* $query = 'SELECT APP_NUMBER FROM PMT_LISTE_PROD WHERE APP_UID =\''.$field['APPLICATION'].'\'';
        $result = executeQuery($query);
    $data['NUM_DOSSIER'] = $result[1]['APP_NUMBER'];*/
    $data['CODE_OPER'] = $code;
    $data['NB_DOSSIERS'] = count($res); //doublon
    convergence_updateDemande($field['APPLICATION'], $data);
    
    }
    catch (Exception $e) {
        var_dump($e);
        die();
    }  
}

//GLOBAL
function convergence_getNumDossier($app_id){
    try{
        $query = 'SELECT APP_NUMBER FROM APPLICATION WHERE APP_UID = \''.$app_id.'\'';
        $result = executeQuery($query);
        return $result[1]['APP_NUMBER'];
    }
    catch (Exception $e){
        var_dump($e);
        die();
    }
}
//GLOBAL
function convergence_updateListeRemboursement($app_id, $res){
    $field = convergence_getAllAppData($app_id);
    try{
        $query = 'SELECT APP_NUMBER FROM PMT_LISTE_RMBT WHERE APP_UID =\''.$field['APPLICATION'].'\'';
        $result = executeQuery($query); 
        $queryCode = 'SELECT CODE_OPERATION FROM PMT_REMBOURSEMENT WHERE APP_UID = \''.$res[0].'\'';
        $resCode = executeQuery($queryCode);
        $data['CODE_OPER'] = $resCode[1]['CODE_OPERATION'];
    $data['NB_DOSSIERS'] = count($res); //doublon
    convergence_updateDemande($field['APPLICATION'], $data);
    
    }
    catch (Exception $e) {
        var_dump($e);
        die();
    }  
}

//convergence_InsertLineImport(@@LINE_IMPORT,@@CONFIG_IMPORT);
//GLOBAL
function convergence_InsertLineImport($line,$config){
    $key = implode(',',array_keys($line));
    $value = '"'.implode('","', $line).'"';
    try{
        
        $query = 'INSERT INTO '.$config['TABLENAME'].'('.$key.') VALUES ('.$value.')';
        $result = executeQuery($query);
        
        
    }
     catch (Exception $e) {
        G::pr('Erreur : impossible d\'exécuter la requête suivante : "INSERT INTO '.$config['TABLENAME'].'('.$key.') VALUES ('.$value.')"');
    G::pr($e);
        die();
    }  
}
//GLOBAL
/******* Met à jour les titres lors des remboursements effectués par l'AS400 via le fichier importé RMB **************/
function convergence_updateTitreRmb($line,$config){
   
   $array = array();
   foreach($line as $key => $value){
       $array []= $key.'="'.$value.'"';
    }
    $set = implode(',', $array);
    
    try{
        $query = 'UPDATE '.$config['TABLENAME'].' SET '.$set.' WHERE NUM_TITRE = '.$line['NUM_TITRE'];
        $result = executeQuery($query);
    }
     catch (Exception $e) {
        G::pr('Erreur : impossible d\'exécuter la requête');
    G::pr($e);
        die();
    }  
}
/***
 * Function à utiliser après un import de production de chèques dans le Workflow
 *   - Update du statut de la demande de 7 à 6 pour 'Produit'
 *   - Ajout dans la table HistoryLog pour avoir la date de production de la demandes x
 * 
 * $data            @array      Contient les datas de la function convergence_importFromAS400
 * $statut          @int        le nouveau statut à appliquer pour la demande, 'Produit' par defaut
 * $user_logged     @varchar    l'id de l'utilisateur courant pour l'historique log
 * 
 */
function convergence_changeStatutFromImport($data, $statut = 6) {
    $dossier = array();
    foreach ($data as $row)
    {
        $dossier[$row['NUM_DOSSIER']] = $row['BCONSTANTE'];
    }
    foreach ($dossier as $key => $value)
    {
        try {
            $query = 'SELECT APP_UID, NUM_DOSSIER_COMPLEMENT FROM PMT_DEMANDES WHERE NUM_DOSSIER = "' . $key . '" AND APP_NUMBER = (SELECT MAX(APP_NUMBER) AS APP_NUMBER FROM PMT_DEMANDES WHERE NUM_DOSSIER = "' . $key . '")';
            $result = executeQuery($query);
            if (isset($result) && count($result) > 0)
            {
                //Si production complémentaire, on insert un historique dans le dossier d'origine
                if (isset($result[1]['NUM_DOSSIER_COMPLEMENT']) && !is_null($result[1]['NUM_DOSSIER_COMPLEMENT']) && $result[1]['NUM_DOSSIER_COMPLEMENT'] != '')
                {
                    $qComplement = 'SELECT APP_UID FROM PMT_DEMANDES WHERE NUM_DOSSIER = "' . $result[1]['NUM_DOSSIER_COMPLEMENT'] . '" AND APP_NUMBER = (SELECT MAX(APP_NUMBER) AS APP_NUMBER FROM PMT_DEMANDES WHERE NUM_DOSSIER = "' . $result[1]['NUM_DOSSIER_COMPLEMENT'] . '")';
                    $rComplement = executeQuery($qComplement);
                    insertHistoryLogPlugin($rComplement[1]['APP_UID'], $_SESSION['USER_LOGGED'], date('Y-m-d H:i:s'), '0', '', "Chéquier de Complément N°" . $value . " produit", 6);
                }
                else
                {
                    convergence_changeStatut($result[1]['APP_UID'], $statut, 'Retour de production Chéquier N°' . $value);
                }
            }
        }
        catch (Exception $e) {
            G::pr('Erreur : les numéros de Dossier ne correspondent pas, veuilliez vérifier que vous avez importer le bon fichier');
            G::pr($e);
            die();
        }
    }    
}

//GLOBAL
function modifyAdresseofDemande($app_uid,$case_uid_demande) {
    //je recupere mes valeurs courantes
    $datas = convergence_getAllAppData($app_uid);
        $noMergeDatas = array('SYS_LANG','SYS_SKIN','SYS_SYS','APPLICATION','PROCESS','TASK','INDEX','USER_LOGGED','USER_USERNAME','PIN','FLAG_ACTION','APP_NUMBER');
    //on modifie le case de la demande
    $oCase = new Cases ();
    $Fields = $oCase->loadCase ($case_uid_demande);
        foreach ($datas as $key => $value) {                
            if(!in_array($key, $noMergeDatas))            
                $Fields['APP_DATA'][$key] = $value;           
        }
        $oCase->updateCase($case_uid_demande, $Fields);
        
        insertHistoryLogPlugin($case_uid_demande,$_SESSION['USER_LOGGED'],date('Y-m-d H:i:s'),'0','',"Modification de l'adresse",6);
        
}

//GLOBAL
function modifyDateExpedition($app_uid,$case_uid_liste_prod) {
    //jerecupere mes valeurs courantes
    $datas = convergence_getAllAppData($app_uid);

    //on modifie le case de la demande
    $oCase = new Cases ();
    $Fields = $oCase->loadCase($case_uid_liste_prod);
    $Fields['APP_DATA']['DATE_EXP'] = $datas['dateExp'];

    $oCase->updateCase($case_uid_liste_prod, $Fields);
        
}

//GLOBAL
function modifyDateVirement($app_uid,$case_uid_liste_rmbt) {
    //je recupere mes valeurs courantes
    $datas = convergence_getAllAppData($app_uid);
    $datasForm = convergence_getAllAppData($case_uid_liste_rmbt);
        
       
    //on modifie le case de la demande
    $oCase = new Cases ();
    $Fields = $oCase->loadCase($case_uid_liste_rmbt);
    $Fields['APP_DATA']['DATE_VIREMENT'] = $datas['dateVir'];
    $oCase->updateCase($case_uid_liste_rmbt, $Fields);
        $query = 'SELECT APP_UID FROM PMT_REMBOURSEMENT WHERE STATUT = 9 AND NUM_DOSSIER IN('.$datasForm['LISTE_DOSSIER'].')';
        $result = executeQuery($query);
        if(isset($result) && count($result) > 0){
            foreach ($result as $row){
                convergence_changeStatut($row['APP_UID'], '10');
            }
        }
}
/* * ***
 *  Fonction récupérant le nombre de dossier traité pour un export
 *
 *  $statut     @integer    le statut des dossiers traités
 *  $groupby      @string     le trie voulu
 */
//LOCALE mais doit etre GLOBAL
function convergence_countCaseToProduct($statut, $codeOper) 
{
    $queryCodeOper = '';
    if(isset($codeOper) && $codeOper != 0)
    {
        $queryCodeOper = ' AND CODE_OPERATION = '.$codeOper;
    }
    $query = 'SELECT APP_UID, THEMATIQUE, THEMATIQUE_LABEL, CODE_OPERATION, T.LABEL FROM PMT_DEMANDES as D INNER JOIN PMT_TYPE_CHEQUIER as T ON (D.CODE_CHEQUIER = T.CODE_CD) WHERE (STATUT = '.$statut.' OR (STATUT = 6 AND REPRODUCTION_CHQ = "O")) '.$queryCodeOper;
    $res = executeQuery($query);
    $count = array();
    if(count($res))
    {
        $msg['NOTHING'] = 0;
        $msg['HTML'] = 'Vous allez lancer la production de : <br />';
        foreach($res as $thema)
        {
            $count[$thema['THEMATIQUE']]['label'] = $thema['THEMATIQUE_LABEL'];
            $count[$thema['THEMATIQUE']]['total'] ++;
            $count[$thema['THEMATIQUE']]['codeOper'] = $thema['CODE_OPERATION'];
            $count[$thema['THEMATIQUE']][$thema['LABEL']]['chequier'] = $thema['LABEL'];
            $count[$thema['THEMATIQUE']][$thema['LABEL']]['total'] ++;
        }
        $queryRepro = 'SELECT COUNT(CODE_OPERATION) as NB, CODE_OPERATION FROM PMT_DEMANDES WHERE STATUT = 6 AND REPRODUCTION_CHQ = "O" '.$queryCodeOper.' GROUP BY CODE_OPERATION';
        $resRepro = executeQuery($queryRepro);
        if(count($resRepro))
        {
            $reproTab = array();
            foreach($resRepro as $repro)
            {
                (intval($repro['NB']) > 1 ) ? $s = 's' : $s = '';
                $reproTab[$repro['CODE_OPERATION']] = 'dont '.$repro['NB'].' reproduction'.$s.'<br />';
            }
        }
        $queryComp = 'SELECT COUNT(CODE_OPERATION) as NB, CODE_OPERATION FROM PMT_DEMANDES WHERE STATUT = 2 AND COMPLEMENT_CHQ = "1" '.$queryCodeOper.' GROUP BY CODE_OPERATION';
        $resComp = executeQuery($queryComp);
        if(count($resComp))
        {
            $compTab = array();
            foreach($resComp as $comp)
            {
                (intval($comp['NB']) > 1 ) ? $s = 's' : $s = '';
                $compTab[$comp['CODE_OPERATION']] = 'dont '.$comp['NB'].' chéquier'.$s.' de complément<br />';
            }
        }
        foreach($count as $tab)
        {
            (intval($tab['total']) > 1) ? $s = 's' : $s = '';
            $nb = $tab['total'];
            $th = $tab['label'];
                               
            $msg['HTML'] .= "-  $nb dossier$s pour la thématique :  $th <br />" ;
            $msg['HTML'] .= $reproTab[$tab['codeOper']];
            $msg['HTML'] .= $compTab[$tab['codeOper']];
            foreach($tab as $chequier)
            {   
                if(is_array($chequier))
                {
                    $nbCheq = $chequier['total'];
                    $lbCheq = $chequier['chequier'];
                    $msg['HTML'] .= "<span style=\"margin-left:5em\">$lbCheq : $nbCheq</span><br />" ;
                }
            }
        }
    }
    else
    {
        $msg['HTML'] = "Aucun dossier à produire ! Veuillez annuler l'opération";
        $msg['NOTHING'] = 1;
    }
    return $msg;
}

/* * ***
 *  Fonction pour vérifier si la demande n'as pas déjà était faite, par un même bénéficiaire
 *
 *  $user     @string    le @@USER_LOGGED du dynaform courant.
 */

//LOCALE mais doit etre GLOBAL
function convergence_justeOneDemande($user) {
    $query = 'SELECT APP_UID FROM APPLICATION WHERE APP_INIT_USER ="' . $user . '"';
    $res = executeQuery($query);
    if (isset($res) && count($res) > 0)
    {
        foreach ($res as $key => $array)
        {
            foreach ($array as $uid)
            {
                $qDemande = 'SELECT APP_UID FROM PMT_DEMANDES WHERE APP_UID = "' . $uid . '" AND STATUT <> 999 AND STATUT <> 0';
                $rDemande = executeQuery($qDemande);
                if (isset($rDemande) && count($rDemande) > 0)
                {
                    return 0;
                }
            }
        }
    }
    return 1;
}

