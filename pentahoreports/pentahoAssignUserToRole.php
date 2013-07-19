<?php
/**
 * @section Filename
 * pentahoAssignUserToRole.php
 * @subsection Description
 * rendering a paged table with the roles list in order to asign a user to a role.
 * @author Gustavo Cruz <gustavo@colosa.com> 
 * @subsection copyright
 * Copyright (C) 2004 - 2010 Colosa Inc.23
 * <hr>
 * @package plugins.pentahoreports.scripts
 */
   /**
    * The global RBAC object
    */
    global $RBAC;
    switch ($RBAC->userCanAccess('PM_USERS')) {
        case - 2:
            G::SendTemporalMessage('ID_USER_HAVENT_RIGHTS_SYSTEM', 'error', 'labels');
            G::header('location: ../login/login');
            die;
        break;
        case - 1:
            G::SendTemporalMessage('ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels');
            G::header('location: ../login/login');
            die;
        break;
        case - 3:
            G::SendTemporalMessage('ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels');
            G::header('location: ../login/login');
            die;
        break;
	}
	
  require_once ("classes/model/PhRole.php");
  G::LoadClass('ArrayPeer');

  /**
   * The role object
   */
  $role = new PhRole();

  /**
   * The array of all the existent roles
   */
  $aRoles = $role->getAllRoles();
  
  /**
   * setting the array keys
   */
  $fields = Array(
    'ROL_UID'=>'char',
    'ROL_CODE'=>'char',
  );

  /**
   * preparing the list to assign a User to a role
   */
  $rows = array_merge(Array($fields), $aRoles);
  
  /**
   * preparing the DB array in order to populate the paged table
   */
  global $_DBArray;

  /**
   * Setting up the DB array
   */
  $_DBArray['pentaho_roles'] = $rows;

  /**
   * Setting up the criteria object
   */
  $oCriteria = new Criteria('dbarray');
  $oCriteria->setDBArrayTable('pentaho_roles');

  /**
   * Creating the new Publisher object
   */
  $G_PUBLISH = new Publisher;
  
  $G_PUBLISH->AddContent('propeltable', 'paged-table', 'pentahoreports/pentahoAssignUserToRole', $oCriteria);
  /**
   * rendering the content of the paged table
   */
  G::RenderPage('publishBlank','blank');
?>



