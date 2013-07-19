SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------------------------------------------------------
-- CASE_CONSOLIDATED
-- ---------------------------------------------------------------------------------------------------------------------

DROP TABLE IF EXISTS CASE_CONSOLIDATED;

CREATE TABLE CASE_CONSOLIDATED(
  TAS_UID     VARCHAR(32) default '' NOT NULL,
  DYN_UID     VARCHAR(32) default '' NOT NULL,
  REP_TAB_UID VARCHAR(32) default '' NOT NULL,
  CON_STATUS  VARCHAR(32) default 'ACTIVE' NOT NULL,
  PRIMARY KEY(TAS_UID)
)ENGINE=MyISAM DEFAULT CHARSET='utf8' COMMENT='The plugin table for consolidate';

-- This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
