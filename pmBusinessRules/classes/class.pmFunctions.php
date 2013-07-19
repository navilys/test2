<?php
/**
 * class.pmBusinessRules.pmFunctions.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2013 Colosa Inc.
 * *
 */

////////////////////////////////////////////////////
// pmReports PM Functions
//
// Copyright (C) 2013 COLOSA
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////

function PMBRLoadGlobalVariables () {
    require_once PATH_PM_BUSINESS_RULES . 'classes' . PATH_SEP . 'model' . PATH_SEP . 'GlobalFields.php';
    $oGlobalFields = new GlobalFields();
    $data = $oGlobalFields->getAll();

    $globalFields = array();

    foreach ($data as $value) {
        $globalFields[$value['GLOBAL_UID']] = $value['GLOBAL_VALUE'];
    }

    return $globalFields;
}

function PMBRLoadFilesPmrl () {
    $filesPmrl = glob(PATH_FILES_BUSINESS_RULES . SYS_SYS . PATH_SEP . 'pmrl' . PATH_SEP . '*.pmrl');
    return $filesPmrl;
}

function PMBREvaluate ($nameFile) {
    require_once PATH_PM_BUSINESS_RULES. 'classes/model/RuleSet.php';
    require_once PATH_PM_BUSINESS_RULES. 'classes/class.RuleReader.php';

    //Load the case
    G::LoadClass("case");
    $oCases = new Cases();

    $caseFields = $oCases->loadCase($_SESSION['APPLICATION']);

    $c = new Criteria();
    $c->addSelectColumn( RuleSetPeer::RST_SOURCE );
    $c->add( RuleSetPeer::RST_NAME, $nameFile );
    $rs = RuleSetPeer::doSelectRS( $c );
    $rs->setFetchmode( ResultSet::FETCHMODE_ASSOC );
    $rs->next();
    $row = $rs->getRow();
    $rstSource = base64_decode($row['RST_SOURCE']);

    $globalFields  = PMBRLoadGlobalVariables();

    $oRuleReader = new RuleReader();

    $r = $oRuleReader->parseRule($rstSource, $caseFields['APP_DATA'], $globalFields);
    global $oPMScript;
    //$oPMScript = new PMScript();
    //$oPMScript->setFields($caseFields['APP_DATA']);
    $oPMScript->aFields['message1'] = 'algo ' . date ('H:i:s');
    $oPMScript->setScript($r->successAction);
    $oPMScript->execute();

    $aFields['APP_DATA'] = array_merge($caseFields['APP_DATA'], $oPMScript->aFields);
    $oCases->updateCase($_SESSION['APPLICATION'], $aFields);

    return $r;
}