<?php
/**
  @mainpage Overview
  @dontinclude overview.php

  @section Introduction
  SigPlus is a plugin available on the Enteprise Edition. 

  The use of Sigplus within the ProcessMaker BPM software, will help achieve the purposes of signature such as:
  - Confirm that a signature will authenticate a writing by identifying the signer with the signed document.
  - The act of signing helps guarantee the validity of contracts.
  - In certain contexts a signature expresses an approval or authorization of the writing.
  - A signature on a written document imparts a sense of clarity in the transaction.
 
  The use of the Sigplus plugin will allow users to capture a handwritten signature in electronic documents to help
  reduce the cost involved in paper contracts and forms, so the company could be using fewer paper documents.
  
  How the plugin works 

  The Sigplus plugin saves the signature position for each signature created within the Output Document and then shows the
  when they are called. For this purpose it works with an applet of Java.
  
  @subpage PAGE_INSTALLATION_CONFIGURATION
  
  @subpage PAGE_EXAMPLE
  - @ref SEC_OUTPUT_DOCUMENT
  - @ref SEC_DYNAFORM
  - @ref SEC_TASK
  - @ref SEC_CASE_RUNNING

  @subpage PAGE_REQUERIMENTS
  - @ref SEC_OS
  - @ref SEC_JRE
  - @ref SEC_TOPAZ_FILES
  - @ref SEC_PROCESSMAKER_VERSION

  @subpage PAGE_HOW_WORKS
  - @ref SEC_HOW_PLUGIN
  - @ref SEC_HOW_CONNECTS
  - @ref SEC_HOW_AUTOREGISTER
  - @ref SEC_HOW_SYNCHRONIZE
  - @ref SEC_MAIN_CLASSES
  - @ref SEC_SETUP_DEV

  @subpage PAGE_GLOSARY
  
  @subpage PAGE_DOCUMENTATION
*/

/**
  @page PAGE_INSTALLATION_CONFIGURATION Installation and Configuration 
  
  - Import the SigPlus plugin on the ADMIN tab. 
  - Enabled the Plugin.
  
  @page PAGE_EXAMPLE Example
  
  @section SEC_OUTPUT_DOCUMENT Creating a Process
  
  Se debe tener almenos un output-document en el proceso, en el mismo se incluiran las imagenes (firmas) que
  seran capturadas con el dispositivo externo.

  La opcion "Output-Document" se encuentra en el menu principal "Designer", esta opcion sera visible cuando se
  este en modo edicion del proceso.

  @image html OutputDocument1.png "Opcion Output-Document"

  @image html OutputDocument2.png "New Output-Document"

  @image html OutputDocument3.png "Edit Output-Document"

  @image html OutputDocument4.png "Definicion de variables en el Output-Document"

  @section SEC_DYNAFORM Dynaform
  
  Create a Dynaform with the variables that were defined on the Output Document created.
  
  @image html Process1.png "Edit DynaForm"

  @image html Process2.png "Variables del DynaForm"
  
  @section SEC_TASK Steps of Task
  
  Assign the Dynaform into the Task 1 and also the External Step which is generated automatically when the plugin is
  installed and activated.
  
  @image html Process3.png "Pasos de una Tarea"

  @image html Process4.png "Nuevo Paso"
  
  Una vez incluido, se debe editar el paso (editar el plugin SigPlus), donde se eligira el output-document
  a utilizar y se ingresara a los firmantes (en este caso, se recomienda manejar variables definidas, para que el proceso
  sea generico).

  @image html Process5.png "Edicion del Paso (SigPlus Plugin)"

  @image html Process6.png "Definicion del Paso (SigPlus Plugin)"
  
  Where:
  - Document List: choose the name of the Output Document where the signs will be.
  - Signer: write the number of signers (variables) that were defined on the Dynaform.
  
  @section SEC_CASE_RUNNING Case Running
  
  When the case is started, the names of the signers must be filled.
  
  @image html CaseRunning1.png ""
  
  Click on Submit, the Sign document contract will display:
  
  @image html CaseRunning2.png ""
  
  As it can be seen the java applet is loading to show the sign part. Also it displays the number of signers who were
  defined on the previous dynaform, and the Open unsigned Document as disabled, beacuse no signs were defined yet.

  While is loading two warning windows of ununverified sign will appear:

  For the first one choose Run
  
  @image html CaseRunning3.png ""
  
  For the second choose No
  
  @image html CaseRunning4.png ""
  
  The sign section will be enabled
  
  @image html CaseRunning5.png ""
  
  Where:
  - Ok: click on this when the sign on the SigPlus LCD is finished
  - Clear: click on this if there is a mistake on the sign.
  - Restart: click on this to restar the SigPlus LCD
  - Open Signed Document: shows the document in a pdf format after the signing procedure.
  - Continue: to go to the next step once the document has been signed.

  Sign on the SigPlus LCD and immediatly the sign will appear on the sign and signer's part:
  
  @image html CaseRunning6.png ""
  
  Click Ok to continue with the next sign.

  Complete the three signs and open the signed document. 
*/

/**
  @page PAGE_REQUERIMENTS Requeriments
  
  @section SEC_OS Operating System

  Windows (XP, Vista, 7). 

  @section SEC_JRE Java SE Runtime Environment (JRE)

  Java Version 1.5 an later. This application must be installed on the user computer.
 
  Tip: The instalation settings can be managed on the client PC by going to “Control Panel” and double-clicking on Java go
  to Control Panel look for the icon.
  
  @image html JavaIcon.png "Java icon"
  
  The Control Panel of Java will open, choose JAVA tab and then View:

  @image html JavaControlPanel.png "Java Control Panel"
  
  The Java Runtime Environment Settings window will appear, check if the version is enabled and where JAVA was installed.
  
  @image html JavaControlPanelSettings.png "Java Runtime Environment Settings"
  
  If there are more than two versions of java, only one must be enabled.
  
  @section SEC_TOPAZ_FILES Additional Files

  Para sistemas operativos Windows, copiar los archivos "SigUsb.dll" y "win32com.dll" en la carpeta "bin" de la instancia
  JRE que esta funcionando (ej. "C:\Program Files\Java\jre6\bin"), este paso se lo realiza en el computador del usuario.
    
  @image html JavaDLLSigplus.png "Additional Files"
  
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

  @section G1 Java SE Runtime Environment (JRE)

  The Java SE Runtime Environment contains the Java virtual machine, runtime class libraries, and Java application
  launcher that are necessary to run programs written in the Java programming language.
*/

/**
  @page PAGE_DOCUMENTATION Documentation

  http://wiki.processmaker.com/index.php/ProcessMaker_-_SigPlus_V_1.0
*/