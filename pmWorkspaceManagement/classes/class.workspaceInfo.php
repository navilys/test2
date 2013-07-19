<?php

    /* 
     * Class with general statistics about a workspace
     */
    class workspaceInfo {
        
        public $workspaceName; // name of the workspace
        public $totalCases;    // an array that groups the totals by case state: ej. $totalCases['todo']
        public $fileDiskUsage; // space used by the workspace files
        public $dataBaseDiskUsage; // space used by the database files of the workspace
        public $totalActiveProcesses; // number of active processes
        public $numberOfUsers; // number of users
        public $totalTables; // an array that groups the total tables by engine
        public $logo; // path to the logo of the workspace. 
                      // This string may contain the place holde {WORKSPACE} where the contents of SYS_SYS must be placed to get the correct route
                      // SYS_SYS must be used independently of what is the workspace we want to get the logo of.

    }

?>