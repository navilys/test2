<?php
require_once (PATH_PLUGINS . "enterprise" . PATH_SEP . "classes" . PATH_SEP . "class.enterpriseUtils.php");





/**
 * class.pmLicenseManager.php
 *
 */
class pmLicenseManager {

  private static $instance = NULL;

  function __construct() {
    G::LoadClass('serverConfiguration');
    $oServerConf = &serverConf::getSingleton();
    $oServerConf->setProperty('LOGIN_NO_WS', true);

    //to do: this files probably needs to be in core, since they are GPL2
    include_once (PATH_PLUGINS . 'enterprise' . PATH_SEP . 'classes' . PATH_SEP . 'class.license.lib.php');
    include_once (PATH_PLUGINS . 'enterprise' . PATH_SEP . 'classes' . PATH_SEP . 'class.license.app.php');

    //searching .dat files in workspace folder
    $server_array = $_SERVER;
    $licfile = glob(PATH_DATA_SITE . "*.dat");

    if (count($licfile) > 0 && is_file($licfile[0])) {
      $oServerConf->setProperty('ACTIVE_LICENSE', array(SYS_SYS => ""));
    }

    $activeLicenseSetting = $oServerConf->getProperty('ACTIVE_LICENSE');

    if((isset($activeLicenseSetting[SYS_SYS])) && (file_exists($activeLicenseSetting[SYS_SYS])) ){
      $licenseFile = $activeLicenseSetting[SYS_SYS];
    }
    else {
      $activeLicense = $this->getActiveLicense();
      $oServerConf->setProperty('ACTIVE_LICENSE', array(SYS_SYS => $activeLicense ['LICENSE_PATH']));
      $licenseFile = $activeLicense['LICENSE_PATH'];
    }

    $application = new license_application($licenseFile, false, true, false, true);
    $application->set_server_vars($server_array);
    $application->DATE_STRING = 'Y-m-d H:i:s';
    $results = $application->validate();
    $application->make_secure();
    $validStatus = array(
      'OK',
      'EXPIRED',
      'TMINUS'
    );

    $this->result = $results['RESULT'];
    if (in_array($this->result, $validStatus)) {
      $this->serial="3ptta7Xko2prrptrZnSd356aqmPXvMrayNPFj6CLdaR1pWtrW6qPw9jV0OHjxrDGu8LVxtmSm9nP5kR23HRpdZWccpeui+bKkK°DoqCt2Kqgpq6Vg37s";
      $info['FIRST_NAME']       = $results['DATA']['FIRST_NAME'];
      $info['LAST_NAME']        = $results['DATA']['LAST_NAME'];
      $info['DOMAIN_WORKSPACE'] = $results['DATA']['DOMAIN_WORKSPACE'];
      $this->date     = $results ['DATE'];
      $this->info     = $info;
      $this->type     = $results ['DATA']['TYPE'];
      $this->plan     = isset($results ['DATA']['PLAN'])?$results ['DATA']['PLAN']:"";
      $this->id       = $results ['ID'];
      $this->expireIn = $this->getExpireIn ();
      $this->features = $this->result!='TMINUS'?isset($results ['DATA']['CUSTOMER_PLUGIN'])?$results ['DATA']['CUSTOMER_PLUGIN']:$this->getActiveFeatures():array();
      $this->status   = $this->getCurrentLicenseStatus ();
      if (isset ( $results ['LIC'] )) {
        $resultsRegister = $results['LIC'];
        $this->server    = $results['LIC']['SRV'];
        $this->file      = $results['LIC']['FILE'];
      }
      else {
        $resultsRegister=array();
        $resultsRegister['ID']=$results ['DATA'] ['DOMAIN_WORKSPACE'];
        $this->server = NULL;
        $this->file = NULL;
      }

      $resultsRegister['date'] = $results ['DATE'];
      $resultsRegister['info'] = $info;
      $resultsRegister['type'] = $results ['DATA'] ['TYPE'];
      if($oServerConf->getProperty ( 'LICENSE_INFO')){
        $licInfoA = $oServerConf->getProperty ( 'LICENSE_INFO');
      }
      else {
        $licInfoA = array();
      }
      $licInfoA[SYS_SYS]=$resultsRegister;
      $oServerConf->setProperty ( 'LICENSE_INFO', $licInfoA );
    }

    $this->activateFeatures ();
  }

  function &getSingleton() {
    if (self::$instance == NULL) {
      self::$instance = new pmLicenseManager ();
    }
    return self::$instance;
  }

  function serializeInstance() {
    return serialize ( self::$instance );
  }

  function activateFeatures() {
    //Get a list of all Enterprise plugins and active/inactive them
    if (file_exists ( PATH_PLUGINS . 'enterprise/data/default' )) {
      if ($this->result=="OK") {
        //Disable
        if (file_exists ( PATH_PLUGINS . 'enterprise/data/data' )) {
          $oPluginRegistry = & PMPluginRegistry::getSingleton ();
          $aPlugins = unserialize ( trim ( file_get_contents ( PATH_PLUGINS . 'enterprise/data/data' ) ) );
          foreach ( $aPlugins as $aPlugin ) {
            $sClassName = substr ( $aPlugin ['sFilename'], 0, strpos ( $aPlugin ['sFilename'], '-' ) );
            require_once PATH_PLUGINS . $sClassName . '.php';
            $oDetails = $oPluginRegistry->getPluginDetails ( $sClassName . '.php' );
            $oPluginRegistry->disablePlugin ( $oDetails->sNamespace );
          }
          unlink(PATH_PLUGINS . 'enterprise/data/data');
        }

        //Enable
        $oPluginRegistry = &PMPluginRegistry::getSingleton();
        $aPlugins = unserialize(trim(file_get_contents(PATH_PLUGINS . "enterprise/data/default")));

        foreach ($aPlugins as $aPlugin) {
          if ($aPlugin ["bActive"]) {
            $sClassName = substr($aPlugin["sFilename"], 0, strpos($aPlugin["sFilename"], "-"));
            require_once (PATH_PLUGINS . $sClassName . ".php");
            $oDetails = $oPluginRegistry->getPluginDetails($sClassName . ".php");
            $oPluginRegistry->enablePlugin($oDetails->sNamespace);
          }
        }

        if (file_exists(PATH_DATA_SITE . "ee")) {
          $aPlugins = unserialize ( trim ( file_get_contents ( PATH_DATA_SITE.'ee' ) ) );
          $aDenied=array();
          foreach ( $aPlugins as $aPlugin ) {
            $sClassName = substr ( $aPlugin ['sFilename'], 0, strpos ( $aPlugin ['sFilename'], '-' ) );
            if(!(in_array($sClassName,$this->features))) {
              if(file_exists(PATH_PLUGINS . $sClassName . '.php')) {
                require_once PATH_PLUGINS . $sClassName . '.php';
                $oDetails = $oPluginRegistry->getPluginDetails ( $sClassName . '.php' );
                $oPluginRegistry->disablePlugin ( $oDetails->sNamespace );
                $aDenied[]=$oDetails->sNamespace;
              }
            }
          }
          if(!(empty($aDenied))) {
            if((SYS_COLLECTION=="enterprise")&&(SYS_TARGET=="pluginsList")) {
              G::SendMessageText("The following plugins were restricted due to your enterprise license: ".implode(", ",$aDenied),"INFO");
            }
          }
        }

        file_put_contents ( PATH_DATA_SITE . 'plugin.singleton', $oPluginRegistry->serializeInstance () );
      }
      else {
        //Disable
        $oPluginRegistry = & PMPluginRegistry::getSingleton ();
        $aPlugins = unserialize ( trim ( file_get_contents ( PATH_PLUGINS . 'enterprise/data/default' ) ) );
        foreach ( $aPlugins as $aPlugin ) {
          $sClassName = substr ( $aPlugin ['sFilename'], 0, strpos ( $aPlugin ['sFilename'], '-' ) );
          //To avoid self disable
          if (($sClassName != "pmLicenseManager") && ($sClassName != "pmTrial") && ($sClassName != "enterprise")) {
            require_once PATH_PLUGINS . $sClassName . '.php';
            $oDetails = $oPluginRegistry->getPluginDetails ( $sClassName . '.php' );
            $oPluginRegistry->disablePlugin ( $oDetails->sNamespace );
          }
          else {
            //Enable default and required plugins
            require_once PATH_PLUGINS . $sClassName . '.php';
            $oDetails = $oPluginRegistry->getPluginDetails ( $sClassName . '.php' );
            $oPluginRegistry->enablePlugin ( $oDetails->sNamespace );
          }
        }

        if (file_exists(PATH_DATA_SITE.'ee')) {
          $aPlugins = unserialize ( trim ( file_get_contents ( PATH_DATA_SITE.'ee' ) ) );

          foreach ( $aPlugins as $aPlugin ) {
            $sClassName = substr ( $aPlugin ['sFilename'], 0, strpos ( $aPlugin ['sFilename'], '-' ) );
            if ( strlen($sClassName) > 0 ) {
              if (!class_exists($sClassName)) {
                require_once PATH_PLUGINS . $sClassName . '.php';
              }
              $oDetails = $oPluginRegistry->getPluginDetails ( $sClassName . '.php' );
              if($oDetails) {
                $oPluginRegistry->disablePlugin ( $oDetails->sNamespace );
              }
            }
          }

        }
        file_put_contents ( PATH_DATA_SITE . 'plugin.singleton', $oPluginRegistry->serializeInstance () );
      }
    }

  }

  function getCurrentLicenseStatus() {
    $result = array ();
    switch ($this->result) {
      case 'OK' :
        $result ['result'] = 'ok';
        $result ['message'] = "";
        break;
      case 'TMINUS' :
        $result ['result'] = 'tminus';
        $startDateA=explode(" ",$this->date['HUMAN']['START']);
        $result ['message'] = "License will be active on ".$startDateA[0];
        break;
      case 'EXPIRED' :
        $result ['result'] = 'expired';
        $result ['message'] = "License Expired";
        break;
      case 'ILLEGAL' :
        $result ['result'] = 'illegal';
        $result ['message'] = "Illegal License";
        break;
      case 'ILLEGAL_LOCAL' :
        $result ['result'] = 'illegal';
        $result ['message'] = "Illegal Local License";
        break;
      case 'INVALID' :
        $result ['result'] = 'invalid';
        $result ['message'] = "Invalid License";
        break;
      case 'EMPTY' :
        $result ['result'] = 'empty';
        $result ['message'] = "Empty License";
        if (defined ( 'write_error' ))
        $result ['message'] = "Write error" . $result ['message'];
        break;
      default :
        break;
    }
    return $result;
  }

  function unSerializeInstance($serialized) {
    if (self::$instance == NULL) {
      self::$instance = new PMPluginRegistry ();
    }
    $instance = unserialize ( $serialized );
    self::$instance = $instance;
  }

  function getExpireIn() {
    $status = $this->getCurrentLicenseStatus ();
    $expireIn = 0;
    if ($status ['result'] == 'ok') {
      if($this->date ['END']!="NEVER"){
        $expireIn = ceil ( ($this->date ['END'] - time ()) / 60 / 60 / 24 );
      }
      else{
        $expireIn = "NEVER";
      }
    }
    return $expireIn;
  }

  function getLicenseInfo() {
    $validStatus = array (
      'ok',
      'expired'
    );
    $status = $this->getCurrentLicenseStatus ();
    $infoText = "";
    if (in_array ( $status ['result'], $validStatus )) {
      $start = explode ( " ", $this->date ['HUMAN'] ['START'] );
      $end = explode ( " ", $this->date ['HUMAN'] ['END'] );
      $infoText .= "<b>Issued to:</b> " . $this->info ['FIRST_NAME'] . " " . $this->info ['LAST_NAME'] . "<br>";
      $infoText .= "<b>Workspace:</b> " . $this->info ['DOMAIN_WORKSPACE'] . "<br>";
      $infoText .= "<i>Valid from " . $start [0] . " to " . $end [0] . "</i>";
    }

    if ($status ['message'] != "")
    $infoText .= "&nbsp;<font color=red><b>- " . $status ['message'] . "</b></font>";

    $info ['infoText'] = $infoText;
    $info ['infoLabel'] = $status ['message'];
    return $info;
  }

  function getExpireInLabel() {
    $linkText = null;

    if ($this->getExpireIn() != "NEVER" && ((int)$this->getExpireIn() <= 30) && ((int)$this->getExpireIn() > 0)) {
      $infoO = $this->getLicenseInfo();
      $infoText = $infoO['infoText'];
      
      $js = (EnterpriseUtils::skinIsUx() == 1)? "Ext.MessageBox.show({title: '', msg: '$infoText', buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.INFO});" : "msgBox('$infoText');";
      
      $linkText = $linkText . "<a href=\"javascript:;\" onclick=\"$js return (false);\"><span style=\"color: red;\">Expires in " . $this->getExpireIn () . " days</span></a>";
    }
    else {
      if ($this->getExpireIn() != "NEVER" && (int)$this->getExpireIn() <= 0) {
        $infoO = $this->getLicenseInfo();
        $infoText = $infoO['infoText'];
        $infoLabel = $infoO['infoLabel'];
        
        $js = (EnterpriseUtils::skinIsUx() == 1)? "Ext.MessageBox.show({title: '', msg: '$infoText', buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.INFO});" : "msgBox('$infoText');";
        
        $linkText = $linkText . "<a href=\"javascript:;\" onclick=\"$js return (false);\"><span style=\"color: red;\">" . $infoLabel . "</span></a>";
      }
    }

    if (class_exists('pmTrialPlugin')) {
      $linkText = $linkText . "<a href='/sys" . SYS_SYS . "/" . SYS_LANG . "/" . SYS_SKIN . "/pmTrial/services/buyNow?n=true" . "'> <img align='absmiddle' src='/plugin/pmLicenseManager/btn_buy_now.gif' border='0' /></a>";
    }

    if (isset($_SESSION["__ENTERPRISE_SYSTEM_UPDATE__"]) && $_SESSION["__ENTERPRISE_SYSTEM_UPDATE__"] == 1) {
      $aOnclick = "onclick=\"this.href='" . EnterpriseUtils::getUrlServerName() . "/sys" . SYS_SYS . "/" . SYS_LANG . "/" . SYS_SKIN . "/setup/main?s=PMENTERPRISE';\"";
      
      if (EnterpriseUtils::skinIsUx() == 1) {
        $aOnclick = "onclick=\"Ext.ComponentMgr.get('mainTabPanel').setActiveTab('pm-option-setup'); Ext.ComponentMgr.get('pm-option-setup').setLocation(Ext.ComponentMgr.get('pm-option-setup').defaultSrc + 's=PMENTERPRISE', true); return (false);\"";
      }
      
      $linkText = $linkText . (($linkText != null)? " | " : null) . "<a href=\"javascript:;\" $aOnclick style=\"color: #008000;\">Upgrade available</a>";
    }
    
    $linkText = ($linkText != null)? $linkText . ((EnterpriseUtils::skinIsUx() == 1)? null : " |") : null;

    return ($linkText);
  }

  function validateLicense($path) {
    $application = new license_application ( $path, false, true, false, true, true );
    $results = $application->validate ( false, false, "", "", "80", true );

    if ($results ['RESULT'] != 'OK') {
      return true;
    }
    else {
      return false;
    }
  }

  function installLicense($path, $redirect = true) {
    $application = new license_application ( $path, false, true, false, true, true );
    $results = $application->validate ( false, false, "", "", "80", true );

    //if the result is ok then it is saved into DB
    $res = $results ['RESULT'];
    if (( $res != 'OK') && ($res != 'EXPIRED' ) && ($res != 'TMINUS') ) {
      G::SendTemporalMessage ( 'ID_ISNT_LICENSE', 'tmp-info', 'labels' );
      return false;
    }
    else {
      G::LoadClass ( 'serverConfiguration' );
      $oServerConf = & serverConf::getSingleton ();
      $oServerConf->setProperty ( 'ACTIVE_LICENSE',array(SYS_SYS => $path));
      $this->saveDataLicense( $results, $path, $redirect );
      if ($redirect) {
        G::Header ( 'location: licenseManagerList' );
      }
      else {
        return true;
      }
    }
  }

  /*
    get Active License
  */
  function getActiveLicense() {
    //Autoinstall license if exists in data folder and move to license folder
    $dirData        = PATH_DATA;
    $dirDataSite    = PATH_DATA_SITE;
    $dirDataSiteLic = PATH_DATA_SITE . "licenses";

    G::verifyPath($dirDataSiteLic, true);

    $licfile = glob($dirDataSite . "*.dat");
    if (count($licfile) > 0 && is_file($licfile[0])) {
      $file = $licfile[0];
      @copy($file, $dirDataSiteLic . PATH_SEP . basename($file));
      $this->installLicense($dirDataSiteLic . PATH_SEP . basename($file), false);
      @unlink($file);
    }

    //get license from database, table LICENSE_MANAGER
    try {
      $aRow = array();
      require_once ("classes/model/LicenseManager.php");
      $oCriteria = new Criteria('workflow');
      $oCriteria->addSelectColumn(LicenseManagerPeer::LICENSE_USER);
      $oCriteria->addSelectColumn(LicenseManagerPeer::LICENSE_START);
      $oCriteria->addSelectColumn(LicenseManagerPeer::LICENSE_PATH);
      $oCriteria->addSelectColumn(LicenseManagerPeer::LICENSE_DATA);
      $oCriteria->add(LicenseManagerPeer::LICENSE_STATUS, 'ACTIVE');
      $oDataset = LicenseManagerPeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      $aRow = $oDataset->getRow();
    } catch (Exception $e) {
      G::pr ($e);
    }
    return $aRow;
  }

  function lookForStatusLicense() {
    require_once ("classes/model/LicenseManager.php");
    //obtening info in a row that has ACTIVE status
    $oCtia = new Criteria ( 'workflow' );
    $oCtia->add ( LicenseManagerPeer::LICENSE_STATUS, 'ACTIVE' );
    $oDataset = LicenseManagerPeer::doSelectRS ( $oCtia );
    $oDataset->next ();
    $aRow = $oDataset->getRow ();

    $oCtiaA = new Criteria ( 'workflow' );
    $oCtiaA->add ( LicenseManagerPeer::LICENSE_UID, $aRow [0] );

    $oCtiaB = new Criteria ( 'workflow' );
    $oCtiaB->add ( LicenseManagerPeer::LICENSE_STATUS, 'INACTIVE' );
    BasePeer::doUpdate ( $oCtiaA, $oCtiaB, Propel::getConnection ( 'workflow' ) );
    return 'ACTIVE';
  }

  function saveDataLicense($results, $path) {
    try {
      //getting info about file
      $LicenseUid    = G::generateUniqueID ();
      $LicenseUser   = $results ['DATA'] ['FIRST_NAME'] . ' ' . $results ['DATA'] ['LAST_NAME'];
      $LicenseStart  = $results ['DATE'] ['START'];
      $LicenseEnd    = $results ['DATE'] ['END'];
      $LicenseSpan   = $results ['DATE'] ['SPAN'];
      $LicenseStatus = $this->lookForStatusLicense(); //we're looking for a status ACTIVE

      //getting the content from file
      $handle = fopen ( $path, "r" );
      $contents = fread ( $handle, filesize ( $path ) );
      fclose ( $handle );
      $LicenseData      = $contents;
      $LicensePath      = $path;
      $LicenseWorkspace = isset($results['DATA']['DOMAIN_WORKSPACE']) ? $results['DATA']['DOMAIN_WORKSPACE'] : '';
      $LicenseType      = $results['DATA']['TYPE'];

      require_once ("classes/model/LicenseManager.php");

      //if exists the row in the database propel will update it, otherwise will insert.
      $tr = LicenseManagerPeer::retrieveByPK ( $LicenseUid );
      if (! (is_object ( $tr ) && get_class ( $tr ) == 'LicenseManager')) {
        $tr = new LicenseManager ();
      }
      $tr->setLicenseUid    ( $LicenseUid );
      $tr->setLicenseUser   ( $LicenseUser );
      $tr->setLicenseStart  ( $LicenseStart );
      $tr->setLicenseEnd    ( $LicenseEnd );
      $tr->setLicenseSpan   ( $LicenseSpan );
      $tr->setLicenseStatus ( $LicenseStatus );
      $tr->setLicenseData   ( $LicenseData );
      $tr->setLicensePath   ( $LicensePath );
      $tr->setLicenseWorkspace ( $LicenseWorkspace );
      $tr->setLicenseType   ( $LicenseType );

      $res = $tr->save ();
    } catch ( Exception $e ) {
      G::pr($e);
    }

  }

  function getResultQry($sNameTable, $sfield, $sCondition) {
    try {
      require_once ("classes/model/LicenseManager.php");
      $oCriteria = new Criteria ( 'workflow' );
      $oCriteria->addSelectColumn ( LicenseManagerPeer::LICENSE_USER );
      $oCriteria->addSelectColumn ( LicenseManagerPeer::LICENSE_START );
      $oCriteria->addSelectColumn ( LicenseManagerPeer::LICENSE_PATH );
      $oCriteria->addSelectColumn ( LicenseManagerPeer::LICENSE_DATA );
      $oCriteria->add ( LicenseManagerPeer::LICENSE_STATUS, 'ACTIVE' );
      $oDataset = LicenseManagerPeer::doSelectRS ( $oCriteria );
      $oDataset->setFetchmode ( ResultSet::FETCHMODE_ASSOC );
      $oDataset->next ();
      $aRow = $oDataset->getRow ();
    } catch ( Exception $e ) {
      G::pr ( $e );
      $aRow = array ();
    }

    return $aRow;
  }

  function getActiveFeatures() {
    return unserialize(G::decrypt($this->serial, file_get_contents(PATH_PLUGINS . 'enterprise/data/default')));
  }

}
