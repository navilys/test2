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

function limousinProject_getMyCurrentDate() {
    return G::CurDate('Y-m-d');
}

function limousinProject_getMyCurrentTime() {
    return G::CurDate('H:i:s');
}

//LOCAL : a transforme dans le moteur de regle
function convergence_getIncompletErreur($app_id) {
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

function limousinProject_nouvelleTransaction() {

    // INIT Ws
    $t = new Transaction();

    // SET Params
    $t->operation = "01";
    $t->partenaire = "00028";
    $t->porteurId = "30280000023";
    $t->sens = "C";
    $t->montant = "100";
    $t->addSousMontant("_reseau1", "_montatnReseau1");
    $t->addSousMontant("_reseau2", "_montatnReseau2");

    // CALL Ws
    try
    {
        $retour = $t->call();
    }
    catch (Exception $e)
    {
        // TODO
        var_dump($e);
        die();
    }
	var_dump($retour);
}

function limousinProject_nouvelleActionCRM() {

    // INIT Ws
    $a = new ActionCRM();
    $action = "_action";
    $motif = "_motif";

    // SET Params
    $a->partenaire = "_partenaire";
    $a->porteurId = "_porteurId";
    $a->action = $action;
    if (!empty($motif))
        $a->motif = $motif;

    // CALL Ws
    try
    {
        $retour = $a->call();
    }
    catch (Exception $e)
    {
        // TODO
        var_dump($e);
        die();
    }
}

function limousinProject_getOperations() {

    // INIT Ws
    $o = new Operation();

    // SET Params
    $o->operation = "_operation";
    $o->partenaire = "_partenaire";
    $o->porteurId = "_porteurId";
    $o->dateDepart = "_dateDepart";
    $o->jours = "_jours";

    // CALL Ws
    try
    {
        $retour = $o->call();
    }
    catch (Exception $e)
    {
        // TODO
        var_dump($e);
        die();
    }
}

function limousinProject_getSolde() {

    // INIT Ws
    $s = new Solde();

    // SET Params
    $s->partenaire = "_partenaire";
    $s->porteurId = 30280000023;

    // CALL Ws
    try
    {
        $retour = $s->call();
    }
    catch (Exception $e)
    {
        // TODO
        var_dump($e);
        die();
    }
}

function limousinProject_identification() {

    // INIT Ws
    $i = new Identification();

    // SET Params
    $i->porteurId = "_porteurId";
    $i->telephone = "_telephone";
    $i->portable = "_portable";
    $i->email = "_email";
    $i->numcarte = "_numCarte";

    // CALL Ws
    try
    {
        $retour = $i->call();
    }
    catch (Exception $e)
    {
        // TODO
        var_dump($e);
        die();
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
        $path = $res[1]['PATH_FILE'] . '/OUT/AQ_PORT_R_001_00008.' . date('Ymd');
    }
    else
        $path = '/var/tmp/AQ_PORT_R_001_00008.' . date('Ymd');

    return $path;
}

/*  Ajout les lignes d'entête et de fin de fichier pour le fichier AQ_PORT 
 * 
 * @param string $file le chemin du fichier à modifier
 *  */

function limousinProject_updateAQPORTR($file) {

    $qIdFile = 'select max(ID) as num_fic from PMT_NUM_PROD_FOR_AQOBA where statut = 1';
    $rIdFile = executeQuery($qIdFile);
    if (!empty($rIdFile[1]['num_fic']))
        $num_fic = str_pad($rIdFile[1]['num_fic'], 15, 0, STR_PAD_LEFT);
    else
        $num_fic = str_pad('1', 15, 0, STR_PAD_LEFT);
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
 * 
 */
function limousin_Project_removeWrapFileAqoba($list_file){
    
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
            $code_erreurs = substr($line, 1123);
            $porter_id = substr($line, 29, 12);
            
            $q = 'select APP_UID from PMT_DEMANDES where PORTEUR_ID = "'.$porter_id.'" and STATUT = 7';
            $r = executeQuery($q);
            if(!empty($r[1]['APP_UID']))
            {
                convergence_changeStatut($r[1]['APP_UID'], 71, 'Erreur dans le fichier AQ_PORT_R code :' . $code_erreurs);
                $code_err = strtr($code_erreurs, ' ', ',');                
                $qError = 'select LABEL_E_AQ from PMT_CODE_ERREUR_AQOBA where CODE_E_AQ IN('.$code_err.')';
                $rError= executeQuery($qError);
                mail('nicolas@oblady.fr','debug du $rError'.date('H:i:s'),var_export($rError,true));
                foreach ($rError as $value) {
                 $data[$r[1]['APP_UID']][] = $value['LABEL_E_AQ'];    
                }                                
            }
        }
        foreach ($data as $app_uid => $list_err) 
        {            
            $msgList = implode("\n\t - ", $list_err);
            $msg['MSGREFUS'] = "Création de carte refusé par AQOBA pour les raisons suivante : \n".$msgList;            
            //convergence_updateDemande($app_uid, $msg);
            mail('nicolas@oblady.fr','debug du $msg'.date('H:i:s'),var_export($msg,true));
        }
    }
}
?>
