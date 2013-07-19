<?php

//if (($RBAC_Response=$RBAC->userCanAccess("PM_USERS"))!=1) return $RBAC_Response;
G::LoadInclude('ajax');
$_POST['action'] = get_ajax_value('action');

require_once ( "class.knowledgeTree.php" );
$KnowledgeTreeClass = new KnowledgeTreeClass( );

switch ($_POST['action'])
{

	case 'toggleFolder':
	    $WIDTH_PANEL = 350;
	    G::LoadClass('tree');





//$searchResults=$KnowledgeTreeClass->kt_search("desarrollo","");
//krumo($searchResults);

//krumo($KnowledgeTreeClass);
//$folderContent=$KnowledgeTreeClass->kt_get_listing("ROOT",1,"F");//F=Folders
$rootFolder="ROOT";
$rootFolder=1;
$folderContent=$KnowledgeTreeClass->kt_get_listing($_POST['folderID']!=0?$_POST['folderID']:$rootFolder,1,"F");
//krumo($folderContent);
if(!is_array($folderContent)){
if (isset($_SESSION['G_MESSAGE_TYPE']) && isset($_SESSION['G_MESSAGE'])) {
    $messageTypes=array("TMP-INFO","INFO","TMP-WARNING", "WARNING", "TMP-ERROR", "ERROR");
    
    if(in_array(strtoupper($_SESSION['G_MESSAGE_TYPE']),$messageTypes)){
        $msgType=strtoupper($_SESSION['G_MESSAGE_TYPE']);
    }else{
        $msgType="WARNING";
    }

    $timeToHideTmpMsg = "";
    if( substr($msgType,0,3) == 'TMP'){
        $timeToHideTmpMsg = "5";
        $msgType = str_replace('TMP-', '', $msgType);
    }

    echo('<table width="65%" cellpadding="5" cellspacing="0" border="0">');
    echo('<tr><td id="temporalMessageTD" class="temporalMessage'.$msgType.'" align="center"><div id="temporalMessage'.$msgType.'"><strong>' . G::capitalize($msgType) . '</strong> : ' . $_SESSION['G_MESSAGE'] . '</div></td></tr>');
	  echo('</table><script>PMOS_TemporalMessage('.$timeToHideTmpMsg.')</script>');

    unset($_SESSION['G_MESSAGE_TYPE']);
    unset($_SESSION['G_MESSAGE']);
}
 echo $folderContent;exit;
}


  $tree = new Tree();
  $tree->height = ((isset($_REQUEST['screenHigh'])) ? $_REQUEST['screenHigh'] * 0.55 : 350 ) . 'px';
  $tree->name = 'DMS';
  $tree->nodeType="blank";
  if($_POST['folderID']==0){
  $tree->nodeType="base";
    }
  //$tree->width="350px";
  $tree->value = '';
  $tree->showSign=false;


  $i=0;

  foreach($folderContent['list'] as $key => $obj)  {
  $i++;
      //if($obj->item_type=="F"){
      if($obj->item_type=="F"){
      $RowClass = ($i%2==0)? 'Row1': 'Row2';


  $htmlGroup   = <<<GHTML
	<table cellspacing='0' cellpadding='0' border='1' style='border:0px;' width="100%" class="pagedTable">
	<tr id="{$i}" onclick="focusRow(this, 'Selected')" onmouseout="setRowClass(this, '{$RowClass}')" onmouseover="setRowClass(this, 'RowPointer' )" class="{$RowClass}">
	<td width='' class='treeNode' style='border:0px;background-color:transparent;'><a href="#" onclick="kt_toggleFolder('{$obj->id}');return false;"><img id="{$obj->id}" src="/plugin/knowledgeTree/images/plus.gif" border = "0" valign="middle" /><img src="/plugin/knowledgeTree/images/folderV2.gif" border = "0" valign="middle" />&nbsp;{$obj->title}</a>&nbsp;<small>{$obj->created_by}</small></td>
	<!-- <td class='treeNode' style='border:0px;background-color:transparent;'> Created by: {$obj->created_by}</td> -->
	<!-- <td class='treeNode' style='border:0px;background-color:transparent;'> Perm: {$obj->permissions}</td> -->
	<!-- <td class='treeNode' style='border:0px;background-color:transparent;'>[<a href="#" onclick="document.getElementById('caseToStart'+'{$i}').submit();return false;">New</a>]</td>-->
	<!--<td class='treeNode' style='border:0px;background-color:transparent;'>[<a href="#" onclick="alert(document.getElementById('caseToStart'+'{$i}').name);return false;">New</a>]</td>-->
	</tr>
	</table>
	<div id="child_{$obj->id}"></div>
GHTML;


$ch =& $tree->addChild($key, $htmlGroup, array('nodeType'=>'child'));
  $ch->point = ' ';

      }
}
if($i!=0){
    print( $tree->render() );
}

	break;
	case 'getFolderContent':


        //krumo($KnowledgeTreeClass);
        //$folderContent=$KnowledgeTreeClass->kt_get_listing("ROOT",1,"F");//F=Folders
        $folderContent=$KnowledgeTreeClass->kt_get_listing($_POST['folderID']!=0?$_POST['folderID']:"1",1,"D");
	   // G::pr($folderContent);
        if(!is_array($folderContent)){
if (isset($_SESSION['G_MESSAGE_TYPE']) && isset($_SESSION['G_MESSAGE'])) {
    $messageTypes=array("TMP-INFO","INFO","TMP-WARNING", "WARNING", "TMP-ERROR", "ERROR");
    
    if(in_array(strtoupper($_SESSION['G_MESSAGE_TYPE']),$messageTypes)){
        $msgType=strtoupper($_SESSION['G_MESSAGE_TYPE']);
    }else{
        $msgType="WARNING";
    }

    $timeToHideTmpMsg = "";
    if( substr($msgType,0,3) == 'TMP'){
        $timeToHideTmpMsg = "5";
        $msgType = str_replace('TMP-', '', $msgType);
    }

    echo('<table width="65%" cellpadding="5" cellspacing="0" border="0">');
    echo('<tr><td id="temporalMessageTD" class="temporalMessage'.$msgType.'" align="center"><div id="temporalMessage'.$msgType.'"><strong>' . G::capitalize($msgType) . '</strong> : ' . $_SESSION['G_MESSAGE'] . '</div></td></tr>');
	  echo('</table><script>PMOS_TemporalMessage('.$timeToHideTmpMsg.')</script>');

    unset($_SESSION['G_MESSAGE_TYPE']);
    unset($_SESSION['G_MESSAGE']);
}
         echo $folderContent;exit;
        }
        $items[] = array ( 'id' => 'char');
        foreach($folderContent['list'] as $key => $obj)  {
          //G::pr($obj);
            $items[] = array (
                'item_type'           => $obj->item_type,
                'custom_document_no'  => $obj->custom_document_no,
                'oem_document_no'     => $obj->oem_document_no,
                'title'               => $obj->title,
                'document_type'       => $obj->document_type,
                'filename'            => $obj->filename,
                'filesize'            => $obj->filesize,
                'created_by'          => $obj->created_by,
                'created_date'        => $obj->created_date,
                'checked_out_by'      => $obj->checked_out_by,
                'checked_out_date'    => $obj->checked_out_date,
                'modified_by'         => $obj->modified_by,
                'modified_date'       => $obj->modified_date,
                'owned_by'            => $obj->owned_by,
                'version'             => $obj->version,
                'is_immutable'        => $obj->is_immutable,
                'permissions'         => $obj->permissions,
                'workflow'            => $obj->workflow,
                'workflow_state'      => $obj->workflow_state,
                'mime_type'           => $obj->mime_type,
                'mime_icon_path'      => $obj->mime_icon_path,
                'mime_display'        => $obj->mime_display,
                'storage_path'        => $obj->storage_path,
            	'downloadScript'      => "../knowledgeTree/documentShow?kta=".$obj->title."&b=".$obj->id."&t=".$obj->type."&r=".rand( 1000, 10000),
                //'downloadScript'      => "../knowledgeTree/services/documentShow?a=" . $obj->filename . "&b=" . $obj->id . "&t=" . $obj->type . "&r=" . rand(1000, 10000)
    	);
        }

        $_DBArray['KT_LIST'] = $items;
          $_SESSION['_DBArray'] = $_DBArray;

          G::LoadClass( 'ArrayPeer');
          $c = new Criteria ('dbarray');
          $c->setDBArrayTable('KT_LIST');
          $c->addAscendingOrderByColumn ('id');
        $G_PUBLISH = new Publisher;

  $G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'knowledgeTree/ktdocumentList', $c );

  G::RenderPage('publish','raw');


	break;
	case "ktDmsConf":

        $G_PUBLISH = new Publisher;
        $G_PUBLISH->AddContent('view', 'knowledgeTree/documentTypes_Tree' );
        $G_PUBLISH->AddContent('smarty', 'knowledgeTree/documentTypeList', '', '', array());
        G::RenderPage( "publish-treeview","raw" );
	break;
	case "showDocumentTypeAssigned":





       /* $KnowledgeTreeClass = new KnowledgeTreeClass( );
        //krumo($KnowledgeTreeClass);
        //$folderContent=$KnowledgeTreeClass->kt_get_listing("ROOT",1,"F");//F=Folders
        $folderContent=$KnowledgeTreeClass->kt_get_listing($_POST['folderID']!=0?$_POST['folderID']:"ROOT",1,"D");
	    //krumo($folderContent);
        if(!is_array($folderContent)){

         echo $folderContent;exit;
        }
        $items[] = array ( 'id' => 'char');
        foreach($folderContent['list'] as $key => $obj)  {
            $items[] = array (
                'item_type'           => $obj->item_type,
                'custom_document_no'  => $obj->custom_document_no,
                'oem_document_no'     => $obj->oem_document_no,
                'title'               => $obj->title,
                'document_type'       => $obj->document_type,
                'filename'            => $obj->filename,
                'filesize'            => $obj->filesize,
                'created_by'          => $obj->created_by,
                'created_date'        => $obj->created_date,
                'checked_out_by'      => $obj->checked_out_by,
                'checked_out_date'    => $obj->checked_out_date,
                'modified_by'         => $obj->modified_by,
                'modified_date'       => $obj->modified_date,
                'owned_by'            => $obj->owned_by,
                'version'             => $obj->version,
                'is_immutable'        => $obj->is_immutable,
                'permissions'         => $obj->permissions,
                'workflow'            => $obj->workflow,
                'workflow_state'      => $obj->workflow_state,
                'mime_type'           => $obj->mime_type,
                'mime_icon_path'      => $obj->mime_icon_path,
                'mime_display'        => $obj->mime_display,
                'storage_path'        => $obj->storage_path,
            	'downloadScript'      => "../knowledgeTree/documentShow?a=".$obj->filename."&b=".$obj->id."&t=".$obj->type."&r=".rand( 1000, 10000),
    	);
        }

        $_DBArray['KT_LIST'] = $items;
          $_SESSION['_DBArray'] = $_DBArray;

          G::LoadClass( 'ArrayPeer');
          $c = new Criteria ('dbarray');
          $c->setDBArrayTable('KT_LIST');
          $c->addAscendingOrderByColumn ('id');*/

	    $G_PUBLISH = new Publisher;


  require_once ( PATH_PLUGINS . 'knowledgeTree' . PATH_SEP . 'class.knowledgeTree.php');
  $pluginObj = new knowledgeTreeClass ();

  require_once ( "classes/model/KtDocType.php" );
  require_once ( "classes/model/KtFieldsMap.php" );
  require_once ( "classes/model/Content.php" );

  $Criteria = new Criteria('workflow');
  $Criteria->clearSelectColumns ( );

  $Criteria->addSelectColumn (  KtDocTypePeer::PRO_UID );
  $Criteria->addSelectColumn (  KtDocTypePeer::DOC_UID );
  $Criteria->addSelectColumn (  KtDocTypePeer::DOC_KT_TYPE_ID );

  $Criteria->addSelectColumn (  ContentPeer::CON_VALUE );


  $Criteria->addJoin (  KtDocTypePeer::DOC_UID, ContentPeer::CON_ID,Criteria::LEFT_JOIN );

  $Criteria->add (  KtDocTypePeer::PRO_UID, $_SESSION['PROCESS'] , CRITERIA::EQUAL );
  $Criteria->add (  KtDocTypePeer::DOC_KT_TYPE_ID, $_POST['documentType'] , CRITERIA::EQUAL );
  $Criteria->add (  ContentPeer::CON_CATEGORY, array("OUT_DOC_TITLE","INP_DOC_TITLE") , CRITERIA::IN );
  $Criteria->add (  ContentPeer::CON_LANG, SYS_LANG , CRITERIA::EQUAL );





	    $docTypeFields=$KnowledgeTreeClass->kt_get_documentTypeFields($_POST['documentType']);
	    //krumo($docTypeFields);

	    $destinationPath=$KnowledgeTreeClass->getDestinationPath($_SESSION['PROCESS'],$_POST['documentType']);





	    $G_PUBLISH->AddContent('smarty', 'knowledgeTree/documentTypeHeader', '', '', array('documentType'=>$_POST['documentType'],'destinationPath'=>$destinationPath));
      $G_PUBLISH->AddContent('propeltable', 'paged-table', 'knowledgeTree/ktDocTypeList', $Criteria , array(),'');

      G::RenderPage( "publish","raw" );

	break;

	case "ktAssignDocument":

    	require_once ( PATH_PLUGINS . 'knowledgeTree' . PATH_SEP . 'class.knowledgeTree.php');
      $pluginObj = new knowledgeTreeClass ();

      require_once ( "classes/model/KtDocType.php" );

      $documentsArray = array();
      $documentsArray[] = array('DOC_UID' => 'char', 'DOC_NAME' => 'char');
      G::LoadClass('processMap');
      $oProcessMap = new processMap(new DBConnection);
      G::LoadClass ( 'ArrayPeer' );
  	  $inputDocuments=$oProcessMap->getInputDocumentsCriteria($_SESSION['PROCESS']);
  	  $outputDocuments=$oProcessMap->getOutputDocumentsCriteria($_SESSION['PROCESS']);

  	  $rs = ArrayBasePeer::doSelectRS($inputDocuments);
      $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $rs->next();


      while ( is_array($row = $rs->getRow())) {
        $aFields = array('DOC_UID' => $row['INP_DOC_UID'], 'DOC_NAME' => "INPUT | ".$row['INP_DOC_TITLE']);
        $documentsArray[] = $aFields;

      $rs->next();
      }

      $rs = ArrayBasePeer::doSelectRS($outputDocuments);
      $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $rs->next();


      while ( is_array($row = $rs->getRow())) {
        $aFields = array('DOC_UID' => $row['OUT_DOC_UID'], 'DOC_NAME' => "OUTPUT | ".$row['OUT_DOC_TITLE']);
        $documentsArray[] = $aFields;

      $rs->next();
      }


  	  //krumo($inputDocuments);
  	  //krumo($outputDocuments);



global $_DBArray;
$_DBArray['documentsArray'] = $documentsArray;


$_SESSION['_DBArray'] = $_DBArray;

    $fields['PRO_UID']=$_SESSION['PROCESS'];
    $fields['DOC_KT_TYPE_ID']=$_POST['documentType'];

      $G_PUBLISH = new Publisher;
      $G_PUBLISH->AddContent('xmlform', 'xmlform', 'knowledgeTree/ktDocTypeEdit', '', $fields, 'ktDocTypeSave' );
      G::RenderPage('publish',"raw");

	break;

	case "ktAssignDocumentSave":
	//krumo($_POST);
	$ProUid=$_SESSION['PROCESS'];
	$DocUid=$_POST['documentId'];
	$DocKtTypeId=$_POST['documentType'];
	require_once ( PATH_PLUGINS . 'knowledgeTree' . PATH_SEP . 'class.knowledgeTree.php');
  $pluginObj = new knowledgeTreeClass ();

  require_once ( "classes/model/KtDocType.php" );

     //if exists the row in the database propel will update it, otherwise will insert.
     $tr = KtDocTypePeer::retrieveByPK( $ProUid, $DocUid );

    if ( ! ( is_object ( $tr ) &&  get_class ($tr) == 'KtDocType' ) ) {
       $tr = new KtDocType();
     }
     $tr->setProUid( $ProUid );
     $tr->setDocUid( $DocUid );
     $tr->setDocKtTypeId( $DocKtTypeId );

     if ($tr->validate() ) {
       // we save it, since we get no validation errors, or do whatever else you like.
       $res = $tr->save();
     }
     else {
       // Something went wrong. We can now get the validationFailures and handle them.
       $msg = '';
       $validationFailuresArray = $tr->getValidationFailures();
       foreach($validationFailuresArray as $objValidationFailure) {
         $msg .= $objValidationFailure->getMessage() . "<br/>";
       }
       //return array ( 'codError' => -100, 'rowsAffected' => 0, 'message' => $msg );
     }

	break;
	case "ktDeleteDocumentSave":

	$ProUid = $_SESSION['PROCESS'];
    $DocUid = $_POST['documentId'];

  require_once ( PATH_PLUGINS . 'knowledgeTree' . PATH_SEP . 'class.knowledgeTree.php');
  $pluginObj = new knowledgeTreeClass ();

    require_once ( "classes/model/KtDocType.php" );

    //if exists the row in the database propel will update it, otherwise will insert.
    $tr = KtDocTypePeer::retrieveByPK( $ProUid, $DocUid );
    if ( ( is_object ( $tr ) &&  get_class ($tr) == 'KtDocType' ) ) {
      $tr->delete();
    }

	break;

	case "ktDmsMapFields":
	    G::LoadClass('xmlfield_InputPM');
      $aFields = getDynaformsVars($_SESSION['PROCESS']);
      //krumo($aFields);



      $pmVarsArray = array();
      $pmVarsArray[] = array('VAR_NAME' => 'char', 'VAR_LABEL' => 'char');
      foreach($aFields as $key => $fieldObj){
        if(method_exists('G','getMinText')){
            $label = G::getMinText($fieldObj['sName'],20);
        }
        else{ // TODO: this part will be removed after version 2.0.45 goes release.
            $text = $fieldObj['sName'];
            $maxTextLenght = 20;
            $points = "...";
            $lengthPoints = strlen($points);
            if(strlen($text) > $maxTextLenght){
                $text = substr($text,0,$maxTextLenght - $lengthPoints) . $points;
            }
            $label = $text;
        }
        $aFields1 = array('VAR_NAME' => $fieldObj['sName'], 
                          'VAR_LABEL' => $label . " (".$fieldObj['sLabel'].")");
        $pmVarsArray[] = $aFields1;

      }



        global $_DBArray;
        $_DBArray['pmVarsArray'] = $pmVarsArray;

        $_SESSION['_DBArray'] = $_DBArray;

      //krumo($_SESSION['_DBArray']);






	    $docTypeFields=$KnowledgeTreeClass->kt_get_documentTypeFields($_POST['documentType']);
	    //krumo($docTypeFields);


	    require_once ( PATH_PLUGINS . 'knowledgeTree' . PATH_SEP . 'class.knowledgeTree.php');
      $pluginObj = new knowledgeTreeClass ();

      require_once ( "classes/model/KtFieldsMap.php" );
      //if exists the row in the database propel will update it, otherwise will insert.
      $tr = KtFieldsMapPeer::retrieveByPK( $_POST['documentType'] ,$_SESSION['PROCESS'] );

      if ( ( is_object ( $tr ) &&  get_class ($tr) == 'KtFieldsMap' ) ) {


         $fieldsMap=unserialize($tr->getFieldsMap());
         $destinationPath=$tr->getDestinationPath();

         $varCount=0;
         $fieldsMapArray=array();
         foreach($fieldsMap as $dmsField => $pmVar){
            $varCount++;
            $fieldsMapArray['MAPFIELDS'][$varCount]['PMVAR']=$pmVar;

         }

      }



	    $fieldCount=0;
	    foreach($docTypeFields->metadata as $key => $fieldsetObj){
	        $fieldSetName=$fieldsetObj->fieldset;
	        foreach($fieldsetObj->fields as $key => $fieldObj){
	            $fieldCount++;
	            $fields['MAPFIELDS'][$fieldCount]['DMSFIELDSET']=$fieldSetName;
	            $fields['MAPFIELDS'][$fieldCount]['FIELDNAME']=$fieldObj->name;
	            if(isset($fieldsMapArray['MAPFIELDS'][$fieldCount]['PMVAR'])){
	                $fields['MAPFIELDS'][$fieldCount]['PMVAR'] = $fieldsMapArray['MAPFIELDS'][$fieldCount]['PMVAR'];
	            }
	        }
	    }
	   $fields['COUNT_ELEMENTS'] = $fieldCount;
	   $fields['DOC_KT_TYPE_ID'] = $_POST['documentType'];



	  $G_PUBLISH = new Publisher;
      $G_PUBLISH->AddContent('xmlform', 'xmlform', 'knowledgeTree/ktFieldsMapMain', '', $fields, 'ktDocTypeSave' );
      G::RenderPage('publish',"raw");
	break;
	case "ktMapFieldSave":
      $DocKtTypeId = $_POST['documentType'];
      unset($_POST['documentType']);
      unset($_POST['action']);
      $FieldsMap = serialize($_POST);
      //krumo($_POST);
      //$DestinationPath = $form['DESTINATION_PATH'];

      require_once ( "classes/model/KtFieldsMap.php" );

         //if exists the row in the database propel will update it, otherwise will insert.
         $tr = KtFieldsMapPeer::retrieveByPK( $DocKtTypeId , $_SESSION['PROCESS'] );
         //krumo($tr);
         if ( ! ( is_object ( $tr ) &&  get_class ($tr) == 'KtFieldsMap' ) ) {
           $tr = new KtFieldsMap();
         }

         $tr->setProUid( $_SESSION['PROCESS'] );
         $tr->setDocKtTypeId( $DocKtTypeId );
         $tr->setFieldsMap( $FieldsMap );
         //krumo($FieldsMap);

         if ($tr->validate() ) {
           // we save it, since we get no validation errors, or do whatever else you like.
           $res = $tr->save();
         }
         else {
           // Something went wrong. We can now get the validationFailures and handle them.
           $msg = '';
           $validationFailuresArray = $tr->getValidationFailures();
           foreach($validationFailuresArray as $objValidationFailure) {
             $msg .= $objValidationFailure->getMessage() . "<br/>";
           }
           //krumo($msg);
           //return array ( 'codError' => -100, 'rowsAffected' => 0, 'message' => $msg );
         }

	break;

	case 'ktdestinationPath':
	    $fields['DOC_KT_TYPE_ID']=$_POST['documentType'];
	    $fields['DESTINATION_PATH']=$KnowledgeTreeClass->getDestinationPath($_SESSION['PROCESS'],$_POST['documentType']);
        $fields['KT_PRO_UID']=$_SESSION['PROCESS'];

      $G_PUBLISH = new Publisher;
      $G_PUBLISH->AddContent('xmlform', 'xmlform', 'knowledgeTree/ktDestinationPathEdit', '', $fields, 'ktDocTypeSave' );
      G::RenderPage('publish',"raw");
	break;
	case 'ktDestinationPathSave':

  $DocKtTypeId = $_POST['documentType'];
  $DestinationPath = $_POST['destinationPath'];

  require_once ( PATH_PLUGINS . 'knowledgeTree' . PATH_SEP . 'class.knowledgeTree.php');
  $pluginObj = new knowledgeTreeClass ();

  require_once ( "classes/model/KtFieldsMap.php" );

     //if exists the row in the database propel will update it, otherwise will insert.
     $tr = KtFieldsMapPeer::retrieveByPK( $DocKtTypeId,$_SESSION['PROCESS'] );
     if ( ! ( is_object ( $tr ) &&  get_class ($tr) == 'KtFieldsMap' ) ) {
       $tr = new KtFieldsMap();
     }
     $tr->setProUid( $_SESSION['PROCESS'] );
     $tr->setDocKtTypeId( $DocKtTypeId );
     $tr->setDestinationPath( $DestinationPath );

     if ($tr->validate() ) {
       // we save it, since we get no validation errors, or do whatever else you like.
       $res = $tr->save();
     }
     else {
       // Something went wrong. We can now get the validationFailures and handle them.
       $msg = '';
       $validationFailuresArray = $tr->getValidationFailures();
       foreach($validationFailuresArray as $objValidationFailure) {
         $msg .= $objValidationFailure->getMessage() . "<br/>";
       }
       krumo($msg);
       //return array ( 'codError' => -100, 'rowsAffected' => 0, 'message' => $msg );
     }
	break;
	case 'ktDmsUserConf':

$fields['USR_UID']=$_SESSION['USER_LOGGED'];
$thisStepA=explode(SYS_SKIN."/cases/",$_SERVER['HTTP_REFERER']);
$fields['NEXT_STEP']=$thisStepA[1];
unset($_SESSION['BREAKSTEP']['NEXT_STEP']);
require_once ( "classes/model/KtConfig.php" );
$tr = KtConfigPeer::retrieveByPK( $_SESSION['USER_LOGGED']  );
  
  if ( ( is_object ( $tr ) &&  get_class ($tr) == 'KtConfig' ) ) {      
     $fields['KT_USERNAME'] = $tr->getKtUsername();
     $fields['KT_PASSWORD'] = G::decrypt($tr->getKtPassword(),$tr->getUsrUid());
  }	

      $G_PUBLISH = new Publisher;
      $G_PUBLISH->AddContent('xmlform', 'xmlform', 'knowledgeTree/userConfiguration', '', $fields, '../knowledgeTree/ktConfig/ktConfigSave' );
      G::RenderPage('publish',"raw");
        break;
    echo "default";
    break;

 }
