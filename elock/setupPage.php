<?php
  G::LoadClass('plugin');
  $pluginFile="elock.php";
  $externalSetup="";
  if(!(isset($details->sPluginFolder))){
  	$oPluginRegistry =& PMPluginRegistry::getSingleton();
  	$details = $oPluginRegistry->getPluginDetails( $pluginFile );
  	$externalSetup="../setup/";
  }

  $xmlform    = isset($details->sPluginFolder) ?  $details->sPluginFolder . '/' . 'setupPage'       : 'elock/setupPage';
  $G_PUBLISH = new Publisher;


  $Fields = $oPluginRegistry->getFieldsForPageSetup( $details->sNamespace );
  
  $G_PUBLISH->AddContent( 'xmlform', 'xmlform', $xmlform,    '',$Fields ,$externalSetup.'pluginsSetupSave?id='.$pluginFile );
  

  G::RenderPage('publishBlank', 'blank');

?>