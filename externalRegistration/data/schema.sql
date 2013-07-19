#-----------------------------------------------------------------------------
#-- ER_CONFIGURATION
#-----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `ER_CONFIGURATION`
(
  `ER_UID`                  VARCHAR(32) default '' NOT NULL,
  `ER_TITLE`                VARCHAR(152) default '' NOT NULL,
  `PRO_UID`                 VARCHAR(32) default '' NOT NULL,
  `ER_TEMPLATE`             VARCHAR(100) default '' NOT NULL,
  `DYN_UID`                 VARCHAR(32) default '',
  `ER_VALID_DAYS`           INTEGER default 5 NOT NULL,
  `ER_ACTION_ASSIGN`        VARCHAR(10) default '',
  `ER_OBJECT_UID`           VARCHAR(32) default '',
  `ER_ACTION_START_CASE`    INTEGER default 0,
  `TAS_UID`                 VARCHAR(32) default '' NOT NULL,
  `ER_ACTION_EXECUTE_TRIGGER`   INTEGER default 0,
  `TRI_UID`                   VARCHAR(32) default '',
  `ER_CREATE_DATE`            DATETIME NOT NULL,
  `ER_UPDATE_DATE`            DATETIME,
  PRIMARY KEY (`ER_UID`),
  KEY `indexApp`(`PRO_UID`, `DYN_UID`, `TAS_UID`, `TRI_UID`)
)Engine=MyISAM  DEFAULT CHARSET='utf8' COMMENT='The table for external registration configurations';

#-----------------------------------------------------------------------------
#-- ER_REQUESTS
#-----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `ER_REQUESTS`
(
  `ER_REQ_UID`                VARCHAR(32) default '' NOT NULL,
  `ER_UID`                    VARCHAR(152) default '' NOT NULL,
  `ER_REQ_DATA`               MEDIUMTEXT  NOT NULL,
  `ER_REQ_DATE`               DATETIME NOT NULL,
  `ER_REQ_COMPLETED`          TINYINT NOT NULL,
  `ER_REQ_COMPLETED_DATE`     DATETIME,
  PRIMARY KEY (`ER_REQ_UID`),
  KEY `indexApp`(`ER_UID`)
)Engine=MyISAM  DEFAULT CHARSET='utf8' COMMENT='The table for external registration requests';
