<?php
/**
  @mainpage Overview
  @dontinclude overview.php

  @section Introduction

  ProcessMaker Monitor Plugin es una extension que permite monitorear ProcessMaker, este plugin
  puede ser instalado en un servidor de ProcessMaker.
  
  @subpage PAGE_INSTALLATION
  - @ref SEC_INSTALLATION_PMMONITOR_OPTION
  
  @subpage PAGE_CONFIGURATION
  - @ref SEC_CONFIGURATION_ENV_INI
  - @ref SEC_CONFIGURATION_EACCELERATOR

  @subpage PAGE_REQUERIMENTS
  - @ref SEC_PROCESSMAKER_VERSION

  @subpage PAGE_HOW_WORKS
  - @ref SEC_HOW_PLUGIN
  - @ref SEC_HOW_CONNECTS
  - @ref SEC_HOW_AUTOREGISTER
  - @ref SEC_HOW_SYNCHRONIZE
  - @ref SEC_MAIN_CLASSES
  - @ref SEC_SETUP_DEV
*/

/**
  @page PAGE_INSTALLATION Installation and Configuration
  
  - Import the ProcessMaker Monitor plugin on the ADMIN tab. 
  - Enabled the Plugin.
  
  @section SEC_INSTALLATION_PMMONITOR_OPTION ProcessMaker Monitor Option
  
  Una vez habilitado el plugin se agregara una opcion en el menu ADMIN > Plugins.
  
  @image html AdminPmMonitor.png "ProcessMaker Monitor Option"
*/

/**
  @page PAGE_CONFIGURATION Configuration
  
  @section SEC_CONFIGURATION_ENV_INI Creating and configurating "env.ini" file
  
  Se debe configurar el archivo "env.ini" ubicado en "<INSTALL-DIRECTORY>processmaker/workflow/engine/config" (en caso
  de no existir este archivo, se debe crear), con la siguiente informacion:
  
  ;-------------------------\n
  debug = 1\n
  debug_sql = 1\n
  memcached = 1\n
  ;-------------------------\n
  
  @section SEC_CONFIGURATION_EACCELERATOR eAccelerator configuration
  
  La pestaña "eAccelerator" estara habilitada si se tiene instalada y configurada esta aplicacion.
  
  @image html PmMonitorEAccelerator.png "eAccelerator"
  
  Se debe configurar el archivo "eaccelerator.ini", con la siguiente informacion:
  
  ;-------------------------\n
  eaccelerator.allowed_admin_path="<INSTALL-DIRECTORY>processmaker/workflow/engine/plugins/pmMonitor/eAcceleratorAjax.php"\n
  ;-------------------------\n
*/

/**
  @page PAGE_REQUERIMENTS Requeriments
  
  @section SEC_PROCESSMAKER_VERSION ProcessMaker Requirements

  ProcessMaker V 2.0.37 and later.
*/

/**
  @page PAGE_HOW_WORKS How the plugin works
  @section SEC_HOW_PLUGIN Plugin Architecture
  Plugins allow additional functionality and interface customization to be added to ProcessMaker.

  The main aim for plug-ins is to add new features to the ProcessMaker

  @subsection SUB_CLASSES Plugin and PluginRegistry classes
  A plugin in ProcessMaker is basically a class descendent of class Plugin and PluginRegistry in ProcessMaker core.

  There are two classes in the Core to allow plugins to work, the plugin and pluginRegistry class:

  Here a graph to describe the main methods and the relationship between a plugin and the core classes:

  @image html pluginClasses.png "Plugin and PluginRegistry Classes"

  @subsection SUB_PLUGINFOLDERS Plugin Folders
  The files in a plugin are classified in some specific folder, meaning of all folder is described below

  Next image show the folder structure for a plugin

  @image html pluginFolder.png "folder structure"

  @subsection SUB_OWNPLUGINS Creating your own Plugins
  These are the steps to creating the small and simplest pugin using the Gulliver framework.

  1. Enter the directory /<processmaker home>/workflow/engine

  2. Execute ./gulliver new-plugin <name_of_plugin>

     In this step the wizard will create the directories and files needed to run a basic application.
     In this tutorial, we'll create the plugin demo that will change the logo of ProcessMaker.

  3. Enable the plugin in your Workspace

  4. Now we're ready to test.

  @subsection SUB_ADVANCEDPLUGINS Creating  Advanced Plugins
  These are the steps to creating the small and simplest plugin using the Gulliver framework.

  1. Example page and adding options to main menu

  2. Creating Roles, Permissions and Redirect

  3. External Step

  4. Pm Functions

  5. using propel Tables

  @subsection SUB_DEVCYCLE Plugin Development Cycle.

  @image html pluginCycle.png "Develop Cycle for a plugin"

  @section SEC_HOW_CONNECTS How the class connect to server
  The plugin uses the plexcel extension for PHP

  @section SEC_HOW_AUTOREGISTER The autoregister feature
  This works using the method checkAutomaticRegister of RBAC

  @section SEC_HOW_SYNCHRONIZE How the synchronize feature works
  manual setup

  automatic synchronization using the ProcessMaker Cron.

  @section SEC_MAIN_CLASSES Main Classes
  main classes are

  @section SEC_SETUP_DEV Setup a Development workspace
  In order to set up a Development Environment with the Pentaho Connector Plugin is necessary
  remember to work in a separate workspace in order to avoid conflicts between you and other developers,
  because not all of your coworkers have this plugin installed in theirs working sites.

  @subsection The Setting Up
  Another good development practice can be separate the plugins repository from the main
  ProcessMaker environment, to accomplish this, a couple of symbolic links can be added in
  the main plugin folder, those are linked to the external plugin folder, for example:\n
  
  <code>
  ln -s /<plugins_path>/pentahoreports/pentahoreports/ /<processmaker_path>/workflow/engine/plugins/\n
  ln -s /<plugins_path>/pentahoreports/pentahoreports.php /<processmaker_path>/workflow/engine/plugins/\n
  </code>
*/