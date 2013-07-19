
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

#-----------------------------------------------------------------------------
#-- ADDONS_STORE
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `ADDONS_STORE`;
CREATE TABLE `ADDONS_STORE`
(
	`STORE_ID` VARCHAR(32)  NOT NULL,
	`STORE_VERSION` INTEGER,
	`STORE_LOCATION` VARCHAR(2048)  NOT NULL,
	`STORE_TYPE` VARCHAR(255)  NOT NULL,
	`STORE_LAST_UPDATED` DATETIME,
	PRIMARY KEY (`STORE_ID`)
)ENGINE=MyISAM ;

#-----------------------------------------------------------------------------
#-- ADDONS_MANAGER
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `ADDONS_MANAGER`;
CREATE TABLE `ADDONS_MANAGER`
(
	`ADDON_ID` VARCHAR(255)  NOT NULL,
	`STORE_ID` VARCHAR(32)  NOT NULL,
	`ADDON_NAME` VARCHAR(255)  NOT NULL,
	`ADDON_NICK` VARCHAR(255)  NOT NULL,
	`ADDON_DOWNLOAD_FILENAME` VARCHAR(1024),
	`ADDON_DESCRIPTION` VARCHAR(2048),
	`ADDON_STATE` VARCHAR(255)  NOT NULL,
	`ADDON_STATE_CHANGED` DATETIME,
	`ADDON_STATUS` VARCHAR(255)  NOT NULL,
	`ADDON_VERSION` VARCHAR(255)  NOT NULL,
	`ADDON_TYPE` VARCHAR(255)  NOT NULL,
	`ADDON_PUBLISHER` VARCHAR(255),
	`ADDON_RELEASE_DATE` DATETIME,
	`ADDON_RELEASE_TYPE` VARCHAR(255),
	`ADDON_RELEASE_NOTES` VARCHAR(255),
	`ADDON_DOWNLOAD_URL` VARCHAR(2048),
	`ADDON_DOWNLOAD_PROGRESS` FLOAT,
	`ADDON_DOWNLOAD_MD5` VARCHAR(32),
	PRIMARY KEY (`ADDON_ID`,`STORE_ID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='Addons manager';


#-----------------------------------------------------------------------------
#-- LICENSE_MANAGER
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `LICENSE_MANAGER`;
CREATE TABLE IF NOT EXISTS `LICENSE_MANAGER` (
  `LICENSE_UID` varchar(32) NOT NULL DEFAULT '',
  `LICENSE_USER` varchar(150) NOT NULL DEFAULT '0',
  `LICENSE_START` int(11) NOT NULL DEFAULT '0',
  `LICENSE_END` int(11) NOT NULL DEFAULT '0',
  `LICENSE_SPAN` int(11) NOT NULL DEFAULT '0',
  `LICENSE_STATUS` varchar(100) DEFAULT '',
  `LICENSE_DATA` mediumtext NOT NULL,
  `LICENSE_PATH` varchar(255) NOT NULL DEFAULT '0',
  `LICENSE_WORKSPACE` varchar(32) NOT NULL DEFAULT '0',
  `LICENSE_TYPE` varchar(32) NOT NULL DEFAULT '0',
  PRIMARY KEY (`LICENSE_UID`)
)ENGINE=MyISAM DEFAULT CHARSET='utf8' COMMENT='Licenses Manager';


# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;