
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

#-----------------------------------------------------------------------------
#-- ELOCK_DYNAFORM
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `ELOCK_DYNAFORM`;


CREATE TABLE `ELOCK_DYNAFORM`
(
	`UID_DYNAFORM` VARCHAR(32) default '' NOT NULL,
	`UID_APPLICATION` VARCHAR(32) default '' NOT NULL,
	`BASE64` MEDIUMTEXT  NOT NULL,
	`USER` VARCHAR(100) default '' NOT NULL,
	`timestamp` VARCHAR(100) default '' NOT NULL,
	PRIMARY KEY (`UID_DYNAFORM`,`UID_APPLICATION`),
	KEY `indexApp`(`UID_DYNAFORM`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='eLock table to save the signed Dynaforms';
#-----------------------------------------------------------------------------
#-- ELOCK_USERS
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `ELOCK_USERS`;


CREATE TABLE `ELOCK_USERS`
(
	`USR_USERNAME` VARCHAR(50) default '' NOT NULL,
	`USR_PASSWORD` VARCHAR(50) default '' NOT NULL,
	PRIMARY KEY (`USR_USERNAME`),
	KEY `indexApp`(`USR_USERNAME`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='Table for Users';
#-----------------------------------------------------------------------------
#-- ELOCK_OUTPUT_CFG
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `ELOCK_OUTPUT_CFG`;


CREATE TABLE `ELOCK_OUTPUT_CFG`
(
	`STP_UID` VARCHAR(32) default '' NOT NULL,
	`PRO_UID` VARCHAR(32) default '' NOT NULL,
	`TAS_UID` VARCHAR(32) default '' NOT NULL,
	`DOC_UID` VARCHAR(32) default '' NOT NULL,
	PRIMARY KEY (`STP_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='Table to save the output config';
#-----------------------------------------------------------------------------
#-- ELOCK_SIGNED_DOCUMENT
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `ELOCK_SIGNED_DOCUMENT`;


CREATE TABLE `ELOCK_SIGNED_DOCUMENT`
(
	`APP_DOC_UID` VARCHAR(32) default '' NOT NULL,
	`DOC_VERSION` INTEGER default 1 NOT NULL,
	`DOC_UID` VARCHAR(32) default '' NOT NULL,
	`USR_UID` VARCHAR(32) default '' NOT NULL,
	`SIGN_DATE` DATETIME  NOT NULL,
	PRIMARY KEY (`APP_DOC_UID`,`DOC_VERSION`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='Signed Documents';
# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
