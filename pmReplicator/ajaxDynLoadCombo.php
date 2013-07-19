<?php
    require_once(PATH_PLUGINS.'pmReplicator'.PATH_SEP.'classes'.PATH_SEP.'class.tclAuxiliary.php');
    require_once(PATH_PLUGINS.'pmReplicator'.PATH_SEP.'classes'.PATH_SEP.'class.tclConnectionManagement.php');
    require_once(PATH_PLUGINS.'pmReplicator'.PATH_SEP.'classes'.PATH_SEP.'class.tclProcessReplicator.php');
    switch($_POST["sLoad"]){
        case "workspace":
            getAlistOfWrokSpaces();
            break;
        case "process":
            getProcessArray();
            break;
    }
      
    function getAlistOfWrokSpaces(){
           $oWorkspaces=new tclManageWorkSpaces();
           $aWorkspaces=$oWorkspaces->getWorkSpaces();
            uasort($aWorkspaces, "strcasecmp");
           $aWorkspaces=  customArray($aWorkspaces);
            print json_encode($aWorkspaces);
    }
    function getProcessQuery($sWorkSpace){
           $sQuery="SELECT 
                                      S.CON_ID,COALESCE(S.CON_VALUE,E.CON_VALUE,'Label not found') as CON_VALUE
                               FROM 
                                       wf_".$sWorkSpace.".PROCESS P 
                               LEFT JOIN
                                       wf_".$sWorkSpace.".CONTENT S
                               ON
                                       (P.PRO_UID=S.CON_ID AND S.CON_LANG='".SYS_LANG."' AND S.CON_CATEGORY='PRO_TITLE')
                               LEFT JOIN
                                       wf_".$sWorkSpace.".CONTENT E
                               ON
                                       (P.PRO_UID=E.CON_ID AND E.CON_LANG='en' AND E.CON_CATEGORY='PRO_TITLE')";
           return $sQuery; 
    }
    function getProcessArray(){
        $oUtility = new tclAuxiliary();
        $cResult = $oUtility->GetHashAdminConfiguration();
        $oCnn = new tclConnectionManagement();
        $oCnn->stablishNewConnection($cResult[0],$cResult[1],$cResult[2]);
        $aRows=$oCnn->executeQuery(getProcessQuery($_POST["sWorkspace"]));
        print json_encode(customArray($aRows));
    }
    function customArray($aArray){
        $newArray;
           foreach ($aArray as $row){
               if ($_POST["sLoad"]=="process")
                  $newArray[$row["CON_ID"]]=$row["CON_VALUE"];
               else
                  $newArray[$row]=$row;
           }
        return $newArray;
    }
?>
