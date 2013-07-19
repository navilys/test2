<?php
/**
  @mainpage Overview
  @dontinclude overview.php

  @section Introduction

  Batch Routing Plugin es una extension que permite la derivacion de casos por lotes en un determinado proceso, este plugin
  puede ser instalado en un servidor de ProcessMaker.
  
  @subpage PAGE_INSTALLATION_CONFIGURATION
  - @ref SEC_BATCHROUTING_OPTION
  
  @subpage PAGE_EXAMPLE
  - @ref SEC_PROCESS_CREATING
  - @ref SEC_DYNAFORMGRID
  - @ref SEC_TASK
  - @ref SEC_CASE_RUNNING

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
  
  - Import the Batch Routing plugin on the ADMIN tab. 
  - Enabled the Plugin.
  
  @section SEC_BATCHROUTING_OPTION Batch Routing Option
  
  Una vez habilitado el plugin y habilitado en propiedades de Tarea con un DynaForm de tipo Grid (este punto se describe
  en la seccion Example), el plugin Batch Routing agregara una opcion en el menu de Casos.
  
  @image html CaseBatchRouting.png "Batch Routing Option"
*/

/**
  @page PAGE_EXAMPLE Example
  
  @section SEC_PROCESS_CREATING Creating a Process
  
  El proceso que se describe a continuacion trata de mostrar el uso de este plugin.
  
  El proceso de ejemplo contempla tres tareas (Task1, Task2 y Task3), cada uno con un dynaform como unico
  paso, la ejecucion del proceso es de la siguiente manera:
  - La derivacion del caso de la tarea Task1 a Task2 es de namera normal.
  - En la tarea Task2 la derivacion a la tarea siguiente depende del evaluador "evaluation routing rule", el cual
    dependiendo de la evaluacion derivara el caso a la siguiente tarea Task3 o devolvera el caso a la tarea Task1.
  - En la tarea Task3 ocurre lo mismo que en la tarea Task2.
  
  @image html Process1.png "Creating a Process"
  
  @image html Process2.png "Evaluation Routing Rule"
  
  Los Dynaforms estan estructurados de la siguiente manera (el dynaform frm3 se crea/deduce en base al dynaform frm2):
  
  @image html ProcessFrm1View.png "Dynaform frm1 - Vista"
  
  @image html ProcessFrm1Xml.png "Dynaform frm1 - XML"
  
  @image html ProcessFrm2View.png "Dynaform frm2 - Vista"
  
  @image html ProcessFrm2Xml.png "Dynaform frm2 - XML"
  
  @section SEC_DYNAFORMGRID DynaForm de tipo Grid

  Dynaform Template
  
  Se debe crear un DynaForm de tipo Grid, el mismo debera corresponder al DynaForm que realiza la derivacion a la siguiente
  tarea en un proceso, este DynaForm de tipo Grid solo puede contener los siguientes objetos:
  
  - DropDown
  - Date
  - Currency
  - Percentage
  - TextArea
  - Link
  - Hidden
  - YesNo
  - Text
  
  Se puede tener varios DynaForm de tipo Grid los cuales podran ser utilizados por el plugin, la creacion de uno o varios
  de estos dynaForms dependera de la aplicacion y su uso.
  
  Para el proceso de ejemplo se tiene el dynaform-grid grdFrm2 y grdFrm3, a continuacion mostramos el dynaform-grid grdFrm2:
  
  @image html ProcessGrdFrm2View.png "Dynaform grdFrm2 - Vista"
  
  @image html ProcessGrdFrm2Xml.png "Dynaform grdFrm2 - XML"
  
  @section SEC_TASK Propiedades de una tarea - Batch Routing Plugin
  
  Una vez habilitado el plugin, para su activacion en el proceso se debera ir a propiedades de la Tarea, ir al tab
  "Consolidated Case List", habilitar la opcion "Enable consolidate for this task." y seleccionar un "Dynaform Template";
  estos "Dynaform Template" son los DynaForm de tipo Grid mencionados anteriormente.
  
  @image html TaskBatchRouting1.png ""
  
  @image html TaskBatchRouting2.png ""
  
  @image html TaskBatchRouting3.png ""
  
  @section SEC_CASE_RUNNING Case Running
  
  Cuando se deriva uno o varios casos de Task1 a Task2 se puede observar lo siguiente:
  
  @image html CaseRunning.png ""
  
  En este punto es donde se puede ingresar/seleccionar informacion para su posterior derivacion, ya sea individual
  o grupal (por lotes).
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