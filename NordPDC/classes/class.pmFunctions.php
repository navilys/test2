<?php
/**
 * class.NordPDC.pmFunctions.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.
 * *
 */

////////////////////////////////////////////////////
// NordPDC PM Functions
//
// Copyright (C) 2007 COLOSA
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////

function NordPDC_getMyCurrentDate()
{
    return G::CurDate('Y-m-d');
}

function NordPDC_getMyCurrentTime()
{
    return G::CurDate('H:i:s');
}

/* //LOCAL
  function Nord_PDC_getChequierDemande($app_id){
  $codeCheq = 0;
  $fields = convergence_getAllAppData($app_id);
  $class = array('BCLA1PREM','ECLA2PREM','GCLA3PREM','ICLA4PREM');
  if($fields['CODE_OPERATION'] == 392){
  if($fields['BOURSE'] == 1 && in_array($fields['CLASSE'],$class)){
  switch ($fields['FILIERE']){
  case 0:
  $codeCheq = 3;
  break;
  case 1:
  $codeCheq = 4;
  break;
  case 2:
  $codeCheq = 2;
  break;

  default:
  break;
  }

  }elseif($fields['BOURSE'] == 1){
  $codeCheq = 2;
  }else{
  $codeCheq = 1;
  }
  }
  if($fields['CODE_OPERATION'] == 393){
  $codeCheq = 9;
  }
  return $codeCheq;
  }
 */

function Nord_PDC_createUser($app_id){
     $fields = convergence_getAllAppData($app_id);
     G::pr($fields);
     $isCreate = PMFCreateUser($fields['EMAIL'], $fields['PASSWORD'], $fields['FIRSTNAME'], $fields['LASTNAME'], $fields['EMAIL'], 'Etablissements');
     if($isCreate == 0){
         $msg = 'Impossible de créer cet utilisateur, le mail renseigné est déjà utilisé par ...';
         return 0;
     }
     $uQuery = 'SELECT APP_UID FROM USERS WHERE USR_USERNAME ="'.$fields['EMAIL'].'"';
     $res = executeQuery($uQuery);
     if(isset($res) && count($res) > 0)
         $usr_uid = $res[1]['APP_UID'];
     else
         return 0;
     //en suspend, le temps que Gary nous explique pour l'ajout d'un champs direct dans la table user ou si on exploite une table mm

     return $usr_uid;
}

/***** GLOBAL
 * Recherche toutes les demandes de statut X dont la date de creation date de plus de Y jours
 *
 * $statut  @int    le statut recherché lié à la table PMT_STATUT
 * $nbJour    @int    le nombre de jour entre la création de la demande et l'instant 't'
 */
function convergence_getCronDemande($statut,$nbJour){
    $sql = 'SELECT D.* FROM PMT_DEMANDES AS D INNER JOIN APPLICATION AS A  ON (D.APP_UID = A.APP_UID) WHERE D.STATUT = '.intval($statut).' AND DATEDIFF(NOW(),A.APP_CREATE_DATE) > '.intval($nbJour);
    $res = executeQuery($sql);

    return $res;
}


/**** LOCAL
 *
 *
 *
 */
function Nord_PDC_cronDelayDemandes($app_id){
     $fields = convergence_getAllAppData($app_id);
     $tab = array();
     //$dossiers = convergence_getCronDemande(12,7);
     $dossiers = convergence_getCronDemande(12,0); // testMode
     if(isset($dossiers) && count($dossiers) > 0){
         foreach ($dossiers as $demande){
             $tab[$demande['RNE']][] = $demande['NUM_DOSSIER'];
         }
     }
     return $tab;
 }

/**** LOCAL
 *
 *
 *
 */
function Nord_PDC_getLabelOfOperation($uid){
     $query = 'SELECT LABEL FROM PMT_TYPE_CHEQUIER WHERE CODE_CD ="'.$uid.'"';
     $res = executeQuery($query);
     if(isset($res) && count($res) > 0)
         return $res[1]['LABEL'];
     else
         return 0;

}

function Nord_PDC_getEtablissementFromRNE($rneCode)
 {
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

