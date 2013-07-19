<?php
/**
 * pentahoreports.php
 * This cron class for pentaho reports check the dimension Tables data and actualizes it as necessary
 * @author gustavo cruz <gustavo@colosa.com>
 * @package plugins.pentahoreports.data
 * @date 17/05/2010
 */

  class pentahoreportsClassCron   {

    /**
     * Default Constructor for the class
     */
    function __construct (  ) {
    }

    /**
     * Method that executes the cron actions inside the main ProcessMaker Cron
     * @author gustavo cruz <gustavo@colosa.com>
     * @return void
     */
    function executeCron(){
        if (!class_exists('pentahoreportsClass')){
            $pluginFile = PATH_PLUGINS.'pentahoreports'.PATH_SEP.'class.pentahoreports.php';
            if(file_exists($pluginFile)){
                G::LoadClass('plugin');
                require_once($pluginFile);
            }
        }
        // the automated task is check and recreate the dimension tables
        $plugin = new pentahoreportsClass();
        $plugin->checkDimensionTimeTables();
    }
  }
?>