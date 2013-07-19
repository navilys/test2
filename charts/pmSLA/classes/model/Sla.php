<?php

/**
 * Skeleton subclass for representing a row from the 'SLA' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    classes.model
 */
require_once 'classes/model/om/BaseSla.php';
require_once 'classes/model/AppSla.php';

require_once 'classes/model/Content.php';
require_once 'classes/model/Process.php';
require_once 'classes/model/Application.php';

class Sla extends BaseSla
{

    public function create($aData)
    {
        $con = Propel::getConnection(SlaPeer::DATABASE_NAME);
        try {
            $this->fromArray($aData, BasePeer::TYPE_FIELDNAME);
            if ($this->validate()) {
                $result = $this->save();
            } else {
                $e = new Exception("Failed Validation in class " . get_class($this) . ".");
                $e->aValidationFailures = $this->getValidationFailures();
                throw($e);
            }
            $con->commit();
            return $result;
        } catch (Exception $e) {
            $con->rollback();
            throw($e);
        }
    }

    public function slaExists($SlaUid)
    {
        try {
            $oRow = SlaPeer::retrieveByPK($SlaUid);
            if (!is_null($oRow)) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $oError) {
            return false;
        }
    }

    public function load($SlaUid)
    {
        try {
            $oRow = SlaPeer::retrieveByPK($SlaUid);
            if (!is_null($oRow)) {
                $aFields = $oRow->toArray(BasePeer::TYPE_FIELDNAME);
                $this->fromArray($aFields, BasePeer::TYPE_FIELDNAME);
                $this->setNew(false);
                return $aFields;
            } else {
                throw(new Exception("The row '" . $SlaUid . "' in table SLA doesn't exist!"));
            }
        } catch (PropelException $e) {
            throw($e);
        } catch (Exception $oError) {
            throw($oError);
        }
    }

    public function loadDetails($SlaUid)
    {
        try {
            $result = array();
            $oSla = SlaPeer::retrieveByPK($SlaUid);
            if (!is_null($oSla)) {

                $result['SLA_UID'] = $oSla->getSlaUid();
                $result['PRO_UID'] = $oSla->getProUid();
                $result['SLA_NAME'] = $oSla->getSlaName();
                $result['SLA_DESCRIPTION'] = $oSla->getSlaDescription();
                $result['SLA_TYPE'] = $oSla->getSlaType();
                $result['SLA_TAS_START'] = $oSla->getSlaTasStart();
                $result['SLA_TAS_END'] = $oSla->getSlaTasEnd();
                $result['SLA_TIME_DURATION'] = $oSla->getSlaTimeDuration();
                $result['SLA_TIME_DURATION_MODE'] = $oSla->getSlaTimeDurationMode();
                $result['SLA_CONDITIONS'] = $oSla->getSlaConditions();
                $result['SLA_PEN_ENABLED'] = $oSla->getSlaPenEnabled();
                $result['SLA_PEN_TIME'] = $oSla->getSlaPenTime();
                $result['SLA_PEN_VALUE'] = $oSla->getSlaPenValue();
                $result['SLA_PEN_TIME_MODE'] = $oSla->getSlaPenTimeMode();
                $result['SLA_PEN_VALUE_UNIT'] = $oSla->getSlaPenValueUnit();
                $result['SLA_STATUS'] = $oSla->getSlaStatus();

                return $result;
            } else {
                throw(new Exception("The row '" . $SlaUid . "' in table SLA doesn't exist!"));
            }
        } catch (Exception $oError) {
            throw($oError);
        }
    }

    public function update($fields)
    {
        $con = Propel::getConnection(SlaPeer::DATABASE_NAME);
        try {
            $con->begin();
            $this->load($fields['SLA_UID']);
            $this->fromArray($fields, BasePeer::TYPE_FIELDNAME);
            if ($this->validate()) {
                $result = $this->save();
                $con->commit();
                return $result;
            } else {
                $con->rollback();
                throw(new Exception("Failed Validation in class " . get_class($this) . "."));
            }
        } catch (Exception $e) {
            $con->rollback();
            throw($e);
        }
    }

    public function remove($SlaUid)
    {
        $con = Propel::getConnection(SlaPeer::DATABASE_NAME);
        try {
            $con->begin();
            $this->setSlaUid($SlaUid);
            $result = $this->delete();
            $con->commit();
            return $result;
        } catch (Exception $e) {
            $con->rollback();
            throw($e);
        }
    }

    public function getSlaNameExist($sSlaName = "")
    {
        $criteria = new Criteria('workflow');

        $criteria->clearSelectColumns();
        $criteria->addSelectColumn(SlaPeer::SLA_UID);
        $criteria->addSelectColumn(SlaPeer::SLA_NAME);

        $criteria->add(SlaPeer::SLA_NAME, $sSlaName);
        $rs = SlaPeer::doSelectRS($criteria);
        $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $rs->next();
        $row = $rs->getRow();
        if (is_array($row)) {
            return true;
        } else {
            return false;
        }
    }

    public function loadByAppSlaName($sSlaName = "", $sCriteria = "")
    {
        $criteria = new Criteria('workflow');

        $criteria->clearSelectColumns();
        $criteria->addSelectColumn(SlaPeer::SLA_UID);
        $criteria->addSelectColumn(SlaPeer::PRO_UID);
        $criteria->addSelectColumn(SlaPeer::SLA_NAME);
        $criteria->addSelectColumn(SlaPeer::SLA_DESCRIPTION);
        $criteria->addSelectColumn(SlaPeer::SLA_TYPE);
        $criteria->addSelectColumn(SlaPeer::SLA_TAS_START);
        $criteria->addSelectColumn(SlaPeer::SLA_TAS_END);
        $criteria->addSelectColumn(SlaPeer::SLA_TIME_DURATION);
        $criteria->addSelectColumn(SlaPeer::SLA_TIME_DURATION_MODE);
        $criteria->addSelectColumn(SlaPeer::SLA_CONDITIONS);
        $criteria->addSelectColumn(SlaPeer::SLA_PEN_ENABLED);
        $criteria->addSelectColumn(SlaPeer::SLA_PEN_TIME);
        $criteria->addSelectColumn(SlaPeer::SLA_PEN_VALUE);
        $criteria->addSelectColumn(SlaPeer::SLA_PEN_TIME_MODE);
        $criteria->addSelectColumn(SlaPeer::SLA_PEN_VALUE_UNIT);
        $criteria->addSelectColumn(SlaPeer::SLA_STATUS);

        $criteria->addSelectColumn('CONTENT.CON_VALUE AS PRO_NAME');

        $sDescription = "'PRO_TITLE'";
        $sLang = "'" . SYS_LANG . "'";
        $aConditions = array();
        $aConditions[] = array(SlaPeer::PRO_UID, ContentPeer::CON_ID);
        $aConditions[] = array(ContentPeer::CON_CATEGORY, $sDescription);
        $aConditions[] = array(ContentPeer::CON_LANG, $sLang);
        $criteria->addJoinMC($aConditions, Criteria::LEFT_JOIN);

        $slaTasks = "IF(SLA.SLA_TYPE = 'PROCESS', 'Entire Process', " .
                    "IF(SLA.SLA_TYPE = 'RANGE', 'Multiple Tasks', " .
                    "IF(SLA.SLA_TYPE = 'TASK', 'Tasks', SLA.SLA_TYPE))) as SLA_TASKS";

        $criteria->addSelectColumn($slaTasks);
        if ($sCriteria == "All") {
            $criteria->addJoin(SlaPeer::SLA_UID, AppSlaPeer::SLA_UID, Criteria::LEFT_JOIN);
            $criteria->addSelectColumn(AppSlaPeer::APP_UID);
            $criteria->addSelectColumn(AppSlaPeer::APP_SLA_DURATION);
            $criteria->addSelectColumn(AppSlaPeer::APP_SLA_DUE_DATE);
            $criteria->addSelectColumn(AppSlaPeer::APP_SLA_PEN_VALUE);
            $criteria->addSelectColumn(AppSlaPeer::APP_SLA_STATUS);
        }
        $criteria->add(SlaPeer::SLA_NAME, "%" . $sSlaName . "%", Criteria::LIKE);
        return $criteria;
    }

    public function loadBySlaNameInArray($sSlaName)
    {
        $c = $this->loadByAppSlaName($sSlaName);
        $rs = SlaPeer::doSelectRS($c);
        $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $rs->next();
        $row = $rs->getRow();
        return $row;
    }

    public function getListSla($sSlaName = "", $sCiteriaAll = "")
    {
        $oCriteria = $this->loadByAppSlaName($sSlaName, $sCiteriaAll);
        $oDataset  = SlaPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        $aSla = array();
        while ($oDataset->next()) {
            $aSla[] = $oDataset->getRow();
        }
        return $aSla;
    }

    public function getListSlaName($allValue = array())
    {
        $oCriteria = $this->loadByAppSlaName();
        $oDataset  = SlaPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        $aSla = array();
        if (count($allValue) > 0) {
            $aSla[] = $allValue;
        }
        while ($oDataset->next()) {
            $reg = $oDataset->getRow();
            $aSla[] = array('SLA_UID' => $reg['SLA_UID'], 'SLA_NAME' => $reg['SLA_NAME']); // $oDataset->getRow();
        }
        return $aSla;
    }

    public function getSelectSlaUid($sSlaUid = "", $nAppNumber = 0)
    {
        $criteria = new Criteria('workflow');

        $criteria->clearSelectColumns();
        $criteria->addSelectColumn(SlaPeer::SLA_UID);
        $criteria->addSelectColumn(SlaPeer::PRO_UID);
        $criteria->addSelectColumn(SlaPeer::SLA_NAME);
        $criteria->addSelectColumn(SlaPeer::SLA_DESCRIPTION);
        $criteria->addSelectColumn(SlaPeer::SLA_TYPE);
        $criteria->addSelectColumn(SlaPeer::SLA_TAS_START);
        $criteria->addSelectColumn(SlaPeer::SLA_TAS_END);
        $criteria->addSelectColumn(SlaPeer::SLA_TIME_DURATION);
        $criteria->addSelectColumn(SlaPeer::SLA_TIME_DURATION_MODE);
        $criteria->addSelectColumn(SlaPeer::SLA_CONDITIONS);
        $criteria->addSelectColumn(SlaPeer::SLA_PEN_ENABLED);
        $criteria->addSelectColumn(SlaPeer::SLA_PEN_TIME);
        $criteria->addSelectColumn(SlaPeer::SLA_PEN_VALUE);
        $criteria->addSelectColumn(SlaPeer::SLA_PEN_TIME_MODE);
        $criteria->addSelectColumn(SlaPeer::SLA_PEN_VALUE_UNIT);
        $criteria->addSelectColumn(SlaPeer::SLA_STATUS);

        $criteria->addSelectColumn('CON_PRO.CON_VALUE AS PRO_NAME');
        $criteria->addAlias('CON_PRO', 'CONTENT');

        $sDescription = "'PRO_TITLE'";
        $sLang = "'" . SYS_LANG . "'";
        $aConditions   = array();
        $aConditions[] = array(SlaPeer::PRO_UID, 'CON_PRO.CON_ID');
        $aConditions[] = array('CON_PRO.CON_CATEGORY', $sDescription);
        $aConditions[] = array('CON_PRO.CON_LANG', $sLang);
        $criteria->addJoinMC($aConditions, Criteria::LEFT_JOIN);

        $sDescription = "'TAS_TITLE'";
        $criteria->addSelectColumn('CON_TST.CON_VALUE AS SLA_TASKS_START');
        $criteria->addAlias('CON_TST', 'CONTENT');
        $aConditions   = array();
        $aConditions[] = array(SlaPeer::SLA_TAS_START, 'CON_TST.CON_ID');
        $aConditions[] = array('CON_TST.CON_CATEGORY', $sDescription);
        $aConditions[] = array('CON_TST.CON_LANG', $sLang);
        $criteria->addJoinMC($aConditions, Criteria::LEFT_JOIN);

        $criteria->addSelectColumn('CON_TED.CON_VALUE AS SLA_TASKS_END');
        $criteria->addAlias('CON_TED', 'CONTENT');
        $aConditions   = array();
        $aConditions[] = array(SlaPeer::SLA_TAS_END, 'CON_TED.CON_ID');
        $aConditions[] = array('CON_TED.CON_CATEGORY', $sDescription);
        $aConditions[] = array('CON_TED.CON_LANG', $sLang);
        $criteria->addJoinMC($aConditions, Criteria::LEFT_JOIN);

        $criteria->addJoin(SlaPeer::SLA_UID, AppSlaPeer::SLA_UID, Criteria::LEFT_JOIN);
        $criteria->addSelectColumn(AppSlaPeer::APP_UID);
        $criteria->addSelectColumn(AppSlaPeer::APP_SLA_DURATION);
        $criteria->addSelectColumn(AppSlaPeer::APP_SLA_DUE_DATE);
        $criteria->addSelectColumn(AppSlaPeer::APP_SLA_EXCEEDED);
        $criteria->addSelectColumn(AppSlaPeer::APP_SLA_PEN_VALUE);
        $criteria->addSelectColumn(AppSlaPeer::APP_SLA_STATUS);
        $criteria->addSelectColumn(ApplicationPeer::APP_NUMBER);
        $criteria->addJoin(AppSlaPeer::APP_UID, ApplicationPeer::APP_UID, Criteria::INNER_JOIN);

        $criteria->add(SlaPeer::SLA_UID, $sSlaUid, Criteria::EQUAL);
        $criteria->add(ApplicationPeer::APP_NUMBER, $nAppNumber, Criteria::EQUAL);

        $oDataset = SlaPeer::doSelectRS($criteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        $aSla = array();
        if ($oDataset->next()) {
            $aSla = $oDataset->getRow();
        }
        return $aSla;
    }

    /**
     * get processesList
     */
    public function getProcessList()
    {
        $process = new Process();
        return $process->getAll();
    }
}

// Sla

