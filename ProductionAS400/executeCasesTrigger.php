<?php

    G::loadClass( 'pmTable' );
    G::loadClass ( 'pmFunctions' );

    ini_set ( 'error_reporting', E_ALL );
    ini_set ( 'display_errors', True );

    // load rol user 
    require_once ("classes/model/Users.php");
    
    # Variables
    $users=$_SESSION['USER_LOGGED'];
    $Us = new Users();
    $Roles=$Us->load($users);
    $rolesAdmin = $Roles['USR_ROLE'];
    $idInbox = "DEMANDES";
     
    $query1 = "SELECT ID_TABLE FROM PMT_INBOX_PARENT_TABLE WHERE ID_INBOX = '".$idInbox."' AND ROL_CODE = '".$rolesAdmin."' ";
    $result = executeQuery($query1);
    
    $tableName = $result[1]['ID_TABLE'];
    $fieldsCSV  = isset($_REQUEST["fieldsCSV"])?$_REQUEST["fieldsCSV"]:"";
    list($dataNum, $data) = getDynaformFields($fieldsCSV,$tableName );
    $resultConfig = getConfigCSV($data,$idInbox);
    $matchFields =  json_encode($resultConfig);
    $firstLineHeader = isset($row["IMPCSV_FIRSTLINEHEADER"])? $row["IMPCSV_FIRSTLINEHEADER"]:'on';
    $fileCSV  = "cve_eie001";
    $uidTask = isset($_SESSION['TASK'])?$_SESSION['TASK']:"37394152351a60079af13d3077553116";
    
    // ************** TOT CASES  ****************+
    //$queryTot = executeQuery("SELECT IMPCSV_TOTCASES FROM wf_".$this->workspace.".PMT_IMPORT_CSV_DATA WHERE IMPCSV_IDENTIFY = '$csvIdentify' AND IMPCSV_TABLE_NAME = '$tableName'");
    //$totCasesCSV = $queryTot[1]['IMPCSV_TOTCASES'];
    $informationCSV = getDataCronCSV('off',$fileCSV,0);
    $_SESSION['REQ_DATA_CSV'] = $informationCSV;
    $_SESSION['CSV_FILE_NAME'] = "cve_eie001.csv";
    $typeAction = 'ADD';
    /*G::pr($matchFields);
    G::pr($uidTask);
    G::pr($tableName);
    G::pr($firstLineHeader);
    G::pr($typeAction);
    G::pr($_SESSION['REQ_DATA_CSV']);*/
    $totalCases = importCreateCase($matchFields,$uidTask,$tableName,$firstLineHeader,$typeAction);

    echo G::json_encode(array("success" => true, "message" => "OK", "totalCases" => $totalCases));
    break;  
    //echo G::json_encode(array("success" => true, "total" => $dataNum, "data" => $resultConfig));
    //break;



    function getDataCronCSV($firstLineCsvAs = 'on', $fileCSV, $totCasesCSV) {
      set_include_path(PATH_PLUGINS . 'convergenceList' . PATH_SEPARATOR . get_include_path());

      if (!$handle = fopen(PATH_DOCUMENT . "csvTmp/".$fileCSV.".csv", "r")) {  
        echo "Cannot open file";  
        exit;  
    }

    $csvData = array(); 
    $csvDataIni = array();
    $i = 0;

    while ($data = fgetcsv($handle, 4096, ";"))
    {
            /*             By Nico 28/08/2013 fix Bug on the import Background by CRON with header csv files.
             * 
             * Add this part because when we import by cron a csv with header, all import are the header for value
             * So, after put the original header in the csv temp file in actionCSV.php,
             * we do this to work perfectly 
             * 
             */
            $col = 0;
            if ($firstLineCsvAs == 'on' && $i == 0)
            {
                foreach ($data as $row)
                {
                    $column_csv[] = $row;
                }
            }
            else
            {
                $num = count($data);

                foreach ($data as $row)
                {
                    /* $csvData key is the header for good import after */
                    if ($firstLineCsvAs == 'on')
                    {
                        if ($totCasesCSV <= $i)
                            $csvDataIni[$column_csv[$col]] = $row;

                        $col++;
                    }
                    else /* No header on csv files */
                    {
                        if ($totCasesCSV <= $i)
                            $csvDataIni[] = $row;
                    }
                }
                if ($totCasesCSV <= $i)
                    $csvData[] = $csvDataIni;
                $csvDataIni = '';
            }
            $i++;
    }
      return $csvData;        
  }