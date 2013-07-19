#-----------------------------------------------------------------------------
#-- ABE_CONFIGURATION
#-----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `ABE_CONFIGURATION`
(
  `ABE_UID`                       VARCHAR(32) default '' NOT NULL,
  `PRO_UID`                       VARCHAR(32) default '' NOT NULL,
  `TAS_UID`                       VARCHAR(32) default '' NOT NULL,
  `ABE_TYPE`                      VARCHAR(10) default '' NOT NULL,
  `ABE_TEMPLATE`                  VARCHAR(100) default '' NOT NULL,
  `ABE_DYN_TYPE`                  VARCHAR(10) default '' NOT NULL,
  `DYN_UID`                       VARCHAR(32) default '' NOT NULL,
  `ABE_EMAIL_FIELD`               VARCHAR(255) default '' NOT NULL,
  `ABE_ACTION_FIELD`              VARCHAR(255) default '' ,
  `ABE_CASE_NOTE_IN_RESPONSE`     INTEGER default 0 NOT NULL,
  `ABE_CREATE_DATE`               DATETIME  NOT NULL,
  `ABE_UPDATE_DATE`               DATETIME,
  PRIMARY KEY (`ABE_UID`),
  KEY `indexApp`(`PRO_UID`)
)Engine=MyISAM  DEFAULT CHARSET='utf8' COMMENT='The application folder in Action By Email';

#-----------------------------------------------------------------------------
#-- ABE_REQUESTS
#-----------------------------------------------------------------------------

CREATE TABLE  IF NOT EXISTS `ABE_REQUESTS`
(
  `ABE_REQ_UID`               VARCHAR(32) default '' NOT NULL,
  `ABE_UID`                   VARCHAR(32) default '' NOT NULL,
  `APP_UID`                   VARCHAR(32) default '' NOT NULL,
  `DEL_INDEX`                 INTEGER default 0 NOT NULL,
  `ABE_REQ_SENT_TO`           VARCHAR(100) default '' NOT NULL,
  `ABE_REQ_SUBJECT`           VARCHAR(150) default '' NOT NULL,
  `ABE_REQ_BODY`              MEDIUMTEXT  NOT NULL,
  `ABE_REQ_DATE`              DATETIME NOT NULL,
  `ABE_REQ_STATUS`            VARCHAR(10) default '',
  `ABE_REQ_ANSWERED`          TINYINT NOT NULL,
  PRIMARY KEY (`ABE_REQ_UID`),
  KEY `indexApp`(`ABE_UID`)
)Engine=MyISAM  DEFAULT CHARSET='utf8' COMMENT='The Process folder in Action By Email';

#-----------------------------------------------------------------------------
#-- ABE_RESPONSES
#-----------------------------------------------------------------------------

CREATE TABLE  IF NOT EXISTS `ABE_RESPONSES`
(
  `ABE_RES_UID`         VARCHAR(32) default '' NOT NULL,
  `ABE_REQ_UID`         VARCHAR(32) default '' NOT NULL,
  `ABE_RES_CLIENT_IP`   VARCHAR(20) default '' NOT NULL,
  `ABE_RES_DATA`        TEXT NOT NULL,
  `ABE_RES_DATE`        DATETIME  NOT NULL,
  `ABE_RES_STATUS`      VARCHAR(10) default '' NOT NULL,
  `ABE_RES_MESSAGE`     VARCHAR(255) default '',
  PRIMARY KEY (`ABE_RES_UID`),
  KEY `indexApp`(`ABE_REQ_UID`)
)Engine=MyISAM  DEFAULT CHARSET='utf8' COMMENT='The Process folder in Action By Email';

