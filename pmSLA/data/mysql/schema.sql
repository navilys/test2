
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

#-----------------------------------------------------------------------------
#-- SLA
#-----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `SLA`
(
	`SLA_UID` VARCHAR(32) default '' NOT NULL,
	`PRO_UID` VARCHAR(32) default '' NOT NULL,
	`SLA_NAME` VARCHAR(50) default '' NOT NULL,
	`SLA_DESCRIPTION` VARCHAR(250) default '' NOT NULL,
	`SLA_TYPE` VARCHAR(20) default '' NOT NULL,
	`SLA_TAS_START` VARCHAR(32) default '' NOT NULL,
	`SLA_TAS_END` VARCHAR(32) default '' NOT NULL,
	`SLA_TIME_DURATION` INTEGER default 0 NOT NULL,
	`SLA_TIME_DURATION_MODE` VARCHAR(10) default 'HOURS' NOT NULL,
	`SLA_CONDITIONS` VARCHAR(150) default '' NOT NULL,
	`SLA_PEN_ENABLED` INTEGER default 0 NOT NULL,
	`SLA_PEN_TIME` INTEGER default 0 NOT NULL,
	`SLA_PEN_TIME_MODE` VARCHAR(10) default 'HOURS' NOT NULL,
	`SLA_PEN_VALUE` INTEGER default 0 NOT NULL,
	`SLA_PEN_VALUE_UNIT` VARCHAR(30) default '' NOT NULL,
	`SLA_STATUS` VARCHAR(20) default '' NOT NULL,
	PRIMARY KEY (`SLA_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='The plugin table for SLA';
#-----------------------------------------------------------------------------
#-- APP_SLA
#-----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `APP_SLA`
(
	`APP_UID` VARCHAR(32) default '' NOT NULL,
	`SLA_UID` VARCHAR(32) default '' NOT NULL,
	`APP_SLA_INIT_DATE` DATETIME,
	`APP_SLA_DUE_DATE` DATETIME,
	`APP_SLA_FINISH_DATE` DATETIME,
	`APP_SLA_DURATION` DOUBLE default 0 NOT NULL,
	`APP_SLA_REMAINING` DOUBLE default 0 NOT NULL,
	`APP_SLA_EXCEEDED` DOUBLE default 0 NOT NULL,
	`APP_SLA_PEN_VALUE` DOUBLE default 0 NOT NULL,
	`APP_SLA_STATUS` VARCHAR(20) default '' NOT NULL,
	PRIMARY KEY (`APP_UID`,`SLA_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='The plugin table for Application SLA';
# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
