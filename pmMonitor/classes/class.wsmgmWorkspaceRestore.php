<?php
/**
 * class.workspaceRestore.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.
 * *
 */

////////////////////////////////////////////////////
// workspaceRestore PM Functions
//
// Copyright (C) 2007 COLOSA
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////


class workspaceRestore {
  
  public function execworkspaceRestore($wspace) {
    try {
      
      $dir = PATH_CORE;
      $sw = 1;
      /*
      $sCommand ='whoami';
      //print PATH_CORE;
      exec($sCommand,$outp);
      //print_r($outp);
      //echo exec('pwd');
      //print $sCommand."<hr>";
      //echo exec ('./gulliver workspace-restore');
      //$rst= exec('pwd',$outp);
      //print_r($outp);
      //print "<hr>";
      */
      return $sw;
    
    } catch ( Exception $oError ) {
      throw ($oError);
    }
  }
  
  function getWorkspaceInfo($wsName) {
    $aResult = Array('logo'=>'', 'num_processes'=>'0', 'num_cases'=>'0');
    if (file_exists ( PATH_DB . $wsName . PATH_SEP . 'db.php' )) {
      
      $sContent = file_get_contents ( PATH_DB . $wsName . PATH_SEP . 'db.php' );
      
      $sContent = str_replace ( '<?php', '', $sContent );
      $sContent = str_replace ( '<?', '', $sContent );
      $sContent = str_replace ( '?>', '', $sContent );
      $sContent = str_replace ( 'define', '', $sContent );
      $sContent = str_replace ( "('", "$", $sContent );
      $sContent = str_replace ( "',", '=', $sContent );
      $sContent = str_replace ( ");", ';', $sContent );
      
      @eval ( $sContent );
      
      if( !(isset($DB_ADAPTER) && isset($DB_USER) && isset($DB_PASS) && isset($DB_HOST) && isset($DB_NAME)) ) {
        return false; 
      }
      
      $dsn = $DB_ADAPTER . '://' . $DB_USER . ':' . $DB_PASS . '@' . $DB_HOST . '/' . $DB_NAME;
      $dsnRbac = $DB_ADAPTER . '://' . $DB_RBAC_USER . ':' . $DB_RBAC_PASS . '@' . $DB_RBAC_HOST . '/' . $DB_RBAC_NAME;
      $dsnRp = $DB_ADAPTER . '://' . $DB_REPORT_USER . ':' . $DB_REPORT_PASS . '@' . $DB_REPORT_HOST . '/' . $DB_REPORT_NAME;
      
      $link = @mysql_connect($DB_HOST, $DB_USER, $DB_PASS);
      
      if($link){
        @mysql_select_db($DB_NAME);
        $result = @mysql_query("SELECT * FROM CONFIGURATION WHERE CFG_UID='USER_LOGO_REPLACEMENT'", $link);
        if($result){
          $aResult['wsName'] = $wsName;
          $a = @mysql_fetch_array($result);
          $cfgValue = unserialize($a['CFG_VALUE']);
          if(isset($cfgValue['DEFAULT_LOGO_NAME']) && $cfgValue['DEFAULT_LOGO_NAME'] != ''){
            $oPluginRegistry     = &PMPluginRegistry::getSingleton();
            $aResult['logo']     = $oPluginRegistry->getCompanyLogo('/files/logos/'.$cfgValue['DEFAULT_LOGO_NAME']);
            $aResult['logoName'] = $oPluginRegistry->getCompanyLogo($cfgValue['DEFAULT_LOGO_NAME']);
          } else {
            $aResult['logo']     = null;
            $aResult['logoName'] = null;
          }
        }
        $result = @mysql_query("SELECT COUNT(*) AS NUM FROM PROCESS WHERE PRO_STATUS='ACTIVE'", $link);
        if($result){ 
          $a = @mysql_fetch_array($result);
          
          if(isset($a['NUM'])){
            $aResult['num_processes'] = $a['NUM'];
          }
        }
        $result = @mysql_query("SELECT COUNT(APP_UID) AS NUM FROM APPLICATION WHERE APP_STATUS<>'COMPLETED'", $link);
        if($result){ 
          $a = @mysql_fetch_array($result);
          
          if(isset($a['NUM'])){
            $aResult['num_cases'] = $a['NUM'];
          }
        }
        mysql_close($link);
      }
    }
    return $aResult;
  }
}//end class workspaceRestore

