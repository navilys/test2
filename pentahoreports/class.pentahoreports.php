<?php
/**
 * @section Filename
 * class.pentahoreports.php
 * @subsection Description
 * this class encapsulates all the essential methods in order to connect
 * get a report list for each workspace, and generate these reports for each workspace
 * also manages the connection to the pentaho reports an the plugin administration area.
 * @author Fernando Ontiveros
 * @subsection Copyright
 * Copyright (C) Colosa Development Team 2010
 * <hr>
 * @package plugins.pentahoreports.classes
 */
  require_once('classes/class.PentahoProxy.php');
  require_once('classes/class.ProxyPentahoUser.php');
  require_once('classes/model/PhReport.php');
  require_once('classes/model/PhRoleReport.php');
  
  class pentahoreportsClass extends PMPlugin  {

    /**
     * server path of the pentaho plugin.
     */
    var $sPluginFolder;
    /**
     * array of the complete solution repository.
     */
    var $aSolutionRepository;
    /**
     * this array store all the reports registered, this is a cache array to improve speed.
     */
    var $aReports;
    /**
     *  String pentaho server address.
     */
    var $sPentahoServer        ;
    /**
     *  String pentaho administrator user console username
     */
    var $sPentahoSuperUsername ;
    /**
     *  String pentaho administrator user console password
     */
    var $sPentahoSuperPassword ;
    /**
     *  String pentaho user console username
     */
    var $sPentahoUsername      ;
    /**
     *  String pentaho user console password
     */
    var $sPentahoPassword      ;
    /**
     *  String pentaho administration console server
     */
    var $sPentahoAdmServer     ;
    /**
     *  String pentaho administration console username
     */
    var $sPentahoAdmUsername   ;
    /**
     *  String pentaho administration console password
     */
    var $sPentahoAdmPassword   ;
    /**
     *  String pentaho publish password
     */
    var $sPentahoPublishPassword;

    /**
     * Class Pentaho Reports constructor method
     */
    function __construct (  ) {
      $this->sPluginFolder = 'pentahoreports';
      set_include_path(
        PATH_PLUGINS . $this->sPluginFolder . PATH_SEPARATOR .
        get_include_path()
      );
    }

    /**
     * Within this method is possible add some configuration options
     */
    function setup()
    {
    }

    /**
     * This method reads the config file from the plugin config folder
     * @return an array from the file that stores the local configuration
     */
    function readConfig () {
      return $this->readLocalConfig ( SYS_SYS );
    }

   /**
    * This method checks if the Pentaho Reports plugin is installed in this workspace
    * @param String $workspaceName
    * @return Boolean true/false
    */
    function isPentahoInstalled ( $workspaceName ) {
      $fileConf = PATH_DB . $workspaceName . PATH_SEP . $this->sPluginFolder . '.conf';
      // checks the configuration file folder
      if ( !file_exists( dirname($fileConf) ) )
        return false;
      // checks the configuration file
      if ( !file_exists ( $fileConf ) )
        return false;
      // reading the configuration file
      $this->readLocalConfig ( $workspaceName );
      // checks the connection, database, and workspace existance
      if ( $this->jdniCreated == false || $this->databaseInstalled == false || $this->workspaceCreated == false ) {
        return false;
      }
      return true;
    }

   /**
    * This method checks if the Pentaho Reports configuration file has been created
    * @return Boolean true/false
    */
    function existPentahoMainConf ( ) {
      // checks the main configuration file folder
      $fileConf = PATH_DATA . 'pentaho.conf';
      if ( !file_exists( dirname($fileConf) ) )
        return false;
      // checks the main configuration file
      if ( !file_exists ( $fileConf ) )
        return false;
        
      return true;
    }

   /**
    * This method gets the Jndi information for the pentaho connection, is based in the database information
    * @return Array $res array of the connection data
    */
    function getJndiInfo () {
      $con = Propel::getConnection('workflow');
      // getting the mysql hostname
      $sql = "SHOW VARIABLES LIKE 'hostname'";
      $stmt = $con->createStatement();
      $rs = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);
      $rs->next();
      $row = $rs->getRow ();
      $mysqlHostname = gethostbyname ( $row['Value'] );
      // getting the mysql port
      $sql = "SHOW VARIABLES LIKE 'port'";
      $stmt = $con->createStatement();
      $rs = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);
      $rs->next();
      $row = $rs->getRow ();
      $mysqlPort = $row['Value'];
      // assembling the array
      $res = array();
      $res['jndiName']        = 'wf_' . SYS_SYS;
      $res['jndiDriverClass'] = 'com.mysql.jdbc.Driver';
      $res['jndiUserName']    = 'ph_' . SYS_SYS;
      $res['jndiPassword']    = DB_PASS;
      $res['jndiUrl']         = "jdbc:mysql://$mysqlHostname:$mysqlPort/wf_" . SYS_SYS ;
      return $res;
    }

   /**
    * This method reads the config file for this workspace
    * @param String $workspaceName
    * @return Array $sFields
    */
    function readLocalConfig ( $workspaceName ) {
      // getting the configuration file path
      $fileConf = PATH_DB . $workspaceName . PATH_SEP . $this->sPluginFolder . '.conf';
      $fileDefaultConf = PATH_DATA . 'pentaho.conf';
      
      // checks the configuration file and folder
      if ( !file_exists( dirname($fileConf) ) )
        throw ( new Exception ("The directory " . dirname($fileConf) . " doesn't exists." ) );

      if ( !file_exists ( $fileDefaultConf ) ) {
        throw ( new Exception ("The file " . $fileDefaultConf . " doesn't exists." ) );
      }
      $defaultconf = unserialize (file_get_contents ( $fileDefaultConf ));

      // validating the non existance of the configuration file serializes and creates a default configuration file
      if ( !file_exists ( $fileConf ) ) {
        $fields = array();
        $fields['PentahoServer']          = $defaultconf['PentahoServer'];
        $fields['PentahoUsername']        = '';
        $fields['PentahoPassword']        = '';
        $fields['PentahoAdmServer']       = $defaultconf['PentahoAdmServer'];
          
        $content = serialize ($fields);
        file_put_contents ( $fileConf, $content);
      }
      // an exception is thrown if the configuration files doesn't exists
      if ( !file_exists ( $fileConf ) || !is_writable( $fileConf ) )
        throw ( new Exception ("The file $fileConf doesn't exists or this file is not writable." ) );
      // getting the configuration file content
      $content = file_get_contents ( $fileConf);
      $fields = unserialize ($content);

      // if the site pentaho.conf file is an older version of .conf file, it needs to be updated.
      if ( isset ( $fields['PentahoApplicationPath']) || !isset ($fields['PentahoAdmServer']) ) {
        $fields = array();
        $fields['PentahoServer']          = $defaultconf['PentahoServer'];
        $fields['PentahoUsername']        = '';
        $fields['PentahoPassword']        = '';
        $fields['PentahoAdmServer']       = $defaultconf['PentahoAdmServer'];
        $content = serialize ($fields);
        file_put_contents ( $fileConf, $content);
      }

      // setting the object attributes with the unserialized data obtained from the configuration file
      $this->sPentahoServer           = $fields['PentahoServer'];
      $this->sPentahoSuperUsername    = $defaultconf['PentahoSuperUsername'];
      $this->sPentahoSuperPassword    = $defaultconf['PentahoSuperPassword'];
      $this->sPentahoUsername         = $fields['PentahoUsername'];
      $this->sPentahoPassword         = $fields['PentahoPassword'];
      $this->sPentahoAdmServer        = $fields['PentahoAdmServer'];
      $this->sPentahoAdmUsername      = $defaultconf['PentahoAdmUsername'];
      $this->sPentahoAdmPassword      = $defaultconf['PentahoAdmPassword'];
      $this->sPentahoPublishPassword  = $defaultconf['PentahoPublishPassword'];
      
      return $fields;
    }

   /**
    * This method updates the config data for this workspace
    * @param String $workspaceName
    * @param Array $oData
    * @return void
    */
    function updateLocalConfig ( $workspaceName, $oData ) {
      // unsseting the ACCEPT form field
      if ( isset ( $oData['form']['ACCEPT'] ) ) unset ( $oData['form']['ACCEPT'] );
      // serializing form data
      if ( isset($oData['form']) )
        $content = serialize ($oData['form']);
      else
        $content = serialize ($oData);

      $fileConf = PATH_DB . $workspaceName . PATH_SEP . $this->sPluginFolder . '.conf';
      // checking the write permissions of the folder
      if ( !is_writable( dirname($fileConf) ) )
        throw ( new Exception ("The directory " . dirname($fileConf) . " doesn't exists or this directory is not writable." ) );
      // checking the write permissions of the file
      if ( file_exists ( $fileConf ) && !is_writable( $fileConf ) )
        throw ( new Exception ("The file $fileConf doesn't exists or this file is not writable." ) );
      // updating the changed data into the configuration file
      file_put_contents ( $fileConf, $content);
      return true;
    }

   /**
    * This method is a standard function to obtains fields of the local configuration
    * @return local configuration serialized array
    */
    function getFieldsForPageSetup () {
      return $this->readLocalConfig ( SYS_SYS );
    }

   /**
    * This method update the local configuration saving the info that is passed in an array of data
    * @param Object $oData  data object
    * @return Array local configuration serialized array
    */
    function updateFieldsForPageSetup ( $oData ) {
      return $this->updateLocalConfig ( SYS_SYS, $oData );
    }

   /**
    * This method Walks thru the report files and folders in a pentaho solution
    * to obtain the info of each one
    * @param String $path  path of the solution
    * @param Object $node current node
    * @return Array $aFiles array with the reports list
    */
    function walkThruFiles ( $path, $node ) {
      $aFiles     = array();
      $roleReport = new PhRoleReport();
      // getting the child node list
      $files = $node->childNodes;
      // iterating over the child nodes list
      foreach( $files as $file ) {
        if ( isset($file->attributes->getNamedItem('visible')->nodeValue) &&
          // checking the visible attribute
          $file->attributes->getNamedItem('visible')->nodeValue == 'true' ) {
          $fileNode = array();
          $fileNode['name']  = $file->attributes->getNamedItem('name')->nodeValue;
          $fileNode['label'] = $file->attributes->getNamedItem('localized-name')->nodeValue;
          $fileNode['desc']  = $file->attributes->getNamedItem('description')->nodeValue;
          $fileNode['type']  = $file->attributes->getNamedItem('isDirectory')->nodeValue == 'true' ? 'folder' : 'file';
          $fileNode['files'] = array();
          // omiting the reports content folder
          if ( $file->attributes->getNamedItem('isDirectory')->nodeValue == 'true' ) {
            if ( $fileNode['name'] != 'reports_content' ){
              // recursively checks if the current node has child nodes
              $pathFile = $path . PATH_SEP . $fileNode['name'];
              $fileNode['files'] = $this->walkThruFiles ($pathFile, $file);
            }
          }
          
          //check if the file (or directory) is registered,
          $found = false;
          foreach ($this->aReports as $aReport) {
            if ($aReport['REP_TITLE']==$fileNode['name'] && $aReport['REP_PATH']==$path ) {
               $found = true;
            }
          }
    
          // if its a file and its not registered in the database create the entry
          if (! $found ) {
            if ( $fileNode['name'] != 'reports_content' ){
              $fileNode['path'] = $path;
              $this->registerReportIntoDB($fileNode);
            }
          }
          // assembling the return array with the current node
          $aFiles[] = $fileNode;
        }
      }
      return $aFiles;
    }

   /**
    * This method gets information from the solution repository, it gets the whole directory tree of files from the main Pentaho Solution.
    * @return Array array of the solution repository
    */
    function getSolutionRepository() {
      try {
        $pluginTemp = PATH_PLUGINS.'pentahoreports'.PATH_SEP.'temp';
        // checking the temp folder
        if(!file_exists($pluginTemp)){
          mkdir($pluginTemp, 0777, true);
          chmod($pluginTemp, 0777);
        }
        // assembling the connection string
        $pentahoRepository = $this->sPentahoServer . '/SolutionRepositoryService?';
        $pentahoRepository .= 'component=getSolutionRepositoryDoc';
        $pentahoRepository .= '&filter=';
        // assembling the curl request
        $ch = curl_init();
        $params = array();
        curl_setopt($ch, CURLOPT_URL, $pentahoRepository );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch, CURLOPT_HEADER, true );
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false );
        curl_setopt($ch, CURLOPT_AUTOREFERER, true );
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $this->sPentahoUsername . ':' . $this->sPentahoPassword );

        curl_setopt($ch, CURLOPT_TIMEOUT, 0);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        // executing the curl request
        $response = curl_exec($ch);
        $headers  = curl_getinfo($ch);
        $header  = substr ( $response, 0, $headers['header_size'] );
        $content = substr ( $response, $headers['header_size'] );
        // closing curl connection
        curl_close($ch);
        // evaluating the responses
        if ( trim ( $content) == '' )
          throw ( new Exception ( $this->sPentahoServer . " is not a valid Pentaho Server URL, please check the Pentaho Setup for this workspace." ) );
        // if the server doesn't fulfill the request
        if ( $headers['http_code'] == 500 ) {
          throw ( new Exception ( "HTTP Status 500 : The server encountered an internal error () that prevented it from fulfilling this request." ) );
        }
        if ( $headers['http_code'] != 200 ) {
          throw ( new Exception ( $content ) );
        }
        // parsing the server response
        $doc  = new DOMDocument();
        $doc -> loadXml( $content );
        $repo = $doc->getElementsByTagName( "repository" );
        $path = "";
        
        // previous to walk thru the file, we are caching the complete list of folders registered
        $report = new PhReport();
        $this -> aReports = $report->getAllReports();
    
        // walk thru the files and folder on repository
        $this->aSolutionRepository = $this->walkThruFiles ($path, $repo->item(0));
        return $this->aSolutionRepository;
      }
      catch ( Exception $e ) {
        throw ( $e  );
      }
    }

    /**
     * This method validate if a folder exists within a solution.
     * @param String $folder folder to search
     * @param String $solution solution to search in
     * @return Boolean $found true/false
     */
    function existsFolder( $folder , $solution) {
      $found = false;
      foreach ( $solution as $root => $files ) {
        if ( $files['name'] == $folder ) {
          $found = true;
        }
      }
      return $found;
    }

    /**
     * This method gets only the repository for this workspace
     * if the workspace didn't exist in pentaho solution, This method
     * will return a valid error code. The folder and other
     * files needs to be created using other methods.
     * @param String $workspace
     * @return Boolean $found true/false
     */
    function getSolutionWorkspace( $workspace ) {
      try {
        // getting the solution repository array
        $solution = $this->getSolutionRepository();
        $solWorkspace = array();
        $found = false;
        // iterating the array in order to get the workspace related reports/folders
        foreach ( $solution as $root => $files ) {
          if ( $files['name'] == $workspace ) {
            $solWorkspace = $files['files'];
            $found = true;
          }
        }
        
        //if not found workspace repository create a new repository
        if ( ! $found ) {
          throw ( new Exception ( "the Pentaho Solution Folder is empty, or you need to rebuild the Pentaho Solution " ) );
          //$this->createNewSolutionWorkspace( $workspace ); this was before..
        }
        // removing the workspace default folder files
        foreach ( $solWorkspace as $index => $files ) {
          if ( $files['name'] == 'reports_content' ) {
            unset ( $solWorkspace[ $index ] );
          }
        }

        return $solWorkspace;
      }
      catch ( Exception $e ) {
        throw ( $e  );
      }
    }

    /**
     * This method creates all the components required for the connection
     * between the Pentaho Bi server and ProcessMaker also generates and
     * upload the default dashboard reports
     * @param String $workspace workspace name
     * @return true
     */
    function createNewSolutionWorkspace( $workspace ) {
      try {
        //create root folder
        $response = $this->createNewFolder( '/', $workspace, "solution repository for workspace $workspace" );
        //create mondrian and metadata files
        $this->createDefaultMondrian( $workspace );
        //$this->createDefaultXmiFile( $workspace );

        //create default PM tables folders
        //$response = $this->createNewFolder( '/' . $workspace, 'PM Tables', "PM Tables for $workspace" );
        //create default folders
        $response = $this->createNewFolder( '/' . $workspace, 'reports_content', "hidden folder for dashboard files" );
        
        //create default dashboards
        $this->createDefaultDashboards( $workspace );

        //create default reports
        $this->createDefaultReports( $workspace );

        //create default pmtables reports
        //$this->createDefaultPmTables( $workspace );
        
        return true;
      }
      catch ( Exception $e ) {
        throw ( $e  );
      }
    }

    /**
     * Confirming the workspace folder exists, the plugin tries to
     * update the main files, mondrian, metadata an other files
     * @deprecated
     * @param String $folder folder to search
     * @param String $solution solution to search in
     * @return Boolean $found true/false
     */
    function updateSolutionWorkspace( $workspace ) {
      try {
/*        //create root folder
        $response = $this->createNewFolder( '/', $workspace, "solution repository for workspace $workspace" );
        $this->createDefaultXmiFile( $workspace );

        //if not exist PM Tables folder, we need to create it.
        if ( !$this->existsFolder( 'PM Tables' , $solWorkspace) ) {
          $response = $this->createNewFolder( '/' . $workspace, 'PM Tables', "PM Tables for $workspace" );
        }

        //create the folder for each process, in case the folder it doesn't exist.
        if ( !is_null( $processes ) && count ( $processes) > 1 ) {
          unset ( $processes[0] );
          foreach ( $processes as $key => $val ) {
            if ( !$this->existsFolder(  $val['PRO_TITLE'] , $solWorkspace) ) {
              $response = $this->createNewFolder( '/' . $workspace, $val['PRO_TITLE']  , $val['PRO_TITLE'] );
            }
          }//for processes
        }
  */
        return $true;
      }
      catch ( Exception $e ) {
        throw ( $e  );
      }
    }

    /**
     * This method creates the main folder in the pentaho respository
     * @param $path String main folder path
     * @param $name String
     * @param $desc String
     * @return first node found
     */
    function createMainFolder( $path, $name, $desc ) {
      try {
        // assembling the curl connection string
        $pentahoRepository = $this->sPentahoServer . '/SolutionRepositoryService?';
        $pentahoRepository .= 'component=createNewFolder';
        $pentahoRepository .= '&path=' . urlencode($path);
        $pentahoRepository .= '&name=' . urlencode( isset($name) ? $name : '' );
        $pentahoRepository .= '&desc=' . urlencode( isset($desc) ? $desc : '' );
        // initializing the curl request
        $ch = curl_init();
        $params = array();
        curl_setopt($ch, CURLOPT_URL, $pentahoRepository );
        curl_setopt($ch, CURLOPT_HEADER, true );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $this->sPentahoSuperUsername . ':' . $this->sPentahoSuperPassword );
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        // executing the curl request also capturing the server response
        $response = curl_exec($ch);
        $headers  = curl_getinfo($ch);
        $header  = substr ( $response, 0, $headers['header_size'] );
        $content = substr ( $response, $headers['header_size'] );
        // closing connection
        curl_close($ch);
        // evaluating the server responses
        if ( trim ( $content ) == '' )
          throw ( new Exception ( $this->sPentahoServer . " is not a valid Pentaho Server URL, please check the Pentaho Setup for this workspace." ) );
        if ( $headers['http_code'] == 500 ) {
          throw ( new Exception ( "HTTP Status 500 : The server encountered an internal error () that prevented it from fulfilling this request." ) );
        }
        if ( $headers['http_code'] != 200 ) {
          throw ( new Exception ( $content ) );
        }
        // obtaining and parsing the reports list
        $doc = new DOMDocument();
        $doc->loadXml( $content );
        $res = $doc->getElementsByTagName( "result" );
        // returning the main node response
        return $res->item( 0 )->firstChild->nodeValue;
      }
      catch ( Exception $e ) {
        throw ( $e );
      }
    }
    
    /**
     * This method creates a new folder in the pentaho respository
     * @param $path String
     * @param $name String
     * @param $desc String
     * @return Array first node of the folder
     */
    function createNewFolder( $path, $name, $desc ) {
      try {
        // assembling the curl connection string
        $pentahoRepository = $this->sPentahoServer . '/SolutionRepositoryService?';
        $pentahoRepository .= 'component=createNewFolder';
        $pentahoRepository .= '&path=' . urlencode($path);
        $pentahoRepository .= '&name=' . urlencode( isset($name) ? $name : '' );
        $pentahoRepository .= '&desc=' . urlencode( isset($desc) ? $desc : '' );
        // initializing the curl request
        $ch = curl_init();
        $params = array();
        curl_setopt($ch, CURLOPT_URL, $pentahoRepository );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $this->sPentahoUsername . ':' . $this->sPentahoPassword );
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        // executing the curl request also capturing the server response
        $response = curl_exec($ch);
        // closing connection
        curl_close($ch);
        // parsing server response
        $doc = new DOMDocument();
        $doc->loadXml( $response );
        $res = $doc->getElementsByTagName( "result" );
	// returning the server response
        return $res->item( 0 )->firstChild->nodeValue;
      }
      catch ( Exception $e ) {
        throw ( $e );
      }
    }

    /**
     * This method returns the html code for the iframe with a pentaho Pivot
     * @return String $response html response
     */
    function showPivot( ) {
      try {
        // assembling the curl connection string
        $pentahoRepository = $this->sPentahoServer ;
        $pentahoRepository .= '/Pivot?solution=processmaker&path=&action=asdfg.analysisview.xaction';
        // initializing the curl request
        $ch = curl_init();
        $params = array();
        curl_setopt($ch, CURLOPT_URL, $pentahoRepository );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $this->sPentahoUsername . ':' . $this->sPentahoPassword );
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, "true");
        
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        
        //curl_setopt($ch, CURLOPT_POST, 1);
        //curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        // executing the curl request also capturing the server response
        $response = curl_exec($ch);
        // clossing connection
        curl_close($ch);
        // returning server response
        return $response;
      }
      catch ( Exception $e ) {
        throw ( $e );
      }
    }

    /**
     * This method returns the html code required in order to render the visualization of a report
     * @param String $solution
     * @param String $path
     * @param String $file
     * @param String $user_uid
     * @return String first node of the folder
     */
    function ViewAction( $solution, $path, $file, $user_uid ) {
      // initializing variables
      $report = new PhReport();
      $roleReport = new PhRoleReport();
      $repUid = $file;
      // check if the report is currently assigned to the user based in the role that he has
      if (!$roleReport->userIsAssignedReport($_SESSION['USER_LOGGED'],$repUid)){
        return 'User doesn\'t have the privileges to view this report';
      }
      try {
        $extension = substr( $file, -5 );
        // checking the type of file that will be rendered
        switch ($extension){
          // comunity dashboard files
          case  '.xcdf':
            $pentahoRepository = $this->sPentahoServer ;
            $pentahoRepository .= '/content/pentaho-cdf/RenderXCDF?';
            $pentahoRepository .= 'solution=' . $solution ;
            $pentahoRepository .= '&path=' . $path;
            $pentahoRepository .= '&action=' . $file;
            $pentahoRepository .= '&user_uid=' . $user_uid;
            $pentahoRepository .= '&template=processmaker';
            $content = "<iframe id='reportContentFrame' frameborder='0' src='" . $pentahoRepository . "&userid=" . $this->sPentahoUsername . "&password=" . $this->sPentahoPassword . "' width='99%' height= '99%'></iframe>";
            return $content;
          break;
          // pentaho reports files
          case '.prpt':
            $pentahoRepository = $this->sPentahoServer ;
            $pentahoRepository .= '/content/reporting/reportviewer/report.html?';
            $pentahoRepository .= 'solution=' . $solution ;
            $pentahoRepository .= '&path=' . $path;
            $pentahoRepository .= '&name=' . $file;
            $pentahoRepository .= '&user_uid=' . $user_uid;
            $pentahoRepository .= '&userid=' . $this->sPentahoUsername;
            $pentahoRepository .= '&password=' . $this->sPentahoPassword;
            $pentahoRepository .= '#output-type=text/html';
            $pentahoRepository .= '&accepted-page=0';
            $pentahoRepository .= '&output-type=text/html';
            $pentahoRepository .= '&solution=' . $solution;
            $pentahoRepository .= '&path=' . $path;
            $pentahoRepository .= '&name=' . $file;
            $content = "<iframe id='reportContentFrame' frameborder='0' src='" . $pentahoRepository . "' width='99%' height= '99%'></iframe>";
            return $content;
          break;
          // default pivot or analysis view
          default :
            $pentahoRepository = $this->sPentahoServer ;
            $pentahoRepository .= '/ViewAction?';
            $pentahoRepository .= 'solution=' . urlencode( $solution );
            $pentahoRepository .= '&path=' .  $path ;
            $pentahoRepository .= '&action=' . urlencode( $file );
          break;
        }
        // initializing curl request
        $ch = curl_init();
        $params = array();
        curl_setopt($ch, CURLOPT_URL, $pentahoRepository );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch, CURLOPT_HEADER, true );
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false );
        curl_setopt($ch, CURLOPT_AUTOREFERER, true );
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $this->sPentahoUsername . ':' . $this->sPentahoPassword );
                
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        // executing curl request
        $response = curl_exec($ch);
        $headers  = curl_getinfo($ch);
        $header  = substr ( $response, 0, $headers['header_size'] );
        $content = substr ( $response, $headers['header_size'] );

        if ( $headers['http_code'] == 500 ) {
          throw ( new Exception ( "HTTP Status 500 : The server encountered an internal error () that prevented it from fulfilling this request." ) );
        }
        if ( $headers['http_code'] == 200 ) {
          curl_close($ch);
          return $content;
        }
        // rendering and returning the content that will be rendered by the div tag
        if ( $headers['http_code'] == 302 ) {
          curl_close($ch);
          $location = substr ( $header, strpos ($header, 'Location:' ) + 10 );
          $location = trim(substr ( $location, 0, strpos ($location, "\n" ) ));
          $content = "<iframe id='reportContentFrame' frameborder='0' src='" . $location . "&userid=" . $this->sPentahoUsername . "&password=" . $this->sPentahoPassword . "' width='99%' height= '481'></iframe>";
          return $content;
        }
        return $content;
      }
      catch ( Exception $e ) {
        throw ( $e  );
      }
    }

    //send the default metadata.xmi file to workspace repository
    //int FILE_EXISTS = 1;
    //int FILE_ADD_FAILED = 2;
    //int FILE_ADD_SUCCESSFUL = 3;
    //int FILE_ADD_INVALID_PUBLISH_PASSWORD = 4;
    //int FILE_ADD_INVALID_USER_CREDENTIALS = 5;
    /**
     * This method sends the metadata file to the pentaho bi server
     * @param String $path
     * @param String $filename
     * @return Mixed $response the response of the curl execution
     */
    function sendMetadataFile( $path, $filename ) {
      try {
        // assembling the curl connection string
        $md5PublishPassword =  hash('md5', $this->sPentahoPublishPassword . 'P3ntah0Publ1shPa55w0rd' );
        $pentahoRepository = $this->sPentahoServer . '/RepositoryFilePublisher?';
        $pentahoRepository .= 'publishPath=' . urlencode($path);
        $pentahoRepository .= '&publishKey=' . urlencode( $md5PublishPassword);
        $pentahoRepository .= '&overwrite=' . urlencode('true');
        // initializing the curl request parameters
        $ch = curl_init();
        $params = array();
        $params['metadata.xmi'] = '@'.$filename;
        curl_setopt($ch, CURLOPT_URL, $pentahoRepository );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $this->sPentahoUsername . ':' . $this->sPentahoPassword );
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        // executing the curl request
        $response = curl_exec($ch);
        curl_close($ch);
        // returning the response
        return $response;
      }
      catch ( Exception $e ) {
        throw ( $e  );
      }
    }
  
    //send one file to workspace repository
    //int FILE_EXISTS = 1;
    //int FILE_ADD_FAILED = 2;
    //int FILE_ADD_SUCCESSFUL = 3;
    //int FILE_ADD_INVALID_PUBLISH_PASSWORD = 4;
    //int FILE_ADD_INVALID_USER_CREDENTIALS = 5;
    /**
     * This method sends the a report file to the pentaho bi server
     * @param String $path
     * @param String $filename
     * @return Mixed $response the response of the curl execution
     */
    function sendFile( $pentahoFilename, $path, $localFilename ) {
      try {
        // assembling the curl connection string
        $md5PublishPassword =  hash('md5', $this->sPentahoPublishPassword . 'P3ntah0Publ1shPa55w0rd' );
        $pentahoRepository = $this->sPentahoServer . '/RepositoryFilePublisher?';
        $pentahoRepository .= 'publishPath=' . urlencode($path);
        $pentahoRepository .= '&publishKey=' . urlencode( $md5PublishPassword);
        $pentahoRepository .= '&overwrite=' . urlencode('true');
        // initializing the curl request parameters
        $ch = curl_init();
        $params = array();
        $params[ $pentahoFilename ] = '@'.$localFilename;
        curl_setopt($ch, CURLOPT_URL, $pentahoRepository );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $this->sPentahoUsername . ':' . $this->sPentahoPassword );
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        // executing the curl request and sending the file
        $response = curl_exec($ch);
        // closing connection
        curl_close($ch);
        // returning the server response
        return $response;
      }
      catch ( Exception $e ) {
        throw ( $e  );
      }
    }
  
    //send a string to be a file in the workspace repository
    //int FILE_EXISTS = 1;
    //int FILE_ADD_FAILED = 2;
    //int FILE_ADD_SUCCESSFUL = 3;
    //int FILE_ADD_INVALID_PUBLISH_PASSWORD = 4;
    //int FILE_ADD_INVALID_USER_CREDENTIALS = 5;
    /**
     * This method sends the content of a file into the pentaho repository
     * @param String $pentahoFilename  report file name
     * @param String $path  publish path
     * @param String $content  content file
     * @return Mixed $response the response of the curl execution
     */
    function sendFileContent( $pentahoFilename, $path, $content ) {
      try {
        // assembling the curl connection string
        $localFilename = PATH_PLUGINS . 'pentahoreports' . PATH_SEP . 'temp' . PATH_SEP . $pentahoFilename ;
        file_put_contents ( $localFilename, $content ) ;

        $md5PublishPassword =  hash('md5', $this->sPentahoPublishPassword . 'P3ntah0Publ1shPa55w0rd' );
        $pentahoRepository = $this->sPentahoServer . '/RepositoryFilePublisher?';
        $pentahoRepository .= 'publishPath=' . urlencode($path);
        $pentahoRepository .= '&publishKey=' . urlencode( $md5PublishPassword);
        $pentahoRepository .= '&overwrite=' . urlencode('true');
        // positioning the directory to the folder file
        chdir( PATH_PLUGINS . 'pentahoreports' . PATH_SEP . 'temp' . PATH_SEP );
        // initializing the curl request parameters
        $ch = curl_init();
        $params = array();
        //$params[ $pentahoFilename ] = '@'.$localFilename;
        $params[ $pentahoFilename ] = '@'.$pentahoFilename;

        curl_setopt($ch, CURLOPT_URL, $pentahoRepository );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $this->sPentahoUsername . ':' . $this->sPentahoPassword );
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
//        curl_setopt($ch, CURLOPT_TIMEOUT, 0);
//        curl_setopt($ch, CURLOPT_CONNECTIONTIMEOUT, 0);
        //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        // executing the curl request
        $response = curl_exec($ch);
        // closing the connection
        curl_close($ch);
        // removing temporal report file
        unlink($localFilename);
        // evaluating server responses
        if ( $response == 2 ) throw ( new Exception ( "Error in add file '$pentahoFilename' to Pentaho Solution" ) );
        if ( $response == 4 ) throw ( new Exception ( 'Invalid Publish Password' ) );
        if ( $response == 4 ) throw ( new Exception ( 'Invalid User Credentials' ) );
        // returning the server response
        return $response;
      }
      catch ( Exception $e ) {
        throw ( new Exception ( 'Error uploading file to Pentaho Server : ' . $e->getMessage()  ) );
      }
    }

    /**
     * This method creates the metadata.xmi file using the default.xmi
     * and the current database information
     * @param String $pentahoFilename
     * @param String $path
     * @param String $content
     * @return Mixed $response the response of the curl execution
     */
    function createDefaultXmiFile ( $workspace ) {
      // setting the metadata template file
      $defaultXmiTpl = PATH_PLUGINS . 'pentahoreports' . PATH_SEP . 'defaults' . PATH_SEP . 'default.xmi';
      $template = new TemplatePower( $defaultXmiTpl );
      $template->prepare();
  
      $con = Propel::getConnection('workflow');
      // getting the mysql hostname
      $sql = "SHOW VARIABLES LIKE 'hostname'";
      $stmt = $con->createStatement();
      $rs = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);
      $rs->next();
      $row = $rs->getRow ();
      $mysqlHostname = $row['Value'];
      // getting mysql port
      $sql = "SHOW VARIABLES LIKE 'port'";
      $stmt = $con->createStatement();
      $rs = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);
      $rs->next();
      $row = $rs->getRow ();
      $mysqlPort = $row['Value'];
      // initializing the template parameters
      $pentahoServer = str_replace( 'https://', '', trim($this->sPentahoServer) );
      $pentahoServer = str_replace( 'http://', '', $pentahoServer);
      if ( strpos($this->sPentahoServer,':') )
        $pentahoServer = substr ( $pentahoServer, 0, strpos($pentahoServer, ':'));
  
      $databaseServer   = $mysqlHostname;
      $databaseType     = 'MYSQL';
      $databaseAccess   = 'Native';
      $databaseDatabase = 'wf_' . $workspace;
      $databasePort     = $mysqlPort;
      $databaseUsername = 'ph_' . $workspace;
      $databasePassword = DB_PASS;
      $databaseJdbcUrl  = "jdbc:mysql://$databaseServer:$databasePort/$databaseDatabase?defaultFetchSize=500&amp;useCursorFetch=true";
      // replacing the variables with the template parameters
      $template->assign( 'WORKSPACE',           $workspace        );
      $template->assign( 'DATABASE_SERVER',     $databaseServer   );
      $template->assign( 'DATABASE_TYPE',       $databaseType     );
      $template->assign( 'DATABASE_ACCESS',     $databaseAccess   );
      $template->assign( 'DATABASE_DATABASE',   $databaseDatabase );
      $template->assign( 'DATABASE_PORT',       $databasePort     );
      $template->assign( 'DATABASE_USERNAME',   $databaseUsername );
      $template->assign( 'DATABASE_PASSWORD',   $databasePassword );
      $template->assign( 'DATABASE_JDBC_URL',   $databaseJdbcUrl  );
      // generating file
      $content = $template->getOutputContent();
      // uploading the generated file
      $this->sendFileContent( 'metadata.xmi', $workspace, $content );
    }

    /**
     * This method creates the processmaker.mondrian.xml cube file using the default data, and publish that file into the server.
     * @param String $workspace
     * @return void
     */
    function createDefaultMondrian ( $workspace ) {
      // initializing the template file
      $defaultTpl = PATH_PLUGINS . 'pentahoreports' . PATH_SEP . 'defaults' . PATH_SEP . 'default.mondrian.xml';
      $template = new TemplatePower( $defaultTpl );
      $template->prepare();
      // replacing the variables with the real data
      $template->assign( 'SOLUTION', $workspace   );
      $template->assign( 'JNDI_SOLUTION', 'wf_' . $workspace   );
      // generating the file
      $content = $template->getOutputContent();
      // uploading the file
      $this->sendFileContent( 'processmaker.mondrian.xml', $workspace, $content );
    }

    /**
     * This method creates Default Dashboards files using the default data. and publish those files into the server.
     * @param String $workspace
     * @todo   generate a new method that encapsulates all the steps required to generate each report
     * @return void
     */
    function createDefaultDashboards ( $workspace ) {
      // first dashboard
      // assembling the connection string and creating the report folder
      $pathBase = PATH_PLUGINS . 'pentahoreports' . PATH_SEP . 'defaults' . PATH_SEP . 'report_1' . PATH_SEP ;
      $pathRepo = '/' . $workspace . '/reports_content/report_1';
      $response = $this->createNewFolder( '/' . $workspace . '/reports_content/', 'report_1', "hidden folder" );

      // preparing template file
      $defaultTpl = $pathBase . 'report_1.xcdf';
      $content = file_get_contents ( $defaultTpl );
      // sending file content
      $this->sendFileContent( 'report_1.xcdf', $workspace, $content );

      // preparing template file
      $defaultTpl = $pathBase . 'report_1_template.html';
      $template = new TemplatePower( $defaultTpl );
      $template->prepare();
      $template->assign( 'SOLUTION',      $workspace   );
      $template->assign( 'JNDI_SOLUTION', 'wf_' . $workspace   );
      $content = $template->getOutputContent();
      // sending file content
      $this->sendFileContent( 'report_1_template.html', $pathRepo, $content );

      // preparing template file
      $defaultTpl = $pathBase . 'parameter.waqr.xaction';
      $template = new TemplatePower( $defaultTpl );
      $template->prepare();
      $template->assign( 'SOLUTION',      $workspace   );
      $template->assign( 'JNDI_SOLUTION', 'wf_' . $workspace   );
      $content = $template->getOutputContent();
      // sending file content
      $this->sendFileContent( 'parameter.waqr.xaction', $pathRepo, $content );

      // preparing template file
      $defaultTpl = $pathBase . 'parameter_template.html';
      $content = file_get_contents ( $defaultTpl );
      // sending file content
      $this->sendFileContent( 'parameter_template.html', $pathRepo, $content );

      // preparing template file
      $defaultTpl = $pathBase . 'tasks_pie.waqr.xaction';
      $template = new TemplatePower( $defaultTpl );
      $template->prepare();
      $template->assign( 'SOLUTION',      $workspace   );
      $template->assign( 'JNDI_SOLUTION', 'wf_' . $workspace   );
      $content = $template->getOutputContent();
      // sending file content
      $this->sendFileContent( 'tasks_pie.waqr.xaction', $pathRepo, $content );
                                
      // preparing template file
      $defaultTpl = $pathBase . 'cases_bars.waqr.xaction';
      $template = new TemplatePower( $defaultTpl );
      $template->prepare();
      $template->assign( 'SOLUTION',      $workspace   );
      $template->assign( 'JNDI_SOLUTION', 'wf_' . $workspace   );
      $content = $template->getOutputContent();
      // sending file content
      $this->sendFileContent( 'cases_bars.waqr.xaction', $pathRepo, $content );

      // preparing template file
      $defaultTpl = $pathBase . 'user_trends.waqr.xaction';
      $template = new TemplatePower( $defaultTpl );
      $template->prepare();
      $template->assign( 'SOLUTION',      $workspace   );
      $template->assign( 'JNDI_SOLUTION', 'wf_' . $workspace   );
      $content = $template->getOutputContent();
      // sending file content
      $this->sendFileContent( 'user_trends.waqr.xaction', $pathRepo, $content );
      
      // second dashboard
      // assembling the connection string and creating the report folder
      $pathBase = PATH_PLUGINS . 'pentahoreports' . PATH_SEP . 'defaults' . PATH_SEP . 'report_2' . PATH_SEP ;
      $pathRepo = '/' . $workspace . '/reports_content/report_2';
      $response = $this->createNewFolder( '/' . $workspace . '/reports_content/', 'report_2', "hidden folder" );
      
      // preparing template file
      $defaultTpl = $pathBase . 'report_2.xcdf';
      $content = file_get_contents ( $defaultTpl );
      // sending file content
      $this->sendFileContent( 'report_2.xcdf', $workspace, $content );

      // preparing template file
      $defaultTpl = $pathBase . 'report_2_template.html';
      $template = new TemplatePower( $defaultTpl );
      $template->prepare();
      $template->assign( 'SOLUTION',      $workspace   );
      $template->assign( 'JNDI_SOLUTION', 'wf_' . $workspace   );
      $content = $template->getOutputContent();
      // sending file content
      $this->sendFileContent( 'report_2_template.html', $pathRepo, $content );

      // preparing template file
      $defaultTpl = $pathBase . 'process_bars_chart.waqr.xaction';
      $template = new TemplatePower( $defaultTpl );
      $template->prepare();
      $template->assign( 'SOLUTION',      $workspace   );
      $template->assign( 'JNDI_SOLUTION', 'wf_' . $workspace   );
      $content = $template->getOutputContent();
      // sending file content
      $this->sendFileContent( 'process_bars_chart.waqr.xaction', $pathRepo, $content );

      // third dashboard
      // assembling the connection string and creating the report folder
      $pathBase = PATH_PLUGINS . 'pentahoreports' . PATH_SEP . 'defaults' . PATH_SEP . 'report_3' . PATH_SEP ;
      $pathRepo = '/' . $workspace . '/reports_content/report_3';
      $response = $this->createNewFolder( '/' . $workspace . '/reports_content/', 'report_3', "hidden folder" );
      // preparing template file
      $defaultTpl = $pathBase . 'report_3.xcdf';
      $content = file_get_contents ( $defaultTpl );
      // sending file content
      $this->sendFileContent( 'report_3.xcdf', $workspace, $content );

      // preparing template file
      $defaultTpl = $pathBase . 'report_3_template.html';
      $template = new TemplatePower( $defaultTpl );
      $template->prepare();
      $template->assign( 'SOLUTION',      $workspace   );
      $template->assign( 'JNDI_SOLUTION', 'wf_' . $workspace   );
      $content = $template->getOutputContent();
      // sending file content
      $this->sendFileContent( 'report_3_template.html', $pathRepo, $content );

      // preparing template file
      $defaultTpl = $pathBase . 'processes_time_comparison.waqr.xaction';
      $template = new TemplatePower( $defaultTpl );
      $template->prepare();
      $template->assign( 'SOLUTION',      $workspace   );
      $template->assign( 'JNDI_SOLUTION', 'wf_' . $workspace   );
      $content = $template->getOutputContent();
      // sending file content
      $this->sendFileContent( 'processes_time_comparison.waqr.xaction', $pathRepo, $content );
      
      // fourth dashboard
      // assembling the connection string and creating the report folder
      $pathBase = PATH_PLUGINS . 'pentahoreports' . PATH_SEP . 'defaults' . PATH_SEP . 'report_4' . PATH_SEP ;
      $pathRepo = '/' . $workspace . '/reports_content/report_4';
      $response = $this->createNewFolder( '/' . $workspace . '/reports_content/', 'report_4', "hidden folder" );

      // preparing template file
      $defaultTpl = $pathBase . 'report_4.xcdf';
      $content = file_get_contents ( $defaultTpl );
      // sending file content
      $this->sendFileContent( 'report_4.xcdf', $workspace, $content );

      // preparing template file
      $defaultTpl = $pathBase . 'report_4_template.html';
      $template = new TemplatePower( $defaultTpl );
      $template->prepare();
      $template->assign( 'SOLUTION',      $workspace   );
      $template->assign( 'JNDI_SOLUTION', 'wf_' . $workspace   );
      $content = $template->getOutputContent();
      // sending file content
      $this->sendFileContent( 'report_4_template.html', $pathRepo, $content );

      // preparing template file
      $defaultTpl = $pathBase . 'parameter_template.html';
      $template = new TemplatePower( $defaultTpl );
      $template->prepare();
      $template->assign( 'SOLUTION',      $workspace   );
      $template->assign( 'JNDI_SOLUTION', 'wf_' . $workspace   );
      $content = $template->getOutputContent();
      // sending file content
      $this->sendFileContent( 'parameter_template.html', $pathRepo, $content );

      // preparing template file
      $defaultTpl = $pathBase . 'parameter.waqr.xaction';
      $template = new TemplatePower( $defaultTpl );
      $template->prepare();
      $template->assign( 'SOLUTION',      $workspace   );
      $template->assign( 'JNDI_SOLUTION', 'wf_' . $workspace   );
      $content = $template->getOutputContent();
      // sending file content
      $this->sendFileContent( 'parameter.waqr.xaction', $pathRepo, $content );
      
      // preparing template file
      $defaultTpl = $pathBase . 'time_process_comparison.waqr.xaction';
      $template = new TemplatePower( $defaultTpl );
      $template->prepare();
      $template->assign( 'SOLUTION',      $workspace   );
      $template->assign( 'JNDI_SOLUTION', 'wf_' . $workspace   );
      $content = $template->getOutputContent();
      // sending file content
      $this->sendFileContent( 'time_process_comparison.waqr.xaction', $pathRepo, $content );
      
      // fifth & sixth dashboard
      // assembling the connection string and creating the report folder
      $pathBase = PATH_PLUGINS . 'pentahoreports' . PATH_SEP . 'defaults' . PATH_SEP . 'report_5' . PATH_SEP ;
      $pathRepo = '/' . $workspace . '/reports_content/report_5';
      $response = $this->createNewFolder( '/' . $workspace . '/reports_content/', 'report_5', "hidden folder" );

      // preparing template file
      $defaultTpl = $pathBase . 'report_5.xcdf';
      $content = file_get_contents ( $defaultTpl );
      // sending file content
      $this->sendFileContent( 'report_5.xcdf', $workspace, $content );

      // preparing template file
      $defaultTpl = $pathBase . 'report_5_template.html';
      $template = new TemplatePower( $defaultTpl );
      $template->prepare();
      $template->assign( 'SOLUTION',      $workspace   );
      $template->assign( 'JNDI_SOLUTION', 'wf_' . $workspace   );
      $content = $template->getOutputContent();
      // sending file content
      $this->sendFileContent( 'report_5_template.html', $pathRepo, $content );

      // preparing template file
      $defaultTpl = $pathBase . 'user_time_inbox.waqr.xaction';
      $template = new TemplatePower( $defaultTpl );
      $template->prepare();
      $template->assign( 'SOLUTION',      $workspace   );
      $template->assign( 'JNDI_SOLUTION', 'wf_' . $workspace   );
      $content = $template->getOutputContent();
      // sending file content
      $this->sendFileContent( 'user_time_inbox.waqr.xaction', $pathRepo, $content );
      
      // preparing template file
      $defaultTpl = $pathBase . 'users_time_comparison.waqr.xaction';
      $template = new TemplatePower( $defaultTpl );
      $template->prepare();
      $template->assign( 'SOLUTION',      $workspace   );
      $template->assign( 'JNDI_SOLUTION', 'wf_' . $workspace   );
      $content = $template->getOutputContent();
      // sending file content
      $this->sendFileContent( 'users_time_comparison.waqr.xaction', $pathRepo, $content );

      // seventh dashform
      // assembling the connection string and creating the report folder
      $pathBase = PATH_PLUGINS . 'pentahoreports' . PATH_SEP . 'defaults' . PATH_SEP . 'report_7' . PATH_SEP ;
      $pathRepo = '/' . $workspace . '/reports_content/report_7';
      $response = $this->createNewFolder( '/' . $workspace . '/reports_content/', 'report_7', "hidden folder" );

      // preparing template file
      $defaultTpl = $pathBase . 'report_7.xcdf';
      $content = file_get_contents ( $defaultTpl );
      // sending file content
      $this->sendFileContent( 'report_7.xcdf', $workspace, $content );

      // preparing template file
      $defaultTpl = $pathBase . 'report_7_template.html';
      $template = new TemplatePower( $defaultTpl );
      $template->prepare();
      $template->assign( 'SOLUTION',      $workspace   );
      $template->assign( 'JNDI_SOLUTION', 'wf_' . $workspace   );
      $content = $template->getOutputContent();
      // sending file content
      $this->sendFileContent( 'report_7_template.html', $pathRepo, $content );

      // preparing template file
      $defaultTpl = $pathBase . 'parameter.waqr.xaction';
      $template = new TemplatePower( $defaultTpl );
      $template->prepare();
      $template->assign( 'SOLUTION',      $workspace   );
      $template->assign( 'JNDI_SOLUTION', 'wf_' . $workspace   );
      $content = $template->getOutputContent();
      // sending file content
      $this->sendFileContent( 'parameter.waqr.xaction', $pathRepo, $content );
      
      // preparing template file
      $defaultTpl = $pathBase . 'parameter_template.html';
      $template = new TemplatePower( $defaultTpl );
      $template->prepare();
      $template->assign( 'SOLUTION',      $workspace   );
      $template->assign( 'JNDI_SOLUTION', 'wf_' . $workspace   );
      $content = $template->getOutputContent();
      // sending file content
      $this->sendFileContent( 'parameter_template.html', $pathRepo, $content );
          
      // preparing template file
      $defaultTpl = $pathBase . 'processes_time_comparison.waqr.xaction';
      $template = new TemplatePower( $defaultTpl );
      $template->prepare();
      $template->assign( 'SOLUTION',      $workspace   );
      $template->assign( 'JNDI_SOLUTION', 'wf_' . $workspace   );
      $content = $template->getOutputContent();
      // sending file content
      $this->sendFileContent( 'processes_time_comparison.waqr.xaction', $pathRepo, $content );
      
      // my efficiency dashboards
      // assembling the connection string and creating the report folder
      $pathBase = PATH_PLUGINS . 'pentahoreports' . PATH_SEP . 'defaults' . PATH_SEP . 'report_efficiency' . PATH_SEP ;
      $pathRepo = '/' . $workspace . '/reports_content/report_efficiency';
      $response = $this->createNewFolder( '/' . $workspace . '/reports_content/', 'report_efficiency', "hidden folder" );

      // preparing template main file
      $defaultTpl = $pathBase . 'report_efficiency.xcdf';
      $content = file_get_contents ( $defaultTpl );
      // sending file content
      $this->sendFileContent( 'report_efficiency.xcdf', $workspace, $content );

      // preparing template file
      $defaultTpl = $pathBase . 'report_efficiency_template.html';
      $template = new TemplatePower( $defaultTpl );
      $template->prepare();
      $template->assign( 'SOLUTION',      $workspace   );
      $template->assign( 'JNDI_SOLUTION', 'wf_' . $workspace   );
      $content = $template->getOutputContent();
      // sending file content
      $this->sendFileContent( 'report_efficiency_template.html', $pathRepo, $content );
   
      // preparing template file
      $defaultTpl = $pathBase . 'process_bars_chart.waqr.xaction';
      $template = new TemplatePower( $defaultTpl );
      $template->prepare();
      $template->assign( 'SOLUTION',      $workspace   );
      $template->assign( 'JNDI_SOLUTION', 'wf_' . $workspace   );
      $content = $template->getOutputContent();
      // sending file content
      $this->sendFileContent( 'process_bars_chart.waqr.xaction', $pathRepo, $content );
      
      // preparing template file
      $defaultTpl = $pathBase . 'tasks_pie.waqr.xaction';
      $template = new TemplatePower( $defaultTpl );
      $template->prepare();
      $template->assign( 'SOLUTION',      $workspace   );
      $template->assign( 'JNDI_SOLUTION', 'wf_' . $workspace   );
      $content = $template->getOutputContent();
      // sending file content
      $this->sendFileContent( 'tasks_pie.waqr.xaction', $pathRepo, $content );
      
      // preparing template file
      $defaultTpl = $pathBase . 'time_process_comparison.waqr.xaction';
      $template = new TemplatePower( $defaultTpl );
      $template->prepare();
      $template->assign( 'SOLUTION',      $workspace   );
      $template->assign( 'JNDI_SOLUTION', 'wf_' . $workspace   );
      $content = $template->getOutputContent();
      // sending file content
      $this->sendFileContent( 'time_process_comparison.waqr.xaction', $pathRepo, $content );
      
      // preparing template file
      $defaultTpl = $pathBase . 'user_time_inbox.waqr.xaction';
      $template = new TemplatePower( $defaultTpl );
      $template->prepare();
      $template->assign( 'SOLUTION',      $workspace   );
      $template->assign( 'JNDI_SOLUTION', 'wf_' . $workspace   );
      $content = $template->getOutputContent();
      // sending file content
      $this->sendFileContent( 'user_time_inbox.waqr.xaction', $pathRepo, $content );

      // eighth dashboard
      // assembling the connection string and creating the report folder
      $pathBase = PATH_PLUGINS . 'pentahoreports' . PATH_SEP . 'defaults' . PATH_SEP . 'report_8' . PATH_SEP ;
      $pathRepo = '/' . $workspace . '/reports_content/report_8';
      $response = $this->createNewFolder( '/' . $workspace . '/reports_content/', 'report_8', "hidden folder" );

      // preparing template main file
      $defaultTpl = $pathBase . 'report_8.xcdf';
      $content = file_get_contents ( $defaultTpl );
      // sending file content
      $this->sendFileContent( 'report_8.xcdf', $workspace, $content );

      // preparing template file
      $defaultTpl = $pathBase . 'report_8_template.html';
      $template = new TemplatePower( $defaultTpl );
      $template->prepare();
      $template->assign( 'SOLUTION',      $workspace   );
      $template->assign( 'JNDI_SOLUTION', 'wf_' . $workspace   );
      $content = $template->getOutputContent();
      // sending file content
      $this->sendFileContent( 'report_8_template.html', $pathRepo, $content );

      // preparing template file
      $defaultTpl = $pathBase . 'parameter.waqr.xaction';
      $template = new TemplatePower( $defaultTpl );
      $template->prepare();
      $template->assign( 'SOLUTION',      $workspace   );
      $template->assign( 'JNDI_SOLUTION', 'wf_' . $workspace   );
      $content = $template->getOutputContent();
      // sending file content
      $this->sendFileContent( 'parameter.waqr.xaction', $pathRepo, $content );

      // preparing template file
      $defaultTpl = $pathBase . 'timeMeasure.waqr.xaction';
      $template = new TemplatePower( $defaultTpl );
      $template->prepare();
      $template->assign( 'SOLUTION',      $workspace   );
      $template->assign( 'JNDI_SOLUTION', 'wf_' . $workspace   );
      $content = $template->getOutputContent();
      // sending file content
      $this->sendFileContent( 'timeMeasure.waqr.xaction', $pathRepo, $content );

      // preparing template file
      $defaultTpl = $pathBase . 'parameter_template.html';
      $content = file_get_contents ( $defaultTpl );
      // sending file content
      $this->sendFileContent( 'parameter_template.html', $pathRepo, $content );

      // preparing template file
      $defaultTpl = $pathBase . 'user_trends.waqr.xaction';
      $template = new TemplatePower( $defaultTpl );
      $template->prepare();
      $template->assign( 'SOLUTION',      $workspace   );
      $template->assign( 'JNDI_SOLUTION', 'wf_' . $workspace   );
      $content = $template->getOutputContent();
      // sending file content
      $this->sendFileContent( 'user_trends.waqr.xaction', $pathRepo, $content );

    }

    /**
     * This method creates Default Analysis Views Reports files using the default data
     *
     * @param String $workspace
     * @return void
     */
    function createDefaultReports ( $workspace ) {
      // preparing template file
      $defaultTpl = PATH_PLUGINS . 'pentahoreports' . PATH_SEP . 'defaults' . PATH_SEP . 'processes_cases.analysisview.xaction';
      $template = new TemplatePower( $defaultTpl );
      $template->prepare();
      $template->assign( 'SOLUTION',      $workspace   );
      $template->assign( 'JNDI_SOLUTION', 'wf_' . $workspace   );
      $content = $template->getOutputContent();
      // sending file content
      $this->sendFileContent( 'processes_cases.analysisview.xaction', $workspace, $content );

      // preparing template file
      $defaultTpl = PATH_PLUGINS . 'pentahoreports' . PATH_SEP . 'defaults' . PATH_SEP . 'processes_duration.analysisview.xaction';
      $template = new TemplatePower( $defaultTpl );
      $template->prepare();
      $template->assign( 'SOLUTION',      $workspace   );
      $template->assign( 'JNDI_SOLUTION', 'wf_' . $workspace   );
      $content = $template->getOutputContent();
      // sending file content
      $this->sendFileContent( 'processes_duration.analysisview.xaction', $workspace, $content );

      // preparing template file
      $defaultTpl = PATH_PLUGINS . 'pentahoreports' . PATH_SEP . 'defaults' . PATH_SEP . 'processes_routings.analysisview.xaction';
      $template = new TemplatePower( $defaultTpl );
      $template->prepare();
      $template->assign( 'SOLUTION',      $workspace   );
      $template->assign( 'JNDI_SOLUTION', 'wf_' . $workspace   );
      $content = $template->getOutputContent();
      // sending file content
      $this->sendFileContent( 'processes_routings.analysisview.xaction', $workspace, $content );
    }

    /**
     * This method creates Default PMTables report files using the default data.
     * @todo solve some presentation and layout issues, for example that the long values of fields are cut in the report view.
     * @param String $workspace
     * @return void
     */
    function createDefaultPmTables ( $workspace ) {
      
      // assembling the connection string and creating the report folder
      $pathBase = PATH_PLUGINS . 'pentahoreports' . PATH_SEP . 'defaults' . PATH_SEP . 'pm_reports' . PATH_SEP ;
      $pathRepo = '/' . $workspace . '/pm_reports';
      $response = $this->createNewFolder( '/' . $workspace , 'pm_reports', 'Reports for PM Tables');

      // preparing template file
      $defaultTpl = $pathBase . 'index.xml';
      $content = file_get_contents ( $defaultTpl );
      // sending file content
      $this->sendFileContent( 'index.xml', $pathRepo, $content );
      
      // preparing template file
      $defaultTpl = $pathBase . 'index.properties';
      $content = file_get_contents ( $defaultTpl );
      // sending file content
      $this->sendFileContent( 'index.properties', $pathRepo, $content );
          
      // get the pmTables List
      $oCriteria = new Criteria('workflow');
      $oCriteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_UID);
      $oCriteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_NAME);
      $pmTables = AdditionalTablesPeer::doSelectRS($oCriteria);
      $pmTables->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      
      while ($pmTables->next()){
        // preparing template file for the current pm table report
        $addTabName = $pmTables->get('ADD_TAB_NAME');
        
        $defaultTpl = $pathBase . 'default_report.xml';
        $template = new TemplatePower( $defaultTpl );
        $template->prepare();
        $template->optionList = $test_fields;
        $sql = 'DESC '.$addTabName;
        $dbh =  Propel::getConnection(AdditionalTablesPeer::DATABASE_NAME);
        $sth = $dbh->createStatement();
        $res = $sth->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);
        $cellspace = 100.0;
        $cellposition = 0.0;
        $template->assign( 'PM_TABLE_NAME', $addTabName );
        
          while ($res->next()){
            // assigning the variables data into the template
            $fieldName = $res->get('Field');
            $template->newBlock("element_label");
            $template->assign("label", $fieldName);
            $template->assign("position", $cellposition);
            $template->newBlock("element_field");
            $template->assign("field", $fieldName);
            $template->assign("position", $cellposition);
            $cellposition = $cellspace + $cellposition;
          }
         // generating the template
         $content = $template->getOutputContent();
         $this->sendFileContent( $addTabName.'_report.xml', $pathRepo, $content );
         $defaultTpl = $pathBase . 'default_report.xaction';
         $template = new TemplatePower( $defaultTpl );
         $template->prepare();
         $template->assign( 'SOLUTION', $workspace );
         $template->assign( 'JNDI_SOLUTION', 'wf_' . $workspace );
         $template->assign( 'PM_TABLE', $addTabName );
         $content = $template->getOutputContent();
         // sending the file content
         $this->sendFileContent( $addTabName.'_report.xaction', $pathRepo, $content );

      }
    }

    /**
     * This method tests the pentaho user for the current workspace
     * @return Boolean true/false
     */
    function testPentahoUser () {
      $fields = $this->readLocalConfig ( SYS_SYS );
      $ProxyPentahoUser = new ProxyPentahoUser();
      if ( isset( $fields['PentahoUsername'] ) && $fields['PentahoUsername'] != '' ) {
        $userName  = $fields['PentahoUsername'];
        $workspace = substr($fields['PentahoUsername'],3); 
      }
      else {
        $userName  = "wf_" . SYS_SYS;
        $workspace = SYS_SYS; 

      }
      $description = "user $userName created by ProcessMaker Connector";
      // the proxy object creates the user in the pentaho bi server
      $res = $ProxyPentahoUser->createUser ( $userName, SYS_SYS, $description );
      // if not exists the status is ok
      if ( $res['status'] == 'OK' ) {
        print G::LoadTranslation("PENTAHO_LABEL_USER_CREATED")." ($userName)<br />";//"User $userName created successfully<br>";
      }
      else {
        // if not evaluates the server response
        $errorMsg = implode( '<br>', $res['tokens'] );
        if ( $res['status'] == 'EX' && strpos( $errorMsg, 'User already exist') === false ) {
          throw ( new Exception ( $errorMsg ) );
        }
        print G::LoadTranslation("PENTAHO_LABEL_USER_EXIST")." ($userName)<br />";//"User $userName already exist<br>";
      }
      // set the roles to the created user
      $res = $ProxyPentahoUser->setRoles ( $userName, $description ) ;
      if ( $res['status'] == 'EX' ) {
        $errorMsg = implode( '<br>', $res['tokens'] );
        throw ( new Exception ( $errorMsg ) );
      }

      //update the pentahoreports.conf file with info about the new user
      $fields = $this->readLocalConfig ( SYS_SYS );
      $fields['PentahoUsername'] = $userName;
      $fields['PentahoPassword'] = substr($userName,3);
      $this->updateLocalConfig ( SYS_SYS, $fields );
      
      
      //if the main folder doesn't exist, create it, else nothing, but if there was an error throw it.
      try {
        //$res = $this->createNewFolder( '/', SYS_SYS , "solution repository for workspace " . SYS_SYS );
        $res = $this->createMainFolder( '/', $workspace , "solution repository for workspace " . $workspace );
      }
      catch ( Exception $e ) {
        throw ( new Exception ( "error creating main folder. ($workspace) " . $e->getMessage() ) );
      }
      // the proxy object sets the solution info
      $res = $ProxyPentahoUser->setSolutionInfo ( $workspace, $userName ) ;
      if ( $res['status'] == 'EX' ) {
        $errorMsg = implode( '<br>', $res['tokens'] );
        throw ( new Exception ( $errorMsg ) );
      }
      
      return true;
    }

    /**
     * This method creates the mysql user and also the JNDI connection using the connection data.
     * @return Array $res the JNDI datasource created
     */
    function createJndi () {
      $jndiInfo = $this->getJndiInfo ();
      $fields = $this->readLocalConfig ( SYS_SYS );
      if ( isset( $fields['PentahoUsername'] ) && $fields['PentahoUsername'] != '' ) {
        $jndiInfo['jndiName']     = $fields['PentahoUsername'];
        $jndiInfo['jndiUserName'] = 'ph_' . substr ( $fields['PentahoUsername'],3 );
      }
      //create the user in the mysql database
      $dbOpt = @explode(SYSTEM_HASH,G::decrypt(HASH_INSTALLATION,SYSTEM_HASH));
      $connectionDatabase = mysql_connect($dbOpt[0],$dbOpt[1],$dbOpt[2] );
      // if the hash doesn't have the required credentials
      if( !$connectionDatabase) {
        throw ( new Exception ( 'HASH INSTALLATION has invalid credentials. Please check the paths_installed.php file' ) );
      }
      // setting the connection data
      $dbName     = $jndiInfo['jndiName'];
      $dbUrl      = $jndiInfo['jndiUrl'];
      $dbUserName = $jndiInfo['jndiUserName'];
      $dbPassword = $jndiInfo['jndiPassword'];
      // creating the mysql user
      $q = "GRANT ALL PRIVILEGES ON `$dbName`.* TO $dbUserName@'%' IDENTIFIED BY '$dbPassword' ";
      $ac = @mysql_query($q,$connectionDatabase);

      $ProxyPentahoUser = new ProxyPentahoUser();
      // creating the data source in the pentaho server
      $res = $ProxyPentahoUser->createDatasource ( $dbName, $dbUrl, $dbUserName, $dbPassword );
      if ( $res['status'] == 'OK' ) {
        print G::LoadTranslation("PENTAHO_LABEL_DB_SUCCESS")." ($dbName)<br />";//"Datasource $dbName created successfully<br>";
      }
      else {
        $errorMsg = implode( '<br>', $res['tokens'] );
        if ( $res['status'] == 'EX' && strpos( $errorMsg, 'Data Source already exists') === false ) {
          throw ( new Exception ( $errorMsg ) );
        }
        print G::LoadTranslation("PENTAHO_LABEL_DB_EXISTS")." ($dbName)<br />";//"Datasource $dbName already exist<br>";
      }
      return $res;
    }

    /**
     * This methods checks and executes the Setup the database tables, roles, permissions, creation of triggers.
     * @return true;
     */
    function setupDatabase () {
      //all this code is supposed to be executed in a Mysql Server 5.37 or greater
      $this->checkMysqlVersion();
      $this->checkApplicationIndexes();
      $this->checkAppDelegation();
      $this->checkAppCacheView();
      $this->checkRolesTables();
      $this->fillAppCacheView();
      $this->triggerAppDelegationInsert();
      $this->triggerAppDelegationUpdate();
      $this->triggerApplicationUpdate();
      $this->checkDimensionTimeTables();
      //if everything is ok, this funcion will return true, else it will throw an exception
      return true;
    }

 /**
  * Check the version of Mysql
  * @return void
  */
  function checkMysqlVersion() {
    $con = Propel::getConnection("workflow");
    $stmt = $con->createStatement();
    // executing the query that gets the mysql version.
    $sql="select version() ";
    $rs1 = $stmt->executeQuery($sql, ResultSet::FETCHMODE_NUM);
    $rs1->next();
    $fields = array();
    $row = $rs1->getRow();
    $aVersion = explode ( '-', $row[0] );
    $version = $aVersion[0];
    // if the version doesn't correspond throw an exception
    if ( $version < '5.1.37' ) {
      throw ( new Exception ( "current version of MySQL is $version, you need to upgrade mysql server to 5.1.37 or later" ));
    }
  }
  
   //ALTER TABLE `APPLICATION` ADD INDEX `APPNUMBER` ( `APP_NUMBER` )
  /**
   * This method searches for the table APP_DELEGATION indexes
   * @return void
   */
  function checkApplicationIndexes () {
    // setting up the connection
    $con = Propel::getConnection("workflow");
    $stmt = $con->createStatement();
    // executing the query that gets the indexes from the table APPLICATION
    $sql="SHOW INDEX from APPLICATION";
    $rs1 = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);
    $rs1->next();
    $fields = array();
    while ( is_array($row = $rs1->getRow() ) ) {
      $fields[] = $row;
      $rs1->next();
    }
    // Searching and adding an index to Column_name APP_NUMBER
    $found = false;
    foreach ( $fields as $key => $val ) if ( $val['Column_name'] == 'APP_NUMBER' ) $found = true;
    if ( !$found )
      $stmt->executeQuery("ALTER TABLE APPLICATION ADD INDEX APPNUMBER ( APP_NUMBER )" );
  }

  /**
   * This method checks and alter the fields of the table APP_DELEGATION
   * @return void
   */
  function checkAppDelegation () {
    // setting up the database connection
    $con = Propel::getConnection("workflow");
    $stmt = $con->createStatement();
    // getting the fields from the APP_DELEGATION table
    $sql="SHOW FIELDS FROM  APP_DELEGATION";
    $rs1 = $stmt->executeQuery($sql, ResultSet::FETCHMODE_NUM);
    $rs1->next();
    $fields = array();
    while ( is_array($row = $rs1->getRow() ) ) {
      $fields[] = $row[0];
      $rs1->next();
    }

    //adding the field DEL_QUEUE_DURATION
    $found = false;
    foreach ( $fields as $key => $val ) if (strtolower($val) == 'del_queue_duration' ) $found = true;
    if ( !$found )
      $stmt->executeQuery("ALTER TABLE APP_DELEGATION ADD COLUMN DEL_QUEUE_DURATION DOUBLE DEFAULT 0 " );

    //adding the field DEL_DELAY_DURATION
    $found = false;
    foreach ( $fields as $key => $val ) if (strtolower($val) == 'del_delay_duration' ) $found = true;
    if ( !$found )
      $stmt->executeQuery("ALTER TABLE APP_DELEGATION ADD COLUMN DEL_DELAY_DURATION DOUBLE DEFAULT 0 " );

    //adding the field DEL_STARTED
    $found = false;
    foreach ( $fields as $key => $val ) if (strtolower($val) == 'del_started' ) $found = true;
    if ( !$found )
      $stmt->executeQuery("ALTER TABLE APP_DELEGATION ADD COLUMN DEL_STARTED TINYINT(4) DEFAULT 0 " );

    //adding the field DEL_FINISHED
    $found = false;
    foreach ( $fields as $key => $val ) if (strtolower($val) == 'del_finished' ) $found = true;
    if ( !$found )
      $stmt->executeQuery("ALTER TABLE APP_DELEGATION ADD COLUMN DEL_FINISHED TINYINT(4) DEFAULT 0 " );

    //adding the field DEL_DELAYED
    $found = false;
    foreach ( $fields as $key => $val ) if (strtolower($val) == 'del_delayed' ) $found = true;
    if ( !$found )
      $stmt->executeQuery("ALTER TABLE APP_DELEGATION ADD COLUMN DEL_DELAYED TINYINT(4) DEFAULT 0 " );

    //adding the field DEL_DATA
    $found = false;
    foreach ( $fields as $key => $val ) if (strtolower($val) == 'del_data' ) $found = true;
    if ( !$found )
      $stmt->executeQuery("ALTER TABLE APP_DELEGATION ADD COLUMN DEL_DATA text NOT NULL " );

    //adding the field APP_OVERDUE_PERCENTAGE
    $found = false;
    foreach ( $fields as $key => $val ) if (strtolower($val) == 'app_overdue_percentage' ) $found = true;
    if ( !$found )
      $stmt->executeQuery("ALTER TABLE APP_DELEGATION ADD COLUMN APP_OVERDUE_PERCENTAGE double NOT NULL default 0" );
  }


  /**
   * This method searches for the fields in the table APP_CACHE_VIEW, and alter some of the files as required
   * @return void
   *
   */
  function checkAppCacheView () {
    // setting up the database connection
    $con = Propel::getConnection("workflow");
    $stmt = $con->createStatement();
    // getting the table list
    $sql="SHOW TABLES";
    $rs1 = $stmt->executeQuery($sql, ResultSet::FETCHMODE_NUM);
    $rs1->next();
    $found = false;
    while ( is_array($row = $rs1->getRow() ) && !$found ) {
      if ( strtolower($row[0]) == 'app_cache_view' ) {
        $found = true;
      }
      $rs1->next();
    }

    //if exists the APP_CACHE_VIEW Table, we need to check if it has the correct number of fields, if not recreate the table
    if ( $found ) {
      $sql="SHOW FIELDS FROM  APP_CACHE_VIEW";
      $rs1 = $stmt->executeQuery($sql, ResultSet::FETCHMODE_NUM);
      $rs1->next();
      $fields = array();
      while ( is_array($row = $rs1->getRow() ) ) {
        $fields[] = $row[0];
        $rs1->next();
      }
      if ( count($fields) != 30 )  $found = false;
      
    }
    // creating the APP_CACHE_VIEW table if not exists
    if ( !$found ) {
      $stmt->executeQuery( "DROP TABLE IF EXISTS `APP_CACHE_VIEW`; ");
      $filenameSql = PATH_PLUGINS . $this->sPluginFolder . '/app_cache_view.sql';
      if ( !file_exists ( $filenameSql ) )
        throw ( new Exception ( "file app_cache_view.sql doesn't exists ") );
      $sql = file_get_contents ( $filenameSql );
      $stmt->executeQuery($sql);
    }
  }

  /**
   * This method populates (fill) the table APP_CACHE_VIEW
   * @return void
   */
  function fillAppCacheView () {
    // setting up the database connection
    $con = Propel::getConnection("workflow");
    $stmt = $con->createStatement();
    // checking if the number of records in the table APP_CACHE_VIEW is 0
    $sql="select count(*) as CANT from APP_CACHE_VIEW ";
    $rs1 = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);
    $rs1->next();
    $row1 = $rs1->getRow();
    $cant = $row1['CANT'];
    // if there are no records execute the external query file app_cache_view_insert.sql
    if ( $cant == 0 ) {
      $filenameSql = PATH_PLUGINS . $this->sPluginFolder . '/app_cache_view_insert.sql';
      if ( !file_exists ( $filenameSql ) )
        throw ( new Exception ( "file app_cache_view_insert.sql doesn't exists ") );

      $sql = explode ( ';', file_get_contents ( $filenameSql ) );
      foreach ( $sql as $key => $val )
        $stmt->executeQuery($val);
    }
  }

  /**
   * This method creates the app delegation trigger
   * @return void
   */
  function triggerAppDelegationInsert() {
    // setting up the database connection
    $con = Propel::getConnection("workflow");
    $stmt = $con->createStatement();
    // getting the database triggers list
    $rs = $stmt->executeQuery('Show TRIGGERS', ResultSet::FETCHMODE_ASSOC);
    $rs->next();
    $row = $rs->getRow();
    $found = false;
    // if both triggers has been found the flag found is set to true
    while ( is_array ( $row ) ) {
      if ( strtolower($row['Trigger'] == 'APP_DELEGATION_INSERT') && strtoupper($row['Table']) == 'APP_DELEGATION' ) {
        $found = true;
      }
      $rs->next();
      $row = $rs->getRow();
    }
    // if the triggers doesn't exists execute the external script triggerAppDelegationInsert.sql
    if ( ! $found ) {
      $filenameSql = PATH_PLUGINS . $this->sPluginFolder . '/triggerAppDelegationInsert.sql';
      if ( !file_exists ( $filenameSql ) )
        throw ( new Exception ( "file triggerAppDelegationInsert.sql doesn't exists ") );
      $sql = file_get_contents ( $filenameSql );
      $stmt->executeQuery($sql);
    }
  }

  /**
   * This method updates the App Delegation triggers
   * @return void
   */
  function triggerAppDelegationUpdate() {
    // setting up the database connection
    $con = Propel::getConnection("workflow");
    $stmt = $con->createStatement();
    // getting the database triggers list
    $rs = $stmt->executeQuery("Show TRIGGERS", ResultSet::FETCHMODE_ASSOC);
    $rs->next();
    $row = $rs->getRow();
    $found = false;
    // iterating over the triggers list
    while ( is_array ( $row ) ) {
      // if both triggers has been found the flag found is set to true
      if ( strtolower($row['Trigger'] == 'APP_DELEGATION_UPDATE') && strtoupper($row['Table']) == 'APP_DELEGATION' ) {
        $found = true;
      }
      $rs->next();
      $row = $rs->getRow();
    }
    // if the triggers doesn't exists execute the external script triggerAppDelegationUpdate.sql
    if ( ! $found ) {
      $filenameSql = PATH_PLUGINS . $this->sPluginFolder . '/triggerAppDelegationUpdate.sql';
      if ( !file_exists ( $filenameSql ) )
        throw ( new Exception ( "file triggerAppDelegationUpdate.sql doesn't exists ") );
      $sql = file_get_contents ( $filenameSql );
      $stmt->executeQuery($sql);
    }
  }

  /**
   * This method updates the Application triggers
   * @return void
   */
  function triggerApplicationUpdate() {
    // setting up the database connection
    $con = Propel::getConnection("workflow");
    $stmt = $con->createStatement();
    // getting the database triggers list
    $rs = $stmt->executeQuery("Show TRIGGERS", ResultSet::FETCHMODE_ASSOC);
    $rs->next();
    $row = $rs->getRow();
    $found = false;
    // iterating over the triggers list
    while ( is_array ( $row ) ) {
      // if both triggers has been found the flag found is set to true
      if ( strtolower($row['Trigger'] == 'APPLICATION_UPDATE') && strtoupper($row['Table']) == 'APPLICATION' ) {
        $found = true;
      }
      $rs->next();
      $row = $rs->getRow();
    }
    // if the triggers doesn't exists execute the external script triggerApplicationUpdate.sql
    if ( ! $found ) {
      $filenameSql = PATH_PLUGINS . $this->sPluginFolder . '/triggerApplicationUpdate.sql';
      if ( !file_exists ( $filenameSql ) )
        throw ( new Exception ( "file triggerAppDelegationUpdate.sql doesn't exists ") );
      $sql = file_get_contents ( $filenameSql );
      $stmt->executeQuery($sql);
    }
  }

  /**
   * This method searches for the time dimension tables and creates those if not exists
   * @return void
   */
  function checkDimensionTimeTables() {
    try {
      
    
    // setting up the database connection
    $con = Propel::getConnection("workflow");
    $stmt = $con->createStatement();
    // getting the tables list
    $sql="SHOW TABLES";
    $rs1 = $stmt->executeQuery($sql, ResultSet::FETCHMODE_NUM);
    $rs1->next();
    $found = false;
    // iterating over the tables list
    while ( is_array( $row = $rs1->getRow() ) && !$found ) {
      if ( strtolower( $row[0]) == 'dim_time_delegate' ) {
        $found = true;
      }
      $rs1->next();
    }

    // if the table dim_time_delegate doesn't exists create it.
    if ( !$found ) {
      $filenameSql = PATH_PLUGINS . $this->sPluginFolder . '/dim_time_delegate.sql';
      if ( !file_exists ( $filenameSql ) )
        throw ( new Exception ( "file dim_time_delegate.sql doesn't exists ") );
      $sql = explode ( ';', file_get_contents ( $filenameSql ) );
      foreach ( $sql as $key => $val )
        if ( trim($val) != '' )
          $stmt->executeQuery($val);
    }
    
    // searching the dim_time_complete table
    $found = false;
    while ( is_array($row = $rs1->getRow() ) && !$found ) {
      if ( strtolower($row[0]) == 'dim_time_complete' ) {
        $found = true;
      }
      $rs1->next();
    }

    // if the table dim_time_complete doesn't exists create it.
    if ( !$found ) {
      $filenameSql = PATH_PLUGINS . $this->sPluginFolder . '/dim_time_complete.sql';
      if ( !file_exists ( $filenameSql ) )
        throw ( new Exception ( "file dim_time_complete.sql doesn't exists ") );
      $sql = explode ( ';', file_get_contents ( $filenameSql ) );
      foreach ( $sql as $key => $val )
        if ( trim($val) != '' )
          $stmt->executeQuery($val);
    }
    
    } catch (Exception $e) {
      //$e->
    }

  }
  /**
   * This method gets the user dashboard url
   * @param String $solution
   * @param String $path
   * @param String $file
   * @param String $user_uid
   * @return String $url
   */
    function getUserDashboardUrl($solution, $path, $file, $user_uid) {
      // Assembling the Dashboard Url
      $url = $this->sPentahoServer ;
      $url .= '/content/pentaho-cdf/RenderXCDF?';
      $url .= 'solution=' . $solution ;
      $url .= '&path=' . $path;
      $url .= '&action=' . $file;
      $url .= '&user_uid=' . $user_uid;
      $url .= '&template=processmaker';
      $url .= '&userid=' . $this->sPentahoUsername . '&password=' . $this->sPentahoPassword;
          
      return $url;
    }
   
    /**
     * This method sends a Pentaho Administration Console  Packet
     * @return Array $node
     */
    function getPac( $payload ) {
      try {
        $ch = curl_init();
        $params = array();
        // setting headers
        $headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
        $headers[] = 'Content-Type: text/x-gwt-rpc; charset=utf-8';
        $headers[] = 'Pragma: no-cache';
        $headers[] = 'Cache-Control: no-cache';
        // initializing curl request
        curl_setopt($ch, CURLOPT_URL, $this->sPentahoAdmServer . "/pacsvc" );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch, CURLOPT_HEADER, true );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false );
        curl_setopt($ch, CURLOPT_AUTOREFERER, true );
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $this->sPentahoAdmUsername .':' . $this->sPentahoAdmPassword );
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload );

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        // executing curl request
        $response = curl_exec($ch);
        $headers  = curl_getinfo($ch);
        $header  = substr ( $response, 0, $headers['header_size'] );
        $content = substr ( $response, $headers['header_size'] );
        // closing connection
        curl_close($ch);
        
        // checking the server response
        if ( trim ( $content) == '' )
          throw ( new Exception ( $this->sPentahoAdmServer . " is not a valid Pentaho Server URL, please check the Pentaho Setup for this workspace." ) );
 
        if ( $headers['http_code'] == 500 ) {
          throw ( new Exception ( "HTTP Status 500 : The server encountered an internal error () that prevented it from fulfilling this request." ) );
        }
        if ( $headers['http_code'] != 200 ) {
          throw ( new Exception ( $content ) );
        }
        
        if ( substr($content, 0,2)  != '//' || (substr($content, 2,2)!='OK' && substr($content, 2,2) != 'EX' ) ) {
          throw ( new Exception ( $content ) );
        }
        // assemblign the node response array
        $status = substr($content, 2,2);
        $pos1 = strpos( $content, '[', 5 );
        $pos2 = strpos( $content, ']', 0 );
        $text = substr( $content, $pos1 + 1, $pos2 - $pos1 - 1 );
        $tokens = explode (',', $text );

        $node = array ();
        $node['status'] = $status;
        $node['tokens'] = $tokens;
        return $node;
      }
      catch ( Exception $e ) {
        throw ( $e );
      }
    }

    /**
     * This method sends a Pentaho mantle Packet
     * @return Array $node
     */
    function getMantle( $payload ) {
      try {
        $ch = curl_init();
        $params = array();
        // setting headers
        $headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
        $headers[] = 'Content-Type: text/x-gwt-rpc; charset=utf-8';
        $headers[] = 'Pragma: no-cache';
        $headers[] = 'Cache-Control: no-cache';
        // initializing curl request parameters
        curl_setopt($ch, CURLOPT_URL, $this->sPentahoServer . "/mantle/MantleService" );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch, CURLOPT_HEADER, true );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false );
        curl_setopt($ch, CURLOPT_AUTOREFERER, true );
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $this->sPentahoSuperUsername .':' . $this->sPentahoSuperPassword );
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload );

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        // executing curl request
        $response = curl_exec($ch);
        $headers  = curl_getinfo($ch);
        $header   = substr ( $response, 0, $headers['header_size'] );
        $content  = substr ( $response, $headers['header_size'] );
        // closing connection
        curl_close($ch);
        // checking server response
        if ( trim ( $content) == '' )
          throw ( new Exception ( $this->sPentahoServer . " is not a valid Pentaho Server URL, please check the Pentaho Setup for this workspace." ) );

        if ( $headers['http_code'] == 500 ) {
          throw ( new Exception ( "HTTP Status 500 : The server encountered an internal error () that prevented it from fulfilling this request." ) );
        }
        if ( $headers['http_code'] != 200 ) {
          throw ( new Exception ( $content ) );
        }

        if ( substr($content, 0,2)  != '//' || (substr($content, 2,2)!='OK' && substr($content, 2,2) != 'EX' ) ) {
          throw ( new Exception ( $content ) );
        }
        // assembling the node array response
        $status = substr($content, 2,2);
        $pos1 = strpos( $content, '[', 5 );
        $pos2 = strpos( $content, ']', 0 );
        $text = substr( $content, $pos1 + 1, $pos2 - $pos1 - 1 );
        $tokens = explode (',', $text );

        $node = array ();
        $node['status'] = $status;
        $node['tokens'] = $tokens;
        return $node;
      }
      catch ( Exception $e ) {
        throw ( $e );
      }
    }
  
  /**
   * This method creates all the required tables for roles access in Pentaho plugin.
   * @return void
   */
  function createRoleTables () {
    $con = Propel::getConnection("workflow");
    $stmt = $con->createStatement();
    // setting the path of the sql schema files
    $filenameSql = PATH_PLUGINS . $this->sPluginFolder . '/data/mysql/schema.sql';
    // checking the existence of the schema file
    if ( !file_exists ( $filenameSql ) )
      throw ( new Exception ( "file data/schema.sql doesn't exists ") );
    // exploding the sql query in an array
    $sql = explode ( ';', file_get_contents ( $filenameSql ) );

    $stmt->executeQuery( "SET FOREIGN_KEY_CHECKS = 0;" );
    // executing each query stored in the array
    foreach ( $sql as $key => $val )
      if ( trim($val) != '' )
        $stmt->executeQuery($val);
  }

  /**
   * This method checks if a report is already in the Database
   * @param String $reportName
   * @param String $reportPath
   * @return Boolean true/false
   */
  function checkReportIsRegistered($reportName,$reportPath){
    $report = new PhReport();
    $oReports = $report->getAllReports();
    $found = false;
    // check if the report is registered in thir respective path
    foreach ($oReports as $aReport){
      if ($aReport['REP_TITLE']==$reportName&&$aReport['REP_PATH']==$reportPath){
        $found = true;
        return $found;
      }
    }
    return $found;
  }

  /**
   * This method inserts a report as a record assigned to the role PH_ADMIN
   * @param Array $reportData
   * @return void
   */
  function registerReportIntoDB($reportData){
    $report = new PhReport();
    // setting the report data array
    $aData['REP_UID'] = G::generateUniqueID();
    $aData['REP_PATH']  = $reportData['path'];
    $aData['REP_TITLE'] = $reportData['name'];
    $aData['REP_NAME']  = $reportData['label'];
    $report->create($aData);
    $rData['REP_UID'] = $aData['REP_UID'];
    $rData['ROL_UID'] = '00000000000000000000000000000001';
    $roleReport = new PhRoleReport();
    // creating the assignment entry
    $roleReport->create($rData);
  }

  /**
   * This method checks the existence of the roles tables
   * @return void;
   */
  function checkRolesTables() {

    $con = Propel::getConnection("workflow");
    $stmt = $con->createStatement();

    $sql="SHOW TABLES";
    $rs1 = $stmt->executeQuery($sql, ResultSet::FETCHMODE_NUM);
    $rs1->next();
    $foundTables = false;
    // check if roles tables exists
    while ( is_array($row = $rs1->getRow() ) && !$foundTables ) {
      switch(strtolower($row[0])){
        case 'ph_user_role':
          $foundTables = true;
        break;
        case 'ph_role':
          $foundTables = true;
        break;
        case 'ph_report':
          $foundTables = true;
        break;
        case 'ph_role_report':
          $foundTables = true;
        break;
      }
      if ($foundTables){
        break;
      }
      $rs1->next();
    }

    //if exists the APP_CACHE_VIEW Table, we need to check if it has the correct number of fields, if not recreate the table

    if ( !$foundTables ) {
      $this->createRoleTables();
    }
  }

  /**
   * This method synchronizes the table of reports in order to sinchronize the newest
   * files in the repository or erase the inexistents in the same way.
   * @param String $sWorkspace  workspace
   * @return void
   */
  function synchronizeReportTables ( ) {
    $report = new PhReport();
    $aReports = $report->getAllReports();
    $this->getSolutionRepository();

    foreach ($aReports as $aCurrentReport){
      $reportFound = false;
      // search the report in the repository
      $reportFound = $this->searchReport($this->aSolutionRepository,$aCurrentReport['REP_TITLE']);
      // if the report no longer exists delete the database record
      if (!$reportFound){
        $repUid = $report->getReportByName($aCurrentReport['REP_TITLE']);
        $report->load($repUid);
        $report->delete();
        $report->setDeleted(false);
      }
    }
  }

  /**
   * This method checks the permissions for a repository or folder inside pentaho recursively
   * @param  Array $aFiles array of report data
   * @return Array $aReports array of reports that pass the verification.
   */
   function checkPermissions ( $aFiles ) {
     $aReports = array();
     $roleReport = new PhRoleReport();
    // recursively check permissions for a report list and if the user has that report assigned
     foreach($aFiles as $aCurrentFile){
       if (count($aCurrentFile['files'])>0) {
         $aCurrentFile['files'] = $this->checkPermissions($aCurrentFile['files']);
       }
       if ($roleReport->userIsAssignedReport($_SESSION['USER_LOGGED'],$aCurrentFile['name'])){
         $aReports[] = $aCurrentFile;
       }

     }
     return $aReports;
   }

  /**
   * This method searches for a report inside a folder or repository of pentaho recursively
   * @param  Array $aRepository array of files
   * @param  String $sReportName  the report name
   * @return Boolean true/false if found or not found.
   */
   function searchReport($aRepository, $sReportName){
     $found = false;
     // recursively searchs for a report with the same name as the parameter report name
     foreach ($aRepository as $aReport){
       if ($aReport['name']==$sReportName){
         $found = true;
         return $found;
       } else {
         if (count($aReport['files'])>0){
           $found = $this->searchReport($aReport['files'],$sReportName);
           if($found==true){
             return $found;
           }
         }
       }
     }
     return $found;
   }

}
