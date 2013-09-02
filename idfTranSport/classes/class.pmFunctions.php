<?php
/**
 * class.idfTranSport.pmFunctions.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.
 * *
 */

////////////////////////////////////////////////////
// idfTranSport PM Functions
//
// Copyright (C) 2007 COLOSA
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////

function idfTranSport_getMyCurrentDate()
{
	return G::CurDate('Y-m-d');
}

function idfTranSport_getMyCurrentTime()
{
	return G::CurDate('H:i:s');
}

function idfTranSport_getCodeOperDemande($codeEnvoi) {
    $info = array();
    $sqlCodeOp = 'SELECT ENVOIE_CODE_OPER FROM PMT_TYPE_ENVOI WHERE CODE_ENVOI="' . $codeEnvoi . '"';
    $resCodeOp = executeQuery($sqlCodeOp);

    $info['CodeOper'] = $resCodeOp[1]['ENVOIE_CODE_OPER'];

    $sqlTh = 'SELECT LABEL FROM PMT_LISTE_OPER WHERE CODE_OPER <> "CH" AND NUM_OPER ="' . $info['CodeOper'] . '"';
    $rTh = executeQuery($sqlTh);
    $info['Thematique'] = $rTh[1]['LABEL'];

    return $info;
}

function getQueryForListDemandeProd($liste) {

    $query = 'SELECT D.APP_UID, D.NUM_DOSSIER, D.NUM_DOSSIER_COMPLEMENT, D.FC_NOM_CLUB as ENTITE, D.REPRODUCTION_CHQ, D.NPAI, TC.LABEL, C.BCONSTANTE
        FROM PMT_DEMANDES AS D INNER JOIN PMT_TYPE_CHEQUIER AS TC ON (TC.CODE_CD = D.CODE_CHEQUIER) INNER JOIN PMT_CHEQUES AS C ON (C.NUM_DOSSIER = D.NUM_DOSSIER)
        WHERE D.NUM_DOSSIER IN (
            ' . implode(',', $liste) . '
        ) AND D.STATUT=6 GROUP BY C.BCONSTANTE ORDER BY D.NUM_DOSSIER';

    return $query;
}
// for action exportInboxNpai.php with adr modify
function idf_exportInboxNpai_callback($res) {
    //$listeApp_uid = array();
    foreach ($res as $k => $npai)
    {
        $query = 'SELECT max(HLOG_DATECREATED) as HLOG_DATECREATED FROM PMT_HISTORY_LOG WHERE HLOG_APP_UID="' . $npai['APP_UID'] . '" AND HLOG_ACTION LIKE "Retour de production%"';
        $resultDate = executeQuery($query);
        $qSetPnd = 'SELECT max(HLOG_DATECREATED) as HLOG_DATECREATED FROM PMT_HISTORY_LOG WHERE HLOG_APP_UID="' . $npai['APP_UID'] . '" AND HLOG_ACTION="Classer en PND"';
        $rSetPnd = executeQuery($qSetPnd);
        if (isset($resultDate[1]['HLOG_DATECREATED']) && $resultDate[1]['HLOG_DATECREATED'] != '' && isset($rSetPnd[1]['HLOG_DATECREATED']) && $rSetPnd[1]['HLOG_DATECREATED'] != '')
        {
            /* up */
            $qPointLivraison = 'SELECT PMT_ETABLISSEMENT.APP_UID AS LIV FROM PMT_ETABLISSEMENT, PMT_DEMANDES WHERE PMT_ETABLISSEMENT.FP_CODE = PMT_DEMANDES.FC_ID_LIV AND PMT_DEMANDES.APP_UID ="' . $npai['APP_UID'] . '" AND PMT_ETABLISSEMENT.STATUT = 1';
            $rPtLiv = executeQuery($qPointLivraison);
            if (isset($rPtLiv[1]['LIV']) && !empty($rPtLiv[1]['LIV']))
            {
                $idLiv = $rPtLiv[1]['LIV'];
            }
            else
                $idLiv = '0';
            $query2 = 'SELECT count(*) as NB, HLOG_APP_UID FROM PMT_HISTORY_LOG WHERE HLOG_APP_UID IN("' . $npai['APP_UID'] . '","' . $idLiv . '") AND HLOG_DATECREATED > "' . $resultDate[1]['HLOG_DATECREATED'] . '" AND HLOG_DATECREATED < "' . $rSetPnd[1]['HLOG_DATECREATED'] . '" AND HLOG_ACTION="Modification de l\'adresse"';
            /* end up */
            $result2 = executeQuery($query2);
            if ($result2[1]['NB'] > 0)
            {
                //$listeApp_uid[] = $npai['APP_UID'];
                unset($res[$k]['APP_UID']);
            }
            else
            {
                unset($res[$k]);
            }
        }
        else
        {
            unset($res[$k]);
        }
    }

    return $res;
}

// for the pm function
// Permet de modifier l'adresse d'un point de livraison d'une demande
// retourne le nouvel app_uid, l'action et le statut pour l'historyLog
function idf_modifyAdresseofDemande_callback($case_uid_demande) {
    $ret = array();
    $ret['action'] = "Modification de l'adresse";
    $ret['status'] = 200;
    $query = 'select FC_ID_LIV as point_liv from PMT_DEMANDES where APP_UID = "' . $case_uid_demande . '"';
    $res = executeQuery($query);
    if (isset($res[1]['point_liv']) && !empty($res[1]['point_liv']))
    {
        $q = 'select APP_UID from PMT_ETABLISSEMENT where FP_CODE = ' . $res[1]['point_liv'];
        $r = executeQuery($q);
        if (isset($r[1]['APP_UID']) && !empty($r[1]['APP_UID']))
        {
            $ret['uid'] = $r[1]['APP_UID'];
            return $ret;
        }
        else
            return false;
    }
    else
        return false;
}

function idf_classerNPAI_callback($item) {
    $newAdresse = 0;
    $qPointLivraison = 'SELECT PMT_ETABLISSEMENT.APP_UID AS LIV FROM PMT_ETABLISSEMENT, PMT_DEMANDES WHERE PMT_ETABLISSEMENT.FP_CODE = PMT_DEMANDES.FC_ID_LIV AND PMT_DEMANDES.APP_UID ="' . $item['APP_UID'] . '" AND PMT_ETABLISSEMENT.STATUT = 1';
    $rPtLiv = executeQuery($qPointLivraison);
    if (isset($rPtLiv[1]['LIV']) && !empty($rPtLiv[1]['LIV']))
    {
        $idLiv = $rPtLiv[1]['LIV'];
    }
    else
        $idLiv = '0';

    $query = 'SELECT max(HLOG_DATECREATED) as HLOG_DATECREATED FROM PMT_HISTORY_LOG WHERE HLOG_APP_UID="' . $item['APP_UID'] . '" AND HLOG_ACTION LIKE "Retour de production%"';
    $result = executeQuery($query);
    //si j'ai une date de retour de prod, je regarde si je n'ai pas de modif d'adresse apres
    if (isset($result[1]['HLOG_DATECREATED']) && $result[1]['HLOG_DATECREATED'] != '')
    {
        $query2 = 'SELECT count(*) as NB FROM PMT_HISTORY_LOG WHERE HLOG_APP_UID IN("' . $item['APP_UID'] . '","' . $idLiv . '") AND HLOG_DATECREATED > "' . $result[1]['HLOG_DATECREATED'] . '" AND HLOG_ACTION="Modification de l\'adresse"';
        $result2 = executeQuery($query2);

        if ($result2[1]['NB'] > 0)
            $newAdresse = 1;        
    }
    return $newAdresse;
}

function idf_actionDeleteCases_callback($item) {
    $appUid = $item['APP_UID'];
    $result = array();
    $result['check'] = true;
    $sqlGetEtabFromAppUid = "SELECT FP_CODE FROM PMT_ETABLISSEMENT WHERE APP_UID = '" . $appUid . "'";
    $resultGetEtab = executeQuery($sqlGetEtabFromAppUid);
    if (isset($resultGetEtab[1]['FP_CODE']) && $resultGetEtab[1]['FP_CODE'] != '')
    {
        $codeEtab = $resultGetEtab[1]['FP_CODE'];
        $sqlCheckEtab = "SELECT NUM_DOSSIER FROM PMT_DEMANDES WHERE (FC_ID_LIV = '" . $codeEtab . "' OR FC_CODE_CLUB = '" . $codeEtab . "' OR FC_CODE_LIGUE = '" . $codeEtab . "') AND STATUT != '0' AND STATUT != '999'";
        $resultCheckEtab = executeQuery($sqlCheckEtab);
        if (isset($resultCheckEtab[1]['NUM_DOSSIER']) && $resultCheckEtab[1]['NUM_DOSSIER'] != '')
        {
            $result['check'] = false;
            $result['messageInfo'] = "Le dossier " . $item['NUM_DOSSIER'] . " n'a pas été supprimé.";
        }
        else
        {
            $result['messageInfo'] = "Le dossier " . $item['NUM_DOSSIER'] . " a été correctement supprimé.";
        }
    }
    return $result;
}

