<?php
  class myAdmin {
    var $adapter;
    var $xaction;
    var $table;
    var $query;

    function myAdmin ($database) {
      $this->xaction = isset($_REQUEST['xaction'])? $_REQUEST['xaction'] : 'showTables';
      $this->table   = isset($_REQUEST['table'])?   $_REQUEST['table'] : '';
      $this->query   = isset($_REQUEST['query'])?   $_REQUEST['query'] : '';
      $this->cnn = Propel::getConnection($database);
      //$this->stm = $this->cnn->createStatement();

      //realize which  adapter we have, database engine.
      $databaseInfo = $this->cnn->getDatabaseInfo();
      $connection   = $databaseInfo->getConnection();
      $dsn          = $connection->getDSN();

      $this->adapter = $dsn['phptype'];

      switch ($this->adapter) {
        case 'mssql':
          $this->showTablesSql = " EXEC sp_tables @table_type = \"'table'\" ";
          $this->showTablesFetch = ResultSet::FETCHMODE_ASSOC;
          $this->showTablesField = 'TABLE_NAME';
          $this->describeTable   = " EXEC sp_columns @table_name = \"{$this->table}\" ";

          $this->describeColumnName = 'COLUMN_NAME';
          $this->describeDataType   = 'DATA_TYPE';
          $this->describeTypeName   = 'TYPE_NAME';
          $this->describePrecision  = 'PRECISION';
          $this->describeLength     = 'LENGTH';
          $this->describeNullable   = 'NULLABLE';
          break;

        default:
          $this->showTablesSql   = " show tables ";
          $this->showTablesFetch = ResultSet::FETCHMODE_NUM;
          $this->showTablesField = 0;
          $this->describeTable   = " DESCRIBE {$this->table} ";

          $this->describeColumnName = 'Field';
          $this->describeDataType   = 'Type';
          $this->describeTypeName   = 'Type';
          $this->describePrecision  = 'Type';
          $this->describeLength     = 'Type';
          $this->describeNullable   = 'Null';

      }

    }

    function execute () {
      $this->{$this->xaction}();
    }

    /**
      get the first list of tables in that workspace
    */
    function showTables() {
      $stm = $this->cnn->createStatement();
      $stm2 = $this->cnn->createStatement();
      $rs = $stm->executeQuery( $this->showTablesSql, $this->showTablesFetch );
      $rs->next();
      $row = $rs->getRow();
      $rows = array();

      while ( is_array ( $row ) ) {
        $sql2 =  "select count(*) as CANT from " . $row[ $this->showTablesField ];
        $rs2 = $stm2->executeQuery($sql2, ResultSet::FETCHMODE_ASSOC);
        $rs2->next();
        $row2 = $rs2->getRow();

        $r = array();
        $r['id']   = count($rows)+1;
        $r['name'] = $row[ $this->showTablesField ];
        $r['cant'] = $row2['CANT'];
        $rows[] = $r;
        $rs->next();
        $row = $rs->getRow();
      }
      $result = array();
      $result['data'] = $rows;
      $result['totalCount'] = count($rows);
      $json = G::json_encode( $result) ;

      print $json;
    }

    /**
      get information about a specific table
      we send the DESCRIBE table   in mysql to obtain columns
    */
    function table() {
      $stm = $this->cnn->createStatement();
      $stm2 = $this->cnn->createStatement();
      $rs = $stm->executeQuery($this->describeTable, ResultSet::FETCHMODE_ASSOC);
      $rs->next();
      $row = $rs->getRow();
      $rows = array();
      $columns = array();
      $fields = array();
      while ( is_array ( $row ) ) {
        $r = array();
        $r['id']          = count($rows)+1;
        $r['name']        = $row[$this->describeColumnName];
        $r['type']        = $row[$this->describeDataType];
        $r['typename']    = $row[$this->describeTypeName];
        $r['nullable']    = $row[$this->describeNullable];
        $rows[] = $r;

        $tn = $row['TYPE_NAME'];
        $length = $tn == 'varchar' ? 90: ($tn=='float' ? 50: 80);
        $r = array();
        $r['header']         = $row[$this->describeColumnName];
        $r['width']          = $length;
        $r['dataIndex']      = $row[$this->describeColumnName];
        $r['sortable']       = 'true';
        $r['editable']       = 'true';
        $r['align']          = 'left';
        $columns[] = $r;

        $r = array();
        $r['name']          = $row[$this->describeColumnName];
        $fields[] = $r;

        $rs->next();
        $row = $rs->getRow();
      }
      $result = array();
      $result['columns'] = $columns;
      $result['fields']  = $fields;
      //$result['data'] = $rows;
      //$result['totalCount'] = count($rows);
      $json = G::json_encode( $result) ;

      print $json;
    }

    function tableRows() {
      $sql = " select * from {$this->table} ";
      $stm = $this->cnn->createStatement();
      $rs = $stm->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);
      $rs->next();
      $row = $rs->getRow();

      $rows = array();
      $i = 0;
      while ( is_array ( $row ) ) {
        $rows[] = $row;
        $rs->next();
        $row = $rs->getRow();
        if ( $i++ == 100 ) break;
      }
      $result = array();
      $result['data'] = $rows;
      $result['totalCount'] = count($rows);
      $json = G::json_encode( $result) ;

      print $json;
    }

    function query() {
      try {
        $rows = array();
        $stm = $this->cnn->createStatement();
        $rs = $stm->executeQuery($this->query, ResultSet::FETCHMODE_ASSOC);
        if ( $rs->next() != false ) {
          $rs->next();
          $row = $rs->getRow();

          $i = 0;
          while ( is_array ( $row ) ) {
            $rows[] = $row;
            $rs->next();
            $row = $rs->getRow();
           if ( $i++ == 100 ) break;
          }
        }
        $result = array();
        $result['error'] = 0;
        $result['message'] = count($rows) . ' rows selected';
        $result['data'] = $rows;
        $result['totalCount'] = count($rows);
      }
      catch ( Exception $e ) {
        $result = array();
        $result['error'] = 1;
        $result['message'] = $e->getMessage();
        $result['data'] = null;
        $result['totalCount'] = 0;
      }
      $json = G::json_encode( $result) ;

      print $json;
    }

    /** query log functions */

    //recursive remove dir
    function rrmdir($dir) {
      if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
          if ($object != "." && $object != "..") {
            if (filetype($dir."/".$object) == "dir") $this->rrmdir($dir."/".$object); else unlink($dir."/".$object);
          }
        }
        reset($objects);
        rmdir($dir);
      }
    }

    function processFile($file, $pos = 0) {
      $result = "";
      $fp = fopen($file, "r");
      fseek($fp, $pos);
      $i = 0;
      while (!feof($fp)) {
        $line = explode("|", fgets($fp));
        if ( count($line) >= 10 ) { //skip old format lines
          $microtime = $line[0];
          $ip        = isset($line[2]) ? $line[2] : '';
          $pid       = isset($line[3]) ? $line[3] : '';
          $command   = isset($line[4]) ? $line[4] : '';
          $sqlTime   = isset($line[5]) ? $line[5] : '';
          $sql       = isset($line[6]) ? $line[6] : '';
          $backtrace = isset($line[7]) ? $line[7] : '';
          $time = (substr($microtime,0,2)*3600 + substr($microtime,3,2)*60 + substr($microtime,6,2)) * 10000 + substr($microtime,9,4);
          if ( $command == 'executeQuery' || $command == 'executeUpdate' ) {
          	if ( !isset( $this->aPages[$ip]) ) $this->aPages[$ip] = array();
          	if ( !isset( $this->aPages[$ip][$pid]) ) {
          		$node['date']      = $microtime;
          		$node['start']     = $time-$sqlTime*10000;
          		$node['end']       = $time-$sqlTime*10000;
          		$node['duration']  = 0; //$sqlTime*10000;
          		$node['count']     = 0;
          		$node['workspace'] = $line[1];
          		$node['method']    = $line[8];
          		$node['uri']       = $line[9];
          		$node['fields']    = trim($line[10]);
          		$this->aPages[$ip][$pid] = $node;
          	}

            $logDir  = PATH_DATA . 'log/propelByIp/' . $ip ;
            $logFile = $logDir . '/' . $pid ;
            if (!is_dir($logDir) )mkdir($logDir);
            $gp = fopen( $logFile, 'a' );

          	$this->aPages[$ip][$pid]['count'] ++;
          	$i = $this->aPages[$ip][$pid]['count'];
            $acumTime = $time - $this->aPages[$ip][$pid]['start'];
            $thisTime = $time - $this->aPages[$ip][$pid]['end'];
            $this->aPages[$ip][$pid]['duration'] += $thisTime;
            $this->aPages[$ip][$pid]['end'] = $time;
            fprintf ( $gp, "%3d|%1.4f|%1.4f|%1.4f|%s|%s\n", $i, $thisTime/10000, $acumTime/10000, $sqlTime, $sql, $backtrace );
            fclose($gp);
          }
        }
      }
      fclose($fp);
      //$result = "$i pages in " . count( $this->aPages ) . " bytes <br>";

      $totalIps = array();
      $logTotal  = PATH_DATA . 'log/propelByIp/total.txt';

      if ( file_exists($logTotal) ) {
        $totalIps = unserialize ( file_get_contents ($logTotal) );
      }

      $pages = 0;
      $totalPages = 0;
      $pagesByIp = array();
      foreach($totalIps as $kp => $vp ) {
        if ( is_array($vp) && count($vp) == 2 ) {
          $pagesByIp[$vp['ip']] = intval($vp['cant']);
          $totalPages += $vp['cant'];
        }
      }

      foreach ( $this->aPages as $kIp => $ip ) {
        $res = array(); $data = array();
        foreach ( $ip as $kPid => $sql ) {
        	$node = array();
        	$node['pid']       = $kPid;
        	$node['date']      = $sql['date'];
        	$node['workspace'] = $sql['workspace'];
        	$node['count']     = $sql['count'];
        	$node['duration']  = $sql['duration']/10000;
        	$node['method']    = $sql['method'];
        	$node['uri']       = $sql['uri'];
        	$node['fields']    = $sql['fields'];
        	$data[] = $node;
          //$result .= sprintf("&nbsp; &nbsp; %06d %s %s %03d %1.4f %s %s<br>",$kPid, $sql['date'], $sql['workspace'], $sql['count'] ,$sql['duration']/10000  ,$sql['method'] ,$sql['uri']  );
        }
        $res = array();
        $res['data'] = $data;
        $res['totalCount'] = count($data);
        $logFile  = PATH_DATA . 'log/propelByIp/' . $kIp . '.txt';
        $gp = fopen( $logFile, 'a' );
        fprintf ( $gp, "%s", serialize ( $res) );
        fclose($gp);

        $pages += count($data);
        //add the cant of page for this ip, if the ip is new, we need to add a new item in the array, otherwise add the cant to already value
        if ( isset($pagesByIp[$kIp] ) ) {
          $pagesByIp[$kIp] += count($data);
        }
        else {
          $pagesByIp[$kIp] = count($data);
        }
      }
      //print_r ($pagesByIp);

      //now build the resulting array for the dropdown
      $totalIps = array();
      foreach($pagesByIp as $ip => $cant ) {
        //$totalIps[] = array( 'ip'=> $ip, 'cant'=> $ip . " has " . $cant . " pages" );
        $totalIps[] = array( 'ip'=> $ip, 'cant' => $cant );
      }

      //save the totals in the file
      $logTotal  = PATH_DATA . 'log/propelByIp/total.txt';
      $gp = fopen( $logTotal, 'w' );
      fprintf ( $gp, "%s", serialize ( $totalIps) );
      fclose($gp);

      $totalPages += $pages;
      $result['message']  = sprintf ("log refreshed, added %d pages, total %d pages in %d ips", $pages, $totalPages, count($totalIps) );
      $result['ipsPages'] = sprintf ("%d/%d", count($totalIps), $pages );
      return $result;
    }

    function logRefresh() {
      try {
        $wfLog = PATH_DATA . "log/propel.log";
        $wfPos = PATH_DATA . "log/propel.pos";
        $wfEnv = PATH_HOME . "engine/config/env.ini";
        $enabled = "false";
        if (file_exists($wfEnv)) {
          $config = @parse_ini_file($wfEnv, false);
          if (isset($config["debug_sql"])) $enabled = $config["debug_sql"] == 1 ? 1 : 0;
        }

        if (!file_exists($wfLog)) throw (new exception("can't find file $wfLog"));
        if (!is_readable($wfLog)) throw (new exception("can't read file $wfLog"));

        //$this->rrmdir(PATH_DATA . "log/propelByIp");  //this line will recursive remove all files
        if ( !is_dir (PATH_DATA . "log/propelByIp") ) {
          mkdir(PATH_DATA . "log/propelByIp");
        }
        if (file_exists($wfLog) && is_readable($wfLog)) {
          $this->aPages = array();
          if ( file_exists($wfPos) ) {
            $pos = intval( file_get_contents( $wfPos ) );
          }
          else {
            $pos = 0;
          }
          $res = $this->processFile($wfLog, $pos);
          $result["error"]    = 0;
          $result["message"]  = $res["message"];
          $result["enabled"]  = $enabled? "true" : "false";
          $result["ipsPages"] = $res["ipsPages"];
          $result["posSize"]  = filesize($wfLog);
          file_put_contents($wfPos, filesize($wfLog));
        }
      }
      catch (Exception $e) {
        $result = array();
        $result["error"] = 1;
        $result["message"] = $e->getMessage();
        $result["data"] = array();
        $result["totalCount"] = 0;
        $result["enabled"]  = $enabled? "true" : "false";
        $result["ipsPages"] = "..";
        $result["posSize"]  = "..";
      }

      $json = G::json_encode($result);

      print $json;
    }

    function logDisableEnable() {
      try {
        $enabled = "no";
        $buffer  = "";
        $wfEnv = PATH_HOME . "engine/config/env.ini";
        G::disableEnableINIvariable($wfEnv, "debug_sql");

        //finally read again the file, in order to get the final value
        $config = @parse_ini_file($wfEnv, false);
        $enabled = $config["debug_sql"];

        $result["enabled"]  = $enabled? "true" : "false";
        $result["message"] = "Changed to " . $result["enabled"];

      }
      catch (Exception $e) {
        $result = array();
        $result["error"] = 1;
        $result["message"] = $e->getMessage();
        $result["enabled"] = $enabled;
      }

      $json = G::json_encode($result);

      print $json;
    }

    function memcachedDisableEnable() {
      try {
      	$enabled = 'no';
      	$buffer  = '';
        $wfEnv = PATH_HOME . 'engine/config/env.ini';
        G::disableEnableINIvariable($wfEnv, 'memcached');

        //finally read again the file, in order to get the final value
        $config = @parse_ini_file($wfEnv, false);
        $enabled = $config['memcached'];

        $result['enabled']  = $enabled ? 'true' : 'false';
        $result['message'] = 'Changed to ' . $result['enabled'] ;

      }
      catch ( Exception $e ) {
        $result = array();
        $result['error'] = 1;
        $result['message'] = $e->getMessage();
        $result['enabled'] = $enabled;
      }
      $json = G::json_encode( $result) ;

      print $json;
    }

    function memcachedClear() {
      try {
        $memcache = &PMmemcached::getSingleton(SYS_SYS);
        $memres = 0;
        if ( $memcache->connected ) {
          $memres = $memcache->flush();
        }
        $result['enabled']  = $memres;
        $result['message'] = 'Memcached cleared';
      }
      catch ( Exception $e ) {
        $result = array();
        $result['error'] = 1;
        $result['message'] = $e->getMessage();
      }
      $json = G::json_encode( $result) ;
      print $json;
    }

    function memcachedEditServer() {
      $result = array();
      $server = isset($_REQUEST['server'])    ? $_REQUEST['server']    : 'localhost';
      try {
        $wfEnv = PATH_HOME . 'engine/config/env.ini';
        G::setINIvariable($wfEnv, 'memcached_server', $server);

        $result['error'] = 0;
        $result['message'] = 'changed server to ' . $server;

      }
      catch ( Exception $e ) {
        $result['error'] = 1;
        $result['message'] = $e->getMessage();
      }
      $json = G::json_encode( $result) ;

      print $json;
    }

    function sphinxDisableEnable() {
      try {
      	$enabled = 'no';
      	$buffer  = '';
        $wfEnv = PATH_HOME . 'engine/config/env.ini';
        G::disableEnableINIvariable($wfEnv, 'sphinx');

        //finally read again the file, in order to get the final value
        $config = @parse_ini_file($wfEnv, false);
        $enabled = $config['sphinx'];

        $result['enabled']  = $enabled ? 'true' : 'false';
        $result['message'] = 'Changed to ' . $result['enabled'] ;

      }
      catch ( Exception $e ) {
        $result = array();
        $result['error'] = 1;
        $result['message'] = $e->getMessage();
        $result['enabled'] = $enabled;
      }
      $json = G::json_encode( $result) ;

      print $json;
    }

    function sphinxClear() {
      try {
    	  $memcache = &PMmemcached::getSingleton(SYS_SYS);
        if ( $memcache->connected ) {
          $memres = $memcache->flush();
        }

        $result['enabled']  = $memres;
        $result['message'] = 'Sphinx cleared';

      }
      catch ( Exception $e ) {
        $result = array();
        $result['error'] = 1;
        $result['message'] = $e->getMessage();
      }
      $json = G::json_encode( $result) ;

      print $json;
    }

    function sphinxEditServer() {
      $result = array();
      $server = isset($_REQUEST['server'])    ? $_REQUEST['server']    : 'localhost';
      try {
        $wfEnv = PATH_HOME . 'engine/config/env.ini';
        G::setINIvariable($wfEnv, 'sphinx_server', $server);

        $result['error'] = 0;
        $result['message'] = 'changed server to ' . $server;

      }
      catch ( Exception $e ) {
        $result['error'] = 1;
        $result['message'] = $e->getMessage();
      }
      $json = G::json_encode( $result) ;

      print $json;
    }

    function refreshSphinx() {
      $result = array();
    	$enabled = 'no';
    	$buffer  = '';

      try {
        $wfEnv = PATH_HOME . 'engine/config/env.ini';
        $config = @parse_ini_file($wfEnv, false);
        if ( !isset($config['sphinx']) ) $config['sphinx'] = 0;
        if ( !isset($config['sphinx_server']) ) $config['sphinx_server'] = '';
        $enabled = $config['sphinx'];
        $server  = $config['sphinx_server'];
        $result['enabled']            = $enabled ? 'true' : 'false';
        if ( $enabled && trim($server) != '' ) {
          G::LoadThirdParty('sphinx', 'sphinxapi');
          $cl = new SphinxClient();
          $cl->SetServer( $server, 9312 );
          $res = $cl->status();
          $result['uptime']           = $res['0'][1];
          $result['connections']      = $res['1'][1];
          $result['command_search']   = $res['3'][1];
          $result['command_excerpt']  = $res['4'][1];
          $result['command_update']   = $res['5'][1];
          $result['command_keywords'] = $res['6'][1];
          $result['command_persist']  = $res['7'][1];
          $result['command_status']   = $res['8'][1];
          $result['sphinx_server']    = $server;
        }
      }
      catch ( Exception $e ) {
        $result['error'] = 1;
        $result['message'] = $e->getMessage();
      }

      $json = G::json_encode( $result) ;
      print $json;
    }

    function gearmanDisableEnable() {
      try {
      	$enabled = 0;
      	$buffer  = '';
        $wfEnv = PATH_HOME . 'engine/config/env.ini';
        G::disableEnableINIvariable($wfEnv, 'gearman');

        //finally read again the file, in order to get the final value
        $config = @parse_ini_file($wfEnv, false);
        $enabled = $config['gearman'];

        $result['enabled']  = $enabled ? 'true' : 'false';
        $result['message'] = 'Changed to ' . $result['enabled'] ;

      }
      catch ( Exception $e ) {
        $result = array();
        $result['error'] = 1;
        $result['message'] = $e->getMessage();
        $result['enabled'] = $enabled;
      }
      $json = G::json_encode( $result) ;

      print $json;
    }

    function gearmanClear() {
      try {
    	  $memcache = &PMmemcached::getSingleton(SYS_SYS);
        if ( $memcache->connected ) {
          $memres = $memcache->flush();
        }

        $result['enabled']  = $memres;
        $result['message'] = 'Sphinx cleared';

      }
      catch ( Exception $e ) {
        $result = array();
        $result['error'] = 1;
        $result['message'] = $e->getMessage();
      }
      $json = G::json_encode( $result) ;

      print $json;
    }

    function gearmanEditServer() {
      $result = array();
      $server = isset($_REQUEST['server'])    ? $_REQUEST['server']    : 'localhost';
      try {
        $wfEnv = PATH_HOME . 'engine/config/env.ini';
        G::setINIvariable($wfEnv, 'gearman_server', $server);

        $result['error'] = 0;
        $result['message'] = 'changed server to ' . $server;

      }
      catch ( Exception $e ) {
        $result['error'] = 1;
        $result['message'] = $e->getMessage();
      }
      $json = G::json_encode( $result) ;

      print $json;
    }

    function refreshGearman() {
      $result = array();

      $support = class_exists('GearmanClient') ? 1 : 0;
      $enabled = 0;
      $result['supported'] = $support ? 'true' : 'false';
      $result['enabled']   = $enabled ? 'true' : 'false';
      $buffer  = '';

      try {
        $wfEnv = PATH_HOME . 'engine/config/env.ini';
        $config = @parse_ini_file($wfEnv, false);
        if ( !isset($config['gearman']) ) $config['gearman'] = 0;
        if ( !isset($config['gearman_server']) ) $config['gearman_server'] = 'xx';
        $enabled = $config['gearman'];
        $server  = $config['gearman_server'];
        $result['enabled'] = $enabled ? 'true' : 'false';
        $result['gearman_server'] = $server;
        if ( $enabled && trim($server) != '' && $support ) {
          $gmclient= new GearmanClient();
          $gmclient->addServer( $server);
          //$res = $gmclient->status();
        }
      }
      catch ( Exception $e ) {
        $result['error']   = 1;
        $result['message'] = $e->getMessage();
      }

      $json = G::json_encode( $result) ;
      print $json;
    }

    function logTotalIp() {
      try {
        $logFile  = PATH_DATA . "log/propelByIp/total.txt";

        if (!file_exists($logFile) || !is_readable($logFile)) throw (new exception("can't read file $logFile"));
        if (file_exists($logFile) && is_readable($logFile)) {
          $totalArray = unserialize(file_get_contents($logFile));
          //format the array to display correctly in the combobox
          $newData = array();
          foreach ( $totalArray as $k => $v ) {
            $newData[] = array('ip' => $v['ip'], 'cant' => $v['ip'] . ' has ' . $v['cant'] . ' pages');
          }
          $result["data"]  = $newData;
          $result["totalCount"] = count($result["data"]);
          $result["error"] = 0;
          $result["message"] = "sucess";
        }
      }
      catch (Exception $e) {
        $result = array();
        $result["data"] = array();
        $result["totalCount"] = 0;
        $result["error"] = 1;
        $result["message"] = $e->getMessage();
      }

      $json = G::json_encode($result) ;

      print $json;
    }

    function logByIp() {
      try {
        $ip = isset($_REQUEST['ip']) ? $_REQUEST['ip'] : '';
        $logFile  = PATH_DATA . "log/propelByIp/$ip.txt";

        if ( !file_exists($logFile) || !is_readable($logFile) ) throw ( new exception("can't read file $logFile"));
        if ( file_exists($logFile) && is_readable($logFile) ) {
          $result = unserialize(file_get_contents($logFile));
          foreach ($result['data'] as $k => $v) {
            $fields = $v['fields'];
            $fields = str_ireplace("\t", '<br>', $fields);
            $result['data'][$k]['fields'] = $fields;
          }
          $result['error'] = 0;
          $result['message'] = 'sucess';
        }
      }
      catch (Exception $e) {
        $result = array();
        $result['error'] = 1;
        $result['message'] = $e->getMessage();
        $result['data'] = array();
        $result['totalCount'] = 0;
      }

      $json = G::json_encode( $result) ;

      print $json;
    }

    function logByIpDetails() {
      try {
        $pid = isset($_REQUEST['pid']) ? $_REQUEST['pid'] : '';
        $ip  = isset($_REQUEST['ip'])  ? $_REQUEST['ip'] : '';
        $logFile  = PATH_DATA . "log/propelByIp/$ip/$pid";

        if ( !file_exists($logFile) || !is_readable($logFile) ) throw ( new exception("can't read file $logFile"));
        if ( file_exists($logFile) && is_readable($logFile) ) {
          $content = file_get_contents($logFile);
          $data = array();
          $fp = fopen ( $logFile,'r');
        	$line = explode('|', fgets($fp) );
          while ( !feof( $fp) ) {
          	$sql = $line[4];
          	$sql = str_ireplace( 'SELECT ',    '<b>SELECT </b><br>',     $sql );
          	$sql = str_ireplace( 'FROM ',      '<br><b>FROM </b><br>',      $sql );
          	$sql = str_ireplace( 'LEFT JOIN ', '<br><b>LEFT JOIN </b><br>', $sql );
          	$sql = str_ireplace( 'WHERE ',     '<br><b>WHERE </b><br>',     $sql );
          	$sql = str_ireplace( 'GROUP BY ',  '<br><b>GROUP BY </b><br>',  $sql );
          	$sql = str_ireplace( 'ORDER BY ',  '<br><b>ORDER BY </b><br>',  $sql );
          	$back = $line[5];
          	$back = str_ireplace( '->',  '<br>',     $back );
          	$node = array();
          	$node['id']        = $line[0];
          	$node['duration']  = $line[1];
          	$node['accum']     = $line[2];
          	$node['sqlTime']   = $line[3];
          	$node['sql']       = $line[4];
          	$node['sqlBold']   = $sql;
          	$node['backtrace'] = $back;
          	$data[] = $node;
          	$line = explode('|', fgets($fp) );
          }
          $result = array();
          $result['error']      = 0;
          $result['data']       = $data;
          $result['totalCount'] = count($data);
          $result['message']    = count($data);
        }
      }
      catch ( Exception $e ) {
        $result = array();
        $result['error'] = 1;
        $result['message'] = $e->getMessage();
        $result['data'] = array();
        $result['totalCount'] = 0;
      }
      $json = G::json_encode( $result) ;

      print $json;
    }

    function logClear() {
      $result = array();
      try {
        $this->rrmdir(PATH_DATA . "log/propelByIp");
        $wfLog = PATH_DATA . "log/propel.log";
        $wfPos = PATH_DATA . "log/propel.pos";

        if (!file_exists($wfLog) || !is_readable($wfLog)) throw (new exception("can't read file $wfLog"));
        //$result["error"] = !unlink($wfLog) || !unlink($wfPos); //generates warning with unlink, in windows

        if (file_exists($wfLog)) {
          file_put_contents($wfLog, null);
        }
        if (file_exists($wfPos)) {
          file_put_contents($wfPos, null);
        }

        $result["error"] = 0;
        $result["message"] = "file $wfLog erased";
      }
      catch ( Exception $e ) {
        $result["error"] = 1;
        $result["message"] = $e->getMessage();
      }

      $json = G::json_encode($result);
      echo $json;
    }

    function refreshMemcached() {
      $result = array();
      try {
        $memcache = &PMmemcached::getSingleton(SYS_SYS);

        if ( $memcache->connected ) {
          $memres = $memcache->getStats();
          if ($memres["cmd_get"] != 0 )
            $percCacheHit = ((real)$memres["get_hits"]/ (real)$memres["cmd_get"] *100);
          else
            $percCacheHit = 0; //to handle the division by zero error
          $percCacheHit = round($percCacheHit,3);
          $percCacheMiss=100-$percCacheHit;

          $MBRead =(real)$memres["bytes_read"]/(1024*1024);
          $MBWrite=(real)$memres["bytes_written"]/(1024*1024) ;
          $MBSize =(real)$memres["limit_maxbytes"]/(1024*1024) ;

          $result['error']         = 0;
          $result['message']       = ''; //$memcache->getStats();
          $result['version']       = $memres['version'];
          $result["uptime"]        = sprintf ( "%2.2f h.", $memres['uptime'] /3660 );
          $result["total_items"]   = $memres['total_items'];
          $result["curr_connections"] = $memres['curr_connections'];
          $result["cmd_get"]       = $memres['cmd_get'];
          $result["cmd_set"]       = $memres['cmd_set'];
          $result["get_hits"]      = sprintf ( "%d  (%2.2f%%)", $memres['get_hits'],   $percCacheHit  );
          $result["get_misses"]    = sprintf ( "%d  (%2.2f%%)", $memres['get_misses'], $percCacheMiss );
          $result["mbytes_read"]   = sprintf ( "%0.2f M", $MBRead  );
          $result["mbytes_write"]  = sprintf ( "%0.2f M", $MBWrite );
          $result["total_mbytes"]  = sprintf ( "%0.2f M", $MBSize  );
          $result["evictions"]  = $memres['evictions'];
        }
        else {
          $result['version']       = 'not connected';
        }
        $wfEnv = PATH_HOME . 'engine/config/env.ini';
        $config = @parse_ini_file($wfEnv, false);
        $enabled = isset($config['memcached']) ? $config['memcached'] : 0;
        $server  = isset($config['memcached_server']) ? $config['memcached_server'] : '';

        //next lines of code were disabling in the ini file the memcached feature,
        //probably this behavoir is not recommended, because automatically is disabling, so I commenting out these lines
        //if ( $enabled && !$memcache->connected ) {
        //  G::disableEnableINIvariable($wfEnv, 'memcached');
        //  $enabled = 0;
        //}
        $result['memcached_server']  = $server;
        $result['supported'] = $memcache->supported;
        $result['enabled']   = $enabled ? 'true' : 'false';

      }
      catch ( Exception $e ) {
        $result['error'] = 1;
        $result['message'] = $e->getMessage();
      }

      $json = G::json_encode( $result) ;
      print $json;
    }

  } //end myAdmin class

  try {
    if (!class_exists("Application")) require_once ("classes/model/Application.php");

    $myAdmin = new myAdmin("workflow");
    $myAdmin->execute();

  }
  catch (Exception $e) {
    print "Error en: " . DB_HOST . " " . $e->getMessage();
    return false;
  }
?>
