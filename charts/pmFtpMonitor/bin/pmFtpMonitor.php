<?php

/**
 * pmFtpMonitorCron.php
 * This cron class for pmFtpMonitor launches the plugin execution 
 * @author <@colosa.com>
 * @package 
 * @date 
 */
G::LoadClass('plugin');
class pmFtpMonitorClassCron {

    /**
     * Default Constructor for the class
     */
    function __construct() {
        
    }

    /**
     * Method that executes the cron actions inside the main ProcessMaker Cron
     * @author <@colosa.com>
     * @return void
     */
    function executeCron() {
        if (!class_exists('pmFtpMonitorClass')) {
            $pluginFile = PATH_PLUGINS . 'pmFtpMonitor' . PATH_SEP . 'class.pmFtpMonitor.php';
            if (file_exists($pluginFile)) {
                G::LoadClass('pmFtpMonitorClass');
                require_once($pluginFile);
            }
        }

        // the automated task executes the plugin
        $plugin = new pmFtpMonitorClass();
        $plugin->executeSchedulerJob();
    }

}