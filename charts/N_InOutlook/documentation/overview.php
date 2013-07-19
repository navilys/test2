<?php

/**
  @mainpage Overview
  @dontinclude overview.php
  @section Introduction
  The Outlook Connector

  @subpage PAGE_FEATURES
  - @ref SEC_CASES

  @subpage PAGE_REQUERIMENTS
  - @ref SEC_NET_FRAMEWORK
  - @ref SEC_OUTLOOK

  @subpage PAGE_INSTALLATION
  - @ref SEC_INSTALL_PLUGIN
  - @ref SEC_INSTALL_ADDIN
  - @ref SEC_SETUP_ADDIN

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
  @page PAGE_FEATURES Main Features
  @section SEC_CASES Cases

  - For this first version it is possible to interact with cases (lists, steps, etc), in the same way as it does from the web application, but in a simpler and faster way, because the Add-in for Outlook records user information the first time and subsequently it is useful to be used by the system without being authenticated in every moment.

  - All of the characteristics (forms, input documents, output documents, triggers, derivations, etc.) are fully functional from Outlook.

*/

/**
  @page PAGE_REQUERIMENTS Requeriments
  @section SEC_NET_FRAMEWORK .NET Framework

  - The Add-in for Outlook requires the computer on which will be used, the installation of .NET Framework because it uses the same libraries.

  @section SEC_OUTLOOK Microsoft Outlook

  - The Add-in has been implemented to work in both Outlook 2007 and Outlook 2010.
*/

/**
  @page PAGE_INSTALLATION Installation
  @section SEC_INSTALL_PLUGIN Install the plugin in ProcessMaker

  - The plugin will be available once imported the Enterprise plugin with the corresponding license, it's necessary activate it once it's installed, it doesn't need additional configuration on the server side.

  @section SEC_INSTALL_ADDIN Install the Add-in for Outlook

  - In addition to the Enterprise plugin a file is provided with the installers for Outlook Add-in for both Outlook 2007 and Outlook 2010. For 2007 version of Outlook first install libraries "Primary Interop Assemblies" which do not come by default with Office 2007, the steps to install are:

  1 .- Close the Outlook application if it's open

  2 .- If 2007 version of Outlook is installed, run first the file "o2007pia.msi" (provided in the package installers)

  3 .- Run the Add-in installer "setup.exe" which will check if .NET Framework is installed on the system, if it isn't, download and installed it

  @section SEC_SETUP_ADDIN Setup the Add-in for Outlook

  - After installing the Add-in and open the Outlook a window appears to configure the data server and the user account to use it, when the button "Save" is pressed it will connect to the server to try to validate them, if the validation was successful the configuration window disappears and the system can be used.
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
  @section Addin
  Add-in is a set of software components that adds specific abilities to a larger software application
*/


