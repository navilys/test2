<?php
/**
 * @section Filename
 * pentahoreportsList.php 
 * @subsection Description
 * This script renders the report list and also the view panel container.
 * @author Gustavo Cruz <gustavo@colosa.com> 
 * @subsection copyright
 * Copyright (C) 2004 - 2010 Colosa Inc.23
 * <hr>
 * @package plugins.pentahoreports.scripts
 */

  if (($RBAC_Response = $RBAC->userCanAccess("PM_CASES"))!=1) return $RBAC_Response;

  /**
   * The main menu global variable
   */
  $G_MAIN_MENU            = 'processmaker';

  /**
   * The main menu selected item
   */
  $G_ID_MENU_SELECTED     = 'ID_PENTAHOREPORTS';
  
  /**
   * The sub menu global variable
   */
  $G_SUB_MENU             = ''; 

  /**
   * The sub menu selected item
   */
  $G_ID_SUB_MENU_SELECTED = 'ID_PENTAHOREPORTSLIST';

  /**
   * The publisher global object
   */
  $G_PUBLISH = new Publisher;
  $G_PUBLISH->AddContent('view', 'pentahoreports/cases_Load');
  /**
   * rendering the view
   */
  G::RenderPage('publish');

  
