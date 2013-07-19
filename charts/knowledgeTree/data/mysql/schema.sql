
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

#-----------------------------------------------------------------------------
#-- KT_APPLICATION
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `KT_APPLICATION`;


CREATE TABLE `KT_APPLICATION`
(
	`APP_UID` VARCHAR(32) default '' NOT NULL,
	`KT_FOLDER_ID` INTEGER default 0 NOT NULL,
	`KT_PARENT_ID` INTEGER default 0 NOT NULL,
	`KT_FOLDER_NAME` VARCHAR(100) default '' NOT NULL,
	`KT_FULL_PATH` VARCHAR(255) default '' NOT NULL,
	`KT_FOLDER_OUTPUT` INTEGER default 0 NOT NULL,
	`KT_FOLDER_ATTACHMENT` INTEGER default 0 NOT NULL,
	`KT_FOLDER_EMAIL` INTEGER default 0 NOT NULL,
	`KT_CREATE_USER` VARCHAR(32) default '' NOT NULL,
	`KT_CREATE_DATE` DATETIME  NOT NULL,
	`KT_UPDATE_DATE` DATETIME  NOT NULL,
	PRIMARY KEY (`APP_UID`),
	KEY `indexApp`(`KT_FOLDER_ID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='The application folder in Knowledge Tree';
#-----------------------------------------------------------------------------
#-- KT_PROCESS
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `KT_PROCESS`;


CREATE TABLE `KT_PROCESS`
(
	`PRO_UID` VARCHAR(32) default '' NOT NULL,
	`KT_FOLDER_ID` INTEGER default 0 NOT NULL,
	`KT_PARENT_ID` INTEGER default 0 NOT NULL,
	`KT_FOLDER_NAME` VARCHAR(100) default '' NOT NULL,
	`KT_FULL_PATH` VARCHAR(255) default '' NOT NULL,
	`KT_CREATE_USER` VARCHAR(32) default '' NOT NULL,
	`KT_CREATE_DATE` DATETIME  NOT NULL,
	`KT_UPDATE_DATE` DATETIME  NOT NULL,
	PRIMARY KEY (`PRO_UID`),
	KEY `indexApp`(`KT_FOLDER_ID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='The Process folder in Knowledge Tree';
#-----------------------------------------------------------------------------
#-- KT_DOCUMENT
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `KT_DOCUMENT`;


CREATE TABLE `KT_DOCUMENT`
(
	`DOC_UID` VARCHAR(32) default '' NOT NULL,
	`DOC_TYPE` VARCHAR(4) default '' NOT NULL,
	`DOC_PMTYPE` VARCHAR(10) default 'OUTPUT' NOT NULL,
	`PRO_UID` VARCHAR(32) default '' NOT NULL,
	`APP_UID` VARCHAR(32) default '' NOT NULL,
	`KT_DOCUMENT_ID` INTEGER default 0 NOT NULL,
	`KT_STATUS` VARCHAR(32) default '' NOT NULL,
	`KT_DOCUMENT_TITLE` VARCHAR(150) default '' NOT NULL,
	`KT_FULL_PATH` VARCHAR(255) default '' NOT NULL,
	`KT_CREATE_USER` VARCHAR(32) default '' NOT NULL,
	`KT_CREATE_DATE` DATETIME  NOT NULL,
	`KT_UPDATE_DATE` DATETIME  NOT NULL,
	PRIMARY KEY (`DOC_UID`,`DOC_TYPE`),
	KEY `indexApp`(`KT_DOCUMENT_ID`),
    KEY `indexKtDocument`(`APP_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='The Process folder in Knowledge Tree';
#-----------------------------------------------------------------------------
#-- KT_DOC_TYPE
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `KT_DOC_TYPE`;


CREATE TABLE `KT_DOC_TYPE`
(
	`PRO_UID` VARCHAR(32) default '' NOT NULL,
	`DOC_UID` VARCHAR(32) default '' NOT NULL,
	`DOC_KT_TYPE_ID` VARCHAR(200) default '' NOT NULL,
	PRIMARY KEY (`PRO_UID`,`DOC_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='KT DocType PM Relation';
#-----------------------------------------------------------------------------
#-- KT_FIELDS_MAP
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `KT_FIELDS_MAP`;


CREATE TABLE `KT_FIELDS_MAP`
(
	`DOC_KT_TYPE_ID` VARCHAR(200) default '' NOT NULL,
	`PRO_UID` VARCHAR(32) default '' NOT NULL,
	`FIELDS_MAP` MEDIUMTEXT  NOT NULL,
	`DESTINATION_PATH` VARCHAR(250) default '' NOT NULL,
	PRIMARY KEY (`DOC_KT_TYPE_ID`,`PRO_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='KT DocType PM Map Fields';
#-----------------------------------------------------------------------------
#-- KT_CONFIG
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `KT_CONFIG`;


CREATE TABLE `KT_CONFIG`
(
	`USR_UID` VARCHAR(32) default '' NOT NULL,
	`KT_USERNAME` VARCHAR(100) default '' NOT NULL,
	`KT_PASSWORD` VARCHAR(100) default '' NOT NULL,
	PRIMARY KEY (`USR_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='Config table for KT';
# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
