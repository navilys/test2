<?php
require_once(PATH_PLUGINS.SYS_COLLECTION.PATH_SEP.'classes'.PATH_SEP.'class.connectionHandler.php');
/**
 * Manage connections to a workspace databases
 * @author Edwin Paredes
 *
 */
class workspaceConnectionManager{
	private $sWorkspace;//workspace name
	private $sDBFileContent;//content of the db.php of a workspace
	public $oWorkflowConnection;//workflow database connection
	public $oRbacConnection;//rbac database connection
	public $oReportConnection;//report database connection
	public $aDataSources;//array of connection parameters 
	private $sType;//connection type can be propel or pdo
	
	function __construct($sType = "PROPEL"){
		$this->sType = $sType;
	}
	/**
	 * Creates the connection objects but doesn't create the connection
	 * @param string $sWorkspace
	 */
	function createConnectionObjects($sWorkspace){
		$this->sWorkspace = $sWorkspace;
		$this->loadParameters();
		switch($this->sType){
			case "PDO":
				//this objects use pdo to connect to the workspace databases
				$this->oWorkflowConnection = new connectionHandler($this->aDataSources['workflow']);
				$this->oRbacConnection = new connectionHandler($this->aDataSources['rbac']);
				$this->oReportConnection = new connectionHandler($this->aDataSources['report']);
			break;
			case "PROPEL":
				//initialize propel configuration to support the new connections
				PROPEL::initConfiguration($this->propelArray());
			break;
		}
	}
	/**
	 * Loads the parameters required to connect to each workspace database
	 */
	function loadParameters(){
		$this->readDBFile();
		$sContent = $this->sDBFileContent;
		$sContent = str_replace( '<?php', '', $sContent );
		$sContent = str_replace( '<?', '', $sContent );
		$sContent = str_replace( '?>', '', $sContent );
		$sContent = str_replace( 'define', '', $sContent );
		$sContent = str_replace( "('", "\$", $sContent );
		$sContent = str_replace( "',", "=", $sContent );
		$sContent = str_replace( ");", ';', $sContent );
		@eval ( $sContent );
		$this->aDataSources['workflow'] = array (
			'sAdapter' => $DB_ADAPTER,
			'sHost' => $DB_HOST,
			'sName' => $DB_NAME,
			'sUser' => $DB_USER,
			'sPass' => $DB_PASS
		);
		$this->aDataSources['rbac'] = array (
			'sAdapter' => $DB_ADAPTER,
			'sHost' => $DB_RBAC_HOST,
			'sName' => $DB_RBAC_NAME,
			'sUser' => $DB_RBAC_USER,
			'sPass' => $DB_RBAC_PASS
		);
		$this->aDataSources['report'] = array (
			'sAdapter' => $DB_ADAPTER,
			'sHost' => $DB_REPORT_HOST,
			'sName' => $DB_REPORT_NAME,
			'sUser' => $DB_REPORT_USER,
			'sPass' => $DB_REPORT_PASS
		);
	}
	/**
	 * Reads the workspace db.php file
	 */
	function readDBFile(){
		if (file_exists ( PATH_DB . $this->sWorkspace . PATH_SEP . 'db.php' )){
			$this->sDBFileContent = file_get_contents ( PATH_DB . $this->sWorkspace . PATH_SEP . 'db.php' );
		}else{
			$this->sDBFileContent = "";
		}
	}
	function propelArray(){
		$datasources['datasources'] = array(
			'workflow' => array(
				'connection' => $this->buildPropelDsnString($this->aDataSources['workflow']),
				'adapter' => "mysql"
			),
			'rbac' => array(
				'connection' => $this->buildPropelDsnString($this->aDataSources['rbac']),
				'adapter' => "mysql"
			),
			'report' => array(
				'connection' => $this->buildPropelDsnString($this->aDataSources['report']),
				'adapter' => "mysql"
			)
		);
		return $datasources;
	}
	/**
	 * Builds the DSN string to be used by PROPEL
	 */
	function buildPropelDsnString($aParameters){
			$sDsn = $aParameters['sAdapter']."://".$aParameters['sUser'].":".$aParameters['sPass']."@".$aParameters['sHost']."/".$aParameters['sName'];
			switch ($aParameters['sAdapter']) {
      	case 'mysql':
      		$sDsn .= '?encoding=utf8';
      	break;
      }
      return $sDsn;
	}
}
