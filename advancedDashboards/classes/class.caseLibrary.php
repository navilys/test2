<?php
class CaseLibrary
{
    public static function processUserGroupSql($option, $processUid)
    {
        //TU_RELATION = 1 => user
        //TU_RELATION = 2 => group

        $sql = "SELECT DISTINCT TU.USR_UID
                FROM   (SELECT TAS_UID
                        FROM   TASK
                        WHERE  TASK.PRO_UID = '$processUid'
                       ) AS TASK1,
                       TASK_USER AS TU
                WHERE  TASK1.TAS_UID = TU.TAS_UID AND TU.TU_RELATION = " . (($option == 1)? 1 : 2);

        return $sql;
    }

    public static function caseDataSql($option, $category, $status, $delStatus, $dateIni, $dateEnd, $arrayConfigData, $processUid, $taskUid, $userUid, $groupUid, $departmentUid)
    {
        $cnn = Propel::getConnection("workflow");
        $stmt = $cnn->createStatement();

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
        //              WHERE  APPDEL.USR_UID = '$userUid'" . (($delStatus != "ALL")? " AND APPDEL.DEL_THREAD_STATUS = '$delStatus'" : null);

        //DISTINCT, the user may have participated in the same case several times

        //Users
        $sqlUserUid = null;

        if ($sqlUserUid == null && $userUid != null) {
            $sqlUserUid = $sqlUserUid . (($sqlUserUid != null)? ", " : null) . "'$userUid'";
        }

        if ($sqlUserUid == null && $groupUid != null) {
            $sql = "SELECT GRPUSR.USR_UID
                    FROM   GROUP_USER AS GRPUSR
                    WHERE  GRPUSR.GRP_UID = '$groupUid'";

            $rsSql = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);

            while ($rsSql->next()) {
                $row = $rsSql->getRow();

                $grpusrUserUid = $row["USR_UID"];

                $sqlUserUid = $sqlUserUid . (($sqlUserUid != null)? ", " : null) . "'$grpusrUserUid'";
            }

            $sqlUserUid = ($sqlUserUid != null)? $sqlUserUid : "'000000000000000000nonexistentuid'";
        }

        if ($sqlUserUid == null && $departmentUid != null) {
            $sql = "SELECT USR.USR_UID
                    FROM   USERS AS USR
                    WHERE  USR.DEP_UID = '$departmentUid'";

            $rsSql = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);

            while ($rsSql->next()) {
                $row = $rsSql->getRow();

                $depusrUserUid = $row["USR_UID"];

                $sqlUserUid = $sqlUserUid . (($sqlUserUid != null)? ", " : null) . "'$depusrUserUid'";
            }

            $sqlUserUid = ($sqlUserUid != null)? $sqlUserUid : "'000000000000000000nonexistentuid'";
        }

        $sqlUserUid = ($sqlUserUid != null)? "APPDELCASE.USR_UID IN ($sqlUserUid)" : null;

        //SQL condition
        $sqlAppDelCondition = null;
        $sqlAppDelCondition = $sqlAppDelCondition . (($processUid != null)? (($sqlAppDelCondition != null)? " AND " : null) . "APPDELCASE.PRO_UID = '$processUid'" : null);
        $sqlAppDelCondition = $sqlAppDelCondition . (($taskUid != null)? (($sqlAppDelCondition != null)? " AND " : null) . "APPDELCASE.TAS_UID = '$taskUid'" : null);

        $sqlAppDelConditionAndUser = $sqlAppDelCondition . (($sqlUserUid != null)? (($sqlAppDelCondition != null)? " AND " : null) . $sqlUserUid : null);

        $sqlDelIndexCondition = "APPDELCASE.DEL_INDEX = (SELECT MAX(APPDEL2.DEL_INDEX)
                                                         FROM   APP_DELEGATION AS APPDEL2
                                                         WHERE  APPDEL2.APP_UID = APPDELCASE.APP_UID
                                                        )";

        switch ($status) {
            case "TO_DO":
                $sqlAppDelCondition = $sqlAppDelConditionAndUser . (($sqlAppDelConditionAndUser != null)? " AND " : null) . "APPDELCASE.DEL_THREAD_STATUS = 'OPEN' AND APPDELCASE.DEL_PREVIOUS > 0 AND APPDELCASE.USR_UID <> ''";
                break;
            case "DRAFT":
                $sqlAppDelCondition = $sqlAppDelConditionAndUser . (($sqlAppDelConditionAndUser != null)? " AND " : null) . "APPDELCASE.DEL_THREAD_STATUS = 'OPEN' AND APPDELCASE.APP_UID NOT IN (SELECT APPDEL2.APP_UID
                                                                                                                                                                                                  FROM   APP_DELEGATION AS APPDEL2
                                                                                                                                                                                                  WHERE  APPDEL2.APP_UID = APPDELCASE.APP_UID AND APPDEL2.DEL_PREVIOUS > 0
                                                                                                                                                                                                 )";
                break;
            case "PAUSED":
                //$sqlAppDel = $sqlAppDel . $sqlAppDelWhere . $sqlAppDelAnd . "APPDELCASE.APP_UID IN (SELECT APPDELAY.APP_UID
                //                                                                                    FROM   APP_DELAY AS APPDELAY
                //                                                                                    WHERE  APPDELAY.APP_TYPE = 'PAUSE' AND APPDELAY.APP_DISABLE_ACTION_USER = '0'
                //                                                                                   )";

                $sqlAppDelCondition = $sqlAppDelConditionAndUser . (($sqlAppDelConditionAndUser != null)? " AND " : null) . "APPDELCASE.APP_UID IN (SELECT APPDELAY.APP_UID
                                                                                                                                                    FROM   APP_DELAY AS APPDELAY
                                                                                                                                                    WHERE  APPDELAY.APP_UID = APPDELCASE.APP_UID AND APPDELAY.APP_DEL_INDEX = APPDELCASE.DEL_INDEX AND
                                                                                                                                                           APPDELAY.APP_DELAY_UID IS NOT NULL AND
                                                                                                                                                           APPDELAY.APP_TYPE NOT IN ('REASSIGN', 'ADHOC', 'CANCEL') AND
                                                                                                                                                           (APPDELAY.APP_DISABLE_ACTION_USER IS NULL OR APPDELAY.APP_DISABLE_ACTION_USER = '0')
                                                                                                                                                   )";
                break;
            //case "CANCELLED":
            //    $sqlAppDelCondition = "$sqlUserUid AND APPDELCASE.DEL_THREAD_STATUS = 'CLOSED'";
            //    break;
            case "COMPLETED":
                $sqlAppDelCondition = $sqlAppDelConditionAndUser . (($sqlAppDelConditionAndUser != null)? " AND " : null) . "APPDELCASE.DEL_THREAD_STATUS = 'CLOSED' AND APPDELCASE.APP_UID NOT IN (SELECT APPDEL2.APP_UID
                                                                                                                                                                                                    FROM   APP_DELEGATION AS APPDEL2
                                                                                                                                                                                                    WHERE  APPDEL2.APP_UID = APPDELCASE.APP_UID AND APPDEL2.DEL_THREAD_STATUS = 'OPEN'
                                                                                                                                                                                                   )";
                break;
            case "UNASSIGNED":
                $appCacheView = new AppCacheView();

                $sqlTaskUid = null;
                $swUid = 0;

                //User
                if ($userUid != null) {
                    $swUid = 1;

                    $task = $appCacheView->getSelfServiceTasks($userUid);

                    for ($i = 0; $i <= count($task) - 1; $i++) {
                        $sqlTaskUid = $sqlTaskUid . (($sqlTaskUid != null)? ", " : null) . "'" . $task[$i] . "'";
                    }
                }
                //else {
                //    //PROCESS, GROUP, DEPARTMENT, OVERDUE
                //}

                //Process
                if ($userUid == null && $processUid != null) {
                    $swUid = 1;

                    //Users
                    $sqlUsr = "SELECT PROUSR.USR_UID
                               FROM   (" . self::processUserGroupSql(1, $processUid) . ") AS PROUSR";

                    //Groups get Users //PROGRP.USR_UID => PROGRP.GRP_UID
                    $sqlGrpUsr = "SELECT DISTINCT GRPUSR.USR_UID
                                  FROM   (" . self::processUserGroupSql(2, $processUid) . ") AS PROGRP,
                                         GROUP_USER AS GRPUSR
                                  WHERE  PROGRP.USR_UID = GRPUSR.GRP_UID";

                    //$sql="($sqlUsr) UNION ALL ($sqlGrpUsr)"; //UNION ALL //all records
                    $sql = "($sqlUsr) UNION ($sqlGrpUsr)";   //UNION     //!= records

                    $rsSql = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);

                    while ($rsSql->next()) {
                        $row = $rsSql->getRow();

                        $proUserUid = $row["USR_UID"];

                        $task = $appCacheView->getSelfServiceTasks($proUserUid);

                        for ($i = 0; $i <= count($task) - 1; $i++) {
                            $sqlTaskUid = $sqlTaskUid . (($sqlTaskUid != null)? ", " : null) . "'" . $task[$i] . "'";
                        }
                    }
                }

                //Group
                if ($userUid == null && $groupUid != null) {
                    $swUid = 1;

                    $sql = "SELECT GRPUSR.USR_UID
                            FROM   GROUP_USER AS GRPUSR
                            WHERE  GRPUSR.GRP_UID = '$groupUid'";

                    $rsSql = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);

                    while ($rsSql->next()) {
                        $row = $rsSql->getRow();

                        $grpusrUserUid = $row["USR_UID"];

                        $task = $appCacheView->getSelfServiceTasks($grpusrUserUid);

                        for ($i = 0; $i <= count($task) - 1; $i++) {
                            $sqlTaskUid = $sqlTaskUid . (($sqlTaskUid != null)? ", " : null) . "'" . $task[$i] . "'";
                        }
                    }
                }

                //Department
                if ($userUid == null && $departmentUid != null) {
                    $swUid = 1;

                    $sql = "SELECT USR.USR_UID
                            FROM   DEPARTMENT AS DEPT,
                                   USERS AS USR
                            WHERE  DEPT.DEP_UID = '$departmentUid' AND DEPT.DEP_UID = USR.DEP_UID";

                    $rsSql = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);

                    while ($rsSql->next()) {
                        $row = $rsSql->getRow();

                        $depusrUserUid = $row["USR_UID"];

                        $task = $appCacheView->getSelfServiceTasks($depusrUserUid);

                        for ($i = 0; $i <= count($task) - 1; $i++) {
                            $sqlTaskUid = $sqlTaskUid . (($sqlTaskUid != null)? ", " : null) . "'" . $task[$i] . "'";
                        }
                    }
                }

                $sqlAppDelCondition = $sqlAppDelCondition . (($sqlAppDelCondition != null)? " AND " : null) . "APPDELCASE.DEL_THREAD_STATUS = 'OPEN' AND APPDELCASE.USR_UID = ''" . (($swUid == 1)? " AND " . (($sqlTaskUid != null)? "APPDELCASE.TAS_UID IN ($sqlTaskUid)" : "1 <> 1") : null);
                break;
            case "ON_TIME":
                $sqlAppDelCondition = $sqlAppDelConditionAndUser . (($sqlAppDelConditionAndUser != null)? " AND " : null) . "$sqlDelIndexCondition AND NOW() <= APPDELCASE.DEL_TASK_DUE_DATE";
                break;
            case "ON_RISK":
                $onRiskRemainingDay = $arrayConfigData["onRiskRemainingDay"];

                $sqlAppDelCondition = $sqlAppDelConditionAndUser . (($sqlAppDelConditionAndUser != null)? " AND " : null) . "$sqlDelIndexCondition AND NOW() BETWEEN DATE_ADD(APPDELCASE.DEL_TASK_DUE_DATE, INTERVAL -$onRiskRemainingDay DAY) AND APPDELCASE.DEL_TASK_DUE_DATE";
                break;
            case "OVERDUE":
                $sqlAppDelCondition = $sqlAppDelConditionAndUser . (($sqlAppDelConditionAndUser != null)? " AND " : null) . "$sqlDelIndexCondition AND APPDELCASE.DEL_TASK_DUE_DATE < NOW()";
                break;
            default:
                switch ($arrayConfigData["caseType"]) {
                    case "STATUS":
                        //If $delStatus = ALL, then "Participated"
                        //If $delStatus = CLOSED, then "casesInbox + casesCompleted"

                        $sqlAppDelCondition = $sqlAppDelConditionAndUser . (($delStatus != "ALL")? (($sqlAppDelConditionAndUser != null)? " AND " : null) . "APPDELCASE.DEL_THREAD_STATUS = '$delStatus'" : null);
                        break;
                    case "VALIDITY":
                        $sqlAuxCondition = null;

                        //ON_TIME
                        $sqlAuxCondition = $sqlAuxCondition . (($sqlAuxCondition != null)? " OR " : null) . "NOW() <= APPDELCASE.DEL_TASK_DUE_DATE";

                        //ON_RISK
                        $onRiskRemainingDay = $arrayConfigData["onRiskRemainingDay"];

                        $sqlAuxCondition = $sqlAuxCondition . (($sqlAuxCondition != null)? " OR " : null) . "NOW() BETWEEN DATE_ADD(APPDELCASE.DEL_TASK_DUE_DATE, INTERVAL -$onRiskRemainingDay DAY) AND APPDELCASE.DEL_TASK_DUE_DATE";

                        //OVERDUE
                        $sqlAuxCondition = $sqlAuxCondition . (($sqlAuxCondition != null)? " OR " : null) . "APPDELCASE.DEL_TASK_DUE_DATE < NOW()";

                        //Condition
                        $sqlAppDelCondition = $sqlAppDelConditionAndUser . (($sqlAppDelConditionAndUser != null)? " AND " : null) . "$sqlDelIndexCondition AND ($sqlAuxCondition)" . (($delStatus != "ALL")? " AND APPDELCASE.DEL_THREAD_STATUS = '$delStatus'" : null);
                        break;
                }
                break;
        }

        switch ($category) {
            case "OVERDUE":
                $sqlAppDelCondition = $sqlAppDelCondition . (($sqlAppDelCondition != null)? " AND " : null) . "APPDELCASE.DEL_TASK_DUE_DATE < NOW()";
                break;
            case "OVERDUENOT":
                $sqlAppDelCondition = $sqlAppDelCondition . (($sqlAppDelCondition != null)? " AND " : null) . "NOW() <= APPDELCASE.DEL_TASK_DUE_DATE";
                break;
        }

        //SQL
        $sqlAppDel = "SELECT APPDELCASE.APP_UID, APPDELCASE.DEL_INDEX, APPDELCASE.TAS_UID, APPDELCASE.USR_UID, APPDELCASE.DEL_TASK_DUE_DATE
                      FROM   APP_DELEGATION AS APPDELCASE ";
        $sqlAppDel = $sqlAppDel . (($sqlAppDelCondition != null)? "WHERE $sqlAppDelCondition " : null);

        switch ($status) {
            case "TO_DO":
                break;
            case "DRAFT":
                break;
            case "PAUSED":
                break;
            case "COMPLETED":
                break;
            case "UNASSIGNED":
                break;
            case "ON_TIME":
                break;
            case "ON_RISK":
                break;
            case "OVERDUE":
                break;
            default:
                //If $delStatus = ALL, then "Participated"
                //If $delStatus = CLOSED, then "casesInbox + casesCompleted"

                //SQL in PROCESSMAKER
                //
                //processmaker/workflow/engine/classes/model/AppCacheView.php
                //
                //SELECT *, APP_CACHE_VIEW.DEL_INIT_DATE
                //FROM APP_CACHE_VIEW
                //WHERE APP_CACHE_VIEW.USR_UID='00000000000000000000000000000001'
                //GROUP BY APP_CACHE_VIEW.APP_UID
                //ORDER BY APP_CACHE_VIEW.APP_NUMBER DESC

                if ($sqlUserUid != null) {
                    $sqlAppDel = $sqlAppDel . "GROUP BY APPDELCASE.APP_UID "; //This clause deleted duplicates //Problems
                }
                break;
        }

        //SQL
        $sqlSelect = null;
        $sqlOrderGroupBy = null;

        if ($option == 0) {
            switch ($category) {
                case "CASE":
                    $sqlSelect = "SELECT COUNT(APP.APP_UID) AS NUMREC ";
                    $sqlOrderGroupBy = null;
                    break;
                case "PROCESS":
                    $sqlSelect = "SELECT APP.PRO_UID, COUNT(APP.PRO_UID) AS NUMREC ";
                    $sqlOrderGroupBy = "GROUP BY APP.PRO_UID ";
                    break;
                case "TASK":
                    $sqlSelect = "SELECT APPDELCASE.TAS_UID, COUNT(APPDELCASE.TAS_UID) AS NUMREC ";
                    $sqlOrderGroupBy = "GROUP BY APPDELCASE.TAS_UID ";
                    break;
                case "USER":
                    $sqlSelect = "SELECT APPDELCASE.USR_UID, COUNT(APPDELCASE.USR_UID) AS NUMREC ";
                    $sqlOrderGroupBy = "GROUP BY APPDELCASE.USR_UID ";
                    break;
                case "GROUP":
                    $sqlSelect = "SELECT GRPUSR.GRP_UID, COUNT(GRPUSR.GRP_UID) AS NUMREC
                                  FROM   (SELECT GRPUSR.GRP_UID, GRPUSR.USR_UID
                                          FROM   GROUP_USER AS GRPUSR
                                         ) AS GRPUSR,

                                         (SELECT APPDELCASE.USR_UID ";

                    $sqlOrderGroupBy = " ) AS APPCASE
                                  WHERE  GRPUSR.USR_UID = APPCASE.USR_UID
                                  GROUP BY GRPUSR.GRP_UID ";
                    break;
                case "DEPARTMENT":
                    $sqlSelect = "SELECT DEPTUSR.DEP_UID, COUNT(DEPTUSR.DEP_UID) AS NUMREC
                                  FROM   (SELECT DEPT.DEP_UID, USR.USR_UID
                                          FROM   DEPARTMENT AS DEPT,
                                                 USERS AS USR
                                          WHERE  DEPT.DEP_UID = USR.DEP_UID
                                         ) AS DEPTUSR,

                                         (SELECT APPDELCASE.USR_UID ";

                    $sqlOrderGroupBy = " ) AS APPCASE
                                  WHERE  DEPTUSR.USR_UID = APPCASE.USR_UID
                                  GROUP BY DEPTUSR.DEP_UID ";
                    break;
                case "OVERDUE":
                case "OVERDUENOT":
                    $sqlSelect = "SELECT COUNT(APP.APP_UID) AS NUMREC ";
                    $sqlOrderGroupBy = null;
                    break;
            }
        } else {
            $sqlSelect = "SELECT APP.APP_UID, APP.APP_STATUS, APP.APP_NUMBER, APP.PRO_UID, APPDELCASE.DEL_INDEX, APPDELCASE.TAS_UID, APPDELCASE.USR_UID, APPDELCASE.DEL_TASK_DUE_DATE ";
            $sqlOrderGroupBy = "ORDER BY APP.APP_NUMBER DESC ";
        }

        //SQL
        //$sqlApp = "$sqlSelect
        //           FROM  APPLICATION AS APP,
        //                 ($sqlAppDel) AS APPDELCASE
        //           WHERE APP.APP_UID = APPDELCASE.APP_UID
        //           $sqlOrderGroupBy";

        //$sqlApp = "$sqlSelect
        //           FROM  APPLICATION AS APP,
        //                 ($sqlAppDel) AS APPDELCASE
        //           WHERE " . (($processUid != null)? "APP.PRO_UID = '$processUid' AND " : null) . "
        //                 APP.APP_UID = APPDELCASE.APP_UID
        //                 " . (($taskUid != null)? " AND APPDELCASE.TAS_UID = '$taskUid'" : null) . "
        //                 " . ((!empty($dateIni) && !empty($dateEnd))? " AND APP.APP_CREATE_DATE BETWEEN '$dateIni' AND '$dateEnd'" : null) . "
        //           $sqlOrderGroupBy";

        $sqlApp = "$sqlSelect
                   FROM  APPLICATION AS APP,
                         ($sqlAppDel) AS APPDELCASE
                   WHERE " . (($processUid != null)? "APP.PRO_UID = '$processUid' AND " : null) . "
                         APP.APP_UID = APPDELCASE.APP_UID
                         " . ((!empty($dateIni) && !empty($dateEnd))? " AND APP.APP_CREATE_DATE BETWEEN '$dateIni' AND '$dateEnd'" : null) . "
                   $sqlOrderGroupBy";

        return $sqlApp;
    }

    public static function caseData($option, $category, $status, $delStatus, $dateIni, $dateEnd, $arrayConfigData, $processUid, $taskUid, $userUid, $groupUid, $departmentUid)
    {
        require_once ("classes" . PATH_SEP . "model" . PATH_SEP . "Process.php");
        require_once ("classes" . PATH_SEP . "model" . PATH_SEP . "TaskUser.php");
        require_once ("classes" . PATH_SEP . "model" . PATH_SEP . "AppCacheView.php");





        $cnn = Propel::getConnection("workflow");
        $stmt = $cnn->createStatement();

        $result = array();

        $sqlApp = self::caseDataSql($option, $category, $status, $delStatus, $dateIni, $dateEnd, $arrayConfigData, $processUid, $taskUid, $userUid, $groupUid, $departmentUid);

        if ($option == 0) {
            switch ($category) {
                case "CASE":
                    $rsSqlApp = $stmt->executeQuery($sqlApp, ResultSet::FETCHMODE_ASSOC);

                    while ($rsSqlApp->next()) {
                        $row = $rsSqlApp->getRow();

                        $appCaseNumRec = $row["NUMREC"];

                        //Result
                        $result[] = $appCaseNumRec;
                    }
                    break;
                case "PROCESS":
                    $rsSqlApp = $stmt->executeQuery($sqlApp, ResultSet::FETCHMODE_ASSOC);

                    while ($rsSqlApp->next()) {
                        $row = $rsSqlApp->getRow();

                        $appProcessUid = $row["PRO_UID"];
                        $appProNumRec = $row["NUMREC"];

                        //Process
                        $proName = null;

                        $sql = "SELECT CON.CON_VALUE
                                FROM   CONTENT AS CON
                                WHERE  CON.CON_ID = '$appProcessUid' AND CON.CON_CATEGORY = 'PRO_TITLE' AND CON.CON_LANG = '" . SYS_LANG . "'";

                        $rsSql = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);

                        if ($rsSql->next()) {
                            $row = $rsSql->getRow();

                            $proName = $row["CON_VALUE"];
                        }

                        //Result
                        $result[] = array($appProcessUid, $proName, $appProNumRec);
                    }
                    break;
                case "TASK":
                    $rsSqlApp = $stmt->executeQuery($sqlApp, ResultSet::FETCHMODE_ASSOC);

                    while ($rsSqlApp->next()) {
                        $row = $rsSqlApp->getRow();

                        $appdelTaskUid = $row["TAS_UID"];
                        $appTaskNumRec = $row["NUMREC"];

                        //Task
                        $taskName = null;

                        $sql = "SELECT CON.CON_VALUE
                                FROM   CONTENT AS CON
                                WHERE  CON.CON_ID = '$appdelTaskUid' AND CON.CON_CATEGORY = 'TAS_TITLE' AND CON.CON_LANG = '" . SYS_LANG . "'";

                        $rsSql = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);

                        if ($rsSql->next()) {
                            $row = $rsSql->getRow();

                            $taskName = $row["CON_VALUE"];
                        }

                        //Result
                        $result[] = array($appdelTaskUid, $taskName, $appTaskNumRec);
                    }
                    break;
                case "USER":
                    /*
                    $rsSqlApp = $stmt->executeQuery($sqlApp, ResultSet::FETCHMODE_ASSOC);

                    while ($rsSqlApp->next()) {
                        $row = $rsSqlApp->getRow();

                        $appdelUserUid = $row["USR_UID"];
                        $appUsrNumRec = $row["NUMREC"];

                        //User
                        $usrName = null;

                        $sql = "SELECT USR.USR_UID, USR.USR_FIRSTNAME, USR.USR_LASTNAME
                                FROM   USERS AS USR
                                WHERE  USR.USR_UID = '$appdelUserUid'";

                        $rsSql = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);

                        if ($rsSql->next()) {
                            $row = $rsSql->getRow();

                            $usrName = ((!empty($row["USR_FIRSTNAME"]))? $row["USR_FIRSTNAME"] . " " : null) . ((!empty($row["USR_LASTNAME"]))? $row["USR_LASTNAME"] : null);
                        }

                        $usrName = ($usrName != null)? $usrName : "[" . strtoupper(G::LoadTranslation("ID_UNASSIGNED")) . "]";

                        //Result
                        $result[] = array($appdelUserUid, $usrName, $appUsrNumRec);
                    }
                    */

                    $rsSqlApp = $stmt->executeQuery($sqlApp, ResultSet::FETCHMODE_ASSOC);

                    while ($rsSqlApp->next()) {
                        $row = $rsSqlApp->getRow();

                        $appdelUserUid = $row["USR_UID"];
                        $appUsrNumRec = $row["NUMREC"];

                        //User
                        $usrName = null;
                        $sw = 0;

                        $sql = "SELECT USR.USR_UID, USR.USR_FIRSTNAME, USR.USR_LASTNAME
                                FROM   USERS AS USR
                                WHERE  USR.USR_UID = '$appdelUserUid'";

                        $rsSql = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);

                        if ($rsSql->next()) {
                            $row = $rsSql->getRow();

                            $usrName = ((!empty($row["USR_FIRSTNAME"]))? $row["USR_FIRSTNAME"] . " " : null) . ((!empty($row["USR_LASTNAME"]))? $row["USR_LASTNAME"] : null);
                            $sw = 1;
                        }

                        $usrName = ($usrName != null)? $usrName : "[" . strtoupper(G::LoadTranslation("ID_UNASSIGNED")) . "]";

                        //SQL
                        if ($sw == 1) {
                            $dataUserUid = $appdelUserUid;
                            $dataUsrName = $usrName;

                            $sql = self::caseDataSql($option, "CASE", $status, $delStatus, $dateIni, $dateEnd, $arrayConfigData, $processUid, $taskUid, $dataUserUid, null, null);

                            $rsSql = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);

                            if ($rsSql->next()) {
                                $row = $rsSql->getRow();

                                $appCaseNumRec = $row["NUMREC"];

                                if ($appCaseNumRec > 0) {
                                    //Result
                                    $result[] = array($dataUserUid, $dataUsrName, $appCaseNumRec);
                                }
                            }
                        } else {
                            //Result
                            $result[] = array($appdelUserUid, $usrName, $appUsrNumRec);
                        }
                    }
                    break;
                case "GROUP":
                    /*
                    $rsSqlApp = $stmt->executeQuery($sqlApp, ResultSet::FETCHMODE_ASSOC);

                    while ($rsSqlApp->next()) {
                        $row = $rsSqlApp->getRow();

                        $appGroupUid = $row["GRP_UID"];
                        $appGrpNumRec = $row["NUMREC"];

                        //Group
                        $grpName = null;

                        $sql = "SELECT CON.CON_VALUE
                                FROM   CONTENT AS CON
                                WHERE  CON.CON_ID = '$appGroupUid' AND CON.CON_CATEGORY = 'GRP_TITLE' AND CON.CON_LANG = '" . SYS_LANG . "'";

                        $rsSql = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);

                        if ($rsSql->next()) {
                          $row = $rsSql->getRow();

                          $grpName = $row["CON_VALUE"];
                        }

                        //Result
                        $result[] = array($appGroupUid, $grpName, $appGrpNumRec);
                    }
                    */

                    $rsSqlApp = $stmt->executeQuery($sqlApp, ResultSet::FETCHMODE_ASSOC);

                    while ($rsSqlApp->next()) {
                        $row = $rsSqlApp->getRow();

                        $appGroupUid = $row["GRP_UID"];

                        //Group
                        $grpName = null;

                        $sql = "SELECT CON.CON_VALUE
                                FROM   CONTENT AS CON
                                WHERE  CON.CON_ID = '$appGroupUid' AND CON.CON_CATEGORY = 'GRP_TITLE' AND CON.CON_LANG = '" . SYS_LANG . "'";

                        $rsSql = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);

                        if ($rsSql->next()) {
                          $row = $rsSql->getRow();

                          $grpName = $row["CON_VALUE"];
                        }

                        //SQL
                        $dataGroupUid = $appGroupUid;
                        $dataGrpName  = $grpName;

                        $sql = self::caseDataSql($option, "CASE", $status, $delStatus, $dateIni, $dateEnd, $arrayConfigData, $processUid, $taskUid, null, $dataGroupUid, null);

                        $rsSql = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);

                        if ($rsSql->next()) {
                            $row = $rsSql->getRow();

                            $appCaseNumRec = $row["NUMREC"];

                            if ($appCaseNumRec > 0) {
                                //Result
                                $result[] = array($dataGroupUid, $dataGrpName, $appCaseNumRec);
                            }
                        }
                    }
                    break;
                case "DEPARTMENT":
                    /*
                    $rsSqlApp = $stmt->executeQuery($sqlApp, ResultSet::FETCHMODE_ASSOC);

                    while ($rsSqlApp->next()) {
                        $row = $rsSqlApp->getRow();

                        $appDepartmentUid = $row["DEP_UID"];
                        $appDeptNumRec = $row["NUMREC"];

                        //Department
                        $deptName = null;

                        $sql = "SELECT CON.CON_VALUE
                                FROM   CONTENT AS CON
                                WHERE  CON.CON_ID = '$appDepartmentUid' AND CON.CON_CATEGORY = 'DEPO_TITLE' AND CON.CON_LANG = '" . SYS_LANG . "'";

                        $rsSql = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);

                        if ($rsSql->next()) {
                          $row = $rsSql->getRow();

                          $deptName = $row["CON_VALUE"];
                        }

                        //Result
                        $result[] = array($appDepartmentUid, $deptName, $appDeptNumRec);
                    }
                    */

                    $rsSqlApp = $stmt->executeQuery($sqlApp, ResultSet::FETCHMODE_ASSOC);

                    while ($rsSqlApp->next()) {
                        $row = $rsSqlApp->getRow();

                        $appDepartmentUid = $row["DEP_UID"];

                        //Department
                        $deptName = null;

                        $sql = "SELECT CON.CON_VALUE
                                FROM   CONTENT AS CON
                                WHERE  CON.CON_ID = '$appDepartmentUid' AND CON.CON_CATEGORY = 'DEPO_TITLE' AND CON.CON_LANG = '" . SYS_LANG . "'";

                        $rsSql = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);

                        if ($rsSql->next()) {
                          $row = $rsSql->getRow();

                          $deptName = $row["CON_VALUE"];
                        }

                        //SQL
                        $dataDepartmentUid = $appDepartmentUid;
                        $dataDeptName = $deptName;

                        $sql = self::caseDataSql($option, "CASE", $status, $delStatus, $dateIni, $dateEnd, $arrayConfigData, $processUid, $taskUid, null, null, $dataDepartmentUid);

                        $rsSql = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);

                        if ($rsSql->next()) {
                            $row = $rsSql->getRow();

                            $appCaseNumRec = $row["NUMREC"];

                            if ($appCaseNumRec > 0) {
                                //Result
                                $result[] = array($dataDepartmentUid, $dataDeptName, $appCaseNumRec);
                            }
                        }
                    }
                    break;
                case "OVERDUE":
                case "OVERDUENOT":
                    $rsSqlApp = $stmt->executeQuery($sqlApp, ResultSet::FETCHMODE_ASSOC);

                    while ($rsSqlApp->next()) {
                        $row = $rsSqlApp->getRow();

                        $appCaseNumRec = $row["NUMREC"];

                        //Result
                        $result[] = $appCaseNumRec;
                    }
                    break;
            }
        } else {
            //List
            $rsSqlApp = $stmt->executeQuery($sqlApp, ResultSet::FETCHMODE_ASSOC);

            while ($rsSqlApp->next()) {
                $rowApp = $rsSqlApp->getRow();

                $applicationUid = $rowApp["APP_UID"];
                $appStatus = $rowApp["APP_STATUS"];
                $appNumber = $rowApp["APP_NUMBER"];
                $appProcessUid = $rowApp["PRO_UID"];

                $appdelDelIndex = $rowApp["DEL_INDEX"];
                $appdelTaskUid = $rowApp["TAS_UID"];
                $appdelUserUid = $rowApp["USR_UID"];
                $appdelTaskDueDate = $rowApp["DEL_TASK_DUE_DATE"];

                //Current delegation
                switch ($status) {
                    case "TO_DO":
                    case "DRAFT":
                    case "PAUSED":
                    case "COMPLETED":
                    case "UNASSIGNED":
                    case "ON_TIME":
                    case "ON_RISK":
                    case "OVERDUE":
                        break;
                    default:
                        $sql = "SELECT APPDEL1.APP_UID, APPDEL1.DEL_INDEX, APPDEL1.TAS_UID, APPDEL1.USR_UID, APPDEL1.DEL_TASK_DUE_DATE
                                FROM   APP_DELEGATION AS APPDEL1
                                WHERE  APPDEL1.APP_UID = '$applicationUid' AND APPDEL1.DEL_INDEX = (SELECT MAX(APPDEL2.DEL_INDEX)
                                                                                                    FROM   APP_DELEGATION AS APPDEL2
                                                                                                    WHERE  APPDEL2.APP_UID = APPDEL1.APP_UID)";

                        $rsSql = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);

                        if ($rsSql->next()) {
                            $row = $rsSql->getRow();

                            $appdelDelIndex = $row["DEL_INDEX"];
                            $appdelTaskUid = $row["TAS_UID"];
                            $appdelUserUid = $row["USR_UID"];
                            $appdelTaskDueDate = $row["DEL_TASK_DUE_DATE"];
                        }
                        break;
                }

                //Process
                $proName = null;

                $sql = "SELECT CON.CON_VALUE
                        FROM   CONTENT AS CON
                        WHERE  CON.CON_ID = '$appProcessUid' AND CON.CON_CATEGORY = 'PRO_TITLE' AND CON.CON_LANG = '" . SYS_LANG . "'";

                $rsSql = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);

                if ($rsSql->next()) {
                    $row = $rsSql->getRow();

                    $proName = $row["CON_VALUE"];
                }

                //Task
                $taskName = null;

                $sql = "SELECT CON.CON_VALUE
                        FROM   CONTENT AS CON
                        WHERE  CON.CON_ID = '$appdelTaskUid' AND CON.CON_CATEGORY = 'TAS_TITLE' AND CON.CON_LANG = '" . SYS_LANG . "'";

                $rsSql = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);

                if ($rsSql->next()) {
                    $row = $rsSql->getRow();

                    $taskName = $row["CON_VALUE"];
                }

                //Sent by
                $sentbyUserUid = null;
                $sentbyUsrName = null;

                $sql = "SELECT USR.USR_UID, USR.USR_FIRSTNAME, USR.USR_LASTNAME
                        FROM   USERS AS USR
                        WHERE  USR.USR_UID = (SELECT APPDEL.USR_UID
                                              FROM   APP_DELEGATION AS APPDEL
                                              WHERE  APPDEL.APP_UID = '$applicationUid' AND
                                                     APPDEL.DEL_INDEX = (SELECT APPDEL2.DEL_PREVIOUS
                                                                         FROM   APP_DELEGATION AS APPDEL2
                                                                         WHERE  APPDEL2.APP_UID = '$applicationUid' AND
                                                                                APPDEL2.DEL_INDEX = $appdelDelIndex
                                                                        )
                                             )";

                $rsSql = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);

                if ($rsSql->next()) {
                    $row = $rsSql->getRow();

                    $sentbyUserUid = $row["USR_UID"];

                    $sentbyUsrName = ((!empty($row["USR_FIRSTNAME"]))? $row["USR_FIRSTNAME"] . " " : null) . ((!empty($row["USR_LASTNAME"]))? $row["USR_LASTNAME"] : null);
                }

                //User of current delegation
                $appdelUsrName = null;

                $sql = "SELECT USR.USR_UID, USR.USR_FIRSTNAME, USR.USR_LASTNAME
                        FROM   USERS AS USR
                        WHERE  USR.USR_UID = '$appdelUserUid'";

                $rsSql = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);

                if ($rsSql->next()) {
                  $row = $rsSql->getRow();

                  $appdelUsrName = ((!empty($row["USR_FIRSTNAME"]))? $row["USR_FIRSTNAME"] . " " : null) . ((!empty($row["USR_LASTNAME"]))? $row["USR_LASTNAME"] : null);
                }

                $appdelUsrName = ($appdelUsrName != null)? $appdelUsrName : "[" . strtoupper(G::LoadTranslation("ID_UNASSIGNED")) . "]";

                //Result
                $result[] = array($applicationUid, $appStatus, $appNumber, $appProcessUid, $proName, $appdelDelIndex, $appdelTaskUid, $taskName, $sentbyUserUid, $sentbyUsrName, $appdelUserUid, $appdelUsrName, $appdelTaskDueDate);
            }
        }

        return $result;
    }
}

