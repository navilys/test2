<?php 
/**
 * @section Filename
 * setupPageSave.php
 * @subsection Description
 * saving the configuration data for the pentaho server connection
 * @Date 17/05/2010
 * @subsection Copyright
 * Copyright (C) 2004 - 2010 Colosa Inc.23
 * <hr>
 * 
 * @package plugins.pentahoreports.scripts
 */
  global $G_PUBLISH;

  try {

    require_once ( "class.pentahoreports.php" );
    /**
     * The main pentaho reports object
     */
    $objPentaho = new pentahoreportsClass();
    /**
     * validating some form fields
     */
    if ( isset ( $_POST['form']['ACCEPT'] ) ) unset ( $_POST['form']['ACCEPT'] );
      if ( isset($_POST['form']) )
        $content = serialize ($_POST['form']);
      else
        $content = serialize ($_POST['form']);
    /**
     * The path of the main configuration file
     */
    $fileConf = PATH_DATA . 'pentaho.conf';
    
    /** 
     * validating some configuration options and write permissions
     */
    if ( !is_writable( dirname($fileConf) ) ) 
      throw ( new Exception ("The directory " . dirname($fileConf) . " doesn't exists or this directory is not writable." ) );
  
    if ( file_exists ( $fileConf ) && !is_writable( $fileConf ) ) 
      throw ( new Exception ("The file $fileConf doesn't exists or this file is not writable." ) );
      
    /**
     * update the main configuration file
     */
    file_put_contents ( $fileConf, $content);

    /** 
     * update the workspace configuration file
     */
    $fields = $objPentaho->readLocalConfig ( SYS_SYS );
    $fields['PentahoServer']    = $_POST['form']['PentahoServer'];
    $fields['PentahoAdmServer'] = $_POST['form']['PentahoAdmServer'];
    $fields['PentahoUsername']  = "wf_".SYS_SYS;
    $fields['PentahoPassword']  = SYS_SYS;

    /**
     * call the update method
     */
    $objPentaho->updateLocalConfig ( SYS_SYS, $fields );

    header ( 'Location: ../setup/pluginsSetup?id=pentahoreports.php' );
  } catch ( Exception $e ) {
    // catching exceptions
    $G_PUBLISH = new Publisher;
  	$aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
    G::RenderPage('publishBlank', 'blank');
  }
