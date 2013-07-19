
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

#-----------------------------------------------------------------------------
#-- FTP_MONITOR_SETTING
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `FTP_MONITOR_SETTING`;

CREATE TABLE IF NOT EXISTS `FTP_MONITOR_SETTING`
(
  `FTP_UID` varchar(32) NOT NULL DEFAULT '',
  `CONNECTION_TYPE` varchar(8) NOT NULL DEFAULT '',
  `HOST` varchar(255) NOT NULL DEFAULT '',
  `PORT` varchar(5) NOT NULL DEFAULT '',
  `USER` varchar(100) NOT NULL DEFAULT '',
  `PASS` varchar(100) NOT NULL DEFAULT '',
  `SEARCH_PATTERN` varchar(100) NOT NULL DEFAULT '',
  `FTP_PATH` varchar(100) NOT NULL DEFAULT '',
  `INPUT_DOCUMENT_UID` varchar(32) NOT NULL,
  `XML_SEARCH` varchar(100) NOT NULL DEFAULT '',
  `PRO_UID` varchar(32) NOT NULL DEFAULT '',
  `TAS_UID` varchar(32) NOT NULL DEFAULT '',
  `DEL_USER_UID` varchar(100) NOT NULL DEFAULT '',
  `FTP_STATUS` varchar(8) NOT NULL DEFAULT '',
  PRIMARY KEY (`FTP_UID`,`CONNECTION_TYPE`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='FTP Monitor Settings of Case Scheduler Job';
#-----------------------------------------------------------------------------
#-- FTP_MONITOR_LOGS
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `FTP_MONITOR_LOGS`;

CREATE TABLE IF NOT EXISTS `FTP_MONITOR_LOGS`
(
  `FTP_LOG_UID` varchar(32) NOT NULL DEFAULT '',
  `FTP_UID` varchar(32) DEFAULT '',
  `EXECUTION_DATE` varchar(255) NOT NULL DEFAULT '',
  `EXECUTION_TIME` varchar(100) NOT NULL DEFAULT '',
  `RESULT` text NOT NULL,
  `EXECUTION_DATETIME` varchar(19) NOT NULL,
  `FAILED` int(32) NOT NULL DEFAULT '0',
  `SUCCEEDED` int(32) NOT NULL DEFAULT '0',
  `PROCESSED` int(32) NOT NULL DEFAULT '0',
  PRIMARY KEY (`FTP_LOG_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='The plugin demo table for pmFtpMonitor';
#-----------------------------------------------------------------------------
#-- FTP_MONITOR_LOGS_DETAILS
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `FTP_MONITOR_LOGS_DETAILS`;

CREATE TABLE IF NOT EXISTS `FTP_MONITOR_LOGS_DETAILS`
(
  `FTP_LOG_DET_UID` varchar(32) NOT NULL DEFAULT '',
  `FTP_LOG_UID` varchar(32) NOT NULL DEFAULT '',
  `APP_UID` varchar(32) NOT NULL,
  `EXECUTION_DATETIME` varchar(19) NOT NULL,
  `FULL_PATH` varchar(256) NOT NULL DEFAULT '',
  `HAVE_XML` char(5) NOT NULL DEFAULT 'FALSE',
  `VARIABLES` text NOT NULL,
  `STATUS` varchar(8) NOT NULL DEFAULT '',
  `DESCRIPTION` varchar(256) NOT NULL DEFAULT '',
  PRIMARY KEY (`FTP_LOG_DET_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='The plugin demo table for pmFtpMonitor';
# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
