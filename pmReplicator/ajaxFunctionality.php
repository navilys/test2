<?php
    require_once(PATH_PLUGINS.'pmReplicator'.PATH_SEP.'classes'.PATH_SEP.'class.tclAuxiliary.php');
    require_once(PATH_PLUGINS.'pmReplicator'.PATH_SEP.'classes'.PATH_SEP.'class.tclConnectionManagement.php');
    require_once(PATH_PLUGINS.'pmReplicator'.PATH_SEP.'classes'.PATH_SEP.'class.tclProcessReplicator.php');
    $dataManipulation = new dataRetrive();
    if (isset($_POST["workspace"])){
        $dataManipulation->getSingleWorkspaceData ($_POST["workspace"]);
    }else{
        switch($_POST["sTableName"]){
            case "dynProcessO":
            case "process":
                $dataManipulation->sendDataToClient(false);
                break;
            case "tables":
                $dataManipulation->sendDataToClient(true);
                break;
            case "wrkspaceO":
                $dataManipulation->sendWorkspacesToClient();
                break;
            case "dynaformsO":
                    $dataManipulation->sendDataToClient(false);
                break;
        }
        //$dataManipulation->sendDataToClient(($_POST["sTableName"]!="process"));
    }
    class dataRetrive{
        private $oCnn;

        public function __construct() {
            $oUtility = new tclAuxiliary();
            $cResult = $oUtility->GetHashAdminConfiguration();
            $this->oCnn = new tclConnectionManagement();
            $this->oCnn->stablishNewConnection($cResult[0],$cResult[1],$cResult[2]);
        }
        /**
        * This function will return a json structure for a single workspace
        * @param string $sWorkSpace
        */
       public function getSingleWorkspaceData($sWorkSpace){
           $sTables=" ORDER BY
                           ADD_TAB_NAME";
           $sProcess="ORDER BY 2";
           $result["tables"]=  $this->oCnn->executeQuery($this->getTableQuery($sWorkSpace).$sTables);
           $result["process"]=  $this->oCnn->executeQuery($this->getProcessQuery($sWorkSpace).$sProcess);
           print json_encode($result);
       }
       /**
        * 
        */
      private function getUnionQuery($bIsTable){
           $aWorkSpaces = (!isset($_POST["sWorkspace"])) ? $this->getAlistOfWorkSpaces():array($_POST["sWorkspace"]);
           $sUnion=" UNION ";
           $sQuery="";
           foreach ($aWorkSpaces as $aRow){
                if ($sQuery!="")
                 $sQuery.=$sUnion;
                $sWhere = isset($_POST['sSearch']) && $_POST['sSearch']!="" ?
                        $this->addingSenteceClause($bIsTable, $aRow):
                        "";
                $sWhere = isset($_POST['aSelectedData']) && $sWhere=="" ? 
                        " WHERE (".$this->notLikeWhere($bIsTable,$aRow).")":$sWhere;
                $sQuery .=$this->getMainQuery($bIsTable, $aRow, $sWhere);
                /*$sQuery.= $bIsTable ? '('.$this->getTableQuery($aRow,true).$sWhere.')':
                         '('.$this->getProcessQuery($aRow,true).$sWhere.')';*/
           }
           return $sQuery;
       }
       private function getMainQuery($bIsTable,$sWorkspace,$sWhere){
           $sGridName="";
           if ($_POST["sTableName"]=="dynaformsO"){
            $sGridName="dynaforms";
           }else{
            $sGridName=$bIsTable ? "tables":"process";
           }
           switch($sGridName){
               case "tables":
                    return '('.$this->getTableQuery($sWorkspace,true).$sWhere.')';
                   break;
               case "process":
                    return '('.$this->getProcessQuery($sWorkspace,true).$sWhere.')';
                   break;
               case "dynaforms":
                    return '('.$this->getProcessOnly($sWorkspace).$sWhere.')';
                   break;
               default:
                   return $sWhere;
                   break;
           }
           
       }
       /**
        * This function will generate process only for dynforms tables
        * 
        * @param string $sWorkSpace: workspace the query will work with
        * @return string
        */
       private function getProcessOnly($sWorkSpace){
            $sQuery="SELECT 
                                  '$sWorkSpace' as WORKSPACE, S.CON_ID,E.CON_VALUE as PROCESS,COALESCE(S.CON_VALUE,'Label not found') as CON_VALUE
                               FROM 
                                       wf_".$sWorkSpace.".DYNAFORM P 
                               LEFT JOIN
                                       wf_".$sWorkSpace.".CONTENT S
                               ON
                                       (P.DYN_UID=S.CON_ID AND S.CON_LANG='".SYS_LANG."' AND S.CON_CATEGORY='DYN_TITLE')
                               LEFT JOIN
                                       wf_".$sWorkSpace.".CONTENT E
                               ON
                                       (P.PRO_UID=E.CON_ID AND E.CON_LANG='en' AND E.CON_CATEGORY='PRO_TITLE')";
           return $sQuery; 
       }
       /**
        * 
        * @param type $bIsTable
        */
       private function dataLimit($bIsTable){
           $sOrderBy=" ORDER BY ".($_POST['iSortCol_0']+1)." ".$_POST['sSortDir_0'];
           $sSetLimit=" LIMIT ".$_POST['iDisplayStart'].", ".$_POST['iDisplayLength'];
           $sQuery='SELECT * FROM ('.$this->getUnionQuery($bIsTable).') UNRES'.$sOrderBy.$sSetLimit;
           //G::pr($sQuery);
           return $this->transformProcessMakerArrayToNormal($this->oCnn->executeQuery($sQuery));
       }
       /**
        * 
        * @param type $bIsTable
        * @return type
        */
       private function numOfRows($bIsTable){
           $sQuery='SELECT count(WORKSPACE) FROM ('.$this->getUnionQuery($bIsTable).') UNRES ';
           $aCount=$this->oCnn->executeQuery($sQuery);
           return $aCount[1][0];
       }
      /**
       * 
       * @return type
       */
      public function getAlistOfWorkSpaces(){
           $oWorkspaces=new tclManageWorkSpaces();
           return $oWorkspaces->getWorkSpaces();
       }
       /**
        * 
        * @param string $sWorkSpace: workspace name
        * @return Array array of returning elements
        */
       public function getTableQuery($sWorkSpace,$bAddWorkspace=false){
           $sAddWorkspace = $bAddWorkspace ? "'$sWorkSpace' as WORKSPACE" : "";
           $sQuery="SELECT 
                           $sAddWorkspace,ADD_TAB_NAME,ADD_TAB_NAME as NAME
                   FROM
                           wf_".$sWorkSpace.".ADDITIONAL_TABLES";
           return $sQuery;
       }
       /**
        * 
        * @param string $sWorkSpace: workspace name
        * @return Array array of returning elements
        */
       public function getProcessQuery($sWorkSpace,$bAddWorkspace=false){
           $sTable=$_POST["sTableName"]=="dynaformsO" ? "DYNAFORM" : "PROCESS";
           $sField=$_POST["sTableName"]=="dynaformsO" ? "DYN_UID" : "PRO_UID";
           $sCategory=$_POST["sTableName"]=="dynaformsO" ? "DYN_TITLE" : "PRO_TITLE";
           $sAddWorkspace = $bAddWorkspace ? "'$sWorkSpace' as WORKSPACE" : "";
           $sQuery="SELECT 
                                       $sAddWorkspace,S.CON_ID,COALESCE(S.CON_VALUE,E.CON_VALUE,'Label not found') as CON_VALUE
                               FROM 
                                       wf_".$sWorkSpace.".$sTable P 
                               LEFT JOIN
                                       wf_".$sWorkSpace.".CONTENT S
                               ON
                                       (P.$sField=S.CON_ID AND S.CON_LANG='".SYS_LANG."' AND S.CON_CATEGORY='$sCategory')
                               LEFT JOIN
                                       wf_".$sWorkSpace.".CONTENT E
                               ON
                                       (P.$sField=E.CON_ID AND E.CON_LANG='en' AND E.CON_CATEGORY='$sCategory')";
           return $sQuery; 
       }
       /**
        * 
        * @param type $aArrayToTransform
        * @return type
        */
       public function transformProcessMakerArrayToNormal($aArrayToTransform){
          $aCleanMatrix = array();
          foreach ($aArrayToTransform as $aRow){
              $aNewRow = array();
              foreach($aRow as $key=>$sValue){
                  if (is_numeric($key))
                      $aNewRow[]=$sValue;
              }
              $aCleanMatrix[]=$aNewRow;
          }
          return $aCleanMatrix;
       }
       /**
        * 
        * @param type $sTable
        * @param type $sOptionToAdd
        */
       private function addingSenteceClause($bTable,$sWorkSpace){
        $sWhereSentence="";
        $aTableColumns=array("ADD_TAB_NAME","'".$sWorkSpace."'");
        $aProcessColumns=array("COALESCE(S.CON_VALUE,'')","COALESCE(E.CON_VALUE,'')","'".$sWorkSpace."'");
        $aToSort=$bTable?$aTableColumns:$aProcessColumns;
        foreach($aToSort as $sValue){
           if ($sWhereSentence!="")
               $sWhereSentence.=" OR ";
           $sWhereSentence.="$sValue like '%".$_POST['sSearch']."%'";
        }
        
        $sWhereNotIn=isset($_POST['aSelectedData']) ? 
                     "(".$this->notLikeWhere($bTable, $sWorkSpace).") AND ":"";
        $sWhereSentence=" WHERE $sWhereNotIn(".$sWhereSentence.")";
        return $sWhereSentence;
       }
       /**
        * 
        * @param type $iKey
        * @return type
        */
       private function putColsInSingleArray($iKey){
           $returningArray;
           foreach(json_decode($_POST['aSelectedData'],true) as $aRow){
               $returningArray[]="'".$aRow[$iKey]."'";
           }
           return $returningArray;
       }
       /**
        * 
        * @param type $bTable
        * @param type $sWorkSpace
        * @return string
        */
       private function notLikeWhere($bTable,$sWorkSpace){
           $sNotLikeWhere="";
           $aTableColumns=array("'".$sWorkSpace."'","ADD_TAB_NAME");
           $aProcessColumns=array("'".$sWorkSpace."'","S.CON_ID");
           $aToSort=$bTable?$aTableColumns:$aProcessColumns;
           foreach($aToSort as $iKey=>$sValue){
               if ($sNotLikeWhere!="")
                   $sNotLikeWhere.=" OR ";
               $sNotLikeWhere.="$sValue NOT IN (".implode(",",$this->putColsInSingleArray($iKey)).")";
           }    
           return $sNotLikeWhere;
           
       }
       /**
        * 
        * @param type $aToValidate
        */
       private function searchFilter($aToValidate){
           $aResult=array();
           foreach($aToValidate as $value){
               if (preg_match("/".$_POST['sSearch']."/",$value["value"])===1){
                   $aResult[]=array("id"=>$value["id"],"value"=>$value["value"]);
               }
           }
           return array($aResult);
       }
       /**
        * 
        */
       public function sendDataToClient($bIsTable){
       $numRows=$this->numOfRows($bIsTable);
        $output = array(
		"sEcho" => intval($_POST['sEcho']),
		"iTotalRecords" => $numRows,
		"iTotalDisplayRecords" => $numRows,
		"aaData" => $this->dataLimit($bIsTable)
	);
        print json_encode($output);
       }
       /**
        * 
        * 
        */
       private function sortArray(&$aArray,$sOrientation){
           uasort($aArray, "strcasecmp");
           if ($sOrientation=="desc")
               $aArray=array_reverse ($aArray);
       }
       private function isValueBaned($sValue){
           if (!isset($_POST['aSelectedData']))
               return false;
           foreach(json_decode($_POST['aSelectedData'],true) as $aRow ){
               if (in_array($sValue,$aRow))
                return true;
           }
           return false;
       }
       public function sendWorkspacesToClient(){
           $aWorkSpaces = $this->getAlistOfWorkSpaces();
           $this->sortArray($aWorkSpaces,$_POST['sSortDir_0']);
           $numRows=count($aWorkSpaces)-1;
           $aFormatWorkSpaces=array();
           foreach($aWorkSpaces as $workSpace){
               if (!$this->isValueBaned($workSpace))
                 $aFormatWorkSpaces[]=array($workSpace,$workSpace);
           }
          
           $output = array(
               "sEcho" => intval($_POST['sEcho']),
               "iTotalRecords"=>$numRows,
               "iTotalDisplayRecords"=>$numRows,
               "aaData"=>array_slice($aFormatWorkSpaces,$_POST['iDisplayStart'],$_POST['iDisplayLength'])
           );
           print json_encode($output);
       }
   }
 ?>