<?php
$RBAC->requirePermissions('PM_SETUP_ADVANCE');
//First refresh the common Plugins list
if ($handle = opendir( PATH_PLUGINS  )) {
  while ( false !== ($file = readdir($handle))) {
    if ( strpos($file, '.php',1) && is_file(PATH_PLUGINS . $file) ) {
      include_once ( PATH_PLUGINS . $file );
    }
  }
}

$aPlugins   = array();
$aPlugins[] = array('id'          => 'int',
                    'title'       => 'char',
                    'description' => 'char',
                    'status'      => 'char',
                    'linkStatus'  => 'char',
                    'edit'        => 'char',
                    'linkEdit'    => 'char');

$aPluginsPP = array();
if(file_exists(PATH_DATA_SITE.'ee')){
  $aPluginsPP = unserialize(trim(file_get_contents(PATH_DATA_SITE.'ee')));
}
$pmLicenseManagerO = &pmLicenseManager::getSingleton();

//$features=$pmLicenseManagerO->features;
//G::pr($aPluginsPP);
//$aPluginsPP = unserialize(trim(file_get_contents(PATH_PLUGINS . 'enterprise/data/data')));

foreach ($aPluginsPP as $aPlugin) {
  $sClassName = substr($aPlugin['sFilename'], 0, strpos($aPlugin['sFilename'], '-'));
  if(file_exists(PATH_PLUGINS . $sClassName . '.php')){
    require_once PATH_PLUGINS . $sClassName . '.php';
    $oDetails = $oPluginRegistry->getPluginDetails($sClassName . '.php');
    if($oDetails){
      $sStatus  = $oDetails->enabled ? G::LoadTranslation('ID_ENABLED') : G::LoadTranslation('ID_DISABLED');
      if (isset($oDetails->aWorkspaces)) {
        if (!in_array(SYS_SYS, $oDetails->aWorkspaces)) {
          continue;
        }
      }
      if(($sClassName=="pmLicenseManager")||($sClassName=="pmTrial"))continue;
      
      $sEdit = (($oDetails->sSetupPage != '') && ($oDetails->enabled) ?  G::LoadTranslation('ID_SETUP') : null);
      $aPlugin = array();
      $aPlugin['id']          = count($aPlugins);
      $aPlugin['title']       = $oDetails->sFriendlyName;// . "\n(" . $oDetails->sNamespace . '.php)';
      $aPlugin['description'] = $oDetails->sDescription;
      $aPlugin['version'] = $oDetails->iVersion;
      if(@in_array($sClassName,$pmLicenseManagerO->features)){
        $aPlugin["status"]     = $sStatus;
        $aPlugin["linkStatus"] = EnterpriseUtils::getUrlServerName() . "/sys" . SYS_SYS . "/" . SYS_LANG . "/" . SYS_SKIN . "/enterprise/pluginsChange?id=" . $sClassName . ".php&status=" . $oDetails->enabled;
        $aPlugin["edit"]       = $sEdit;
        $aPlugin["linkEdit"]   = "pluginsSetup?id=" . $sClassName . ".php";
      }
      else{
        $aPlugin["status"]     = "";
        $aPlugin["linkStatus"] = "";
        $aPlugin["edit"]       = "";
        $aPlugin["linkEdit"]   = "";
      }
      $aPlugins[] = $aPlugin;
    }
  }
}

global $_DBArray;
$_DBArray['plugins']  = $aPlugins;
$_SESSION['_DBArray'] = $_DBArray;
G::LoadClass('ArrayPeer');
$oCriteria = new Criteria('dbarray');
$oCriteria->setDBArrayTable('plugins');

$G_PUBLISH = new Publisher;
$sw_show_add=false;
if((isset($_GET['add']))&&($_GET['add']==1)){
  $sw_show_add=true;
}

if(!(class_exists("pmLicenseManager"))){
  require_once ( PATH_PLUGINS . 'enterprise' . PATH_SEP . 'class.pmLicenseManager.php');
}

//seems this pmLicenseManagerClass is not used
//$pluginObj = new pmLicenseManagerClass ();
if(!(class_exists("LicenseManager"))){
  require_once ( "classes/model/LicenseManager.php" );
}
$Criteria = new Criteria('workflow');
$Criteria->clearSelectColumns ( );

$Criteria->addSelectColumn ( LicenseManagerPeer::LICENSE_UID );
$Criteria->addSelectColumn ( LicenseManagerPeer::LICENSE_USER );
$Criteria->addSelectColumn ( LicenseManagerPeer::LICENSE_START );
$Criteria->addSelectColumn ( LicenseManagerPeer::LICENSE_END );
$Criteria->addSelectColumn ( LicenseManagerPeer::LICENSE_SPAN );
$Criteria->addSelectColumn ( LicenseManagerPeer::LICENSE_STATUS );
$Criteria->addSelectColumn ( LicenseManagerPeer::LICENSE_DATA );
$Criteria->addSelectColumn ( LicenseManagerPeer::LICENSE_PATH );
$Criteria->addSelectColumn ( LicenseManagerPeer::LICENSE_WORKSPACE );
$Criteria->addSelectColumn ( LicenseManagerPeer::LICENSE_TYPE );
$Criteria->add  ( LicenseManagerPeer::LICENSE_TYPE,'ES');
$Criteria->addOr( LicenseManagerPeer::LICENSE_TYPE, null, Criteria::NOT_EQUAL );
$Criteria->add  ( LicenseManagerPeer::LICENSE_WORKSPACE, null, Criteria::NOT_EQUAL );
$Criteria->addOr( LicenseManagerPeer::LICENSE_WORKSPACE,SYS_SYS, Criteria::GREATER_EQUAL );//SYS_SYS
$Criteria->addAscendingOrderByColumn( LicenseManagerPeer::LICENSE_STATUS);

$dir = PATH_DATA_SITE . "licenses";
G::verifyPath( $dir , true );

$Criteria->add (  licenseManagerPeer::LICENSE_UID, "xx" , CRITERIA::NOT_EQUAL );
$Fields = array();

/*
if(isset($pmLicenseManagerO->result)&&($pmLicenseManagerO->result=='OK')){
  $licenseFields=array();
  $licenseFields['LICENSE_START']=date("Y-m-d",$pmLicenseManagerO->date['START']);
  $licenseFields['LICENSE_END']=$pmLicenseManagerO->expireIn!="NEVER"?date("Y-m-d",$pmLicenseManagerO->date['END']):"NA";
  $licenseFields['LICENSE_USER']=$pmLicenseManagerO->info['FIRST_NAME']." ".$pmLicenseManagerO->info['LAST_NAME']." (".$pmLicenseManagerO->info['DOMAIN_WORKSPACE'].")";
  $licenseFields['LICENSE_SPAN']=$pmLicenseManagerO->expireIn!="NEVER"?ceil($pmLicenseManagerO->date['SPAN']/60/60/24):"~";
  $licenseFields['LICENSE_TYPE']=$pmLicenseManagerO->type;
  $licenseFields['LICENSE_EXPIRE']=$pmLicenseManagerO->expireIn;
  $licenseFields['LICENSE_MESSAGE']=$pmLicenseManagerO->status['message'];

  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'pmLicenseManager/licenseInformation', '', $licenseFields, '' );
}
else {
  $sw_show_add=true;
}
*/

/*
if($sw_show_add){
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'pmLicenseManager/licenseManagerUpLicense', '', $Fields, 'pluginsList?add=1' );
  //$G_PUBLISH->AddContent('xmlform', 'xmlform', 'rbac/userEdit',                            '', $obj->Fields, 'userEdit2');
  if(sizeof($_FILES)) {
    //uploading the license
    $aInfoLoadFile=$_FILES['form'];
    $aExtentionFile = explode('.',$aInfoLoadFile['name']['upLicense']);

    //validating the extention before to upload it
    if(trim($aExtentionFile[sizeof($aExtentionFile)-1])!='dat') {
      G::SendTemporalMessage('ID_ISNT_LICENSE', 'tmp-info', 'labels');
    }
    else {
      G::uploadFile ($aInfoLoadFile['tmp_name']['upLicense'],$dir, $aInfoLoadFile['name']['upLicense'] );
      //reading the file that was uploaded
      $pmLicenseManagerO =& pmLicenseManager::getSingleton();
      $response=$pmLicenseManagerO->installLincense($dir.PATH_SEP.$aInfoLoadFile['name']['upLicense']);
      $message="A license has been correctly installed. Please login again to complete apply the changes";
      G::SendMessageText($message,"INFO");
      $_SESSION["___PMEE_INSTALLED_LIC___"]=$message;
      G::header('location: pluginsList' );

      exit;
    }
  }

  $oDataset = LicenseManagerPeer::doSelectRS($Criteria);
  $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
  $oDataset->next();
  $aRows [] = array ('dummy' => 'char' );
  $sw_active_lic=false;
  while($aRow = $oDataset->getRow()){
    $aRow['DELETE_LABEL']="";
    if ( $aRow['LICENSE_STATUS']!='ACTIVE'){
      $aRow['DELETE_LABEL']=G::LoadTranslation("ID_DELETE");
    }
    $aRow['LICENSE_START']=date("Y-m-d",$aRow['LICENSE_START']);
    $aRow['LICENSE_END']=date("Y-m-d",$aRow['LICENSE_END']);
    $aRow['LICENSE_SPAN']=ceil($aRow['LICENSE_SPAN']/60/60/24);;
    $aRows[]=$aRow;
    $oDataset->next();
  }
  $_DBArray['licenses'] = $aRows;
  $_SESSION['_DBArray'] = $_DBArray;
  G::LoadClass( 'ArrayPeer');
  $c = new Criteria ('dbarray');
  $c->setDBArrayTable('licenses');

  $G_PUBLISH->AddContent('propeltable', 'paged-table', 'pmLicenseManager/licenseManagerList', $c, array(),'');
}
*/

if(isset($pmLicenseManagerO->result) && ($pmLicenseManagerO->result=='OK')){
  $G_PUBLISH->AddContent('propeltable', 'paged-table', 'enterprise/pluginsList', $oCriteria);
}

G::RenderPage('publishBlank', 'blank');
