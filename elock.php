<?php
if(class_exists("enterprisePlugin")){
    G::LoadClass('plugin');
    if(!defined("PATH_PLUGIN_ELOCK")){
        define("PATH_PLUGIN_ELOCK",PATH_CORE . "/plugins/elock/");
    }
    set_include_path(
    PATH_PLUGIN_ELOCK.PATH_SEPARATOR.
    get_include_path()
    );

    class elockPlugin extends enterprisePlugin
    {
        function elockPlugin($sNamespace, $sFilename = null) {
            $pathPluginTrunk=PATH_PLUGINS . PATH_SEP.'elock';
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

            $res = parent::PMPlugin($sNamespace, $sFilename);
            $config = parse_ini_file(PATH_PLUGINS .'elock'.PATH_SEP .'pluginConfig.ini');
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

        function setup() {
            if(!$this->isInstalled()){
                $this->install();
            }
            $this->registerMenu( 'setup', 'menuelock.php');
            $this->registerStep( '1604512424b13e60108f772041214042', 'stepOutputSignature', 'eLock Digital Signature for Output Documents', '../elock/elockOutputCfg/elockOutputCfgEdit' );
            $this->registerPmFunction();
            //$this->redirectLogin( 'PROCESSMAKER_ELOCK', 'users/users_List' );
            if(method_exists($this,'registerCss')){
                $this->registerCss("/plugin/elock/elock_css");
            }else{
                //Nothing
            }
            $this->registerFolder( 'ELOCK_DYNAFORM_FOLDER', 'dynaforms/fields');
            if(method_exists($this,'registerToolbarFile')){
              if(method_exists("G", "streamCSSBigFile")){
                $this->registerToolbarFile( 'NORMAL', 'toolbarElock2.html' );
              }else{
                $this->registerToolbarFile( 'NORMAL', 'toolbarElock.html' );
              }
            }
        }

        function isInstalled(){
            $con = Propel::getConnection("workflow");
            $stmt = $con->createStatement();

            //krumo($backupTables);
            $sw_installed=true;
            foreach($this->backupTables as $key => $table){

                //First Search if the Table exists
                $Sqla = sprintf("SHOW TABLES LIKE '$table'");
                //krumo($Sqla);
                $rs1 = $stmt->executeQuery($Sqla, ResultSet::FETCHMODE_ASSOC);
                //krumo($rs1->getRecordCount());
                if ($rs1->getRecordCount() == 0){
                    $sw_installed=false;
                }
            }
            return $sw_installed;
        }

        function backupTables($backupTables,$backupSuffix="_TEMP",$backupPrefix="_"){
            //krumo("Backup");
            //Database Connections
            $con = Propel::getConnection("workflow");
            $stmt = $con->createStatement();

            //krumo($backupTables);

            foreach($backupTables as $key => $table){

                $tableBackup=$backupPrefix.$table.$backupSuffix;
                //krumo($table);
                //krumo($tableBackup);
                //First Search if the Table exists
                $Sqla = sprintf("SHOW TABLES LIKE '$table'");
                //krumo($Sqla);
                $rs1 = $stmt->executeQuery($Sqla, ResultSet::FETCHMODE_ASSOC);
                //krumo($rs1->getRecordCount());
                if ($rs1->getRecordCount() != 0){ //Table $table exists, so we can Backup
                    //If there are records in $table Backup
                    $Sqla = sprintf("SELECT * FROM $table");
                    //krumo($Sqla);
                    $rs1 = $stmt->executeQuery($Sqla, ResultSet::FETCHMODE_ASSOC);
                    //krumo($rs1->getRecordCount());
                    if ($rs1->getRecordCount() > 0){ //There are records in $table!! Backup!
                        //Delete a Prev. Backup if exists
                        $Sqla = sprintf("DROP TABLE IF EXISTS $tableBackup;");
                        //krumo($Sqla);
                        $rs1 = $stmt->executeQuery($Sqla, ResultSet::FETCHMODE_ASSOC);
                        //Create a COPY of $table in $tableBackup :: Backup
                        $Sqla = sprintf("CREATE TABLE $tableBackup SELECT * FROM $table ");
                        //krumo($Sqla);
                        $rs1 = $stmt->executeQuery($Sqla, ResultSet::FETCHMODE_ASSOC);
                    }

                }
            }

        }
        function backupTablesRestore($backupTables,$backupSuffix="_TEMP",$backupPrefix="_"){
            //krumo("Restoring");
            //Database Connections
            $con = Propel::getConnection("workflow");
            $stmt = $con->createStatement();

            //krumo($backupTables);

            foreach($backupTables as $key => $table){
                $tableBackup=$backupPrefix.$table.$backupSuffix;
                //krumo($table);
                //krumo($tableBackup);
                //First Search if the $tableBackup exists
                $Sqla = sprintf("SHOW TABLES LIKE '$tableBackup'");
                //krumo($Sqla);
                $rs1 = $stmt->executeQuery($Sqla, ResultSet::FETCHMODE_ASSOC);
                //krumo($rs1->getRecordCount());
                if ($rs1->getRecordCount() != 0){ //Table $tableBackup exists, so we can Restore
                    //Delete a Prev. $table if exists
                    $Sqla = sprintf("DROP TABLE IF EXISTS $table;");
                    //krumo($Sqla);
                    $rs1 = $stmt->executeQuery($Sqla, ResultSet::FETCHMODE_ASSOC);
                    //Rename $tableBackup to $table in $tableBackup :: Restore
                    $Sqla = sprintf("RENAME TABLE `$tableBackup` TO `$table`");
                    //krumo($Sqla);
                    $rs1 = $stmt->executeQuery($Sqla, ResultSet::FETCHMODE_ASSOC);
                }
            }

        }


        function install() {
            $backupTables=$this->backupTables;

            //echo "<h3>Make Backup of Actual Tables</h3>";
            $this->backupTables($backupTables);

            $sqlFile = PATH_PLUGINS . 'elock'. PATH_SEP . 'data' . PATH_SEP . 'mysql' . PATH_SEP . 'schema.sql';
            $sqlContents = file_get_contents ( PATH_PLUGINS . 'elock'. PATH_SEP . 'data' . PATH_SEP . 'mysql' . PATH_SEP . 'schema.sql');

            $handle = @fopen( $sqlFile, "r"); // Open file form read.
            $line = '';
            if ($handle) {
                while ( !feof($handle)) { // Loop til end of file.
                    $buffer = fgets($handle, 4096); // Read a line.
                    if ($buffer[0] != "#" && strlen( trim($buffer)) >0) { // Check for valid lines
                        $line .= $buffer;
                        $buffer = trim( $buffer);
                        if ( $buffer [ strlen( $buffer)-1] == ';' ) {
                            //echo "$line <hr>";
                            $con = Propel::getConnection('workflow');
                            $stmt = $con->createStatement();
                            $rs = $stmt->executeQuery($line, ResultSet::FETCHMODE_NUM);
                            $line = '';
                        }
                    }
                }
                fclose($handle); // Close the file.
            }
            $this->backupTablesRestore($backupTables);

        }
        function enable(){

        }
        function disable(){

        }
    }

    $oPluginRegistry =& PMPluginRegistry::getSingleton();
    $oPluginRegistry->registerPlugin('elock', __FILE__);
}