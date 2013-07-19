<?php
/**
 * @section Filename
 * setupPage.php
 * @subsection Description
 * configuration page for the pentaho server connection
 * @Date 17/05/2010
 * @subsection Copyright
 * Copyright (C) 2004 - 2010 Colosa Inc.23
 * <hr>
 * @package plugins.pentahoreports.scripts
 */

// initializing the header and other settings
G::LoadClass('plugin');
/**
 * The Head Publisher singleton
 */
$oHeadPublisher =& headPublisher::getSingleton();
$oHeadPublisher->addScriptCode(file_get_contents(PATH_PLUGINS . 'pentahoreports/pentahoRolesManager.js'));
/**
 * get the configuration
 */
if (isset($_GET['configuration'])){
  $sEditConfig = $_GET['configuration'];
} else {
  $sEditConfig = '0';
}
$G_PUBLISH = new Publisher;
try {
  
  require_once ( "class.pentahoreports.php" );
  /**
   * The main pentahoreports object
   */
  $objPentaho = new pentahoreportsClass();
  // if the main configuration file doesn't exists
  if ( !$objPentaho->existPentahoMainConf()) {
    //default values for an empty main pentaho.conf file
    $fields = array();
    $fields['PentahoServer']          = 'http://localhost:8080/pentaho';
    $fields['PentahoSuperUsername']   = 'joe';
    $fields['PentahoSuperPassword']   = 'password';
    $fields['PentahoAdmServer']       = 'http://localhost:8099';
    $fields['PentahoAdmUsername']     = 'admin';
    $fields['PentahoAdmPassword']     = 'password';
    $fields['PentahoAdmPassword']     = 'password';
    $fields['PentahoPublishPassword'] = 'sample';
    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'pentahoreports/setupPage', null, $fields , '../pentahoreports/setupPageSave');
    G::RenderPage('publishBlank', 'blank');
    die;
  } else {
    // if exists, unserialize the connection data and assign it to the $aFields array in order to fill the form
    if ($sEditConfig=='1'){
      $bConfiguration = PATH_DATA.'pentaho.conf';
      $bFile = fopen($bConfiguration, 'r');
      $aFields = fread($bFile,filesize($bConfiguration));
      fclose($bFile);
      $aFields = unserialize($aFields);
      $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'pentahoreports/setupPage', null, $aFields , '../pentahoreports/setupPageSave');
      G::RenderPage('publishBlank', 'blank');
      die;
    }
  }
  $objPentaho->readLocalConfig( SYS_SYS );

  /**
   * The template setup plugin path
   */
  $setupTemplate = PATH_PLUGINS . 'pentahoreports' . PATH_SEP . 'setup.html' ;

  /**
   * The template power object
   */
  $template = new TemplatePower( $setupTemplate );
  $template->prepare();

  /**
   * assigning translation labels
   */
  $template->assign( 'PENTAHO_LABEL_TABLE_TRIGGERS',  G::LoadTranslation('PENTAHO_LABEL_TABLE_TRIGGERS') );//Database tables and triggers
  $template->assign( 'PENTAHO_LABEL_USER_CREATE',  G::LoadTranslation('PENTAHO_LABEL_USER_CREATE') );//Create user in Pentaho
  $template->assign( 'PENTAHO_LABEL_SYNC',  G::LoadTranslation('PENTAHO_LABEL_SYNC') );//Sync to Pentaho Solution
  $template->assign( 'PENTAHO_LABEL_USER_CONSOLE',  G::LoadTranslation('PENTAHO_LABEL_USER_CONSOLE') );//Pentaho User Console
  $template->assign( 'PENTAHO_LABEL_SERVER',  G::LoadTranslation('PENTAHO_LABEL_SERVER') );//Pentaho Server (URL)
  $template->assign( 'PENTAHO_LABEL_WS_USER_PASSWORD',  G::LoadTranslation('PENTAHO_LABEL_WS_USER_PASSWORD') );//Pentaho Workspace User and Password
  $template->assign( 'PENTAHO_LABEL_JNDI_CONNECTION',  G::LoadTranslation('PENTAHO_LABEL_JNDI_CONNECTION') );//JNDI Connection
  $template->assign( 'PENTAHO_LABEL_JNDI_INFORMATION',  G::LoadTranslation('PENTAHO_LABEL_JNDI_INFORMATION') );//JNDI Information
  $template->assign( 'PENTAHO_LABEL_DATASOURCE',  G::LoadTranslation('PENTAHO_LABEL_DATASOURCE') );// Datasource Name
  $template->assign( 'PENTAHO_LABEL_DRIVER_CLASS',  G::LoadTranslation('PENTAHO_LABEL_DRIVER_CLASS') );//Driver Class
  $template->assign( 'PENTAHO_LABEL_USER_NAME',  G::LoadTranslation('PENTAHO_LABEL_USER_NAME') );//User Name
  $template->assign( 'PENTAHO_LABEL_PASSWORD',  G::LoadTranslation('PENTAHO_LABEL_PASSWORD') );//Password
  $template->assign( 'PENTAHO_LABEL_URL',  G::LoadTranslation('PENTAHO_LABEL_URL') );//URL
  $template->assign( 'PENTAHO_LABEL_CONFIGURATION',  G::LoadTranslation('PENTAHO_LABEL_CONFIGURATION') );//Configuration
  $template->assign( 'PENTAHO_LABEL_ROLES_MANAGER',  G::LoadTranslation('PENTAHO_LABEL_ROLES_MANAGER') );//Roles Manager
  $template->assign( 'PENTAHO_LABEL_SHOW_JNDI_INFORMATION',  G::LoadTranslation('PENTAHO_LABEL_SHOW_JNDI_INFORMATION') );//Show Jndi Info
  $template->assign( 'PENTAHO_LABEL_REBUILD',  G::LoadTranslation('PENTAHO_LABEL_REBUILD') );//Rebuild

 /**
  * assigning the pentaho settings to the template
  */
  $template->assign( 'PentahoServer',          $objPentaho->sPentahoServer );
  $template->assign( 'PentahoSuperUsername',   $objPentaho->sPentahoSuperUsername );
  $template->assign( 'PentahoSuperPassword',   $objPentaho->sPentahoSuperPassword );
  $template->assign( 'PentahoUsername',        $objPentaho->sPentahoUsername );
  $template->assign( 'PentahoPassword',        $objPentaho->sPentahoPassword );
  $template->assign( 'PentahoAdmServer',       $objPentaho->sPentahoAdmServer );
  $template->assign( 'PentahoAdmUsername',     $objPentaho->sPentahoAdmUsername );
  $template->assign( 'PentahoAdmPassword',     $objPentaho->sPentahoAdmPassword );
  $template->assign( 'PentahoPublishPassword', $objPentaho->sPentahoPublishPassword );

 /** 
  * getting the jndi information
  */
  $jndiInfo = $objPentaho->getJndiInfo ();
  $template->assign( 'jndiName',        $jndiInfo['jndiName']  );
  $template->assign( 'jndiDriverClass', $jndiInfo['jndiDriverClass'] );
  $template->assign( 'jndiUserName',    $jndiInfo['jndiUserName'] );
  $template->assign( 'jndiPassword',    $jndiInfo['jndiPassword'] );
  $template->assign( 'jndiUrl',         $jndiInfo['jndiUrl']  );

  /**
   * adding the content to the template
   */
  $G_PUBLISH->AddContent( 'template', null, null , null, $template );
}
catch ( Exception $e ){
  $aMessage['MESSAGE'] = $e->getMessage();
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
}
/**
 * Publishing the view
 */
G::RenderPage('publishBlank', 'blank');
