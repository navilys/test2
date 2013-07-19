<?php
/**
* Class that contains common utilities that will 
* perform simple tasks
*
*/
class tclAuxiliary {
	public $con;
	public $customConnect;
	public $dbCon;
	private $sConnectionString;
	/**
	* this construct will initiate a db connection that
	* will allow extend query execution.
	*
	*	@param $bSpeedConnection: a boolean that will tell
	*							  if the connection will be initiated
	* @return void
	*/
	public function __construct($bSpeedConnection=true,$sConnectionString="workflow"){
		if($bSpeedConnection){
			$this->sConnectionString=$sConnectionString;
			$this->con= Propel::getConnection($this->sConnectionString);
			$this->customConnect=mysql_connect(DB_HOST,DB_NAME,DB_PASS);
			if (!$this->con){
				print_r("connection error ".mysql_error());
			}else{
				$this->dbCon=mysql_select_db(DB_NAME,$this->customConnect);
			}
		}
	}
	/**
	* This function will insert the " character at the begining and the end
	* of every element in a given array
	*
	* @param &$aToChange: the array that needs to be changed
	*
	* @return the $aToChange is updated with the new data
	*/
	public function insertDobleCharOnArray(&$aToChange){
			foreach ($aToChange as $key=>$value){
				$aToChange[$key]='"'.$value.'"';	
			}
	}
	/**
	 * function that will execute a php archive in diferent
	 * workspace or a given workspace
	 * @param $sArchiveToExecute : An string with the name of the archive to be executed
	 * @param $aWorkSpace: an array with a selected workspace
	 */
	public function executeArchiveAnotherWorkflow($sArchiveToExecute,$aWorkSpace=NULL){
		if (is_null($aWorkSpace)){
			$oAprObject=new tclPropelSelection("AprCustomer");
			$aHeader=array ("CusWorkspace","CusName");
			$aSearchCriteria = array();
			$aResult=$oAprObject->setSearch($aHeader,$aSearchCriteria);
		}else{
			$aResult=$aWorkSpace;
		}
		foreach($aResult as $aValue){
			//valid workspace name
			exec($this->setExecRute($sArchiveToExecute,$aValue["CusWorkspace"]));
			//exec($this->setExecRute($sArchiveToExecute,$aValue["CusName"]),$res,$err);
		}
	}
	/**
	 * this function will create a lambda sentece with the file to be executed
	 * 
	 * @param $sArchiveToExecute : An string with the name of the archive to be executed
	 * @param $sWorkSpace: an string with the name of the workspace to excute for 
	 *
	 * @return a string with the curl execution pat
	 */
	private function setExecRute($sArchiveToExecute,$sWorkSpace){
		$sHost=$_SERVER['HTTP_HOST'];
		$sRute="curl -p -O http:".PATH_SEP.PATH_SEP."{$sHost}".
		PATH_SEP."sys{$sWorkSpace}".PATH_SEP."en".
		PATH_SEP."classic".PATH_SEP."customizedApprovals".
		PATH_SEP."services".PATH_SEP."{$sArchiveToExecute}.php";
		return $sRute;
	}
	/**
	* This function will clean an array of strings, from unwanted characters 
	* that could be potentially dangerous for the Data Base.
	* 
	* @param $aUncleanArray: The array that needs to be cleaned
	*
	* @return the $aUncleanArray variable is updated with cleaned strings. 
	*/
	public function cleanStringsInArrayForQuery(&$aUncleanArray){
		foreach ($aUncleanArray as $key=>$value){
			$aUncleanArray[$key]="'".$this->cleanString($value)."'";
		}
	}
	/**
	* This function will clean a single string from unwanted characters 
	* 
	* @param $sUncleanString: the string that needs to be clean. 
	*
	* @return a cleaned string.
	*/
	public function cleanString($sUncleanString){
		$sUncleanString=str_replace("\\","\\\\",$sUncleanString);
		return str_replace("'","\\'",$sUncleanString);
	}
 /**
 * This function will extend the functionallity of the 
 * executeQuery() global function, and allow complex query execution
 *
 * @param $SqlStatement: the sql that needs to be executed.
 *
 * @returns depending on the query result:
 *					Select, Execute, show: a matrix with the resultant select sentence,
 *																 if an error ocurred it will return a -1
 *					Insert,Update,Delete,Create,Drop: the number of rows affected,
 *																						if an error ocurred a -1
 */
	public function executeQuery($SqlStatement) {
  try {
	    $statement = trim($SqlStatement);
	    $statement = str_replace('(', '', $statement);
	    //$this->con = Propel::getConnection($DBConnectionUID);
	    //$this->con->begin();
	
	    $result = false;
	
	    switch(true) {
	      case preg_match("/^SELECT\s/i", $statement):
	      case preg_match("/^EXECUTE\s/i", $statement):
	      case preg_match("/^SHOW\s/i",$statement):
	        $rs = $this->con->executeQuery($SqlStatement);
	        //$con->commit();
	        $result = Array();
	        $i=1;
	        while ($rs->next()) {
	          $result[$i++] = $rs->getRow();
	        }
	        break;
	      case preg_match("/^INSERT\s/i", $statement):
	      	$rs =($this->sConnectionString=="workflow") ? mysql_query($SqlStatement,$this->customConnect):
	      												  $this->con->executeUpdate($SqlStatement);
	        //$rs = $this->con->executeUpdate($SqlStatement);
	        //$con->commit();
	        //$result = $lastId->getId();
	        $sPosibleError=mysql_error();
	        $result = (!empty($sPosibleError)) ? mysql_error() : 1;
	        break;
	      case preg_match("/^UPDATE\s/i", $statement):
	        $rs = ($this->sConnectionString=="workflow") ? mysql_query($SqlStatement,$this->customConnect):
	      												  $this->con->executeUpdate($SqlStatement);
	        //$rs = $this->con->executeUpdate($SqlStatement);
					//$this->con->commit();
	        $result =  $this->con->getUpdateCount();
	        break;
	      case preg_match("/^DELETE\s/i", $statement):
	        $rs = $this->con->executeUpdate($SqlStatement);
	        //$con->commit();
	        $result =  $this->con->getUpdateCount();
	        break;
	       case preg_match("/^CREATE\s/i", $statement):
	       case preg_match("/^DROP\s/i", $statement):
	       	$rs = ($this->sConnectionString=="workflow") ? mysql_query($SqlStatement,$this->customConnect) :
	       												   $this->con->executeQuery($SqlStatement);
	       	$result =  $this->con->getUpdateCount();
	       break;
	    }
	    return $result;
	  } catch (SQLException $sqle) {
	    //$con->rollback();
	    throw $sqle;
	  }
	}
	/*
	* This function, will find if an element is in a given matrix, 
	*
	*	@param $sElementToSearch: the element that will fetched.
	* @param $aMatrix: the matrix in which the search will take place.
	*
	* @return true if the element was found or false if not.
	*/
	public function findElementInMatrix($sElementToSearch,$aMatrix){
  	foreach ($aMatrix as $key=>$value){
  		$bIsInArray=array_search($sElementToSearch,$value);
  		if ($bIsInArray!=false){
  				return true;
  		}
  	}	
  	return false;
	 }
	/*
	* this function will close any aditional connection.
	*
	* @return void.
	*/
	public function closeConnection(){
		//mysql_close($this->customConnect);
	}
	/*
	* This function sends an email
	*
	*	@param $sFrom Email address, the email address of the person who sends out the email.
	*	@param $sTo Email receptors, the email address(es) to whom the email is sent. If multiple recipients, separate each email address with a semicolon.
	*	@param $sCc Email addresses for copies, the email address(es) of people who will receive carbon copies of the email.
	*	@param $sBcc Email addresses for hidden copies, the email address(es) of people who will receive blind carbon copies of the email.
	*	@param $sSubject Subject of the email
	*	@param $sTemplate The name of the template file in plain text or HTML format which will produce the body of the email.
	*	@param $aFields An associative array where the keys are the variable names and the values are the variables' values.
	* @param $aAttachment An  Optional arrray. An array of files (full paths) to be attached to the email.
	*
	* @return true if the element was found or false if not.
	*/
	public function sendMessage($sFrom, $sTo, $sCc, $sBcc, $sSubject, $sTemplate, $aFields, $aAttachment = null){
		G::LoadClass('wsBase');
		try {
	      $aSetup = getEmailConfiguration();
	      $oSpool = new spoolRun();
	      $oSpool->setConfig(array(
	        'MESS_ENGINE'   => $aSetup['MESS_ENGINE'],
	        'MESS_SERVER'   => $aSetup['MESS_SERVER'],
	        'MESS_PORT'     => $aSetup['MESS_PORT'],
	        'MESS_ACCOUNT'  => $aSetup['MESS_ACCOUNT'],
	        'MESS_PASSWORD' => $aSetup['MESS_PASSWORD'],
	        'SMTPAuth'      => $aSetup['MESS_RAUTH']
	      ));
	      if ( ! file_exists ( $sTemplate ) ) {
	        $result = new wsResponse (28, "Template file '$sTemplate' does not exist."  );
	        return $result;
	      }
	      $fileTemplate = $sTemplate;
	      $Fields = $aFields;
	      
	      $templateContents = file_get_contents ( $fileTemplate );
	
	      //$sContent    = G::unhtmlentities($sContent);
	      $iAux        = 0;
	      $iOcurrences = preg_match_all('/\@(?:([\>])([a-zA-Z\_]\w*)|([a-zA-Z\_][\w\-\>\:]*)\(((?:[^\\\\\)]*(?:[\\\\][\w\W])?)*)\))((?:\s*\[[\'"]?\w+[\'"]?\])+)?/',  $templateContents, $aMatch, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE);
	
	      if ($iOcurrences) {
	        for($i = 0; $i < $iOcurrences; $i++) {
	          preg_match_all('/@>' . $aMatch[2][$i][0] . '([\w\W]*)' . '@<' . $aMatch[2][$i][0] . '/', $templateContents, $aMatch2, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE);
	          $sGridName       = $aMatch[2][$i][0];
	          $sStringToRepeat = $aMatch2[1][0][0];
	          if (isset($Fields[$sGridName])) {
	            if (is_array($Fields[$sGridName])) {
	              $sAux = '';
	              foreach ($Fields[$sGridName] as $aRow) {
	                $sAux .= G::replaceDataField($sStringToRepeat, $aRow);
	              }
	            }
	          }
	          $templateContents = str_replace('@>' . $sGridName . $sStringToRepeat . '@<' . $sGridName, $sAux, $templateContents);
	        }
	      }
	
	      $sBody = G::replaceDataField( $templateContents, $Fields);
	
	      if ($sFrom != '') {
	        $sFrom = $sFrom . ' <' . $aSetup['MESS_ACCOUNT'] . '>';
	      } 
	      else {
	        $sFrom = $aSetup['MESS_ACCOUNT'];
	      }
				$caseId = "";
	      $messageArray = array(
	        'msg_uid'          => '',
	        'app_uid'          => $caseId,
	        'del_index'        => 0,
	        'app_msg_type'     => 'TRIGGER',
	        'app_msg_subject'  => $sSubject,
	        'app_msg_from'     => $sFrom,
	        'app_msg_to'       => $sTo,
	        'app_msg_body'     => $sBody,
	        'app_msg_cc'       => $sCc,
	        'app_msg_bcc'      => $sBcc,
	        'app_msg_attach'   => $aAttachment,
	        'app_msg_template' => '',
	        'app_msg_status'   => 'pending'
	      );
	      $oSpool->create( $messageArray );
	      $oSpool->sendMail();
	
	      if ( $oSpool->status == 'sent' )
	        $result = new wsResponse (0, "message sent : $sTo" );
	      else
	        $result = new wsResponse (29, $oSpool->status . ' ' . $oSpool->error . print_r ($aSetup ,1 ) );
	      return $result;
	    } 
	    catch ( Exception $e ) {
	      $result = new wsResponse (100, $e->getMessage());
	      return $result;
	    }
	}
	
	/**
	 * This method retrieves the admin configuration from the PM hash file
	 * @return array containing the admin configuration strings
	 */
	public function GetHashAdminConfiguration()
	{
		$cResult = G::Decrypt(HASH_INSTALLATION, SYSTEM_HASH);
		$cResult = str_replace(SYSTEM_HASH, '-', $cResult);
		$cResult = explode("-",$cResult);
		
		return $cResult;
	}
	
	public function settingXml($sXmlFilenameRoute, $sFieldToChange, $sAtributeToChange, $sValueToChange){		
		$oXmlObject=new DOMDocument();
		$bEditStatus = $oXmlObject->load($sXmlFilenameRoute);
		$oXmlDomElement = $oXmlObject->documentElement;		
		$element = $oXmlDomElement->getElementsByTagName($sFieldToChange);
		$element->item(0)->setAttribute($sAtributeToChange,$sValueToChange);		
		$bEditStatus=$oXmlObject->save($sXmlFilenameRoute);
		return $bEditStatus;
	}
}

?>