<?php
class CaseLibrary
{
  public static function processUserGroupSQL($option, $process_uid)
  {
    //TU_RELATION = 1 => user
    //TU_RELATION = 2 => group
      
    $sql = "SELECT DISTINCT TU.USR_UID
            FROM   (SELECT TAS_UID
                    FROM   TASK
                    WHERE  TASK.PRO_UID = '$process_uid'
                   ) AS TASK1,
                   TASK_USER AS TU
            WHERE  TASK1.TAS_UID = TU.TAS_UID AND TU.TU_RELATION = " . (($option == 1)? 1 : 2);

    return ($sql);
  }
  
  public static function caseData($option, $category, $appStatus, $appdelStatus, $process_uid, $task_uid, $user_uid, $group_uid, $department_uid)
  {
    require_once ("classes/model/Process.php");
    require_once ("classes/model/TaskUser.php");
    require_once ("classes/model/AppCacheView.php");
    
    $cnn = Propel::getConnection("workflow");
    $stmt = $cnn->createStatement();
    
    $result = array();

    ///////
    //$sql = "SELECT STR_TO_DATE(DATE_FORMAT(APP_CREATE_DATE, '$strDateField'), '%Y-%m-%d %H:%i:%s') AS CASE_DATEC, COUNT(APP_CREATE_DATE) AS CASE_NUM
    //        FROM   APPLICATION
    //        GROUP BY CASE_DATEC
    //        ORDER BY CASE_DATEC ASC";
    
    //APPLICATION.APP_STATUS           -> DRAFT, TO_DO, COMPLETED, ALL
    //or
    //APP_DELEGATION.DEL_THREAD_STATUS -> OPEN, CLOSED, ALL
    
    //APP_DELAY //Table //Cases paused
    
    ///////
    //$sqlAppDel = "SELECT DISTINCT APPDEL.APP_UID
    //              FROM   APP_DELEGATION AS APPDEL
    //              WHERE  APPDEL.USR_UID = '$user_uid'" . (($appdelStatus != "ALL")? " AND APPDEL.DEL_THREAD_STATUS = '$appdelStatus'" : null);
    
    //DISTINCT, the user may have participated in the same case several times
    
    ///////
    $sqlAppDel = null;
    $sqlWHERE = null;
    $sqlAND = null;
    
    $sqlAppDel = "SELECT APPDELCASE.APP_UID, APPDELCASE.DEL_INDEX, APPDELCASE.TAS_UID, APPDELCASE.USR_UID, APPDELCASE.DEL_TASK_DUE_DATE
                  FROM   APP_DELEGATION AS APPDELCASE";
      
    if ($user_uid != null) {
      //$sqlAppDel = $sqlAppDel . "";
    }
    if ($group_uid != null) {
      $sqlAppDel = $sqlAppDel . ", (SELECT DISTINCT GRPUSR1.USR_UID
                                    FROM   GROUP_USER AS GRPUSR1
                                    WHERE  GRPUSR1.GRP_UID = '$group_uid'
                                   ) AS GRPUSR1";
    }
    if ($department_uid != null) {
      $sqlAppDel = $sqlAppDel . ", (SELECT USR1.USR_UID
                                    FROM   DEPARTMENT AS DEPT1,
                                           USERS AS USR1
                                    WHERE  DEPT1.DEP_UID = '$department_uid' AND DEPT1.DEP_UID = USR1.DEP_UID
                                   ) AS DEPTUSR1";
    }
    
    ///////
    $sw = 0;
    
    if ($user_uid != null) {
      $sqlAppDel = $sqlAppDel . (($sw == 0)? " WHERE " : " AND ") . "APPDELCASE.USR_UID = '$user_uid'";
      $sw = 1;
    }
    if ($group_uid != null) {
      $sqlAppDel = $sqlAppDel . (($sw == 0)? " WHERE " : " AND ") . "APPDELCASE.USR_UID = GRPUSR1.USR_UID";
      $sw = 1;
    }
    if ($department_uid != null) {
      $sqlAppDel = $sqlAppDel . (($sw == 0)? " WHERE " : " AND ") . "APPDELCASE.USR_UID = DEPTUSR1.USR_UID";
      $sw = 1;
    }
    
    $sqlWHERE = ($sw == 0)? " WHERE " : null;
    $sqlAND   = ($sw == 1)? " AND " : null;
    
    ///////
    $swAND = 0;
    
    switch ($appStatus) {
      case "TO_DO":
        $sqlAppDel = $sqlAppDel . $sqlWHERE . $sqlAND . "APPDELCASE.DEL_THREAD_STATUS = 'OPEN' AND APPDELCASE.DEL_PREVIOUS > 0 AND APPDELCASE.USR_UID <> ''";
        $swAND = 1;
        break;
      
      case "DRAFT":
        $sqlAppDel = $sqlAppDel . $sqlWHERE . $sqlAND . "APPDELCASE.DEL_THREAD_STATUS = 'OPEN' AND APPDELCASE.APP_UID NOT IN (SELECT APPDEL1.APP_UID
                                                                                                                              FROM   APP_DELEGATION AS APPDEL1
                                                                                                                              WHERE  APPDEL1.APP_UID = APPDELCASE.APP_UID AND APPDEL1.DEL_PREVIOUS > 0
                                                                                                                             )";
        $swAND = 1;
        break;
      
      case "COMPLETED":
        $sqlAppDel = $sqlAppDel . $sqlWHERE . $sqlAND . "APPDELCASE.DEL_THREAD_STATUS = 'CLOSED' AND APPDELCASE.APP_UID NOT IN ($sqlAppDel $sqlWHERE $sqlAND APPDELCASE.DEL_THREAD_STATUS = 'OPEN')";
        $swAND = 1;
        break;
      
      case "UNASSIGNED":
        $str = null;
        $sw = 0;
      
        ///////
        if ($user_uid != null) {
          //USER
          $appCacheView = new AppCacheView();
          
          $task = $appCacheView->getSelfServiceTasks($user_uid);
      
          for ($i = 0; $i <= count($task) - 1; $i++) {
            $str = $str . (($sw == 1)? ", " : null) . "'" . $task[$i] . "'";
            $sw = 1;
          }
        }
        //else {
        //  //PROCESS, GROUP, DEPARTMENT, OVERDUE
        //}
        
        ///////
        if ($user_uid == null && $process_uid != null) {
          //PROCESS
          
          //Users
          $sqlUSR = "SELECT PROUSR.USR_UID
                     FROM   (" . self::processUserGroupSQL(1, $process_uid) . ") AS PROUSR";
          
          //Groups get Users //PROGRP.USR_UID => PROGRP.GRP_UID
          $sqlGRPUSR = "SELECT DISTINCT GRPUSR.USR_UID
                        FROM   (" . self::processUserGroupSQL(2, $process_uid) . ") AS PROGRP,
                               GROUP_USER AS GRPUSR
                        WHERE  PROGRP.USR_UID = GRPUSR.GRP_UID";
      
          //$sql="($sqlUSR) UNION ALL ($sqlGRPUSR)"; //UNION ALL //all records
          $sql = "($sqlUSR) UNION ($sqlGRPUSR)";   //UNION     //!= records
      
          $rsSQL = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);
          if ($rsSQL->getRecordCount() > 0) {
            $appCacheView = new AppCacheView();
            
            while ($rsSQL->next()) {
              $row = $rsSQL->getRow();
      
              $grpusr_user_uid = $row["USR_UID"];
      
              ///////
              $task = $appCacheView->getSelfServiceTasks($grpusr_user_uid);
      
              for ($i = 0; $i <= count($task) - 1; $i++) {
                $str = $str . (($sw == 1)? ", " : null) . "'" . $task[$i] . "'";
                $sw = 1;
              }
            }
          }
        }
        
        ///////
        if ($user_uid == null && $group_uid != null) {
          //GROUP
          $sql = "SELECT DISTINCT GRPUSR.USR_UID
                  FROM   GROUP_USER AS GRPUSR
                  WHERE  GRPUSR.GRP_UID = '$group_uid'";
          $rsSQL = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);
          if ($rsSQL->getRecordCount() > 0) {
            $appCacheView = new AppCacheView();
            
            while ($rsSQL->next()) {
              $row = $rsSQL->getRow();
          
              $grpusr_user_uid = $row["USR_UID"];
              
              ///////
              $task = $appCacheView->getSelfServiceTasks($grpusr_user_uid);
          
              for ($i = 0; $i <= count($task) - 1; $i++) {
                $str = $str . (($sw == 1)? ", " : null) . "'" . $task[$i] . "'";
                $sw = 1;
              }
            }
          }
        }
        
        ///////
        if ($user_uid == null && $department_uid != null) {
          //DEPARTMENT
          $sql = "SELECT USR.USR_UID
                  FROM   DEPARTMENT AS DEPT,
                         USERS AS USR
                  WHERE  DEPT.DEP_UID = '$department_uid' AND DEPT.DEP_UID = USR.DEP_UID";
          $rsSQL = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);
          if ($rsSQL->getRecordCount() > 0) {
            $appCacheView = new AppCacheView();
            
            while ($rsSQL->next()) {
              $row = $rsSQL->getRow();
        
              $grpusr_user_uid = $row["USR_UID"];
              
              ///////
              $task = $appCacheView->getSelfServiceTasks($grpusr_user_uid);
        
              for ($i = 0; $i <= count($task) - 1; $i++) {
                $str = $str . (($sw == 1)? ", " : null) . "'" . $task[$i] . "'";
                $sw = 1;
              }
            }
          }
        }
        
        ///////
        $sqlAppDel = "SELECT APPDELCASE.APP_UID
                      FROM   APP_DELEGATION AS APPDELCASE
                      WHERE  APPDELCASE.DEL_THREAD_STATUS = 'OPEN' AND APPDELCASE.USR_UID = '' AND " . (($str != null)? "APPDELCASE.TAS_UID IN ($str)" : "1 <> 1");
        
        $swAND = 1;
        break;
        
      case "PAUSED":
        //$sqlAppDel = $sqlAppDel . $sqlWHERE . $sqlAND . "APPDELCASE.APP_UID IN (SELECT APPDELAY.APP_UID
        //                                                                        FROM   APP_DELAY AS APPDELAY
        //                                                                        WHERE  APPDELAY.APP_TYPE = 'PAUSE' AND APPDELAY.APP_DISABLE_ACTION_USER = '0'
        //                                                                       )";
        
        $sqlAppDel = $sqlAppDel . $sqlWHERE . $sqlAND . "APPDELCASE.APP_UID IN (SELECT APPDELAY.APP_UID
                                                                                FROM   APP_DELAY AS APPDELAY
                                                                                WHERE  APPDELAY.APP_DELAY_UID IS NOT NULL AND
                                                                                       APPDELAY.APP_TYPE NOT IN ('REASSIGN', 'ADHOC', 'CANCEL') AND
                                                                                       (APPDELAY.APP_DISABLE_ACTION_USER IS NULL OR APPDELAY.APP_DISABLE_ACTION_USER = '0')
                                                                               )";
        $swAND = 1;
        break;
      
      default:
        //if $appdelStatus = ALL, then "Participated"
        //if $appdelStatus = CLOSED, then "casesInbox + casesCompleted"
        
        if ($appdelStatus != "ALL") {
          $sqlAppDel = $sqlAppDel . $sqlWHERE . $sqlAND . "APPDELCASE.DEL_THREAD_STATUS = '$appdelStatus'";
          $swAND = 1;
        }
        break;
    }
    
    ///////
    switch ($category) {
      case "OVERDUE":    $sqlAppDel = $sqlAppDel . (($swAND == 1)? " AND " : $sqlWHERE . $sqlAND) . "APPDELCASE.DEL_TASK_DUE_DATE <= NOW()"; break;
      case "OVERDUENOT": $sqlAppDel = $sqlAppDel . (($swAND == 1)? " AND " : $sqlWHERE . $sqlAND) . "NOW() <= APPDELCASE.DEL_TASK_DUE_DATE"; break;
    }
    
    //////
    switch ($appStatus) {
      case "TO_DO": break;
      case "DRAFT": break;
      case "COMPLETED": break;
      case "UNASSIGNED": break;
      case "PAUSED": break;
      
      default:
        //if $appdelStatus = ALL, then "Participated"
        //if $appdelStatus = CLOSED, then "casesInbox + casesCompleted"
        
        //if $appdelStatus = ALL, then "Participated"
        //SQL in PROCESSMAKER
        //
        //processmaker/workflow/engine/classes/model/AppCacheView.php
        //
        //SELECT *, APP_CACHE_VIEW.DEL_INIT_DATE
        //FROM APP_CACHE_VIEW
        //WHERE APP_CACHE_VIEW.USR_UID='00000000000000000000000000000001'
        //GROUP BY APP_CACHE_VIEW.APP_UID
        //ORDER BY APP_CACHE_VIEW.APP_NUMBER DESC
        
        
        $sqlAppDel = $sqlAppDel . " GROUP BY APPDELCASE.APP_UID"; //problems
        break;
    }
    
    ///////
    $sqlSELECT = null;
    $sqlORDERGROUPBY = null;
    
    if ($option == 0) {
      switch ($category) {
        case "CASE":
          $sqlSELECT = "SELECT COUNT(APP.APP_UID) AS NUMREC";
          $sqlORDERGROUPBY = null;
          break;

        case "PROCESS":
          $sqlSELECT = "SELECT APP.PRO_UID, COUNT(APP.PRO_UID) AS NUMREC";
          $sqlORDERGROUPBY = " GROUP BY APP.PRO_UID";
          break;
    
        case "TASK":
          $sqlSELECT = "SELECT APPDELCASE.TAS_UID, COUNT(APPDELCASE.TAS_UID) AS NUMREC";
          $sqlORDERGROUPBY = " GROUP BY APPDELCASE.TAS_UID";
          break;
    
        case "USER":
          $sqlSELECT = "SELECT APPDELCASE.USR_UID, COUNT(APPDELCASE.USR_UID) AS NUMREC";
          $sqlORDERGROUPBY = " GROUP BY APPDELCASE.USR_UID";
          break;
    
        case "GROUP":
          $sqlSELECT = "SELECT GRPUSR.GRP_UID, COUNT(GRPUSR.GRP_UID) AS NUMREC
                        FROM   (SELECT GRPUSR.GRP_UID, GRPUSR.USR_UID
                                FROM   GROUP_USER AS GRPUSR
                               ) AS GRPUSR,
                               
                               (SELECT APPDELCASE.USR_UID";
                               
          $sqlORDERGROUPBY = " ) AS APPCASE
                        WHERE  GRPUSR.USR_UID = APPCASE.USR_UID
                        GROUP BY GRPUSR.GRP_UID";
          break;
         
        case "DEPARTMENT":
          $sqlSELECT = "SELECT DEPTUSR.DEP_UID, COUNT(DEPTUSR.DEP_UID) AS NUMREC
                        FROM   (SELECT DEPT.DEP_UID, USR.USR_UID
                                FROM   DEPARTMENT AS DEPT,
                                       USERS AS USR
                                WHERE  DEPT.DEP_UID = USR.DEP_UID
                               ) AS DEPTUSR,
                               
                               (SELECT APPDELCASE.USR_UID";
                               
          $sqlORDERGROUPBY = " ) AS APPCASE
                        WHERE  DEPTUSR.USR_UID = APPCASE.USR_UID
                        GROUP BY DEPTUSR.DEP_UID";
          break;
      
        case "OVERDUE":
        case "OVERDUENOT":
          $sqlSELECT = "SELECT COUNT(APP.APP_UID) AS NUMREC";
          $sqlORDERGROUPBY = null;
          break;
      }
    }
    else {
      $sqlSELECT = "SELECT APP.APP_UID, APP.APP_STATUS, APP.APP_NUMBER, APP.PRO_UID, APPDELCURRENT.DEL_INDEX, APPDELCURRENT.TAS_UID, APPDELCURRENT.USR_UID, APPDELCURRENT.DEL_TASK_DUE_DATE";
      $sqlORDERGROUPBY = " ORDER BY APP.APP_NUMBER DESC";
    }
    
    ///////
    $sqlAPP = $sqlSELECT. " FROM  APPLICATION AS APP,
                                  ($sqlAppDel) AS APPDELCASE,
                                  (SELECT APPDELCURRENT.APP_UID, APPDELCURRENT.DEL_INDEX, APPDELCURRENT.TAS_UID, APPDELCURRENT.USR_UID, APPDELCURRENT.DEL_TASK_DUE_DATE
                                   FROM   APP_DELEGATION AS APPDELCURRENT
                                   WHERE  APPDELCURRENT.DEL_INDEX = (SELECT MAX(APPDEL1.DEL_INDEX)
                                                                     FROM   APP_DELEGATION AS APPDEL1
                                                                     WHERE  APPDEL1.APP_UID = APPDELCURRENT.APP_UID
                                                                    )
                                  ) AS APPDELCURRENT
                            WHERE " . (($process_uid != null)? "APP.PRO_UID = '$process_uid' AND " : null) . "
                                  APP.APP_UID = APPDELCASE.APP_UID AND
                                  " . (($task_uid != null)? "APPDELCASE.TAS_UID = '$task_uid' AND " : null) . "
                                  APP.APP_UID = APPDELCURRENT.APP_UID
                          " . $sqlORDERGROUPBY;

    ///////
    $rsSQLAPP = $stmt->executeQuery($sqlAPP, ResultSet::FETCHMODE_ASSOC);
    $numRec = $rsSQLAPP->getRecordCount();
    
    if ($numRec > 0) {
      if ($option == 0) {
        switch ($category) {
          case "CASE":
            while ($rsSQLAPP->next()) {
              $row = $rsSQLAPP->getRow();
        
              $appCaseNumRec = $row["NUMREC"];
            
              ///////
              $result[] = $appCaseNumRec;
            }
            break;

          case "PROCESS":
            while ($rsSQLAPP->next()) {
              $row = $rsSQLAPP->getRow();
        
              $app_process_uid = $row["PRO_UID"];
              $appProNumRec = $row["NUMREC"];
            
              ///////
              $proName = null;
        
              $sql = "SELECT CON.CON_VALUE
                      FROM   CONTENT AS CON
                      WHERE  CON.CON_ID = '$app_process_uid' AND CON.CON_CATEGORY = 'PRO_TITLE'";
              $rsSQL = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);
              if ($rsSQL->getRecordCount() > 0) {
                $rsSQL->next();
                $row = $rsSQL->getRow();
            
                $proName = $row["CON_VALUE"];
              }
            
              ///////
              $result[] = array($app_process_uid, $proName, $appProNumRec);
            }
            break;
      
          case "TASK":
            while ($rsSQLAPP->next()) {
              $row = $rsSQLAPP->getRow();
        
              $appdel_task_uid = $row["TAS_UID"];
              $appTaskNumRec = $row["NUMREC"];
            
              ///////
              $taskName = null;
        
              $sql = "SELECT CON.CON_VALUE
                      FROM   CONTENT AS CON
                      WHERE  CON.CON_ID = '$appdel_task_uid' AND CON.CON_CATEGORY = 'TAS_TITLE'";
              $rsSQL = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);
              if ($rsSQL->getRecordCount() > 0) {
                $rsSQL->next();
                $row = $rsSQL->getRow();
            
                $taskName = $row["CON_VALUE"];
              }
            
              ///////
              $result[] = array($appdel_task_uid, $taskName, $appTaskNumRec);
            }
            break;
       
          case "USER":
            while ($rsSQLAPP->next()) {
              $row = $rsSQLAPP->getRow();
        
              $appdel_user_uid = $row["USR_UID"];
              $appUsrNumRec = $row["NUMREC"];
            
              ///////
              $usrName = null;
        
              $sql = "SELECT USR.USR_UID, USR.USR_FIRSTNAME, USR.USR_LASTNAME
                      FROM   USERS AS USR
                      WHERE  USR.USR_UID = '$appdel_user_uid'";
              $rsSQL = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);
              if ($rsSQL->getRecordCount() > 0) {
                $rsSQL->next();
                $row = $rsSQL->getRow();
        
                $usrName = ((!empty($row["USR_FIRSTNAME"]))? $row["USR_FIRSTNAME"] : null) . ((!empty($row["USR_LASTNAME"]))? " " . $row["USR_LASTNAME"] : null);
              }
            
              ///////
              $result[] = array($appdel_user_uid, $usrName, $appUsrNumRec);
            }
            break;
       
          case "GROUP":
            while ($rsSQLAPP->next()) {
              $row = $rsSQLAPP->getRow();
        
              $app_group_uid = $row["GRP_UID"];
              $appGrpNumRec = $row["NUMREC"];
            
              ///////
              $grpName = null;
        
              $sql = "SELECT CON.CON_VALUE
                      FROM   CONTENT AS CON
                      WHERE  CON.CON_ID = '$app_group_uid' AND CON.CON_CATEGORY = 'GRP_TITLE'";
              $rsSQL = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);
              if ($rsSQL->getRecordCount() > 0) {
                $rsSQL->next();
                $row = $rsSQL->getRow();
        
                $grpName = $row["CON_VALUE"];
              }
            
              ///////
              $result[] = array($app_group_uid, $grpName, $appGrpNumRec);
            }
            break;
       
          case "DEPARTMENT":
            while ($rsSQLAPP->next()) {
              $row = $rsSQLAPP->getRow();
        
              $app_department_uid = $row["DEP_UID"];
              $appDeptNumRec = $row["NUMREC"];
            
              ///////
              $deptName = null;
        
              $sql = "SELECT CON.CON_VALUE
                      FROM   CONTENT AS CON
                      WHERE  CON.CON_ID = '$app_department_uid' AND CON.CON_CATEGORY = 'DEPO_TITLE'";
              $rsSQL = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);
              if ($rsSQL->getRecordCount() > 0) {
                $rsSQL->next();
                $row = $rsSQL->getRow();
        
                $deptName = $row["CON_VALUE"];
              }
            
              ///////
              $result[] = array($app_department_uid, $deptName, $appDeptNumRec);
            }
            break;
       
          case "OVERDUE":
          case "OVERDUENOT":
            while ($rsSQLAPP->next()) {
              $row = $rsSQLAPP->getRow();
        
              $appCaseNumRec = $row["NUMREC"];
            
              ///////
              $result[] = $appCaseNumRec;
            }
            break;
        }
      }
      else {
        //List
        while ($rsSQLAPP->next()) {
          $row = $rsSQLAPP->getRow();
        
          $application_uid = $row["APP_UID"];
          $appStatus = $row["APP_STATUS"];
          $appNumber = $row["APP_NUMBER"];
          $app_process_uid = $row["PRO_UID"];

          $appdelcurrentDelIndex = $row["DEL_INDEX"];
          $appdelcurrent_task_uid = $row["TAS_UID"];
          $appdelcurrent_user_uid = $row["USR_UID"];
          $appdelcurrentDelTaskDueDate = $row["DEL_TASK_DUE_DATE"];
        
          ///////
          $proName = null;
        
          $sql = "SELECT CON.CON_VALUE
                  FROM   CONTENT AS CON
                  WHERE  CON.CON_ID = '$app_process_uid' AND CON.CON_CATEGORY = 'PRO_TITLE'";
          $rsSQL = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);
          if ($rsSQL->getRecordCount() > 0) {
            $rsSQL->next();
            $row = $rsSQL->getRow();
            
            $proName = $row["CON_VALUE"];
          }
        
          ///////
          $taskName = null;
        
          $sql = "SELECT CON.CON_VALUE
                  FROM   CONTENT AS CON
                  WHERE  CON.CON_ID = '$appdelcurrent_task_uid' AND CON.CON_CATEGORY = 'TAS_TITLE'";
          $rsSQL = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);
          if ($rsSQL->getRecordCount() > 0) {
            $rsSQL->next();
            $row = $rsSQL->getRow();
            
            $taskName = $row["CON_VALUE"];
          }
        
          ///////
          $sentby_user_uid = null; //Sent by
          $sentbyUsrName = null;
        
          $sql = "SELECT USR.USR_UID, USR.USR_FIRSTNAME, USR.USR_LASTNAME
                  FROM   USERS AS USR
                  WHERE  USR.USR_UID = (SELECT APPDEL.USR_UID
                                        FROM   APP_DELEGATION AS APPDEL
                                        WHERE  APPDEL.APP_UID = '$application_uid' AND
                                               APPDEL.DEL_INDEX = (SELECT MAX(APPDEL1.DEL_INDEX)
                                                                   FROM   APP_DELEGATION AS APPDEL1
                                                                   WHERE  APPDEL1.APP_UID = '$application_uid'
                                                                  ) - 1
                                       )";
          $rsSQL = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);
          if ($rsSQL->getRecordCount() > 0) {
            $rsSQL->next();
            $row = $rsSQL->getRow();
        
            $sentby_user_uid = $row["USR_UID"];
              
            $sentbyUsrName = ((!empty($row["USR_FIRSTNAME"]))? $row["USR_FIRSTNAME"] : null) . ((!empty($row["USR_LASTNAME"]))? " " . $row["USR_LASTNAME"] : null);
          }
            
          ///////
          $appdelcurrentUsrName = null;
          
          $sql = "SELECT USR.USR_UID, USR.USR_FIRSTNAME, USR.USR_LASTNAME
                  FROM   USERS AS USR
                  WHERE  USR.USR_UID = '$appdelcurrent_user_uid'";
          $rsSQL = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);
          if ($rsSQL->getRecordCount() > 0) {
            $rsSQL->next();
            $row = $rsSQL->getRow();
        
            $appdelcurrentUsrName = ((!empty($row["USR_FIRSTNAME"]))? $row["USR_FIRSTNAME"] : null) . ((!empty($row["USR_LASTNAME"]))? " " . $row["USR_LASTNAME"] : null);
          }
        
          ///////
          $result[] = array($application_uid, $appStatus, $appNumber, $app_process_uid, $proName, $appdelcurrentDelIndex, $appdelcurrent_task_uid, $taskName, $sentby_user_uid, $sentbyUsrName, $appdelcurrent_user_uid, $appdelcurrentUsrName, $appdelcurrentDelTaskDueDate);
        }
      }
    }
   
    return ($result);
  }
}
?>