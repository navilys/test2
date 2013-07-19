<?php

  require_once 'classes/model/Step.php';
  require_once ( "classes/model/OutputDocument.php" );
  require_once ( "classes/model/Content.php" );
  
  //get the step row
  $oCriteria = new Criteria ( 'workflow' );
  $oCriteria->add ( StepPeer::STEP_UID, $_GET['STP_UID'] );
  $oDataset = StepPeer::doSelectRS ( $oCriteria );
  $oDataset->setFetchmode ( ResultSet::FETCHMODE_ASSOC );
  $oDataset->next ();
  $aRow = $oDataset->getRow ();
  
  
  $oCriteria = new Criteria('workflow');

  $oCriteria->addSelectColumn( OutputDocumentPeer::OUT_DOC_UID );
  $oCriteria->addSelectColumn( OutputDocumentPeer::OUT_DOC_GENERATE );
  $oCriteria->addSelectColumn( ContentPeer::CON_VALUE );
  $oCriteria->addJoin( OutputDocumentPeer::OUT_DOC_UID, ContentPeer::CON_ID, Criteria::LEFT_JOIN);
  $oCriteria->add( ContentPeer::CON_CATEGORY, 'OUT_DOC_TITLE' , Criteria::EQUAL );
  $oCriteria->add( OutputDocumentPeer::PRO_UID, $aRow['PRO_UID'] , Criteria::EQUAL );
  
  $oDataset = OutputDocumentPeer::doSelectRS( $oCriteria );
  $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
  $oDataset->next();
  
  $aDoc = array(); 
  $aDoc[] = Array('id'=>'char', 'name'=>'char');

  while ($oRow = $oDataset->getRow()){
      $aDoc[] = Array('id'=>$oRow['OUT_DOC_UID'], 'name'=>$oRow['CON_VALUE']);
      $oDataset->next();
  }

  global $_DBArray;
  $_DBArray['docList']   = $aDoc;
  $_SESSION['_DBArray'] = $_DBArray;
  G::LoadClass('ArrayPeer');
  $oCriteria = new Criteria('dbarray');
  $oCriteria->setDBArrayTable('docList');

    
  require_once ( PATH_PLUGINS . 'elock' . PATH_SEP . 'class.elock.php');
  $pluginObj = new elockClass ();

  require_once ( "classes/model/ElockOutputCfg.php" );
  //if exists the row in the database propel will update it, otherwise will insert.
  $pk = $aRow['STEP_UID'];
  $tr = ElockOutputCfgPeer::retrieveByPK($pk);
  
  if ( ( is_object ( $tr ) &&  get_class ($tr) == 'ElockOutputCfg' ) ) {
    $fields = array();
    $tarray = array();
    //$fields['SIGNERS_GRID'] = unserialize($tr->getSigSigners());
    //$tarray = unserialize( $tr->getSigSigners() );
    $fields['PRO_UID'] = $tr->getProUid();
    $fields['DOC_UID'] = $tr->getDocUid();
    $fields['STP_UID'] = $pk;
  }
  else{
    $fields = array();
    $fields['PRO_UID'] = $aRow['PRO_UID'];
    $fields['TAS_UID'] = $aRow['TAS_UID'];
    $fields['STP_UID'] = $_GET['STP_UID'];
  }

  $G_MAIN_MENU = 'workflow';
  $G_SUB_MENU = 'sigplusSigners';
  $G_ID_MENU_SELECTED = '';
  $G_ID_SUB_MENU_SELECTED = '';

  $G_PUBLISH = new Publisher;
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'elock/elockOutputCfgEdit', '', $fields, '../elock/elockOutputCfg/elockOutputCfgSave' );
  G::RenderPage('publish','raw');
?>