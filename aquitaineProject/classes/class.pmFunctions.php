<?php
/**
 * class.colosaExample.pmFunctions.php
 * ess  
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.
 * *
 */

////////////////////////////////////////////////////
// colosaExample PM Functions
//
// Copyright (C) 2007 COLOSA
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////


function colosaExample_getMyCurrentDate()
{
	return G::CurDate('Y-m-d');
}

function colosaExample_getMyCurrentFDate()
{
	return G::CurDate('d-m-Y');
}

function colosaExample_getMyCurrentTime()
{
	return G::CurDate('H:i:s');
}

function  getUserInfoPlugin($username){
    $res = "";
    $query = "SELECT USR_FIRSTNAME, USR_LASTNAME FROM USERS where USR_USERNAME = '$username' ";
    $result = executeQuery($query);
    if(isset($result))
        $res = $result[1]['USR_FIRSTNAME']." ". $result[1]['USR_LASTNAME'];

    return $res;
}

//LOCAL : A priori ne sert que pour Aquitaine
function testCkeck($arrayFields,$fields,$value = 1){
    $check = false;
    foreach($fields as $key => $val){
        if((in_array($key, $arrayFields)) && (intval($val) != $value)) $check = true;
    }
    return $check;
}

//LOCAL : A priori ne sert que pour Aquitaine
function testCompletion($fields){
    $check = true;
    foreach($arrayFields as $key => $val){
        if('' == $val || null == $val) $check = false;
    }
    return $check;
}
//LOCAL : a transforme dans le moteur de regle
function convergence_getMsgErreurRmb($app_id) {

    $fields = convergence_getAllAppData($app_id);
    $date1 = 1;
    $date2 = 1;
    $dateReception = strtotime($fields['dateReception']);
    if(isset($fields['dateFacture']) && $fields['dateFacture'] != ''){
        $dateFacture = strtotime($fields['dateFacture']);
        $dateFin = strtotime($fields['dateFin']);
        $dateEmission = strtotime($fields['dateEmission']);
        //$lastmonth = mktime(0, 0, 0, date("m")-1, date("d"),   date("Y"));
        $explodeDateFacture = explode('-', $fields['dateFacture']);
        $dateLimite = mktime(0, 0, 0, $explodeDateFacture[1]+3, $explodeDateFacture[0], $explodeDateFacture[2]);
        $datetab[] = $explodeDateFacture;
        $datetab[] = $dateLimite;
        $datetab[] = $dateEmission;
        $datetab[] = $dateFin;

    if(strtotime($fields['dateFin']) < strtotime($fields['dateFacture']) || strtotime($fields['dateFacture']) < strtotime($fields['dateEmission'])) $date1 = 0;
    if(($dateReception < $dateFacture) || ($dateReception > $dateLimite)) $date2 = 0;
    }
   

   $error = array();
   
    if ($date1 == 0)
        $error[] = 'La date de facture n\'est pas dans la période de validitée du chèque';

    if ($date2 == 0)
        $error[] = 'La date de récèption n\'est pas comprise entre la date d‘acquittement + 3 mois';

     if ($fields['refus'] == 1)
        $error[] = 'Dossier refusé par Adéquation pour le motif suivant : '.$fields['motif'];

    return $error;

}

//LOCAL : a transforme dans le moteur de regle
function convergence_getIncompletErreurRmb($app_id) {

    $fields = convergence_getAllAppData($app_id);
    $incomplet = array();
    if ($fields['dateReception'] == '') $incomplet[] = "La date de récèption de la demande n'est pas renseignée" ;
     if ($fields['chqpresent'] == '0')
        $incomplet[] = 'Le chèque n\'est pas présent' ;
    if ($fields['chqtamponne'] == '0')
        $incomplet[] = 'Le chèque n\'est pas tamponné';
    if ($fields['dateEncaissement'] == '') $incomplet[] = "La date d'encaissement du chèque n'est pas renseignée" ;
    if ($fields['signature'] == '0') $incomplet[] = 'Signature manquante';
    if ($fields['bordereauConforme'] == '0')
        $incomplet[] = 'Le bordereau n\'est pas présent';
    if ($fields['facturepresente'] == '0')
        $incomplet[] = 'Facture non présente';
    if ($fields['dateFacture'] == '') $incomplet[] = 'Date de facture non renseignée';
    if ($fields['prestataire_check'] == '') $incomplet[] = "Les données du préstataire lié à la demande ne correcspondent pas";
    if ($fields['client_check'] == '') $incomplet[] = "Les informations client ne correspondent pas";
    if ($fields['montant_check'] == '') $incomplet[] = "Les montants des travaux demandés ne correspondent pas";
    if ($fields['technique_check'] == '') $incomplet[] = "Les informations techniques ne correspondent pas";

    return $incomplet;
}
//LOCAL : a passé dans le moteur de regle
function testEligibiliteRemboursement($app_id) {

    //recuperation des variable du formulaire
    $fields = convergence_getAllAppData($app_id);
    $eligible = 1;
   // G::pr($fields);die;
    $key_check = array('bordereauConforme','bordereauPresent','chqpresent','chqtamponne','client_check','facturepresente','prestataire_check','signature','montant_check','technique_check');
// if(testCompletion($fields)) $eligible = 0;
    if(testCkeck($key_check,$fields)) $eligible = 0;
    if($fields['refus'] == 1) $eligible = 0;
    $dateReception = strtotime($fields['dateReception']);
    if(isset($fields['dateFacture']) && $fields['dateFacture'] != ''){
        $dateFacture = strtotime($fields['dateFacture']);
    }else{
        $eligible = 0;
    }
    if($eligible == 1){
        $dateFin = strtotime($fields['dateFin']);
        $dateEmission = strtotime($fields['dateEmission']);
        //$lastmonth = mktime(0, 0, 0, date("m")-1, date("d"),   date("Y"));
        $explodeDateFacture = explode('-', $fields['dateFacture']);
        $dateLimite = mktime(0, 0, 0, $explodeDateFacture[1]+3, $explodeDateFacture[2], $explodeDateFacture[0]);

        if(strtotime($fields['dateFin']) < strtotime($fields['dateFacture']) || strtotime($fields['dateFacture']) < strtotime($fields['dateEmission'])) $eligible = 0;
        if(($dateReception < $dateFacture) || ($dateReception > $dateLimite)) $eligible = 0;
    }
    return $eligible;    
}

//LOCAL : a passé dans le moteur de regle
function testEligibiliteDemande($app_id) {

    //recuperation des variable du formulaire
    $fields = convergence_getAllAppData($app_id);

   //G::pr($fields);
   //die;
    
    $eligible = 1;

    

    if ($fields['devisPresent'] == '0' || $fields['devisConforme'] == '0' || $fields['avisImpotPresent'] == '0' || $fields['avisImpotConforme'] == '0' 
            || $fields['attestationPresent'] =='0' || $fields['attestationConforme'] == '0' || $fields['proprietaire'] == '0' 
            || $fields['justifPresent'] == '0' || $fields['justifConforme'] == '0' || $fields['occupant'] == '0')
            $eligible=0;


    if ($fields['codePostal'] != '') {
        $query = 'SELECT ZIP FROM PMT_VILLE WHERE UID ='.$fields['codePostal'];
        $result = executeQuery($query);
        if ( !isset($result) || !in_array(substr($result[1]['ZIP'],0,2),array('24','33','40','47','64'))) {
                $eligible = 0;
        }
    }
    else
        $eligible = 0;

    if ($fields['maisonIndividuelle'] == '0')
            $eligible = 0;


    if (strtotime(str_replace('/','-',$fields['construite'])) >= mktime(0,0,0,1,1,2000))
    if (intval($fields['construite']) >= 2000){    
        $eligible = 0;
    }


    if ($fields['travauxEligible'] == '0')
            $eligible = 0;


    if (strtotime(str_replace('/','-',$fields['dateSignature'])) < mktime(0,0,0,11,5,2012))
            $eligible = 0;


    if (strtotime(str_replace('/','-',$fields['dateDebutTravaux'])) < time())
            $eligible = 0;


    switch($fields['nbPers']) {
            case '1' : if ($fields['sommeRevenu'] > 23688) $eligible = 0; break;
            case '2' : if ($fields['sommeRevenu'] > 31588) $eligible = 0; break;
            case '3' : if ($fields['sommeRevenu'] > 36538) $eligible = 0; break;
            case '4' : if ($fields['sommeRevenu'] > 40488) $eligible = 0; break;
            case '5' : if ($fields['sommeRevenu'] > 44425) $eligible = 0; break;

    }
    
    
    return $eligible;

}

//LOCAL : a passé dans le moteur de regle
function calculMontantEligible($thematique,$bio) {
    switch($thematique) {
            case '1' : $montant = 800; break;
            case '2' : $montant = 1600; break;
            case '3' : $montant = 500; break;
    }

    if ($bio == '1' && $thematique != '3')
            $montant += 200;
    return $montant;

}
//LOCAL : a passé dans le moteur de regle
function convergence_getCodeChequier($montant) {
    switch($montant) {
            case 0 : $code = 0; break;
            case 800 : $code = 1; break;
            case 1000 : $code = 2; break;
            case 1600 : $code = 3; break;
            case 1800 : $code = 4; break;
            case 500 : $code = 5; break;
    }
    return $code;
}

//LOCAL : recupere le user de la demande
function convergence_getUserOfDemande($app_id) {
    
    $query = 'SELECT ID_USER FROM PMT_DEMANDES WHERE APP_UID = "'.$app_id.'"';
    $result = executeQuery($query);
    if(isset($result)) {
        return $result[1]['ID_USER'];
    }
    else
        return 0;
}


//LOCAL : a transforme dans le moteur de regle
function convergence_getMsgErreur($app_id) {
    
   $fields = convergence_getAllAppData($app_id);
   
   $error = array();
   
   if ($fields['proprietaire'] == '0')
       $error[] = 'Vous n\'êtes pas propriétaire.';

   if ($fields['occupant'] == '0')
       $error[] = 'Vous n\'occupez pas l\'habitation.';


   if ($fields['codePostal'] != '') {
        $query = 'SELECT ZIP FROM PMT_VILLE WHERE UID ='.$fields['codePostal'];

        $result = executeQuery($query);
        
        if ( !isset($result) || !in_array(substr($result[1]['ZIP'],0,2),array('24','33','40','47','64'))) {
                $error[] = 'Votre adresse n\'est pas dans la région Aquitaine.';
        }
   }
        
    
   
    if ($fields['maisonIndividuelle'] == '0')
       $error[] = 'Le type de logement n\'est pas une maison individuelle.';

    
    if($fields['construite'] !='') {
        //if (strtotime(str_replace('/','-',$fields['construite'])) >= mktime(0,0,0,1,1,2000))
        if (intval($fields['construite']) >= 2000)
            $error[] = 'La date de construction n\'est pas postérieure au 1er javier 2000.';
    }
    
    
    if ($fields['travauxEligible'] == '0')
       $error[] = 'Le contrôle de l\'EIE n\'est pas favorable.';

    
    if($fields['dateSignature'] !='') {
        if (strtotime(str_replace('/','-',$fields['dateSignature'])) < mktime(0,0,0,11,5,2012))
        $error[] = 'La date de demande est antérieure au 5 Novembre 2012.';
    }

    //todo c'est pas time() a utiliser
    if($fields['dateDebutTravaux'] !='') {
        if (strtotime(str_replace('/','-',$fields['dateDebutTravaux'])) < time())
        $error[] = 'La date de prévision des travaux est déjà passé.';
    } 
    
    $iserror = 0;
    switch($fields['nbPers']) {
            case '1' : if ($fields['sommeRevenu'] > 23688) $iserror = 1; break;
            case '2' : if ($fields['sommeRevenu'] > 31588) $iserror = 1; break;
            case '3' : if ($fields['sommeRevenu'] > 36538) $iserror = 1; break;
            case '4' : if ($fields['sommeRevenu'] > 40488) $iserror = 1; break;
            case '5' : if ($fields['sommeRevenu'] > 44425) $iserror = 1; break;
    }
    
    if ($iserror == 1)
       $error[] = 'Vos conditions de ressources sont trop importantes pour le nombre de personnes dans le foyer.';

    
    return $error;
    
}

//LOCAL : a transforme dans le moteur de regle
function convergence_getIncompletErreur($app_id) {
    
    $fields = convergence_getAllAppData($app_id);
    
    $incomplet = array();
    
    if ($fields['devisPresent'] == '1' && $fields['devisConforme'] == '0')
        $incomplet[] = 'votre devis n\'est pas conforme.'; 
    elseif ($fields['devisPresent'] == '0')
        $incomplet[] = 'une photocopie de votre devis.'; 
        
    
    if ($fields['avisImpotPresent'] == '1' && $fields['avisImpotConforme'] == '0')
        $incomplet[] = 'votre avis d\'imposition n\'est pas conforme.'; 
    elseif ($fields['avisImpotPresent'] == '0') 
        $incomplet[] = 'une photocopie de votre avis d\'imposition.'; 


    if ($fields['attestationPresent'] == '1' && $fields['attestationConforme'] == '0')
        $incomplet[] = 'votre acte de propriété n\'est pas conforme.'; 
    elseif ($fields['attestationPresent'] == '0')
        $incomplet[] = 'une photocopie de votre acte de propriété.'; 
       
     
    if ($fields['justifPresent'] == '1' && $fields['justifConforme'] == '0')
        $incomplet[] = 'votre justificatif de domicile. n\'est pas conforme.'; 
    elseif ($fields['justifPresent'] == '0')
        $incomplet[] = 'un justificatif de domicile.'; 
     
    
    if ($fields['idPresta'] == false)
        $incomplet[] = 'aucun prestataire séléctionné.';
    
    return $incomplet; 
}
//obsolète
//LOCAL : test l existence d'un presta
function convergence_testPresta($uid) {
    
    if ($uid == '')
        return false;
    else {
        $query = 'SELECT count(*) as NB FROM PMT_PRESTATAIRE WHERE SIRET = '.$uid;
        $result = executeQuery($query);
        if(isset($result) && $result[1]['NB'] > 0) 
            return true;
        else
            return false;
    }
    
}

//OBSOLETE a priori car dans les variable on a les champs _label pour les valeurs
function convergence_getRelationBdd($table,$uid,$returnField) {
    
    if ($uid == '')
        return '';
    else {
        $query = 'SELECT '.$returnField.' FROM '.$table.' WHERE UID = '.$uid;        
        $result = executeQuery($query);
        if(isset($result)) 
            return $result[1][$returnField];
        else
            return '';
    }
    
}


//OBSOLETE
function convergence_makeChequeProduction() {
    
    $csv = array();
    $entete = array('Id','nom','prenom','voie','type voie','rue','code postal','ville','montant');
    
    $query = 'SELECT APP_UID,NOM,PRENOM,NUMVOIE,TYPEVOIE,NOMVOIE,CODEPOSTAL,VILLE,MONTANT FROM PMT_DEMANDES WHERE STATUT = 2';        
    $result = executeQuery($query);
    if(isset($result)) {
        $fp = fopen('file.csv', 'w');
        fputcsv($fp, $entete);
        foreach ($result as $demande) {
            fputcsv($fp, $demande);
        }
        fclose($fp);
    }
    
    //var_dump($csv);
    
}

//OBSOLETE
function convergence_importNumeroCheque($app_id) {
    
    $query = 'SELECT C.CON_ID, C.CON_VALUE, AD.DOC_VERSION FROM APP_DOCUMENT AD, CONTENT C
   WHERE AD.APP_UID="'.$app_id.'" AND AD.APP_DOC_TYPE="INPUT" AND AD.APP_DOC_STATUS="ACTIVE"
       AND AD.APP_DOC_UID=C.CON_ID AND C.CON_CATEGORY="APP_DOC_FILENAME" AND C.CON_VALUE<>""';
    
    $result = executeQuery($query);
    if (is_array($result) and count($result) > 0) {

        $filePath = PATH_DOCUMENT.$app_id.'/'.$result[1]['CON_ID'].'_'.$result[1]['DOC_VERSION'].'.'.pathinfo($result[1]['CON_VALUE'], PATHINFO_EXTENSION);
        $row = 0;
        if (($handle = fopen($filePath, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if ($row > 0) {
                    if ($data[9] != '') {
                        $query2 = 'INSERT INTO PMT_CHEQUES (UID,NUM,ANNULE,ID_DEMANDE) VALUES ("'.G::generateUniqueID().'","'.$data[9].'",0,"'.$data[0].'")';
                        $result2 = executeQuery($query2);
                        convergence_changeStatut($data[0],6);
                    }
                }
                $row++;
            }
            fclose($handle);
        }
    }

}

//OBSOLETE ?
function convergence_loadInfoDemande($demandeID) {    
    $query = 'SELECT * FROM PMT_DEMANDES WHERE APP_UID="'.$demandeID.'"';
    $result = executeQuery($query);
    if (is_array($result)) {
        return $result[1];
    }
    
} 
//OBSOLETE ?
function convergence_loadInfoListeProd($demandeID) {    
    $query = 'SELECT * FROM PMT_LISTE_PROD WHERE APP_UID="'.$demandeID.'"';
    $result = executeQuery($query);
    if (is_array($result)) {
        return $result[1];
    }
    
} 
//OBSOLETE ?
function convergence_loadInfoPresta($prestaID) {    
    $query = 'SELECT * FROM PMT_PRESTATAIRE WHERE SIRET='.$prestaID;
    $result = executeQuery($query);
    if (is_array($result)) {
        return $result[1];
    }
    
} 

/*** OBSOLETE
 * Génère la liste des champs pour un INSERT
 */
function convergence_makeINSERT($datas,$fields){
    foreach($datas as $k => $v){
        /*$type = gettype($v);
        if('double' == $type || 'integer' == $type){
            $datas[$k] = intval($v);
        }else{
            $datas[$k] =  mysql_real_escape_string($v);
        }*/
         $datas[strtoupper($k)] = $v;
    }
    
    $res = array_intersect_key($datas,$fields);
    $ret[0] = implode(',',array_keys($res));
    $values = implode('","',array_values($res));
    $ret[1] = '"'.$values.'"';     
    return $ret;
}
//OBSOLETE
function convergence_makeUPDATE($datas,$fields){
    foreach($datas as $k => $v){
        /*$type = gettype($v);
        if('double' == $type || 'integer' == $type){
            $datas[$k] = intval($v);
        }else{
            $datas[$k] =  mysql_real_escape_string($v);
        }*/
        $datas[strtoupper($k)] = $v;
    }    
    $res = array_intersect_key($datas,$fields);          
    $set = array();
    foreach($res as $k => $v){
        $set[] = $k.'="'.$v.'"';
    }
    $ret = implode(', ',$set);
    return $ret;
}
//OBSOLETE
function convergence_insertPMT($app_id,$table,$array_key){    
    try{      
        $fields = convergence_getAllAppData($app_id);
        $fields['USR_UID'] = $fields['USER_LOGGED'];
        $fields[$table.'_APP_UID'] = $fields['APPLICATION'];
        $array_insert = array_flip($array_key);    
        $insert = convergence_makeINSERT($fields,$array_insert);
        $query = 'INSERT INTO '.$table.'('.$insert[0].') VALUES ('.$insert[1].')';
        $result = executeQuery($query);           
    }
    catch (Exception $e) {
        var_dump($e);
        die();
    }     
    return $result;    
   
}
//OBSOLETE
function convergence_updatePMT($app_id,$table,$wherefields,$array_key){
   try{     
        $fields = convergence_getAllAppData($app_id);
        $fields[$table.'_APP_UID'] = $fields['APPLICATION'];
       /*$array_insert = array('RAISONSOCIALE','SIRET','NUM_PRESTA',
            'ACTIVITE','DATE_CREATION','DATE_CONV','ADRESSE1','ADRESSE2','ADRESSE3',
            'CODEPOSTAL','VILLE','TELEPHONE','TELECOPIE','CIVILITE_CONTACT','NOM_CONTACT','PRENOM_CONTACT',
            'TEL_CONTACT','TEL_PORTABLE' ,'MAIL','DOMICILIATION','CODE_BANQUE','CODE_GUICHET','NUM_COMPTE','CLE_RIB','USR_UID');*/
        $array_insert = array_flip($array_key);
        $set = convergence_makeUPDATE($fields,$array_insert);
        $query = 'UPDATE '.$table.' SET '.$set.' WHERE '.$wherefields.' = "'.$fields['USER_LOGGED'].'"';     
        $result = executeQuery($query);
    }
    catch (Exception $e) {
        var_dump($e);
        die();
    }     
    return $result;      
}
//OBSOLETE
function convergence_test_uid_PMT_PARTENAIRE($uid) {   
    if ($uid == ''){
        return false;
    }    
    else {
        $query = 'SELECT count(*) as NB FROM PMT_PARTENAIRE WHERE USR_UID = "'.$uid.'"';        
        $result = executeQuery($query);
        if(isset($result) && $result[1]['NB'] > 0){           
            return true;
        }else{
            return false;
        }
    }
}
//OBSOLETE
function convergence_get_PMT_DATA($table,$field,$uid) {   
    $query = 'SELECT * FROM '.$table.' WHERE '.$field.' = "'.$uid.'"';
    $result = executeQuery($query);
    return $result;
}


//LOCAL
function convergence_getCodeRNE($uid){
    $query = 'SELECT count(*) as nb FROM PMT_ETABLISSEMENT WHERE RNE = "'.$uid.'"';
    $result = executeQuery($query);
    if(isset($result) && $result[1]['NB'] > 0){           
        return true;
    }else{
        return false;
    }
}


/* * ***
 *  Fonction récupérant le nombre de dossier traité pour un export
 *
 *  $statut     @integer    le statut des dossiers traités
 *  $groupby      @string     le trie voulu
 */
//LOCALE mais doit etre GLOBAL
function convergence_countCase($statut) {
    //$query = 'SELECT COUNT(APP_UID) as NB, THEMATIQUE_LABEL FROM PMT_DEMANDES WHERE STATUT = '.$statut.' OR (STATUT = 6 AND REPRODUCTION_CHQ = "O") GROUP BY THEMATIQUE';
    $query = 'SELECT APP_UID, BIOSOURCE, THEMATIQUE, THEMATIQUE_LABEL FROM PMT_DEMANDES WHERE STATUT = '.$statut.' OR (STATUT = 6 AND REPRODUCTION_CHQ = "O")';
    $res = executeQuery($query);
    
    $count = array();
    if(count($res)){
        $msg['NOTHING'] = 0;
        $msg['HTML'] = 'Vous allez lancer la production de : <br />';
        foreach($res as $thema){
            
            $count[$thema['THEMATIQUE']]['label'] = $thema['THEMATIQUE_LABEL'];
            $count[$thema['THEMATIQUE']]['total'] ++;
            if ($thema['BIOSOURCE'] == 1)
                $count[$thema['THEMATIQUE']]['bio'] ++;
        }
         
        foreach($count as $tab){
            (intval($tab['total']) > 1) ? $s = 's' : $s = '';
            $nb = $tab['total'];
            $th = $tab['label'];
            $bio = $tab['bio'];
            
            if (intval($bio) == 1) 
                $bioLabel = ", dont $bio bio-sourcé"; 
            else {
                (intval($bio) > 0) ? $bioLabel = ", dont $bio bio-sourcés" : $bioLabel = '';
            }
            
            $msg['HTML'] .= "-  $nb dossier$s pour la thématique :  $th $bioLabel<br />" ;
        }
        
        $query = 'SELECT COUNT(APP_UID) as NB FROM PMT_DEMANDES WHERE STATUT = 6 AND REPRODUCTION_CHQ = "O"';
        $res = executeQuery($query);
        if(count($res) && isset($res[1]['NB'])){
             (intval($res[1]['NB']) > 1 ) ? $s = 's' : $s = '';
             $msg['HTML'] .= 'dont '.$res[1]['NB'].' reproduction'.$s;
        }
        
    }else{
        $msg['HTML'] = "Ancun dossier à produire ! Veuillez annuler l'opération";
        $msg['NOTHING'] = 1;
    }
     //$msg = 'Ancun dossier à produire! Veuillez annuler l\'opération';
     //$msg = $msg;
    return $msg;
}
/* * ***
 *  Fonction récupérant le nombre de dossier traité pour un export
 *
 *  $statut     @integer    le statut des dossiers traités
 *  $groupby      @string     le trie voulu
 */
//LOCALE mais doit etre GLOBAL
function convergence_countCaseRmb($statut) {
    $query = 'SELECT count(R.APP_UID) AS NB, D.THEMATIQUE_LABEL AS THEMATIQUE_LABEL FROM PMT_REMBOURSEMENT AS R, PMT_DEMANDES AS D WHERE R.STATUT = '.$statut.' AND R.ID_DEMANDE = D.NUM_DOSSIER AND D.STATUT <> 0 GROUP BY D.THEMATIQUE_LABEL';
    $res = executeQuery($query);
    if(count($res)){
        $msg['NOTHING'] = 0;
        $msg['HTML'] = 'Vous allez lancer le remboursement : <br />';
        foreach($res as $thema){
            (intval($thema['NB']) > 1) ? $s = 's' : $s = '';
            $nb = $thema['NB'];
            $th = $thema['THEMATIQUE_LABEL'];
            $msg['HTML'] .= "-  $nb dossier$s pour la thématique :  $th <br />" ;
        }

    }else{
        $msg['HTML'] = "Ancun dossier à rembourser ! Veuillez annuler l'opération";
        $msg['NOTHING'] = 1;
    }     
    return $msg;
}

//GLOBAL à remplacer par celle du dessous //obsolete
function convergence_creatNumDossierRmb($app_id){
    try{
        $query = 'SELECT APP_NUMBER FROM PMT_LISTE_RMBT WHERE APP_UID = \''.$app_id.'\'';       
        $result = executeQuery($query);
        $data['NUM_DOSSIER'] = $result[1]['APP_NUMBER'];
        convergence_updateDemande($app_id, $data);
    }
    catch (Exception $e){
        var_dump($e);
        die();
    }
     return $result[1]['APP_NUMBER'];
}

//OBSOLETE
function convergence_UpdateHidden($table,$field,$value){
     try{         
        $query = 'UPDATE '.$table.' SET HIDDEN=1 WHERE '.$field.' = "'.$value.'"';     
        $result = executeQuery($query);
    }
    catch (Exception $e) {
        var_dump($e);
        die();
    }     
    return $result;      
}

//LOCAL
function convergence_makeExportPresta($fileName) {
    
    $csv = array();
    $entete = array('app_uid','app_number','activite','adresse 1',
    'adresse 2','adresse 3','civilite contact','civilite contact label',
    'cle RIB','code banque','code_guichet','date convention',
    'date creation','domiciliation','mail','nom contact','numero de compte',
    'numero prestataire','partenaire uid','pmt_partenaire_connection',
    'prenom contact','raison sociale','siret','telecopie','telephone',
    'telephone contact','telephone portable','etat','etat label','cp',
    'ville','affiliation');
    
    $query = 'SELECT * FROM PMT_PRESTATAIRE';        
    $result = executeQuery($query);
    if(isset($result)) {
        $fp = fopen('/var/tmp/'.$fileName.'.csv', 'w');
        fputcsv($fp, $entete);
        foreach ($result as $presta) {
            fputcsv($fp, $presta);
        }
        fclose($fp);
    }
    
}



// GENERATE EXCEL
function generateExcel($app_uid,$num_dossier){
		
	// CREATE FOLDERS
	
  	$Folder="SELECT F.FOLDER_UID FROM APP_FOLDER AS F
	      			 WHERE F.FOLDER_NAME='Production' 
	    			"; 
	$FolderUid=executeQuery($Folder);
	if(sizeof($FolderUid)==0)
	{
		require_once ("classes/model/AppFolder.php");    
	   	 	$oPMFolder = new AppFolder ();
	   	 	$UIDFolderCreated=$oPMFolder->createFolder('Production',PATH_DOCUMENT);
	    	$UID=$UIDFolderCreated['folderUID'];
	}
	else
		$UID = $FolderUid[1]['FOLDER_UID'];
		
	// Reimboursement 
	$query = "SELECT * FROM PMT_REMBOURSEMENT ";
	$aDatos = executeQuery($query);
	
	// TEMPLATE EXCEL
  	$test='<h2>LISTE DES PRODUCTIONS</h2>';
	$test.='<table cellspacing="0" cellpadding="0">';
 	$test.='<tr style= "border: 2px solid #009ADF;">';
 	$test.='<th style= "background-color: #009ADF; color: #FFFFFF; border-bottom: 2px solid #009ADF;">NUMBER</th>';
 	$test.='<th style= "background-color: #009ADF; color: #FFFFFF; border-bottom: 2px solid #009ADF;">ADDRESSE</th>';
 	$test.='<th style= "background-color: #009ADF; color: #FFFFFF; border-bottom: 2px solid #009ADF;">BORDEREAUCONFORME</th>';
 	$test.='<th style= "background-color: #009ADF; color: #FFFFFF; border-bottom: 2px solid #009ADF;">BORDEREAUPRESENT</th>';
 	$test.='<th style= "background-color: #009ADF; color: #FFFFFF; border-bottom: 2px solid #009ADF;">CHQPRESENT</th>';
 	$test.='<th style= "background-color: #009ADF; color: #FFFFFF; border-bottom: 2px solid #009ADF;">CHQTAMPONNE</th>';
 	$test.='<th style= "background-color: #009ADF; color: #FFFFFF; border-bottom: 2px solid #009ADF;">CLIENT_CHECK</th>';
 	$test.='<th style= "background-color: #009ADF; color: #FFFFFF; border-bottom: 2px solid #009ADF;">DATEENCAISSEMENT</th>';
 	$test.='<th style= "background-color: #009ADF; color: #FFFFFF; border-bottom: 2px solid #009ADF;">DATEFACTURE</th>';
 	$test.='<th style= "background-color: #009ADF; color: #FFFFFF; border-bottom: 2px solid #009ADF;">DATE_TRAVAUX</th>';
 	$test.='<th style= "background-color: #009ADF; color: #FFFFFF; border-bottom: 2px solid #009ADF;">FACTUREPRESENTE</th>';
 	$test.='<th style= "background-color: #009ADF; color: #FFFFFF; border-bottom: 2px solid #009ADF;">MONTANT_CHECK</th>';
 	$test.='<th style= "background-color: #009ADF; color: #FFFFFF; border-bottom: 2px solid #009ADF;">MONTANT_CHQ</th>';
 	$test.='<th style= "background-color: #009ADF; color: #FFFFFF; border-bottom: 2px solid #009ADF;">MONTANT_HT</th>';
 	$test.='<th style= "background-color: #009ADF; color: #FFFFFF; border-bottom: 2px solid #009ADF;">MONTANT_TTC</th>';
 	$test.='<th style= "background-color: #009ADF; color: #FFFFFF; border-bottom: 2px solid #009ADF;">MONTANT_TVA</th>';
 	$test.='<th style= "background-color: #009ADF; color: #FFFFFF; border-bottom: 2px solid #009ADF;">MOTIF</th>';
 	$test.='<th style= "background-color: #009ADF; color: #FFFFFF; border-bottom: 2px solid #009ADF;">NOM</th>';
 	$test.='<th style= "background-color: #009ADF; color: #FFFFFF; border-bottom: 2px solid #009ADF;">NUM_PRESTA</th>';
 	$test.='<th style= "background-color: #009ADF; color: #FFFFFF; border-bottom: 2px solid #009ADF;">P_NAME</th>';
 	$test.='<th style= "background-color: #009ADF; color: #FFFFFF; border-bottom: 2px solid #009ADF;">PRENOM</th>';
 	$test.='<th style= "background-color: #009ADF; color: #FFFFFF; border-bottom: 2px solid #009ADF;">PRESTATAIRE_CHECK</th>';
 	$test.='<th style= "background-color: #009ADF; color: #FFFFFF; border-bottom: 2px solid #009ADF;">REFUS</th>';
 	$test.='<th style= "background-color: #009ADF; color: #FFFFFF; border-bottom: 2px solid #009ADF;">SIGNATURE</th>';
 	$test.='<th style= "background-color: #009ADF; color: #FFFFFF; border-bottom: 2px solid #009ADF;">SIRET</th>';
 	$test.='<th style= "background-color: #009ADF; color: #FFFFFF; border-bottom: 2px solid #009ADF;">SOCIAL</th>';
 	$test.='</tr>';
  	$test.='</table>';
 	$test.='<table cellspacing="0" cellpadding="0">';
 	foreach($aDatos As $row)
 	{
 		# Encaissement Dates
  		if($row['DATEENCAISSEMENT'] != ''){
  			$dateEncaissement = explode ( '-', $row['DATEENCAISSEMENT'] );      
  			if( strlen($dateEncaissement[2]) < 3)
	  			$encaisssementDate = $dateEncaissement [1] . '/' . $dateEncaissement [2] . '/' . $dateEncaissement [0];
	  		else
	  			$encaisssementDate = $dateEncaissement [1] . '/' . $dateEncaissement [0] . '/' . $dateEncaissement [2];
	  		$row['DATEENCAISSEMENT'] = $encaisssementDate;
  		}  				  
  		# end Encaisssement Dates
  	
  		# Facture Dates
  		if($row['DATEFACTURE'] != ''){
  			$dateFacture = explode ( '-', $row['DATEFACTURE'] );
  			if( strlen($dateFacture[2])<  3 )      
	  			$factureDate = $dateFacture [1] . '/' . $dateFacture [2] . '/' . $dateFacture [0];
	  		else
	  			$factureDate = $dateFacture [1] . '/' . $dateFacture [0] . '/' . $dateFacture [2];
	  		$row['DATEFACTURE'] = $factureDate;
  		}  				  
  		# end Facture Dates
  		
  		# Travaux Dates
  		if($row['DATE_TRAVAUX'] != ''){
  			$dateTravaux = explode ( '-', $row['DATE_TRAVAUX'] ); 
  			if(strlen($dateTravaux[2]) < 3 )     
	  			$travauxDate = $dateTravaux [1] . '/' . $dateTravaux [2] . '/' . $dateTravaux [0];
	  		else
	  			$travauxDate = $dateTravaux [1] . '/' . $dateTravaux [0] . '/' . $dateTravaux [2];
	  		
	  		$row['DATE_TRAVAUX'] = $travauxDate;
  		}  				  
  		# end Travaux Dates
  		
 		$test.='<tr style= "border: 1px solid #000000; border-bottom: 2px solid #009ADF; border-right: 2px solid #009ADF;">';
 		$test.='<td align="center">'.$row['APP_NUMBER'].'</td>';
 		$test.='<td>'.$row['ADDRESSE'].'</td>';
 		$test.='<td>'.$row['BORDEREAUCONFORME'].'</td>';
 		$test.='<td>'.$row['BORDEREAUPRESENT'].'</td>';
 		$test.='<td>'.$row['CHQPRESENT'].'</td>';
 		$test.='<td>'.$row['CHQTAMPONNE'].'</td>';
 		$test.='<td>'.$row['CLIENT_CHECK'].'</td>';
 		$test.='<td>'.$row['DATEENCAISSEMENT'].'</td>';
 		$test.='<td>'.$row['DATEFACTURE'].'</td>';
 		$test.='<td>'.$row['DATE_TRAVAUX'].'</td>';
 		$test.='<td>'.$row['FACTUREPRESENTE'].'</td>';
 		$test.='<td>'.$row['MONTANT_CHECK'].'</td>';
 		$test.='<td>'.$row['MONTANT_CHQ'].'</td>';
 		$test.='<td>'.$row['MONTANT_HT'].'</td>';
 		$test.='<td>'.$row['MONTANT_TTC'].'</td>';
 		$test.='<td>'.$row['MONTANT_TVA'].'</td>';
 		$test.='<td>'.$row['MOTIF'].'</td>';
 		$test.='<td>'.$row['NOM'].'</td>';
 		$test.='<td>'.$row['NUM_PRESTA'].'</td>';
 		$test.='<td>'.$row['P_NAME'].'</td>';
 		$test.='<td>'.$row['PRENOM'].'</td>';
 		$test.='<td>'.$row['PRESTATAIRE_CHECK'].'</td>';
 		$test.='<td>'.$row['REFUS'].'</td>';
 		$test.='<td>'.$row['SIGNATURE'].'</td>';
 		$test.='<td>'.$row['SIRET'].'</td>';
 		$test.='<td>'.$row['SOCIAL'].'</td></tr>';
 		
 	}
 	$test.='</table>';
 	
 	$pathDocument = PATH_DOCUMENT."Production";
	//Verify
	
	if(!file_exists($pathDocument))
	{
		G::mk_dir ($pathDocument);
	}	 
	 $xls_filename = PATH_DOCUMENT."Production/Reimboursement_".$num_dossier .".xls";
	  
	  $fp = fopen ($xls_filename, "w");
	   if (!is_resource($fp)) {  
		die("Failed to create $xls_filename");  
	}
	  fwrite($fp, $test);  
	  fclose($fp);
	  
	  // SAVE EXCEL IN APP_DOCUMENT
	  $form['APPDOCUID']='-1';
		$aFields = array (
		  'APP_UID' => $_SESSION ['APPLICATION'], 
		  'DEL_INDEX' => $_SESSION ['INDEX'], 
		  'USR_UID' => $_SESSION ['USER_LOGGED'], 
		  'DOC_UID' => '138507476518d6da82f0e97031493508', 
		  'APP_DOC_TYPE' => 'INPUT', 
		  'APP_DOC_CREATE_DATE' => date ( 'Y-m-d H:i:s' ), 
		  'APP_DOC_COMMENT' => 'EXCEL REIMBOURSEMENT', 
		  'APP_DOC_TITLE' => 'Excel Reimboursement: '.$num_dossier,
		  'FOLDER_UID'          => $UID, 
		  'APP_DOC_FILENAME' => 'Reimboursement_'.$num_dossier.'.xls' 
		  );
		  
		$oAppDocument = new AppDocument ();
		$oAppDocument->create ( $aFields );

		$res=true;
		header("Content-Type: text/html");
		$returnvkh = array('success' => $res);
		echo json_encode($returnvkh);
}


