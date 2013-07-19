<?php
G::LoadClass( "plugin" );

/**
 * @mainpage Overview
 * @section Description
 * The Pentaho Connector Plugin is an extension that can be installed in
 * a ProcessMaker server in order to bring some Business Intelligence
 * features to Processmaker. \n
 * The Connector makes possible the render of complex reports and dashboards
 * inside ProcessMaker and also provides a Roles Management Interface in
 * order to restrict the visualization of these reports inside ProcessMaker.\n
 * More information in:\n
 * <a href='http://wiki.processmaker.com/index.php/ProcessMaker_-_Pentaho'>ProxessMaker Wiki - Pentaho</a>
 * @section setting The Development Environment
 * In order to set up a Development Environment with the Pentaho Connector Plugin is necessary
 * remember to work in a separate workspace in order to avoid conflicts between you and other developers,
 * because not all of your coworkers have this plugin installed in theirs working sites. \n
 * @section The Setting Up
 * Another good development practice can be separate the plugins repository from the main
 * ProcessMaker environment, to accomplish this, a couple of symbolic links can be added in
 * the main plugin folder, those are linked to the external plugin folder, for example:\n
 * <code>
 * ln -s /<plugins_path>/pentahoreports/pentahoreports/ /<processmaker_path>/workflow/engine/plugins/\n
 * ln -s /<plugins_path>/pentahoreports/pentahoreports.php /<processmaker_path>/workflow/engine/plugins/\n
 * </code>
 * @section Requirements
 * In order that the ProcessMaker Fact Table can be created and also the triggers that populates the data for
 * that table, the version of mysql should it be 5.1.7 or greater.
 */

/**
 * @section Filename
 * pentahoreports.php
 * @subsection Description
 * Main class that handles the install, enabling and disabling of the pentaho connector plugin.
 * @author Fernando Ontiveros
 * @subsection Copyright
 * Copyright (C) Colosa Development Team 2010
 * <hr>
 * @package plugins.pentahoreports.classes
 *
 */
if(class_exists("enterprisePlugin")){
    class pentahoreportsPlugin extends enterprisePlugin
    {
        /**
         * This is the constructor method of a pentahoreportPlugin object
         * @author Fernando Ontiveros
         * @param $sNamespace domain
         * @param $sFilename
         * @return $res object
         */
        function pentahoreportsPlugin($sNamespace, $sFilename = null)
        {
            // getting the plugin version
            $pathPluginTrunk=PATH_PLUGINS . PATH_SEP.'pentahoreports';
            if ( file_exists ( $pathPluginTrunk.PATH_SEP.'VERSION' ) ){
                $version = trim(file_get_contents ( $pathPluginTrunk.PATH_SEP.'VERSION' ));
            }else {
                $cmd = sprintf	("cd %s && git status | grep 'On branch' | awk '{print $3 $4} ' && git log --decorate | grep '(tag:' | head -1  | awk '{print $3$4} ' ", $pathPluginTrunk);
                if ( exec ( $cmd , $target) ) {
                    $cmd = sprintf	("cd %s && git log --decorate | grep '(tag:' | head -1  | awk '{print $2} ' ", $pathPluginTrunk);
                    $commit = exec ( $cmd , $dummyTarget);
                    $cmd = sprintf	("echo ' +' && cd %s && git log %s.. --oneline | wc -l && echo ' commits.'", $pathPluginTrunk, $commit );
                    exec ( $cmd , $target) ;
                    $version = implode(' ', $target) ;
                }else{
                    $version = 'Development Version' ;
                }
            }
            // setting the basic attributes that will be used
            $res = parent::PMPlugin($sNamespace, $sFilename);
            $config = parse_ini_file(PATH_PLUGINS .'pentahoreports'.PATH_SEP .'pluginConfig.ini');
            $this->sFriendlyName = $config['name'];
            $this->sDescription  = $config['description'];
            $this->sPluginFolder = $config['pluginFolder'];
            $this->sSetupPage    = $config['setupPage'];
            $this->iVersion      = $version;
            $this->aWorkspaces = null;
            $this->aDependences  = array(array("sClassName"=>"enterprise"),array("sClassName"=>"pmLicenseManager"));
            $this->bPrivate=parent::registerEE($this->sPluginFolder,$this->iVersion);
            $this->backupTables=array("KT_APPLICATION","KT_PROCESS","KT_DOCUMENT","KT_DOC_TYPE","KT_FIELDS_MAP","KT_CONFIG");
            return $res;
        }
        /**
         * This method registers the menues for the plugin
         * @author Fernando Ontiveros
         * @return void
         */
        function setup()
        {
            // registering menus
            $this->registerMenu( 'processmaker', 'menupentahoreports.php' );
            $this->registerMenu( 'setup', 'menusetup.php' );
            // registering the dashboards page
            if( method_exists( $this,'registerDashboardPage' )){
                $this->registerDashboardPage("../../blank/pentahoreports/dashboard","Pentaho","ICON_PENTAHO");
            }
            // registering css files
            if( method_exists( $this,'registerCss' ) ){
                $this->registerCss("/plugin/pentahoreports/pentaho_css");
            }
        }

        /**
         * Method that installs some required files and also create the temp folder
         * @author Fernando Ontiveros
         * @return void
         */
        function install()
        {
            // copy the cron file to the plugin jobs folder
            $this->copy( 'data' . PATH_SEP . 'pentahoreports.php', PATH_CORE . 'bin' . PATH_SEP . 'plugins' . PATH_SEP . 'pentahoreports.php', false, true );
            $pluginTemp = PATH_PLUGINS.'pentahoreports'.PATH_SEP.'temp';
            // creating the temp file
            if(!file_exists($pluginTemp)){
                mkdir($pluginTemp, 0777, true);
                chmod($pluginTemp, 0777);
            }
        }
        function enable(){

        }
        function disable(){

        }

    }
    // calling an registering the plugin
    $oPluginRegistry =& PMPluginRegistry::getSingleton();
    $oPluginRegistry->registerPlugin('pentahoreports', __FILE__);
}