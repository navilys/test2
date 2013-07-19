<?php
if(class_exists("enterprisePlugin")){
    G::LoadClass( "plugin");
    if(!defined("PATH_PLUGIN_KT")){
        define("PATH_PLUGIN_KT",PATH_CORE . "/plugins/knowledgeTree/");
    }
    set_include_path(
    PATH_PLUGIN_KT.PATH_SEPARATOR.
    get_include_path()
    );
    class KnowledgeTreePlugin extends enterprisePlugin
    {
        function KnowledgeTreePlugin($sNamespace, $sFilename = null)
        {
            $pathPluginTrunk=PATH_PLUGINS . PATH_SEP.'knowledgeTree';
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
            $config = parse_ini_file(PATH_PLUGINS .'knowledgeTree'.PATH_SEP .'pluginConfig.ini');
            $this->sFriendlyName = $config['name'];
            $this->sDescription  = $config['description'];
            $this->sPluginFolder = $config['pluginFolder'];
            $this->sSetupPage    = $config['setupPage'];
            $this->iVersion      = $version;
            $this->aWorkspaces = null;
            $this->aDependences  = array(array("sClassName"=>"enterprise"),array("sClassName"=>"pmLicenseManager"));
            $this->bPrivate=parent::registerEE($this->sPluginFolder,$this->iVersion);
            
            $this->sMethodGetUrlDownload = $config["methodGetUrlDownload"];

            $this->backupTables=array("KT_APPLICATION","KT_PROCESS","KT_DOCUMENT","KT_DOC_TYPE","KT_FIELDS_MAP","KT_CONFIG");

            return $res;
        }

        function setup()
        {
            if(!$this->isInstalled()){
                $this->install();
            }
            $this->registerMenu( 'processmaker', 'menuKT.php');
            $this->registerMenu( 'processes', 'menuKTProcess.php');
            $this->registerMenu( 'setup', 'menuKTSetup.php');

            //$this->registerTrigger( PM_LOGIN, 'kt_login' );

            $this->registerTrigger( PM_CREATE_CASE, 'createCaseFolder' );
            $this->registerTrigger( PM_CASE_DOCUMENT_LIST, 'caseDocumentList' );
            $this->registerTrigger( PM_CASE_DOCUMENT_LIST_ARR, 'getListing' );
            $this->registerTrigger( PM_UPLOAD_DOCUMENT, 'addDocument' );

            $this->registerTrigger( PM_UPLOAD_DOCUMENT_BEFORE, 'ktUserConfigBreakStep' );
            if(method_exists($this,'registerCss')){
                $this->registerCss("/plugin/knowledgeTree/kt_css");
            }else{
                //Nothing
            }

            if(method_exists($this,'registerDashboardPage')){
                //$this->registerDashboardPage("../knowledgeTree/ktDashboard","KT Documents","ICON_KTDMS");
            }else{
                //Nothing
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

        function updateTables(){
            $con = Propel::getConnection("workflow");
            $stmt = $con->createStatement();
            //SHOW FIELDS FROM table / SHOW COLUMNS FROM table / DESCRIBE table / DESC table / EXPLAIN table
            $sql="SHOW FIELDS FROM KT_DOCUMENT";
            $rs1 = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);
            $rs1->next();

            $fieldsUpdated['DOC_TYPE']['NAME']="DOC_TYPE";
            $fieldsUpdated['DOC_TYPE']['TYPE']="VARCHAR( 4 ) NOT NULL";
            $fieldsUpdated['DOC_TYPE']['MOREATTRIB']="AFTER `DOC_UID`";


            $fieldsUpdated['DOC_PMTYPE']['NAME']="DOC_PMTYPE";
            $fieldsUpdated['DOC_PMTYPE']['TYPE']="VARCHAR( 10 ) NOT NULL DEFAULT 'OUTPUT'";
            $fieldsUpdated['DOC_PMTYPE']['MOREATTRIB']="AFTER `DOC_TYPE`";

            while ( is_array($row = $rs1->getRow())) {
                if(array_key_exists($row['Field'],$fieldsUpdated)){
                    unset($fieldsUpdated[$row['Field']]);
                }
                $rs1->next();
            }

            $sw_update=false;
            foreach($fieldsUpdated as $fieldName => $fieldInfo){
                $sql="ALTER TABLE `KT_DOCUMENT` ADD `".$fieldInfo['NAME']."` ".$fieldInfo['TYPE']." ".$fieldInfo['MOREATTRIB'];
                $rs2 = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);
                $sw_update=true;
            }
            if($sw_update){
                $sql="ALTER TABLE `KT_DOCUMENT` DROP PRIMARY KEY ,ADD PRIMARY KEY ( `DOC_UID` , `DOC_TYPE` ) ";
                $rs3 = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);
            }
        }






        function install(){

            $backupTables=$this->backupTables;

            //echo "<h3>Make Backup of Actual Tables</h3>";
            $this->backupTables($backupTables);

            $sqlFile = PATH_PLUGINS . 'knowledgeTree'. PATH_SEP . 'data' . PATH_SEP . 'mysql' . PATH_SEP . 'schema.sql';
            $sqlContents = file_get_contents ( PATH_PLUGINS . 'knowledgeTree'. PATH_SEP . 'data' . PATH_SEP . 'mysql' . PATH_SEP . 'schema.sql');

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

            $this->updateTables();
        }
        function enable(){

        }
        function disable(){

        }

    }

    $oPluginRegistry =& PMPluginRegistry::getSingleton();
    $oPluginRegistry->registerPlugin('knowledgeTree', __FILE__);

    if(class_exists("headPublisher")){
        $oHeadPublisher =& headPublisher::getSingleton();
        $oHeadPublisher->addScriptFile("/plugin/knowledgeTree/js/ktDMS.js",1);
    }
}