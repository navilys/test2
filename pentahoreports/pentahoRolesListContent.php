<?php
/**
 * @section Filename
 * pentahoRolesListContent.php
 * @subsection Description
 * Container that renders the roles interface
 * 
 * @author gustavo cruz <gustavo@colosa.com>
 * @Date 20/05/2010
 * @subsection Copyright
 * Copyright (C) 2004 - 2010 Colosa Inc.23
 * <hr>
 * @package plugins.pentahoreports.scripts
 */

   /**
    * The RBAC global variable that checks the permissions of the currenmt logged user
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
	
?>

<span id="rolesContainer">
</span>

