--
-- Database: `multitenant`
--
CREATE DATABASE IF NOT EXISTS `multitenant` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `multitenant`;

-- --------------------------------------------------------

--
-- Table structure for table `PMT_LOGGER`
--

CREATE TABLE IF NOT EXISTS `MTT_LOG` (
  `LOG_ID` int(10) NOT NULL AUTO_INCREMENT,
  `USR_UID` varchar(32) NOT NULL,
  `LOG_IP` varchar(50) NOT NULL,
  `LOG_DATETIME` datetime NOT NULL,
  `LOG_ACTION` varchar(50) NOT NULL,
  `LOG_DESCRIPTION` varchar(255) DEFAULT NULL,
  `LOG_TYPE` varchar(20) DEFAULT NULL,
  `LOG_ADDITIONAL_DETAILS` mediumtext NOT NULL,
  PRIMARY KEY (`LOG_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

CREATE USER 'multitenant'@'localhost' IDENTIFIED BY 'multitenant';

GRANT ALL ON * . * TO 'multitenant'@'localhost' IDENTIFIED BY 'multitenant' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0 ;

GRANT ALL ON multitenant.* TO 'multitenant'@'localhost' IDENTIFIED BY 'multitenant'