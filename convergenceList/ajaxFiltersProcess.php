<?php
#####################################################################
# @@ ajaxFiltersProcess.php
# @@ Recent change : April 10
#####################################################################

##### Headers
G::LoadClass ( 'configuration' );
G::loadClass ( 'pmFunctions' );

##### End Headers

$start = isset ( $_POST ['start'] ) ? $_POST ['start'] : 0;
$limit = isset ( $_POST ['limit'] ) ? $_POST ['limit'] : 200000;
if(isset($_GET['Type']) && $_GET['Type'] == 'UserCombo'){	
	$sQuery = "SELECT '1' AS USR_UID, ' All Users' AS USER 
              UNION
              SELECT DISTINCT USERS.USR_UID, CONCAT(USERS.USR_LASTNAME, ' ',USERS.USR_FIRSTNAME) AS USER
   					  FROM USERS
   					  INNER JOIN GROUP_USER ON (GROUP_USER.USR_UID = USERS.USR_UID)
   					  ORDER BY USER";
	$aDatos = executeQuery ( $sQuery );
}

##### Filters (Search Simple - Suggest)

if (isset($_REQUEST['Type']) && $_REQUEST['Type'] == 'custom'){  
  try {              
    $input      = isset($_REQUEST['query'])?strtoupper(trim($_REQUEST['query'])):'';
    $fieldName  = isset($_REQUEST['fieldName'])?trim($_REQUEST['fieldName']):'';
    $idTable    = isset($_REQUEST['idTable'])?trim($_REQUEST['idTable']):'';
    $idInbox    = isset($_REQUEST['idInbox'])?trim($_REQUEST['idInbox']):'';
    $sRol       = getRolUser();

    ##### Section Where

    $sWhere='WHERE 1';
  /*  $sqlGeneralwhere = "SELECT * FROM PMT_INBOX_WHERE WHERE IWHERE_ROL_CODE ='$sRol' AND IWHERE_IID_INBOX = '$idInbox' ";
    $resultGeneralwhere = executeQuery($sqlGeneralwhere);
    if(sizeof($resultGeneralwhere)){
      foreach ($resultGeneralwhere as $key => $value) {
          $sWhere.=' '.$value['IWHERE_QUERY'];
      } 
    }*/
     $sWhere.=' '.getSqlWhere($idInbox); 
    ##### End Section Where

    #### Get JOINS 

    $sSQL = " SELECT JOIN_QUERY FROM PMT_INBOX_JOIN WHERE JOIN_ROL_CODE ='$sRol' AND JOIN_ID_INBOX = '$idInbox'";
    $aData = executeQuery ($sSQL);
    if(is_array($aData) && count($aData) >0 && isset($aData[1]['JOIN_QUERY'])){
      $sJoins = $aData[1]['JOIN_QUERY'];
    }
    #### End Get JOINS
    
    ##### Options select query
	$contSelect = 1;
	$fieldSelect = "SELECT QUERY_SELECT FROM PMT_INBOX_FIELDS_SELECT WHERE ID_INBOX = '".$idInbox ."' AND ROL_CODE ='".$sRol."' AND FIELD_NAME = '".$fieldName."'  ";
	$datafieldSelect = executeQuery($fieldSelect);
	$fieldSelectQuery = '';
	$totSelectQuery = sizeof($datafieldSelect);
	if(sizeof($datafieldSelect))
	{
		foreach($datafieldSelect as $index)
		{
			if($contSelect == 1)
				$fieldSelectQuery = $index['QUERY_SELECT'];
			else
				$fieldSelectQuery = $fieldSelectQuery.', '.$index['QUERY_SELECT'];
			$contSelect++;
		} 
		$newNameFielData = explode('AS',$fieldSelectQuery);
		$newNameField1 = trim($newNameFielData[0]);
		if(sizeof($newNameFielData) <= 1)
		{
			$newNameFielData = explode('as',$fieldSelectQuery);
			$newNameField2 = trim($newNameFielData[0]);
			$newNameField = $newNameField2;
			$fieldName = trim($newNameFielData[1]);
		}
		else 
		{
			$newNameField = $newNameField1;
			$fieldName = trim($newNameFielData[1]);
		}
	}
	
	##### End options select query
	
    $tableAlias = getAliasTable($idInbox,$fieldName);
  	if(strlen(trim($input))>0 && $tableAlias != '' && sizeof($datafieldSelect) == '') {
      $sWhere .= getQueryForSimpleSearch($idInbox,$fieldName, trim($input),false);
  	}
    else {
    	$sWhere .= getQueryForSimpleSearch($idInbox,$fieldName, trim($input),false);
    }
    if($tableAlias != '' && sizeof($datafieldSelect) == '')
    	 $sSQL = "SELECT DISTINCT $tableAlias.$fieldName AS 'ID',$tableAlias.$fieldName AS 'DESCRIPTION' FROM $idTable $sJoins  $sWhere  ORDER BY  DESCRIPTION LIMIT 20";
    else 
    	 $sSQL = "SELECT DISTINCT $newNameField AS 'ID',$newNameField AS 'DESCRIPTION' FROM $idTable $sJoins  $sWhere  ORDER BY  DESCRIPTION LIMIT 20";
    $aDatos = executeQuery ($sSQL);
  } catch(Exception $e){
    $err = $e->getMessage();
    $err = preg_replace("[\n|\r|\n\r]", ' ', $err);
    echo '{"success":false,"total":0, "message":"'.$err.'","data":null}';die;
  }

}
##### End Filters (Search Simple - Suggest)

$array = Array ();
foreach ( $aDatos as $valor ) {
	$array [] = $valor;
}
$total = count ( $aDatos );
header ( "Content-Type: text/plain" );
$paging = array ('success' => true, 'total' => $total, 'data' => array_splice ( $array, $start, $limit ) );
echo json_encode ( $paging );
?>
