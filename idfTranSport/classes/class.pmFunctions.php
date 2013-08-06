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

