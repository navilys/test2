<?php

require_once 'classes/model/om/BaseAppSla.php';
require_once 'classes/model/Sla.php';

require_once 'classes/model/Application.php';
require_once 'classes/model/AppCacheView.php';

require_once PATH_PM_SLA . 'class.pmSLA.php';

/**
 * Skeleton subclass for representing a row from the 'APP_SLA' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    classes.model
 */
class AppSla extends BaseAppSla
{

    public $totalRows = 0;
    public $sort = '';
    public $dir = '';
    public $start = 0;
    public $limit = 0;
    public $slaUidRep = "";
    public $dateStart = "";
    public $dateEnd = "";
    public $status = "";
    public $typeExceeded = "";
    public $typeDate = "";
    private $execDurationType = "";
    private $execNumber	 = 0;

    public function getTotalRows()
    {
        return $this->totalRows;
    }

    public function getSort()
    {
        return $this->sort;
    }

    public function getDir()
    {
        return $this->dir;
    }

    public function getStart()
    {
        return $this->start;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function getSlaUidRep()
    {
        return $this->slaUidRep;
    }

    public function getDateStart()
    {
        return $this->dateStart;
    }

    public function getDateEnd()
    {
        return $this->dateEnd;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getTypeExceeded()
    {
        return $this->typeExceeded;
    }

    public function getTypeDate()
    {
        return $this->typeDate;
    }

    public function getExecDurationType()
    {
        return $this->execDurationType;
    }

    public function getExecNumber()
    {
        if ($this->getExecDurationType() == 'DAYS') {
            return $this->execNumber * 24;
        } else {
            return $this->execNumber;
        }
    }

    public function setTotalRows($x)
    {
        $this->totalRows = $x;
    }

    public function setSort($x)
    {
        $this->sort = $x;
    }

    public function setDir($x)
    {
        $this->dir = $x;
    }

    public function setStart($x)
    {
        $this->start = $x;
    }

    public function setLimit($x)
    {
        $this->limit = $x;
    }

    public function setSlaUidRep($x)
    {
        $this->slaUidRep = $x;
    }

    public function setDateStart($x)
    {
        $this->dateStart = $x;
    }

    public function setDateEnd($x)
    {
        $this->dateEnd = $x;
    }

    public function setStatus($x)
    {
        $this->status = $x;
    }

    public function setTypeExceeded($x)
    {
        $this->typeExceeded = $x;
    }

    public function setTypeDate($x)
    {
        $this->typeDate = $x;
    }
    public function setExecDurationType($x)
    {
        $this->execDurationType = $x;
    }

    public function setExecNumber($x)
    {
        $this->execNumber = $x;
    }

    public function create($aData)
    {
        $con = Propel::getConnection(AppSlaPeer::DATABASE_NAME);
        try {
            $this->fromArray($aData, BasePeer::TYPE_FIELDNAME);
            $result = $this->save();
            $con->commit();
            return $result;
        } catch (Exception $e) {
            $con->rollback();
            throw($e);
        }
    }

    public function slaExists($AppUid, $SlaUid)
    {
        try {
            $oRow = AppSlaPeer::retrieveByPK($AppUid, $SlaUid);
            if (!is_null($oRow)) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $oError) {
            return false;
        }
    }

    public function load($AppUid, $SlaUid)
    {
        try {
            $oRow = AppSlaPeer::retrieveByPK($AppUid, $SlaUid);
            if (!is_null($oRow)) {
                $aFields = $oRow->toArray(BasePeer::TYPE_FIELDNAME);
                $this->fromArray($aFields, BasePeer::TYPE_FIELDNAME);
                $this->setNew(false);
                return $aFields;
            } else {
                throw(new Exception("The rows '" . $AppUid . "/" . $SlaUid . "' in table APP_SLA doesn't exist!"));
            }
        } catch (PropelException $e) {
            throw($e);
        } catch (Exception $oError) {
            throw($oError);
        }
    }

    public function loadDetails($AppUid, $SlaUid)
    {
        try {
            $result = array();
            $oAppLsa = AppSlaPeer::retrieveByPK($AppUid, $SlaUid);
            if (!is_null($oAppLsa)) {

                $result['APP_UID'] = $oAppLsa->getAppUid();
                $result['SLA_UID'] = $oAppLsa->getSlaUid();
                $result['APP_SLA_INIT_DATE'] = $oAppLsa->getAppSlaInitDate();
                $result['APP_SLA_DUE_DATE'] = $oAppLsa->getAppSlaDueDate();
                $result['APP_SLA_FINISH_DATE'] = $oAppLsa->getAppSlaFinishDate();
                $result['APP_SLA_DURATION'] = $oAppLsa->getAppSlaDuration();
                $result['APP_SLA_EXCEEDED'] = $oAppLsa->getAppSlaExceeded();
                $result['APP_SLA_PEN_VALUE'] = $oAppLsa->getAppSlaPenValue();
                $result['APP_SLA_STATUS'] = $oAppLsa->getAppSlaStatus();

                return $result;
            } else {

                throw(new Exception("The rows '" . $AppUid . "/" . $SlaUid . "' in table APP_LSA doesn't exist!"));
            }
        } catch (Exception $oError) {
            throw($oError);
        }
    }

    public function update($fields)
    {
        $con = Propel::getConnection(AppSlaPeer::DATABASE_NAME);
        try {
            $con->begin();
            $this->load($fields['APP_UID'], $fields['SLA_UID']);
            $this->fromArray($fields, BasePeer::TYPE_FIELDNAME);
            $result = $this->save();
            $con->commit();
            return $result;
        } catch (Exception $e) {
            $con->rollback();
            throw($e);
        }
    }

    public function remove($AppUid, $SlaUid)
    {
        $con = Propel::getConnection(AppSlaPeer::DATABASE_NAME);
        try {
            $con->begin();
            $this->setAppUid($AppUid);
            $this->setSlaUid($SlaUid);
            $result = $this->delete();
            $con->commit();
            return $result;
        } catch (Exception $e) {
            $con->rollback();
            throw($e);
        }
    }

    public function loadByAppSla()
    {
        $criteria = new Criteria('workflow');
        // $del = DBAdapter::getStringDelimiter();

        $criteria->clearSelectColumns();
        //$criteria->addSelectColumn(AppSlaPeer::APP_UID);
        $criteria->addSelectColumn(AppSlaPeer::SLA_UID);
        $criteria->addSelectColumn(SlaPeer::SLA_NAME);
        $criteria->addSelectColumn(SlaPeer::PRO_UID);
        $criteria->addSelectColumn(ApplicationPeer::APP_NUMBER);
        $criteria->addSelectColumn(ApplicationPeer::APP_STATUS);
        $criteria->addSelectColumn('1 AS TOTAL_CASES');
        $criteria->addSelectColumn(AppSlaPeer::APP_SLA_EXCEEDED . ' AS TOTAL_EXCEEDED');
        $criteria->addSelectColumn(AppSlaPeer::APP_SLA_PEN_VALUE);
        $criteria->addSelectColumn(AppSlaPeer::APP_SLA_EXCEEDED . ' AS AVG_CASES');
        $criteria->addSelectColumn(AppSlaPeer::APP_SLA_INIT_DATE);
        $criteria->addSelectColumn(AppSlaPeer::APP_SLA_DUE_DATE);
        $criteria->addSelectColumn(AppSlaPeer::APP_SLA_FINISH_DATE);
        $criteria->addSelectColumn(SlaPeer::SLA_PEN_VALUE_UNIT);
        $criteria->addSelectColumn(AppSlaPeer::APP_SLA_STATUS);

        $criteria->addJoin(AppSlaPeer::SLA_UID, SlaPeer::SLA_UID, Criteria::LEFT_JOIN);

        $criteria->addSelectColumn(SlaPeer::SLA_STATUS);
        $criteria->addSelectColumn('CONTENT.CON_VALUE AS PRO_NAME');

        $sDescription = "'PRO_TITLE'";
        $sLang = "'" . SYS_LANG . "'";
        $aConditions = array();
        $aConditions[] = array(SlaPeer::PRO_UID, ContentPeer::CON_ID);
        $aConditions[] = array(ContentPeer::CON_CATEGORY, $sDescription);
        $aConditions[] = array(ContentPeer::CON_LANG, $sLang);
        $criteria->addJoinMC($aConditions, Criteria::LEFT_JOIN);

        $criteria->addJoin(AppSlaPeer::APP_UID, ApplicationPeer::APP_UID, Criteria::INNER_JOIN);

        if ($this->getSlaUidRep() != "") {
            $criteria->add(AppSlaPeer::SLA_UID, $this->getSlaUidRep());
        }
        if ($this->getTypeDate() != '') {
            switch ($this->getTypeDate()) {
                case '>': // Greater than
                    if ($this->getDateStart() != "") {
                        $criteria->add(AppSlaPeer::APP_SLA_INIT_DATE, $this->getDateStart(), Criteria::GREATER_THAN);
                    }
                    break;
                case '>=': // Greater or equal than
                    if ($this->getDateStart() != "") {
                        $criteria->add(AppSlaPeer::APP_SLA_INIT_DATE, $this->getDateStart(), Criteria::GREATER_THAN);
                    }
                    break;
                case '<': // Below than
                    if ($this->getDateStart() != "") {
                        $criteria->add(AppSlaPeer::APP_SLA_INIT_DATE, $this->getDateStart(), Criteria::GREATER_THAN);
                    }
                    break;
                case '<=': // Below or equal than
                    if ($this->getDateStart() != "") {
                        $criteria->add(AppSlaPeer::APP_SLA_INIT_DATE, $this->getDateStart(), Criteria::GREATER_THAN);
                    }
                    break;
                case 'between': // between
                    if ($this->getDateStart() != "" && $this->getDateEnd() != "") {
                        $criteria->add(AppSlaPeer::APP_SLA_INIT_DATE, AppSlaPeer::APP_SLA_INIT_DATE .
                            " BETWEEN  '" . $this->getDateStart() . "' AND '" . $this->getDateEnd() . "'",
                            Criteria::CUSTOM);
                    }
                    break;
                default:
                    break;
            }
        }

        if ($this->getStatus() != "") {
            switch ($this->getStatus()) {
                case 'COMPLETED':
                    $criteria->add(ApplicationPeer::APP_STATUS, array("COMPLETED", "CANCELLED"), Criteria::IN);
                    break;
                case 'OPEN':
                    $criteria->add(ApplicationPeer::APP_STATUS, array("DRAFT", "TO_DO", "PAUSED"), Criteria::IN);
                    break;
                default:
                    break;
            }
        }
        switch ($this->getTypeExceeded()) {
            case 'NO_EXCEEDED':
                $criteria->add(AppSlaPeer::APP_SLA_EXCEEDED, 0, Criteria::EQUAL);
                break;
            case 'EXCEEDED':
                $criteria->add(AppSlaPeer::APP_SLA_EXCEEDED, 0, Criteria::GREATER_THAN);
                break;
            case 'EXCEEDED_LESS':
                $criteria->add(AppSlaPeer::APP_SLA_EXCEEDED, $this->getExecNumber(), Criteria::LESS_THAN);
                break;
            case 'EXCEEDED_MORE':
                $criteria->add(AppSlaPeer::APP_SLA_EXCEEDED, $this->getExecNumber(), Criteria::GREATER_THAN);
                break;
            default:
                break;
        }

        $this->setTotalRows(AppSlaPeer::doCount($criteria));
        if ($this->getLimit() != 0) {
            $criteria->setLimit($this->getLimit());
            $criteria->setOffset($this->getStart());
        }


        if ($this->getSort() != '') {
            switch ($this->getSort()) {
                case 'APP_NUMBER':
                    if ($this->getDir() == 'ASC') {
                        $criteria->addAscendingOrderByColumn(ApplicationPeer::APP_NUMBER);
                    } else {
                        $criteria->addDescendingOrderByColumn(ApplicationPeer::APP_NUMBER);
                    }
                    break;
                case 'TOTAL_EXCEEDED':
                    if ($this->getDir() == 'ASC') {
                        $criteria->addAscendingOrderByColumn(AppSlaPeer::APP_SLA_EXCEEDED);
                    } else {
                        $criteria->addDescendingOrderByColumn(AppSlaPeer::APP_SLA_EXCEEDED);
                    }
                    break;
                case 'APP_SLA_INIT_DATE':
                    if ($this->getDir() == 'ASC') {
                        $criteria->addAscendingOrderByColumn(AppSlaPeer::APP_SLA_INIT_DATE);
                    } else {
                        $criteria->addDescendingOrderByColumn(AppSlaPeer::APP_SLA_INIT_DATE);
                    }
                    break;
                case 'APP_SLA_DUE_DATE':
                    if ($this->getDir() == 'ASC') {
                        $criteria->addAscendingOrderByColumn(AppSlaPeer::APP_SLA_DUE_DATE);
                    } else {
                        $criteria->addDescendingOrderByColumn(AppSlaPeer::APP_SLA_DUE_DATE);
                    }
                    break;
                case 'APP_SLA_FINISH_DATE':
                    if ($this->getDir() == 'ASC') {
                        $criteria->addAscendingOrderByColumn(AppSlaPeer::APP_SLA_FINISH_DATE);
                    } else {
                        $criteria->addDescendingOrderByColumn(AppSlaPeer::APP_SLA_FINISH_DATE);
                    }
                    break;
                case 'APP_SLA_PEN_VALUE':
                    if ($this->getDir() == 'ASC') {
                        $criteria->addAscendingOrderByColumn(AppSlaPeer::APP_SLA_PEN_VALUE);
                    } else {
                        $criteria->addDescendingOrderByColumn(AppSlaPeer::APP_SLA_PEN_VALUE);
                    }
                    break;
                case 'APP_SLA_STATUS':
                    if ($this->getDir() == 'ASC') {
                        $criteria->addAscendingOrderByColumn(AppSlaPeer::APP_SLA_STATUS);
                    } else {
                        $criteria->addDescendingOrderByColumn(AppSlaPeer::APP_SLA_STATUS);
                    }
                    break;
                default:
                    $criteria->addAscendingOrderByColumn(AppCacheViewPeer::APP_NUMBER);
                    break;
            }
        }
        return $criteria;
    }

    /**
     * Get Report First Level
     *
     * @param type $slaUid
     * @param type $dateStart
     * @param type $dateEnd
     * @param type $status
     * @param type $typeExceeded
     * @return type $aAppSla return Array for first level
     */
    public function getReportFirstLevel()
    {
        $criteria = new Criteria('workflow');

        $criteria->clearSelectColumns();
        $criteria->addSelectColumn(AppSlaPeer::SLA_UID);
        $criteria->addSelectColumn(SlaPeer::SLA_NAME);
        $criteria->addSelectColumn(SlaPeer::SLA_PEN_VALUE_UNIT);
        $criteria->addSelectColumn('COUNT(' . AppSlaPeer::APP_UID . ') AS SUM_DURATION');
        $criteria->addSelectColumn('SUM(' . AppSlaPeer::APP_SLA_EXCEEDED . ') AS SUM_EXCEEDED');
        $criteria->addSelectColumn('SUM(' . AppSlaPeer::APP_SLA_EXCEEDED . ')/COUNT(' . AppSlaPeer::SLA_UID . ') ' .
                ' AS AVG_SLA');
        $criteria->addSelectColumn('SUM(' . AppSlaPeer::APP_SLA_PEN_VALUE . ') AS SUM_PEN_VALUE');

        $criteria->addJoin(AppSlaPeer::SLA_UID, SlaPeer::SLA_UID, Criteria::LEFT_JOIN);
        $criteria->addGroupByColumn(AppSlaPeer::SLA_UID);
        $criteria->addGroupByColumn(SlaPeer::SLA_NAME);
        $criteria->addGroupByColumn(SlaPeer::SLA_PEN_VALUE_UNIT);

        $criteria->addJoin(AppSlaPeer::APP_UID, ApplicationPeer::APP_UID, Criteria::INNER_JOIN);

        if ($this->getSlaUidRep() != "") {
            $criteria->add(AppSlaPeer::SLA_UID, $this->getSlaUidRep());
        }
        if ($this->getTypeDate() != '') {
            switch ($this->getTypeDate()) {
                case '>': // Greater than
                    if ($this->getDateStart() != "") {
                        $criteria->add(AppSlaPeer::APP_SLA_INIT_DATE, $this->getDateStart(), Criteria::GREATER_THAN);
                    }
                    break;
                case '>=': // Greater or equal than
                    if ($this->getDateStart() != "") {
                        $criteria->add(AppSlaPeer::APP_SLA_INIT_DATE, $this->getDateStart(), Criteria::GREATER_THAN);
                    }
                    break;
                case '<': // Below than
                    if ($this->getDateStart() != "") {
                        $criteria->add(AppSlaPeer::APP_SLA_INIT_DATE, $this->getDateStart(), Criteria::GREATER_THAN);
                    }
                    break;
                case '<=': // Below or equal than
                    if ($this->getDateStart() != "") {
                        $criteria->add(AppSlaPeer::APP_SLA_INIT_DATE, $this->getDateStart(), Criteria::GREATER_THAN);
                    }
                    break;
                case 'between': // between
                    if ($this->getDateStart() != "" && $this->getDateEnd() != "") {
                        $criteria->add(AppSlaPeer::APP_SLA_INIT_DATE, AppSlaPeer::APP_SLA_INIT_DATE .
                            " BETWEEN  '" . $this->getDateStart() . "' AND '" . $this->getDateEnd() . "'",
                            Criteria::CUSTOM);
                    }
                    break;
                default:
                    break;
            }
        }

        if ($this->getStatus() != "") {
            switch ($this->getStatus()) {
                case 'COMPLETED':
                    $criteria->add(ApplicationPeer::APP_STATUS, array("COMPLETED", "CANCELLED"), Criteria::IN);
                    break;
                case 'OPEN':
                    $criteria->add(ApplicationPeer::APP_STATUS, array("DRAFT", "TO_DO", "PAUSED"), Criteria::IN);
                    break;
                default:
                    break;
            }
        }
        switch ($this->getTypeExceeded()) {
            case 'NO_EXCEEDED':
                $criteria->add(AppSlaPeer::APP_SLA_EXCEEDED, 0, Criteria::EQUAL);
                break;
            case 'EXCEEDED':
                $criteria->add(AppSlaPeer::APP_SLA_EXCEEDED, 0, Criteria::GREATER_THAN);
                break;
            case 'EXCEEDED_LESS':
                $criteria->add(AppSlaPeer::APP_SLA_EXCEEDED, $this->getExecNumber(), Criteria::LESS_THAN);
                break;
            case 'EXCEEDED_MORE':
                $criteria->add(AppSlaPeer::APP_SLA_EXCEEDED, $this->getExecNumber(), Criteria::GREATER_THAN);
                break;
            default:
                break;
        }
        $criteria->addAscendingOrderByColumn(AppSlaPeer::SLA_UID);
        $this->setTotalRows(AppSlaPeer::doCount($criteria));
        if ($this->getLimit() != 0) {
            $criteria->setLimit($this->getLimit());
            $criteria->setOffset($this->getStart());
        }
        $oDataset = SlaPeer::doSelectRS($criteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        $aAppSla = array();
        while ($oDataset->next()) {
            $aAppSla[] = $oDataset->getRow();
        }
        return $aAppSla;
    }

    public function loadDashlet()
    {
        $criteria = new Criteria('workflow');

        $criteria->clearSelectColumns();
        $criteria->addSelectColumn(AppSlaPeer::SLA_UID);
        $criteria->addSelectColumn(SlaPeer::SLA_NAME);
        $criteria->addSelectColumn(SlaPeer::SLA_PEN_VALUE_UNIT);
        $criteria->addSelectColumn('COUNT(' . AppSlaPeer::APP_UID . ') AS SUM_DURATION');
        $criteria->addSelectColumn('SUM(' . AppSlaPeer::APP_SLA_EXCEEDED . ') AS SUM_EXCEEDED');
        $criteria->addSelectColumn('SUM(' . AppSlaPeer::APP_SLA_EXCEEDED . ')/COUNT(' . AppSlaPeer::SLA_UID . ') ' .
                ' AS AVG_SLA');
        $criteria->addSelectColumn('SUM(' . AppSlaPeer::APP_SLA_PEN_VALUE . ') AS SUM_PEN_VALUE');

        $criteria->addJoin(AppSlaPeer::SLA_UID, SlaPeer::SLA_UID, Criteria::LEFT_JOIN);
        $criteria->addGroupByColumn(AppSlaPeer::SLA_UID);
        $criteria->addGroupByColumn(SlaPeer::SLA_NAME);
        $criteria->addGroupByColumn(SlaPeer::SLA_PEN_VALUE_UNIT);

        $oDataset = SlaPeer::doSelectRS($criteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        $aAppSla = array();
        while ($oDataset->next()) {
            $aAppSla[] = $oDataset->getRow();
        }
        return $aAppSla;
    }

    public function loadDetailReportSel($sSlaUid = "", $nAppNumber = 0)
    {
        require_once PATH_PM_SLA . 'classes/class.pmCalendar.php';
        $oPmCalendar = new pmCalendar();

        $criteria = new Criteria('workflow');
        $criteria->clearSelectColumns();
        $criteria->addSelectColumn(AppSlaPeer::APP_UID);
        $criteria->addSelectColumn(AppSlaPeer::SLA_UID);
        $criteria->addSelectColumn(AppSlaPeer::APP_SLA_INIT_DATE);
        $criteria->addSelectColumn(AppSlaPeer::APP_SLA_DUE_DATE);
        $criteria->addSelectColumn(AppSlaPeer::APP_SLA_FINISH_DATE);
        $criteria->addSelectColumn(AppSlaPeer::APP_SLA_DURATION);
        $criteria->addSelectColumn(AppSlaPeer::APP_SLA_EXCEEDED);
        $criteria->addSelectColumn(AppSlaPeer::APP_SLA_PEN_VALUE);
        $criteria->addSelectColumn(AppSlaPeer::APP_SLA_STATUS . ' AS APP_TYPE');
        $criteria->addSelectColumn(AppCacheViewPeer::APP_TAS_TITLE . ' AS TASK_NAME');  
        $criteria->addSelectColumn(AppCacheViewPeer::PRO_UID);
        $criteria->addSelectColumn(AppCacheViewPeer::APP_CURRENT_USER . ' AS USR_NAME');
        $criteria->addSelectColumn(AppCacheViewPeer::DEL_DELEGATE_DATE);
        $criteria->addSelectColumn(AppCacheViewPeer::DEL_TASK_DUE_DATE);
        $criteria->addSelectColumn(AppCacheViewPeer::DEL_INIT_DATE);
        $criteria->addSelectColumn(AppCacheViewPeer::DEL_FINISH_DATE);
        $criteria->addSelectColumn(AppCacheViewPeer::DEL_DURATION);
        $criteria->addSelectColumn(AppCacheViewPeer::APP_THREAD_STATUS);
        $criteria->addSelectColumn(AppCacheViewPeer::APP_NUMBER);

        $criteria->addJoin(AppSlaPeer::APP_UID, AppCacheViewPeer::APP_UID, Criteria::INNER_JOIN);
        $criteria->add(AppSlaPeer::SLA_UID, $sSlaUid, Criteria::EQUAL);
        $criteria->add(AppCacheViewPeer::APP_NUMBER, $nAppNumber, Criteria::EQUAL);

        $oDataset = AppSlaPeer::doSelectRS($criteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        $aAppSla = array();
        $nCnt = 0;
        $proUid = '';
        while ($oDataset->next()) {
            $aAppSla[] = $oDataset->getRow();
            if ($oPmCalendar->pmCalendarUid == '' || $proUid != $aAppSla[$nCnt]['PRO_UID']) {
                $proUid = $aAppSla[$nCnt]['PRO_UID'];
                $oPmCalendar->getCalendar(null, $proUid);
                $oPmCalendar->getCalendarData();
            }

            $dateIni = $aAppSla[$nCnt]['DEL_INIT_DATE'];
            $dateFinish = $aAppSla[$nCnt]['DEL_FINISH_DATE'];
            $proUid = $aAppSla[$nCnt]['PRO_UID'];

            if (!(isset($dateIni)) || is_null($dateIni) || $dateIni == '') {
                $aAppSla[$nCnt]['VAL_DURATION'] = 0;
            } else {
                $aAppSla[$nCnt]['VAL_DURATION'] = $oPmCalendar->calculateDuration($dateIni, $dateFinish);    
            }
            $nCnt++;
        }
        return $aAppSla;
    }

    public function loadBySlaNameInArray($sSlaUid)
    {
        $this->setSlaUid($sSlaUid);
        $c = $this->loadByAppSla();
        $rs = SlaPeer::doSelectRS($c);
        $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $rs->next();
        $row = $rs->getRow();
        print_r($row);
        return $row;
    }

    public function getReportAppSla()
    {
        $oCriteria = $this->loadByAppSla();
        $oDataset = SlaPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        $aAppSla = array();
        while ($oDataset->next()) {
            $aAppSla[] = $oDataset->getRow();
        }
        return $aAppSla;
    }
}

// AppSla

