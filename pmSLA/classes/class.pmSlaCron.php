<?php

class pmSLAClassCron
{
    public $pmCalendar;

    public function __construct ()
    {
        set_include_path(PATH_PLUGINS . 'pmSLA' . PATH_SEPARATOR . get_include_path());
        if (!defined('PATH_PM_SLA')) {
            define('PATH_PM_SLA', PATH_CORE . 'plugins' . PATH_SEP . 'pmSLA' . PATH_SEP );
        }

        require_once 'classes/model/Application.php';
        require_once 'classes/model/AppDelegation.php';

        require_once 'classes/model/Sla.php';
        require_once 'classes/model/AppSla.php';

        require_once PATH_PM_SLA . 'class.pmSLA.php';
        require_once PATH_PM_SLA . 'classes/class.pmCalendar.php';

        $this->pmCalendar = new pmCalendar();
    }

    public function executeCron()
    {
        $this->firstPartCron();
        $this->secondPartCron();
        pmSLAClass::saveLogSla('cronSLA', 'executeCron', 'Execute Cron SLA');
    }

    public function firstPartCron()
    {
        // Query for cases news with SLA and not registred in APP_SLA
        $criteria = new Criteria('workflow');
        $criteria->clearSelectColumns();
        $criteria->addSelectColumn( SlaPeer::SLA_UID );
        $criteria->addSelectColumn( SlaPeer::SLA_TYPE );
        $criteria->addSelectColumn( SlaPeer::PRO_UID  );
        $criteria->addSelectColumn( SlaPeer::SLA_TAS_START );
        $criteria->addSelectColumn( SlaPeer::SLA_TIME_DURATION );
        $criteria->addSelectColumn( SlaPeer::SLA_TIME_DURATION_MODE );
        $criteria->addSelectColumn( SlaPeer::SLA_CONDITIONS );

        $criteria->addSelectColumn( ApplicationPeer::APP_UID );
        $criteria->addSelectColumn( ApplicationPeer::APP_DATA );
        $criteria->addSelectColumn( ApplicationPeer::APP_CREATE_DATE );

        $criteria->addJoin( SlaPeer::PRO_UID, ApplicationPeer::PRO_UID, Criteria::JOIN);

        $queryNotIn = 'APPLICATION.APP_UID NOT IN ';
        $queryNotIn .= '(SELECT APP_SLA.APP_UID FROM APP_SLA WHERE APP_SLA.SLA_UID = SLA.SLA_UID)';
        $criteria->add( ApplicationPeer::APP_UID, $queryNotIn, Criteria::CUSTOM);
        $criteria->add(SlaPeer::SLA_STATUS, 'ACTIVE');

        //$criteria->addDescendingOrderByColumn(ApplicationPeer::APP_UID)->addDescendingOrderByColumn(SlaPeer::SLA_UID);

        $dataSet = ApplicationPeer::doSelectRs($criteria);
        $dataSet->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        // End Query

        $countAppSla = 0;
        $proUid = '';
        // Verify Query's list
        while ($dataSet->next()) {
            $dataCase = $dataSet->getRow();
            if ($this->pmCalendar->pmCalendarUid == '' || $proUid != $dataCase['PRO_UID']) {
                $proUid = $dataCase['PRO_UID'];
                $this->pmCalendar->getCalendar(null, $dataCase['PRO_UID']);
                $this->pmCalendar->getCalendarData();
            }

            $executeSla = true;
            if ($dataCase['SLA_CONDITIONS'] !== '') {
                $appData = unserialize($dataCase['APP_DATA']);
                G::LoadClass('pmScript');
                $oPMScript = new PMScript();
                $oPMScript->setFields($appData);
                $oPMScript->setScript($dataCase['SLA_CONDITIONS']);
                $executeSla = $oPMScript->evaluate();
            }

            if (!$executeSla) {
                continue;
            }

            $dataInsertAppSla = array();
            $dataInsertAppSla['APP_UID'] = $dataCase['APP_UID'];
            $dataInsertAppSla['SLA_UID'] = $dataCase['SLA_UID'];
            $dataInsertAppSla['APP_SLA_DURATION'] = '0';
            $dataInsertAppSla['APP_SLA_PEN_VALUE'] = '0';
            $dataInsertAppSla['APP_SLA_STATUS'] = 'OPEN';

            if ($dataCase['SLA_TYPE'] == 'PROCESS') {
                $taskUid = null;
                $dataInsertAppSla['APP_SLA_INIT_DATE'] = $dataCase['APP_CREATE_DATE'];
                $dataInsertAppSla['APP_SLA_DUE_DATE']  = $this->pmCalendar->calculateDate(
                    $dataCase['APP_CREATE_DATE'],
                    $dataCase['SLA_TIME_DURATION'],
                    $dataCase['SLA_TIME_DURATION_MODE']);

                //$dataInsertAppSla['APP_SLA_DUE_DATE'] = $dataCase['APP_CREATE_DATE'];
            } else {
                // Query to verify if TASK_START start
                $criteriaDel = new Criteria('workflow');
                $criteriaDel->clearSelectColumns();
                $criteriaDel->addSelectColumn(AppDelegationPeer::DEL_DELEGATE_DATE );

                $criteriaDel->add(AppDelegationPeer::APP_UID, $dataCase['APP_UID']);
                $criteriaDel->add(AppDelegationPeer::TAS_UID, $dataCase['SLA_TAS_START']);
                $dataSetDel = AppDelegationPeer::doSelectRs($criteriaDel);
                $dataSetDel->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                // End Query

                $dateStartTask = '';

                if ($dataSetDel->next()) {
                    $dataDel = $dataSetDel->getRow();
                    $dateStartTask = $dataDel['DEL_DELEGATE_DATE'];
                    $dataInsertAppSla['APP_SLA_INIT_DATE'] = $dateStartTask;
                    $dataInsertAppSla['APP_SLA_DUE_DATE'] = $this->pmCalendar->calculateDate(
                        $dataDel['DEL_DELEGATE_DATE'],
                        $dataCase['SLA_TIME_DURATION'],
                        $dataCase['SLA_TIME_DURATION_MODE']);
                }
            }

            pmSLAClass::insertAppSla($dataInsertAppSla);
            $countAppSla++;
        }

        if ($countAppSla > 0) {
            pmSLAClass::saveLogSla('cronSLA', 'action', 'Insert ' . $countAppSla . ' registres in APP_SLA');
        }
    }

    public function secondPartCron()
    {
        // Query for cases news with SLA and not registred in APP_SLA
        $criteriaAppSla = new Criteria('workflow');
        $criteriaAppSla->clearSelectColumns();

        $criteriaAppSla->addSelectColumn( AppSlaPeer::APP_UID );
        $criteriaAppSla->addSelectColumn( AppSlaPeer::SLA_UID );
        $criteriaAppSla->addSelectColumn( AppSlaPeer::APP_SLA_DUE_DATE );
        $criteriaAppSla->addSelectColumn( AppSlaPeer::APP_SLA_INIT_DATE );

        $criteriaAppSla->addSelectColumn( SlaPeer::SLA_TYPE );
        $criteriaAppSla->addSelectColumn( SlaPeer::PRO_UID  );
        $criteriaAppSla->addSelectColumn( SlaPeer::SLA_TAS_START );
        $criteriaAppSla->addSelectColumn( SlaPeer::SLA_TAS_END );
        $criteriaAppSla->addSelectColumn( SlaPeer::SLA_TIME_DURATION );
        $criteriaAppSla->addSelectColumn( SlaPeer::SLA_TIME_DURATION_MODE );

        $criteriaAppSla->addSelectColumn( SlaPeer::SLA_PEN_ENABLED );

        $criteriaAppSla->addSelectColumn( SlaPeer::SLA_PEN_TIME );
        $criteriaAppSla->addSelectColumn( SlaPeer::SLA_PEN_TIME_MODE );
        $criteriaAppSla->addSelectColumn( SlaPeer::SLA_PEN_VALUE );

        $criteriaAppSla->addSelectColumn(ApplicationPeer::APP_CREATE_DATE );
        $criteriaAppSla->addSelectColumn(ApplicationPeer::APP_FINISH_DATE );

        $criteriaAppSla->addJoin( AppSlaPeer::SLA_UID, SlaPeer::SLA_UID, Criteria::JOIN);
        $criteriaAppSla->addJoin( AppSlaPeer::APP_UID, ApplicationPeer::APP_UID, Criteria::JOIN);

        $criteriaAppSla->add(AppSlaPeer::APP_SLA_STATUS, 'OPEN');
        $criteriaAppSla->add(SlaPeer::SLA_STATUS, 'ACTIVE');

        $criterionDate1 = $criteriaAppSla->getNewCriterion(AppSlaPeer::APP_SLA_DUE_DATE, Date("Y-m-d H:i:s"),
                Criteria::LESS_EQUAL);

        $criterionDate2 = $criteriaAppSla->getNewCriterion(AppSlaPeer::APP_SLA_DUE_DATE, null, Criteria::ISNULL );

        $criterionDate1->addOr($criterionDate2);
        $criteriaAppSla->add($criterionDate1);

        $dataSetAppSla = ApplicationPeer::doSelectRs($criteriaAppSla);

        $dataSetAppSla->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        // End Query

        // Verify Query's list
        $countAppSla = 0;
        $proUid = '';
        while ($dataSetAppSla->next()) {
            $dataAppSla = $dataSetAppSla->getRow();
            if ($this->pmCalendar->pmCalendarUid == '' || $proUid != $dataAppSla['PRO_UID']) {
                $proUid = $dataAppSla['PRO_UID'];
                $this->pmCalendar->getCalendar(null, $dataAppSla['PRO_UID']);
                $this->pmCalendar->getCalendarData();
            }

            $dataUpdateAppSla = array();

            $executePenal = ($dataAppSla['SLA_PEN_ENABLED'] == '1') ? true : false;

            $calculatePenal = true;
            $dataUpdateAppSla['APP_UID'] = $dataAppSla['APP_UID'];
            $dataUpdateAppSla['SLA_UID'] = $dataAppSla['SLA_UID'];

            // If the sla is Entire Process
            if ($dataAppSla['SLA_TYPE'] == 'PROCESS') {
                $dateIniSla = $dataAppSla['APP_SLA_INIT_DATE'];
                $dateDueSla = $dataAppSla['APP_SLA_DUE_DATE'];
                if ( (is_null($dataAppSla['APP_FINISH_DATE'])) || 
                     ($dataAppSla['APP_FINISH_DATE'] == '') || 
                     ($dataAppSla['APP_FINISH_DATE'] == '1902-01-01 00:00:00') ) {
                    $dateFinishApp = Date("Y-m-d H:i:s");
                    $dataUpdateAppSla['APP_SLA_FINISH_DATE'] = $dateFinishApp;
                    $statusAppSla = 'OPEN';
                } else {
                    $dateFinishApp = $dataAppSla['APP_FINISH_DATE'];
                    $dataUpdateAppSla['APP_SLA_FINISH_DATE'] = $dateFinishApp;
                    $statusAppSla = 'CLOSED';
                }
            } else {
                $criteriaDel = new Criteria('workflow');
                $criteriaDel->clearSelectColumns();
                $criteriaDel->addSelectColumn(AppDelegationPeer::DEL_DELEGATE_DATE );
                $criteriaDel->addSelectColumn(AppDelegationPeer::DEL_FINISH_DATE );

                $criteriaDel->add(AppDelegationPeer::APP_UID, $dataAppSla['APP_UID']);
                $criteriaDel->add(AppDelegationPeer::TAS_UID, $dataAppSla['SLA_TAS_START']);
                $dataSetDel = AppDelegationPeer::doSelectRs($criteriaDel);
                $dataSetDel->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                // if started the start task
                if ($dataSetDel->next()) {
                    $dataDel = $dataSetDel->getRow();

                    // if don't save the initiate and duedate the sla
                    if (is_null($dataAppSla['APP_SLA_INIT_DATE']) || ($dataAppSla['APP_SLA_INIT_DATE'] == '')) {
                        $dataAppSla['APP_SLA_INIT_DATE'] = $dataDel['DEL_DELEGATE_DATE'];
                        $dataUpdateAppSla['APP_SLA_INIT_DATE'] = $dataDel['DEL_DELEGATE_DATE'];

                        $dataAppSla['APP_SLA_DUE_DATE'] = $this->pmCalendar->calculateDate(
                            $dataDel['DEL_DELEGATE_DATE'],
                            $dataAppSla['SLA_TIME_DURATION'],
                            $dataAppSla['SLA_TIME_DURATION_MODE']);

                        $dataUpdateAppSla['APP_SLA_DUE_DATE'] = $dataAppSla['APP_SLA_DUE_DATE'];
                    }

                    // if the sla is the range tasks
                    if (($dataAppSla['SLA_TYPE'] == 'RANGE') && ($dataAppSla['SLA_TAS_START'] != $dataAppSla['SLA_TAS_END'])) {
                        $criteriaTaskEnd = new Criteria('workflow');
                        $criteriaTaskEnd->clearSelectColumns();
                        $criteriaTaskEnd->addSelectColumn(AppDelegationPeer::DEL_DELEGATE_DATE );
                        $criteriaTaskEnd->addSelectColumn(AppDelegationPeer::DEL_FINISH_DATE );

                        $criteriaTaskEnd->add(AppDelegationPeer::APP_UID, $dataAppSla['APP_UID']);
                        $criteriaTaskEnd->add(AppDelegationPeer::TAS_UID, $dataAppSla['SLA_TAS_END']);
                        $dataTaskEnd = AppDelegationPeer::doSelectRs($criteriaTaskEnd);
                        $dataTaskEnd->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                        // if the end task initiate
                        if ($dataTaskEnd->next()) {
                            $dataDelEnd = $dataTaskEnd->getRow();

                            // if wasn't finish the end task
                            if (is_null($dataDelEnd['DEL_FINISH_DATE']) || ($dataDelEnd['DEL_FINISH_DATE'] == '')) {
                                $dateFinishApp = Date("Y-m-d H:i:s");
                                $dataUpdateAppSla['APP_SLA_FINISH_DATE'] = $dateFinishApp;
                                $statusAppSla = 'OPEN';
                            } else {
                                // if the end task finished
                                $dateFinishApp = $dataDelEnd['DEL_FINISH_DATE'];
                                $dataUpdateAppSla['APP_SLA_FINISH_DATE'] = $dateFinishApp;
                                $statusAppSla = 'CLOSED';
                            }

                            // if wasn't start the end task
                        } else {
                            $dateFinishApp = Date("Y-m-d H:i:s");
                            $dataUpdateAppSla['APP_SLA_FINISH_DATE'] = $dateFinishApp;
                            $statusAppSla = 'OPEN';
                        }
                    } else {
                        // if the sla has a task

                        // if wasn't finish the end task
                        if (is_null($dataDel['DEL_FINISH_DATE']) || ($dataDel['DEL_FINISH_DATE'] == '')) {
                            $dateFinishApp = Date("Y-m-d H:i:s");
                            $dataUpdateAppSla['APP_SLA_FINISH_DATE'] = $dateFinishApp;
                            $statusAppSla = 'OPEN';

                            // if the end task finished
                        } else {
                            $dateFinishApp = $dataDel['DEL_FINISH_DATE'];
                            $dataUpdateAppSla['APP_SLA_FINISH_DATE'] = $dateFinishApp;
                            $statusAppSla = 'CLOSED';
                        }
                    }

                    $dateIniSla = $dataAppSla['APP_SLA_INIT_DATE'];
                    $dateDueSla = $dataAppSla['APP_SLA_DUE_DATE'];
                } else {
                    // if wasn't start the start task
                    $calculatePenal = false;
                    $statusAppSla = 'OPEN';
                }
            }


            if ($calculatePenal) {
                $penTime     = $dataAppSla['SLA_PEN_TIME'];
                $penTimeMode = $dataAppSla['SLA_PEN_TIME_MODE'];

                if ( G::toUpper($penTimeMode) == 'DAYS' ) {
                    $penTime = $penTime*$this->pmCalendarData['HOURS_FOR_DAY'];
                }

                $penTimeMinute = $penTime*60;
                $penValue    = $dataAppSla['SLA_PEN_VALUE'];

                $secondDuration = $this->pmCalendar->calculateDuration($dateIniSla, $dateFinishApp);
                $minuteDuration = pmSLAClass::minutesToHours($secondDuration);

                $valueExceeded  = 0;
                $valuePenality  = 0;

                if (strtotime($dateDueSla) < strtotime($dateFinishApp)) {
                    // later
                    $valueExceeded = $this->pmCalendar->calculateDuration($dateDueSla, $dateFinishApp);
                    $valueExceeded = pmSLAClass::minutesToHours($valueExceeded);

                    // rule 3
                    if ($executePenal) {
                        $valTemp = (float)$valueExceeded * (float)$penValue ;
                        $valuePenality = (float)$valTemp / (float)$penTimeMinute;
                    }
                }

                $dataUpdateAppSla['APP_SLA_PEN_VALUE'] = $valuePenality;
                $dataUpdateAppSla['APP_SLA_DURATION']  = $minuteDuration;
                $dataUpdateAppSla['APP_SLA_EXCEEDED']  = $valueExceeded;
            } else {
                $dataUpdateAppSla['APP_SLA_DURATION']  = 0;
                $dataUpdateAppSla['APP_SLA_PEN_VALUE'] = 0;
                $dataUpdateAppSla['APP_SLA_EXCEEDED']  = 0;
            }

            $dataUpdateAppSla['APP_SLA_STATUS'] = $statusAppSla;
            pmSLAClass::updateAppSla($dataUpdateAppSla);
            $countAppSla++;
        }

        if ($countAppSla > 0) {
            pmSLAClass::saveLogSla('cronSLA', 'action', 'Update ' . $countAppSla . ' registres in APP_SLA');
        }
    }
}

