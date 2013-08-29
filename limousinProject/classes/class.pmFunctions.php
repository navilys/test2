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

function limousinProject_getMyCurrentDate()
{
    return G::CurDate('Y-m-d');
}

function limousinProject_getMyCurrentTime()
{
    return G::CurDate('H:i:s');
}

//LOCAL : a transforme dans le moteur de regle
function convergence_getIncompletErreur($app_id)
{
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

function convergence_getMsgErreur($app_id){
   
    $refus = array();
    $fields = convergence_getAllAppData($app_id);
    if(!isset($fields['GDCERTIFSCOLARITE']) || $fields['GDCERTIFSCOLARITE'] == 0)
    {
        $refus[] = "Votre certificat de scolarité est manquant.";
    }
    else if(!isset($fields['GDCERTIFSCOLARITEOK']) || $fields['GDCERTIFSCOLARITEOK'] == 0)
    {
        $refus[] = "Votre certificat de scolarité n'est pas conforme.";
    }
    if(!isset($fields['GDSIGNATUREPARENTALE']) || $fields['GDSIGNATUREPARENTALE'] == 0)
    {
        $refus[] = "Votre signature parentale est manquante.";
    }
    if(!isset($fields['GDJUSTIFDOM']) || $fields['GDJUSTIFDOM'] == 0)
    {
        $refus[] = "Votre justificatif de domicile est manquant.";
    }
    else if(!isset($fields['GDJUSTIFDOMOK']) || $fields['GDJUSTIFDOMOK'] == 0)
    {
        $refus[] = "Votre justificatif de domicile n'est pas conforme.";
    }
    if(!isset($fields['GDJUSTIFIDENTITE']) || $fields['GDJUSTIFIDENTITE'] == 0)
    {
        $refus[] = "Votre justificatif d'identité est manquant.";
    }
    else if(!isset($fields['GDJUSTIFIDENTITEOK']) || $fields['GDJUSTIFIDENTITEOK'] == 0)
    {
        $refus[] = "Votre justificatif d'identité n'est pas conforme.";
    }
    return $refus;
}

function limousinProject_generatePorteurID($num_dossier) {
    
    /*Les 4 premiers caractères seront : 3028
    Les 6 autres seront le numéro unique créé par convergence
    Concernant le dernier la formule exacte est : 9 - somme(des 10 premiers chiffres) modulo 9.
    Ce qui fait que l'exemple du document est faux : 23 mod 9 = 5, et 9-5=4 donc le dernier chiffre doit être 4.
    */
    
    $prefix = '3028'; 
    $temp_num_dossier = str_pad($num_dossier, 6, "0", STR_PAD_LEFT);     
    $somme = array_sum(str_split($prefix.$temp_num_dossier));
    $cle_modulo = 9 - $somme%9;

     
    $porteurID = $prefix.$temp_num_dossier.$cle_modulo;

    return $porteurID;
    
}


function limousinProject_nouvelleTransaction() {
    
	// INIT Ws
    $t = new Transaction();

	// SET Params
	$t->operation = "_operation";
	$t->partenaire = "_partenaire";
	$t->porteurId = "_porteurId";
	$t->sens = "_sens";
	$t->montant = "_montant";
	$t->addSousMontant("_reseau1","_montatnReseau1");
	$t->addSousMontant("_reseau2","_montatnReseau2");
	
	// CALL Ws
	try{
		$retour = $t->call();
	} catch (Exception $e) {
		// TODO	
		var_dump($e);
		die();
	}
	
	
}

function limousinProject_nouvelleActionCRM() {
    
	// INIT Ws
    $a = new ActionCRM();
	$action ="_action";
	$motif = "_motif";
	
	// SET Params
	$a->partenaire = "_partenaire";
	$a->porteurId = "_porteurId";
	$a->action = $action;
	if(!empty($motif))
		$a->motif = $motif;
	
	// CALL Ws
	try{
		$retour = $a->call();
	} catch (Exception $e) {
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
	try{
		$retour = $o->call();
	} catch (Exception $e) {
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
	$s->porteurId = "_porteurId";	
	
	// CALL Ws
	try{
		$retour = $s->call();
	} catch (Exception $e) {
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
	try{
		$retour = $i->call();
	} catch (Exception $e) {
		// TODO	
		var_dump($e);
		die();		
	}
	
}