<?php
/**
 * This class has the code to copy a form from an origin workspace to another
 * @author Edwin Paredes
 *
 */
class tclFormReplicator{
	private $oConnection;
	public $sDestinationWorkspace;
	public $sOriginWorkspace;
	public $sDynUid;
	public $sDynUidDestination;
	public $sOriginProUid;
	public $sDestinationProUid;
	public $aErrors;
	public $aWarnings = array();
	private $bChangeDynUid = false;
	/**
	 * Creates connection as mysql administrator
	 */
	function __construct($sOriginWorkspace, $sDestinationWorkspace, $sDynUid, $sDestinationProUid){
		$oUtility = new tclAuxiliary();
		$cResult = $oUtility->GetHashAdminConfiguration();
		$this->oConnection = new tclConnectionManagement();
		$this->oConnection->stablishNewConnection($cResult[0],$cResult[1],$cResult[2]);
		$this->sOriginWorkspace = $sOriginWorkspace;
		$this->sDestinationWorkspace = $sDestinationWorkspace;
		$this->sDynUid = $sDynUid;
		$this->sOriginProUid = $this->getOriginProUid();
		$this->sDestinationProUid = $sDestinationProUid;
	}
	/**
	 * Validates if forms replication is possible
	 * @return boolean, true if valid
	 */
	function validateReplication(){
		$bValidateWorkspaces = $this->validateWorkspaces();
		$bDestinationProcess = $this->validateProcesses();
		$this->verifyDestination();
		return $bDestinationProcess&&$bValidateWorkspaces;
	}
	/**
	 * Verifies the conditions to replicate the dynaform, considering if the UID exists in the target workspace and if it does the method will verify it it should be updated or created with a new UID
	 */
	function verifyDestination(){
		$sOriginProUid = $this->getOriginProUid();
		$sQueryDestinationProcess = "
			SELECT
				PRO_UID
			FROM
				wf_".$this->sDestinationWorkspace.".DYNAFORM
			WHERE
				DYN_UID = '".$this->sDynUid."'
		";
		$aResult = $this->oConnection->executeQuery($sQueryDestinationProcess);
		if(count($aResult)){
			if($aResult[1]['PRO_UID'] == $this->sDestinationProUid){
				$this->aWarnings[] = "Dynaform exists in the same process, Update dynaform";
			}else{
				//this flag will be read to change the UID when replicating the form
				$this->bChangeDynUid = true;
				$this->aWarnings[] = "Dynaform exists in other process, Change dynaform UID required";
			}
		}
	}
	/**
	 * Validates if workspaces involved in the replication operation are valid
	 * @return boolean, true if valid
	 */
	private function validateWorkspaces(){
		//do not copy the dynaform if the origin and destination workspaces are the same
		if($this->sOriginWorkspace == $this->sDestinationWorkspace && $this->sOriginProUid == $this->sDestinationProUid){
			$this->aErrors[] = "Origin and Destination workspaces are the same";
			return false;
		}
		return true;
	}
	/**
	 * Check if the destination and source processes exist in the destination workspace
	 * @param array $aIncludeProcesses, list of destination processes exceptions that will pass the validation without checking the database
	 * @return boolean, true if valid
	 */
	private function validateProcesses($aIncludeProcesses = array()){
		//validate source process
		if($this->getOriginProUid() == ""){
			$this->aErrors[] = "Source process is invalid";
			return false;
		}
		//validate destination process
		if(array_search($this->sDestinationProUid,$aIncludeProcesses)){
			return true;
		}
		$sQueryDestinationProcess = "
			SELECT
				PRO_UID
			FROM
				wf_".$this->sDestinationWorkspace.".PROCESS
			WHERE
				PRO_UID = '".$this->sDestinationProUid."'
		";
		$aResult = $this->oConnection->executeQuery($sQueryDestinationProcess);
		if(count($aResult)){
			return true;
		}
		$this->aErrors[] = "Destination process is invalid";
		return false;
	}
	/**
	 * Copy the dynaform data along with the xml and html files
	 * @param string $sOriginWorkspace
	 * @param string $sDestinationWorkspace
	 * @param string $sDynUid
	 * @param string $sDestinationProUid
	 * @return boolean, true on success
	 */
	function copyDynaform(){//($sOriginWorkspace, $sDestinationWorkspace, $sDynUid, $sDestinationProUid){
		//$this->sOriginWorkspace = $sOriginWorkspace;
		//$this->sDestinationWorkspace = $sDestinationWorkspace;
		//$this->sDynUid = $sDynUid;
		//$this->sOriginProUid = $this->getOriginProUid();
		//$this->sDestinationProUid = $sDestinationProUid;
		//runs a validation before doing any transference 
		if(!$this->validateReplication())
			return false;
		//if change dynaform uid flag is true then create a new UID to be assigned to the dynaform
		if($this->bChangeDynUid){
			$this->sDynUidDestination = G::generateUniqueID();
		}else{
			$this->sDynUidDestination = $this->sDynUid;
		}
		//copy the files to the destination workspace
		if(!$this->copyDynaformFiles())
			return false;
		//copy the database rows for a dynaform to the destination workspace
		try{
			$this->copyDynaformData();
			return true;
		}catch (Exception $e){
			$this->aErrors[] = $e->getMessage();
			return false;
		}
	}
	/**
	 * Gets the Pro Uid for the source process where the dynaform is originally in
	 * @return string
	 */
	function getOriginProUid(){
		$sQueryProUid = "
			SELECT
				PRO_UID
			FROM
				wf_".$this->sOriginWorkspace.".DYNAFORM
			WHERE
				DYN_UID = '".$this->sDynUid."'
		";
		$aResult = $this->oConnection->executeQuery($sQueryProUid);
		if(count($aResult)){
			return $aResult[1]['PRO_UID'];
		}else{
			return "";
		}
	}
	/**
	 * Copy a dynaform files from one source workspace to a destination workspace
	 * @return boolean, true on success
	 */
	private function copyDynaformFiles(){
		//buidl paths for origin and destination
		$sOriginPath = PATH_DB.$this->sOriginWorkspace.PATH_SEP."xmlForms".PATH_SEP.$this->sOriginProUid.PATH_SEP;
		$sDestinationPath = PATH_DB.$this->sDestinationWorkspace.PATH_SEP."xmlForms".PATH_SEP.$this->sDestinationProUid.PATH_SEP;
		if(!is_dir($sDestinationPath)){
			G::mk_dir($sDestinationPath);
		}
		//copy xml file
		if(!copy($sOriginPath.$this->sDynUid.".xml", $sDestinationPath.$this->sDynUidDestination.".xml")){
			$this->aErrors[] = "Couldn't copy xml file";
			return false;
		}
		//if exists, copy the html file
		if(is_file($sOriginPath.$this->sDynUid.".html")){
			if(!copy($sOriginPath.$this->sDynUid.".html", $sDestinationPath.$this->sDynUidDestination.".html")){
				$this->aErrors[] = "Couldn't copy html file";
				return false;
			}
		}
		//fix xml references in dynaform name and grids
		if(!$this->changeDynaformNameAttribute($sOriginPath.$this->sDynUid.".xml")){
			//this suboperation result is not critical 
			$this->aErrors[] = "Warning, couldn't change XML dynaform name attribute change";
		}
		return true;
	}
	/**
	 * Changes the dynaform name attribute
	 * @param string $sXmlFile
	 * @return boolean, true on success
	 */
	private function changeDynaformNameAttribute($sXmlFile){
		//if destination and source uids are the same return true
		if($this->sDestinationProUid == $this->sOriginProUid)
			return true;
		//creates a new DOM object and opens the xml file
		$oXmlObject=new DOMDocument();
		if(!$oXmlObject->load($sXmlFile)){
			return false;
		}
		//select the root node, in a dynaform this node will be the element with the "dynaform" tag
		$oXmlDomElement = $oXmlObject->documentElement;
		$bChangeFile = false;
		//if the dynaform name attribute is a reference to the origin process/dynaform uids, then change it to destination process/dyanform uids
		if($oXmlDomElement->getAttribute('name') != $this->sOriginProUid."/".$this->sDynUid){
			$oXmlDomElement->setAttribute('name',$this->sDestinationProUid."/".$this->sDynUidDestination);
			$bChangeFile = true;
		}
		//change grids process referece
		$elements = $oXmlDomElement->getElementsByTagName("*");
		foreach($elements as $element){
			$sProUidPattern = '/.'.$this->sOriginProUid.'./';
			if($element->getAttribute("type") == "grid" && preg_match($sProUidPattern,$element->getAttribute("xmlgrid"))){
				$sNewValue = str_replace($this->sOriginProUid, $this->sDestinationProUid, $element->getAttribute("xmlgrid"));
				$element->setAttribute("xmlgrid",$sNewValue);
				$bChangeFile = true;
			}
		}
		if($bChangeFile){
			return $oXmlObject->save($sXmlFile);
		}
		return true;
	}
	/**
	 * Copy the dynaform database information
	 */
	private function copyDynaformData(){
		$sQueryDynaform = "
			REPLACE INTO
				wf_".$this->sDestinationWorkspace.".DYNAFORM
			 (
				SELECT
					'".$this->sDynUidDestination."' AS DYN_UID,
					'".$this->sDestinationProUid."' AS PRO_UID,
					DYN_TYPE,
					'".$this->sDestinationProUid."/".$this->sDynUid."' AS DYN_FILENAME
				FROM
					wf_".$this->sOriginWorkspace.".DYNAFORM
				WHERE
					DYN_UID = '".$this->sDynUid."'
			)
		";
		$aResult = $this->oConnection->executeQuery($sQueryDynaform);
		$sQueryContent = "
			REPLACE INTO
				wf_".$this->sDestinationWorkspace.".CONTENT
			 (
				SELECT
					CON_CATEGORY,
					CON_PARENT,
					'".$this->sDynUidDestination."' AS CON_ID,
					CON_LANG,
					CON_VALUE
				FROM
					wf_".$this->sOriginWorkspace.".CONTENT
				WHERE
					CON_ID = '".$this->sDynUid."'
			)
		";
		$aResult = $this->oConnection->executeQuery($sQueryContent);
	}
	public function getDynaformName($sDynUid, $sWorkspace){
		$sQuery = "
			SELECT
				CON_VALUE
			FROM
				wf_$sWorkspace.CONTENT
			WHERE
				CON_ID = '$sDynUid' AND
				CON_CATEGORY = 'DYN_TITLE'
		";
		$aResult = $this->oConnection->executeQuery($sQuery);
		if(count($aResult)){
			return $aResult[1]['CON_VALUE'];
		}
	}
	public function getProcessName($sProUid, $sWorkspace){
		$sQuery = "
			SELECT
				CON_VALUE
			FROM
				wf_$sWorkspace.CONTENT
			WHERE
				CON_ID = '$sProUid' AND
				CON_CATEGORY = 'PRO_TITLE'
		";
		$aResult = $this->oConnection->executeQuery($sQuery);
		if(count($aResult)){
			return $aResult[1]['CON_VALUE'];
		}
	}
}