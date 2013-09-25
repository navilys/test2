<?php

/**
 * class.limousinProject.pmFunctions.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.
 * *
 */
require_once("plugins/limousinProject/classes/Webservices/Webservice.php");
require_once("plugins/limousinProject/classes/Webservices/Transaction.php");
require_once("plugins/limousinProject/classes/Webservices/ActionCRM.php");
require_once("plugins/limousinProject/classes/Webservices/Activation.php");
require_once("plugins/limousinProject/classes/Webservices/Operation.php");
require_once("plugins/limousinProject/classes/Webservices/Solde.php");
require_once("plugins/limousinProject/classes/Webservices/Identification.php");

////////////////////////////////////////////////////
// limousinProject PM Functions
//
// Copyright (C) 2007 COLOSA
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////

function limousinProject_getMyCurrentDate(){
    return G::CurDate('Y-m-d');
}

function limousinProject_getMyCurrentTime(){
    return G::CurDate('H:i:s');
}

//LOCAL : a transforme dans le moteur de regle
function convergence_getIncompletErreur($app_id){
    $fields = convergence_getAllAppData($app_id);   
    $incomplet = array();    
    if ($fields['FINOM'] == '')
    {
        $incomplet[] = "Votre nom";
    }
    if ($fields['FIPRENOM'] == '')
    {
        $incomplet[] = "Votre prénom";
    }
    if ($fields['FIDATEDENAISSANCE'] == '')
    {
        $incomplet[] = "Votre date de naissance";
    }
    if ($fields['FIADRESSE1'] == '' || $fields['FICODEPOSTAL'] == '' || $fields['FIVILLE'] == '')
    {
        $incomplet[] = "L'adresse d'envoie pour la carte";
    }
    if ($fields['FIVILLE'] == '')
    {
        $incomplet[] = "Un code postal sur le territoire national";
    }
    if ($fields['FITELEPHONE'] == '')
    {
        $incomplet[] = "Votre numéro de téléphone";
    }
    if ($fields['FIEMAIL'] == '')
    {
        $incomplet[] = "Votre e-mail";
    }
    if ($fields['FISITUATION'] == '1' && $fields['FIETABLISSEMENT'] == '')
    {
        $incomplet[] = "Votre établissement";
    }
    if ($fields['FISITUATION'] == '2' && $fields['FISITUATIONDETAIL'] == '')
    {
        $incomplet[] = "Votre situation";
    }
    return $incomplet;
}

function convergence_getMsgErreur($app_id) {
   
    $refus = array();
    $fields = convergence_getAllAppData($app_id);
    if (!isset($fields['GDCERTIFSCOLARITE']) || $fields['GDCERTIFSCOLARITE'] == 0)
    {
        $refus[] = "Votre certificat de scolarité est manquant.";
    }
    else if (!isset($fields['GDCERTIFSCOLARITEOK']) || $fields['GDCERTIFSCOLARITEOK'] == 0)
    {
        $refus[] = "Votre certificat de scolarité n'est pas conforme.";
    }
    if (!isset($fields['GDSIGNATUREPARENTALE']) || $fields['GDSIGNATUREPARENTALE'] == 0)
    {
        $refus[] = "Votre signature parentale est manquante.";
    }
    if (!isset($fields['GDJUSTIFDOM']) || $fields['GDJUSTIFDOM'] == 0)
    {
        $refus[] = "Votre justificatif de domicile est manquant.";
    }
    else if (!isset($fields['GDJUSTIFDOMOK']) || $fields['GDJUSTIFDOMOK'] == 0)
    {
        $refus[] = "Votre justificatif de domicile n'est pas conforme.";
    }
    if (!isset($fields['GDJUSTIFIDENTITE']) || $fields['GDJUSTIFIDENTITE'] == 0)
    {
        $refus[] = "Votre justificatif d'identité est manquant.";
    }
    else if (!isset($fields['GDJUSTIFIDENTITEOK']) || $fields['GDJUSTIFIDENTITEOK'] == 0)
    {
        $refus[] = "Votre justificatif d'identité n'est pas conforme.";
    }
    return $refus;
}

function limousinProject_generatePorteurID($num_dossier) {
    
    /* Les 4 premiers caractères seront : 3028
      Les 6 autres seront le numéro unique créé par convergence
      Concernant le dernier la formule exacte est : 9 - somme(des 10 premiers chiffres) modulo 9.
      Ce qui fait que l'exemple du document est faux : 23 mod 9 = 5, et 9-5=4 donc le dernier chiffre doit être 4.
     */

    $prefix = '3028';
    $temp_num_dossier = str_pad($num_dossier, 6, "0", STR_PAD_LEFT);
    $somme = array_sum(str_split($prefix . $temp_num_dossier));
    $cle_modulo = 9 - $somme % 9;


    $porteurID = $prefix . $temp_num_dossier . $cle_modulo;

    return $porteurID;
}

function limousinProject_getDemandeFromUserID($userId) {
    $arrayDemandeInfos = array();
    $queryDemande = "SELECT APP_UID FROM PMT_DEMANDES WHERE USER_ID = '" . $userId . "' AND STATUT = 6 AND THEMATIQUE = 1";
    $resultAppUid = executeQuery($queryDemande);
    if(sizeof($resultAppUid) == 1)
    {
        $appUid = $resultAppUid[1]['APP_UID'];
        $arrayDemandeInfos = convergence_getAllAppData($appUid, 1);
    }
    return $arrayDemandeInfos;
}

function limousin_addTransactionPriv($codePartenaire, $porteurID, $montant, $libelle, $thematique) {
    $queryInsertTransactionPriv = "INSERT INTO PMT_TRANSACTIONS_PRIV(CODE_PARTENAIRE, PORTEUR_ID, MONTANT, LIBELLE, THEMATIQUE) VALUES('".$codePartenaire."','".$porteurID."','".$montant."','".$libelle."','".$thematique."')";
    executeQuery($queryInsertTransactionPriv);
}

function limousinProject_nouvelleTransaction($operation = 0, $porteurId = 0, $sens = 'N', $montant = 0) {

    // INIT Ws 201
    $t = new Transaction();

	// SET Params
    $t->partenaire = wsPrestaId;
    $t->operation = $operation;
    $t->porteurId = $porteurId;
    // C -> Chargement ou D -> Dechargement
    $t->sens = $sens;
    $t->montant = $montant;

    /* Mode Test On */
    //$t->porteurId = "30280000023";
    $t->porteurId = "30280055283";
    //$t->porteurId = "0009";
    $t->sens = "C";
    $t->montant = "200";
    /* $t->addSousMontant("_reseau1", "_montatnReseau1");
      $t->addSousMontant("_reseau2", "_montatnReseau2"); */
    /* Mode Test Off */


    // CALL Ws
    try
    {
        // TODO
        $retour = $t->call();
        $echo = $retour->idTransaction;
        echo 'trans ok = ' . $echo;
    }
    catch (Exception $e)
    {
        // TODO
        $echo = $e->errors->code . ' : ' . $e->errors->message;
        echo 'Code Erreur transaction = ' . $echo . '--- End Error ---';
    }

}

function limousinProject_nouvelleActionCRM($porteurId = 0, $action = '00', $motif = '') {

    // INIT Ws 210
    $a = new ActionCRM();

    // SET Params
    $a->partenaire = wsPrestaId;
    $a->porteurId = $porteurId;
    $a->action = $action;


    /* Mode Test On */
    $action = "06";
    $motif = "02";
    $a->partenaire = "00028";
    $a->porteurId = "30280055364";
    $a->action = $action;
    /* Mode Test Off */

    if (!empty($motif))
        $a->motif = $motif;

    // CALL Ws
    try
    {
        $retour = $a->call();
        echo 'ok => crm' . $retour . '--- end retour';
    }
    catch (Exception $e)
    {
        // TODO
        $echo = $e->errors->code . ' : ' . $e->errors->message;
        echo 'Code Erreur action = ' . $echo . '--- End Error ---';
    }
}

function limousinProject_getOperations($porteurId = 0, $op = '00', $nbJours = '100') {

    // INIT Ws 303
    $o = new Operation();

    // SET Params
    $o->operation = $op;
    $o->porteurId = $porteurId;
    $o->partenaire = wsPrestaId;
    $dateDep = date("Ymd", mktime(0, 0, 0, date("m") - 2, date("d"), date("Y"))); // a définir
    $o->dateDepart = $dateDep;
    $o->jours = $nbJours;
    /*
      00: Toutes les operations listé ci-dessous sauf les frais programmes,
      01: Retraits (et annulations de retraits),
      02: Versements (et annulations de versements),
      03: Déversements (et annulations de déversements),
      04: Achats (et annulations d’achats),
      05: Frais Porteurs (de retraits ou autres),
      06: Frais Programme
     */

    /* Mode Test On */
    //$o->porteurId = 30280000023;
    $o->porteurId = 30280055283;
    $o->operation = '02';
    /* Mode Test Off */

    // CALL Ws
    try
    {
        // TODO
        $retour = $o->call();
        echo 'ok oper => ' . $retour . '--- end retour';
    }
    catch (Exception $e)
    {
        // TODO
        //$echo = $e->errors; //. ' : ' . $e->message;
        echo 'Code Erreur operation = ' . $o->errors->code . '--- End Error ---';
        //var_dump($e);
    }
}

function limousinProject_getActivation($porteurId = 0) {

    // INIT Ws 211    
    $v = new Activation();

    // SET Params
    $v->partenaire = wsPrestaId;
    $v->porteurId = $porteurId;

    /* Mode Test On */
    $v->porteurId = 30280055364;
    //$s->porteurId = 30280000023;
    /* Mode Test Off */

    // CALL Ws
    try
    {
        // TODO
        $retour = $v->call();
        echo 'ok act => ' . $retour . '--- end retour';
    }
    catch (Exception $e)
    {
        // TODO
        $echo = $e->errors->code . ' : ' . $e->errors->message;
        echo 'Code Erreur activation = ' . $echo . '--- End Error ---';
    }
}
function limousinProject_getSolde($porteurId = 0) {

    // INIT Ws 304
    $s = new Solde();

    // SET Params
    $s->partenaire = wsPrestaId;
    $s->porteurId = $porteurId;

    /* Mode Test On */
    //$s->porteurId = 30280055364;
    $s->porteurId = 30280055283;
    //$s->porteurId = '30280000023';
    /* Mode Test Off */

    // CALL Ws
    try
    {
        // TODOs
        $retour = $s->call();
        echo 'ok solde => ' . $retour->solde . '--- end retour';
        //var_export($retour);
    }
    catch (Exception $e)
    {
        // TODO
        $echo = $e->errors->code . ' : ' . $e->errors->message;
        echo 'Code Erreur solde = ' . $echo . '--- End Error ---';
    }
}

function limousinProject_identification($porteurId = 0, $tel = '', $portable = '', $mail = '', $numCarte = '') {

    // INIT Ws 307
    $i = new Identification();

    // SET Params
    $i->porteurId = $porteurId;
    $i->telephone = $tel;
    $i->portable = $portable;
    $i->email = $mail;
    $i->numcarte = $numCarte;

    /* Mode Test On */
    //$i->porteurId = 30280055364;
    $i->porteurId = 30280055283;
    $i->numcarte = '0007';
    $i->portable = 'quentin@oblady.fr';
    $i->email = '11-15-61-56-51';
    //$i->porteurId = 30280000023;
    /* Mode Test Off */

    // CALL Ws
    try
    {
        $retour = $i->call();
    }
    catch (Exception $e)
    {
        // TODO
        $echo = $e->errors->code . ' : ' . $e->errors->message;
        echo 'Code Erreur identification = ' . $echo . '--- End Error ---';
    }
}


function limousinProject_createUser($app_id, $role) {
    $fields = convergence_getAllAppData($app_id);
    //PMFCreateUser(string userId, string password, string firstname, string lastname, string email, string role)
    $isCreate = PMFCreateUser($fields['MAIL'], $fields['PASSWORD'], $fields['NOM_CONTACT'], $fields['PRENOM_CONTACT'], $fields['MAIL'], $role);
    if ($isCreate == 0)
        return FALSE;

    $uQuery = 'SELECT USR_UID FROM USERS WHERE USR_USERNAME ="' . $fields['MAIL'] . '"';
    $rQuery = executeQuery($uQuery);
    if (!empty($rQuery))
    {
        $usr_uid = $rQuery[1]['USR_UID'];
        $qGpId = ' SELECT *  FROM `CONTENT` WHERE `CON_VALUE` LIKE "' . $role . '" AND CON_CATEGORY = "GRP_TITLE"';
        $rGpId = executeQuery($qGpId);

        if (!empty($rGpId[1]['CON_ID']))
        {

            $IP = $_SERVER['HTTP_HOST'];
            $port = port_extranet;
            if(empty($port))
                $port = '8084';
            $groupId = $rGpId[1]['CON_ID'];
            $var = PMFAssignUserToGroup($usr_uid, $groupId);

            // creation du fe_user dans typo3
            //$res = userSettingsPlugin($groupId, $urlTypo3 = 'http://172.17.20.29:8084/');
            ini_set("soap.wsdl_cache_enabled", "0");
            $hostTypo3 = 'http://' . $IP . ':' . $port . '/typo3conf/ext/pm_webservices/serveur.php?wsdl';
            $pfServer = new SoapClient($hostTypo3);
            $key = rand();
            $ret = $pfServer->createAccount(array(
                'username' => $fields['MAIL'],
                'password' => md5($fields['PASSWORD']),
                'email' => $fields['MAIL'],
                'lastname' => $fields['PRENOM_CONTACT'],
                'firstname' => $fields['NOM_CONTACT'],
                'key' => $key,
                'pmid' => $usr_uid,
                'usergroup' => $groupId,
                'cHash' => md5($fields['MAIL'] . '*' . $fields['PRENOM_CONTACT'] . '*' . $fields['NOM_CONTACT'] . '*' . $key)));
        }
    }
    else
    {
        return FALSE;
    }
    return TRUE;
}

function limousinProject_getEtablissementFromRNE($rneCode) {
    $sql = 'SELECT RNE, NOM FROM PMT_ETABLISSEMENT WHERE RNE = "' . $rneCode . '" AND STATUT = 1';
    $res = executeQuery($sql);
    if (isset($res) && count($res) > 0)
    {
        $ret = $res[1]['RNE'] . ' - ' . $res[1]['NOM'];
    }
    else
    {
        $ret = '0';
    }
    return $ret;
}

function limousinProject_getPathAQPORTR() {

    $sql = 'select PATH_FILE from PMT_LISTE_OPER';
    $res = executeQuery($sql);
    if (!empty($res))
    {
        $path = $res[1]['PATH_FILE'] . '/OUT/AQ_PORT_R_001_00028.' . date('Ymd');
    }
    else
        $path = '/var/tmp/AQ_PORT_R_001_00028.' . date('Ymd');

    return $path;
}

/*  Ajout les lignes d'entête et de fin de fichier pour le fichier AQ_PORT 
 *
 * @param string $file le chemin du fichier à modifier
 *  */

function limousinProject_updateAQPORTR($file, $num_dossier) {

    $qIdFile = 'select max(ID)+1 as num_fic from PMT_NUM_PROD_FOR_AQOBA';
    $rIdFile = executeQuery($qIdFile);
    if (!empty($rIdFile[1]['num_fic']) && $rIdFile[1]['num_fic'] != 0)
    {
        $num_fic = str_pad($rIdFile[1]['num_fic'], 15, 0, STR_PAD_LEFT);
        $id = $rIdFile[1]['num_fic'];
    }
    else
    {
        $num_fic = str_pad('1', 15, 0, STR_PAD_LEFT);
        $id = 1;
    }
    $qAdd = 'insert into PMT_NUM_PROD_FOR_AQOBA (FILE_NAME, NUM_PROD, ID) values ("' . mysql_escape_string(basename($file)) . '","' . intval($num_dossier) . '","' . intval($id) . '")';
    $rAdd = executeQuery($qAdd);
    $filler = str_pad('', 32, ' ');
    $start_line = '00101004' . date("YmdHis") . $num_fic . $filler . "\n";
    $end_line = '00301004' . date("YmdHis") . $num_fic . $filler . "\n";
    $content = file($file);
    array_unshift($content, $start_line);
    array_push($content, $end_line);
    $new_content = implode('', $content);
    $fp = fopen($file, 'w');
    $w = fwrite($fp, $new_content);
    fclose($fp);
}

/*  Supprime les lignes d'entête et de fin de fichier fournie par AQOBA
 *
 * @param   array   $list_file  liste des fichiers sur la machine PM en local
 * 
 * @return  array   $list_file  liste des fichiers modifiés sur la machine PM en local
 */

function limousinProject_removeWrapFileAqoba($list_file){
    
    if(!empty($list_file)){        
        foreach ($list_file as $file) 
        {
            $content = file($file);
            if(!empty($content))
            {
                $first = array_shift($content);
                $last = array_pop($content);
                $new_content =  implode('', $content);
                $fp = fopen($file, 'w+');
                $w = fwrite($fp, $new_content);
                fclose($fp);
            }
        }
        return TRUE;        
    }
    return FALSE;
}
function limousinProject_updateFromAQPORTREJ($file){
    //on récupère le contenu du fichier
    $content = file($file);
    $data = array();
    if(!empty($content))
    {
        foreach($content as $line)            
        {            
            $code_erreurs = trim(substr($line, 1123));
            $porter_id = trim(substr($line, 29, 12));
            $q = 'select APP_UID from PMT_DEMANDES where PORTEUR_ID = "' . $porter_id . '" and STATUT = 7';
            $r = executeQuery($q);
            if(!empty($r[1]['APP_UID']))
            {
                convergence_changeStatut($r[1]['APP_UID'], 41, 'Erreur dans le fichier AQ_PORT_R code :' . $code_erreurs);
                $code_err = '"' . strtr(trim($code_erreurs), array(' ' => '","')) . '"';
                $qError = 'select LABEL_E_AQ from PMT_CODE_ERREUR_AQOBA where CODE_E_AQ IN('.$code_err.')';
                $rError = executeQuery($qError);
                foreach ($rError as $value)
                {
                    $data[$r[1]['APP_UID']][] = $value['LABEL_E_AQ'];
                }
            }
        }            
        foreach ($data as $app_uid => $list_err)
        {            
            $msgList = '<br/>&nbsp;-&nbsp;' . implode('<br/>&nbsp;-&nbsp;', $list_err);
            $msg['msgRefus'] = 'Création de carte refusé par AQOBA pour les raisons suivante : <br/>' . $msgList;
            convergence_updateDemande($app_uid, $msg);
            
        }
    }
}
function limousinProject_explicationStatut_callback($app_data) {

    $libelStatut = 'SELECT TITLE FROM PMT_STATUT WHERE UID=' . intval($app_data['STATUT']);
    $libelRes = executeQuery($libelStatut);
    switch (intval($app_data['STATUT']))
    {
        case 41 :
            $messageInfo = $app_data['msgRefus'];
            break;

        default :
            $messageInfo = 'Le dossier est en statut : <b>' . strtolower($libelRes[1]['TITLE']) . '</b>';
            break;
    }
    return $messageInfo;
}
function limousinProject_readLineFromAQCARTE($datas) {
    
    //INIT
    $err = array();

    foreach ($datas as $line)
    {
        $escapeLine = array();

        // Escape scpeial caracters
        foreach ($line as $key => $lineItem)
            $escapeLine[$key] = mysql_escape_string($lineItem);

        $qExist = 'select count(UID) as nb from PMT_CHEQUES where CARTE_PORTEUR_ID = "' . $escapeLine['CARTE_PORTEUR_ID'] . '"';
        $rExist = executeQuery($qExist);
        $nbID = $rExist[1]['nb'];
        switch ($escapeLine['CODE_EVENT'])
        {
            case '05' :// Phase 1 : Prise en compte et création dans le système AQOBA
                /* création de la ligne dans la table PMT_CHEQUES */
                if ($nbID == 0)
                {
                    $escapeLine['CARTE_STATUT'] = 'Création';
                    $escapeLine['DATE_CREATION'] = date('Ymd');
                    $keys = implode(',', array_keys($escapeLine));
                    $values = '"' . implode('","', $escapeLine) . '"';
                    $query = 'INSERT INTO PMT_CHEQUES (' . $keys . ') VALUES (' . $values . ')';
                    $result = executeQuery($query);
                }
                else                
                    $err[] = 'Porteur Id existe déjà';                
                break;

            case '14' : //Phase 2 : Frabrication et envoie
                /* Dans ce cas, vérifier si une demande possède ce porteur id en mode ré-édition
                 * pour transférer les soldes entre les deux cartes
                 * sinon mettre à jour simplement la ligne et le statut, et mettre la demande en produite  */
                if ($nbID > 0)
                {
                    $set = array();
                    $escapeLine['CARTE_STATUT'] = 'Envoyée';
                    foreach ($escapeLine as $key => $value)
                    {
                        $set[] = $key . '="' . $value . '"';
                    }
                    $update = implode(',', $set);
                    $query = 'update PMT_CHEQUES SET ' . $update . ' where CARTE_PORTEUR_ID= "' . $escapeLine['CARTE_PORTEUR_ID'] . '"';
                    $result = executeQuery($query);
                    // reste les ws dans le cas des ré-édition
                    $qDemande = 'select count(*) as nb, APP_UID, OLD_PORTEUR_ID from PMT_DEMANDES where PORTEUR_ID ="' . $escapeLine['CARTE_PORTEUR_ID'] . '" and STATUT <> 0 and STATUT <> 999';
                    $rDemande = executeQuery($qDemande);
                    if ($rDemande[1]['nb'] > 0)
                    {
                        convergence_changeStatut($rDemande[1]['APP_UID'], 6);
                        if (!empty($rDemande[1]['OLD_PORTEUR_ID']))
                        {
                            // ws pour le transfert des soldes
                        }
                    }
                }
                else
                    $err[] = "Porteur Id n'existe pas";
                break;

            case '10' : // Phase 3 : Activation
                /* Mettre à jour la ligne, le DATE_ACTIVE et le statut à Active */
                if ($nbID > 0)
                {
                    $escapeLine['CARTE_STATUT'] = 'Active';
                    $escapeLine['DATE_ACTIVE'] = date('Ymd');
                    foreach ($escapeLine as $key => $value)
                    {
                        $set[] = $key . '="' . $value . '"';
                    }
                    $update = implode(',', $set);
                    $query = 'update PMT_CHEQUES SET ' . $update . ' where CARTE_PORTEUR_ID= "' . $escapeLine['CARTE_PORTEUR_ID'] . '"';
                    $result = executeQuery($query);
                }
                else
                    $err[] = "Porteur Id n'existe pas";
                break;

            default:
                break;
        }
    }
    return TRUE;
}

// non op
function limousinProject_showPdf($app_uid) {
    $query = 'SELECT * FROM APP_DOCUMENT, CONTENT WHERE APP_UID="' . $app_uid . '" AND DOC_UID="884895097521c61f362fc13075215643" AND APP_DOC_TYPE="OUTPUT" AND APP_DOC_STATUS="ACTIVE" AND APP_DOC_UID = CON_ID AND CON_CATEGORY = "APP_DOC_FILENAME" AND CON_LANG = "fr"';
    $result = executeQuery($query);
    if (method_exists('G', 'getPathFromUID'))
    {
        $app_uid = G::getPathFromUID($app_uid);
    }

    $path = PATH_DOCUMENT . $app_uid . PATH_SEP . 'outdocs' . PATH_SEP . $result[1]['APP_DOC_UID'] . '_' . $result[1]['DOC_VERSION'];
    $file = $path . '.pdf';

   /* mail('nicolas@oblady.fr', date('H:i:s') . ' debug $filemail ', var_export($file, true));

      if (file_exists($file))
      {
      //OUPUT HEADERS
      header("Pragma: public");
      header("Expires: 0");
      //header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
      //header("Cache-Control: private",false);
      header("Content-Type: application/pdf");
      header('Content-Disposition: attachment; filename="' . $result[1]['CON_VALUE'] . '.pdf";');
      header('Content-Length: ' . sizeof($file));
      header("Content-Transfer-Encoding: binary");
      header('Content-Description: File Transfer');
      header('Cache-Control: must-revalidate');
      //ob_clean();
      //flush();
      mail('nicolas@oblady.fr', date('H:i:s') . ' flush debug mail ', var_export($file, true));
      readfile($file);
      } */
}

?>