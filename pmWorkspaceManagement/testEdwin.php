<?php
      G::LoadClass( 'replacementLogo' );
      $oLogoR = new replacementLogo();
      //if(defined("SYS_SYS")){
        $aFotoSelect = $oLogoR->getNameLogo();

        if (is_array($aFotoSelect)) {
          $sFotoSelect   = trim($aFotoSelect['DEFAULT_LOGO_NAME']);
          $sWspaceSelect = trim($aFotoSelect['WORKSPACE_LOGO_NAME']);
        }
      //}
if (class_exists('PMPluginRegistry')) {
        $oPluginRegistry = &PMPluginRegistry::getSingleton();
        if ( isset($sFotoSelect) && $sFotoSelect!='' && !(strcmp($sWspaceSelect, SYS_SYS)) ){
          $sCompanyLogo = $oPluginRegistry->getCompanyLogo($sFotoSelect);
          $sCompanyLogo = "/sys".SYS_SYS."/".SYS_LANG."/".SYS_SKIN."/setup/showLogoFile.php?id=".base64_encode($sCompanyLogo);
        }
        else {
          $sCompanyLogo = $oPluginRegistry->getCompanyLogo('/images/processmaker.logo.jpg');
        }
      }
      else {
        $sCompanyLogo = '/images/processmaker.logo.jpg';
      }


die;
require_once(PATH_PLUGINS.SYS_COLLECTION.PATH_SEP.'classes'.PATH_SEP.'class.workspaceInfoManager.php');

$oWorkspaceInfoManager = new workspaceInfoManager();
G::pr($oWorkspaceInfoManager->fillWorkspacesData());
die;



G::loadClass('processes');
G::pr(Propel::getConfiguration());
$oWorkspaceConnectionManager = new workspaceConnectionManager();
$oWorkspaceConnectionManager->createConnectionObjects("test");
//$con = Propel::getConnection('workflowCon');
//G::pr(Propel::getConfiguration());
$oCriteria = new Criteria();
$oCriteria->add(ProcessPeer::PRO_STATUS, 'ACTIVE');
//$result = ProcessPeer::doCount($oCriteria, true, $con);
$result = ProcessPeer::doCount($oCriteria);
//$con->close();
G::pr($result);



die;
