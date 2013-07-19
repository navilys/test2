-- This is a fix for InnoDB in MySQL >= 4.1.x
-- It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------------------------------------------------------
-- PM_REPORT
-- ---------------------------------------------------------------------------------------------------------------------

DROP TABLE IF EXISTS PM_REPORT;

CREATE TABLE PM_REPORT(
	 PMR_UID     VARCHAR(32) default '' NOT NULL,
	 DYN_UID     VARCHAR(32) default '' NOT NULL,
	 REP_TAB_UID VARCHAR(32) default '' NOT NULL,
	 PMR_STATUS  VARCHAR(32) default '' NOT NULL,
	 PRIMARY KEY(PMR_UID)
)ENGINE=MyISAM DEFAULT CHARSET='utf8' COMMENT='The plugin table for pmReports';

-- ---------------------------------------------------------------------------------------------------------------------
-- PM_REPORT_PERMISSIONS
-- ---------------------------------------------------------------------------------------------------------------------

DROP TABLE IF EXISTS PM_REPORT_PERMISSIONS;

CREATE TABLE PM_REPORT_PERMISSIONS(
  PMR_UID   VARCHAR(32) default '' NOT NULL,
  USR_UID   VARCHAR(32) default '' NOT NULL,
  PMRP_TYPE VARCHAR(10) default '' NOT NULL,
  PRIMARY KEY(PMR_UID, USR_UID)
)ENGINE=MyISAM DEFAULT CHARSET='utf8' COMMENT='The plugin table for pmReports';

-- This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
