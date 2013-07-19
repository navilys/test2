<?php
/**
 * Manage pdo connections to databases
 * @author Edwin Paredes
 *
 */
class connectionHandler{
	private $sAdapter;
	private $sHost;
	private $sPort;
	private $sName;
	private $sUser;
	private $sPass;
	private $sDsn;
	private $oConnection;
	
	function __construct($aParameters){
		$this->sAdapter = $aParameters['sAdapter'];
		$this->sPort = isset($aParameters['sPort'])? $aParameters['sPort']:'';
		$this->sHost = $this->setHost($aParameters['sHost']);
		$this->sName = $aParameters['sName'];
		$this->sUser = $aParameters['sUser'];
		$this->sPass = $aParameters['sPass'];
	}
	/**
	 * Set the host name, if the host name has the port as part of the string, the method will split the two elements and assign the port to another attribute
	 * @param string $sHost
	 */
	function setHost($sHost){
		$aHost = explode(":", $sHost);
		if(count($aHost) == 2){
			$this->sHost = $aHost[0];
			$this->sPort = $aHost[1];
		}else{
			$this->sHost = $sHost;
		}
	}
	/**
	 * Builds the DSN string to be used by PDO
	 */
	function buildPdoDsnString(){
		$this->sDsn = $this->sAdapter.':dbname='.$this->sName.';host='.$this->sHost;
		if($this->sPort != '')
			$this->sDsn .= ';port='.$this->sPort;
	}
	/**
	 * Create the connection to a database
	 */
	function connect(){
			$this->buildPdoDsnString();
			$this->oConnection = new PDO($this->sDsn, $this->sUser, $this->sPass);
	}
	/**
	 * Runs a query in a database
	 * @param string $sQueryString: the query to be executed
	 * @return array: array of results or an empty array with a false element
	 */
	function query($sQueryString){
		$result = $this->oConnection->query($sQueryString);
		if($result){
			return $result->fetchAll();
		}
		else{
			G::pr($this->oConnection->errorInfo());
		}
		return array(false);
	}
	/**
	 * @return string: adapter name
	 */
	function getAdapter(){
		return $this->sAdapter;
	}
	/**
	 * @return string: database name
	 */
	function getDatabaseName(){
		return $this->sName;
	}
}