-- ---------------------------------------------------------------------------------------------------------------------
-- SIGPLUS
-- ---------------------------------------------------------------------------------------------------------------------

-- ---------------------------------------------------------------------------------------------------------------------
-- SIGPLUS_SIGNERS
-- ---------------------------------------------------------------------------------------------------------------------

DROP TABLE IF EXISTS SIGPLUS_SIGNERS;

CREATE TABLE SIGPLUS_SIGNERS(
  STP_UID     varchar(32) NOT NULL,
  PRO_UID     varchar(32) DEFAULT '' NOT NULL,
  TAS_UID     varchar(32) DEFAULT '' NOT NULL,
  DOC_UID     varchar(32) DEFAULT '' NOT NULL,
  SIG_SIGNERS text NOT NULL,
  PRIMARY KEY(STP_UID)
)ENGINE=MyISAM DEFAULT CHARSET='utf8' COMMENT='Signers Table for the SigPlus Plugin';
