<?php
/**
 * @section Filename
 * main_init.php
 * @subsection Description
 * this scripts render the main_init.html template
 * which is in charge to load the panels and the construccion pf the interface for the report list.
 * @author Gustavo Cruz <gustavo@colosa.com>
 * @subsection Copyright
 * Copyright (C) Colosa Development Team 2010
 * <hr>
 * @package plugins.pentahoreports.scripts
 */

/**
 * The default icon size
 */
  $_ICON_SIZE = '18';

 /**
  * Calling the Rbac global variable
  */
  global $RBAC;
  // selecting the template system
  G::LoadSystem('templatePower');

 /**
  * Creating the new template
  */
  $tpl = new TemplatePower(PATH_PLUGINS ."pentahoreports/main_init.html");
  $tpl->prepare();

 /**
  * Assigning the variables into the template
  */
  $tpl->assign ( 'SYS_SKIN', SYS_SKIN );
  $tpl->assign ( 'ICON_SIZE', 18 );

 /**
  * Initializing the publisher global variable
  */
  $G_PUBLISH = new Publisher;
  /**
   * adding the template content
   */
  $G_PUBLISH->AddContent('template', '', '', '', $tpl );
  /**
   * Publishing the view
   */
  G::RenderPage('publish', 'raw');
