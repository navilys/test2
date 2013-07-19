<?php
/**
 * documentShow.php
 *  
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.23
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd., 
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 * 
 */
/*
 * Created on 13-02-2008
 *
 * @author David Callizaya <davidsantos@colosa.com>
 */

  try {
  //require_once ( "classes/model/AppDocumentPeer.php" );
    include ( 'class.knowledgeTree.php' );
    $kt = new KnowledgeTreeClass();
    $kt->readConfig();
if(!isset($_GET['kta'])){
    $kt_document = KtDocumentPeer::retrieveByPk( $_GET['a'],$_GET['t'] );
    
  //$oAppDocument = new AppDocument();
  //$oAppDocument->Fields = $oAppDocument->load($_GET['a']);

  //$sAppDocUid = $oAppDocument->getAppDocUid();
    $sAppDocUid = $kt_document->getDocUid();
  //$info = pathinfo( $oAppDocument->getAppDocFilename() );
    $info = pathinfo( $kt_document->getKtDocumentTitle() );
    $ext = $info['extension'];
}

    $response = $kt->start_session();
    if (PEAR::isError($response) ) {
  	  print $response->getMessage(); 	exit;
    }

  $docId = $_GET['b'];
  $sFilename = $kt->downloadDocument( $docId );
  
  $realPath = PATH_DOCUMENT . $sFilename;
  if ( file_exists ( $realPath ) )
      if(!isset($_GET['kta'])){
        G::streamFile ( $realPath, true, $kt_document->getKtDocumentTitle() );
      }else{
        G::streamFile ( $realPath, true, $_GET['kta'] );
      }
  else
  	throw ( new Exception ( 'Error downloading file from KnowledgeTree.' ) );
  

}
catch ( Exception $e ){
  $G_MAIN_MENU            = 'processmaker';
  $G_ID_MENU_SELECTED     = 'CASES';
  $G_SUB_MENU             = 'caseOptions';
  $G_ID_SUB_MENU_SELECTED = '_';
   
    $G_PUBLISH = new Publisher;
  	$aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
    G::RenderPage('publish');
}