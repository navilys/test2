<?php
/**
 * 
 */
class tclConnectionManagement {
	private 
			$oPropelCon,
	        $oCustomCon,
			$bUsePropel;
	/**
	 * 
	 * Class constructor that will initiate a propel or native connection
	 * based on the number of arguments given
	 * 
 	 * @param string sWorkspace: single argument only, connection to given workspace type of connection.
	 * @param string sHost: three params first part, data base host name.  
	 * @param string sUserName: three params second part, data base name.
	 * @param string sUserPass: three params third part, data base password
	 * @param string sDbDatabase : database to be selected
	 */
	public function __construct(){
		if (func_num_args()==1){
			$this->bUsePropel=true;
			$this->createPropelConnection(func_get_arg(0));
		}else if (func_num_args()==4 or func_num_args()==3){
			$this->bUsePropel=false;
			$this->createNativeConnection(func_get_args());
			if (func_num_args()==4){
				$this->selectDataBase(func_get_arg(3));
			}
		}
	}
	/**
	 * 
	 * Functon that will create a propel connection
	 * @param string $sWorkspace: workspace name
	 */
	private function createPropelConnection($sWorkspace){
		$this->oPropelCon=Propel::getConnection($sWorkspace);
	}
	/**
	 * 
	 * function that will create a native connection 
	 * @param string $aConnectionData: array that contains 
	 */
	private function createNativeConnection($aConnectionData){
		$this->oCustomCon=mysql_connect($aConnectionData[0],$aConnectionData[1],$aConnectionData[2]);
	}
	/**
	 * 
	 * this function will execute a query 
	 * @param string $sSqlQuery: contains the sql sentence
	 * @throws SQLException : when error ocurres
	 * @return when select function an array with result, when execute update 
	 */
	public function executeQuery($sSqlQuery){
		try {
		    $statement = trim($sSqlQuery);
		    //$statement = str_replace('(', '', $statement);
		    $result = false;
		    switch(true) {
		      case preg_match("/^SELECT\s/i", $statement):
		      case preg_match("/^EXECUTE\s/i", $statement):
		      case preg_match("/^SHOW\s/i",$statement):
		        	$result = $this->selectExecuteQuery($statement);
		        break;
		      case preg_match("/^UPDATE\s/i", $statement):
		      case preg_match("/^INSERT\s/i", $statement):
		      case preg_match("/^DELETE\s/i", $statement):
		      case preg_match("/^CREATE\s/i", $statement):
		      case preg_match("/^DROP\s/i", $statement):
		      		$result =$this->executeUpdate($statement);
		        break;
		      default:
		      		$result = $this->selectExecuteQuery($statement);
		    }
		    return $result;
		  } catch (SQLException $sqle) {
		    //$con->rollback();
		    throw $sqle;
		  }
	}
	/**
	 * 
	 * this function will allow to select database
	 * @param unknown_type $sDbToSelect
	 */
	public function selectDataBase($sDbToSelect){
		$bDatabaseSelected = mysql_select_db($sDbToSelect,$this->oCustomCon);
		if (!$bDatabaseSelected) {
		    die (mysql_error());
		}
	}
	/**
	 * 
	 * Enter description here ...
 	 * @param string sWorkspace: single argument only, connection to given workspace type of connection.
	 * @param string sHost: three params first part, data base host name.  
	 * @param string sUserName: three params second part, data base name.
	 * @param string sUserPass: three params third part, data base password
	 * @param string sDbDatabase : database to be selected
	 */
	public function stablishNewConnection(){
		if (func_num_args()==1){
			$this->bUsePropel=true;
			$this->createPropelConnection(func_get_arg(0));
		}else if (func_num_args()==4 or func_num_args()==3){
			$this->closeConnection();
			$this->bUsePropel=false;
			$this->createNativeConnection(func_get_args());
			if (func_num_args()==4){
				$this->selectDataBase(func_get_arg(3));
			}
		}
	}
	/**
	 * 
	 * This function will clos data base connection
	 */
	public function closeConnection(){
		if (isset($this->oCustomCon)) 
		 mysql_close($this->oCustomCon);
	}
	/**
	 * 
	 * this function will execute a Propel Like sentece or a mysql native connection
	 * based on the current connection.
	 * @param string $sqlSentence:this is a sql sentence that willbe executed
	 * @return sqlReturn
	 */
	private function selectExecuteQuery($sqlSentence){
		return $this->bUsePropel ? $this->propelExecuteQuery($sqlSentence) : $this->nativeExecuteQuery($sqlSentence);
	}
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $sqlSentence
	 */
	private function executeUpdate($sqlSentence){
		return $this->bUsePropel ? $this->propelExecuteUpdate($sqlSentence) : $this->nativeExecuteQuery($sqlSentence);
	}
	/**
	 * 
	 * this function will excute an update type of sentence with propel engine
	 * @param unknown_type $sqlSentence
	 */
	private function propelExecuteUpdate($sqlSentence){
		$this->oPropelCon->executeUpdate($sqlSentence);
		return $this->oPropelCon->getUpdateCount();
	}
	/**
	 * 
	 * this function will execute an select type ofsentence with propel engine.
	 * @param unknown_type $sqlSentence
	 */
	private function propelExecuteQuery($sqlSentence){
		$recorSet = $this->oPropelCon->executeQuery($sqlSentence);
	 	$aResult = Array();
        $i=1;
        while ($recorSet->next()) {
          $aResult[$i++] = $recorSet->getRow();
        }
        return $aResult;
	}
	/**
	 * 
	 * This function will execute a query in a native connection
	 * @param unknown_type $sqlSentence
	 */
	private function nativeExecuteQuery($sqlSentence){
		$oResult= mysql_query($sqlSentence,$this->oCustomCon);
		if (mysql_error()){
			echo mysql_error();
		} else if (is_resource($oResult)) {
			$aResult = array();
			$i=1;
			while ($aRow=mysql_fetch_array($oResult)){
				$aResult[$i++] = $aRow;
			}
			return $aResult;
		} else if (is_bool($oResult)){
			return ($this->oCustomCon);
		}
		return false;
	}
}
?>