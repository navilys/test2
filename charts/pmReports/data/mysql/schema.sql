-- ---------------------------------------------------------------------------------------------------------------------
-- PM_REPORT_PERMISSIONS
-- ---------------------------------------------------------------------------------------------------------------------

DROP TABLE IF EXISTS `PM_REPORT_PERMISSIONS`;
CREATE TABLE `PM_REPORT_PERMISSIONS`(
    `PMR_UID`                VARCHAR(32) default '' NOT NULL,
    `ADD_TAB_UID`            VARCHAR(32) default '' NOT NULL,
    `PMR_TYPE`               VARCHAR(20) default '' NOT NULL,
    `PMR_OWNER_UID`          VARCHAR(32),
    `PMR_CREATE_DATE`        DATETIME NOT NULL,
    `PMR_UPDATE_DATE`        DATETIME NOT NULL,
    `PMR_STATUS`             TINYINT NOT NULL,
    PRIMARY KEY(`PMR_UID`),
    KEY `indexApp`(`ADD_TAB_UID`)
)Engine=MyISAM DEFAULT CHARSET='utf8' COMMENT='The plugin table for pmReports';


