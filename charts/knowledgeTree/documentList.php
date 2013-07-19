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
  switch ($RBAC->userCanAccess('PM_CASES'))
  {
  	case -2:
  	  G::SendTemporalMessage('ID_USER_HAVENT_RIGHTS_SYSTEM', 'error', 'labels');
  	  G::header('location: ../login/login');
  	  die;
  	break;
  	case -1:
  	  G::SendTemporalMessage('ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels');
  	  G::header('location: ../login/login');
  	  die;
  	break;
  }

try {
  include ( 'class.knowledgeTree.php' );

  
  $kt = new KnowledgeTreeClass();
  $kt->readConfig();
  //$response = $kt->start_session();
  //if (PEAR::isError($response) ) {
  //	print $response->getMessage(); 	exit;
  //}

    // 1 root folder
    // 0 PM Folder
  
  $listing = $kt->getListing( 0 );
  if (PEAR::isError($listing) ) {
  	throw ( new Exception ( $listing->getMessage() ) );
  }

  // lets display the items
  $items[] = array ( 'id' => 'char', 'title' => 'char', 'type' => 'char', 'creator' => 'char' ,
                   'modifiedBy' => 'char', 'filename' => 'char', 'size' => 'char', 'mime' => 'char', 'rand' => 'char');
  if ( is_array ($listing) )
  krumo($listing);
    foreach($listing as $folderitem)
    {
    	$items[] = array ( 
  	           'id'          => $folderitem->id, 
        	     'title'       => $folderitem->title, 
  	           'type'        => $folderitem->item_type == 'D' ? 'Document' : 'Folder', 
      	       'creator'     => isset($folderitem->creator)    ? $folderitem->creator : '', 
      	       'modifiedby'  => isset($folderitem->modifiedby) ? $folderitem->modifiedby : '', 
      	       'filename'    => $folderitem->filename, 
  	           'size'        => isset($folderitem->size)       ? $folderitem->size : '', 
  	           'created_date'=> $folderitem->created_date ,
    	);
    }
  $_DBArray['KT_Listing'] = $items;
  $_SESSION['_DBArray'] = $_DBArray;

  G::LoadClass( 'ArrayPeer');
  $c = new Criteria ('dbarray');
  $c->setDBArrayTable('KT_Listing');
  $c->addAscendingOrderByColumn ('id');

  $G_MAIN_MENU            = 'processmaker';
  $G_ID_MENU_SELECTED     = 'CASES';
  $G_SUB_MENU             = 'cases';
  $G_ID_SUB_MENU_SELECTED = 'KNOWLEDGE_TREE';

  $G_PUBLISH = new Publisher;

  $G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'knowledgeTree/documentList', $c );  
  
  G::RenderPage('publish');
}
catch ( Exception $e ){
    $G_MAIN_MENU            = 'processmaker';
    $G_ID_MENU_SELECTED     = 'SETUP';
    $G_SUB_MENU             = 'setup';
    $G_ID_SUB_MENU_SELECTED = 'PLUGINS';
   
    $G_PUBLISH = new Publisher;
  	$aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
    G::RenderPage('publish');
}
