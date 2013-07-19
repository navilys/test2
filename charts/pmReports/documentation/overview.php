<?php
/**
  @mainpage Overview
  @dontinclude overview.php

  @section Introduction

  Simple Reporting Plugin es una extension que permite obtener reportes a traves de "Report Tables", este plugin
  puede ser instalado en un servidor de ProcessMaker.
  
  @subpage PAGE_INSTALLATION_CONFIGURATION
  - @ref SEC_FILE_PERMISSIONS
  - @ref SEC_PMREPORTS_OPTION
  
  @subpage PAGE_CONFIGURATION
  - @ref SEC_REPORTTABLE_CREATING
  - @ref SEC_PMREPORTS

  @subpage PAGE_REQUERIMENTS
  - @ref SEC_PROCESSMAKER_VERSION

  @subpage PAGE_HOW_WORKS
  - @ref SEC_HOW_PLUGIN
  - @ref SEC_HOW_CONNECTS
  - @ref SEC_HOW_AUTOREGISTER
  - @ref SEC_HOW_SYNCHRONIZE
  - @ref SEC_MAIN_CLASSES
  - @ref SEC_SETUP_DEV

  @subpage PAGE_GLOSARY
*/

/**
  @page PAGE_INSTALLATION_CONFIGURATION Installation and Configuration
  
  - Import the Simple Reporting plugin on the ADMIN tab. 
  - Enabled the Plugin.
  
  @section SEC_FILE_PERMISSIONS Setting File Permissions
  
  After install and enabled the Simple Reporting plugin, several of the subdirectories need to be made writable, so
  ProcessMaker running on Apache can write to them.
 
  Linux/UNIX: 

  cd <INSTALL-DIRECTORY>/processmaker/workflow/engine/plugins/pmReports/pmReports/public_html
  chmod 0777 generatedReports
  
  Windows:
 
  Right click on the following folders, choose option: "Properties", on the window "Properties" uncheck the
  option "Read only":
  
  <INSTALL-DIRECTORY>/processmaker/workflow/engine/plugins/pmReports/pmReports/public_html/generatedReports
  
  @section SEC_PMREPORTS_OPTION Simple Reporting Option
  
  Una vez habilitado el plugin se agregara una opcion en el menu de Casos.
  
  @image html CasePmReports.png "Simple Reporting Option"
*/

/**
  @page PAGE_CONFIGURATION Configuration
  
  @section SEC_REPORTTABLE_CREATING Creating a Report Table
  
  La creacion del "Report Table" se la realiza a traves de la opcion "PM Tables".
  
  @image html ReportTable1.png "Creating a Report Table"
  
  El nuevo Report Table debera cumplir con lo siguiente: el atributo "DB Connection" debera ser "workflow".
  
  @image html ReportTable2.png "Setting the Report Table"
  
  Una vez creado el Report Table hacer right-click sobre el mismo (esto en el listado), para luego seleccionar del menu
  emergente la opcion "Convert to Simple Report".
  
  @image html ReportTable3.png ""
  
  @image html ReportTable4.png ""
  
  En el Report Table creado, se puede establecer campos para realizar filtrado de informacion. Esta caracteristica
  se la realiza modificando el Report Table, y segun el campo, marcar la propiedad "Filter".
  
  @image html ReportTable5.png "Report Table Filter"
  
  @section SEC_PMREPORTS Simple Reporting plugin
  
  Para iniciar este plugin ir a HOME y seleccionar la opcion "Reports". Posteriormente aparece la interfaz del
  Simple Reporting plugin, en donde cada pestaña representa al Report Table habilitado para este plugin. En la interfaz se
  tiene lo siguiente:
  - Download As .Xml, esta opcion permite abrir/descargar la informacion visible del listado en formato Excel
  - Filter By, Realiza el filtrado de informacion del listado. Se sugiere revisar el link
    http://www.sql-tutorial.net/SQL-LIKE.asp
    En donde se explica la clausula LIKE.
  - Filter Reset, restablece al estado inicial el listado.
  
  @image html PmReports1.png "Simple Reporting"
  
  @image html PmReports2.png "Simple Reporting Filter"
*/

/**
  @page PAGE_REQUERIMENTS Requeriments
  
  @section SEC_PROCESSMAKER_VERSION ProcessMaker Requirements

  ProcessMaker V 1.8 and later.
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

/**
  @page PAGE_GLOSARY Glosary
*/