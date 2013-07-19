
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

#-----------------------------------------------------------------------------
#-- RULE_SET
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `RULE_SET`;


CREATE TABLE `RULE_SET`
(
	`RST_UID` VARCHAR(32) default '' NOT NULL,
	`RST_NAME` VARCHAR(64) default '' NOT NULL,
	`RST_DESCRIPTION` VARCHAR(256) default '',
	`RST_TYPE` VARCHAR(10) default '',
	`RST_STRUCT` MEDIUMTEXT,
	`RST_SOURCE` MEDIUMTEXT,
	`RST_CREATE_DATE` DATETIME,
	`RST_UPDATE_DATE` DATETIME,
	`RST_CHECKSUM` MEDIUMTEXT,
	`RST_DELETED` INTEGER default 0,
	`PRO_UID` VARCHAR(32)  NOT NULL,
	PRIMARY KEY (`RST_UID`)
)ENGINE=InnoDB ;
#-----------------------------------------------------------------------------
#-- GLOBAL_FIELDS
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `GLOBAL_FIELDS`;


CREATE TABLE `GLOBAL_FIELDS`
(
	`GF_NAME` VARCHAR(100) default '' NOT NULL,
	`GF_VALUE` MEDIUMTEXT,
	`GF_TYPE` VARCHAR(32) default '',
	`GF_QUERY` VARCHAR(512) default '',
	`DBS_UID` VARCHAR(32) default '',
	PRIMARY KEY (`GF_NAME`)
)ENGINE=InnoDB ;
# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
