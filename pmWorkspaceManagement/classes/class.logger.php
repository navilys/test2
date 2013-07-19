<?php
require_once(PATH_PLUGINS.'pmWorkspaceManagement'.PATH_SEP.'classes'.PATH_SEP.'model'.PATH_SEP.'MttLog.php');
require_once(PATH_PLUGINS.'pmWorkspaceManagement'.PATH_SEP.'classes'.PATH_SEP.'model'.PATH_SEP.'MttLogPeer.php');

/**
 * Manage log
 * @author Edwin Paredes
 *
 */
class logger{
	const bPropelConfigured = false;
	public static function register($sAction, $sDescription, $sType, $sUsrUid = "", $sAdditionalDetails = ""){
		self::configurePropel();
		$sUsrUid = ($sUsrUid!="")? $sUsrUid:$_SESSION['USER_LOGGED'];
		$oLogger = new MttLog();
		$oLogger->setUsrUid($sUsrUid);
		$oLogger->setLogDatetime(date("Y-m-d H:i:s"));
		$oLogger->setLogAction($sAction);
		$oLogger->setLogDescription($sDescription);
		$oLogger->setLogType($sType);
		$oLogger->setLogIp($_SERVER['SERVER_ADDR']);
		$oLogger->setLogAdditionalDetails($sAdditionalDetails);
		$oLogger->save();
	}
	public function configurePropel(){
		if(!self::bPropelConfigured){
			require(PATH_PLUGINS.'pmWorkspaceManagement'.PATH_SEP.'db.php');
			$configuration = PROPEL::getConfiguration();
			$sDsn = $dbAdapter."://".$dbUser.":".$dbUser."@".$dbHost."/".$dbName;
			$sDsn .= '?encoding=utf8';
			$configuration['datasources']['multitenant'] = array(
					'connection' => $sDsn,
					'adapter' => $dbAdapter
				);
			PROPEL::initConfiguration($configuration);
		}
	}

    // function that prepares the Propel Criteria object used for filtering the log in the log viewer
    private function prepareCriteriaToFilterLog($dDateFrom, $dDateTo, $sIpAddress, $sActionName, $sTypeName, $sContent) {
        
        // Create the criteria object for the query
        $oCriteria = new Criteria();
		$oCriteria->clearSelectColumns();
        $oCriteria->addSelectColumn(MttLogPeer::LOG_ID);
        $oCriteria->addSelectColumn(MttLogPeer::LOG_DATETIME);
        $oCriteria->addSelectColumn(MttLogPeer::LOG_IP);
        $oCriteria->addSelectColumn(MttLogPeer::LOG_ACTION);
        $oCriteria->addSelectColumn(MttLogPeer::LOG_TYPE);
        $oCriteria->addSelectColumn(MttLogPeer::LOG_DESCRIPTION);

        // shorten then details column to 50 chars, so that we don't send huge quantities of data back.
        $oCriteria->addAsColumn("DETAILS", "CASE WHEN LENGTH(LOG_ADDITIONAL_DETAILS) > 50 THEN CONCAT(LEFT(LOG_ADDITIONAL_DETAILS,47),'...') ELSE LOG_ADDITIONAL_DETAILS END");
        
        // apply the filters.
        if ($dDateFrom != NULL)
            $oCriteria->add(MttLogPeer::LOG_DATETIME, $dDateFrom->format("Y-m-d"), Criteria::GREATER_EQUAL);
        if ($dDateTo != NULL)
            $oCriteria->add(MttLogPeer::LOG_DATETIME, $dDateTo->format("Y-m-d"), Criteria::LESS_EQUAL);
        if ($sIpAddress != NULL)
            $oCriteria->add(MttLogPeer::LOG_IP, '%'.$sIpAddress.'%', Criteria::LIKE);
        if ($sActionName != NULL)
            $oCriteria->add(MttLogPeer::LOG_ACTION, $sActionName);
        if ($sTypeName != NULL)
            $oCriteria->add(MttLogPeer::LOG_TYPE, $sTypeName);
        if ($sContent != NULL) {
            $oDescriptionCriterion = $oCriteria->getNewCriterion(MttLogPeer::LOG_DESCRIPTION, '%'.$sContent.'%',Criteria::LIKE);
            $oDetailsCriterion = $oCriteria->getNewCriterion(MttLogPeer::LOG_ADDITIONAL_DETAILS, '%'.$sContent.'%',Criteria::LIKE);
            $oCriteria->add($oDescriptionCriterion->addOr($oDetailsCriterion));
        }

        // return created criteria object
        return $oCriteria;

    }

    // function to filter the log according to the filters for the multitenant log viewer screen.
    public function filterLogList($dDateFrom, $dDateTo, $sIpAddress, $sActionName, $sTypeName, $sContent, $iLimit, $iOffset, $iSortBy, $sSortDirection) {
        self::configurePropel();
        
        $oCriteria = $this->prepareCriteriaToFilterLog($dDateFrom, $dDateTo, $sIpAddress, $sActionName, $sTypeName, $sContent);

        // add the sorting:
        if ($sSortDirection == "asc")
            $oCriteria->addAscendingOrderByColumn($iSortBy);
        else
            $oCriteria->AddDescendingOrderByColumn($iSortBy);

        // set the paging properties
        $oCriteria->setLimit($iLimit);
		$oCriteria->setOffset($iOffset);

        /*
        $oCriteria->setDbName("multitenant");
        $con = Propel::getConnection($oCriteria->getDbName());
        $dbMap = Propel::getDatabaseMap($oCriteria->getDbName());
        $aParameters = array();
        $sql =  BasePeer::createSelectSql($oCriteria, $aParameters);
        G::pr($sql);
        */

        // Execute the query and return the Recordset
        $oLogRecordset = MttLogPeer::DoSelectRS($oCriteria);
		$oLogRecordset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

		return $oLogRecordset;
    }

    // function that return the total number of records that comply with the given filters
    public function getFilteredLogListCount($dDateFrom, $dDateTo, $sIpAddress, $sActionName, $sTypeName, $sContent) {
        self::configurePropel();

        $oCriteria = $this->prepareCriteriaToFilterLog($dDateFrom, $dDateTo, $sIpAddress, $sActionName, $sTypeName, $sContent);
        $iCount = MttLogPeer::DoCount($oCriteria);

        return $iCount;
    }

    // retrieves the details of a log entry based on the ID
    public function getDetailsFromLogID($logID) {
        
        self::configurePropel();
        $logEntry = MttLogPeer::retrieveByPK($logID);
        if ($logEntry != NULL) {
            return $logEntry->getLogAdditionalDetails();
        }

        // if the entry does not exist, return an empty string... maybe this should throw an exception though.
        return "";
    }
}