<?php
G::loadClass('pmFunctions');
//require_once(PATH_PLUGINS.'customizedApprovals'.PATH_SEP.'classes'.PATH_SEP.'class.customizedFunctions.php');
require_once(PATH_PLUGINS.'pmReplicator'.PATH_SEP.'classes'.PATH_SEP.'class.tclAuxiliary.php');
require_once(PATH_PLUGINS.'pmReplicator'.PATH_SEP.'classes'.PATH_SEP.'class.tclConnectionManagement.php');
/**
*
* This class will p
*/
class tclReplicator{
	//variables
		private
		$aDestinationDb,
		$sOriginDb,
		$sPrefix = "REPLICA_",
		$oConnection,
		$sSuperUID= "6117666494de8085def7cc0077310579";
		/**
		* this is the constructor
		*/
		function __construct($sOriginDbName,$aDestinationDbName){
			$this->aDestinationDb=$aDestinationDbName;
			$this->sOriginDb=$sOriginDbName;
			$oUtility = new tclAuxiliary();
       		$cResult = $oUtility->GetHashAdminConfiguration();
      		$this->oConnection = new tclConnectionManagement();
       		$this->oConnection->stablishNewConnection($cResult[0],$cResult[1],$cResult[2]);
		}
		/**
		* set the given value to the private attribute sOriginDb
		*/
		function setOriginWorkspace($sWorkspace){
			$this->sOriginDb = $sWorkspace;
		}
		/**
		 * 
		 * A function that will execute a backup process
		 * @param string $sTableToBackup: an string 
		 */
		private function executeBackUpQuery($sTableToBackup){
			foreach ($this->aDestinationDb as $value){
				$aResult=$this->verifyIfExist($value, $sTableToBackup);
				if (count($aResult)>0){
					$aExistAuxTable=$this->verifyIfExist($value,$this->sPrefix.$sTableToBackup);
					if (count($aExistAuxTable)>0){
						$this->executeDeleteQuery($sTableToBackup,true);
					}
					$sSql="CREATE TABLE
									wf_".$value.".".$this->sPrefix.$sTableToBackup."
								 SELECT * FROM
								 	wf_".$value.".".$sTableToBackup;
					$this->tclExecuteQuery($sSql);
				}
			}
		}
		/**
		 * 
		 * this function calls the execute query that belongs to propel structure.
		 * @param unknown_type $sSql
		 */
		private function tclExecuteQuery($sSql){
				return $this->oConnection->executeQuery($sSql);
				
			}
		/**
		 * 
		 * this function will origin's structure only, keping 
		 * destination's data
		 * @param array $aSelectedTables: all the selected tables
		 */
		public function copyStructureOnly($aSelectedTables){
			try {
				foreach ($aSelectedTables as $value){
						$this->executeBackUpQuery($value);
						$this->executeDeleteQuery($value);
						$this->executeCopyQuery($value);
						$this->emptyDataQuery($value);
						$this->replaceDataFromBackup($value);
						$this->createPropelFileStructure($value);
						$this->executeDeleteQuery($value,true);
						$this->saveStructureInAdditionalTables($value);
					}
				return "done";
			} catch(Exception $e){
				return $e->getMessage();
			}
		 }
		 /**
		  * 
		  * This function will copy structure and data from the origin workspace to the
		  * selected destination workspaces 
		  * @param array $aSelectedTables: all the selected tables
		  */
		 public function copyTablesWithData($aSelectedTables){
		 	try{
		 		foreach ($aSelectedTables as $value){
		 			$this->executeDeleteQuery($value);
		 			$this->executeCopyQuery($value);//
					$this->createPropelFileStructure($value);
					$this->saveStructureInAdditionalTables($value);//
				}	
				return "done";
		 	} catch (Exception $e){
		 		return $e->getMessage();
		 	}
		 }
		 /**
		  * 
		  * This function will copy data for the origin Workspace 
		  * and erase the data in destination workspaces mataining the structure
		  * @param array $aSelectedTables: all the selected tables 
		  */
		 public function copyDataOnly($aSelectedTables){
		 	try{
			 	foreach ($aSelectedTables as $value){
			 		$this->emptyDataQuery($value);
			 		$this->replaceDataFromOrigin($value);
			 	}	
			 	return "done";
		 	} catch (Exception $e){
		 		return $e->getMessage();
		 	}
		 }
		/**
		 * 
		 * This function will eliminate a single temporary table inside the every single workspace selected
		 * @param string $sTableToDelete: table to be deleted
		 * @param boolean $isTemp: if the table is temporay 
		 */
		private function executeDeleteQuery($sTableToDelete,$isTemp=false){
			$sTableToDelete = $isTemp ? $this->sPrefix.$sTableToDelete : $sTableToDelete;
			foreach ($this->aDestinationDb as $value){
				$aResult=$this->verifyIfExist($value,$sTableToDelete);
				if (count($aResult)>0){
					$sSql="DROP TABLE
									wf_".$value.".".$sTableToDelete;
					$this->tclExecuteQuery($sSql);
				}
			}
		}
		/**
		 * 
		 * This function will execute a copy sql sentence
		 * @param string $sTableToCopy: an string with the table name to be copied
		 */
		private function executeCopyQuery($sTableToCopy){
			foreach ($this->aDestinationDb as $value){
					$aKeys=$this->getPrimaryKeyFields($sTableToCopy);
					if (count($aKeys)>0){
						$sSql="CREATE TABLE
										wf_".$value.".".$sTableToCopy."
							   (PRIMARY KEY (".implode(",", $aKeys).")) 
									   SELECT * FROM
									 	wf_".$this->sOriginDb.".".$sTableToCopy;
						$this->tclExecuteQuery($sSql);
					}
				}
		 }
		 /**
		  * 
		  * This function will get the primary key's field name 
		  * @param string $sTableToGetPk:a string with the table name needed to get the primary key field from
		  */ 
		 private function getPrimaryKeyFields($sTableToGetPk){
		 	$sSql="SELECT fie.FLD_NAME FROM 
	 				wf_".$this->sOriginDb.".FIELDS fie 
	 			   INNER JOIN
	 				wf_".$this->sOriginDb.".ADDITIONAL_TABLES af
	 			   ON
	 			   	fie.ADD_TAB_UID=af.ADD_TAB_UID
	 			   WHERE
	 			   	af.ADD_TAB_NAME='$sTableToGetPk' AND
	 			   	fie.FLD_KEY=1";
		 	$aQueryResult=$this->tclExecuteQuery($sSql);
		  	$aResult;
		  	foreach ($aQueryResult as $value){
		  		$aResult[]=$value["FLD_NAME"];
		  	}
		  	return $aResult;
		 }
		 /**
		  * 
		  * this functio will empty a given table
		  * @param string $sTableToEmpty: 
		  */
		 private function emptyDataQuery($sTableToEmpty){
		 		foreach ($this->aDestinationDb as $value){
		 			$aResult=$this->verifyIfExist($value, $sTableToEmpty);
		 			if (count($aResult)){
						$sSql="DELETE FROM
										wf_".$value.".".$sTableToEmpty;
						$this->tclExecuteQuery($sSql);
		 			}
				}
		  }
		/**
		 * 
		 * Enter description here ...
		 * @param unknown_type $sTableToFill
		 */
		 private function replaceDataFromBackup($sTableToFill){
		  		foreach ($this->aDestinationDb as $value){
		  			$aResult=$this->verifyIfExist($value,$this->sPrefix.$sTableToFill);
		  			if (count($aResult)>0){
		  				$aOriginFields=$this->getFieldsForTable($this->sOriginDb,$sTableToFill);
		  				$aFields=$this->getFieldsForTable($value,$sTableToFill);
		  				//$aFields=(count($aOriginFields)>=count($aFields))? $aFields : $aOriginFields;
		  				//Edwin, only need fields that both tables have in common
		  				$aFields = array_intersect($aFields,$aOriginFields);
		  				if (count($aFields)>0){
								$sSql="INSERT INTO 
												wf_".$value.".".$sTableToFill."
											 (".implode(",", $aFields).") 
											 SELECT ".implode(",",$aFields)." FROM wf_".
											  $value.".".$this->sPrefix.$sTableToFill;
								$this->tclExecuteQuery($sSql);
							}
		  			}
				}
		  }
		  /**
		   * 
		   * Enter description here ...
		   * @param unknown_type $sTableToGetFieldsFor
		   */
		  private function getFieldsForTable($db,$sTableToGetFieldsFor){
		  	$sSql="SELECT fie.FLD_NAME FROM 
	 				wf_".$db.".FIELDS fie 
	 			   INNER JOIN
	 				wf_".$db.".ADDITIONAL_TABLES af
	 			   ON
	 			   	fie.ADD_TAB_UID=af.ADD_TAB_UID
	 			   WHERE
	 			   	af.ADD_TAB_NAME='$sTableToGetFieldsFor'
	 			   ORDER BY
	 			    fie.FLD_INDEX";
		  	$aQueryResult=$this->tclExecuteQuery($sSql);
		  	$aResult;
		  	foreach ($aQueryResult as $value){
		  		$aResult[]=$value["FLD_NAME"];
		  	}
		  	return $aResult;
		  }
		  /**
		   * 
		   * Enter description here ...
		   * @param unknown_type $sTableToFill
		   */
		private function replaceDataFromOrigin($sTableToFill){
		  		foreach ($this->aDestinationDb as $value){
		  			$aResult=$this->verifyIfExist($value, $sTableToFill);
		  			if (count($aResult)){
						$sSql="INSERT INTO 
										wf_".$value.".".$sTableToFill."
									 SELECT * FROM wf_".
									  	$this->sOriginDb.".".$sTableToFill;
						$this->tclExecuteQuery($sSql);
		  			}
				}
		  }
		  /**
		   * 
		   * This function will verify in every selected databases if 
		   * the table is pressent
		   * @param string $sTableToVerif: the name of the table to verfy
		   */
		 private function verifyIfExist($sDbToVerify,$sTableToVerif){
		 		$sSql="SELECT * FROM
		 					INFORMATION_SCHEMA.tables 
		 			   WHERE
		 			   		TABLE_SCHEMA='wf_".$sDbToVerify."' AND
		 			   		TABLE_NAME='$sTableToVerif'";
		 	return $this->tclExecuteQuery($sSql);
		 }
		 /**
		  * 
		  * This function will copy propel schema to the new set of directory schema
		  * 
		  * @param string $sTableToCopy: the table that will be created
		  */
		 private function copyPropelSchema($sTableToCopy){
		 	
		 }
		/**
		 * 
		 * Enter description here ...
		 * @param unknown_type $sTableToCopy
		 */
		 private function createPropelFileStructure($sTableToCopy){
		 	$sRuteToOrigin=PATH_DB.$this->sOriginDb.PATH_SEP."classes".PATH_SEP;
		 	foreach ($this->aDestinationDb as $value){
		 		$sRuteTodestinyClass=PATH_DB.$value.PATH_SEP."classes".PATH_SEP;
		 		G::verifyPath($sRuteTodestinyClass,true);
		 		$this->copyPropelFiles($sRuteToOrigin,$sRuteTodestinyClass,$this->underscoreToCammelCase($sTableToCopy));
		 		$this->copyPropelFiles($sRuteToOrigin,$sRuteTodestinyClass,$this->underscoreToCammelCase($sTableToCopy)."Peer");
		 		G::verifyPath($sRuteTodestinyClass."map",true);
		 		$this->copyPropelFiles($sRuteToOrigin."map".PATH_SEP, $sRuteTodestinyClass."map".PATH_SEP, $this->underscoreToCammelCase($sTableToCopy)."MapBuilder");
		 		G::verifyPath($sRuteTodestinyClass."om",true);
		 		$this->copyPropelFiles($sRuteToOrigin."om".PATH_SEP,$sRuteTodestinyClass."om".PATH_SEP,"Base".$this->underscoreToCammelCase($sTableToCopy));
		 		$this->copyPropelFiles($sRuteToOrigin."om".PATH_SEP,$sRuteTodestinyClass."om".PATH_SEP,"Base".$this->underscoreToCammelCase($sTableToCopy)."Peer");
		 	}
		 }
		 /**
		  * 
		  * Enter description here ...
		  * @param  $sTableToCopy
		  */
		 private function copyPropelFiles($sOriginRute,$sDestinyRute,$sTableToCopy){
		 	$verifiedOPath=G::verifyPath($sOriginRute.$sTableToCopy.".php");
		 	if ($verifiedOPath){
		 		copy($sOriginRute.$sTableToCopy.".php", $sDestinyRute.$sTableToCopy.".php");
		 	}
		 }
		 /**
		  * 
		  * this function will change an underscored type of string into a cammel case string
		  * @param string $sToCammel 
		  * @return a cammelcase string
		  */
		 private function underscoreToCammelCase($sToCammel){
		 	$sToCammel=strtolower($sToCammel);
     		$sToCammel[0] = strtoupper($sToCammel[0]);
    		$func = create_function('$sChar', 'return strtoupper($sChar[1]);');
    		return preg_replace_callback('/_([a-z])/', $func, $sToCammel);
		 }
		 /**
		  * 
		  * this function will save new the new table in dataTable Aditional_Tables
		  * @param string $sTableToAdd: an string with the name of the table to transfer 
		  */
		 private function saveStructureInAdditionalTables($sTableToAdd){
		 	foreach ($this->aDestinationDb as $sWorkSpace){
		 		$this->deleteStructureInFields($sWorkSpace, $sTableToAdd);
		 		$this->deleteStructureInAdditionalTables($sWorkSpace, $sTableToAdd);
		 		$sSql="INSERT INTO 
		 				wf_".$sWorkSpace.".ADDITIONAL_TABLES
		 			   SELECT * FROM 
		 				wf_".$this->sOriginDb.".ADDITIONAL_TABLES o
		 			   WHERE
		 			   	o.ADD_TAB_NAME='$sTableToAdd'";
		 		$this->tclExecuteQuery($sSql);
		 		$this->saveStructureInFields($sWorkSpace, $sTableToAdd);
		 	}
		 }
		 /**
		  * 
		  * Enter description here ...
		  * @param unknown_type $sWorspace
		  * @param unknown_type $sTableToClean
		  */
		 private function deleteStructureInAdditionalTables($sWorkspace,$sTableToClean){
		 	$sSql="DELETE FROM 
		 			wf_".$sWorkspace.".ADDITIONAL_TABLES
		 		   WHERE 
		 		   	ADD_TAB_NAME='$sTableToClean'";
		 	$this->tclExecuteQuery($sSql);
		 }
		 /**
		  * 
		  * Enter description here ...
		  * @param unknown_type $sWorkspace
		  * @param unknown_type $sTableToClean
		  */
		 private function deleteStructureInFields($sWorkspace,$sTableToClean){
		 	$sSql="DELETE FROM
		 			wf_".$sWorkspace.".FIELDS 
		 		   WHERE 
		 		    ADD_TAB_UID=
		 		    (SELECT 
		 		    	ADD_TAB_UID
		 		    FROM
		 		    	wf_".$sWorkspace.".ADDITIONAL_TABLES 
					WHERE
						ADD_TAB_NAME='$sTableToClean' )";
		 	$this->tclExecuteQuery($sSql);
		 }
		 /**
		  * 
		  * this function will save the fields structure of pm tables.
		  * @param string $sWorkSpace: workspace's name
		  * @param string $sTableToAdd: table's name
		  */
		 private function saveStructureInFields($sWorkspace,$sTableToAdd){
		 	$sSql="INSERT INTO 
	 				wf_".$sWorkspace.".FIELDS
	 			   SELECT fie.* FROM 
	 				wf_".$this->sOriginDb.".FIELDS fie 
	 			   INNER JOIN
	 				wf_".$this->sOriginDb.".ADDITIONAL_TABLES af
	 			   ON
	 			   	fie.ADD_TAB_UID=af.ADD_TAB_UID
	 			   WHERE
	 			   	af.ADD_TAB_NAME='$sTableToAdd'";
		 	$this->tclExecuteQuery($sSql);
		 }
	}
/**
 * 
 */
class tclManageWorkSpaces{
	private $aWrokSpaces;
	/**
	 * 
	 * Enter description here ...
	 */
	public function __construct(){
		$dir = PATH_DB;
		  if (file_exists ( $dir )) {
		  	$handle = opendir ( $dir);
		    if ($handle) {
		      while ( false !== ($file = readdir ( $handle )) ) {
		        if (($file != ".") && ($file != "..")) {
		          if (file_exists ( $dir . $file . '/db.php' )) {
		            //$statusl = ( $oServerConf->isWSDisabled($file)) ? G::LoadTranslation ( 'ID_DISABLED' ) : G::LoadTranslation ( 'ID_ENABLED' );
		              $this->aWrokSpaces [] = $file;
		          }
		        }
		      }
		      closedir ( $handle );
		    }
		  }
	}
	public function getWorkSpaces(){
		return $this->aWrokSpaces;
	}
}
/**
 * 
 * This class will allow process transfrence manipulation.
 * @author Marel
 *
 */
class tclProcessManipulator{
	private 
		$sDestinyRute,
		$sActualRute,
		$sPExportFileName="processExport.php",
		$sPImportFileName="processImport.php",
		$oCurl,
		$sFolderName="services";
	/**
	 * 
	 * Enter description here ...
	 */
	public function __construct(){
		$this->sDestinyRute=PATH_CORE."methods".PATH_SEP."services".PATH_SEP;
		$this->sActualRute=PATH_PLUGINS.'pmReplicator'.PATH_SEP.$this->sFolderName.PATH_SEP;
		$this->copyFileToNewPosition($this->sPExportFileName);
		$this->copyFileToNewPosition($this->sPImportFileName);
		$this->oCurl=curl_init();
	}
	/**
	 * 
	 * 
	 * @param unknown_type $sFileName
	 */
	private function copyFileToNewPosition($sFileName){
		$verifyPath=G::verifyPath($this->sActualRute.$sFileName);
		if ($verifyPath){
			copy($this->sActualRute.$sFileName, $this->sDestinyRute.$sFileName);
		}
	}
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $sUrl
	 * @param unknown_type $aData
	 */
	private function executeRute($sUrl,$aData){
		curl_setopt($this->oCurl,CURLOPT_URL,$sUrl);
		curl_setopt($this->oCurl,CURLOPT_POST,1);
		curl_setopt($this->oCurl,CURLOPT_POSTFIELDS,$aData);
		curl_setopt($this->oCurl, CURLOPT_RETURNTRANSFER,true);
		$sResult=curl_exec($this->oCurl);
		return $sResult;
	}
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $sFile
	 * @param unknown_type $sWorkspace
	 */
	private function ruteComposition($sFile,$sWorkspace){
		$sProtocol = "http:";
   		if(isset($_SERVER['HTTPS']))
       		$sProtocol = "https:";
       	$sProtocol.=PATH_SEP.PATH_SEP;
		$sUrl=$sProtocol.$_SERVER['HTTP_HOST'].PATH_SEP."sys$sWorkspace".PATH_SEP.SYS_LANG.PATH_SEP."raw".PATH_SEP.$this->sFolderName.PATH_SEP;
		return $sUrl.$sFile;
	}
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $sOriginWorkspace
	 * @param unknown_type $sDestinyWorkspace
	 * @param unknown_type $filename
	 */
	private function moveArchiveToDestinyFolder($sOriginWorkspace,$sDestinyWorkspace,$sFilename){
		$pathOrigin = PATH_DB.$sOriginWorkspace.PATH_SEP.'files'.PATH_SEP.'output'.PATH_SEP.$sFilename;
		G::verifyPath(PATH_DB.$sDestinyWorkspace.PATH_SEP.'files'.PATH_SEP.'input',true);
		$pathDestiny = PATH_DB.$sDestinyWorkspace.PATH_SEP.'files'.PATH_SEP.'input'.PATH_SEP.$sFilename;
		if(is_file($pathOrigin)){
			return copy($pathOrigin,$pathDestiny);
		}
		return false;
	}
	/**
	 * 
	 * This function will export process in a given workspace and put that on the 
	 * @param unknown_type $sWorkspace
	 * @param unknown_type $sProcess
	 */
	public function exportProcess($sWorkspace,$sData){
		$aData=array("DATA_UID"=>$sData);
		$_SESSION['EX_ROUTE'] = $this->ruteComposition($this->sPExportFileName,$sWorkspace);
		$_SESSION['EX_DATA'] = $aData;
		return $this->executeRute($this->ruteComposition($this->sPExportFileName,$sWorkspace), $aData);
	}
	/**
	 * 
	 * this function will import process in a given workspace.
	 * @param unknown_type $sWorkspace
	 * @param unknown_type $sData
	 */
	public function importProcess($sOriginWorkspace,$sDestinyWorkspace,$sData){
		$bCopyResult = $this->moveArchiveToDestinyFolder($sOriginWorkspace, $sDestinyWorkspace, $sData);
		if(!$bCopyResult)
			return "fail";
		$aData=array("DATA_FILENAME"=>$sData);
		return $this->executeRute($this->ruteComposition($this->sPImportFileName,$sDestinyWorkspace), $aData);
	}
}
?>