
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

#-----------------------------------------------------------------------------
#-- PH_USER_ROLE
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `PH_USER_ROLE`;


CREATE TABLE `PH_USER_ROLE`
(
	`ROL_OBJ_UID` VARCHAR(32) default '0' NOT NULL,
	`ROL_UID` VARCHAR(32) default '0' NOT NULL,
	`OBJ_UID` VARCHAR(32) default '0' NOT NULL,
	`OBJ_TYPE` VARCHAR(32) default '0' NOT NULL,
	`OBJ_DASHBOARD` VARCHAR(32) default '0' NOT NULL,
	PRIMARY KEY (`ROL_OBJ_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='Pentaho Plugin User-Role relation table';
#-----------------------------------------------------------------------------
#-- PH_ROLE
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `PH_ROLE`;


CREATE TABLE `PH_ROLE`
(
	`ROL_UID` VARCHAR(32) default '0' NOT NULL,
	`ROL_CODE` VARCHAR(32) default '0' NOT NULL,
	PRIMARY KEY (`ROL_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='Pentaho Plugin Role table';
#-----------------------------------------------------------------------------
#-- PH_ROLE_REPORT
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `PH_ROLE_REPORT`;


CREATE TABLE `PH_ROLE_REPORT`
(
	`ROL_REP_UID` VARCHAR(32) default '0' NOT NULL,
	`ROL_UID` VARCHAR(32) default '0' NOT NULL,
	`REP_UID` VARCHAR(32) default '0' NOT NULL,
	PRIMARY KEY (`ROL_REP_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='Pentaho Plugin Role-Report relation table';
#-----------------------------------------------------------------------------
#-- PH_REPORT
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `PH_REPORT`;


CREATE TABLE `PH_REPORT`
(
	`REP_UID` VARCHAR(32) default '0' NOT NULL,
	`REP_PATH` VARCHAR(128) default '0' NOT NULL,
	`REP_TITLE` VARCHAR(128) default '0' NOT NULL,
	`REP_NAME` VARCHAR(128) default '0' NOT NULL,
	PRIMARY KEY (`REP_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='Pentaho Plugin Reports table';
# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;


INSERT INTO `PH_ROLE` (`ROL_UID`, `ROL_CODE`) VALUES
('00000000000000000000000000000001', 'PH_ADMIN'),
('00000000000000000000000000000002', 'PH_USER');

INSERT INTO `PH_USER_ROLE` (`ROL_OBJ_UID` ,`ROL_UID` ,`OBJ_UID` ,`OBJ_TYPE` ,`OBJ_DASHBOARD`)
VALUES ('3831110824c2368c100faf9031385921', '00000000000000000000000000000001', '00000000000000000000000000000001', 'USER', '0');