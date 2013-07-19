<?php

/**
 * @section Filename
 * pentahoRolesList.php
 * @subsection Description
 * This script generates the Roles list for the pentaho connector plugin.
 * @author  gustavo cruz <gustavo@colosa.com>
 * @date    05/06/2010
 * @subsection Copyright
 * Copyright (C) 2004 - 2010 Colosa Inc.23
 * <hr>
 * @package plugins.pentahoreports.scripts
 */

  /**
   * Call to the global RBAC variable
   */
    global $RBAC;

  /**
   * Checking the USER permissions in order to allow or forbid the access
   */
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
	
    require_once ( "classes/model/PhRole.php" );
    G::LoadClass('ArrayPeer');

    /**
     * The role object.
     */
    $role = new PhRole();

    /**
     * The array that stores the all roles list.
     */
    $aRoles = $role->getAllRoles();

    /**
     * Setting up the headers array.
     */	       
    $fields = Array(        
    	'ROL_UID'=>'char', 
    	'ROL_CODE'=>'char',
    );
    /**
     * Setting up the rows array, making a merge of the headers array with the all roles array.
     */
    $rows = array_merge(Array($fields), $aRoles);

    /**
     * The global DB Array.
     */
    global $_DBArray;
    /**
     * The global DB Array that stores the roles list.
     */
    $_DBArray['pentaho_roles'] = $rows;

    /**
     * Assembling a Criteria object with the Db array in order to populate the paged table the type of the criteria will be DB Array.
     */
    $oCriteria = new Criteria('dbarray');
    $oCriteria->setDBArrayTable('pentaho_roles');

    /**
     * Initializing the headers.
     */
    $oHeadPublisher =& headPublisher::getSingleton();
    $oHeadPublisher->addScriptCode(file_get_contents(PATH_PLUGINS . 'pentahoreports/pentahoRolesManager.js'));

    /**
     * Initializing the publisher in order to create the propel table. 
     */
    $G_PUBLISH = new Publisher;
    $G_PUBLISH->AddContent('propeltable', 'paged-table', 'pentahoreports/pentahoRolesList', $oCriteria);
    $G_PUBLISH->AddContent('view', 'pentahoreports/pentahoRolesListContent');

    /**
     * Publishing the page content.
     */
    G::RenderPage('publishBlank','blank');
?>



